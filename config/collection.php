<?php

return [

    'defaults' => [
        'email' => "Olá {nome},\n\nSua fatura {fatura} no valor de {valor} venceu há {dias_atraso} dia(s).\nRegularize pelo link: {link_pagamento}\n\nAtenciosamente,\nSisProv",
        'whatsapp' => "Olá {nome}! Sua fatura {fatura} de {valor} está vencida há {dias_atraso} dia(s). Evite bloqueio, pague agora!",
        'sms' => "Fatura {fatura} vencida - {valor}. Regularize para evitar bloqueio.",
        'system' => "Fatura {fatura} de {valor} está vencida.",
    ],

    'rules' => [
        [
            'days' => 0,
            'action' => 'warning',
            'label' => 'Aviso de vencimento',
            'channels' => ['email', 'system'],
            'templates' => [
                'email_body' => 'Olá {nome}, sua fatura {fatura} de {valor} vence hoje. Não deixe para depois! {link_pagamento}',
                'whatsapp_body' => 'Oi {nome}! Lembrete: fatura {fatura} de {valor} vence hoje.',
                'system_body' => 'Sua fatura {fatura} de {valor} vence hoje.',
            ],
        ],
        [
            'days' => 1,
            'action' => 'notification',
            'label' => '1º aviso D+1',
            'channels' => ['email', 'whatsapp', 'system'],
            'templates' => [
                'whatsapp_body' => 'Oi {nome}! Sua fatura {fatura} de {valor} venceu ontem. Pague agora e evite juros!',
            ],
        ],
        [
            'days' => 3,
            'action' => 'notification',
            'label' => '2º aviso D+3',
            'channels' => ['email', 'whatsapp', 'sms', 'system'],
        ],
        [
            'days' => 5,
            'action' => 'notification',
            'label' => '3º aviso D+5',
            'channels' => ['email', 'whatsapp', 'system'],
            'templates' => [
                'whatsapp_body' => '⚠️ {nome}, fatura {fatura} de {valor} está há 5 dias vencida. Risco de bloqueio!',
            ],
        ],
        [
            'days' => 7,
            'action' => 'partial_block',
            'label' => 'Aviso bloqueio D+7',
            'channels' => ['email', 'whatsapp', 'sms', 'system'],
            'templates' => [
                'email_body' => 'Olá {nome}, sua fatura {fatura} está há {dias_atraso} dias vencida. Seu link será bloqueado em breve. Regularize: {link_pagamento}',
                'whatsapp_body' => '⚠️ ÚLTIMO AVISO {nome}! Fatura {fatura} de {valor} com {dias_atraso} dias. Bloqueio iminente!',
                'sms_body' => 'Aviso final: fatura {fatura} vencida há {dias_atraso} dias. Bloqueio em breve.',
            ],
        ],
    ],

];
