<?php

return [
    'notification' => [
        'title' => 'Ordine aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'confirm' => [
            'label' => 'Conferma',
        ],
        'close' => [
            'label' => 'Chiudi',
        ],
        'cancel' => [
            'label' => 'Annulla',
        ],
        'print' => [
            'label' => 'Stampa',
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Ordine eliminato',
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
