# RITSoft-3.0

A web application for RIT students and teachers for academic management. Developed using larvel.

## Prerequisite

1. [PHP 7/8](https://www.php.net/): Backend
2. [Composer](https://getcomposer.org/): PHP Dependency Management
3. [NPM](https://www.npmjs.com/): JS Dependency Management
4. [Docker](https://www.docker.com/): Container Tool

## Getting Started

### First Step

Check php, npm and composer is working properly by typing following commands in a terminal.

```bash
composer --version
npm --version
php --version
```

### Second Step

```bash
git clone https://github.com/RITct/RITSoft-3.0.git
cd src
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan serve
```

Change config values inside copied .env

## Contributors Guideline

This project is a work in progress, so try to update the readme as you add more features.

## Licence

## Docs

[Permission Example](./docs/permission.md)

1. For setting up roles and permissions

```
php artisan migrate
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=RoleSeeder
```

2. Setup users
    - Admin: `php artisan db:seed --class=AdminUserSeeder`
    - HOD: `php artisan db:seed --class=HODUserSeeder`
    - Faculty: `php artisan db:seed --class=FacultyUserSeeder`
    - Student: `php artisan db:seed --class=StudentSeeder`
    
| UserName | Roles |
| --- | --- |
| csestudent@rit.com | Student |
| csefaculty1@rit.com | Faculty |
| csehod@rit.com | HOD |
| admin@rit.com | Admin |

**All users have 123456 as their password**


## Contributors
