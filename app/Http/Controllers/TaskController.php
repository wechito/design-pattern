<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(): JsonResponse {
        return response()->json([
            "success" => true,
            "data" => $this->taskService->findAll()
        ]);
    }

    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            "title" => "required",
            "description" => "required",
            "status" => "required"
        ]);

        $task = $this->taskService->create($data);

        return response()->json([
            "success" => true,
            "data" => $task
        ]);
    }

    public function update(Request $request, Task $task): JsonResponse {
        $data = $request->validate([
            "title" => "required",
            "description" => "required",
            "status" => "required"
        ]);

        $task = $this->taskService->update($task, $data);

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function destroy(Task $task): JsonResponse {
        $task = $this->taskService->delete($task);

        return response()->json([
            "success" => true,
            "data" => $task
        ]);
    }
}
