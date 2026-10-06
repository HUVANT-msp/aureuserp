<?php

return [
    'global-search' => [
        'vendor' => 'Fornitore',
        'reference' => 'Riferimento',
        'amount' => 'Importo',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'vendor' => 'Fornitore',
                    'vendor-reference' => 'Riferimento del fornitore',
                    'vendor-reference-tooltip' => 'Il numero di riferimento dell\'ordine di vendita o del preventivo fornito dal fornitore. Viene utilizzato per la riconciliazione al momento della ricezione dei prodotti, poiché questo riferimento è solitamente incluso nella bolla di consegna del fornitore.',
                    'agreement' => 'Accordo',
                    'currency' => 'Valuta',
                    'confirmation-date' => 'Data di conferma',
                    'order-deadline' => 'Scadenza dell\'ordine',
                    'expected-arrival' => 'Arrivo previsto',
                    'confirmed-by-vendor' => 'Confermato dal fornitore',
                    'deliver-to' => 'Consegnare a',
                ],
            ],
        ],
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
                'repeater' => [
                    'products' => [
                        'title' => 'Prodotti',
                        'add-product-line' => 'Aggiungi prodotto',
                        'fields' => [
                            'product' => 'Prodotto',
                            'expected-arrival' => 'Arrivo previsto',
                            'quantity' => 'Quantità',
                            'received' => 'Ricevuto',
                            'billed' => 'Fatturato',
                            'unit' => 'Unità',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Sconto (%)',
                            'unit-price' => 'Prezzo unitario',
                            'amount' => 'Importo',
                        ],
                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Impossibile ridurre l\'importo',
                                'body' => 'La quantità non può essere ridotta al di sotto della quantità ricevuta (:qty).',
                            ],
                            'blanket-order-qty-limit' => [
                                'title' => 'La quantità supera il limite dell\'ordine aperto',
                                'body' => 'La quantità del prodotto (:product_qty) supera la quantità disponibile (:available_qty) dell\'ordine aperto.',
                            ],
                        ],
                        'columns' => [
                            'product' => 'Prodotto',
                            'expected-arrival' => 'Arrivo previsto',
                            'quantity' => 'Quantità',
                            'received' => 'Ricevuto',
                            'billed' => 'Fatturato',
                            'unit' => 'Unità',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Sconto (%)',
                            'unit-price' => 'Prezzo unitario',
                            'amount' => 'Importo',
                        ],
                        'delete-action' => [
                            'error' => [
                                'title' => 'Impossibile eliminare il prodotto',
                                'body' => 'I prodotti non possono essere rimossi da un ordine d\'acquisto confermato.',
                            ],
                        ],
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Prodotto aperto',
                            ],
                        ],
                    ],
                    'section' => [
                        'title' => 'Aggiungi sezione',
                        'fields' => [

                        ],
                    ],
                    'note' => [
                        'title' => 'Aggiungi nota',
                        'fields' => [

                        ],
                    ],
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'buyer' => 'Acquirente',
                    'company' => 'Azienda',
                    'source-document' => 'Documento di origine',
                    'incoterm' => 'Incoterm',
                    'incoterm-tooltip' => 'I termini commerciali internazionali (Incoterms) sono un insieme di termini commerciali standardizzati utilizzati nelle transazioni globali per definire le responsabilità tra acquirenti e venditori.',
                    'incoterm-location' => 'Località Incoterm',
                    'payment-term' => 'Termine di pagamento',
                    'fiscal-position' => 'Regime fiscale',
                ],
            ],
            'terms' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'favorite' => 'Preferito',
            'priority' => 'Priorità',
            'vendor-reference' => 'Riferimento del fornitore',
            'reference' => 'Riferimento',
            'vendor' => 'Fornitore',
            'buyer' => 'Acquirente',
            'company' => 'Azienda',
            'order-deadline' => 'Scadenza dell\'ordine',
            'source-document' => 'Documento di origine',
            'untaxed-amount' => 'Importo senza tasse',
            'total-amount' => 'Importo totale',
            'status' => 'Stato',
            'billing-status' => 'Stato di fatturazione',
            'receipt-status' => 'Stato della ricezione',
            'currency' => 'Valuta',
        ],
        'groups' => [
            'vendor' => 'Fornitore',
            'buyer' => 'Acquirente',
            'state' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'status' => 'Stato',
            'vendor-reference' => 'Riferimento del fornitore',
            'reference' => 'Riferimento',
            'untaxed-amount' => 'Importo senza tasse',
            'total-amount' => 'Importo totale',
            'order-deadline' => 'Scadenza dell\'ordine',
            'vendor' => 'Fornitore',
            'buyer' => 'Acquirente',
            'company' => 'Azienda',
            'payment-term' => 'Termine di pagamento',
            'incoterm' => 'Incoterm',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ordine eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare l\'ordine',
                        'body' => 'L\'ordine non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ordini eliminati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare gli ordini',
                        'body' => 'Gli ordini non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'summary' => [
        'tax' => 'Imposta',
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'purchase-order' => 'Ordine di acquisto',
                    'vendor' => 'Fornitore',
                    'vendor-reference' => 'Riferimento del fornitore',
                    'vendor-reference-tooltip' => 'Il numero di riferimento dell\'ordine di vendita o del preventivo fornito dal fornitore. Viene utilizzato per la riconciliazione al momento della ricezione dei prodotti, poiché questo riferimento è solitamente incluso nella bolla di consegna del fornitore.',
                    'agreement' => 'Accordo',
                    'currency' => 'Valuta',
                    'confirmation-date' => 'Data di conferma',
                    'order-deadline' => 'Scadenza dell\'ordine',
                    'expected-arrival' => 'Arrivo previsto',
                    'confirmed-by-vendor' => 'Confermato dal fornitore',
                ],
            ],
        ],
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
                'repeater' => [
                    'products' => [
                        'title' => 'Prodotti',
                        'add-product-line' => 'Aggiungi prodotto',
                        'entries' => [
                            'product' => 'Prodotto',
                            'expected-arrival' => 'Arrivo previsto',
                            'quantity' => 'Quantità',
                            'received' => 'Ricevuto',
                            'billed' => 'Fatturato',
                            'unit' => 'Unità',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Sconto (%)',
                            'unit-price' => 'Prezzo unitario',
                            'amount' => 'Importo',
                        ],
                    ],
                    'section' => [
                        'title' => 'Aggiungi sezione',
                    ],
                    'note' => [
                        'title' => 'Aggiungi nota',
                    ],
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'buyer' => 'Acquirente',
                    'company' => 'Azienda',
                    'source-document' => 'Documento di origine',
                    'incoterm' => 'Incoterm',
                    'incoterm-tooltip' => 'I termini commerciali internazionali (Incoterms) sono un insieme di termini commerciali standardizzati utilizzati nelle transazioni globali per definire le responsabilità tra acquirenti e venditori.',
                    'incoterm-location' => 'Località Incoterm',
                    'payment-term' => 'Termine di pagamento',
                    'fiscal-position' => 'Regime fiscale',
                ],
            ],
            'terms' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
];
