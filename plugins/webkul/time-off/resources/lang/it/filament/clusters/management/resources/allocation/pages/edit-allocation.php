<?php

return [
    'notification' => [
        'title' => 'Assegnazione aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Compito eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
        'approved' => [
            'title' => 'Approvato',
            'notification' => [
                'title' => 'Incarico approvato',
                'body' => 'L\'incarico è stato approvato con successo.',
            ],
        ],
        'refuse' => [
            'title' => 'Rifiuta',
            'notification' => [
                'title' => 'Incarico rifiutato',
                'body' => 'L\'incarico è stato rifiutato con successo.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Segna come pronto per confermare',
            'notification' => [
                'title' => 'Contrassegnato come pronto per la conferma',
                'body' => 'L\'assegnazione è stata contrassegnata come pronta per essere eseguita correttamente.',
            ],
        ],
    ],
];
