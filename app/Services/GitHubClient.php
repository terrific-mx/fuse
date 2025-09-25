<?php

namespace App\Services;

use App\Models\SourceProvider;

class GitHubClient
{
    public function __construct(protected SourceProvider $provider) {}
}
