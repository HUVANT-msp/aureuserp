<?php

return [
    'form' => [
        'name' => 'Nome',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
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
            'edit' => [
                'notification' => [
                    'title' => 'Concorrenza aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Concorrenza ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminata la concorrenza',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Concorsi rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Concorsi rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Competenze ripristinate',
                    'body' => 'Le abilità sono state ripristinate con successo.',
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
