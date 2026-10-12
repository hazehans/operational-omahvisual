@echo off
title Web Inventory - Omah Visual LED
echo ========================================================
echo       MEMULAI WEB INVENTORY - OMAH VISUAL LED
echo ========================================================
echo.
set "PATH=%PATH%;C:\Users\user\php"

echo Membuka browser ke http://127.0.0.1:8000 ...
start http://127.0.0.1:8000

echo Menjalankan Laravel Artisan Serve...
echo (Tekan Ctrl + C atau tutup jendela ini untuk mematikan web)
echo.

php artisan serve --port=8000
pause
