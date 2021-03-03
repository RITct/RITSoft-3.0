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

| Change config values inside copied .env

## Contributors Guideline

## Licence

## Docs

1. [Permission Example](./docs/permission.md)

## Contributors
