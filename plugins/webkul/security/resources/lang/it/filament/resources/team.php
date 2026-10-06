<?php

return [
    'title' => 'Team',
    'navigation' => [
        'title' => 'Team',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'created-by' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attrezzatura aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Squadra eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Squadre create',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
        ],
    ],
];
