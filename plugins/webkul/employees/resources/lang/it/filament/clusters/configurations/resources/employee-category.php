<?php

return [
    'title' => 'Etichetta',
    'navigation' => [
        'title' => 'Etichetta',
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
            'name-placeholder' => 'Inserisci il nome dell\'etichetta',
            'color' => 'Colore',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'color' => 'Colore',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'created-by' => 'Creato da',
            'updated-by' => 'Modificato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Nome',
            'job-position' => 'Ruolo aziendale',
            'color' => 'Colore',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etichetta aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Etichetta rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tag rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Etichetta creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
        'color' => 'Colore',
    ],
];
