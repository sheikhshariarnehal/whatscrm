@echo off
title WhatsCRM Queue Worker
echo ====================================================
echo Starting WhatsCRM Background Queue Worker
echo ====================================================
cd /d "%~dp0backend"
echo [INFO] Running Laravel Queue Worker for Campaigns, Webhooks, and Jobs...
php artisan queue:work --tries=3 --timeout=120
pause
