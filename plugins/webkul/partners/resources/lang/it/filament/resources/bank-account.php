<?php

return [
    'navigation' => [
        'group' => 'Banche',
        'title' => 'Conti bancari',
    ],
    'form' => [
        'account-number' => 'Numero di conto',
        'bank' => 'Banca',
        'account-holder' => 'Titolare del conto',
        'can-send-money' => 'Puoi inviare denaro',
    ],
    'table' => [
        'columns' => [
            'account-number' => 'Numero di conto',
            'bank' => 'Banca',
            'account-holder' => 'Titolare del conto',
            'send-money' => 'Puoi inviare denaro',
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
            'bank' => 'Banca',
            'can-send-money' => 'Puoi inviare denaro',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Conto bancario aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Conto bancario ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Conto bancario cancellato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Conto bancario eliminato definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Conti bancari ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Conti bancari cancellati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Conti bancari cancellati definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
