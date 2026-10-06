<?php

return [
    'navigation' => [
        'title' => 'Ricezioni',
        'group' => 'Trasferimenti',
    ],
    'global-search' => [
        'partner' => 'Partner',
        'origin' => 'Origine',
    ],
    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ricezione rimossa',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la ricezione',
                        'body' => 'La ricezione non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ricevimenti cancellati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le ricezioni',
                        'body' => 'Le ricezioni non possono essere cancellate perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
];
