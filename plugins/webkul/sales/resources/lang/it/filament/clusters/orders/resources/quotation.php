<?php

return [
    'title' => 'Preventivo',
    'navigation' => [
        'title' => 'Preventivi',
    ],
    'global-search' => [
        'customer' => 'Cliente',
        'reference' => 'Riferimento',
        'amount' => 'Importo',
    ],
    'form' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'customer' => 'Cliente',
                    'expiration' => 'Maturità',
                    'quotation-date' => 'Data di bilancio',
                    'order-date' => 'Data dell\'ordine',
                    'payment-term' => 'Termine di pagamento',
                ],
            ],
        ],
        'tabs' => [
            'order-line' => [
                'title' => 'Riga dell\'ordine',
                'repeater' => [
                    'products' => [
                        'title' => 'Prodotti',
                        'add-product' => 'Aggiungi prodotto',
                        'columns' => [
                            'product' => 'Prodotto',
                            'product-variants' => 'Varianti prodotto',
                            'product-simple' => 'Prodotto semplice',
                            'quantity' => 'Quantità',
                            'insufficient-stock-tooltip' => 'Scorte insufficienti per soddisfare questa domanda.',
                            'uom' => 'UoM',
                            'lead-time' => 'Tempi di consegna',
                            'qty-delivered' => 'Consegnato',
                            'qty-invoiced' => 'Fatturato',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'unit-price' => 'Prezzo unitario',
                            'cost' => 'Costo',
                            'margin' => 'Margine',
                            'taxes' => 'Imposte',
                            'amount' => 'Importo',
                            'margin-percentage' => 'Margine (%)',
                            'discount-percentage' => 'Sconto (%)',
                        ],
                        'fields' => [
                            'product' => 'Prodotto',
                            'product-variants' => 'Varianti prodotto',
                            'product-simple' => 'Prodotto semplice',
                            'quantity' => 'Quantità',
                            'uom' => 'Unità di misura',
                            'lead-time' => 'Tempi di consegna',
                            'qty-delivered' => 'Quantità consegnata',
                            'qty-invoiced' => 'Importo fatturato',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'unit-price' => 'Prezzo unitario',
                            'cost' => 'Costo',
                            'margin' => 'Margine',
                            'taxes' => 'Imposte',
                            'amount' => 'Importo',
                            'margin-percentage' => 'Margine (%)',
                            'discount-percentage' => 'Sconto (%)',
                        ],
                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Impossibile ridurre l\'importo',
                                'body' => 'La quantità non può essere ridotta al di sotto della quantità consegnata (:qty).',
                            ],
                        ],
                        'delete-action' => [
                            'error' => [
                                'title' => 'Impossibile eliminare il prodotto',
                                'body' => 'I prodotti non possono essere rimossi da un ordine di vendita confermato.',
                            ],
                        ],
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Prodotto aperto',
                            ],
                        ],
                    ],
                    'product-optional' => [
                        'title' => 'Prodotti opzionali',
                        'add-product' => 'Aggiungi prodotto',
                        'columns' => [
                            'product' => 'Prodotto',
                            'description' => 'Descrizione',
                            'quantity' => 'Quantità',
                            'uom' => 'Unità di misura',
                            'unit-price' => 'Prezzo unitario',
                            'discount-percentage' => 'Sconto (%)',
                        ],
                        'fields' => [
                            'product' => 'Prodotto',
                            'description' => 'Descrizione',
                            'quantity' => 'Quantità',
                            'uom' => 'Unità di misura',
                            'unit-price' => 'Prezzo unitario',
                            'discount-percentage' => 'Sconto (%)',
                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Aggiungi riga d\'ordine',
                                    'already-added' => 'Già aggiunto all\'ordine',
                                ],
                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Prodotto aggiunto',
                                        'body' => 'Il prodotto è stato aggiunto con successo.',
                                    ],
                                    'product-not-found' => [
                                        'title' => 'Prodotto non trovato',
                                    ],
                                    'product-already-exists' => [
                                        'title' => 'Il prodotto esiste già',
                                        'body' => 'Questo prodotto è già nelle righe dell\'ordine. Aggiorna invece la riga esistente.',
                                    ],
                                    'missing-product-data' => [
                                        'title' => 'Mancano i dati del prodotto',
                                        'body' => 'Il prodotto selezionato non può essere elaborato.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Altre informazioni',
                'fieldset' => [
                    'sales' => [
                        'title' => 'Vendite',
                        'fields' => [
                            'sales-person' => 'Venditore',
                            'customer-reference' => 'Riferimento del cliente',
                            'tags' => 'Etichetta',
                        ],
                    ],
                    'shipping' => [
                        'title' => 'Spedizione',
                        'fields' => [
                            'warehouse' => 'Magazzino',
                            'commitment-date' => 'Data di consegna',
                        ],
                    ],
                    'tracking' => [
                        'title' => 'Monitoraggio',
                        'fields' => [
                            'source-document' => 'Documento di origine',
                            'medium' => 'Medio',
                            'source' => 'Fonte',
                            'campaign' => 'Campagna',
                        ],
                    ],
                    'additional-information' => [
                        'title' => 'Informazioni aggiuntive',
                        'fields' => [
                            'company' => 'Azienda',
                            'currency' => 'Valuta',
                        ],
                    ],
                ],
            ],
            'term-and-conditions' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'number' => 'Numero',
            'status' => 'Stato',
            'delivery-status' => 'Stato di consegna',
            'invoice-status' => 'Stato della fattura',
            'creation-date' => 'Data di creazione',
            'commitment-date' => 'Data dell\'impegno',
            'expected-date' => 'Data prevista',
            'customer' => 'Cliente',
            'sales-person' => 'Venditore',
            'sales-team' => 'Team di vendita',
            'untaxed-amount' => 'Importo senza tasse',
            'amount-tax' => 'Importo fiscale',
            'amount-total' => 'Importo totale',
            'customer-reference' => 'Riferimento del cliente',
        ],
        'summarizers' => [
            'total' => 'Totale',
            'taxes' => 'Imposte',
            'total-amount' => 'Importo totale',
        ],
        'filters' => [
            'sales-person' => 'Venditore',
            'utm-source' => 'Origine dell\'UTM',
            'company' => 'Azienda',
            'customer' => 'Cliente',
            'journal' => 'Sezionale',
            'invoice-address' => 'Indirizzo di fatturazione',
            'shipping-address' => 'Indirizzo di spedizione',
            'fiscal-position' => 'Regime fiscale',
            'payment-term' => 'Termine di pagamento',
            'currency' => 'Valuta',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'medium' => 'Medio',
            'source' => 'Fonte',
            'team' => 'Team',
            'sales-person' => 'Venditore',
            'currency' => 'Valuta',
            'company' => 'Azienda',
            'customer' => 'Cliente',
            'quotation-date' => 'Data di bilancio',
            'commitment-date' => 'Data dell\'impegno',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bilancio ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Budget rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Budget rimosso definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Budget ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Budget eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Budget rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Budget creati',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'sale-order' => 'Ordine di vendita',
                    'customer' => 'Cliente',
                    'expiration' => 'Maturità',
                    'quotation-date' => 'Data di bilancio',
                    'payment-term' => 'Termine di pagamento',
                ],
            ],
        ],
        'tabs' => [
            'order-line' => [
                'title' => 'Riga dell\'ordine',
                'repeater' => [
                    'products' => [
                        'title' => 'Prodotti',
                        'add-product' => 'Aggiungi prodotto',
                        'entries' => [
                            'product' => 'Prodotto',
                            'product-variants' => 'Varianti prodotto',
                            'product-simple' => 'Prodotto semplice',
                            'quantity' => 'Quantità',
                            'qty-delivered' => 'Consegnato',
                            'qty-invoiced' => 'Fatturato',
                            'uom' => 'UoM',
                            'lead-time' => 'Tempi di consegna',
                            'packaging-qty' => 'Quantità di imballaggio',
                            'packaging' => 'Imballaggio',
                            'unit-price' => 'Prezzo unitario',
                            'cost' => 'Costo',
                            'margin' => 'Margine',
                            'taxes' => 'Imposte',
                            'amount' => 'Importo',
                            'margin-percentage' => 'Margine (%)',
                            'discount-percentage' => 'Sconto (%)',
                            'sub-total' => 'Totale parziale',
                        ],
                    ],
                    'product-optional' => [
                        'title' => 'Prodotti opzionali',
                        'add-product' => 'Aggiungi prodotto',
                        'entries' => [
                            'product' => 'Prodotto',
                            'description' => 'Descrizione',
                            'quantity' => 'Quantità',
                            'uom' => 'Unità di misura',
                            'unit-price' => 'Prezzo unitario',
                            'discount-percentage' => 'Sconto (%)',
                            'sub-total' => 'Totale parziale',
                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Aggiungi riga d\'ordine',
                                ],
                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Prodotto aggiunto',
                                        'body' => 'Il prodotto è stato aggiunto con successo.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Altre informazioni',
                'fieldset' => [
                    'sales' => [
                        'title' => 'Vendite',
                        'entries' => [
                            'sales-person' => 'Venditore',
                            'customer-reference' => 'Riferimento del cliente',
                            'tags' => 'Etichetta',
                        ],
                    ],
                    'shipping' => [
                        'title' => 'Spedizione',
                        'entries' => [
                            'commitment-date' => 'Data di consegna',
                        ],
                    ],
                    'tracking' => [
                        'title' => 'Monitoraggio',
                        'entries' => [
                            'source-document' => 'Documento di origine',
                            'medium' => 'Medio',
                            'source' => 'Fonte',
                            'campaign' => 'Campagna',
                        ],
                    ],
                    'additional-information' => [
                        'title' => 'Informazioni aggiuntive',
                        'entries' => [
                            'company' => 'Azienda',
                            'currency' => 'Valuta',
                        ],
                    ],
                ],
            ],
            'term-and-conditions' => [
                'title' => 'Termini e condizioni',
            ],
        ],
    ],
];
