<?php

return [
    'navigation' => [
        'title' => 'Contratti di acquisto',
        'group' => 'Acquisto',
    ],
    'global-search' => [
        'vendor' => 'Fornitore',
        'type' => 'Tipo',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'vendor' => 'Fornitore',
                    'valid-from' => 'Valido dal',
                    'valid-to' => 'Valido fino al',
                    'buyer' => 'Acquirente',
                    'reference' => 'Riferimento',
                    'reference-placeholder' => 'pag. per esempio. PO/123',
                    'agreement-type' => 'Tipo di contratto',
                    'company' => 'Azienda',
                    'currency' => 'Valuta',
                ],
            ],
        ],
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
                'columns' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'ordered' => 'Ordine',
                    'uom' => 'Unità di misura',
                    'unit-price' => 'Prezzo unitario',
                ],
                'fields' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'ordered' => 'Ordine',
                    'uom' => 'Unità di misura',
                    'unit-price' => 'Prezzo unitario',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
            ],
            'terms' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'agreement' => 'Accordo',
            'vendor' => 'Fornitore',
            'agreement-type' => 'Tipo di contratto',
            'buyer' => 'Acquirente',
            'company' => 'Azienda',
            'valid-from' => 'Valido dal',
            'valid-to' => 'Valido fino al',
            'reference' => 'Riferimento',
            'status' => 'Stato',
        ],
        'groups' => [
            'agreement-type' => 'Tipo di contratto',
            'vendor' => 'Fornitore',
            'state' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'agreement' => 'Accordo',
            'vendor' => 'Fornitore',
            'agreement-type' => 'Tipo di contratto',
            'buyer' => 'Acquirente',
            'company' => 'Azienda',
            'valid-from' => 'Valido dal',
            'valid-to' => 'Valido fino al',
            'reference' => 'Riferimento',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Contratto di acquisto rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Contratto di acquisto ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contratto di acquisto rimosso definitivamente',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il contratto di acquisto',
                        'body' => 'Impossibile eliminare il contratto di acquisto poiché è attualmente in uso.',
                    ],
                    'warning' => [
                        'title' => 'Impossibile eliminare il contratto di acquisto',
                        'body' => 'È possibile eliminare solo gli accordi di acquisto con stato Bozza o Annullato.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Contratti di acquisto rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Accordi di acquisto ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contratti di acquisto rimossi definitivamente',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i contratti di acquisto',
                        'body' => 'I contratti di acquisto non possono essere eliminati perché sono attualmente in uso.',
                    ],
                    'warning' => [
                        'title' => 'Impossibile eliminare il contratto di acquisto',
                        'body' => 'È possibile eliminare solo gli accordi di acquisto con stato Bozza o Annullato.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'vendor' => 'Fornitore',
                    'valid-from' => 'Valido dal',
                    'valid-to' => 'Valido fino al',
                    'buyer' => 'Acquirente',
                    'reference' => 'Riferimento',
                    'reference-placeholder' => 'pag. per esempio. PO/123',
                    'agreement-type' => 'Tipo di contratto',
                    'company' => 'Azienda',
                    'currency' => 'Valuta',
                ],
            ],
            'metadata' => [
                'title' => 'Metadati',
                'entries' => [
                    'created-at' => 'Data creazione',
                    'created-by' => 'Creato da',
                    'updated-at' => 'Ultima modifica',
                ],
            ],
        ],
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
                'entries' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'ordered' => 'Ordine',
                    'uom' => 'Unità di misura',
                    'unit-price' => 'Prezzo unitario',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
            ],
            'terms' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
];
