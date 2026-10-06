<?php

return [
    'navigation' => [
        'title' => 'Conti bancari',
        'group' => 'Banche',
    ],
    'form' => [
        'account-number' => 'Numero di conto',
        'bank' => [
            'title' => 'Banca',
            'sections' => [
                'general' => [
                    'title' => 'Generale',
                    'fields' => [
                        'name' => 'Nome',
                        'code' => 'Codice identificativo della banca',
                        'email' => 'Email',
                        'phone' => 'Telefono',
                    ],
                ],
                'address' => [
                    'title' => 'Indirizzo',
                    'fields' => [
                        'address' => 'Indirizzo',
                        'city' => 'Città',
                        'street1' => 'Via 1',
                        'street2' => 'Via 2',
                        'state' => 'Stato',
                        'zip' => 'Codice postale',
                        'country' => 'Paese',
                    ],
                ],
            ],
        ],
        'account-holder' => 'Titolare del conto',
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
