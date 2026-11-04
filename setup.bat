@echo off
echo ==============================================================
echo   CodeX404 Solutions - Automated Project ^& Database Setup
echo ==============================================================

set "PHP_CMD=php"
where php >nul 2>nul || if exist "C:\xampp\php\php.exe" set "PHP_CMD=C:\xampp\php\php.exe"

if not exist ".env" if exist ".env.example" copy ".env.example" ".env" >nul
if exist "librarymanagement\.env.example" if not exist "librarymanagement\.env" copy "librarymanagement\.env.example" "librarymanagement\.env" >nul

echo [i] Provisioning database and executing migrations...
"%PHP_CMD%" "librarymanagement\database\migrate.php"
if %errorlevel% neq 0 goto :failed

echo(
echo ==============================================================
echo [SUCCESS] Project and Database Setup Complete!
echo ==============================================================
exit /b 0

:failed
echo(
echo [ERROR] Database setup or migration failed!
echo Please ensure MySQL is running (e.g. via XAMPP Control Panel).
exit /b 1
