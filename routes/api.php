<?php

use App\Http\Controllers\Api\AuthUserController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\WeeklyRepaymentController;
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

Route::post('/register', [AuthUserController::class, 'registerUser']);
Route::post('/login', [AuthUserController::class, 'loginUser']);

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('/loans', LoanController::class);
    Route::post('/loan-status', [LoanController::class, 'changeLoanStatus']);
    Route::post('/loan-repayment', [WeeklyRepaymentController::class, 'loanRepayment']);
    Route::post('/logout', [AuthUserController::class, 'logoutUser']);
});
