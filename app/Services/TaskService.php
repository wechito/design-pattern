<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService {
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
    }

    public function findAll() {
        return $this->taskRepository->findAll();
    }

    public function create(array $data) {
        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data) {
        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task) {
        return $this->taskRepository->delete($task);
    }
}