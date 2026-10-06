<?php

return [
    'notification' => [
        'title' => 'Post aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'draft' => [
            'label' => 'Segna come bozza',
            'notification' => [
                'title' => 'Post contrassegnato come bozza',
                'body' => 'Il post è stato contrassegnato correttamente come bozza.',
            ],
        ],
        'publish' => [
            'label' => 'Pubblica',
            'notification' => [
                'title' => 'Post pubblicato',
                'body' => 'Il post è stato pubblicato con successo.',
            ],
        ],
        'delete' => [
            'notification' => [
                'title' => 'Messaggio eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
