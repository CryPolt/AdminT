@echo off

:: Проверьте, установлен ли PHP
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo PHP не установлен. Пожалуйста, установите PHP и попробуйте снова.
    exit /b 1
)

:: Запустите сервер PHP
php server.php
