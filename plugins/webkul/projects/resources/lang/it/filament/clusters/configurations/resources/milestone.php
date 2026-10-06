<?php

return [
    'navigation' => [
        'title' => 'Milestone',
    ],
    'form' => [
        'name' => 'Nome',
        'deadline' => 'Scadenza',
        'is-completed' => 'Completato',
        'project' => 'Progetto',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'deadline' => 'Scadenza',
            'is-completed' => 'Completato',
            'completed-at' => 'Completato il',
            'project' => 'Progetto',
            'creator' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'is-completed' => 'Completato',
            'project' => 'Progetto',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'is-completed' => 'Completato',
            'project' => 'Progetto',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Traguardo aggiornato',
                    'body' => 'La pietra miliare è stata aggiornata con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Pietra miliare rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Traguardi eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
