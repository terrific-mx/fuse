<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateTaskStatusJob;
use App\Models\Task;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function task(Request $request, $id)
    {
        abort_unless($request->hasValidSignatureWhileIgnoring(['exit_code']), 401);

        $task = Task::findOrFail($id);

        abort_unless($task->status === 'running', 404);

        UpdateTaskStatusJob::dispatch($task, (int) $request->input('exit_code', 0));
    }
}
