<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
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
];
