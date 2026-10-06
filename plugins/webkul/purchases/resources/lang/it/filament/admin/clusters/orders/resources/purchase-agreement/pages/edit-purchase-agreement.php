<?php

return [
    'notification' => [
        'title' => 'Contratto di acquisto aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'confirm' => [
            'label' => 'Conferma',
            'notification' => [
                'unable' => [
                    'title' => 'Il contratto di acquisto non può essere confermato',
                    'body' => 'Aggiungi almeno una linea di prodotti prima di confermare questo contratto di acquisto.',
                ],
            ],
        ],
        'close' => [
            'label' => 'Chiudi',
            'notification' => [
                'warning' => [
                    'title' => 'Il contratto di acquisto non può essere chiuso',
                    'body' => 'Questo contratto di acquisto non può essere chiuso perché alcune richieste di preventivo correlate non sono nello stato Completato o Annullato.',
                ],
            ],
        ],
        'cancel' => [
            'label' => 'Annulla',
        ],
        'print' => [
            'label' => 'Stampa',
        ],
        'delete' => [
            'notification' => [
                'title' => 'Contratto di acquisto rimosso',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
