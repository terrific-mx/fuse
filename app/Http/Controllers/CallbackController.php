<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;

class CallbackController extends Controller
{
    public function task($id): JsonResponse
    {
        $task = Task::findOrFail($id);

        $task->update(['status' => 'finished']);

        foreach ($task->after_actions as $callback) {
            if (!isset($callback['class'])) {
                continue;
            }

            $instance = app()->makeWith($callback['class'], $callback['args'] ?? []);

            $instance($task);
        }

        return response()->json($task);
    }
}
