<?php

return [
    'navigation' => [
        'title' => 'Fasi',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'done' => 'Completato',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'done' => 'Completato',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'done' => 'Completato',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Fase aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Fase eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tappe eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome',
                    'done' => 'Completato',
                ],
            ],
        ],
    ],
];
