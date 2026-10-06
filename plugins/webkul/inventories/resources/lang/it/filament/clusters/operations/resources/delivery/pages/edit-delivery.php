<?php

return [
    'notification' => [
        'title' => 'Consegna aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
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
