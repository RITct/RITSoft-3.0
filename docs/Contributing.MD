# Contributing

## Table of contents

- [Standard of Code](#standard-of-code)
- [Initial Setup](#initial-setup-and-credentials)
- [Running shell commands inside container](#running-shell-commands-inside-the-container)
- [Installing PHP Packages](#installing-php-packages)
- [Creating Controllers](#creating-controllers)
- [Creating Request Class](#creating-request-class)
- [Creating Middlewares](#creating-middleware)
- [Creating Models](#creating-models)
- [Creating Migrations](#creating-migrations)
- [Running Migrations](#running-migrations)
- [Making Tests](#making-tests)
- [Running Tests](#running-tests)
- [Running Linter](#running-linter)
- [pgAdmin](#using-pgadmin)
- [Code Inspection](#setting-up-your-ide-and-code-inspection)

### Standard of code

The code should be strictly be maintained in PSR12 standard. It's enforced by PHPCodeSniffer linter.

[Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

### Initial setup and credentials

```
docker-compose run app sh initialsetup.sh
```
Or
```
docker exec -it ritsoft_app sh initialsetup.sh
```
| UserName | Roles |
| --- | --- |
| csestudent0@rit.com | Student |
| csestudent1@rit.com | Student |
| csestudent2@rit.com | Student |
| csefaculty1@rit.com | Faculty |
| csefaculty2@rit.com | Faculty, StaffAdvisor |
| hodcse@rit.com | HOD, Faculty |
| principal@rit.com | Principal |
| office_staff1@rit.com | Office |
| office_staff0@rit.com | Office | 
| admin@rit.com | Admin |

- Principal is an ECE faculty, without any courses
- Departments: CSE, ECE
- Classrooms: BTECH S1 CSE, BTECH S2 CSE. 
  - All students are enrolled in Classroom `BTECH S1 CSE`
  - `csefaculty2@rit.com` is the staff advisor of `BTECH S1 CSE`
- Subjects: CSE201, CSE301
- Courses:
  - `csefaculty1@rit.com` takes `CSE201` for `BTECH S1 CSE`
  - `csefaculty2@rit.com` takes `CSE301` for `BTECH S2 CSE`

**All users have 123456 as their password**

### Running shell commands inside the container

Use the format `docker exec -it ritsoft_app <CMD>` or `docker-compose run app <CMD>`

An Example `docker exec -it ritsoft_app php artisan migrate` or `docker-compose run app php artisan migrate`

### Installing PHP packages
```
docker exec -it ritsoft_app composer install <package_name> 
```
Or
```
docker-compose run app composer install <package_name>
```

### Creating Controllers
```
docker exec -it ritsoft_app php artisan make:controller <controller_name> 
```
Or
```
docker-compose run app php artisan make:controller <controller_name>
```

### Creating Request Class
```
docker exec -it ritsoft_app php artisan make:request <name> 
```
Or
```
docker-compose run app php artisan make:request <name>
```

### Creating Middleware
```
docker exec -it ritsoft_app php artisan make:middleware <middleware_name>
```
Or
```
docker-compose run app php artisan make:middleware <middleware_name>
```

### Creating Models

```
docker exec -it ritsoft_app php artisan make:model <model_name>
```
Or
```
docker-compose run app php artisan make:model <model_name>
```

### Creating Migrations
```
docker exec -it ritsoft_app php artisan make:migration <migration_name>
```
Or
```
docker-compose run app php artisan make:migration <migration_name>
```
Migrations should be named with a proper naming convention like for example,
creating a table users should be named like create_users_table

If multiple tables are created in a migration for a feature, it should be named like,
create_&lt;feature&gt;_tables

For adding columns, add_column_&lt;column_name&gt;&lowbar;to&lowbar;&lt;table_name&gt;

For removing columns, remove_column_&lt;column_name&gt;&lowbar;from&lowbar;&lt;table_name&gt;

### Running Migrations

```
docker exec -it ritsoft_app php artisan migrate
```
Or
```
docker-compose run app php artisan migrate
```

### Making Tests

```
docker exec -it ritsoft_app php artisan make:<test_name>
```
Or
```
docker-compose run app php artisan make:<test_name>
```

Add `--unit` to make unit tests instead of FeatureTest.

### Running Tests
```
sh ./test.sh 
```

### Running Linter
```
sh ./lint.sh
```

### Using pgAdmin
pgAdmin can be used to directly access the database for debugging purposes.
0. Start pgAdmin service by `docker-compose --profile pg-admin up -d`
1. Visit [localhost:9000](http://localhost:9000) and login
   - Username: admin@rit.com
   - Password: 123456
2. Get Ip Address of the container
```
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ritsoft_db
```
3. Click add new server, set any name and paste this IP Address in the Connection > Host Column.
4. **DO NOT** change the database
    - Username: ritsoft
    - Password: ritsoft
5. After successful connection, click Databases > ritsoft, in the left panel.
6. You can now do whatever you want with this db.

### Setting up your IDE and Code Inspection
So far the easiest way I've seen is just copying the vendor directory from the container to host.

```
sudo docker cp ritsoft_app:/ritsoft/vendor /absolute/path/to/RITSoft-3.0/src/
```
Better alternatives are always welcome
