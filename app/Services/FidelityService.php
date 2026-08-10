<?php

namespace App\Services;

use App\Models\Contract;
use Carbon\Carbon;

class FidelityService
{
    public function calculateCancellationFine(Contract $contract): float
    {
        if (!$contract->minimum_duration_months || !$contract->start_date) {
            return 0;
        }

        $monthsElapsed = $contract->start_date->diffInMonths(Carbon::now());
        $remaining = max(0, $contract->minimum_duration_months - $monthsElapsed);
        $totalMonths = $contract->minimum_duration_months;

        if ($remaining <= 0) {
            return 0;
        }

        $monthlyPrice = $contract->effective_price;
        $formula = $contract->cancellation_fine_formula ?? 'proportional_remaining';

        return match ($formula) {
            'fixed_20' => $monthlyPrice * 0.20,
            'fixed_30' => $monthlyPrice * 0.30,
            'fixed_50' => $monthlyPrice * 0.50,
            'proportional_remaining' => ($monthlyPrice / $totalMonths) * $remaining,
            'anatel' => $this->calculateAnatelFine($monthlyPrice, $remaining, $totalMonths),
            default => 0,
        };
    }

    public function getMonthsElapsed(Contract $contract): int
    {
        if (!$contract->start_date) {
            return 0;
        }
        return (int) $contract->start_date->diffInMonths(Carbon::now());
    }

    public function getMonthsRemaining(Contract $contract): int
    {
        return max(0, ($contract->minimum_duration_months ?? 0) - $this->getMonthsElapsed($contract));
    }

    public function isWithinFidelity(Contract $contract): bool
    {
        return $this->getMonthsRemaining($contract) > 0;
    }

    protected function calculateAnatelFine(float $monthlyPrice, int $remaining, int $totalMonths): float
    {
        $finePercent = match (true) {
            $totalMonths <= 6 => 0.10,
            $totalMonths <= 12 => 0.15,
            default => 0.20,
        };

        return ($monthlyPrice * $finePercent) * ($remaining / $totalMonths);
    }
}
