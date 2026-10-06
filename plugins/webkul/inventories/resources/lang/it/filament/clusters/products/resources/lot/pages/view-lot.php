<?php

return [
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
