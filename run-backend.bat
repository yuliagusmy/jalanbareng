@echo off
title Jalan Bareng - Backend API (Port 8001)
cd /d "%~dp0backend"
echo ===================================================
echo Starting Jalan Bareng Laravel API on http://127.0.0.1:8001
echo Using Portable PHP 8.2 & SQLite database
echo ===================================================
"..\php82\php.exe" artisan serve --host=127.0.0.1 --port=8001
pause
