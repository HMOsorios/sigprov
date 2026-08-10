<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Link;
use App\Models\Server;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'total_contracts' => Contract::where('status', 'active')->count(),
            'total_links' => Link::where('status', 'active')->count(),
            'total_servers' => Server::count(),
            'online_servers' => Server::where('status', 'online')->count(),
            'open_tickets' => Ticket::open()->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'pending_invoices' => Invoice::pending()->count(),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereMonth('paid_date', now()->month)
                ->whereYear('paid_date', now()->year)
                ->sum('total'),
            'pending_revenue' => Invoice::pending()->sum('total'),
            'total_users' => User::count(),
        ];

        $recent_clients = Client::latest()->take(5)->get();
        $recent_tickets = Ticket::open()->latest()->take(5)->get();
        $overdue_invoices = Invoice::overdue()->latest()->take(5)->get();
        $online_servers = Server::where('status', 'online')->take(5)->get();

        $revenue_chart = Invoice::selectRaw('MONTH(issue_date) as month, SUM(total) as total')
            ->where('status', 'paid')
            ->whereYear('issue_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $tickets_by_category = Ticket::selectRaw('category, COUNT(*) as total')
            ->whereIn('status', ['open', 'in_progress', 'waiting_client'])
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.dashboard', compact(
            'stats', 'recent_clients', 'recent_tickets',
            'overdue_invoices', 'online_servers',
            'revenue_chart', 'tickets_by_category'
        ));
    }

    public function executive(): View
    {
        $kpis = Cache::remember('dashboard.executive', 300, function () {
            $now = now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();

            $revenueThisMonth = Invoice::where('status', 'paid')
                ->whereBetween('paid_date', [$monthStart, $monthEnd])
                ->sum('total');

            $totalInvoiced = Invoice::whereBetween('issue_date', [$monthStart, $monthEnd])
                ->sum('total');

            $totalOverdue = Invoice::overdue()->sum('total');
            $totalPortfolio = Invoice::sum('total');
            $delinquencyRate = $totalPortfolio > 0
                ? round(($totalOverdue / $totalPortfolio) * 100, 2)
                : 0;

            $activeContracts = Contract::where('status', 'active')->count();
            $canceledThisMonth = Contract::where('status', 'canceled')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
            $churnRate = $activeContracts > 0
                ? round(($canceledThisMonth / $activeContracts) * 100, 2)
                : 0;

            $newClients = Client::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $openTickets = Ticket::open()->count();
            $pendingWorkOrders = WorkOrder::whereIn('status', ['pending', 'scheduled'])->count();

            $revenue12Months = Invoice::selectRaw('
                    DATE_FORMAT(issue_date, "%Y-%m") as month,
                    SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid,
                    SUM(total) as invoiced
                ')
                ->where('issue_date', '>=', $now->copy()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $clientEvolution = Client::selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as month,
                    COUNT(*) as total
                ')
                ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $ticketsByStatus = Ticket::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return compact(
                'revenueThisMonth', 'totalInvoiced',
                'totalOverdue', 'delinquencyRate',
                'activeContracts', 'canceledThisMonth', 'churnRate',
                'newClients', 'openTickets', 'pendingWorkOrders',
                'revenue12Months', 'clientEvolution', 'ticketsByStatus',
            );
        });

        return view('admin.executive', compact('kpis'));
    }
}
