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

        if ($task->callback && class_exists($task->callback)) {
            $callback = app($task->callback);

            $callback($task);
        }

        return response()->json($task);
    }
}
