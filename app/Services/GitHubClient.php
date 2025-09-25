<?php

namespace App\Services;

class GitHubClient
{
    public function __construct(public ?string $token = null) {}
}
