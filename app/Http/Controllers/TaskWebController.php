<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskWebController extends Controller
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function kanban(Request $request): View
    {
        $tasks = $this->taskRepository->all();

        return view('tasks.index', compact('tasks'));
    }

    public function list(): View
    {
        return view('tasks.list');
    }
}
