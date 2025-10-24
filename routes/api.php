<?php

use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TaskController::class);
Route::apiResource('tags', TagController::class);

Route::post('tasks-paginated', [TaskController::class, 'paginate']);
