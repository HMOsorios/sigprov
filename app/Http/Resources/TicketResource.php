<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'assigned_to' => $this->whenLoaded('assignedTo', fn() => [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
            ]),
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
            'created_at' => $this->created_at->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
        ];
    }
}
