<?php

return [
    'global-search' => [
        'reference' => 'Riferimento',
        'barcode' => 'Codice a barre',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ad es. Maglietta',
                    'description' => 'Descrizione',
                    'tags' => 'Etichetta',
                ],
            ],
            'images' => [
                'title' => 'Immagini',
            ],
            'inventory' => [
                'title' => 'Magazzino',
                'fields' => [

                ],
                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistica',
                        'fields' => [
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'type' => 'Tipo',
                    'reference' => 'Riferimento',
                    'barcode' => 'Codice a barre',
                    'category' => 'Categoria',
                    'company' => 'Azienda',
                    'company-placeholder' => 'Tutte le aziende',
                ],
            ],
            'pricing' => [
                'title' => 'Prezzi',
                'fields' => [
                    'price' => 'Prezzo',
                    'cost' => 'Costo',
                    'uom-placeholder' => 'UoM',
                ],
            ],
            'additional' => [
                'title' => 'Ulteriori',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'favorite' => 'Preferito',
            'name' => 'Nome',
            'variants' => 'Varianti',
            'images' => 'Immagini',
            'type' => 'Tipo',
            'reference' => 'Riferimento',
            'responsible' => 'Responsabile',
            'barcode' => 'Codice a barre',
            'category' => 'Categoria',
            'company' => 'Azienda',
            'company-placeholder' => 'Tutte le aziende',
            'price' => 'Prezzo',
            'cost' => 'Costo',
            'on-hand' => 'Giacenza disponibile',
            'tags' => 'Etichetta',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'type' => 'Tipo',
            'category' => 'Categoria',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'name' => 'Nome',
            'type' => 'Tipo',
            'reference' => 'Riferimento',
            'barcode' => 'Codice a barre',
            'category' => 'Categoria',
            'company' => 'Azienda',
            'price' => 'Prezzo',
            'cost' => 'Costo',
            'is-favorite' => 'È preferito',
            'weight' => 'Peso',
            'volume' => 'Volume',
            'tags' => 'Etichetta',
            'responsible' => 'Responsabile',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Prodotto ricondizionato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Prodotto eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prodotto rimosso definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il prodotto',
                        'body' => 'Il prodotto non può essere rimosso perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'print' => [
                'label' => 'Stampa etichette',
                'form' => [
                    'fields' => [
                        'quantity' => 'Numero di etichette',
                        'format' => 'Formato',
                        'format-options' => [
                            'dymo' => 'Dymo',
                            '2x7_price' => '2x7 con prezzo',
                            '4x7_price' => '4x7 con prezzo',
                            '4x12' => '4x12',
                            '4x12_price' => '4x12 con prezzo',
                        ],
                    ],
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Prodotti ricondizionati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Prodotti eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prodotti rimossi definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i prodotti',
                        'body' => 'I prodotti non possono essere eliminati perché sono in uso.',
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
                    'name' => 'Nome',
                    'name-placeholder' => 'ad es. Maglietta',
                    'description' => 'Descrizione',
                    'tags' => 'Etichetta',
                ],
            ],
            'images' => [
                'title' => 'Immagini',
                'entries' => [

                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'type' => 'Tipo',
                    'reference' => 'Riferimento',
                    'barcode' => 'Codice a barre',
                    'category' => 'Categoria',
                    'company' => 'Azienda',
                ],
            ],
            'pricing' => [
                'title' => 'Prezzi',
                'entries' => [
                    'price' => 'Prezzo',
                    'cost' => 'Costo',
                ],
            ],
            'inventory' => [
                'title' => 'Magazzino',
                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistica',
                        'entries' => [
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-at' => 'Data creazione',
                    'created-by' => 'Creato da',
                    'updated-at' => 'Ultima modifica',
                ],
            ],
        ],
    ],
];
