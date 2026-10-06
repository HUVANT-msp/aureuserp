<?php

return [
    'form' => [
        'name' => 'Nome',
        'level' => 'Livello',
        'default-level' => 'Livello predefinito',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'level' => 'Livello',
            'default-level' => 'Livello predefinito',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'deleted-records' => 'Record eliminati',
        ],
        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Livello di competizione creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
            'edit' => [
                'notification' => [
                    'title' => 'Livello di competenza aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Livello di competenza ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Livello di competizione rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Livelli di competizione rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Livelli di competenza rimossi permanentemente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Livelli di competenza ripristinati',
                    'body' => 'Le abilità sono state ripristinate con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'level' => 'Livello',
            'default-level' => 'Livello predefinito',
        ],
    ],
];
