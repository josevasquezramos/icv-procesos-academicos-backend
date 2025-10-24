<?php

use App\Http\Controllers\Api\CompleteGroupController;
use App\Http\Controllers\Api\CredentialPdfController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\AcademicSettingController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ClassesController;
use App\Http\Controllers\Api\CoursePreviousRequirementController;
use App\Http\Controllers\Api\CredentialController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\FinalGradeController;
use App\Http\Controllers\Api\GradeRecordController;
use App\Http\Controllers\Api\GroupParticipantController ;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProgramCourseController;
use App\Http\Controllers\Api\TeacherProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ClassMaterialController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminTeacherController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminCourseController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {


   // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Estudiantes
    Route::apiResource('/students', AdminStudentController::class);
    
    // Docentes
    Route::apiResource('/teachers', AdminTeacherController::class);
    
    // Administradores
    Route::apiResource('/admins', AdminUserController::class);
    
    // Cursos pendientes
    Route::get('/pending-courses', [AdminCourseController::class, 'pendingCourses']);
    Route::post('/courses/{id}/approve', [AdminCourseController::class, 'approveCourse']);
    Route::post('/courses/{id}/reject', [AdminCourseController::class, 'rejectCourse']);
    Route::post('/courses/bulk-approve', [AdminCourseController::class, 'bulkApprove']);


    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    Route::resource('courses', CourseController::class)->except('create', 'edit');
    Route::get('/courses/{id}/groups', [CourseController::class, 'getGroupsByCourse']);

    Route::resource('groups', GroupController::class)->except('create', 'edit');
    Route::post('groups/{group}/complete', CompleteGroupController::class)->name('groups.complete');
    Route::get('credentials/{credential}/pdf', CredentialPdfController::class)->name('credentials.pdf');
    Route::post('/groups/{id}/join', [GroupController::class, 'joinGroup']);
    Route::get('/groups/completed/{userId}', [GroupController::class, 'getCompletedGroupsByStudent']);

    // CRUD Resources
    Route::apiResource('academic-settings', AcademicSettingController::class);
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('classes', ClassesController::class);
    Route::get('classes/group/{groupId}', [ClassesController::class, 'getByGroup']);
    Route::apiResource('course-previous-requirements', CoursePreviousRequirementController::class);
    Route::apiResource('credentials', CredentialController::class);
    Route::apiResource('evaluations', EvaluationController::class);
    Route::get('evaluations/group/{groupId}', [EvaluationController::class, 'getByGroup']);
    Route::apiResource('final-grades', FinalGradeController::class);
    Route::apiResource('grade-records', GradeRecordController::class);
    Route::apiResource('group-participants', GroupParticipantController::class);
    Route::apiResource('programs', ProgramController::class);
    Route::apiResource('program-courses', ProgramCourseController::class);
    Route::apiResource('teacher-profiles', TeacherProfileController::class);
    Route::apiResource('users', UserController::class);

     Route::apiResource('class-materials', ClassMaterialController::class);
    Route::get('classes/{classId}/materials', [ClassMaterialController::class, 'getByClass']);

    Route::get('/profile', [ProfileController::class, 'show']);
});
