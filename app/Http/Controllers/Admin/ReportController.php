<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientExport;
use App\Exports\FinancialReportExport;
use App\Exports\InvoiceExport;
use App\Exports\TicketExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function financial(Request $request): View
    {
        $year = $request->get('year', now()->year);

        $monthlyRevenue = Invoice::selectRaw('
                MONTH(issue_date) as month,
                SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid,
                SUM(CASE WHEN status IN ("pending", "overdue") THEN total ELSE 0 END) as pending,
                COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_count,
                COUNT(CASE WHEN status IN ("pending", "overdue") THEN 1 END) as pending_count
            ')
            ->whereYear('issue_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totalRevenue = Invoice::where('status', 'paid')->whereYear('paid_date', $year)->sum('total');
        $totalPending = Invoice::whereIn('status', ['pending', 'overdue'])->sum('total');
        $totalOverdue = Invoice::overdue()->sum('total');
        $averageTicket = Invoice::where('status', 'paid')->whereYear('paid_date', $year)->avg('total');
        $invoicedTotal = Invoice::whereYear('issue_date', $year)->sum('total');

        return view('admin.reports.financial', compact(
            'monthlyRevenue', 'totalRevenue', 'totalPending', 'totalOverdue',
            'averageTicket', 'invoicedTotal', 'year'
        ));
    }

    public function clients(Request $request): View
    {
        $clientStats = Client::selectRaw('
                status,
                COUNT(*) as total,
                (SELECT COUNT(*) FROM contracts WHERE contracts.client_id = clients.id AND contracts.status = "active") as active_contracts
            ')
            ->groupBy('status')
            ->get();

        $topClients = Client::withSum(['invoices' => function ($q) {
            $q->where('status', 'paid');
        }], 'total')
            ->orderByDesc('invoices_sum_total')
            ->limit(10)
            ->get();

        $clientsByCity = Client::selectRaw('city, state, COUNT(*) as total')
            ->groupBy('city', 'state')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        $totalActive = Client::where('status', 'active')->count();
        $totalBlocked = Client::where('status', 'blocked')->count();
        $totalCanceled = Client::where('status', 'canceled')->count();

        $newClientsThisMonth = Client::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        return view('admin.reports.clients', compact(
            'clientStats', 'topClients', 'clientsByCity',
            'totalActive', 'totalBlocked', 'totalCanceled', 'newClientsThisMonth'
        ));
    }

    public function tickets(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $ticketsByCategory = Ticket::selectRaw('category, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('category')
            ->get();

        $ticketsByPriority = Ticket::selectRaw('priority, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('priority')
            ->get();

        $averageResolutionTime = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $totalTickets = Ticket::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $resolvedTickets = Ticket::where('status', 'resolved')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        return view('admin.reports.tickets', compact(
            'ticketsByCategory', 'ticketsByPriority',
            'averageResolutionTime', 'totalTickets',
            'resolvedTickets', 'startDate', 'endDate'
        ));
    }

    public function churn(Request $request): View
    {
        $year = $request->get('year', now()->year);

        $canceledContracts = Contract::selectRaw('
                MONTH(updated_at) as month,
                COUNT(*) as total
            ')
            ->where('status', 'canceled')
            ->whereYear('updated_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $activeByMonth = [];
        foreach (range(1, 12) as $m) {
            $date = now()->setMonth($m)->setYear($year);
            $activeByMonth[$m] = Contract::where('status', 'active')
                ->where('start_date', '<=', $date->endOfMonth())
                ->where(function ($q) use ($date) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $date->startOfMonth());
                })
                ->count();
        }

        $churnData = [];
        foreach (range(1, 12) as $m) {
            $canceled = $canceledContracts->get($m)?->total ?? 0;
            $active = $activeByMonth[$m] ?? 1;
            $churnData[] = [
                'month' => $m,
                'active' => $active,
                'canceled' => $canceled,
                'rate' => $active > 0 ? round(($canceled / $active) * 100, 2) : 0,
            ];
        }

        $totalCanceledYear = Contract::where('status', 'canceled')->whereYear('updated_at', $year)->count();
        $churnRate = $activeByMonth[now()->month] > 0
            ? round(($totalCanceledYear / $activeByMonth[now()->month]) * 100, 2)
            : 0;

        return view('admin.reports.churn', compact('churnData', 'churnRate', 'totalCanceledYear', 'year'));
    }

    public function cac(Request $request): View
    {
        $year = $request->get('year', now()->year);
        $monthlyMarketingCost = $request->get('marketing_cost', 0);

        $newClientsByMonth = Client::selectRaw('
                MONTH(created_at) as month,
                COUNT(*) as total
            ')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $cacData = [];
        foreach (range(1, now()->month) as $m) {
            $newClients = $newClientsByMonth->get($m)?->total ?? 0;
            $cacData[] = [
                'month' => $m,
                'new_clients' => $newClients,
                'marketing_cost' => $monthlyMarketingCost,
                'cac' => $newClients > 0 ? round($monthlyMarketingCost / $newClients, 2) : 0,
            ];
        }

        $totalNewClients = collect($cacData)->sum('new_clients');

        return view('admin.reports.cac', compact('cacData', 'totalNewClients', 'year', 'monthlyMarketingCost'));
    }

    public function delinquency(Request $request): View
    {
        $aging = [
            '0_30' => Invoice::whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '>=', now()->subDays(30))
                ->where('due_date', '<=', now()),
            '31_60' => Invoice::whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now()->subDays(30))
                ->where('due_date', '>=', now()->subDays(60)),
            '61_90' => Invoice::whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now()->subDays(60))
                ->where('due_date', '>=', now()->subDays(90)),
            '90_plus' => Invoice::whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now()->subDays(90)),
        ];

        $agingData = [];
        foreach ($aging as $range => $query) {
            $agingData[$range] = [
                'total' => $query->count(),
                'amount' => $query->sum('total'),
            ];
        }

        $totalOverdue = Invoice::whereIn('status', ['pending', 'overdue'])->sum('total');
        $totalPortfolio = Invoice::sum('total');
        $delinquencyPercent = $totalPortfolio > 0 ? round(($totalOverdue / $totalPortfolio) * 100, 2) : 0;

        return view('admin.reports.delinquency', compact('agingData', 'totalOverdue', 'totalPortfolio', 'delinquencyPercent'));
    }

    public function budget(Request $request): View
    {
        $year = $request->get('year', now()->year);
        $monthlyGoal = $request->get('monthly_goal', 0);

        $realized = Invoice::selectRaw('
                MONTH(issue_date) as month,
                SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid,
                SUM(total) as invoiced
            ')
            ->whereYear('issue_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $budgetData = [];
        foreach (range(1, 12) as $m) {
            $data = $realized->get($m);
            $paid = $data->paid ?? 0;
            $invoiced = $data->invoiced ?? 0;
            $goal = $monthlyGoal ?: 0;

            $budgetData[] = [
                'month' => $m,
                'goal' => $goal,
                'invoiced' => $invoiced,
                'paid' => $paid,
                'achievement' => $goal > 0 ? round(($paid / $goal) * 100, 1) : 0,
            ];
        }

        $totalGoal = $monthlyGoal * 12;
        $totalPaid = collect($budgetData)->sum('paid');
        $totalInvoiced = collect($budgetData)->sum('invoiced');

        return view('admin.reports.budget', compact('budgetData', 'totalGoal', 'totalPaid', 'totalInvoiced', 'year', 'monthlyGoal'));
    }

    public function collection(Request $request): View
    {
        $year = $request->get('year', now()->year);

        $monthly = Invoice::selectRaw('
                MONTH(issue_date) as month,
                SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as collected,
                SUM(CASE WHEN status IN ("pending", "overdue") THEN total ELSE 0 END) as outstanding,
                SUM(total) as total
            ')
            ->whereYear('issue_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $collectionData = [];
        foreach ($monthly as $m) {
            $collectionData[] = [
                'month' => $m->month,
                'collected' => $m->collected,
                'outstanding' => $m->outstanding,
                'total' => $m->total,
                'rate' => $m->total > 0 ? round(($m->collected / $m->total) * 100, 1) : 0,
            ];
        }

        $totalCollected = collect($collectionData)->sum('collected');
        $totalOutstanding = collect($collectionData)->sum('outstanding');
        $overallRate = ($totalCollected + $totalOutstanding) > 0
            ? round(($totalCollected / ($totalCollected + $totalOutstanding)) * 100, 1)
            : 0;

        return view('admin.reports.collection', compact('collectionData', 'totalCollected', 'totalOutstanding', 'overallRate', 'year'));
    }

    public function sla(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $slaByCategory = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('
                category,
                AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours,
                COUNT(*) as total
            ')
            ->groupBy('category')
            ->get();

        $slaByTechnician = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereNotNull('assigned_to')
            ->selectRaw('
                assigned_to,
                AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours,
                COUNT(*) as total
            ')
            ->with('assignedTo')
            ->groupBy('assigned_to')
            ->get();

        $avgFirstResponse = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $ticketsWithinSla = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, resolved_at) <= 24')
            ->count();

        $totalResolved = Ticket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        return view('admin.reports.sla', compact(
            'slaByCategory', 'slaByTechnician',
            'avgFirstResponse', 'ticketsWithinSla', 'totalResolved',
            'startDate', 'endDate'
        ));
    }

    public function nps(Request $request): View
    {
        $responses = Client::whereNotNull('nps_score')
            ->selectRaw('
                CASE
                    WHEN nps_score >= 9 THEN "promoters"
                    WHEN nps_score >= 7 THEN "neutrals"
                    ELSE "detractors"
                END as category,
                COUNT(*) as total,
                AVG(nps_score) as avg_score
            ')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $promoters = $responses->get('promoters')?->total ?? 0;
        $neutrals = $responses->get('neutrals')?->total ?? 0;
        $detractors = $responses->get('detractors')?->total ?? 0;
        $total = $promoters + $neutrals + $detractors;

        $npsScore = $total > 0
            ? round((($promoters - $detractors) / $total) * 100, 1)
            : 0;

        $totalNpsResponses = $total;

        return view('admin.reports.nps', compact(
            'promoters', 'neutrals', 'detractors',
            'totalNpsResponses', 'npsScore'
        ));
    }

    public function exportInvoices(Request $request)
    {
        return (new InvoiceExport)->export($request->only(['status', 'start_date', 'end_date']));
    }

    public function exportClients(Request $request)
    {
        return (new ClientExport)->export($request->only(['status', 'person_type']));
    }

    public function exportTickets(Request $request)
    {
        return (new TicketExport)->export($request->only(['status', 'category', 'start_date', 'end_date']));
    }

    public function exportFinancial(Request $request)
    {
        return (new FinancialReportExport)->export($request->get('year', now()->year));
    }
}
