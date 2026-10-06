<?php

return [
    'title' => 'Fattura',
    'navigation' => [
        'title' => 'Fatture',
        'group' => 'Fatture',
    ],
    'global-search' => [
        'vendor' => 'Fornitore',
        'date' => 'Data',
        'due-date' => 'Data di scadenza',
    ],
    'form' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'vendor-bill' => 'Fattura di acquisto',
                    'vendor' => 'Fornitore',
                    'bill-date' => 'Data della fattura',
                    'bill-reference' => 'Riferimento della fattura',
                    'accounting-date' => 'Data contabile',
                    'payment-reference' => 'Riferimento al pagamento',
                    'recipient-bank' => 'La banca del destinatario',
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
                            'discount-percentage' => 'Percentuale di sconto',
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
                    'accounting' => [
                        'title' => 'Contabilità',
                        'fields' => [
                            'company' => 'Azienda',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                            'payment-method' => 'Metodo di pagamento',
                            'fiscal-position' => 'Regime fiscale',
                            'fiscal-position-tooltip' => 'Gli elementi fiscali vengono utilizzati per personalizzare le imposte e i conti in base alla posizione del cliente.',
                            'cash-rounding' => 'Metodo di arrotondamento del contante',
                            'cash-rounding-tooltip' => 'Specifica l\'unità di valuta più piccola che può essere pagata in contanti.',
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
            'customer' => 'Cliente',
            'bill-date' => 'Data della fattura',
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
            'bill-currency' => 'Valuta della fattura',
        ],
        'summarizers' => [
            'total' => 'Totale',
        ],
        'groups' => [
            'name' => 'Nome',
            'bill-partner-display-name' => 'Nome visualizzato del contatto per la fatturazione',
            'bill-date' => 'Data della fattura',
            'checked' => 'Verificato',
            'date' => 'Data',
            'bill-due-date' => 'Data di scadenza della fattura',
            'bill-origin' => 'Origine della fattura',
            'sales-person' => 'Venditore',
            'currency' => 'Valuta',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'number' => 'Numero',
            'bill-partner-display-name' => 'Nome visualizzato del contatto per la fatturazione',
            'bill-date' => 'Data della fattura',
            'bill-due-date' => 'Data di scadenza della fattura',
            'bill-origin' => 'Origine della fattura',
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
                    'vendor-invoice' => 'Fattura del fornitore',
                    'vendor' => 'Fornitore',
                    'bill-date' => 'Data della fattura',
                    'bill-reference' => 'Riferimento della fattura',
                    'accounting-date' => 'Data contabile',
                    'payment-reference' => 'Riferimento al pagamento',
                    'recipient-bank' => 'La banca del destinatario',
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
                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Contabilità',
                        'entries' => [
                            'company' => 'Azienda',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                            'payment-method' => 'Metodo di pagamento',
                            'checked' => 'Verificato',
                            'fiscal-position' => 'Regime fiscale',
                            'cash-rounding' => 'Metodo di arrotondamento del contante',
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
                        'due-date' => 'Data di scadenza',
                        'currency' => 'Valuta',
                        'taxes' => 'Imposte',
                        'debit' => 'Addebito',
                        'credit' => 'Credito',
                    ],
                ],
            ],
        ],
    ],
];
