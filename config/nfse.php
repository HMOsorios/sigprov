<?php

return [

    'ambiente' => env('NFSE_AMBIENTE', 'homologacao'),

    'modelo' => env('NFSE_MODELO', '21'),

    'serie' => env('NFSE_SERIE', '1'),

    'certificado' => env('NFSE_CERTIFICADO'),

    'senha_certificado' => env('NFSE_CERTIFICADO_SENHA'),

    'municipio' => env('NFSE_MUNICIPIO_IBGE'),

    'inscricao_municipal' => env('NFSE_INSCRICAO_MUNICIPAL'),

    'regime_tributario' => env('NFSE_REGIME_TRIBUTARIO', '6'),

    'urls' => [
        'homologacao' => env('NFSE_URL_HOMOLOGACAO', 'https://homologacao.nfse.gov.br'),
        'producao' => env('NFSE_URL_PRODUCAO', 'https://producao.nfse.gov.br'),
    ],

    'emitir_automaticamente' => env('NFSE_EMITIR_AUTOMATICO', false),

];
