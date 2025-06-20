<?php
     use Illuminate\Support\Facades\Route;
     use App\Http\Controllers\Api\GeneralAffairController;

     Route::prefix('ga')->group(function () {
    Route::get('/employees', [GeneralAffairController::class, 'getEmployees']);
    
    // Protected attendance routes with rate limiting
    Route::middleware(['attendance.rate.limit'])->group(function () {
        Route::post('/morning-reflections', [GeneralAffairController::class, 'storeMorningReflection']);
        Route::post('/zoom-join', [GeneralAffairController::class, 'recordZoomJoin']);
    });
    
    // Dashboard routes (read-only, no rate limiting needed)
    Route::get('/morning-reflections', [GeneralAffairController::class, 'getMorningReflections']);
    Route::get('/leaves', [GeneralAffairController::class, 'getLeaves']);
});