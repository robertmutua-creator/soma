<?php

use App\Controllers\Admin\SchoolAdminsController;
use App\Controllers\Admin\SchoolController;
use App\Controllers\AuthController;
use App\Controllers\School\AllocationsController;
use App\Controllers\School\ClassesController;
use App\Controllers\School\EnrollmentsController;
use App\Controllers\School\StudentsController;
use App\Controllers\School\SubjectsController;
use App\Controllers\School\TeachersController;
use App\Controllers\School\TermsController;
use App\Controllers\School\YearsController;
use App\Controllers\SetupController;
use App\Core\Controller;

$router->get('/migrate', [SetupController::class, 'migrate']);
// $router->get('/seed', [SetupController::class, 'admin']);

// Auth Route
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginStore']);
$router->get('/dashboard', [Controller::class, 'dashboard']);
$router->post('/logout', [AuthController::class, 'logout']);


// Schools Management
$router->get('/admin/schools', [SchoolController::class, 'index']);
$router->post('/admin/schools/save', [SchoolController::class, 'save']);
$router->post('/admin/schools/delete', [SchoolController::class, 'delete']);

// School Admins Management
$router->get('/admin/admins', [SchoolAdminsController::class, 'index']);
$router->post('/admin/admins/save', [SchoolAdminsController::class, 'save']);
$router->post('/admin/admins/delete', [SchoolAdminsController::class, 'delete']);


// Schools
// Classes Management
$router->get('/school/classes', [ClassesController::class, 'index']);
$router->post('/school/classes/save', [ClassesController::class, 'save']);
$router->post('/school/classes/delete', [ClassesController::class, 'delete']);

// Subjects Management
$router->get('/school/subjects', [SubjectsController::class, 'index']);
$router->post('/school/subjects/save', [SubjectsController::class, 'save']);
$router->post('/school/subjects/delete', [SubjectsController::class, 'delete']);

// Teachers Management
$router->get('/school/teachers', [TeachersController::class, 'index']);
$router->post('/school/teachers/save', [TeachersController::class, 'save']);
$router->post('/school/teachers/delete', [TeachersController::class, 'delete']);


// Students Management
$router->get('/school/students', [StudentsController::class, 'index']);
$router->post('/school/students/save', [StudentsController::class, 'save']);
$router->post('/school/students/delete', [StudentsController::class, 'delete']);
$router->get('/school/students/template', [StudentsController::class, 'downloadTemplate']);
$router->post('/school/students/import', [StudentsController::class, 'importBatch']);

// Academic Years Management
$router->get('/school/years', [YearsController::class, 'index']);
$router->post('/school/years/save', [YearsController::class, 'save']);
$router->post('/school/years/delete', [YearsController::class, 'delete']);

// Terms Management
$router->get('/school/terms', [TermsController::class, 'index']);
$router->post('/school/terms/save', [TermsController::class, 'save']);
$router->post('/school/terms/delete', [TermsController::class, 'delete']);

// Teacher-Subject Allocations
$router->get('/school/allocations', [AllocationsController::class, 'index']);
$router->post('/school/allocations/save', [AllocationsController::class, 'save']);
$router->post('/school/allocations/delete', [AllocationsController::class, 'delete']);

// Student Enrollments Routes
$router->get('/school/enrollments', [EnrollmentsController::class, 'index']);
$router->post('/school/enrollments/store', [EnrollmentsController::class, 'store']);
$router->post('/school/enrollments/status', [EnrollmentsController::class, 'status']);
$router->get('/school/promotions', [EnrollmentsController::class, 'promotions']);
$router->post('/school/promotions', [EnrollmentsController::class, 'promotions']);
