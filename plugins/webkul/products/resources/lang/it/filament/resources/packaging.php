<?php

return [
    'form' => [
        'name' => 'Nome',
        'barcode' => 'Codice a barre',
        'product' => 'Prodotto',
        'routes' => 'Rotte di movimentazione',
        'qty' => 'Quantità',
        'company' => 'Azienda',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'product' => 'Prodotto',
            'routes' => 'Rotte di movimentazione',
            'qty' => 'Quantità',
            'company' => 'Azienda',
            'barcode' => 'Codice a barre',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'product' => 'Prodotto',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'product' => 'Prodotto',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Confezione aggiornata',
                    'body' => 'La confezione è stata aggiornata correttamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Imballaggio rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile rimuovere l\'imballaggio',
                        'body' => 'L\'imballaggio non può essere rimosso perché in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'print' => [
                'label' => 'Stampa',
            ],
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Imballaggio rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile rimuovere l\'imballaggio',
                        'body' => 'L\'imballaggio non può essere smaltito perché in uso.',
                    ],
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'label' => 'Nuova confezione',
                'notification' => [
                    'title' => 'Confezione creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome del pacchetto',
                    'barcode' => 'Codice a barre',
                    'product' => 'Prodotto',
                    'qty' => 'Quantità',
                ],
            ],
            'organization' => [
                'title' => 'Dettagli dell\'organizzazione',
                'entries' => [
                    'company' => 'Azienda',
                    'creator' => 'Creato da',
                    'created_at' => 'Data creazione',
                    'updated_at' => 'Ultimo aggiornamento su',
                ],
            ],
        ],
    ],
];
