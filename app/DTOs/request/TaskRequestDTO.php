<?php

namespace App\DTOs\request;

use App\Models\Task;

class TaskRequestDTO {
    public string $title;
    public string $description;
    public string $status;

    public function __construct(string $title, string $description, string $status) {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public static function fromModel(Task $task): self {
        return new self($task->title, $task->description, $task->status);
    }
}