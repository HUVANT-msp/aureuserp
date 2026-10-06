<?php

return [
    'navigation' => [
        'title' => 'Spedizioni',
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
                        'title' => 'Consegna rimossa',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la consegna',
                        'body' => 'La consegna non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Consegne cancellate',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le consegne',
                        'body' => 'Le consegne non possono essere eliminate perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
];
