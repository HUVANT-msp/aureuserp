<?php

return [
    'title' => 'Modifica valuta',
    'notification' => [
        'title' => 'Valuta aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Valuta rimossa',
                'body' => 'Eliminazione completata con successo.',
                'error' => [
                    'title' => 'Impossibile eliminare la valuta',
                    'body' => 'La valuta non può essere eliminata perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
