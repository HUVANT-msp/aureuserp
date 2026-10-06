<?php

return [
    'title' => 'Gestire le tasse',
    'form' => [
        'default-taxes' => [
            'label' => 'Tasse predefinite',
            'helper-text' => 'L\'impostazione predefinita verrà applicata ai prodotti se non viene selezionata alcuna imposta',
        ],
        'sales-tax' => [
            'label' => 'Imposta sulle vendite',
        ],
        'purchase-tax' => [
            'label' => 'Imposta sugli acquisti',
        ],
        'prices' => [
            'label' => 'Prezzi',
        ],
        'rounding-method' => [
            'label' => 'Metodo di arrotondamento',
            'helper-text' => 'Metodo utilizzato per arrotondare gli importi delle imposte',
            'options' => [
                'round-per-line' => 'Giro per riga',
                'round-globally' => 'Rotondo a livello globale',
            ],
        ],
        'fiscal-country' => [
            'label' => 'Paese fiscale',
        ],
    ],
];
