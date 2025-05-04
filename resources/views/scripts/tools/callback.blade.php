@include('partials.scripts.shell-defaults', ['exitImmediately' => false])

DIRECTORY=$(dirname "$0")
FILENAME=$(basename "$0")
EXT="${FILENAME##*.}"
PATH_ACTUAL_SCRIPT="$DIRECTORY/${FILENAME%.*}-original.$EXT"

# Writing actual script to $PATH_ACTUAL_SCRIPT
cat > $PATH_ACTUAL_SCRIPT << '{!! $token !!}'
{!! $task->script !!}

{!! $token !!}

# Running actual script
@if ($task->timeout() > 0)
    timeout {{ $task->timeout() }}s bash $PATH_ACTUAL_SCRIPT
@else
    bash $PATH_ACTUAL_SCRIPT
@endif
EXIT_CODE=$?

curl --insecure "{!! url(config('app.callback_url') . URL::signedRoute('callback', ['task' => $task->id], absolute: false)) !!}&exit_code=$EXIT_CODE" > /dev/null 2>&1
