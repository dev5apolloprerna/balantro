<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\GroupsController;
// use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\SupervisorsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\{
    SupervisorDashboardController,
    ClientDashboardController,
    ManagerDashboardController,
    SuperAdminDashboardController,
    LandingController,
    DocumentsController,
    ProfilesController,
    UserDevicesController
};
use App\Http\Controllers\GstSettingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['register' => true]);

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return 'Cache is cleared';
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created';
});

Route::get('/', [LandingController::class, 'index']);

Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});

// Route::get('/test-firebase', function () {
//     try {
//         $firebase = app('firebase');
//         $auth = $firebase->createAuth();
//         return 'Firebase connected successfully!';
//     } catch (\Exception $e) {
//         return 'Firebase error: ' . $e->getMessage();
//     }
// });

// WebSocket (replaces ActionCable)
// Route::post('/broadcasting/auth', function () {
//     return Broadcast::auth(request());
// });

// Auth::routes();
//, 'role:super_admin'
Route::middleware(['auth:web', 'nocache'])->group(function () {

    // Admin namespace
    Route::prefix('admin')->group(function () {

        Route::resource('groups', \App\Http\Controllers\GroupsController::class);
        Route::post('groups/{group}/assign_permissions', [\App\Http\Controllers\GroupsController::class, 'assignPermissions'])->name('groups.assignPermissions');
        Route::get('groups/{group}/get_permissions', [\App\Http\Controllers\GroupsController::class, 'getPermissions'])->name('groups.getPermissions');

        Route::resource('managers', \App\Http\Controllers\ManagersController::class)->except(['show']);
        Route::post('managers/{manager}/assign-groups', [\App\Http\Controllers\ManagersController::class, 'assignGroups'])->name('managers.assignGroups');
        Route::get('managers/{manager}/get-groups', [\App\Http\Controllers\ManagersController::class, 'getGroups'])->name('managers.getGroups');
        Route::post('managers/{manager}/assign-permissions', [\App\Http\Controllers\ManagersController::class, 'assignPermissions'])->name('managers.assignPermissions');
        Route::get('managers/{manager}/get-permissions', [\App\Http\Controllers\ManagersController::class, 'getPermissions'])->name('managers.getPermissions');

        Route::resource('supervisors', \App\Http\Controllers\SupervisorsController::class);
        Route::post('supervisors/{supervisor}/assign_managers', [\App\Http\Controllers\SupervisorsController::class, 'assignManagers'])->name('supervisors.assignManagers');;
        Route::post('supervisors/{supervisor}/assign_groups', [\App\Http\Controllers\SupervisorsController::class, 'assignGroups'])->name('supervisors.assignGroups');;
        Route::get('supervisors/{supervisor}/get_groups', [\App\Http\Controllers\SupervisorsController::class, 'getGroups'])->name('supervisors.getGroups');;
        Route::post('supervisors/{supervisor}/assign_permissions', [\App\Http\Controllers\SupervisorsController::class, 'assignPermissions'])->name('supervisors.assignPermissions');;
        Route::get('supervisors/{supervisor}/get_permissions', [\App\Http\Controllers\SupervisorsController::class, 'getPermissions'])->name('supervisors.getPermissions');;

        Route::resource('data_entry_operators', \App\Http\Controllers\DataEntryOperatorsController::class);

        Route::get('bulkupload/complete', [\App\Http\Controllers\DataEntryOperatorsController::class, 'bulkuploadcompletelist'])->name('data_entry_operators.bulkuploadcompletelist');
        Route::get('bulkupload/bankingcomplete', [\App\Http\Controllers\DataEntryOperatorsController::class, 'bulkuploadbankingcompletelist'])->name('data_entry_operators.bulkuploadbankingcompletelist');

        Route::get('/admin/bulkupload/sales', [\App\Http\Controllers\SalesUploadController::class, 'index'])->name('data_entry_operators.bulkuploadsales');
        Route::post('/sales-upload', [\App\Http\Controllers\SalesUploadController::class, 'upload'])->name('sales.upload');
        Route::get('/sales-preview/{id}', [\App\Http\Controllers\SalesUploadController::class, 'preview'])->name('sales.preview');
        Route::post('/sales-save',[\App\Http\Controllers\SalesUploadController::class,'save'])->name('sales.save');
        Route::post('/ledger-store',[\App\Http\Controllers\SalesUploadController::class,'storeLedger'])->name('sales.ledger.store');
        Route::post('/sales-delete/{id}', [\App\Http\Controllers\SalesUploadController::class, 'delete'])->name('sales.delete');
        Route::get('/select-company/{id}', [\App\Http\Controllers\SalesUploadController::class,'selectCompany'])->name('sales.select.company');
        // Route::get('/select-year/{year}', function ($year) {
        //     Session::put('year', $year);
        //     return back();
        // })->name('sales.select.year');
        Route::get('/select-year/{year}', function ($year) {

            Session::put('year', $year);

            // 🔥 Convert to date range
            [$startYear, $endYear] = explode('-', $year);

            $fromDate = $startYear . '-04-01';
            $toDate   = $endYear . '-03-31';
            
            Session::put('year_from', $fromDate);
            Session::put('year_to', $toDate);

            return back();
        })->name('sales.select.year');
        Route::post('/sales-update',[\App\Http\Controllers\SalesUploadController::class,'update'])->name('sales.update');
        Route::get('/sales/{id}/show',  [\App\Http\Controllers\SalesUploadController::class, 'show'])->name('sales.show');
        Route::post('/sales/update',    [\App\Http\Controllers\SalesUploadController::class, 'update'])->name('sales.update');
        Route::post('/sales-change-status', [\App\Http\Controllers\SalesUploadController::class, 'changeStatus'])->name('sales.change.status');
        Route::post('/sales-bulk-delete', [\App\Http\Controllers\SalesUploadController::class, 'bulkDelete'])->name('sales.bulk.delete');
        Route::post('/sales/manual-create', [\App\Http\Controllers\SalesUploadController::class, 'manualCreate'])->name('sales.manual.create');

        Route::get('/admin/bulkupload/purchase', [\App\Http\Controllers\PurchaseUploadController::class, 'index'])->name('data_entry_operators.bulkuploadpurchase');
        Route::post('/purchase-upload', [\App\Http\Controllers\PurchaseUploadController::class, 'upload'])->name('purchase.upload');
        Route::get('/purchase-preview/{id}', [\App\Http\Controllers\PurchaseUploadController::class, 'preview'])->name('purchase.preview');
        Route::post('/purchase-save',[\App\Http\Controllers\PurchaseUploadController::class,'save'])->name('purchase.save');
        Route::post('/purchase-delete/{id}', [\App\Http\Controllers\PurchaseUploadController::class, 'delete'])->name('purchase.delete');
        Route::post('/purchase-update',[\App\Http\Controllers\PurchaseUploadController::class,'update'])->name('purchase.update');
        Route::post('/purchase/ledger-store',[\App\Http\Controllers\PurchaseUploadController::class,'storeLedger'])->name('purchase.ledger.store');
        Route::get('/purchase/{id}/show',  [\App\Http\Controllers\PurchaseUploadController::class, 'show'])->name('purchase.show');
        Route::post('/purchase/update',    [\App\Http\Controllers\PurchaseUploadController::class, 'update'])->name('purchase.update');
        Route::post('/purchase-change-status', [\App\Http\Controllers\PurchaseUploadController::class, 'changeUploadStatus'])->name('purchase.upload.status');
        Route::post('/purchase-bulk-delete', [\App\Http\Controllers\PurchaseUploadController::class, 'bulkDelete'])->name('purchase.bulk.delete');
        Route::post('/purchase/manual-create', [\App\Http\Controllers\PurchaseUploadController::class, 'manualCreate'])->name('purchase.manual.create');

        // List page
        Route::get('/credit-notes', [\App\Http\Controllers\CreditNoteController::class, 'index'])->name('cn.index');
        Route::get('/credit-notes/upload',   [\App\Http\Controllers\CreditNoteController::class, 'uploadForm'])->name('cn.upload.form');
        Route::post('/credit-notes/upload',  [\App\Http\Controllers\CreditNoteController::class, 'upload'])->name('cn.upload');
        Route::get('/credit-notes/{id}',     [\App\Http\Controllers\CreditNoteController::class, 'show'])->name('cn.show');
        Route::get('/credit-preview/{id}',     [\App\Http\Controllers\CreditNoteController::class, 'preview'])->name('cn.preview');
        Route::post('/credit-notes/update',  [\App\Http\Controllers\CreditNoteController::class, 'update'])->name('cn.update');
        Route::post('/credit-notes/delete/{id}',  [\App\Http\Controllers\CreditNoteController::class, 'destroy'])->name('cn.delete');
        Route::post('/credit-notes/save',    [\App\Http\Controllers\CreditNoteController::class, 'save'])->name('cn.save');
        Route::post('/credit-notes/change-status', [\App\Http\Controllers\CreditNoteController::class, 'changeUploadStatus'])->name('cn.upload.status');
        Route::post('/credit-notes/bulk-delete', [\App\Http\Controllers\CreditNoteController::class, 'bulkDelete'])->name('cn.bulk.delete');
        Route::post('/credit-notes/manual-create', [\App\Http\Controllers\CreditNoteController::class, 'manualCreate'])->name('cn.manual.create');

        // List page
        Route::get('/debit-notes', [\App\Http\Controllers\DebitNoteController::class, 'index'])->name('dn.index');
        // Route::get('/debit-notes/upload',   [\App\Http\Controllers\DebitNoteController::class, 'uploadForm'])->name('dn.upload.form');
        Route::post('/debit-notes/upload',  [\App\Http\Controllers\DebitNoteController::class, 'upload'])->name('dn.upload');
        Route::get('/debit-notes/{id}',     [\App\Http\Controllers\DebitNoteController::class, 'show'])->name('dn.show');
        Route::get('/debit-preview/{id}',     [\App\Http\Controllers\DebitNoteController::class, 'preview'])->name('dn.preview');
        Route::post('/debit-notes/update',  [\App\Http\Controllers\DebitNoteController::class, 'update'])->name('dn.update');
        Route::post('/debit-notes/delete/{id}',  [\App\Http\Controllers\DebitNoteController::class, 'destroy'])->name('dn.delete');
        Route::post('/debit-notes/save',    [\App\Http\Controllers\DebitNoteController::class, 'save'])->name('dn.save');
        Route::post('/debit-notes/change-status', [\App\Http\Controllers\DebitNoteController::class, 'changeUploadStatus'])->name('dn.upload.status');
        Route::post('/debit-notes/bulk-delete', [\App\Http\Controllers\DebitNoteController::class, 'bulkDelete'])->name('dn.bulk.delete');
        Route::post('/debit-notes/manual-create', [\App\Http\Controllers\DebitNoteController::class, 'manualCreate'])->name('dn.manual.create');        

        Route::get('/journal', [\App\Http\Controllers\JournalController::class, 'index'])->name('journal.index');
        Route::post('/journal/upload', [\App\Http\Controllers\JournalController::class, 'upload'])->name('journal.upload');
        Route::get('/journal/preview/{id}', [\App\Http\Controllers\JournalController::class, 'preview'])->name('journal.preview');
        Route::get('/journal/show/{id}', [\App\Http\Controllers\JournalController::class, 'show'])->name('journal.show');
        Route::post('/journal/update', [\App\Http\Controllers\JournalController::class, 'update'])->name('journal.update');
        Route::post('/journal/save', [\App\Http\Controllers\JournalController::class, 'save'])->name('journal.save');
        Route::post('/journal/submit', [\App\Http\Controllers\JournalController::class, 'submit'])->name('journal.submit');
        Route::get('/journal/delete/{id}', [\App\Http\Controllers\JournalController::class, 'delete'])->name('journal.delete');
        Route::post('/journal/change-status', [\App\Http\Controllers\JournalController::class, 'changeUploadStatus'])->name('journal.upload.status');
        Route::post('/journal/bulk-delete', [\App\Http\Controllers\JournalController::class, 'bulkDelete'])->name('journal.bulk.delete');
        Route::post('/journal/manual-create', [\App\Http\Controllers\JournalController::class, 'manualCreate'])->name('journal.manual.create');        

        Route::get('/processing-sales',[\App\Http\Controllers\TransactionProcessingController::class,'processing_sales'])->name('transaction_processing.processing_sales');
        Route::get('/processing-sales/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_sales'])->name('transaction_processing.preview_processing_sales');
        Route::post('/processing-sales/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'sales_sumbit'])->name('transaction_processing.sales_sumbit');

        Route::get('/processing-purchase',[\App\Http\Controllers\TransactionProcessingController::class,'processing_purchase'])->name('transaction_processing.processing_purchase');
        Route::get('/processing-purchase/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_purchase'])->name('transaction_processing.preview_processing_purchase');
        Route::post('/processing-purchase/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'purchase_sumbit'])->name('transaction_processing.purchase_sumbit');

        Route::get('/processing-bank',[\App\Http\Controllers\TransactionProcessingController::class,'processing_bank'])->name('transaction_processing.processing_bank');
        Route::get('/processing-bank/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_bank'])->name('transaction_processing.preview_processing_bank');
        Route::post('/processing-bank/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'bank_sumbit'])->name('transaction_processing.bank_sumbit');

        Route::get('/processing-credit-note',[\App\Http\Controllers\TransactionProcessingController::class,'processing_credit_note'])->name('transaction_processing.processing_credit_note');
        Route::get('/processing-credit-note/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_credit_note'])->name('transaction_processing.preview_processing_credit_note');
        Route::post('/processing-credit-note/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'credit_note_sumbit'])->name('transaction_processing.credit_note_sumbit');

        Route::get('/processing-debit-note',[\App\Http\Controllers\TransactionProcessingController::class,'processing_debit_note'])->name('transaction_processing.processing_debit_note');
        Route::get('/processing-debit-note/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_debit_note'])->name('transaction_processing.preview_processing_debit_note');
        Route::post('/processing-debit-note/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'debit_note_sumbit'])->name('transaction_processing.debit_note_sumbit');

        Route::get('/processing-journal',[\App\Http\Controllers\TransactionProcessingController::class,'processing_journal'])->name('transaction_processing.processing_journal');
        Route::get('/processing-journal/preview/{id}',[\App\Http\Controllers\TransactionProcessingController::class,'preview_processing_journal'])->name('transaction_processing.preview_processing_journal');
        Route::post('/processing-journal/sumbit',[\App\Http\Controllers\TransactionProcessingController::class,'journal_sumbit'])->name('transaction_processing.journal_sumbit');


        Route::get('/bulkupload/bankstatement', [\App\Http\Controllers\BankUploadController::class, 'index'])->name('data_entry_operators.bulkuploadbankstatement');
        Route::post('bank-upload', [\App\Http\Controllers\BankUploadController::class, 'upload'])->name('bank.upload');
        Route::get('/bank-preview/{id}', [\App\Http\Controllers\BankUploadController::class, 'preview'])->name('bank.preview');
        Route::post('/bank-save',[\App\Http\Controllers\BankUploadController::class,'save'])->name('bank.save');
        Route::post('/bank-delete/{id}', [\App\Http\Controllers\BankUploadController::class,'delete'])->name('bank.delete');
        Route::post('/bank-update',[\App\Http\Controllers\BankUploadController::class,'update'])->name('bank.update');
        Route::post('/bank-change-status', [\App\Http\Controllers\BankUploadController::class, 'changeUploadStatus'])->name('bank.upload.status');
        Route::post('/bank-bulk-delete', [\App\Http\Controllers\BankUploadController::class, 'bulkDelete'])->name('bank.bulk.delete');
        Route::post('/bank/mark-suspense', [\App\Http\Controllers\BankUploadController::class, 'markSuspense'])->name('bank.markSuspense');
        

        Route::get('data_entry_operators/{operator}/manager_supervisors', [\App\Http\Controllers\DataEntryOperatorsController::class, 'managerSupervisors'])->name('data_entry_operators.managerSupervisors');
        Route::get('data_entry_operators/{operator}/get_groups', [\App\Http\Controllers\DataEntryOperatorsController::class, 'getGroups'])->name('data_entry_operators.getGroups');
        Route::get('data_entry_operators/{operator}/get_permissions', [\App\Http\Controllers\DataEntryOperatorsController::class, 'getPermissions'])->name('data_entry_operators.getPermissions');
    
        Route::post('data_entry_operators/{data_entry_operator}/assign_permissions', [\App\Http\Controllers\DataEntryOperatorsController::class, 'assignPermissions'])->name('data_entry_operators.assign_permissions');
        Route::post('data_entry_operators/{data_entry_operator}/assign_groups', [\App\Http\Controllers\DataEntryOperatorsController::class, 'assignGroups'])->name('data_entry_operators.assign_groups');
        Route::post('data_entry_operators/{data_entry_operator}/assign_users', [\App\Http\Controllers\DataEntryOperatorsController::class, 'assignUsers'])->name('data_entry_operators.assign_users');

        Route::any('/clients/input', [\App\Http\Controllers\ClientsController::class, 'suspense'])->name('clients.suspense');
        Route::post('/bank/resolve-suspense', [\App\Http\Controllers\ClientsController::class, 'resolveSuspense'])->name('clients.resolveSuspense');
        Route::get('/clients/resolved-suspense', [\App\Http\Controllers\ClientsController::class, 'resolvedSuspense'])->name('clients.resolvedSuspense');
        Route::post('/clients/update-remark',[\App\Http\Controllers\ClientsController::class, 'updateRemark'])->name('clients.updateRemark');
        
        Route::resource('clients', \App\Http\Controllers\ClientsController::class);

        // Assign Users (by client)
        Route::post('clients/{client}/assign-users', [\App\Http\Controllers\ClientsController::class, 'assignUsers'])->name('clients.assignUsers'); // Cascade loads for the Assign Users modal
        Route::get('clients/manager/{manager}/supervisors', [\App\Http\Controllers\ClientsController::class, 'managerSupervisors'])->name('clients.managerSupervisors'); // expects Manager $manager
        Route::get('clients/supervisor/{supervisor}/data-entry-operators', [\App\Http\Controllers\ClientsController::class, 'supervisorDataEntryOperators'])->name('clients.supervisorDataEntryOperators'); // expects Supervisor $supervisor
        // Groups
        Route::post('clients/{client}/assign-groups', [\App\Http\Controllers\ClientsController::class, 'assignGroups'])->name('clients.assignGroups');
        Route::get('clients/{client}/groups', [\App\Http\Controllers\ClientsController::class, 'getGroups'])->name('clients.getGroups');
        // Permissions
        Route::post('clients/{client}/assign-permissions', [\App\Http\Controllers\ClientsController::class, 'assignPermissions'])->name('clients.assignPermissions');
        Route::get('clients/{client}/permissions', [\App\Http\Controllers\ClientsController::class, 'getPermissions'])->name('clients.getPermissions');
        Route::patch('clients/{client}/status', [\App\Http\Controllers\ClientsController::class, 'toggleStatus'])->name('clients.toggleStatus');
        Route::any('clients/dashboard/{guid}', [\App\Http\Controllers\ClientsController::class, 'dashboard'])->name('clients.dashboard');
        Route::any('/clients/{guid}/documents/dashboard', [\App\Http\Controllers\ClientsController::class, 'documentDashboard'])->name('clients.documents.dashboard');

        Route::any('clients/reports/pnl/{guid}', [\App\Http\Controllers\ClientsController::class, 'pnl'])->name('clients.reports.pnl');
        Route::any('clients/reports/balanceSheet/{guid}', [\App\Http\Controllers\ClientsController::class, 'balanceSheet'])->name('clients.reports.balanceSheet');
        Route::any('clients/reports/ledger/{guid}', [\App\Http\Controllers\ClientsController::class, 'ledger'])->name('clients.reports.ledger');
        Route::any('clients/reports/voucher/History', [\App\Http\Controllers\ClientsController::class, 'voucherHistory'])->name('clients.reports.voucherHistory');
        Route::any('clients/reports/voucher/{guid}/{strGUID}/{vchType}',[\App\Http\Controllers\ClientsController::class, 'viewVoucher'])->name('clients.reports.voucher-history.viewVoucher');
        
        Route::get('/clients/Gst-Setting/index/{guid}',[\App\Http\Controllers\ClientsController::class, 'Gstindex'])->name('clients.Gstindex');
        Route::post('/clients/Gst-Setting/update',[\App\Http\Controllers\ClientsController::class, 'GstSettingupdate'])->name('clients.GstSettingupdate');
        Route::post('/clients/roundoff-setting/update',[\App\Http\Controllers\ClientsController::class, 'updateRoundoffSetting'])->name('clients.updateRoundoffSetting');
        Route::post('/clients/gst-mapping/save',[\App\Http\Controllers\ClientsController::class,'saveGstMapping'])->name('clients.saveGstMapping');
        Route::delete('/clients/gst-mapping/delete/{id}',[\App\Http\Controllers\ClientsController::class,'deleteGstMapping'])->name('clients.deleteGstMapping');
        Route::post('/clients/item-gst-mapping/save',[\App\Http\Controllers\ClientsController::class,'saveItemGstMapping'])->name('clients.saveItemGstMapping');
        Route::delete('/clients/item-gst-mapping/delete/{id}',[\App\Http\Controllers\ClientsController::class,'deleteItemGstMapping'])->name('clients.deleteItemGstMapping');

        Route::get('/documents/index',        [\App\Http\Controllers\DocumentsController::class, 'index'])->name('documents.index');

        Route::delete('/documents/{document}', [\App\Http\Controllers\DocumentsController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{id}/download', [\App\Http\Controllers\DocumentsController::class, 'download'])->name('documents.download');
        Route::post('/documents', [\App\Http\Controllers\DocumentsController::class, 'store'])->name('documents.store'); // ← NEW
        //Route::patch('documents/{document}/verify', [\App\Http\Controllers\DocumentsController::class, 'verify']);
        Route::post('/documents/verify', [\App\Http\Controllers\DocumentsController::class, 'verify'])->name('documents.verify');
        Route::post('/documents/sup_verify', [\App\Http\Controllers\DocumentsController::class, 'sup_verify'])->name('documents.sup_verify');
        Route::put('/documents/{document}', [\App\Http\Controllers\DocumentsController::class, 'update'])->name('documents.update');
        Route::get('/documents/{document}/edit', [\App\Http\Controllers\DocumentsController::class, 'edit'])->name('documents.edit');

        Route::get('/documents/{document}/doc_activities', [\App\Http\Controllers\DocumentActivityController::class, 'index'])->name('documents.activities');
        //Route::resource('messages', \App\Http\Controllers\MessagesController::class)->only(['index', 'create']);
        Route::get('/client/messages', [\App\Http\Controllers\ClientMessagesController::class, 'index'])->name('client.messages.index');
        Route::post('/client/messages', [\App\Http\Controllers\ClientMessagesController::class, 'store'])->name('client.messages.store');

        // Supervisor
        Route::get('/supervisor/messages', [\App\Http\Controllers\SupervisorMessagesController::class, 'index'])->name('supervisor.messages.index');
        Route::post('/supervisor/messages', [\App\Http\Controllers\SupervisorMessagesController::class, 'store'])->name('supervisor.messages.store');
        // Route::post('/supervisor/conversations/{id}/reassign', ...)->name('supervisor.conversations.reassign');

        Route::get('/deo/messages',  [\App\Http\Controllers\DataEntryOperatorMessagesController::class, 'index'])->name('deo.messages.index');
        Route::post('/deo/messages', [\App\Http\Controllers\DataEntryOperatorMessagesController::class, 'store'])->name('deo.messages.store');

        Route::get('/manager/messages',  [\App\Http\Controllers\ManagerMessagesController::class, 'index'])->name('manager.messages.index');
        Route::post('/manager/messages', [\App\Http\Controllers\ManagerMessagesController::class, 'store'])->name('manager.messages.store');

        Route::post('/dashboard/save-card-preferences', [App\Http\Controllers\HomeController::class, 'saveCardPreferences'])->name('dashboard.save-card-preferences');
    });
});

Route::prefix('settings')->middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/gst-setting',[GstSettingController::class, 'index'])->name('gst.setting');
        Route::post('/gst-setting',[GstSettingController::class, 'update'])->name('gst.setting.update');
    });
});

// Profiles routes
Route::resource('profile', ProfilesController::class)->except(['index']);
Route::post('profile/pincode-details', [ProfilesController::class, 'fetchPincodeDetails'])->name('profile.pincode-details');
Route::get('user/profile', [ProfilesController::class, 'userProfileEdit'])->name('profile.userProfileEdit');

Route::post('user/update', [ProfilesController::class, 'userProfileUpdate'])->name('profile.userProfileUpdate');

Route::get('/profile/documents', [ProfilesController::class, 'documents'])->name('profile.documents');
Route::post('/profile/documents/upload', [ProfilesController::class, 'uploadDocuments'])->name('profile.documents.upload');
Route::get('/profile/documents/download/{type}', [ProfilesController::class, 'downloadDocument'])->name('profile.documents.download');

// Password
Route::get('/change-password', [ProfilesController::class, 'changePassword'])->name('profile.change_password');
Route::post('/change-password', [ProfilesController::class, 'updatePassword'])->name('profile.update_password');


// User devices
Route::resource('user_devices', UserDevicesController::class)->only(['create']);
Route::delete('user_devices', [UserDevicesController::class, 'destroy']);

// Queue monitoring (replaces Sidekiq)
Route::middleware(['auth:web', 'role:super_admin'])->group(function () {
    Route::get('/horizon', function () {
        return view('horizon::dashboard');
    })->middleware('can:viewHorizon');
});


// Route::prefix('admin')
//     ->name('admin.')
//     ->group(function () {
//         Route::resource('permissions', PermissionsController::class);
//     });

// Password reset routes
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')
    ->name('password.email');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')
    ->name('password.update');

Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/theme/update', [App\Http\Controllers\ThemeController::class, 'update'])->name('theme.update');
Route::middleware(['auth']) // add role middleware if needed: ->middleware(['auth','role:client,admin'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {
        Route::any('/pl',            [ReportsController::class, 'pl'])->name('pl');           // route('reports.pl')
        Route::get('/pl/pdf', [ReportsController::class, 'exportPdf'])->name('pl.pdf');
        Route::get('/pl/excel', [ReportsController::class, 'exportExcel'])->name('pl.excel');

        Route::any('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance_sheet'); // route('reports.balance')
        Route::get('/balance-sheet/export-excel', [ReportsController::class, 'exportBalanceSheetExcel'])->name('balance-sheet.export-excel');
        Route::get('/balance-sheet/export-pdf', [ReportsController::class, 'exportBalanceSheetPDF'])->name('balance-sheet.export-pdf');

        Route::any('/ledger',        [ReportsController::class, 'ledger'])->name('ledger');   // route('reports.ledger')
        Route::get('/ledger/export-excel', [ReportsController::class, 'exportLedgerExcel'])->name('ledger.export-excel');
        Route::get('/ledger/export-pdf', [ReportsController::class, 'exportLedgerPDF'])->name('ledger.export-pdf');

        Route::any('/voucher-history', [ReportsController::class, 'voucherHistory'])->name('voucher_history');
        Route::get('/voucher-history/export-excel', [ReportsController::class, 'exportVoucherHistoryExcel'])->name('voucher-history.export-excel');
        Route::get('/voucher-history/export-pdf', [ReportsController::class, 'exportVoucherHistoryPDF'])->name('voucher-history.export-pdf');

        Route::get('/reports/voucher/{strGUID}/{vchType}',[ReportsController::class, 'viewVoucher'])->name('voucher-history.viewVoucher');
        Route::get('/reports/voucher/export/pdf/{strGUID}/{vchType}/{guid?}',[ReportsController::class, 'exportVoucherPDF'])->name('voucher.export.pdf');
        Route::get('/reports/voucher/export/excel/{strGUID}/{vchType}/{guid?}', [ReportsController::class, 'exportVoucherExcel'])->name('voucher.export.excel');
        // ->where('vchNo', '[^/]+/[^/]+')
        Route::any('/graph', [ReportsController::class, 'graph'])->name('graph');
    });
Route::get('/balance-sheet/print', [ReportsController::class, 'printBalanceSheet'])
    ->name('reports.balance-sheet.print');
    //-------------25-04-kush-----/
Route::prefix('admin')->name('super-admin.')->middleware(['auth'])->group(function () {
    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('blog/store', [BlogController::class, 'store'])->name('blog.store');
    Route::get('blog/edit/{blog_id}', [BlogController::class, 'edit'])->name('blog.edit');
    Route::post('blog/update/{blog_id}', [BlogController::class, 'update'])->name('blog.update');
    Route::post('blog/delete/{blog_id}', [BlogController::class, 'destroy'])->name('blog.delete');
    Route::post('blog/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blog.bulkDelete');
});

Route::get('/', [FrontendController::class, 'index'])->name('homeindex');
Route::get('/features', [FrontendController::class, 'features'])->name('features');
Route::get('/services', [FrontendController::class, 'services'])->name('services');
Route::get('/company', [FrontendController::class, 'company'])->name('company');
Route::get('/resources', [FrontendController::class, 'resources'])->name('resources');
Route::get('/guides', [FrontendController::class, 'guides'])->name('guides');
Route::get('/faqs', [FrontendController::class, 'faqs'])->name('faqs');
Route::get('/insights', [FrontendController::class, 'insights'])->name('insights');
// Route::get('/insight-detail', [FrontendController::class, 'insightDetail'])->name('insight.detail');
Route::get('/insight-detail/{slugname}', [FrontendController::class, 'insightDetail'])
    ->name('insight.detail');


Route::post('/logout-idle', function () {
    Auth::logout();
    Session::invalidate();
    Session::regenerateToken();
    return response()->json([
        'success' => true
    ]);
})->name('logout.idle');
