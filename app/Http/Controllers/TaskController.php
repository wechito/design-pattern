<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    use ApiResponseTrait;
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(): JsonResponse {
        return $this->successResponse($this->taskService->findAll(), true, "Task obtenidas correctamente");
    }

    public function store(StoreTaskRequest $request): JsonResponse {
        $data = $this->taskService->create($request->all());
        return $this->successResponse($data, true, "Task creada correctamente", 201);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse {
        $data = $this->taskService->update($task, $request->all());
        return $this->successResponse($data, true, "Task actualizada correctamente");
    }

    public function destroy(Task $task): JsonResponse {
        $this->taskService->delete($task);
        return $this->successResponse($task, true, "", 204);
    }
}