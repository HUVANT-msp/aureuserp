<?php

return [
    'form' => [
        'fields' => [
            'color' => 'Colore',
            'country' => 'Paese',
            'applicability' => 'Applicabilità',
            'name' => 'Nome',
            'status' => 'Stato',
            'tax-negate' => 'Negare le tasse',
        ],
    ],
    'table' => [
        'columns' => [
            'color' => 'Colore',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'applicability' => 'Applicabilità',
            'name' => 'Nome',
            'status' => 'Stato',
            'tax-negate' => 'Negare le tasse',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'deleted-at' => 'Eliminato il',
        ],
        'filters' => [
            'bank' => 'Banca',
            'account-holder' => 'Titolare del conto',
            'creator' => 'Creato da',
            'can-send-money' => 'Puoi inviare denaro',
        ],
        'groups' => [
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'applicability' => 'Applicabilità',
            'name' => 'Nome',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etichetta dell\'account aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tag dell\'account eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tag dell\'account rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'color' => 'Colore',
            'country' => 'Paese',
            'applicability' => 'Applicabilità',
            'name' => 'Nome',
            'status' => 'Stato',
            'tax-negate' => 'Negare le tasse',
        ],
    ],
];
