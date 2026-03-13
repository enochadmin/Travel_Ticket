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

use App\Http\Controllers\UserController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('users/import', [UserController::class, 'import'])->name('users.import')->middleware('role:admin');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export')->middleware('role:admin');
    Route::get('users/template', [UserController::class, 'downloadTemplate'])->name('users.template')->middleware('role:admin');
    Route::resource('users', UserController::class)->middleware('role:admin');

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
    Route::get('reception/tickets/export', [ReceptionTicketController::class, 'export'])
        ->name('reception.tickets.export')
        ->middleware('role:reception');
    Route::get('reception/tickets/{ticket}', [ReceptionTicketController::class, 'show'])
        ->name('reception.tickets.show')
        ->middleware('role:reception');
    Route::get('reception/destinations/{destination}', [ReceptionTicketController::class, 'destination'])
        ->name('reception.destinations.show')
        ->middleware('role:reception');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('role:admin|head-office-director|commercial-director|ceo');

    // Notification routes
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

require __DIR__ . '/auth.php';
