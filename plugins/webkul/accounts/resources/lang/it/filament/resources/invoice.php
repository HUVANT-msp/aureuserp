<?php

return [
    'title' => 'Fattura',
    'navigation' => [
        'title' => 'Fatture',
        'group' => 'Fatture',
    ],
    'global-search' => [
        'customer' => 'Cliente',
        'date' => 'Data',
        'due-date' => 'Data di scadenza',
    ],
    'form' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'customer-invoice' => 'Fattura di vendita',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data della fattura',
                    'due-date' => 'Data di scadenza',
                    'payment-term' => 'Termine di pagamento',
                    'journal' => 'Sezionale',
                    'currency' => 'Valuta',
                ],
            ],
        ],
        'tabs' => [
            'invoice-lines' => [
                'title' => 'Righe della fattura',
                'repeater' => [
                    'products' => [
                        'title' => 'Prodotti',
                        'add-product' => 'Aggiungi prodotto',
                        'columns' => [
                            'product' => 'Prodotto',
                            'quantity' => 'Quantità',
                            'unit' => 'Unità',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Sconto',
                            'unit-price' => 'Prezzo unitario',
                            'sub-total' => 'Totale parziale',
                        ],
                        'fields' => [
                            'product' => 'Prodotto',
                            'quantity' => 'Quantità',
                            'unit' => 'Unità',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Percentuale di sconto',
                            'unit-price' => 'Prezzo unitario',
                            'sub-total' => 'Totale parziale',
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Altre informazioni',
                'fieldset' => [
                    'invoice' => [
                        'title' => 'Fattura',
                        'fields' => [
                            'customer-reference' => 'Riferimento del cliente',
                            'sales-person' => 'Venditore',
                            'payment-reference' => 'Riferimento al pagamento',
                            'recipient-bank' => 'La banca del destinatario',
                            'delivery-date' => 'Data di consegna',
                        ],
                    ],
                    'accounting' => [
                        'title' => 'Contabilità',
                        'fields' => [
                            'company' => 'Azienda',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                            'fiscal-position' => 'Regime fiscale',
                            'fiscal-position-tooltip' => 'Gli elementi fiscali vengono utilizzati per personalizzare le imposte e i conti in base alla posizione del cliente.',
                            'cash-rounding' => 'Metodo di arrotondamento del contante',
                            'cash-rounding-tooltip' => 'Specifica l\'unità di valuta più piccola che può essere pagata in contanti.',
                            'payment-method' => 'Metodo di pagamento',
                            'auto-post' => 'Pubblicazione automatica',
                            'checked' => 'Verificato',
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
        'total' => 'Totale',
        'columns' => [
            'number' => 'Numero',
            'state' => 'Stato',
            'created-by' => 'Creato da',
            'customer' => 'Cliente',
            'invoice-date' => 'Data della fattura',
            'checked' => 'Verificato',
            'accounting-date' => 'Contabilità',
            'due-date' => 'Data di scadenza',
            'source-document' => 'Documento di origine',
            'reference' => 'Riferimento',
            'sales-person' => 'Venditore',
            'tax-excluded' => 'Tasse escluse',
            'tax' => 'Imposta',
            'total' => 'Totale',
            'amount-due' => 'Importo in sospeso',
            'invoice-currency' => 'Valuta della fattura',
        ],
        'summarizers' => [
            'total' => 'Totale',
        ],
        'groups' => [
            'name' => 'Nome',
            'invoice-partner-display-name' => 'Nome del contatto per la fatturazione',
            'invoice-date' => 'Data della fattura',
            'checked' => 'Verificato',
            'date' => 'Data',
            'invoice-due-date' => 'Data di scadenza della fattura',
            'invoice-origin' => 'Origine della fattura',
            'sales-person' => 'Venditore',
            'currency' => 'Valuta',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'number' => 'Numero',
            'invoice-partner-display-name' => 'Nome del contatto per la fatturazione',
            'invoice-date' => 'Data della fattura',
            'invoice-due-date' => 'Data di scadenza della fattura',
            'invoice-origin' => 'Origine della fattura',
            'reference' => 'Riferimento',
            'payment-reference' => 'Riferimento al pagamento',
            'narration' => 'Annotazione',
            'partner' => 'Partner',
            'journal' => 'Sezionale',
            'fiscal-position' => 'Regime fiscale',
            'currency' => 'Valuta',
            'company' => 'Azienda',
            'date' => 'Data contabile',
            'delivery-date' => 'Data di consegna',
            'amount-untaxed' => 'Importo senza tasse',
            'amount-tax' => 'Importo fiscale',
            'amount-total' => 'Importo totale',
            'amount-residual' => 'Importo in sospeso',
            'checked' => 'Verificato',
            'posted-before' => 'Pubblicato prima',
            'is-move-sent' => 'Inviato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagamento rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagamenti rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Esporta',
            ],
        ],
    ],
    'infolist' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'customer-invoice' => 'Fattura di vendita',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data della fattura',
                    'due-date' => 'Data di scadenza',
                    'payment-term' => 'Termine di pagamento',
                    'journal' => 'Sezionale',
                    'currency' => 'Valuta',
                ],
            ],
        ],
        'tabs' => [
            'invoice-lines' => [
                'title' => 'Righe della fattura',
                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product' => 'Prodotto',
                            'quantity' => 'Quantità',
                            'unit' => 'Unità di misura',
                            'taxes' => 'Imposte',
                            'discount-percentage' => 'Percentuale di sconto',
                            'unit-price' => 'Prezzo unitario',
                            'sub-total' => 'Totale parziale',
                            'total' => 'Totale',
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Altre informazioni',
                'fieldset' => [
                    'invoice' => [
                        'title' => 'Fattura',
                        'entries' => [
                            'customer-reference' => 'Riferimento del cliente',
                            'sales-person' => 'Venditore',
                            'payment-reference' => 'Riferimento al pagamento',
                            'recipient-bank' => 'La banca del destinatario',
                            'delivery-date' => 'Data di consegna',
                        ],
                    ],
                    'accounting' => [
                        'title' => 'Contabilità',
                        'entries' => [
                            'company' => 'Azienda',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                            'payment-method' => 'Metodo di pagamento',
                            'cash-rounding' => 'Metodo di arrotondamento del contante',
                            'fiscal-position' => 'Regime fiscale',
                            'auto-post' => 'Pubblicazione automatica',
                            'checked' => 'Verificato',
                        ],
                    ],
                ],
            ],
            'term-and-conditions' => [
                'title' => 'Termini e condizioni',
            ],
            'journal-items' => [
                'title' => 'Note contabili',
                'repeater' => [
                    'entries' => [
                        'account' => 'Conto',
                        'partner' => 'Partner',
                        'label' => 'Etichetta',
                        'currency' => 'Valuta',
                        'due-date' => 'Data di scadenza',
                        'taxes' => 'Imposte',
                        'debit' => 'Addebito',
                        'credit' => 'Credito',
                    ],
                ],
            ],
        ],
    ],
    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Aggiungi',
            ],
            'unreconcile' => [
                'label' => 'Scollega',
            ],
        ],
    ],
];
