<?php

return [
    'navigation' => [
        'title' => 'Piani di attività',
        'group' => 'Attività',
    ],
    'global-search' => [
        'name' => 'Reparto',
        'department' => 'Reparto',
        'manager' => 'Responsabile',
        'company' => 'Azienda',
        'plugin' => 'Modulo',
        'creator-name' => 'Creato da',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'department' => 'Reparto',
            'company' => 'Azienda',
            'manager' => 'Responsabile',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'plugin' => 'Modulo',
            'activity-types' => 'Tipi di attività',
            'company' => 'Azienda',
            'department' => 'Reparto',
            'is-active' => 'Stato',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'status' => 'Stato',
            'name' => 'Nome',
            'created-by' => 'Creato da',
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
];
