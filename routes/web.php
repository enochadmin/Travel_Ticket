<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReceptionTicketController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceptionBookingController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsSessionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('users/import', [UserController::class, 'import'])->name('users.import')->middleware('role:admin');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export')->middleware('role:admin');
    Route::get('users/template', [UserController::class, 'downloadTemplate'])->name('users.template')->middleware('role:admin');
    Route::resource('users', UserController::class)->middleware('role:admin');

    Route::get('user-registrations', [UserRegistrationController::class, 'index'])
        ->name('user-registrations.index')
        ->middleware('role:admin');
    Route::post('user-registrations/{userRegistration}/approve', [UserRegistrationController::class, 'approve'])
        ->name('user-registrations.approve')
        ->middleware('role:admin');
    Route::post('user-registrations/{userRegistration}/approve-with-project', [UserRegistrationController::class, 'approveWithProject'])
        ->name('user-registrations.approve-with-project')
        ->middleware('role:admin');
    Route::post('user-registrations/{userRegistration}/reject', [UserRegistrationController::class, 'reject'])
        ->name('user-registrations.reject')
        ->middleware('role:admin');

    // Project resource routes (except show)
    Route::post('projects/import', [ProjectController::class, 'import'])->name('projects.import')->middleware('role:admin|head-office-director|commercial-director|ceo');
    Route::get('projects/export', [ProjectController::class, 'export'])->name('projects.export')->middleware('role:admin|head-office-director|commercial-director|ceo');
    Route::get('projects/template', [ProjectController::class, 'downloadTemplate'])->name('projects.template')->middleware('role:admin|head-office-director|commercial-director|ceo');
    Route::resource('projects', ProjectController::class)->except(['show'])->middleware('role:admin|head-office-director|commercial-director|ceo');

    // Project show route (accessible by PMs for their own project)
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('projects/{project}/members', [ProjectController::class, 'addMembers'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.destroy');

    Route::resource('travel-requests', TravelRequestController::class);
    Route::patch('travel-requests/{travel_request}/approve', [TravelRequestController::class, 'approve'])->name('travel-requests.approve');
    Route::patch('travel-requests/{travel_request}/reject', [TravelRequestController::class, 'reject'])->name('travel-requests.reject');

    Route::get('reception/dashboard', [ReceptionTicketController::class, 'dashboard'])
        ->name('reception.dashboard')
        ->middleware('role:reception');
    Route::get('reception/tickets', [ReceptionTicketController::class, 'index'])
        ->name('reception.tickets.index')
        ->middleware('role:reception');
    Route::get('reception/tickets/archived', [ReceptionTicketController::class, 'archived'])
        ->name('reception.tickets.archived')
        ->middleware('role:reception');
    Route::post('reception/tickets/process', [ReceptionTicketController::class, 'process'])
        ->name('reception.tickets.process')
        ->middleware('role:reception');
    Route::post('reception/tickets/process-and-book', [ReceptionTicketController::class, 'processAndBook'])
        ->name('reception.tickets.process_and_book')
        ->middleware('role:reception');
    Route::get('reception/bookings/create', [ReceptionBookingController::class, 'create'])
        ->name('reception.bookings.create')
        ->middleware('role:reception');
    Route::post('reception/bookings', [ReceptionBookingController::class, 'store'])
        ->name('reception.bookings.store')
        ->middleware('role:reception');
    Route::get('reception/tickets/export', [ReceptionTicketController::class, 'export'])
        ->name('reception.tickets.export')
        ->middleware('role:reception');
    Route::get('reception/tickets/{ticket}', [ReceptionTicketController::class, 'show'])
        ->name('reception.tickets.show')
        ->middleware('role:reception');
    Route::get('reception/destinations/{destination}', [ReceptionTicketController::class, 'destination'])
        ->name('reception.destinations.show')
        ->middleware('role:reception');

    Route::prefix('reports')->name('reports.')->middleware('role:admin|head-office-director|commercial-director|ceo|project-manager')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/most-traveled-cities', [ReportController::class, 'mostTraveledCities'])->name('most-traveled-cities')->middleware('role:admin|commercial-director|project-manager');
        Route::get('/most-requested-projects', [ReportController::class, 'mostRequestedProjects'])->name('most-requested-projects')->middleware('role:admin|commercial-director|head-office-director|ceo');
        Route::get('/travel-trend-analysis', [ReportController::class, 'travelTrendAnalysis'])->name('travel-trend-analysis')->middleware('role:admin|commercial-director');
        Route::get('/frequent-travelers', [ReportController::class, 'frequentTravelers'])->name('frequent-travelers')->middleware('role:admin|commercial-director');
        Route::get('/export/travel-requests', [ReportController::class, 'exportTravelRequests'])->name('export.travel-requests');
        Route::get('/export/most-traveled-cities', [ReportController::class, 'exportMostTraveledCities'])->name('export.most-traveled-cities')->middleware('role:admin|commercial-director|project-manager');
        Route::get('/export/most-requested-projects', [ReportController::class, 'exportMostRequestedProjects'])->name('export.most-requested-projects')->middleware('role:admin|commercial-director|head-office-director|ceo');
        Route::get('/export/travel-trend-analysis', [ReportController::class, 'exportTravelTrendAnalysis'])->name('export.travel-trend-analysis')->middleware('role:admin|commercial-director');
        Route::get('/export/frequent-travelers', [ReportController::class, 'exportFrequentTravelers'])->name('export.frequent-travelers')->middleware('role:admin|commercial-director');
    });

    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', '/settings/cities')->name('index');
        Route::resource('cities', CityController::class)->except(['show']);
        
        Route::resource('roles', RoleController::class);
        
        Route::get('sessions', [SettingsSessionController::class, 'show'])->name('session.show');
        Route::delete('sessions/bulk', [SettingsSessionController::class, 'bulkDestroy'])->name('session.bulk-destroy');
        Route::delete('sessions/{sessionId}', [SettingsSessionController::class, 'destroy'])->name('session.destroy');
    });

    // Notification routes
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

require __DIR__ . '/auth.php';
