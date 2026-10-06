<?php

return [
    'navigation' => [
        'title' => 'Scarti',
        'group' => 'Impostazioni',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'product' => 'Prodotto',
                    'package' => 'Collo',
                    'quantity' => 'Quantità',
                    'unit' => 'Unità di misura',
                    'lot' => 'Lotto/serie',
                    'tags' => 'Etichetta',
                    'name' => 'Nome',
                    'color' => 'Colore',
                    'owner' => 'Proprietario',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Posizione del ritiro',
                    'source-document' => 'Documento di origine',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'date' => 'Data',
            'reference' => 'Riferimento',
            'product' => 'Prodotto',
            'package' => 'Collo',
            'quantity' => 'Quantità',
            'uom' => 'Unità di misura',
            'source-location' => 'Posizione di origine',
            'scrap-location' => 'Posizione del ritiro',
            'unit' => 'Unità di misura',
            'lot' => 'Lotto/serie',
            'tags' => 'Etichetta',
            'state' => 'Stato',
        ],
        'groups' => [
            'product' => 'Prodotto',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Posizione del ritiro',
        ],
        'filters' => [
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Posizione del ritiro',
            'product' => 'Prodotto',
            'state' => 'Stato',
            'product-category' => 'Categoria prodotto',
            'uom' => 'Unità di misura',
            'lot' => 'Lotto/serie',
            'package' => 'Collo',
            'tags' => 'Etichetta',
            'company' => 'Azienda',
            'quantity' => 'Quantità',
            'creator' => 'Creato da',
            'closed-at' => 'Chiuso',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Eliminazione degli sprechi',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'I rifiuti non potevano essere eliminati',
                        'body' => 'Il restringimento non può essere eliminato perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rifiuti eliminati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Le perdite non potevano essere eliminate',
                        'body' => 'Il restringimento non può essere eliminato perché è in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Dettagli di restringimento',
                'entries' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'lot' => 'Lotto',
                    'tags' => 'Etichetta',
                    'package' => 'Collo',
                    'owner' => 'Proprietario',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Posizione del ritiro',
                    'source-document' => 'Documento di origine',
                    'company' => 'Azienda',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-by' => 'Creato da',
                    'created-at' => 'Data creazione',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
        ],
    ],
];
