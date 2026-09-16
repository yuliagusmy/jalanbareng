@echo off
setlocal
set "ROOT=%~dp0"
set "PHPRC=%ROOT%php82"
set "PATH=%ROOT%php82;%PATH%"

cd /d "%ROOT%backend"
echo Starting Laravel API on http://127.0.0.1:8001
php artisan serve --host=127.0.0.1 --port=8001
endlocal
