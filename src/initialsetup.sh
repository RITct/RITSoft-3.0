#!/bin/bash
php artisan migrate
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=SubjectSeeder
php artisan db:seed --class=FacultyUserSeeder
php artisan db:seed --class=StudentSeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=CurriculumSeeder
php artisan db:seed --class=AttendanceSeeder

