<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileTravelRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [MobileAuthController::class, 'login']);
Route::post('/logout', [MobileAuthController::class, 'logout']);
Route::get('/me', [MobileAuthController::class, 'me']);

Route::get('/dashboard', [MobileDashboardController::class, 'index']);

Route::get('/projects', [MobileTravelRequestController::class, 'projects']);
Route::get('/cities', [MobileTravelRequestController::class, 'cities']);
Route::get('/travel-requests', [MobileTravelRequestController::class, 'index']);
Route::post('/travel-requests', [MobileTravelRequestController::class, 'store']);
Route::get('/travel-requests/{travelRequest}', [MobileTravelRequestController::class, 'show']);
Route::patch('/travel-requests/{travelRequest}/approve', [MobileTravelRequestController::class, 'approve']);
Route::patch('/travel-requests/{travelRequest}/reject', [MobileTravelRequestController::class, 'reject']);
