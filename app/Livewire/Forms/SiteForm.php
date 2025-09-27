<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class SiteForm extends Form
{
    public ?int $server_id = null;
    public string $hostname = '';
    public string $php_version = '';
    public string $type = '';
    public string $web_folder = '/public';
    public bool $zero_downtime = true;
    public string $repository_url = '';
    public string $repository_branch = '';
    public bool $use_deploy_key = false;

    public function store($server): \App\Models\Site
    {
        return $server->sites()->create([
            'hostname' => $this->hostname,
            'php_version' => $this->php_version,
            'type' => $this->type,
            'web_folder' => $this->web_folder,
            'zero_downtime' => $this->zero_downtime,
            'repository_url' => $this->repository_url,
            'repository_branch' => $this->repository_branch,
            'use_deploy_key' => $this->use_deploy_key,
        ]);
    }
}
