<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ActivationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth routes
    Route::get('/google/url', [GoogleAuthController::class, 'getAuthUrl']);
    Route::get('/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
    Route::post('/google/one-tap', [GoogleAuthController::class, 'handleGoogleOneTap']);
});

// Protected Destination routes (must be defined before public slug route)
Route::middleware('auth:sanctum')->group(function () {
    // Destinations
    Route::get('/destinations/my-posts', [DestinationController::class, 'myPosts']);
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{destination}', [DestinationController::class, 'update']);
    Route::delete('/destinations/{destination}', [DestinationController::class, 'destroy']);
});

// Public read-only routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{destination}', [DestinationController::class, 'show']);
Route::get('/destinations/nearby', [DestinationController::class, 'nearby']);
Route::get('/destinations/map/all', [DestinationController::class, 'map']);

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/events/featured/list', [EventController::class, 'featured']);

Route::get('/profile/{user}', [ProfileController::class, 'show']);
Route::get('/users', [UserController::class, 'index']);

// Activations
Route::get('/activations', [ActivationController::class, 'index']);
Route::get('/activations/{slug}', [ActivationController::class, 'show']);

// Pages
Route::get('/pages/footer', [PageController::class, 'footer']);
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Settings
Route::get('/settings/footer', [SettingController::class, 'footer']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Profile
    Route::put('/profile', [ProfileController::class, 'update']);

    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::post('/events/{event}/join', [EventController::class, 'join']);
    Route::post('/events/{event}/leave', [EventController::class, 'leave']);

    // Comments
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Likes
    Route::post('/likes/toggle', [LikeController::class, 'toggle']);
    Route::get('/likes/check', [LikeController::class, 'check']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    // Users (Admin only)
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::post('/users/{user}/ban', [UserController::class, 'ban']);
    Route::post('/users/{user}/unban', [UserController::class, 'unban']);

    // Categories (Admin only)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{category}', [CategoryController::class, 'update']); // Using POST because of file upload
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Activations (Admin only)
    Route::post('/activations', [ActivationController::class, 'store']);
    Route::post('/activations/{id}', [ActivationController::class, 'update']); // Using POST because of file upload
    Route::delete('/activations/{id}', [ActivationController::class, 'destroy']);
    
    // Activation Media
    Route::post('/activations/{id}/media', [ActivationController::class, 'uploadMedia']);
    Route::delete('/activations/{id}/media/{mediaId}', [ActivationController::class, 'deleteMedia']);
    
    // Activation FAQs
    Route::post('/activations/{id}/faqs', [ActivationController::class, 'storeFaq']);
    Route::put('/activations/{id}/faqs/{faqId}', [ActivationController::class, 'updateFaq']);
    Route::delete('/activations/{id}/faqs/{faqId}', [ActivationController::class, 'deleteFaq']);
    
    // Activation Testimonials
    Route::post('/activations/{id}/testimonials', [ActivationController::class, 'storeTestimonial']);
    Route::post('/activations/{id}/testimonials/{testimonialId}', [ActivationController::class, 'updateTestimonial']); // Using POST because of file upload
    Route::delete('/activations/{id}/testimonials/{testimonialId}', [ActivationController::class, 'deleteTestimonial']);
    
    // Pages (Admin only)
    Route::get('/admin/pages', [PageController::class, 'index']);
    Route::post('/admin/pages', [PageController::class, 'store']);
    Route::put('/admin/pages/{id}', [PageController::class, 'update']);
    Route::delete('/admin/pages/{id}', [PageController::class, 'destroy']);
    
    // Settings (Admin only)
    Route::get('/admin/settings', [SettingController::class, 'index']);
    Route::put('/admin/settings', [SettingController::class, 'update']);
});
