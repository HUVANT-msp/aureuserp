<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
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
];
