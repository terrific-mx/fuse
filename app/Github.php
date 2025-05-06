<?php

namespace App;

use Illuminate\Support\Facades\Http;

class Github extends FakeSourceProvider
{
    public function valid()
    {
        return (bool) $this->request('get', '/user/repos')->successful();
    }

    public function validRepository(string $repository)
    {
        return (bool) $this->request('get', "/repos/{$repository}/branches")->successful();
    }

    public function validBranch(string $branch, string $repository)
    {
        $response = $this->request('get', "/repos/{$repository}/branches");

        if ($response->failed()) {
            return false;
        }

        return collect($response->json())->contains(function ($b) use ($branch) {
            return $b['name'] === $branch;
        });
    }

    protected function request(string $method, string $path, $parameters = [])
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'token '.$this->sourceProvider->token,
        ])->{$method}('https://api.github.com/'.ltrim($path, '/'), $parameters);

        return $response;
    }
}
