<?php

return [
    'title' => 'Etichetta',
    'navigation' => [
        'title' => 'Etichetta',
        'group' => 'Ordini di vendita',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'color' => 'Colore',
        ],
    ],
    'table' => [
        'columns' => [
            'created-by' => 'Creato da',
            'name' => 'Nome',
            'color' => 'Colore',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etichetta del prodotto aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Etichetta del prodotto rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etichetta del prodotto rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'color' => 'Colore',
        ],
    ],
];
