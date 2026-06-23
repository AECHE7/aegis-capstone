@echo off
title A.E.G.I.S. Services Starter
echo ====================================================
echo   A.E.G.I.S. Capstone - Starting Services Suite      
echo ====================================================
echo.

echo [+] Launching Laravel Web Server...
start "A.E.G.I.S. Web Server" cmd /k "php artisan serve"

echo [+] Launching Laravel Queue Worker...
start "A.E.G.I.S. Queue Worker" cmd /k "php artisan queue:work"

echo [+] Launching Python Flask AI Service...
start "A.E.G.I.S. Flask AI" cmd /k "cd aegis-ai && .\venv\Scripts\python.exe app.py"

echo.
echo ====================================================
echo   All services have been started in separate windows. 
echo   Keep them open to interact with the application.
echo ====================================================
pause
