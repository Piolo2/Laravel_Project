<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VerificationController;


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
    // Dashboards & Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/service-provider', [\App\Http\Controllers\Provider\ProviderLandingController::class, 'index'])->name('service_provider');
    Route::get('/service-seeker', [\App\Http\Controllers\Seeker\SeekerLandingController::class, 'index'])->name('service_seeker');

    Route::get('/view-profile/{id}', [ProfileController::class, 'viewProfile'])->name('view_profile');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Skills
    Route::get('/services', [SkillController::class, 'index'])->name('services');
    Route::post('/services/add', [SkillController::class, 'store'])->name('services.add');
    Route::post('/services/toggle/{id}/{status}', [SkillController::class, 'toggle'])->name('services.toggle');
    Route::delete('/services/{id}', [SkillController::class, 'destroy'])->name('services.delete');

    // Service Requests
    Route::get('/requests', [ServiceRequestController::class, 'providerIndex'])->name('requests');
    Route::get('/my-requests', [ServiceRequestController::class, 'seekerIndex'])->name('my_requests');
    Route::post('/requests/store', [ServiceRequestController::class, 'store'])->name('requests.store');
    Route::post('/requests/update', [ServiceRequestController::class, 'update'])->name('requests.update');

    // Admin
    Route::get('/admin/api/stats', [\App\Http\Controllers\Admin\AdminLandingController::class, 'getStats'])->name('admin.stats.api');
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminLandingController::class, 'index'])->name('admin.dashboard');

    // Admin Users
    Route::get('/admin/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.users.show');
    Route::put('/admin/users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{id}/verify', [\App\Http\Controllers\Admin\AdminUserController::class, 'approveVerification'])->name('admin.users.verify');
    Route::post('/admin/users/{id}/reject', [\App\Http\Controllers\Admin\AdminUserController::class, 'rejectVerification'])->name('admin.users.reject');

    // Admin Reports
    Route::get('/admin/reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/users', [\App\Http\Controllers\Admin\AdminReportController::class, 'users'])->name('admin.reports.users');
    Route::get('/admin/reports/requests', [\App\Http\Controllers\Admin\AdminReportController::class, 'requests'])->name('admin.reports.requests');
    Route::get('/admin/reports/export/users', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportUsers'])->name('admin.reports.export.users');
    Route::get('/admin/reports/export/requests', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportRequests'])->name('admin.reports.export.requests');

    // Admin Skills
    Route::get('/admin/skills', [\App\Http\Controllers\Admin\AdminSkillController::class, 'index'])->name('admin.skills.index');
    Route::post('/admin/skills', [\App\Http\Controllers\Admin\AdminSkillController::class, 'store'])->name('admin.skills.store');
    Route::put('/admin/skills/{id}', [\App\Http\Controllers\Admin\AdminSkillController::class, 'update'])->name('admin.skills.update');
    Route::delete('/admin/skills/{id}', [\App\Http\Controllers\Admin\AdminSkillController::class, 'destroy'])->name('admin.skills.destroy');
    Route::post('/admin/categories', [\App\Http\Controllers\Admin\AdminSkillController::class, 'storeCategory'])->name('admin.categories.store');

    // Admin Announcements
    Route::resource('/admin/announcements', \App\Http\Controllers\Admin\AdminAnnouncementController::class)->names([
        'index' => 'admin.announcements.index',
        'create' => 'admin.announcements.create',
        'store' => 'admin.announcements.store',
        'edit' => 'admin.announcements.edit',
        'update' => 'admin.announcements.update',
        'destroy' => 'admin.announcements.destroy',
    ]);
    // Service Provider Verification
    Route::get('/verification', [VerificationController::class, 'show'])->name('verification.show');
    Route::post('/verification', [VerificationController::class, 'store'])->name('verification.store');

    // Accomplishments
    Route::post('/accomplishments', [\App\Http\Controllers\AccomplishmentController::class, 'store'])->name('accomplishments.store');
    Route::delete('/accomplishments/{id}', [\App\Http\Controllers\AccomplishmentController::class, 'destroy'])->name('accomplishments.destroy');

    // Reviews
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});


