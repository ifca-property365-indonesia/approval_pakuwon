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
Route::POST('/Gettotaldata', [GetAppr::class, 'GetTotalData']);

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

use App\Http\Controllers\LandSphController as LandSph;
Route::POST('/landsph', [LandSph::class, 'index']);
Route::GET('/landsph/{status}/{encrypt}', [LandSph::class, 'processData']);
Route::POST('/landsph/getaccess', [LandSph::class, 'getaccess']);

use App\Http\Controllers\LandMapController as LandMap;
Route::POST('/landmap', [LandMap::class, 'index']);
Route::GET('/landmap/{status}/{encrypt}', [LandMap::class, 'processData']);
Route::POST('/landmap/getaccess', [LandMap::class, 'getaccess']);

use App\Http\Controllers\LandMeasuringSftController as landmeasuringSft;
Route::POST('/landmeasuringsft', [landmeasuringSft::class, 'index']);
Route::GET('/landmeasuringsft/{status}/{encrypt}', [landmeasuringSft::class, 'processData']);
Route::POST('/landmeasuringsft/getaccess', [landmeasuringSft::class, 'getaccess']);

use App\Http\Controllers\LandSftProposeController as LandSftPropose;
Route::POST('/landsftpropose', [LandSftPropose::class, 'index']);
Route::GET('/landsftpropose/{status}/{encrypt}', [LandSftPropose::class, 'processData']);
Route::POST('/landsftpropose/getaccess', [LandSftPropose::class, 'getaccess']);

use App\Http\Controllers\LandSftBphtbController as LandSftBphtb;
Route::POST('/landsftbphtb', [LandSftBphtb::class, 'index']);
Route::GET('/landsftbphtb/{status}/{encrypt}', [LandSftBphtb::class, 'processData']);
Route::POST('/landsftbphtb/getaccess', [LandSftBphtb::class, 'getaccess']);

use App\Http\Controllers\LandBoundaryController as LandBoundary;
Route::POST('/landboundary', [LandBoundary::class, 'index']);
Route::GET('/landboundary/{status}/{encrypt}', [LandBoundary::class, 'processData']);
Route::POST('/landboundary/getaccess', [LandBoundary::class, 'getaccess']);

use App\Http\Controllers\LandChangeNameController as LandChangeName;
Route::POST('/landchangename', [LandChangeName::class, 'index']);
Route::GET('/landchangename/{status}/{encrypt}', [LandChangeName::class, 'processData']);
Route::POST('/landchangename/getaccess', [LandChangeName::class, 'getaccess']);

use App\Http\Controllers\LandSftShgbController as LandSftShgb;
Route::POST('/landsftshgb', [LandSftShgb::class, 'index']);
Route::GET('/landsftshgb/{status}/{encrypt}', [LandSftShgb::class, 'processData']);
Route::POST('/landsftshgb/getaccess', [LandSftShgb::class, 'getaccess']);

use App\Http\Controllers\LandHandoverShgbController as LandHandoverShgb;
Route::POST('/landhandovershgb', [LandHandoverShgb::class, 'index']);
Route::GET('/landhandovershgb/{status}/{encrypt}', [LandHandoverShgb::class, 'processData']);
Route::POST('/landhandovershgb/getaccess', [LandHandoverShgb::class, 'getaccess']);

use App\Http\Controllers\LandCancelNopController as LandCancelNop;
Route::POST('/landcancelnop', [LandCancelNop::class, 'index']);
Route::GET('/landcancelnop/{status}/{encrypt}', [LandCancelNop::class, 'processData']);
Route::POST('/landcancelnop/getaccess', [LandCancelNop::class, 'getaccess']);

use App\Http\Controllers\LandHandoverLegalController as LandHandoverLegal;
Route::POST('/landhandoverlegal', [LandHandoverLegal::class, 'index']);
Route::GET('/landhandoverlegal/{status}/{encrypt}', [LandHandoverLegal::class, 'processData']);
Route::POST('/landhandoverlegal/getaccess', [LandHandoverLegal::class, 'getaccess']);

use App\Http\Controllers\LandSplitShgbController as LandSplitShgb;
Route::POST('/landsplitshgb', [LandSplitShgb::class, 'index']);
Route::GET('/landsplitshgb/{status}/{encrypt}', [LandSplitShgb::class, 'processData']);
Route::POST('/landsplitshgb/getaccess', [LandSplitShgb::class, 'getaccess']);

use App\Http\Controllers\LandMergeShgbController as LandMergeShgb;
Route::POST('/landmergeshgb', [LandMergeShgb::class, 'index']);
Route::GET('/landmergeshgb/{status}/{encrypt}', [LandMergeShgb::class, 'processData']);
Route::POST('/landmergeshgb/getaccess', [LandMergeShgb::class, 'getaccess']);

use App\Http\Controllers\LandExtensionShgbController as LandExtensionShgb;
Route::POST('/landextensionshgb', [LandExtensionShgb::class, 'index']);
Route::GET('/landextensionshgb/{status}/{encrypt}', [LandExtensionShgb::class, 'processData']);
Route::POST('/landextensionshgb/getaccess', [LandExtensionShgb::class, 'getaccess']);