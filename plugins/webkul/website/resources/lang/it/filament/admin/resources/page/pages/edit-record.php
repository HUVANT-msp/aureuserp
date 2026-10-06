<?php

return [
    'notification' => [
        'title' => 'Pagina aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'draft' => [
            'label' => 'Segna come bozza',
            'notification' => [
                'title' => 'Pagina contrassegnata come bozza',
                'body' => 'La pagina è stata contrassegnata correttamente come bozza.',
            ],
        ],
        'publish' => [
            'label' => 'Pubblica',
            'notification' => [
                'title' => 'Pagina pubblicata',
                'body' => 'La pagina è stata pubblicata con successo.',
            ],
        ],
        'delete' => [
            'notification' => [
                'title' => 'Pagina eliminata',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
