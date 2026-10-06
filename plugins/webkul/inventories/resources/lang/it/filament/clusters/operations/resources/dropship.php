<?php

return [
    'navigation' => [
        'title' => 'Spedizioni dirette',
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
                        'title' => 'Drop shipping rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il dropship',
                        'body' => 'Il drop shipping non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropshipping rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i dropship',
                        'body' => 'I dropship non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
];
