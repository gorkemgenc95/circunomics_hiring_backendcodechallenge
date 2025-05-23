<?php

namespace App\Api;

interface ApiClientInterface
{
    public function getMostRecentCommits(string $owner, string $repo, int $limit = 1000): array;
}
