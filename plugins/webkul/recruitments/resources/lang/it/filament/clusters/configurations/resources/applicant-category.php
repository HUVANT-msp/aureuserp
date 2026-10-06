<?php

return [
    'title' => 'Etichetta',
    'navigation' => [
        'title' => 'Etichetta',
        'group' => 'Candidature',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Immettere il nome delle etichette',
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
            'employee' => 'Collaboratore',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etichette aggiornate',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tag rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorie di candidati rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tag creati',
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
