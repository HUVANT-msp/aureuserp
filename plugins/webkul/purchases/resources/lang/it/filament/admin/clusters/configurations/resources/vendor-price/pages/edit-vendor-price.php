<?php

return [
    'navigation' => [
        'title' => 'Modifica listino prezzi fornitori',
    ],
    'notification' => [
        'title' => 'Prezzo fornitore aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Prezzo del fornitore rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare il prezzo del fornitore',
                    'body' => 'Il prezzo del fornitore non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
