<?php

use App\Livewire\Forms\UpdateDeploymentSettingsForm;
use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;

    public UpdateDeploymentSettingsForm $form;

    public function mount(): void
    {
        $this->authorize('view', $this->server);

        $this->authorize('view', $this->site);

        $this->form->setSite($this->site);
    }

    public function save(): void
    {
        $this->form->update();
    }
}; ?>

<div>

</div>
