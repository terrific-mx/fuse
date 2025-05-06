<?php

namespace App;

use App\Models\SourceProvider;

class FakeSourceProvider
{
    public function __construct(public SourceProvider $sourceProvider) {}

    public function valid()
    {
        if ($this->sourceProvider->token === 'valid-token') {
            return true;
        }

        return false;
    }

    public function validRepository(string $repository)
    {
        if ($repository === 'example/valid-repository') {
            return true;
        }

        return false;
    }

    public function validBranch(string $branch, string $repository)
    {
        if ($branch === 'valid-branch' && $this->validRepository($repository)) {
            return true;
        }

        return false;
    }
}
