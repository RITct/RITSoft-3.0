#!/bin/bash
php artisan migrate
php artisan serve --verbose --host=0.0.0.0 --port=8000 & npm run watch
