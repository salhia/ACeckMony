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
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Models\SmtpSetting;



//user Frontend All Route data
Route::get('/', [UserController::class, 'index']);

Route::get('/clear', function () {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');

    return view('frontend.index'); // أو أي View تريده
});

Route::post('/github-deploy', function () {
    Log::info('Webhook triggered');

    // تنفيذ git pull إن أمكن
    $output = [];
    $status = null;
    exec('cd /home/akec.money/public_html && git pull 2>&1', $output, $status);

    Log::info('Git Pull Output:', $output);
    Log::info('Git Pull Status: ' . $status);

    return response('Git pull triggered.', 200);
});

Route::get('/dashboard', function () {
    return view ('dashboard');
})->middleware(['auth', 'roles:user', 'verified'])->name('dashboard');


//User Management Group Middleware
Route::middleware(['auth', 'roles:user'])->group(function () {

    Route::get('/user/profile', [UserController::class, 'UserProfile'])->name('user.profile');
    Route::post('/user/profile/store', [UserController::class, 'UserProfileStore'])->name('user.profile.store');
    Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
    Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');
    Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.pasword.update');
});

require __DIR__ . '/auth.php';

//Admin Management Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
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
}); //End Group Admin Middleware


//Agent Management Group Middleware
Route::middleware(['auth', 'roles:agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'AgentDashboard'])->name('agent.dashboard');
    Route::get('/agent/logout', [AgentController::class, 'AgentLogout'])->name('agent.logout');
    Route::get('/agent/profile', [AgentController::class, 'AgentProfile'])->name('agent.profile');
    Route::post('/agent/profile/store', [AgentController::class, 'AgentProfileStore'])->name('agent.profile.store');
    Route::get('/agent/change/passowrd', [AgentController::class, 'AgentChangePassword'])->name('agent.change.password');
    Route::post('/agent/update/password', [AgentController::class, 'AgentUpdatePassword'])->name('agent.update.password');
}); //End Group Agent Middleware


//User Group Middleware
Route::middleware(['auth', 'roles:user'])->group(function () {

    // User WishlistAll Route
    Route::controller(WishlistController::class)->group(function () {

        Route::get('/user/wishlist', 'UserWishlist')->name('user.wishlist');
        Route::get('/get-wishlist-property', 'GetWishlistProperty');
        Route::get('/wishlist-remove/{id}', 'WishlistRemove');
    });

    // User Compare Route
    Route::controller(CompareController::class)->group(function () {

        Route::get('/user/compare', 'UserCompare')->name('user.compare');
        Route::get('/get-compare-property', 'GetCompareProperty');
        Route::get('/compare-remove/{id}', 'CompareRemove');
    });

    //Show User schedule request
    Route::get('/user/schedule/request', [UserController::class, 'UserScheduleRequest'])->name('user.schedule.request');
});


//Admin Group Middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {

    //Property type All route
    Route::controller(PropertyTypeController::class)->group(function () {
        Route::get('/all/type', 'AllType')->name('all.type')->middleware('permission:all.type');
        Route::get('/add/type', 'AddType')->name('add.type')->middleware('permission:add.type');
        Route::post('/store/type', 'StoreType')->name('store.type');
        Route::get('/edit/type/{id}', 'EditType')->name('edit.type');
        Route::post('/update/type/', 'UpdateType')->name('update.type');
        Route::get('/delete/type/{id}', 'DeleteType')->name('delete.type');
    });

    //Amenities All route
    Route::controller(PropertyTypeController::class)->group(function () {
        Route::get('/all/amenitie', 'AllAmenitie')->name('all.amenitie');
        Route::get('/add/amenitie', 'AddAmenitie')->name('add.amenitie');
        Route::post('/store/amenitie', 'StoreAmenitie')->name('store.amenitie');
        Route::get('/edit/amenitie/{id}', 'EditAmenitie')->name('edit.amenitie');
        Route::post('/update/amenitie/', 'UpdateAmenitie')->name('update.amenitie');
        Route::get('/delete/amenitie/{id}', 'DeleteAmenitie')->name('delete.amenitie');
        Route::get('/admin/package/history', 'AdminPackageHistory')->name('admin.package.history');
        Route::get('/package/invoice/{id}', 'PackageInvoice')->name('package.invoice');
    });

    //Properties All route
    Route::controller(PropertyController::class)->group(function () {
        Route::get('admin/all/property', 'AdminAllProperty')->name('admin.all.property');
        Route::get('/add/property', 'AddProperty')->name('add.property');
        Route::post('/store/property', 'StoreProperty')->name('store.property');
        Route::get('/edit/property/{id}', 'EditProperty')->name('edit.property');
        Route::post('/update/property', 'UpdateProperty')->name('update.property');
        Route::post('/update/property/thambnail', 'UpdatePropertyThambnail')->name('update.property.thambnail');
        Route::post('/update/property/multiimage', 'UpdatePropertyMultiimage')->name('update.property.multiimage');
        Route::get('/property/multiimg/delete/{id}', 'PropertyMultiImageDelete')->name('property.multiimg.delete');
        Route::post('/store/new/multiimage', 'StoreNewMultiimage')->name('store.new.multiimage');
        Route::post('/update/property/facilities', 'UpdatePropertyFacilities')->name('update.property.facilities');
        Route::get('/delete/property/{id}', 'DeleteProperty')->name('delete.property');
        Route::get('/details/property/{id}', 'DetailsProperty')->name('details.property');
        Route::post('/details/property/inactive', 'InactiveProperty')->name('inactive.property');
        Route::post('/details/property/active', 'ActiveProperty')->name('active.property');
        Route::get('/changePropertyStatus', 'changePropertyStatus');
        Route::get('/admin/property/message', 'AdminPropertyMessage')->name('admin.property.message');
        Route::get('/admin/message/details/{id}', 'AdminMessageDetails')->name('admin.message.details');
    });

    // Agent All Route from admin
    Route::controller(AdminController::class)->group(function () {

        Route::get('/all/agent', 'AllAgent')->name('all.agent');
        Route::get('/add/agent', 'AddAgent')->name('add.agent');
        Route::post('/store/agent', 'StoreAgent')->name('store.agent');
        Route::get('/edit/agent/{id}', 'EditAgent')->name('edit.agent');
        Route::post('/update/agent', 'UpdateAgent')->name('update.agent');
        Route::get('/delete/agent/{id}', 'DeleteAgent')->name('delete.agent');
        Route::get('/changeStatus', 'changeStatus');
    });

    // State  All Route
    Route::controller(StateController::class)->group(function () {

        Route::get('/all/state', 'AllState')->name('all.state');
        Route::get('/add/state', 'Addstate')->name('add.state');
        Route::post('/store/state', 'StoreState')->name('store.state');
        Route::get('/edit/state/{id}', 'EditState')->name('edit.state');
        Route::post('/update/state', 'UpdateState')->name('update.state');
        Route::get('/delete/state/{id}', 'DeleteState')->name('delete.state');
    });

    // Testimonials  All Route
    Route::controller(TestimonialController::class)->group(function () {

        Route::get('/all/testimonials', 'AllTestimonials')->name('all.testimonials');
        Route::get('/add/testimonials', 'AddTestimonials')->name('add.testimonials');
        Route::post('/store/testimonials', 'StoreTestimonials')->name('store.testimonials');
        Route::get('/edit/testimonials/{id}', 'EditTestimonials')->name('edit.testimonials');
        Route::post('/update/testimonials', 'UpdateTestimonials')->name('update.testimonials');
        Route::get('/delete/testimonials/{id}', 'DeleteTestimonials')->name('delete.testimonials');
    });

    //Blog Post All Route
    Route::controller(BlogController::class)->group(function () {

        Route::get('/all/post', 'AllPost')->name('all.post');
        Route::get('/add/post', 'AddPost')->name('add.post');
        Route::post('/store/post', 'StorePost')->name('store.post');
        Route::get('/edit/post/{id}', 'EditPost')->name('edit.post');
        Route::post('/update/post', 'UpdatePost')->name('update.post');
        Route::get('/delete/post/{id}', 'DeletePost')->name('delete.post');
    });
}); // End Group Admin Middleware

// Blog Category Routes for Admin
Route::prefix('admin')->middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/all/blog/category', [BlogController::class, 'AllBlogCategory'])->name('all.blog.category');
    Route::post('/store/blog/category', [BlogController::class, 'StoreBlogCategory'])->name('store.blog.category');
    Route::get('/blog/category/{id}', [BlogController::class, 'EditBlogCategory'])->name('edit.blog.category');
    Route::post('/update/blog/category', [BlogController::class, 'UpdateBlogCategory'])->name('update.blog.category');
    Route::get('/delete/blog/category/{id}', [BlogController::class, 'DeleteBlogCategory'])->name('delete.blog.category');
});



//Agent Group Middleware
Route::middleware(['auth', 'roles:agent'])->group(function () {

    // Agent All Property
    Route::controller(AgentPropertyController::class)->group(function () {

        Route::get('/agent/all/property', 'AgentAllProperty')->name('agent.all.property');
        Route::get('/agent/add/property', 'AgentAddProperty')->name('agent.add.property');
        Route::post('/agent/store/property', 'AgentStoreProperty')->name('agent.store.property');

        Route::get('/agent/edit/property/{id}', 'AgentEditProperty')->name('agent.edit.property');
        Route::post('/agent/update/property', 'AgentUpdateProperty')->name('agent.update.property');
        Route::get('/agent/delete/property/{id}', 'AgentDeleteProperty')->name('agent.delete.property');

        Route::post('/agent/update/property/thambnail', 'AgentUpdatePropertyThambnail')->name('agent.update.property.thambnail');
        Route::post('/agent/update/property/multiimage', 'AgentUpdatePropertyMultiimage')->name('agent.update.property.multiimage');
        Route::get('/agent/update/property/multiimg/delete/{id}', 'AgentPropertyMultiimgDelete')->name('agent.property.multiimg.delete');
        Route::post('/agent/store/new/multiimage', 'AgentStoreNewMultiimage')->name('agent.store.new.multiimage');

        Route::post('/agent/update/property/facilities', 'AgentUpdatePropertyFacilities')->name('agent.update.property.facilities');

        Route::get('/agent/details/property/{id}', 'AgentDetailsProperty')->name('agent.details.property');
        Route::get('/agent/property/message', 'AgentPropertyMessage')->name('agent.property.message');
        Route::get('/agent/message/details/{id}', 'AgentMessageDetails')->name('agent.message.details');
        Route::get('/agent/profile/message', 'AgentProfileMessage')->name('agent.profile.message');
        Route::get('/agent/profile/message/details/{id}', 'AgentProfileMessageDetails')->name('agent.profile.message.details');
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
Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login')->middleware(RedirectIfAuthenticated::class);;
Route::get('/agent/login', [AgentController::class, 'AgentLogin'])->name('agent.login')->middleware(RedirectIfAuthenticated::class);;
Route::post('/agent/register', [AgentController::class, 'AgentRegister'])->name('agent.register');


//Frontend Property Details All Route
Route::get('/property/details/{slug}', [IndexController::class, 'PropertyDetails']);

//Frontend Wishlist Add Route
Route::post('/add-to-wishList/{property_id}', [WishlistController::class, 'AddToWishList']);

// User Property Compare Route
Route::post('/add-to-compare/{property_id}', [CompareController::class, 'AddToCompare']);


//Send Message from Property Details Page to Admin & Agent Backend
Route::post('/property/message', [IndexController::class, 'PropertyMessage'])->name('property.message');

// Agent Details Page in Frontend
Route::get('/agent/details/{id}', [IndexController::class, 'AgentDetails'])->name('agent.details');

// Send Message from Agent Details Page to Agent Backend
Route::post('/agent/details/message', [IndexController::class, 'AgentDetailsMessage'])->name('agent.details.message');

//Get all Rent property
Route::get('/rent/property', [IndexController::class, 'RentProperty'])->name('rent.property');

//Get all Buy property
//Route::get('/buy/property', [IndexController::class, 'BuyProperty'])->name('buy.property');

//Get Property type details
Route::get('/property/type/{id}', [IndexController::class, 'PropertyType'])->name('property.type');

//Get all Buy property
Route::get('/all/property', [IndexController::class, 'AllProperty'])->name('all.property');

//Get all Category
Route::get('/all/category', [IndexController::class, 'AllCategory'])->name('all.category');

// Get State Details Data
Route::get('/state/details/{id}', [IndexController::class, 'StateDetails'])->name('state.details');

// Home Page Buy Search Option
Route::post('/buy/property/search', [IndexController::class, 'BuyPropertySearch'])->name('buy.property.search');

// Home Page Rent Search Option
Route::post('/rent/property/search', [IndexController::class, 'RentPropertySearch'])->name('rent.property.search');

//All Property Search Option
Route::post('/all/property/search', [IndexController::class, 'AllPropertySearch'])->name('all.property.search');

// Blog Details Page
Route::get('/blog/details/{slug}', [BlogController::class, 'BlogDetails']);
// Blog Category Details Page
Route::get('blog/category/{id}', [BlogController::class, 'BlogCategoryList']);
// Blog Header
Route::get('/blog', [BlogController::class, 'BlogList'])->name('blog.list');

//Blog Comment
Route::post('/store/comment', [BlogController::class, 'StoreComment'])->name('store.comment');

//Blog Reply Route,SMTP Setting,Site Setting  All Route
Route::middleware(['auth', 'roles:admin'])->group(function () {

    // Display All Comment on Admdin Dashboard
    Route::get('/admin/blog/comment', [BlogController::class, 'AdminBlogComment'])->name('admin.blog.comment');
    // Reply On Comment in Admdin Dashboard
    Route::get('/admin/comment/reply/{id}', [BlogController::class, 'AdminCommentReply'])->name('admin.comment.reply');
    // Reply On Comment in Admdin Dashboard
    Route::post('/reply/message', [BlogController::class, 'ReplyMessage'])->name('reply.message');
    //delete comment from admin
    Route::get('/admin/delete/comment/{id}', [BlogController::class, 'DeleteComment'])->name('admin.delete.comment');


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
});

//Schedule a tour
Route::post('/store/schedule', [IndexController::class, 'StoreSchedule'])->name('store.schedule');
