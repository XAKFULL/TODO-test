<?php

use App\Http\Controllers\TaskWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::name('tasks.')->prefix('tasks')->group(function () {
    Route::get('/kanban', [TaskWebController::class, 'kanban'])->name('kanban');
    Route::get('/list', [TaskWebController::class, 'list'])->name('list');
});
