#!/bin/bash
php artisan migrate
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=RoleSeeder
