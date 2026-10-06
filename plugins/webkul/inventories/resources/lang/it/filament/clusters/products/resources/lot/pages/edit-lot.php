<?php

return [
    'notification' => [
        'title' => 'Lotto aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lotto eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare il batch',
                    'body' => 'Il batch non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
