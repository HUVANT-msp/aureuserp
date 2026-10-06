<?php

return [
    'notification' => [
        'title' => 'Categoria aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Categoria rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare la categoria',
                    'body' => 'La categoria non può essere eliminata perché è in uso.',
                ],
            ],
        ],
    ],
    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Errore durante l\'aggiornamento della categoria',
            ],
        ],
    ],
];
