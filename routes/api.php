<?php

use App\Http\Controllers\admin\ProjectController as AdminProjectController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ToolsController;
use App\Http\Requests\KomentarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("/v1")->group(function () {
    Route::prefix("/auth")->group(function () {
        Route::post("/login", [AuthController::class, "Login"]);
        Route::post("/register", [AuthController::class, "register"]);
        Route::post("/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::get("/user", [AuthController::class, "User"]);
        Route::resource('/tool', ToolsController::class);
        Route::resource("/project", ProjectController::class);
        Route::post("/project/komentar/{id}", [KomentarController::class, "Store"]);
        Route::delete("/project/komentar/{id}/{komentar:id}", [KomentarController::class, "destroy"]);
        Route::put("/project/{id}", [ProjectController::class, "Update"]);
        Route::post("/user", [AuthController::class, "editProfile"]);

        Route::prefix("/admin")->group(function (){
            Route::resource("project", AdminProjectController::class);
            Route::resource("user", UserController::class);
        });
    });
});