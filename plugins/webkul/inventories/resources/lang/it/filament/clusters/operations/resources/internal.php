<?php

return [
    'navigation' => [
        'title' => 'Trasferimenti interni',
        'group' => 'Trasferimenti',
    ],
    'global-search' => [
        'origin' => 'Origine',
    ],
    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Trasferimento interno rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il trasferimento interno',
                        'body' => 'Il trasferimento interno non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Trasferimenti interni rimossi',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i trasferimenti interni',
                        'body' => 'I trasferimenti interni non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
];
