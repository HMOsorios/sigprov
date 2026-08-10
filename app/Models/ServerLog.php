<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'server_id', 'type', 'message', 'metadata', 'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
