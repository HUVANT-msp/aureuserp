<?php

return [
    'title' => 'Motivo del rifiuto',
    'navigation' => [
        'title' => 'Motivi del rifiuto',
        'group' => 'Candidature',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'template' => [
                'title' => 'Modello',
                'applicant-refuse' => 'Candidato rifiutato',
                'applicant-not-interested' => 'Candidato disinteressato',
            ],
            'name-placeholder' => 'Inserisci il nome del motivo del rifiuto',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'template' => 'Modello',
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
                    'title' => 'Motivo aggiornato del rifiuto',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Motivo del rifiuto rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati i motivi di rigetto',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo del rifiuto creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
        'template' => 'Modello',
    ],
];
