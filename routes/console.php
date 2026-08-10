<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:generate-invoices')->dailyAt('02:00');
Schedule::command('app:check-overdue-invoices')->dailyAt('02:30');
Schedule::command('app:send-collection-notifications')->dailyAt('08:00');
Schedule::command('app:auto-block-overdue --days=10')->dailyAt('03:00');
Schedule::command('app:auto-unblock-paid')->everyFiveMinutes();
