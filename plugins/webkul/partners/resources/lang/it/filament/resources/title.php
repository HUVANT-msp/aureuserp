<?php

return [
    'form' => [
        'name' => 'Nome',
        'short-name' => 'Nome breve',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'short-name' => 'Nome breve',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'creator' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Trattamento aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Trattamento rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Trattamenti eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
