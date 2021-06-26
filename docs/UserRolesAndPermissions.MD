# User Roles and Permissions

## Table of Contents
- [Basic Users](#basic-users)
- [Roles](#roles)
- [Leveraging roles and database to craft our users](#leveraging-roles-and-database-to-craft-our-users)
- [Permissions](#permissions)
- [Roles vs Permissions](#roles-vs-permissions)
- [Role Permission Middlewares](#role-permission-middlewares)


### Basic Users

There are 4 types of users with respect to the database
- Student - All users with `user.student_id` != `null`, they also have role `STUDENT`
- Faculty - All users with `user.faculty_id` != `null`, they also have role `FACULTY`
- OfficeStaff - All users with `office_staff_id` != `null`, they also have role `OFFICE_STAFF`
- Admin - All users with role `ADMIN`

### Roles
There are also various roles implemented using [spatie](https://spatie.be/docs/laravel-permission/v4/introduction) package.
- STUDENT
- OFFICE_STAFF
- FACULTY
- STAFF_ADVISOR
- HOD
- PRINCIPAL
- ADMIN

`roles` and `users` have a many to many relationship, and for future reference while saying that a faculty has a role, 
it is implied that the user of the faculty will have that role.

### Leveraging roles and database to craft our users

- Staff advisors are special type of faculty with an additional role of `STAFF_ADVISOR`. Their `advisor_classroom_id` must NOT be `null`
- HODs are special faculty with an additional role of `HOD`. The department is identified from faculty's `department` property.
- PRINCIPAL is a special faculty with an additional role of `PRINCIPAL`. There is only one principal(at least currently)

### Permissions

Permissions are stored as `STRING` created by US in the `permissions` table. 

- **PERMISSIONS ARE NOT AUTO GENERATED**
- Currently, they are being created in the [PermissionSeeder](../src/database/seeders/PermissionTableSeeder.php) file.
- While creating permissions give them an easy name to identify, eg: to create users, use `user.create`
- This permission system works only for levels, you still need object permissions. Eg: All faculty will have `attendance.create` permission, but they can only create attendance for their own courses. 

The basic idea is as follows.

- We create a permission, `"user.create"` to allow users to be created by an authorized users.
- We use a middleware `permission` as `permission:user.create`, to enforce that only users with `user.create` permission can access the protected route.
- Basically the middleware accesses the logged in users permissions and simply check if this permission is in them.

### Roles vs Permissions
 
While theoretically permissions can directly be assigned to users, we almost never do that. 

All Permissions are allocated to certain roles, and then those roles are assigned to users.

Eg: For Role `STUDENT`, `attendance.view` permission is allocated, and then all student users are simply assigned role `STUDENT`

### Role Permission Middlewares

Refer [documentation](https://spatie.be/docs/laravel-permission/v4/basic-usage/middleware)