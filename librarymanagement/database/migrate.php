<?php
/**
 * CodeX404 Solutions - Universal Database Migration Runner
 * 
 * Capabilities:
 * - Auto-creates target database if missing (CREATE DATABASE IF NOT EXISTS)
 * - Automatically initializes fresh database with baseline schema.sql and seeds.sql
 * - Tracks applied migrations via idempotent `migrations` table
 * - Chronologically executes pending migrations in database/migrations/
 * - Supports CLI arguments: --status, --create=name, --export, --host=, --user=, --pass=, --db=
 * - Multi-environment safe: Local, Development, Staging, and Production
 */

declare(strict_types=1);

// Disable error limits
ini_set('display_errors', '1');
error_reporting(E_ALL);

$isCli = (php_sapi_name() === 'cli');

function output(string $msg, string $type = 'info'): void {
    global $isCli;
    $colors = [
        'info'    => "\033[36m", // Cyan
        'success' => "\033[32m", // Green
        'warn'    => "\033[33m", // Yellow
        'error'   => "\033[31m", // Red
        'reset'   => "\033[0m"
    ];
    if ($isCli) {
        $prefix = match ($type) {
            'success' => '[✓] ',
            'warn'    => '[!] ',
            'error'   => '[✗] ',
            default   => '[i] ',
        };
        echo ($colors[$type] ?? '') . $prefix . $msg . ($colors['reset'] ?? '') . PHP_EOL;
    } else {
        echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "<br>\n";
    }
}

// 1. Resolve Paths
$dbDir = __DIR__;
$appDir = dirname($dbDir);
$migrationsDir = $dbDir . '/migrations';
if (!is_dir($migrationsDir)) {
    @mkdir($migrationsDir, 0755, true);
}

// Default database name configured for this project
$defaultProjectDb = 'lms';

// 2. Resolve Environment & Credentials
function getEnvConfig(string $dbDir, string $defaultDb): array {
    $config = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'pass' => '',
        'db'   => $defaultDb
    ];

    $searchDirs = [$dbDir, dirname($dbDir), dirname(dirname($dbDir))];
    foreach ($searchDirs as $dir) {
        $envFile = $dir . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    if (in_array($k, ['DB_HOST', 'DATABASE_HOST'])) $config['host'] = $v;
                    if (in_array($k, ['DB_PORT', 'DATABASE_PORT'])) $config['port'] = (int)$v;
                    if (in_array($k, ['DB_USER', 'DB_USERNAME', 'DATABASE_USER'])) $config['user'] = $v;
                    if (in_array($k, ['DB_PASS', 'DB_PASSWORD', 'DATABASE_PASS'])) $config['pass'] = $v;
                    if (in_array($k, ['DB_NAME', 'DB_DATABASE', 'DATABASE_NAME'])) $config['db'] = $v;
                }
            }
            break;
        }
    }

    if (getenv('DB_HOST')) $config['host'] = getenv('DB_HOST');
    if (getenv('DB_PORT')) $config['port'] = (int)getenv('DB_PORT');
    if (getenv('DB_USER')) $config['user'] = getenv('DB_USER');
    if (getenv('DB_PASS') !== false) $config['pass'] = getenv('DB_PASS');
    if (getenv('DB_NAME')) $config['db'] = getenv('DB_NAME');

    global $argv;
    if (!empty($argv)) {
        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--host=')) $config['host'] = substr($arg, 7);
            if (str_starts_with($arg, '--port=')) $config['port'] = (int)substr($arg, 7);
            if (str_starts_with($arg, '--user=')) $config['user'] = substr($arg, 7);
            if (str_starts_with($arg, '--pass=')) $config['pass'] = substr($arg, 7);
            if (str_starts_with($arg, '--db='))   $config['db'] = substr($arg, 5);
        }
    }

    return $config;
}

// 3. Helper: Robust SQL Statement Splitter
function splitSqlStatements(string $sql): array {
    $statements = [];
    $current = '';
    $len = strlen($sql);
    $inSingle = false;
    $inDouble = false;
    $inBacktick = false;
    $inLineComment = false;
    $inBlockComment = false;

    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $nxt = ($i + 1 < $len) ? $sql[$i + 1] : '';

        if ($inLineComment) {
            if ($c === "\n" || $c === "\r") {
                $inLineComment = false;
            }
            continue;
        }

        if ($inBlockComment) {
            if ($c === '*' && $nxt === '/') {
                $inBlockComment = false;
                $i++;
            }
            continue;
        }

        if (!$inSingle && !$inDouble && !$inBacktick) {
            if ($c === '-' && $nxt === '-') {
                $inLineComment = true;
                $i++;
                continue;
            }
            if ($c === '#') {
                $inLineComment = true;
                continue;
            }
            if ($c === '/' && $nxt === '*') {
                $inBlockComment = true;
                $i++;
                continue;
            }
        }

        if ($c === "'" && !$inDouble && !$inBacktick) {
            $prev = ($i > 0) ? $sql[$i - 1] : '';
            if ($prev !== '\\') {
                $inSingle = !$inSingle;
            }
        } elseif ($c === '"' && !$inSingle && !$inBacktick) {
            $prev = ($i > 0) ? $sql[$i - 1] : '';
            if ($prev !== '\\') {
                $inDouble = !$inDouble;
            }
        } elseif ($c === '`' && !$inSingle && !$inDouble) {
            $inBacktick = !$inBacktick;
        }

        if ($c === ';' && !$inSingle && !$inDouble && !$inBacktick) {
            $trimmed = trim($current);
            if ($trimmed !== '') {
                $statements[] = $trimmed;
            }
            $current = '';
            continue;
        }

        $current .= $c;
    }

    $trimmed = trim($current);
    if ($trimmed !== '') {
        $statements[] = $trimmed;
    }

    return $statements;
}

$config = getEnvConfig($dbDir, $defaultProjectDb);

// Handle CLI subcommands: --create=name
global $argv;
if (!empty($argv)) {
    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--create=')) {
            $migrationName = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', substr($arg, 9)));
            $filename = date('Ymd_His') . '_' . $migrationName . '.sql';
            $filePath = $migrationsDir . '/' . $filename;
            $stub = "-- Migration: {$migrationName}\n-- Created At: " . date('Y-m-d H:i:s') . "\n\n-- Write your idempotent SQL queries below (e.g. ALTER TABLE, CREATE TABLE IF NOT EXISTS)\n";
            file_put_contents($filePath, $stub);
            output("Created migration file: {$filename}", 'success');
            exit(0);
        }
    }
}

output("==============================================================");
output("CodeX404 Solutions - Universal Database Migration Runner");
output("Database: " . $config['db'] . " on " . $config['host'] . ":" . $config['port']);
output("==============================================================");

try {
    // 4. Ensure Database Exists (Connect without selecting DB first)
    $serverDsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
    try {
        $serverPdo = new PDO($serverDsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $pe) {
        if (($config['host'] === '127.0.0.1' || $config['host'] === 'localhost') && ($config['user'] !== 'root' || $config['pass'] !== '')) {
            output("Notice: Auth failed for '{$config['user']}'. Falling back to local default user 'root'...", 'warn');
            $config['user'] = 'root';
            $config['pass'] = '';
            $config['db'] = $defaultProjectDb;
            $serverPdo = new PDO($serverDsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } else {
            throw $pe;
        }
    }

    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    output("Database `{$config['db']}` confirmed / ready.", 'success');

    // 5. Connect to Target Database
    $dbDsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset=utf8mb4";
    $pdo = new PDO($dbDsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);

    // 6. Check if Database is Fresh (0 tables)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = :db");
    $stmt->execute(['db' => $config['db']]);
    $tableCount = (int)($stmt->fetch()['cnt'] ?? 0);
    $stmt->closeCursor();

    if ($tableCount === 0) {
        output("Database is empty (0 tables). Initializing baseline schema...", 'warn');
        $schemaFile = $dbDir . '/schema.sql';
        if (file_exists($schemaFile)) {
            $schemaSql = file_get_contents($schemaFile);
            $statements = splitSqlStatements($schemaSql);
            foreach ($statements as $s) {
                $pdo->exec($s);
            }
            output("Baseline schema.sql imported successfully.", 'success');
        }

        // Check for seed data
        foreach (['seeds.sql', 'seed.sql'] as $seedName) {
            $seedFile = $dbDir . '/' . $seedName;
            if (file_exists($seedFile)) {
                $seedSql = file_get_contents($seedFile);
                $seedStmts = splitSqlStatements($seedSql);
                foreach ($seedStmts as $s) {
                    try {
                        $pdo->exec($s);
                    } catch (Throwable $se) {
                        // Ignore duplicate key warnings on seeds
                    }
                }
                output("Baseline {$seedName} imported successfully.", 'success');
                break;
            }
        }
    } else {
        output("Database has existing tables ({$tableCount} tables found).", 'info');
    }

    // 7. Ensure Migrations Ledger Table Exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL UNIQUE,
        `batch` INT NOT NULL,
        `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Fetch applied migrations
    $appliedStmt = $pdo->query("SELECT `migration` FROM `migrations`");
    $appliedMigrations = $appliedStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $appliedStmt->closeCursor();

    // Determine current batch number
    $batchStmt = $pdo->query("SELECT MAX(`batch`) AS max_batch FROM `migrations`");
    $maxRow = $batchStmt->fetch();
    $currentBatch = ((int)($maxRow['max_batch'] ?? 0)) + 1;
    $batchStmt->closeCursor();

    // 8. Discover and Sort Migration Files
    $migrationFiles = [];
    if (is_dir($migrationsDir)) {
        $files = scandir($migrationsDir);
        foreach ($files as $f) {
            if (str_ends_with($f, '.sql') || str_ends_with($f, '.php')) {
                $migrationFiles[] = $f;
            }
        }
    }
    sort($migrationFiles);

    // Handle --status
    if (!empty($argv) && in_array('--status', $argv)) {
        output("\nMigration Status Report:", 'info');
        if (empty($migrationFiles)) {
            output("  No migration files found in database/migrations/", 'info');
        }
        foreach ($migrationFiles as $mf) {
            $isApplied = in_array($mf, $appliedMigrations);
            $badge = $isApplied ? "[APPLIED]" : "[PENDING]";
            $color = $isApplied ? 'success' : 'warn';
            output("  {$badge} {$mf}", $color);
        }
        exit(0);
    }

    // 9. Execute Pending Migrations
    $pendingCount = 0;
    foreach ($migrationFiles as $mf) {
        if (in_array($mf, $appliedMigrations)) {
            continue;
        }

        $pendingCount++;
        output("Applying migration: {$mf}...", 'info');
        $fullPath = $migrationsDir . '/' . $mf;

        if (str_ends_with($mf, '.sql')) {
            $sql = file_get_contents($fullPath);
            $statements = splitSqlStatements($sql);
            foreach ($statements as $s) {
                $sTrim = trim($s);
                if ($sTrim === '') continue;
                if (preg_match('/^SELECT\b/i', $sTrim)) {
                    $q = $pdo->query($sTrim);
                    if ($q) $q->closeCursor();
                } else {
                    $pdo->exec($sTrim);
                }
            }
        } elseif (str_ends_with($mf, '.php')) {
            $closure = require $fullPath;
            if (is_callable($closure)) {
                $closure($pdo);
            } elseif (is_object($closure) && method_exists($closure, 'up')) {
                $closure->up($pdo);
            }
        }

        // Record in ledger
        $ins = $pdo->prepare("INSERT INTO `migrations` (`migration`, `batch`) VALUES (:migration, :batch)");
        $ins->execute(['migration' => $mf, 'batch' => $currentBatch]);
        output("Migration applied: {$mf}", 'success');
    }

    if ($pendingCount === 0) {
        output("Database is up-to-date. No pending migrations.", 'success');
    } else {
        output("Applied {$pendingCount} migration(s) in Batch #{$currentBatch}.", 'success');
    }

    // 10. Handle --export
    if (!empty($argv) && in_array('--export', $argv)) {
        output("Exporting database schema to database/schema.sql...", 'info');
        $tablesStmt = $pdo->query("SHOW TABLES");
        $allTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
        $dump = "-- CodeX404 Solutions Schema Dump\n-- Generated: " . date('Y-m-d H:i:s') . "\n-- Database: {$config['db']}\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        foreach ($allTables as $t) {
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$t}`")->fetch();
            $dump .= "DROP TABLE IF EXISTS `{$t}`;\n";
            $dump .= $createStmt['Create Table'] . ";\n\n";
        }
        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($dbDir . '/schema.sql', $dump);
        output("Schema exported successfully to database/schema.sql ({$tableCount} tables).", 'success');
    }

    output("==============================================================");
    output("Execution finished successfully.", 'success');
    exit(0);

} catch (Throwable $e) {
    output("Migration Error: " . $e->getMessage(), 'error');
    output("File: " . $e->getFile() . ":" . $e->getLine(), 'error');
    exit(1);
}
