<?php

use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ResumeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

Route::get('/members', [MemberController::class, 'index']);
Route::get('/members/email/{email}', [MemberController::class, 'showByEmail']);
Route::get('/members/noapplication', [MemberController::class, 'showNotGetApplication']);

Route::get('/resumes', [ResumeController::class, 'index']);
Route::get('/resumes/name/{name}', [ResumeController::class, 'showByName']);

Route::get('/applications', [JobApplicationController::class, 'index']);
Route::get('/applications/job/{id}', [JobApplicationController::class, 'showByJobId']);
Route::get('/applications/candidate/{name}', [JobApplicationController::class, 'showByCandidateName']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
