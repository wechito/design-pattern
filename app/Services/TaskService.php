<?php

namespace App\Services;

use App\DTOs\response\TaskResponseDTO;
use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService {
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
    }

    public function findAll() {
        $tasks = $this->taskRepository->findAll();

        return $tasks->map(function (Task $task) {
            return TaskResponseDTO::fromModel($task);
        });
    }

    public function create(array $data) {
        $task = $this->taskRepository->create($data);

        return TaskResponseDTO::fromModel($task);
    }

    public function update(Task $task, array $data) {
        $this->taskRepository->update($task, $data);
        return TaskResponseDTO::fromModel($task);
    }

    public function delete(Task $task) {
        return $this->taskRepository->delete($task);
    }
}