<?php

namespace App\DTOs\response;

use App\Models\Task;

class TaskResponseDTO {
    public string $id;
    public string $title;
    public string $description;
    public string $status;

    public function __construct(string $id, string $title, string $description, string $status) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public static function fromModel(Task $task): self {
        return new self($task->id, $task->title, $task->description, $task->status);
    }
}