<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ad es. Lampade',
                    'parent' => 'In alto',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'full-name' => 'Nome completo',
            'parent-path' => 'Itinerario migliore',
            'parent' => 'In alto',
            'creator' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'parent' => 'In alto',
            'creator' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'parent' => 'In alto',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoria rimossa',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la categoria',
                        'body' => 'La categoria non può essere eliminata perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categorie rimosse',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le categorie',
                        'body' => 'Le categorie non possono essere eliminate perché sono in uso.',
                    ],
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
                    'parent' => 'Categoria superiore',
                    'full_name' => 'Nome completo della categoria',
                    'parent_path' => 'Percorso delle categorie',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'creator' => 'Creato da',
                    'created_at' => 'Data creazione',
                    'updated_at' => 'Ultimo aggiornamento su',
                ],
            ],
        ],
    ],
];
