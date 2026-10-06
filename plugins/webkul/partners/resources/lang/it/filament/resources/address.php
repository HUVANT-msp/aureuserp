<?php

return [
    'form' => [
        'partner' => 'Partner',
        'name' => 'Nome',
        'email' => 'Email',
        'phone' => 'Telefono',
        'mobile' => 'Cellulare',
        'type' => 'Tipo',
        'address' => 'Indirizzo',
        'city' => 'Città',
        'street1' => 'Via 1',
        'street2' => 'Via 2',
        'state' => 'Stato',
        'zip' => 'Codice postale',
        'code' => 'Codice',
        'country' => 'Paese',
    ],
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi indirizzo',
                'notification' => [
                    'title' => 'Indirizzo creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'columns' => [
            'type' => 'Tipo',
            'name' => 'Nome del contatto',
            'address' => 'Indirizzo',
            'city' => 'Città',
            'street1' => 'Via 1',
            'street2' => 'Via 2',
            'state' => 'Stato',
            'zip' => 'Codice postale',
            'country' => 'Paese',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Indirizzo aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Indirizzo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Indirizzi cancellati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
