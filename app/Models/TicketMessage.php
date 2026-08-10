<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id', 'user_id', 'message', 'attachments',
        'is_internal', 'is_system',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_internal' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
