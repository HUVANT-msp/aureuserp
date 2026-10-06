<?php

return [
    'form' => [
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
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Codice identificativo della banca',
            'country' => 'Paese',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'deleted-at' => 'Eliminato il',
        ],
        'groups' => [
            'country' => 'Paese',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Panca aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Panca restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Banca rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Banca rimossa definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Panchine restaurate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Banche eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Banche rimosse definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
