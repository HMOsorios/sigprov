<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'contract_id' => $this->contract_id,
            'technician' => $this->whenLoaded('technician', fn() => [
                'id' => $this->technician->id,
                'name' => $this->technician->name,
            ]),
            'type' => $this->type,
            'type_label' => $this->type_label,
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'description' => $this->description,
            'resolution' => $this->resolution,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'finished_at' => $this->finished_at?->toIso8601String(),
            'photos' => $this->photos,
            'client_signature' => $this->client_signature ? url("storage/{$this->client_signature}") : null,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
