<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function task(Request $request, $id)
    {
        abort_unless($request->hasValidSignatureWhileIgnoring(['exit_code']), 401);

        $task = Task::findOrFail($id);

        $task->update(['status' => 'finished']);

        foreach ($task->after_actions ?? [] as $callback) {
            if (!isset($callback['class'])) {
                continue;
            }

            $instance = app()->makeWith($callback['class'], $callback['args'] ?? []);

            $instance($task);
        }
    }
}
