<?php

return [
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'rounding-precision' => 'Precisione dell\'arrotondamento',
            'rounding-strategy' => 'Strategia di arrotondamento',
            'profit-account' => 'Conto dei profitti',
            'loss-account' => 'Conto perdite',
            'rounding-method' => 'Metodo di arrotondamento',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'rounding-strategy' => 'Strategia di arrotondamento',
            'rounding-method' => 'Metodo di arrotondamento',
            'created-by' => 'Creato da',
            'profit-account' => 'Conto dei profitti',
            'loss-account' => 'Conto perdite',
        ],
        'groups' => [
            'name' => 'Nome',
            'rounding-strategy' => 'Strategia di arrotondamento',
            'rounding-method' => 'Metodo di arrotondamento',
            'created-by' => 'Creato da',
            'profit-account' => 'Conto dei profitti',
            'loss-account' => 'Conto perdite',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Arrotondamento di cassa rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Arrotondamento di cassa rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'rounding-precision' => 'Precisione dell\'arrotondamento',
            'rounding-strategy' => 'Strategia di arrotondamento',
            'profit-account' => 'Conto dei profitti',
            'loss-account' => 'Conto perdite',
            'rounding-method' => 'Metodo di arrotondamento',
        ],
    ],
];
