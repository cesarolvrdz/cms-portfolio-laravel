<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProjectAdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SocialLinksController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\CertificatesController;
use App\Http\Controllers\Admin\CvController;
use App\Http\Controllers\Auth\LoginController;

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirigir la raíz al panel admin
Route::get('/', function () {
    return redirect('/admin');
});

// API pública para disponibilidad
include 'api-availability.php';// Panel de administración (protegido por autenticación)
Route::prefix('admin')->name('admin.')->middleware('auth.admin')->group(function () {
    // Dashboard principal
    Route::get('/', function () {
        return redirect()->route('admin.projects.index');
    });

    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gestión de proyectos
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectAdminController::class, 'index'])->name('index');
        Route::get('/create', [ProjectAdminController::class, 'create'])->name('create');
        Route::post('/', [ProjectAdminController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProjectAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProjectAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProjectAdminController::class, 'destroy'])->name('destroy');
    });

    // Gestión de formación académica
    Route::prefix('education')->name('education.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\EducationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\EducationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\EducationController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\EducationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\EducationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\EducationController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\EducationController::class, 'destroy'])->name('destroy');
    });

    // Gestión de experiencia laboral
    Route::prefix('experience')->name('experience.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ExperienceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ExperienceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ExperienceController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\ExperienceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\ExperienceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\ExperienceController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ExperienceController::class, 'destroy'])->name('destroy');
    });

    // Gestión de usuarios
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UsersController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\UsersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\UsersController::class, 'update'])->name('update');
        Route::patch('/{id}/activate', [App\Http\Controllers\Admin\UsersController::class, 'activate'])->name('activate');
        Route::patch('/{id}/deactivate', [App\Http\Controllers\Admin\UsersController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{id}', [App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('destroy');
    });

    // Gestión de disponibilidad
    Route::prefix('availability')->name('availability.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AvailabilityController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Admin\AvailabilityController::class, 'edit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\Admin\AvailabilityController::class, 'update'])->name('update');
        Route::post('/quick-update', [App\Http\Controllers\Admin\AvailabilityController::class, 'quickUpdate'])->name('quick-update');
    });

    // Gestión de certificados
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificatesController::class, 'index'])->name('index');
        Route::get('/create', [CertificatesController::class, 'create'])->name('create');
        Route::post('/', [CertificatesController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CertificatesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CertificatesController::class, 'update'])->name('update');
        Route::delete('/{id}', [CertificatesController::class, 'destroy'])->name('destroy');
        // AJAX routes
        Route::post('/{id}/toggle-featured', [CertificatesController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{id}/toggle-active', [CertificatesController::class, 'toggleActive'])->name('toggle-active');
    });

    // Gestión de CV
    Route::prefix('cv')->name('cv.')->group(function () {
        Route::get('/', [CvController::class, 'index'])->name('index');
        Route::get('/create', [CvController::class, 'create'])->name('create');
        Route::post('/', [CvController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CvController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CvController::class, 'update'])->name('update');
        Route::delete('/{id}', [CvController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [CvController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [CvController::class, 'download'])->name('download');
        // AJAX routes
        Route::post('/{id}/set-current', [CvController::class, 'setCurrent'])->name('set-current');
    });

    // Gestión de perfil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });

    // Gestión de enlaces sociales
    Route::prefix('social')->name('social.')->group(function () {
        Route::get('/', [SocialLinksController::class, 'index'])->name('index');
        Route::get('/create', [SocialLinksController::class, 'create'])->name('create');
        Route::post('/', [SocialLinksController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SocialLinksController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SocialLinksController::class, 'update'])->name('update');
        Route::delete('/{id}', [SocialLinksController::class, 'destroy'])->name('destroy');
    });

    // Gestión de configuraciones del sitio
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SiteSettingsController::class, 'index'])->name('index');
        Route::get('/{group?}', [SiteSettingsController::class, 'show'])->name('show');
        Route::put('/{key}', [SiteSettingsController::class, 'update'])->name('update');
        Route::post('/', [SiteSettingsController::class, 'store'])->name('store');
        Route::delete('/{id}', [SiteSettingsController::class, 'destroy'])->name('destroy');
    });
});

// API routes
require __DIR__.'/api-certificates.php';
require __DIR__.'/api-cv.php';
