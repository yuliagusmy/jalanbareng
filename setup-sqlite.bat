@echo off
setlocal
set "ROOT=%~dp0"
set "PHPRC=%ROOT%php82"
set "PATH=%ROOT%php82;%PATH%"

cd /d "%ROOT%backend"

if not exist "database\database.sqlite" type nul > "database\database.sqlite"

php artisan migrate:fresh --seed --force
php artisan storage:link

echo.
echo SQLite database ready at backend\database\database.sqlite
echo Start backend with start-backend.bat
endlocal
