<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    protected $table = 'commits';

    protected $fillable = [
        'hash',
        'author',
        'date',
        'repository_owner',
        'repository_name',
        'platform',
        'message',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function toApiFormat(): array
    {
        return [
            'hash' => $this->getAttribute('hash'),
            'author' => $this->getAttribute('author'),
            'date' => $this->getAttribute('date'),
            'platform' => $this->getAttribute('platform'),
        ];
    }

    public function scopeForRepository($query, string $platform, string $owner, string $repo)
    {
        return $query
            ->where('platform', $platform)
            ->where('repository_owner', $owner)
            ->where('repository_name', $repo);
    }

    public function scopeMostRecent($query, int $limit = 1000)
    {
        return $query->orderBy('date', 'desc')->limit($limit);
    }
}
