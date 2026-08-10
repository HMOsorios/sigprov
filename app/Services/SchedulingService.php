<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;

class SchedulingService
{
    public function getUnscheduled(): Collection
    {
        return WorkOrder::with(['client', 'contract'])
            ->whereIn('status', ['pending', 'scheduled'])
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();
    }

    public function getTechSchedule(int $technicianId, string $date): Collection
    {
        return WorkOrder::with(['client', 'contract'])
            ->where('technician_id', $technicianId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();
    }

    public function suggestSchedule(?string $date = null): Collection
    {
        $date = $date ?: now()->toDateString();

        $orders = WorkOrder::with(['client', 'contract'])
            ->whereIn('status', ['pending', 'scheduled'])
            ->whereNull('scheduled_at')
            ->orWhereDate('scheduled_at', '>=', $date)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($o) => $this->getRegionKey($o));

        $techs = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician']))
            ->where('active', true)
            ->get();

        $techsWithOrders = [];
        $techIndex = 0;

        foreach ($orders as $region => $regionOrders) {
            $sorted = $regionOrders->sortByDesc('priority')->values();

            foreach ($sorted as $order) {
                if ($techs->isEmpty()) {
                    break;
                }

                $tech = $techs[$techIndex % $techs->count()];

                $existingCount = WorkOrder::where('technician_id', $tech->id)
                    ->whereDate('scheduled_at', $date)
                    ->count();

                if ($existingCount >= 6) {
                    $techIndex++;
                    $tech = $techs[$techIndex % $techs->count()];
                }

                $hour = 8 + ($existingCount * 1.5);
                $scheduledAt = "{$date} " . sprintf('%02d:%02d:00', floor($hour), ($hour - floor($hour)) * 60);

                $order->suggested_technician_id = $tech->id;
                $order->suggested_time = $scheduledAt;

                $techIndex++;
            }
        }

        return $orders->flatten();
    }

    public function applySuggestion(int $workOrderId, int $technicianId, string $scheduledAt): WorkOrder
    {
        $order = WorkOrder::findOrFail($workOrderId);

        $order->update([
            'technician_id' => $technicianId,
            'scheduled_at' => $scheduledAt,
            'status' => 'scheduled',
        ]);

        return $order->fresh();
    }

    protected function getRegionKey(WorkOrder $order): string
    {
        $client = $order->client;
        if (!$client) {
            return 'sem-regiao';
        }

        $city = $client->city ?? 'sem-cidade';
        $neighborhood = $client->neighborhood ?? 'sem-bairro';

        return "{$city}::{$neighborhood}";
    }
}
