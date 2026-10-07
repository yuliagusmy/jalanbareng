@echo off
REM backup-railway-local.bat - Backup Railway MySQL to Local Computer
REM For Windows

echo ================================================
echo   Jalan Bareng - Railway Database Backup
echo   Backup to Local Computer
echo ================================================
echo.

REM Configuration
set BACKUP_DIR=%USERPROFILE%\backups\jalan-bareng
set DATE_TIME=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE_TIME=%DATE_TIME: =0%
set BACKUP_FILE=%BACKUP_DIR%\railway_jalan_bareng_%DATE_TIME%.sql

REM Create backup directory
if not exist "%BACKUP_DIR%" (
    echo Creating backup directory: %BACKUP_DIR%
    mkdir "%BACKUP_DIR%"
)

REM Check if Railway CLI is installed
where railway >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Railway CLI is not installed!
    echo.
    echo Install Railway CLI:
    echo 1. Download from: https://railway.app/cli
    echo 2. Or run: npm install -g @railway/cli
    echo.
    pause
    exit /b 1
)

REM Check if logged in to Railway
echo Checking Railway authentication...
railway whoami >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Not logged in to Railway!
    echo.
    echo Please login first:
    echo   railway login
    echo.
    pause
    exit /b 1
)

REM Link to project (if not already linked)
echo Linking to Railway project...
railway link

REM Export database
echo.
echo Exporting database from Railway...
echo This may take a few minutes depending on database size...
echo.

railway run mysqldump --no-tablespaces -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD -P $MYSQLPORT $MYSQLDATABASE > "%BACKUP_FILE%"

if %ERRORLEVEL% neq 0 (
    echo.
    echo [ERROR] Backup failed!
    echo Check your Railway connection and database credentials.
    pause
    exit /b 1
)

REM Get file size
for %%A in ("%BACKUP_FILE%") do set BACKUP_SIZE=%%~zA

echo.
echo ================================================
echo   Backup Completed Successfully!
echo ================================================
echo.
echo Backup file: %BACKUP_FILE%
echo File size: %BACKUP_SIZE% bytes
echo.
echo To restore this backup:
echo 1. Transfer file to VPS: scp "%BACKUP_FILE%" deploy@vps-ip:/home/deploy/
echo 2. Import: mysql -u jalan_bareng -p jalan_bareng ^< railway_backup.sql
echo.

REM List recent backups
echo Recent backups:
dir /O-D /B "%BACKUP_DIR%\railway_*.sql" 2>nul | findstr /N "^" | findstr "^[1-5]:"
echo.

REM Cleanup old backups (keep last 7)
echo Cleaning up old backups (keeping last 7)...
for /f "skip=7 delims=" %%F in ('dir /B /O-D "%BACKUP_DIR%\railway_*.sql" 2^>nul') do (
    echo Deleting old backup: %%F
    del "%BACKUP_DIR%\%%F"
)

echo.
echo Done! Press any key to exit...
pause >nul
