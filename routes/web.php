<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VerificationController;



if (!defined('ID_PARAM')) {
    define('ID_PARAM', '/{id}');
}

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/announcement/{id}', [HomeController::class, 'showAnnouncement'])->name('announcement.show');
Route::get('/api/markers', [SearchController::class, 'getMarkers'])->name('api.markers');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | User Dashboards & Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/view-profile/{id}', [ProfileController::class, 'viewProfile'])->name('view_profile');

    // Role-based Landing Pages
    Route::get('/service-provider', [\App\Http\Controllers\Provider\ProviderLandingController::class, 'index'])->name('service_provider');
    Route::get('/service-seeker', [\App\Http\Controllers\Seeker\SeekerLandingController::class, 'index'])->name('service_seeker');

    /*
    |--------------------------------------------------------------------------
    | Services Features (Search, Skills, Requests)
    |--------------------------------------------------------------------------
    */
    // search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // skills CRUD
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [SkillController::class, 'index'])->name('index'); // services
        Route::post('/add', [SkillController::class, 'store'])->name('add');
        Route::post('/toggle/{id}/{status}', [SkillController::class, 'toggle'])->name('toggle');
        Route::delete(ID_PARAM, [SkillController::class, 'destroy'])->name('delete');
    });

    // Requests
    Route::prefix('requests')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'providerIndex'])->name('requests');
        Route::get('/my-requests', [ServiceRequestController::class, 'seekerIndex'])->name('my_requests'); // path for seekers
        Route::post('/store', [ServiceRequestController::class, 'store'])->name('requests.store');
        Route::post('/update', [ServiceRequestController::class, 'update'])->name('requests.update');
    });
    // this needs seperate route for bulk delete
    Route::post('/service-requests/bulk-delete', [ServiceRequestController::class, 'bulkDelete'])->name('requests.bulk_delete');

    // Verification & Reviews
    Route::get('/verification', [VerificationController::class, 'show'])->name('verification.show');
    Route::post('/verification', [VerificationController::class, 'store'])->name('verification.store');

    Route::post('/accomplishments', [\App\Http\Controllers\AccomplishmentController::class, 'store'])->name('accomplishments.store');
    Route::delete('/accomplishments/{id}', [\App\Http\Controllers\AccomplishmentController::class, 'destroy'])->name('accomplishments.destroy');

    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminLandingController::class, 'index'])->name('dashboard');
        Route::get('/api/stats', [\App\Http\Controllers\Admin\AdminLandingController::class, 'getStats'])->name('stats.api');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('index');
            Route::get(ID_PARAM, [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('show');
            Route::put(ID_PARAM, [\App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('update');
            Route::delete(ID_PARAM, [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('destroy');
            Route::post(ID_PARAM . '/verify', [\App\Http\Controllers\Admin\AdminUserController::class, 'approveVerification'])->name('verify');
            Route::post(ID_PARAM . '/reject', [\App\Http\Controllers\Admin\AdminUserController::class, 'rejectVerification'])->name('reject');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\AdminReportController::class, 'users'])->name('users');
            Route::get('/requests', [\App\Http\Controllers\Admin\AdminReportController::class, 'requests'])->name('requests');
            Route::get('/export/users', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportUsers'])->name('export.users');
            Route::get('/export/requests', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportRequests'])->name('export.requests');
        });

        // Skills & Categories
        Route::get('/skills', [\App\Http\Controllers\Admin\AdminSkillController::class, 'index'])->name('skills.index');
        Route::post('/skills', [\App\Http\Controllers\Admin\AdminSkillController::class, 'store'])->name('skills.store');
        Route::put('/skills/{id}', [\App\Http\Controllers\Admin\AdminSkillController::class, 'update'])->name('skills.update');
        Route::delete('/skills/{id}', [\App\Http\Controllers\Admin\AdminSkillController::class, 'destroy'])->name('skills.destroy');
        Route::post('/categories', [\App\Http\Controllers\Admin\AdminSkillController::class, 'storeCategory'])->name('categories.store');

        // Announcements (Resource Route)
        Route::resource('announcements', \App\Http\Controllers\Admin\AdminAnnouncementController::class);
    });
});





