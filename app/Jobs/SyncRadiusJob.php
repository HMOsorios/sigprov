<?php

namespace App\Jobs;

use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SyncRadiusJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Link $link, public string $action) {}

    public function handle(): void
    {
        $this->link->logs()->create([
            'action' => "radius_{$this->action}",
            'description' => "Sincronia Radius: {$this->action} - PPPoE {$this->link->pppoe_user}",
        ]);
    }
}
