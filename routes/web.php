<?php

use App\Http\Controllers\TaskWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskWebController::class, 'kanban'])->name('tasks.kanban');
Route::get('/list', [TaskWebController::class, 'list'])->name('tasks.list');
