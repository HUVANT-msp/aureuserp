<?php

return [
    'form' => [
        'name' => 'Nome',
        'full-name' => 'Nome completo',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'full-name' => 'Nome completo',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Settore aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Settore restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Settore eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Settore rimosso definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Settori restaurati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Settori eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Settori rimossi permanentemente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
