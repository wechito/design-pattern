<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository {

    public function findAll() {
        return Task::all();
    }

    public function create(array $data) {
        return Task::create($data);
    }

    public function update(Task $task, array $data) {
        return $task->update($data);
    }

    public function delete(Task $task): void {
        $task->delete();
    }
}