@echo off
title WhatsCRM Multi-Process Launcher
echo ====================================================
echo Starting WhatsCRM Full Stack (Web + Queue Worker)
echo ====================================================

start "WhatsCRM Web Server" cmd /k "cd /d %~dp0backend && php artisan serve --port=8000"
start "WhatsCRM Queue Worker" cmd /k "cd /d %~dp0backend && php artisan queue:work --tries=3"

echo [OK] WhatsCRM services launched in dedicated consoles.
echo App URL: http://localhost:8000
echo.
