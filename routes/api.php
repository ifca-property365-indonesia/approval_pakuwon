<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// use App\Http\Controllers\MailDataController as MailData;

// Route::POST('/maildata', [MailData::class, 'receive']);
// Route::GET('/processdata/{module}/{status}/{encrypt}', [MailData::class, 'processData']);
// Route::POST('/getaccess', [MailData::class, 'getAccess']);

use App\Http\Controllers\StaffActionController as StaffAction;
Route::POST('/fileexist', [StaffAction::class, 'fileexist']);
Route::POST('/feedbackland', [StaffAction::class, 'feedback_land']);

use App\Http\Controllers\GetApprControllers as GetAppr;
Route::POST('/getappr', [GetAppr::class, 'Index']);
Route::POST('/getapprDetail', [GetAppr::class, 'Detail']);

use App\Http\Controllers\LandfphController as Landfph;
Route::POST('/landfph', [Landfph::class, 'index']);
Route::GET('/landfph/{status}/{encrypt}', [Landfph::class, 'processData']);
Route::POST('/landfph/getaccess', [Landfph::class, 'getaccess']);
Route::POST('/landfph/feedback', [Landfph::class, 'feedback_fph']);

use App\Http\Controllers\LandVerificationController as LandVerification;
Route::POST('/landverification', [LandVerification::class, 'index']);
Route::GET('/landverification/{status}/{encrypt}', [LandVerification::class, 'processData']);
Route::POST('/landverification/getaccess', [LandVerification::class, 'getaccess']);
Route::POST('/landverification/feedback', [LandVerification::class, 'feedback_verification']);

use App\Http\Controllers\LandMeasuringController as LandMeasuring;
Route::POST('/landmeasuring', [LandMeasuring::class, 'index']);
Route::GET('/landmeasuring/{status}/{encrypt}', [LandMeasuring::class, 'processData']);
Route::POST('/landmeasuring/getaccess', [LandMeasuring::class, 'getaccess']);