<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Codice',
            'name' => 'Nome',
        ],
    ],
    'table' => [
        'columns' => [
            'code' => 'Codice',
            'name' => 'Nome',
            'created-by' => 'Creato da',
        ],
        'groups' => [
            'code' => 'Codice',
            'name' => 'Nome',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Incoterm rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Incoterm ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ripristinati gli Incoterms',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati gli Incoterms',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'code' => 'Codice',
        ],
    ],
];
