<?php

use App\Models\SshKey;
use Livewire\Volt\Component;

new class extends Component
{
    public SshKey $sshKey;

    public function mount(SshKey $sshKey)
    {
        $this->authorize('update', $sshKey);
    }
}; ?>

<div>
    //
</div>
