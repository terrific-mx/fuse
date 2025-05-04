<?php

use App\Jobs\FinishTask;
use App\Models\Task;
use Illuminate\Http\Request;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('callback');

render(function (Request $request, Task $task) {
    abort_unless($request->hasValidSignatureWhileIgnoring(['exit_code'], absolute: false), 401);

    abort_unless($task->isRunning(), 404);

    FinishTask::dispatch(
        $task,
        (int) $request->query('exit_code')
    );
});
