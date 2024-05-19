<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group(['prefix' => 'auth'], function () {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);

  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
  });
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function () {
  Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
  Route::apiResource('achievements', App\Http\Controllers\Api\AchievementController::class);
  Route::apiResource('bodies', App\Http\Controllers\Api\BodyController::class);
  Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
  Route::apiResource('diaries', App\Http\Controllers\Api\DiaryController::class);
  Route::apiResource('exercises', App\Http\Controllers\Api\ExerciseController::class);
  Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);
  Route::apiResource('meals', App\Http\Controllers\Api\MealController::class);
  Route::apiResource('weights', App\Http\Controllers\Api\WeightController::class);

  Route::get("users/achievements/list", [App\Http\Controllers\Api\UserController::class, 'achievementsList'])->name('user_achievements_list');
  Route::post("users/achievements/{achievement_id}", [App\Http\Controllers\Api\UserController::class, 'achievement'])->name('user_achievements_add');

  Route::get("users/weights/list", [App\Http\Controllers\Api\UserController::class, 'weightsList'])->name('user_weights_list');
  Route::get("users/bodies/list", [App\Http\Controllers\Api\UserController::class, 'BodiesList'])->name('user_bodies_list');
  Route::get("users/exercises/list", [App\Http\Controllers\Api\UserController::class, 'exercisesList'])->name('user_exercises_list');
  Route::post("users/exercises", [App\Http\Controllers\Api\UserController::class, 'exercises'])->name('user_exercises_add');

  Route::post("users/categories/add", [App\Http\Controllers\Api\UserController::class, 'categoryAdd'])->name('user_categories_add');
  Route::get("users/categories/list", [App\Http\Controllers\Api\UserController::class, 'categoriesList'])->name('user_categories_list');
  Route::delete("users/categories/{id}", [App\Http\Controllers\Api\UserController::class, 'categoryRemove'])->name('user_categories_delete');

  Route::get("users/posts/list", [App\Http\Controllers\Api\UserController::class, "postsList"])->name("user_posts_list");

  Route::get("users/diaries/list", [App\Http\Controllers\Api\UserController::class, "diariesList"])->name("user_diaries_list");

  Route::get("users/meals/list", [App\Http\Controllers\Api\UserController::class, "mealsList"])->name("user_meals_list");
});
