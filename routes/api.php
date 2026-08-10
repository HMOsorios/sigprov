<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\TechInventoryController as ApiTechInventoryController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/webhooks/{gateway}', WebhookController::class)
    ->whereIn('gateway', ['asaas', 'mercadopago']);

Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle']);
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/client', [ClientController::class, 'show']);
    Route::put('/client', [ClientController::class, 'update']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'pay']);
    Route::get('/invoices/{invoice}/payment-status', [PaymentController::class, 'status']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('/tickets/{ticket}/messages', [TicketController::class, 'reply']);

    Route::get('/work-orders', [WorkOrderController::class, 'index']);
    Route::get('/work-orders/today', [WorkOrderController::class, 'today']);
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show']);
    Route::post('/work-orders/{workOrder}/start', [WorkOrderController::class, 'start']);
    Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);
    Route::post('/work-orders/{workOrder}/photo', [WorkOrderController::class, 'uploadPhoto']);
    Route::post('/work-orders/{workOrder}/signature', [WorkOrderController::class, 'uploadSignature']);

    Route::get('/tech/inventory', [ApiTechInventoryController::class, 'index']);
    Route::post('/tech/inventory/checkout', [ApiTechInventoryController::class, 'checkout']);
    Route::post('/tech/inventory/{techInventory}/checkin', [ApiTechInventoryController::class, 'checkin']);

    Route::get('/equipment/{serial}', [ApiTechInventoryController::class, 'lookup']);
    Route::post('/equipment/{serial}/assign', [ApiTechInventoryController::class, 'assign']);
});
