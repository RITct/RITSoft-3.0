# RITSoft-3.0

A web application for RIT students and teachers for academic management. Developed using larvel.

## Prerequisite

- [Docker](https://www.docker.com/): Container Tool

## Getting Started

### First Step

Verify docker and docker-compose is installed by running
```
docker --version
docker-compose --version
```

### Second Step
Run
```
docker-compose build
docker-compose up -d
```
Visit `localhost:3000` to view the app.

**Run `docker-compose build` only if you're setting up for the first time or have made changes.**

If you're setting up for the first time, run
```
docker exec -it ritsoft_app /ritsoft/initialsetup.sh
```

To stop the container run `docker-compose down`


## Contributors Guideline

This project is a work in progress, so try to update the readme as you add more features.

### Running shell commands inside the container

Use the format `docker exec -it ritsoft_app <CMD>`

An Example `docker exec -it ritsoft_app php artisan migrate`

### Installing php packages

```
sh ./composer_install.sh <package_name>
```
Example
```
sh ./composer_install.sh bensampo/laravel-enum
```

### Setting up your IDE and Code Inspection

So far the easiest way I've seen is just copying the vendor directory from the container to host. 

```
sudo docker cp ritsoft_app:/ritsoft/vendor /absolute/path/to/RITSoft-3.0/src/
```
Better alternatives are always welcome

## Licence

## Docs

[Permission Example](./docs/permission.md)

1. For setting up roles and permissions

```
docker exec -it ritsoft_app /ritsoft/initialsetup.sh
```

2. Setup users
    - Admin: `docker exec -it ritsoft_app php artisan db:seed --class=AdminUserSeeder`
    - HOD: `docker exec -it ritsoft_app php artisan db:seed --class=HODUserSeeder`
    - Faculty: `docker exec -it ritsoft_app php artisan db:seed --class=FacultyUserSeeder`
    - Student: `docker exec -it ritsoft_app php artisan db:seed --class=StudentSeeder`
    
| UserName | Roles |
| --- | --- |
| csestudent@rit.com | Student |
| csefaculty1@rit.com | Faculty |
| csehod@rit.com | HOD |
| admin@rit.com | Admin |

**All users have 123456 as their password**


## Contributors
