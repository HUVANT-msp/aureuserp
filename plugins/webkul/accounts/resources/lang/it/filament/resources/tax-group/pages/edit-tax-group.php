<?php

return [
    'notification' => [
        'title' => 'Gruppo fiscale aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Gruppo fiscale eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare il gruppo fiscale',
                    'body' => 'Il gruppo fiscale non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
