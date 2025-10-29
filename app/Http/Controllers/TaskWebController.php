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
        $pageTitle = 'Канбан';

        return view('tasks.pages.kanban', compact('tasks', 'pageTitle'));
    }

    public function list(): View
    {
        $pageTitle = 'Список';

        return view('tasks.pages.list', compact('pageTitle'));
    }
}
