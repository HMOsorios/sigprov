<?php

return [

    'default' => env('PAYMENT_GATEWAY', 'local'),

    'local' => [
        'class' => null,
    ],

    'asaas' => [
        'class' => App\Services\Gateways\AsaasGateway::class,
        'api_key' => env('ASAAS_API_KEY'),
        'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('ASAAS_WEBHOOK_SECRET'),
    ],

    'mercadopago' => [
        'class' => App\Services\Gateways\MercadoPagoGateway::class,
        'access_token' => env('MERCADO_PAGO_ACCESS_TOKEN'),
        'environment' => env('MERCADO_PAGO_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('MERCADO_PAGO_WEBHOOK_SECRET'),
    ],

];
