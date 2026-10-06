<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Ordine cancellato',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare l\'ordine',
                    'body' => 'L\'ordine non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
