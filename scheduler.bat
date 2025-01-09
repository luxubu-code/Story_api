@echo off
cd F:\laragon project\htdocs\StoryApi
:loop
php artisan schedule:run
timeout /t 60
goto loop