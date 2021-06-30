<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\PhotoUploadController;
use App\Http\Controllers\TestRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view("/", "welcome");

Route::group(["middleware" => ["auth"]], function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get("auth/logout", [AuthController::class, "logout"])->name("logout");
    Route::resource("attendance", AttendanceController::class, [
        "names" => [
            "index" => "listAttendance",
            "create" => "createAttendance",
            "store" => "storeAttendance",
            "show" => "retrieveAttendance",
            "edit" => "editAttendance",
            "update" => "updateAttendance",
            "destroy" => "destroyAttendance"
        ]
    ]);
    Route::resource("faculty", FacultyController::class, [
        "names" => [
            "index" => "listFaculty",
            "create" => "createFaculty",
            "store" => "storeFaculty",
            "show" => "retrieveFaculty",
            "edit" => "editFaculty",
            "update" => "updateFaculty",
            "destroy" => "destroyFaculty"
        ]
    ]);
    Route::resource("requests", RequestController::class, [
        "names" => [
            "index" => "listRequests",
            "update" => "updateRequest"
        ]
    ]);
    Route::resource("users/photo", PhotoUploadController::class, [
        "names" => [
            "store" => "uploadPhotoStore",
            "create" => "uploadPhotoCreate"
        ]
    ]);
    Route::resource("testrequest", TestRequestController::class, [
        "names" => [
            "store" => "testRequestStore",
            "create" => "testRequestCreate"
        ]
    ]);
});

Route::group(["middleware" => ["guest"]], function () {
    Route::get("auth/login", [AuthController::class, "login"])->name("getLogin");
    Route::post("auth/login", [AuthController::class, "authenticate"])->name("postLogin");
});


/*
|--------------------------------------------------------------------------
| Students Routes
|--------------------------------------------------------------------------
|Upload photo for verification.
|Students current class details.
|Viewing attendance details.
|Semester registration.
|Enter university marks.
|View series exam marks.
|View draft/final sessional marks.
|Feedback for faculty evaluation.
|
|
*/
/*
Route::group(['prefix' => 'students',"middleware" => ["auth"]], function () {

    Route::get('', [StudentsController::class, "dashboards"])->name("students.dashboards");
    Route::get('/attendance', [StudentsController::class, "attendance"])->name("students.attendance");
    Route::get('/semRegistrations', [StudentsController::class, "semRegistrations"])
        ->name("students.semRegistrations");
    Route::get('/universityMarks', [StudentsController::class, "universityMarks"])
        ->name("students.universityMarks");
    Route::get('/seriesMarks', [StudentsController::class, "seriesMarks"])
        ->name("students.seriesMarks");
    Route::get('/sessionMarks', [StudentsController::class, "sessionMarks"])
        ->name("students.sessionMarks");
    Route::get('/facultyEvaluvations', [StudentsController::class, "facultyEvaluvations"])
        ->name("students.facultyEvaluvations");
    Route::get('/photos', [StudentsController::class, "photos"])->name("students.photos");

    Route::post('/universityMarks', [StudentsController::class, "postUniversityMarks"])
        ->name("students.postUniversityMarks");
    Route::post('/facultyEvaluvations', [StudentsController::class, "postFacultyEvaluvations"])
        ->name("students.postFacultyEvaluvation");
    Route::post('/semRegistrations', [StudentsController::class, "postSemRegistrations"])
        ->name("students.postSemRegistration");
    Route::post('/photos', [StudentsController::class, "postPhoto"])->name("students.postPhotos");
});
*/
