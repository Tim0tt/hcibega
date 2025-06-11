<?php
     use Illuminate\Support\Facades\Route;
     use App\Http\Controllers\Api\GeneralAffairController;

     Route::prefix('ga')->group(function () {
         Route::get('/employees', [GeneralAffairController::class, 'getEmployees']);
         Route::post('/morning-reflections', [GeneralAffairController::class, 'storeMorningReflection']);
         Route::post('/zoom-join', [GeneralAffairController::class, 'recordZoomJoin']);
         Route::get('/morning-reflections', [GeneralAffairController::class, 'getMorningReflections']);
         Route::get('/leaves', [GeneralAffairController::class, 'getLeaves']);
     });