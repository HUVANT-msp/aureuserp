<?php

return [
    'title' => 'Tipi di competizione',
    'navigation' => [
        'title' => 'Tipi di competizione',
        'group' => 'Collaboratore',
    ],
    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Nome',
                'name-placeholder' => 'Inserisci il nome del tipo di competizione',
                'color' => 'Colore',
                'status' => 'Stato',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Tipo di competizione',
            'status' => 'Stato',
            'color' => 'Colore',
            'skills' => 'Competenze',
            'levels' => 'Livelli',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'skill-levels' => 'Livelli di competizione',
            'skills' => 'Competenze',
            'created-by' => 'Creato da',
            'status' => 'Stato',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Tipo di competizione',
            'color' => 'Colore',
            'status' => 'Stato',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo di competenza ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di competizione rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipi di competizione ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipi di competizione rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Tipi di competizione rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipi di competizione',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Tipo di competizione',
                'color' => 'Colore',
                'status' => 'Stato',
            ],
        ],
    ],
];
