<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Backend\PropertyController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Backend\PropertyTypeController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Agent\AgentPropertyController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CompareController;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\DiscountMethodController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Models\SmtpSetting;
use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\TransferVerificationController;
use App\Http\Controllers\AgentReportController;
use App\Http\Controllers\Admin\AgentPaymentReportController;
use App\Http\Controllers\AdminFeeController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CashBoxController;


Route::get('/', [HomeController::class, 'homeRedirect'])->middleware('auth');
Route::get('/clear', function () {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return view('frontend.index'); // أو أي View تريده
});

Route::post('/github-deploy', function (Request $request) {
    Log::info('Webhook triggered', $request->all());
    // يمكنك هنا تنفيذ عملية نشر أو أي أمر آخر
    return response('Webhook received', 200);
});

//User Management Group Middleware
Route::middleware(['auth', 'roles:user'])->group(function () {

    Route::get('/user/profile', [UserController::class, 'UserProfile'])->name('user.profile');
    Route::post('/user/profile/store', [UserController::class, 'UserProfileStore'])->name('user.profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
       Route::post('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');

    Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');
    Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.pasword.update');

    // مسارات حاسبة العمولة
    Route::get('/commission/calculator', [CommissionController::class, 'calculator'])->name('commission.calculator');
    Route::post('/commission/calculate', [CommissionController::class, 'calculateCommission'])->name('commission.calculate');
});

require __DIR__ . '/auth.php';

//Admin Management Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminController::class, 'AdminReports'])->name('admin.reports');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/passowrd', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/update/password', [AdminController::class, 'AdminUpdatePassword'])->name('admin.update.password');

    //Role & Permission, Import-Export Excel File All route
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('store.permission');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');

        //Import-Export Excel File
        Route::get('/import/permission', 'ImportPermission')->name('import.permission');
        Route::get('/export', 'Export')->name('export');
        Route::post('/import', 'Import')->name('import');
    });

    //ALl Role
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/role', 'AllRole')->name('all.role');
        Route::post('/store/role', 'StoreRole')->name('store.role');
        Route::get('/edit/role/{id}', 'EditRole')->name('edit.role');
        Route::post('/update/role', 'UpdateRole')->name('update.role');
        Route::get('/delete/role/{id}', 'DeleteRole')->name('delete.role');

        Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
        Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');
        Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');
        Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles');
        Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');
        Route::get('/admin/delete/roles/{id}', 'AdminDeleteRoles')->name('admin.delete.roles');
    });


    // Admin User All Route
    Route::controller(AdminController::class)->group(function () {
        Route::get('/all/admin', 'AllAdmin')->name('all.admin');
        Route::get('/add/admin', 'AddAdmin')->name('add.admin');
        Route::post('/store/admin', 'StoreAdmin')->name('store.admin');
        Route::get('/edit/admin/{id}', 'EditAdmin')->name('edit.admin');
        Route::post('/update/admin/{id}', 'UpdateAdmin')->name('update.admin');
        Route::get('/delete/admin/{id}', 'DeleteAdmin')->name('delete.admin');
     });

      // Agent All Route from admin
        Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/all/agent', 'AllAgent')->name('all.superagent');
        Route::get('/admin/add/agent', 'AddAgent')->name('add.superagent');
        Route::post('/admin/store/agent', 'StoreAgent')->name('store.superagent');
        Route::get('/admin/edit/agent/{id}', 'EditAgent')->name('edit.superagent');
        Route::post('/admin/update/agent', 'UpdateAgent')->name('update.superagent');
        Route::get('/admin/delete/agent/{id}', 'DeleteAgent')->name('delete.superagent');
        Route::get('/admin/changeStatus', 'changeStatus');

});

 }); //End Group Admin Middleware


  //Agent Management Group Middleware
  Route::middleware(['auth', 'roles:agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'AgentDashboard'])->name('agent.dashboard');
    Route::get('/agent/logout', [AgentController::class, 'AgentLogout'])->name('agent.logout');
    Route::post('/agent/logout', [AgentController::class, 'AgentLogout'])->name('agent.logout');
    Route::get('/agent/profile', [AgentController::class, 'AgentProfile'])->name('agent.profile');
    Route::post('/agent/profile/store', [AgentController::class, 'AgentProfileStore'])->name('agent.profile.store');
    Route::get('/agent/change/passowrd', [AgentController::class, 'AgentChangePassword'])->name('agent.change.password');
    Route::post('/agent/update/password', [AgentController::class, 'AgentUpdatePassword'])->name('agent.update.password');
    Route::get('/agent/payments/history', [AgentController::class, 'paymentHistory'])->name('agent.payments.history');
    Route::get('/agent/payments/grouped', [AgentController::class, 'paymentsGroupedByDate'])->name('agent.payments.grouped');
}); //End Group Agent Middleware




//Agent Group Middleware
Route::middleware(['auth', 'roles:agent'])->group(function () {
     // Agent All Route from admin
        Route::controller(AgentController::class)->group(function () {
        Route::get('/all/agent', 'AllAgent')->name('all.agent');
        Route::get('/add/agent', 'AddAgent')->name('add.agent');
        Route::post('/store/agent', 'StoreAgent')->name('store.agent');
        Route::get('/edit/agent/{id}', 'EditAgent')->name('edit.agent');
        Route::post('/update/agent', 'UpdateAgent')->name('update.agent');
        Route::get('/delete/agent/{id}', 'DeleteAgent')->name('delete.agent');
        Route::get('/changeStatus', 'changeStatus');
    });

// Agent Reports Routes
Route::middleware(['auth', 'roles:agent'])->group(function () {
    Route::get('/agent/reports/office-summary', [AgentReportController::class, 'officeSummary'])->name('agent.office.summary');
    Route::get('/agent/reports/office-detailed', [AgentReportController::class, 'officeDetailed'])->name('agent.office.detailed');
    Route::get('/agent/reports/user-transactions', [AgentReportController::class, 'userTransactions'])->name('agent.user.transactions');
    Route::get('/agent/reports/commission', [AgentReportController::class, 'commissionReport'])->name('agent.commission.report');
   Route::get('agent/users-balance-report', [AgentReportController::class, 'usersBalanceReport'])->name('agent.users.balance.report');



});


    // Agent Buy Package Route from admin
    Route::controller(AgentPropertyController::class)->group(function () {

        Route::get('/buy/package', 'BuyPackage')->name('buy.package');
        Route::get('/buy/business/plan', 'BuyBusinessPlan')->name('buy.business.plan');
        Route::post('/store/business/plan', 'StoreBusinessPlan')->name('store.business.plan');
        Route::get('/buy/professional/plan', 'BuyProfessionalPlan')->name('buy.professional.plan');
        Route::post('/store/professional/plan', 'StoreProfessionalPlan')->name('store.professional.plan');
        Route::get('/package/history', 'PackageHistory')->name('package.history');
        Route::get('/agent/package/invoice/{id}', 'AgentPackageInvoice')->name('agent.package.invoice');
    });

    // Schedule Request Route
    Route::controller(AgentPropertyController::class)->group(function () {

        Route::get('/agent/schedule/request', 'AgentScheduleRequest')->name('agent.schedule.request');
        Route::get('/agent/details/schedule/{id}', 'AgentDetailsSchedule')->name('agent.details.schedule');
        Route::post('/agent/update/schedule/', 'AgentUpdateSchedule')->name('agent.update.schedule');
    });


}); // End Group Admin Middleware


//All user login Route


    // SMTP Setting All Route
    Route::controller(SettingController::class)->group(function () {

        Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
        Route::post('/update/smpt/setting', 'UpdateSmtpSetting')->name('update.smpt.setting');
    });

    // Site Setting All Route
    Route::controller(SettingController::class)->group(function () {

        Route::get('/site/setting', 'SiteSetting')->name('site.setting');
        Route::post('/update/site/setting', 'UpdateSiteSetting')->name('update.site.setting');
    });


//Schedule a tour
Route::post('/store/schedule', [IndexController::class, 'StoreSchedule'])->name('store.schedule');


//User Group Middleware users
Route::middleware(['auth', 'roles:user'])->group(function () {

// تحول الي agent

  //  Route::get('/agent/commission', [CommissionController::class, 'index'])->name('agent.commission.index');

// Route::post('/agent/commission', [CommissionController::class, 'store'])->name('agent.commission.store');

 Route::get('/agent/reports', [AgentDashboardController::class, 'reports'])->name('agent.reports');

    // أضف هذا المسار الجديد
    Route::get('/agent/reports/data', [AgentDashboardController::class, 'getReportsData'])->name('agent.reports.data');



    Route::get('/user/dashboard', [AgentDashboardController::class, 'index'])->name('user.dashboard');

    // Route::get('/user/profile', [AgentController::class, 'AgentProfile'])->name('agent.profile');
    // Route::post('/user/profile/store', [AgentController::class, 'AgentProfileStore'])->name('agent.profile.store');
    // Route::get('/user/change/passowrd', [AgentController::class, 'AgentChangePassword'])->name('agent.change.password');
    // Route::post('/user/update/password', [AgentController::class, 'AgentUpdatePassword'])->name('agent.update.password');

    // Add these routes
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');






    // User Compare Route
    Route::controller(CompareController::class)->group(function () {

        Route::get('/user/compare', 'UserCompare')->name('user.compare');
        Route::get('/get-compare-property', 'GetCompareProperty');
        Route::get('/compare-remove/{id}', 'CompareRemove');
    });

    //Show User schedule request
    Route::get('/user/schedule/request', [UserController::class, 'UserScheduleRequest'])->name('user.schedule.request');
   });

// مجموعة مسارات التحويل
    Route::prefix('transfers')->middleware(['auth', 'roles:user'])->group(function () {
    Route::get('/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('/search-customer', [TransferController::class, 'searchCustomer'])->name('customers.search');
    Route::post('/store-customer', [TransferController::class, 'storeCustomer'])->name('customers.store');
    Route::post('/', [TransferController::class, 'store'])->name('transfers.store');
    Route::get('/', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
    Route::get('/transfers/{id}/print', [TransferController::class, 'print'])->name('transfers.print');
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/agent/received-transfers', [TransferController::class, 'receivedTransfers'])->name('agent.received.transfers');

    Route::get('/agent/sending-transfers', [TransferController::class, 'SenderTransfers'])->name('agent.sending.transfers');
    Route::post('/transfers/update-status', [TransferController::class, 'updateStatusAjax'])->name('transfers.updateStatus.ajax');

   Route::get('/agentuser/cashbox/opening', [CashBoxController::class, 'showOpeningForm'])->name('agentuser.cashbox.opening.form');
   Route::post('/cashbox/opening', [CashBoxController::class, 'storeOpening'])->name('cashbox.opening.store');

   Route::get('/cashbox/refill', [CashBoxController::class, 'showRefillForm'])->name('cashbox.refill.form');
   Route::post('/cashbox/refill', [CashBoxController::class, 'storeRefill'])->name('cashbox.refill.store');

   Route::get('/cashbox/bank', [CashBoxController::class, 'showBankForm'])->name('cashbox.bank.form');
   Route::post('/cashbox/bank', [CashBoxController::class, 'storeBank'])->name('cashbox.bank.store');

Route::get('/cashbox/daily-report', [CashBoxController::class, 'dailyReport'])->name('cashbox.daily.report');


});

// Public Transfer Verification Routes
Route::domain('akec.money')->group(function () {
    // Simple verification URL
    Route::get('/verify', [TransferVerificationController::class, 'showVerificationPage'])->name('transfers.verify');
    Route::get('/verify/qr', [TransferVerificationController::class, 'generateVerificationUrlQr'])->name('transfers.verify.qr');

    // Direct transfer verification with code
    Route::get('/verify/{code}', [TransferVerificationController::class, 'verifyWithCode'])->name('transfers.verify.code');

    // QR code and PDF routes
    Route::get('/transfer/{id}/qr', [TransferVerificationController::class, 'generateQrCode'])->name('transfers.qr-code');
    Route::get('/transfer/{id}/pdf', [TransferVerificationController::class, 'downloadPdf'])->name('transfers.download.pdf');

    // API Routes for Transfer Verification - without api prefix for simplicity
    Route::post('/verify-phone', [TransferVerificationController::class, 'verifyPhone'])->name('api.transfers.verify-phone');
    Route::post('/verify-qr', [TransferVerificationController::class, 'verifyQrCode'])->name('api.transfers.verify-qr');
});

// Vendor Commission Routes
Route::middleware(['auth', 'roles:admin'])->group(function () {
    // Agent Payments Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/agent-payments', [AdminFeeController::class, 'index'])->name('agent.payments.report');
        Route::get('/agent-payments/history', [AdminFeeController::class, 'history'])->name('agent.payments.history');
        Route::post('/transfer-payments', [AdminFeeController::class, 'transferPayments'])->name('transfer.payments');
        Route::post('/process-payments', [AdminFeeController::class, 'processPayments'])->name('process.payments');
        Route::post('/update-payment-status/{adminFee}', [AdminFeeController::class, 'updateStatus'])->name('update.payment.status');
    });
});

// Agent Payments Routes





//????????????????????????????????????????????????؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟؟

// Transaction Management Routes
Route::prefix('admin')->middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transaction.history');
    Route::get('/transactions/{id}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transaction.details');
    Route::get('/transactions/{id}/print', [App\Http\Controllers\Admin\TransactionController::class, 'print'])->name('transaction.print');
    Route::put('/transactions/{id}/status', [App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transaction.update-status');
});

// For admin viewing all agents' transactions
Route::get('/admin/agents/{agent}/transactions', [AgentReportController::class, 'agentTransactions'])->name('admin.agent.transactions');

// For agent viewing their own transactions
Route::get('/agent/transactions', [AgentReportController::class, 'myTransactions'])->name('agent.transactions');




