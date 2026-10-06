<?php

return [
    'navigation' => [
        'title' => 'Imballaggi',
        'group' => 'Prodotti',
    ],
    'form' => [
        'package-type' => 'Tipo di collo',
        'routes' => 'Rotte di movimentazione',
    ],
    'table' => [
        'columns' => [
            'package-type' => 'Tipo di collo',
        ],
        'groups' => [
            'package-type' => 'Tipo di collo',
        ],
        'filters' => [
            'package-type' => 'Tipo di collo',
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Tipo di collo',
                ],
            ],
            'routing' => [
                'title' => 'Informazioni sul percorso',
                'entries' => [
                    'routes' => 'Percorsi di magazzino',
                    'route_name' => 'Nome del percorso',
                ],
            ],
        ],
    ],
];
