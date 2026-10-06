<?php

return [
    'title' => 'Vedi valuta',
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
