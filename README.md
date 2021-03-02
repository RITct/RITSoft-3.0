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

## Running the Permission Example

This example is based on [Spatie example](https://www.itsolutionstuff.com/post/laravel-8-user-roles-and-permissions-tutorialexample.html)

Mention [@UKnowWhoIm](https://github.com/UKnowWhoIm/) if you have any doubts.

1. Setup your database
- If you intend on using sail to host the server, either use mysql db or edit the dockerfile to create your db.
- Alternatively if you intend to use php artisan to host it, you can use whatever db you like.
- Head over to your .env file(create from .env.example if you don't have one) and set up the db(set values with prefix DB)
```
composer install
php artisan migrate
```

2. Create mock values for db.

- Create permissions(eg: subject-create, subject-edit, etc)
  
    We create basic permissions, which will be assigned to roles, or used directly by middleware
```
php artisan db:seed --class=PermissionTableSeeder
```
- Create Roles(eg: HOD, Admin, etc)
    - Admin has all listed permissions.
    - HOD has all subject permissions(eg: subject-create).
    - Faculty only has subject-list permission.
```
php artisan db:seed --class=RoleSeeder
```

- Create Users
```
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=FacultyUserSeeder
php artisan db:seed --class=HODUserSeeder
```

| User Name | Role | 
| --- | --- |
| csefaculty1@ritsoft.com | Faculty |
| csehod@ritsoft.com | HOD |
| admin@ritsoft.com | Admin |

**All users have password: 123456.**

3. Run the server.

```
php artisan serve
or 
./vendor/bin/sail up
```