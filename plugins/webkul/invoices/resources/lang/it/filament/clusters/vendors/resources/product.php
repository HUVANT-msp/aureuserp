<?php

return [
    'navigation' => [
        'title' => 'Prodotti',
        'group' => 'Magazzino',
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
                    'sales' => 'Vendite',
                    'purchase' => 'Acquisto',
                ],
            ],
            'invoice-policy' => [
                'title' => 'Politica di fatturazione',
                'ordered-policy' => 'È possibile fatturare la merce prima che venga consegnata.',
                'delivered-policy' => 'Fattura dopo la consegna, in base alle quantità consegnate, non a quelle ordinate.',
            ],
            'images' => [
                'title' => 'Immagini',
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'type' => 'Tipo',
                    'reference' => 'Riferimento',
                    'barcode' => 'Codice a barre',
                    'category' => 'Categoria',
                    'company' => 'Azienda',
                ],
            ],
            'category-and-tags' => [
                'title' => 'Categoria e tag',
                'fields' => [
                    'category' => 'Categoria',
                    'tags' => 'Etichetta',
                ],
            ],
            'pricing' => [
                'title' => 'Prezzi',
                'fields' => [
                    'price' => 'Prezzo',
                    'cost' => 'Costo',
                ],
            ],
            'additional' => [
                'title' => 'Ulteriori',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'images' => 'Immagini',
            'type' => 'Tipo',
            'reference' => 'Riferimento',
            'responsible' => 'Responsabile',
            'barcode' => 'Codice a barre',
            'category' => 'Categoria',
            'company' => 'Azienda',
            'price' => 'Prezzo',
            'cost' => 'Costo',
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
                    'title' => 'Prodotto rimosso definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
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
                    'title' => 'Prodotti rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
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
                'entries' => [

                ],
                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Monitoraggio',
                        'entries' => [
                            'track-inventory' => 'Monitoraggio dell\'inventario',
                            'track-by' => 'Seguici',
                            'expiration-date' => 'Data di scadenza',
                        ],
                    ],
                    'operation' => [
                        'title' => 'Operazioni',
                        'entries' => [
                            'routes' => 'Rotte di movimentazione',
                        ],
                    ],
                    'logistics' => [
                        'title' => 'Logistica',
                        'entries' => [
                            'responsible' => 'Responsabile',
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                            'sale-delay' => 'Tempi di consegna al cliente (giorni)',
                        ],
                    ],
                    'traceability' => [
                        'title' => 'Tracciabilità',
                        'entries' => [
                            'expiration-date' => 'Data di scadenza (giorni)',
                            'best-before-date' => 'Data di scadenza (giorni)',
                            'removal-date' => 'Data di ritiro (giorni)',
                            'alert-date' => 'Data dell\'avviso (giorni)',
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
