# RITSoft-3.0

## Starting the server

Using Docker Container
```
cd src
composer install
npm install
copy .env.example .env
php artisan key:generate
./vendor/bin/sail up
```

Using PHP artisan
```
cd src
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan serve
```