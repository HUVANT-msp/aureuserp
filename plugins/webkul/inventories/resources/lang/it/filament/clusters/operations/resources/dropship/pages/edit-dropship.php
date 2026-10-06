<?php

return [
    'notification' => [
        'title' => 'Drop Shipping aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
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
];
