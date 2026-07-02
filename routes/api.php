<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CodeMasterController;
use App\Http\Controllers\Api\PandLAccountController;
use App\Http\Controllers\Api\BalanceSheetController;
use App\Http\Controllers\Api\LedgerMasterController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Http\Controllers\Api\V1\MessagesController;
use App\Http\Controllers\Api\V1\ClientBankSuspenseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/api-test', function () {
    return response()->json(['status' => 'API is working']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/code-master', [CodeMasterController::class, 'search']);


// Route::get('/code-master', [CodeMasterController::class, 'search']);

// API Routes
Route::prefix('v1')->group(function () {
    Route::post('sign_in', [\App\Http\Controllers\Api\V1\SessionsController::class, 'login']);
    Route::post('sign_up', [\App\Http\Controllers\Api\V1\RegistrationsController::class, 'register']);
    Route::post('logout', [\App\Http\Controllers\Api\V1\SessionsController::class, 'destroy']);
    Route::post('/auth/forgot_password', [ForgotPasswordOtpController::class, 'forgot_password']);
    Route::post('/auth/password/verify-otp', [ForgotPasswordOtpController::class, 'verifyOtp']);
    Route::post('/auth/password/reset-password', [ForgotPasswordOtpController::class, 'resetPassword']);
    Route::post('/auth/password/resend-otp', [ForgotPasswordOtpController::class, 'resendOtp']);
    Route::post('refreshFcmToken', [\App\Http\Controllers\Api\V1\SessionsController::class, 'refreshFcmToken']);
    Route::post('emp_sign_in', [\App\Http\Controllers\Api\V1\SessionsController::class, 'emp_login']);
    Route::get('/pl/export/download/{filename}', [PandLAccountController::class, 'downloadExportedFile'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('api.pl.export.download');

    Route::get('/balance-sheet/export/download/{filename}', [BalanceSheetController::class, 'downloadExportedFile'])
        ->where('filename', 'balance-sheet-[A-Za-z0-9._-]+-to-[A-Za-z0-9._-]+\.(xlsx|pdf)')
        ->name('api.balance-sheet.export.download');

    Route::get('/ledger/export/download/{filename}', [LedgerMasterController::class, 'downloadExportedFile'])
        ->where('filename', '(?:(?:ledger-report|voucher-history)-[A-Za-z0-9._-]+-to-[A-Za-z0-9._-]+|voucher-[A-Za-z0-9._-]+)\.(xlsx|pdf)')
        ->name('api.ledger.export.download');
    // Resource routes
    Route::middleware('auth:api')->group(function () {
        //Route::apiResource('client_profile', \App\Http\Controllers\Api\V1\\App\Http\Controllers\Api\V1\ClientProfilesController::class)->only(['show', 'update']);
        Route::post('change_password', [\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'changePassword']);
        Route::post('client_profile', [\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'update']);
        Route::get('client_profile/show', [\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'show']);
        Route::post('/profile/business-types',[\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'businessTypes']);
        // Documents
        Route::post('/profile/documents/upload', [\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'uploadDocuments']);
        Route::post('/profile/documents', [\App\Http\Controllers\Api\V1\ClientProfilesController::class, 'documents']);

        Route::post('/bank/suspense',[ClientBankSuspenseController::class, 'suspense']);
        Route::post('/bank/resolve-suspense',[ClientBankSuspenseController::class, 'resolveSuspense']);

        //Route::apiResource('documents', \App\Http\Controllers\Api\V1\DocumentsController::class)->except(['show']);
        Route::post('documents/list', [\App\Http\Controllers\Api\V1\DocumentsController::class, 'index']);
        // Create a new document
        Route::post('documents', [\App\Http\Controllers\Api\V1\DocumentsController::class, 'store']);
        // Update a document
        //Route::put('documents/{document}', [\App\Http\Controllers\Api\V1\DocumentsController::class, 'update']);
        Route::post('documents/update', [\App\Http\Controllers\Api\V1\DocumentsController::class, 'update']);
        Route::post('documents/delete', [\App\Http\Controllers\Api\V1\DocumentsController::class, 'destroy']);
        // Route::apiResource('dashboard', \App\Http\Controllers\Api\V1\DashboardController::class)->only(['index']);
        
        Route::prefix('dashboard')->group(function () {
            Route::post('/', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
            Route::post('preferences', [\App\Http\Controllers\Api\V1\DashboardController::class, 'saveCardPreferences']);
            Route::post('financial-graphs', [\App\Http\Controllers\Api\V1\DashboardController::class, 'financialGraphs']);
            Route::post('profit-loss-balance-sheet', [\App\Http\Controllers\Api\V1\DashboardController::class, 'profitLossBalanceSheet']);
            Route::post('monthly-financial-columns', [\App\Http\Controllers\Api\V1\DashboardController::class, 'monthlyFinancialColumns']);
            Route::post('year-listing', [\App\Http\Controllers\Api\V1\DashboardController::class, 'yearListing']);
            Route::post('group-balances', [\App\Http\Controllers\Api\V1\DashboardController::class, 'getGroupBalances']);
        });

        Route::post('dropdown_type_list', [\App\Http\Controllers\Api\V1\DashboardController::class, 'dropdown_type_list']);
        Route::post('dashboard/graph', [\App\Http\Controllers\Api\V1\DashboardController::class, 'dropdown_graph']);

        //Route::apiResource('messages', \App\Http\Controllers\Api\V1\MessagesController::class)->only(['index']);
        Route::post('/PandL-account', [PandLAccountController::class, 'index_new']);
		Route::post('/pl/export/excel', [PandLAccountController::class, 'exportExcel']);
        Route::post('/pl/export/pdf', [PandLAccountController::class, 'exportPdf']);
        
        // Direct download routes (optional)
        Route::get('/pl/download/excel', [PandLAccountController::class, 'downloadExcel']);
        Route::get('/pl/download/pdf', [PandLAccountController::class, 'downloadPdf']);
		
        Route::post('/balance-sheet', [BalanceSheetController::class, 'index_new']);
		Route::post('/balance-sheet/export/excel', [BalanceSheetController::class, 'exportExcel']);
		Route::post('/balance-sheet/export/pdf', [BalanceSheetController::class, 'exportPdf']);
		Route::post('/balance-sheet/download/excel', [BalanceSheetController::class, 'downloadExcel']);
		Route::post('/balance-sheet/download/pdf', [BalanceSheetController::class, 'downloadPdf']);
		
        Route::post('/ledger', [LedgerMasterController::class, 'index_new']);
        Route::post('/ledger_voucher_history', [LedgerMasterController::class, 'vch_history_new']);
		
		// Ledger Export Routes
		Route::post('/ledger/export/excel', [LedgerMasterController::class, 'exportLedgerExcel']);
		Route::post('/ledger/export/summary-excel', [LedgerMasterController::class, 'exportLedgerSummaryExcel']);
		Route::post('/ledger/export/pdf', [LedgerMasterController::class, 'exportLedgerPdf']);

		// Voucher History Export Routes
		Route::post('/voucher-history/export/excel', [LedgerMasterController::class, 'exportVoucherHistoryExcel']);
		Route::post('/voucher-history/export/pdf', [LedgerMasterController::class, 'exportVoucherHistoryPdf']);

        // Voucher Details Routes
		Route::post('/voucher/details', [LedgerMasterController::class, 'voucherDetails']);
		Route::post('/voucher/export/excel', [LedgerMasterController::class, 'exportVoucherDetailsExcel']);
		Route::post('/voucher/export/pdf', [LedgerMasterController::class, 'exportVoucherDetailsPdf']);
        
		// Direct Download Routes
		Route::post('/ledger/download/excel', [LedgerMasterController::class, 'downloadLedgerExcel']);
        Route::post('/ledger/download/pdf', [LedgerMasterController::class, 'downloadLedgerPdf']);
		Route::post('/voucher-history/download/excel', [LedgerMasterController::class, 'downloadVoucherHistoryExcel']);
		
        Route::post('/group_master', [LedgerMasterController::class, 'group_master']);
        // Route::post('/dropdown_type_list', [\App\Http\Controllers\Api\V1\DashboardController::class, 'dropdown_type_list']);
        // Route::post('/dashboard/graph', [\App\Http\Controllers\Api\V1\DashboardController::class, 'dropdown_graph']);
        
        // Route::get('messages', [\App\Http\Controllers\Api\V1\MessagesController::class, 'index']);
        // List messages (optionally filter by a specific participant)
        Route::post('/client/messages', [MessagesController::class, 'index']);

        // Send a message (client -> DEO only). Supports attachments (multipart/form-data)
        Route::post('/client/messages/store', [MessagesController::class, 'store']);
    });
});
// Route::middleware('auth:api')->group(function () {
//     Route::post('/device-token', [DeviceTokenController::class, 'store']);
//     Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);
//     Route::get('/device-tokens', [DeviceTokenController::class, 'index']);
// });
