<?php

namespace App\Models;

use App\SecureShellCommand;
use App\ShellResult;
use Facades\App\ShellProcessRunner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    const DEFAULT_TIMEOUT = 3600;

    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function run()
    {
        $this->markAsRunning();

        $this->ensureWorkingDirectoryExists();

        if (! $this->upload()) {
            return $this->markAsTimedOut();
        }

        return $this->updateForResult($this->runInline(sprintf(
            'bash %s 2>&1 | tee %s',
            $this->scriptFile(),
            $this->outputFile()
        ), $this->options['timeout'] ?? 60));
    }

    public function runInBackground()
    {
        $this->markAsRunning();

        $this->addCallbackToScript();

        $this->ensureWorkingDirectoryExists();

        if (! $this->upload()) {
            return $this->markAsTimedOut();
        }

        ShellProcessRunner::run($this->toSecureShellCommand(sprintf(
            '\'nohup bash %s >> %s 2>&1 &\'',
            $this->scriptFile(),
            $this->outputFile()
        )), 10);

        return $this;
    }

    public function isFinished()
    {
        return $this->status === 'finished';
    }

    public function isRunning()
    {
        return $this->status === 'running';
    }

    public function successful()
    {
        return (int) $this->exit_code === 0;
    }

    public function timeout()
    {
        return (int) ($this->options['timeout'] ?? Task::DEFAULT_TIMEOUT);
    }

    public function finish($exitCode = 0)
    {
        $this->markAsFinished($exitCode);

        $this->update([
            'output' => $this->retrieveOutput(),
        ]);

        foreach ($this->options['then'] ?? [] as $callback) {
            is_object($callback)
                ? $callback->handle($this)
                : app($callback)->handle($this);
        }
    }

    public function retrieveOutput(?string $path = null)
    {
        return $this->runInline('tail --bytes=2000000 '.($path ?? $this->outputFile()), 10)->output;
    }

    protected function markAsRunning()
    {
        return tap($this)->update([
            'status' => 'running',
        ]);
    }

    protected function markAsTimedOut($output = '')
    {
        return tap($this)->update([
            'exit_code' => 1,
            'status' => 'timeout',
            'output' => $output,
        ]);
    }

    protected function markAsFinished($exitCode = 0, $output = '')
    {
        return tap($this)->update([
            'exit_code' => $exitCode,
            'status' => 'finished',
            'output' => $output,
        ]);
    }

    protected function ensureWorkingDirectoryExists()
    {
        $this->runInline('mkdir -p '.$this->path(), 10);
    }

    protected function runInline(string $script, $timeout = 60)
    {
        $token = Str::random(20);

        return ShellProcessRunner::run($this->toSecureShellCommand('\'bash -s \' << \''.$token.'\'
'.$script.'
'.$token), $timeout);
    }

    protected function toSecureShellCommand(string $script)
    {
        return SecureShellCommand::forScript(
            $this->server->public_address,
            $this->server->port,
            $this->server->ownerKeyPath(),
            $this->user,
            $script
        );
    }

    protected function path()
    {
        return $this->user === 'root'
            ? '/root/.fuse'
            : '/home/fuse/.fuse';
    }

    protected function upload()
    {
        $secureShellCommand = SecureShellCommand::forUpload(
            $this->server->public_address,
            $this->server->port,
            $this->server->ownerKeyPath(),
            $this->user,
            $localScript = $this->writeScript(),
            $this->scriptFile()
        );

        $result = ShellProcessRunner::run($secureShellCommand, timeout: 15);

        @unlink($localScript);

        return $result->exitCode === 0;
    }

    protected function writeScript()
    {
        $hash = md5(Str::random(20).$this->script);

        return tap(storage_path('app/scripts').'/'.$hash, function ($path) {
            file_put_contents($path, $this->script);
        });
    }

    protected function scriptFile()
    {
        return $this->path().'/'.$this->id.'.sh';
    }

    protected function outputFile()
    {
        return $this->path().'/'.$this->id.'.out';
    }

    protected function updateForResult(ShellResult $result)
    {
        return tap($this)->update([
            'status' => $result->timedOut ? 'timeout' : 'finished',
            'exit_code' => $result->exitCode,
            'output' => $result->output,
        ]);
    }

    protected function addCallbackToScript()
    {
        $this->update([
            'script' => view('scripts.tools.callback', [
                'task' => $this,
                'token' => Str::upper(Str::random(20)),
            ])->render(),
        ]);
    }

    protected function options(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => unserialize($value),
            set: fn (array $value) => serialize($value),
        );
    }
}
