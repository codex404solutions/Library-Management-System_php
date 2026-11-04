@echo off
echo ==============================================================
echo   CodeX404 Solutions - Local Database Schema Sync Tool
echo ==============================================================

set "PHP_CMD=php"
where php >nul 2>nul || if exist "C:\xampp\php\php.exe" set "PHP_CMD=C:\xampp\php\php.exe"

echo [i] Syncing local MySQL schema to database/schema.sql...
"%PHP_CMD%" "librarymanagement\database\migrate.php" --export
if %errorlevel% neq 0 goto :failed

echo(
echo [OK] Schema updated in repository! You can now commit and push to Git.
exit /b 0

:failed
echo(
echo [ERROR] Failed to sync schema. Ensure MySQL is running.
exit /b 1
