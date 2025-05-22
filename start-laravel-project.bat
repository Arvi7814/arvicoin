@echo off
cd /d C:\laragon\www\arvicoin.uz-main
start "" "C:\laragon\laragon.exe"
start "" "http://127.0.0.1:8000/admin/login"
call php artisan serve
