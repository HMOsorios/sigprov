<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkLog extends Model
{
    protected $fillable = [
        'link_id', 'action', 'description', 'metadata', 'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
