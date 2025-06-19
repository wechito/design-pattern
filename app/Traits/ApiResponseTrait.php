<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait {

    public function successResponse($data, bool $success, string $message, int $code = 200): JsonResponse {
        return response()->json([
            "success" => $success,
            "message" => $message,
            "data" => $data
        ], $code);
    }
}