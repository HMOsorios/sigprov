<?php

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\IpPoolController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\NetworkElementController;
use App\Http\Controllers\Admin\NFController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TechInventoryController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:developer,admin,technician,administrativo'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/block', [ClientController::class, 'block'])->name('clients.block');
    Route::post('clients/{client}/unblock', [ClientController::class, 'unblock'])->name('clients.unblock');

    Route::resource('plans', PlanController::class)->only(['index']);
    Route::middleware('role:developer,admin,administrativo')->group(function () {
        Route::resource('plans', PlanController::class)->except(['index']);
    });

    Route::resource('contracts', ContractController::class);
    Route::post('contracts/{contract}/suspend', [ContractController::class, 'suspend'])->name('contracts.suspend');
    Route::post('contracts/{contract}/reactivate', [ContractController::class, 'reactivate'])->name('contracts.reactivate');
    Route::post('contracts/{contract}/send-for-signing', [ContractController::class, 'sendForSigning'])->name('contracts.send-for-signing');
    Route::post('contracts/{contract}/mark-signed', [ContractController::class, 'markSigned'])->name('contracts.mark-signed');
    Route::get('contracts/{contract}/calculate-fine', [ContractController::class, 'calculateFine'])->name('contracts.calculate-fine');

    Route::get('leads/kanban', [LeadController::class, 'kanban'])->name('leads.kanban');
    Route::resource('leads', LeadController::class);
    Route::get('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('leads/{lead}/convert', [LeadController::class, 'doConvert'])->name('leads.do-convert');
    Route::post('leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('leads/bulk/update-status', [LeadController::class, 'bulkUpdateStatus'])->name('leads.bulk-update-status');

    Route::middleware('role:developer,admin,technician')->group(function () {
        Route::resource('servers', ServerController::class);
        Route::post('servers/{server}/ping', [ServerController::class, 'ping'])->name('servers.ping');

        Route::resource('links', LinkController::class);
        Route::post('links/{link}/block', [LinkController::class, 'block'])->name('links.block');
        Route::post('links/{link}/unblock', [LinkController::class, 'unblock'])->name('links.unblock');
    });

    Route::middleware('role:developer,admin,administrativo')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
        Route::post('invoices/{invoice}/emitir-nf', [NFController::class, 'emitir'])->name('invoices.emitir-nf');

        Route::get('nf', [NFController::class, 'index'])->name('nf.index');
        Route::get('nf/{nf}', [NFController::class, 'show'])->name('nf.show');
        Route::post('nf/{nf}/cancelar', [NFController::class, 'cancelar'])->name('nf.cancelar');
        Route::post('nf/{nf}/consultar', [NFController::class, 'consultar'])->name('nf.consultar');
    });

    Route::middleware('role:developer,admin,technician')->group(function () {
        Route::resource('equipment', EquipmentController::class);
        Route::post('equipment/import', [EquipmentController::class, 'import'])->name('equipment.import');

        Route::resource('assignments', AssignmentController::class)->only([
            'index', 'create', 'store', 'show',
        ]);
        Route::post('assignments/{assignment}/return', [AssignmentController::class, 'returnEquipment'])
            ->name('assignments.return');
    });

    Route::middleware('role:developer,admin,technician')->group(function () {
        Route::resource('ip-pools', IpPoolController::class);

        Route::resource('network-elements', NetworkElementController::class);
        Route::get('network-elements/map', [NetworkElementController::class, 'map'])->name('network-elements.map');
        Route::get('network-elements/tree', [NetworkElementController::class, 'tree'])->name('network-elements.tree');

        Route::get('work-orders/schedule', [WorkOrderController::class, 'schedule'])->name('work-orders.schedule');
        Route::post('work-orders/apply-schedule', [WorkOrderController::class, 'applySchedule'])->name('work-orders.apply-schedule');
        Route::get('work-orders/map', [WorkOrderController::class, 'map'])->name('work-orders.map');

        Route::resource('work-orders', WorkOrderController::class);
        Route::post('work-orders/{workOrder}/assign', [WorkOrderController::class, 'assign'])->name('work-orders.assign');
        Route::post('work-orders/{workOrder}/start', [WorkOrderController::class, 'start'])->name('work-orders.start');
        Route::post('work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');
        Route::post('work-orders/{workOrder}/cancel', [WorkOrderController::class, 'cancel'])->name('work-orders.cancel');

        Route::resource('tech-inventories', TechInventoryController::class)->only([
            'index', 'create', 'store',
        ]);
        Route::post('tech-inventories/{techInventory}/checkin', [TechInventoryController::class, 'checkin'])
            ->name('tech-inventories.checkin');
    });

    Route::middleware('role:developer,admin')->group(function () {
        Route::resource('suppliers', SupplierController::class);

        Route::resource('warehouse', WarehouseController::class);
        Route::post('warehouse/{warehouseItem}/movement', [WarehouseController::class, 'movement'])
            ->name('warehouse.movement');
    });

    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::post('tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');

    Route::middleware('role:developer,admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::get('dashboard/executive', [DashboardController::class, 'executive'])->name('dashboard.executive');

    Route::middleware('role:developer,admin,administrativo')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('reports/clients', [ReportController::class, 'clients'])->name('reports.clients');
        Route::get('reports/tickets', [ReportController::class, 'tickets'])->name('reports.tickets');
        Route::get('reports/churn', [ReportController::class, 'churn'])->name('reports.churn');
        Route::get('reports/cac', [ReportController::class, 'cac'])->name('reports.cac');
        Route::get('reports/delinquency', [ReportController::class, 'delinquency'])->name('reports.delinquency');
        Route::get('reports/budget', [ReportController::class, 'budget'])->name('reports.budget');
        Route::get('reports/collection', [ReportController::class, 'collection'])->name('reports.collection');
        Route::get('reports/sla', [ReportController::class, 'sla'])->name('reports.sla');
        Route::get('reports/nps', [ReportController::class, 'nps'])->name('reports.nps');

        Route::get('reports/export/invoices', [ReportController::class, 'exportInvoices'])->name('reports.export.invoices');
        Route::get('reports/export/clients', [ReportController::class, 'exportClients'])->name('reports.export.clients');
        Route::get('reports/export/tickets', [ReportController::class, 'exportTickets'])->name('reports.export.tickets');
        Route::get('reports/export/financial', [ReportController::class, 'exportFinancial'])->name('reports.export.financial');
    });

    Route::get('settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
    Route::post('settings/notifications/{notification}/read', [SettingController::class, 'markNotification'])->name('settings.notifications.read');
    Route::post('settings/notifications/read-all', [SettingController::class, 'markAllNotifications'])->name('settings.notifications.read-all');

    Route::middleware('role:developer,admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('settings/system', [SettingController::class, 'system'])->name('settings.system');
        Route::get('settings/audit', [SettingController::class, 'audit'])->name('settings.audit');
        Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');
    });
});
