<?php

return [
    'navigation' => [
        'title' => 'Piani di attività',
    ],
    'form' => [
        'name' => 'Nome',
        'status' => 'Stato',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Piano di attività ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Piano di attività eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Piano di attività eliminato definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Piani di attività ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Piani di attività eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Piani di attività eliminati definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Piano di attività creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
        'status' => 'Stato',
    ],
];
