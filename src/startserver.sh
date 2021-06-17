#!/bin/bash
bash ./wait_for_it.sh db:5432 -t 0
php artisan migrate
php artisan serve --verbose --host=0.0.0.0 --port=8000
