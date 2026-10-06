<?php

return [
    'title' => 'Motivi del ritiro',
    'navigation' => [
        'title' => 'Motivi del ritiro',
        'group' => 'Collaboratore',
    ],
    'groups' => [
        'status' => 'Stato',
        'created-by' => 'Creato da',
        'created-at' => 'Data creazione',
        'updated-at' => 'Ultima modifica',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'employee' => 'Collaboratore',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo del ritiro aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Motivo del ritiro rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati i motivi del recesso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo dell\'annullamento creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
    ],
];
