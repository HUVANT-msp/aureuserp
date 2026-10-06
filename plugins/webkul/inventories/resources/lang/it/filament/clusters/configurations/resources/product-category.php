<?php

return [
    'navigation' => [
        'title' => 'Categorie',
        'group' => 'Prodotti',
    ],
    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Magazzino',
                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistica',
                        'fields' => [
                            'routes' => 'Rotte di movimentazione',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Magazzino',
                'subsections' => [
                    'logistics' => [
                        'title' => 'Logistica',
                        'entries' => [
                            'routes' => 'Percorsi di magazzino',
                            'route_name' => 'Nome del percorso',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
