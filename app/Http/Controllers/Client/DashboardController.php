<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $clients = $user->clients;

        $contracts = $clients->flatMap->activeContracts;

        $activeContracts = $contracts->count();
        $pendingInvoices = Invoice::whereIn('client_id', $clients->pluck('id'))
            ->pending()->sum('total');

        $overdueInvoices = Invoice::whereIn('client_id', $clients->pluck('id'))
            ->overdue()->count();

        $openTickets = Ticket::whereIn('client_id', $clients->pluck('id'))
            ->open()->count();

        $recentInvoices = Invoice::whereIn('client_id', $clients->pluck('id'))
            ->latest()->take(5)->get();

        $recentTickets = Ticket::whereIn('client_id', $clients->pluck('id'))
            ->latest()->take(5)->get();

        return view('client.dashboard', compact(
            'contracts', 'activeContracts', 'pendingInvoices',
            'overdueInvoices', 'openTickets',
            'recentInvoices', 'recentTickets'
        ));
    }
}
