<?php

return [
    'title' => 'Fattura',
    'navigation' => [
        'title' => 'Fatture',
        'group' => 'Fatture',
    ],
    'form' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'vendor-credit-note' => 'Nota di credito del fornitore',
                    'vendor' => 'Fornitore',
                    'bill-date' => 'Data della fattura',
                    'bill-reference' => 'Riferimento della fattura',
                    'accounting-date' => 'Data contabile',
                    'payment-reference' => 'Riferimento al pagamento',
                    'recipient-bank' => 'La banca del destinatario',
                    'due-date' => 'Data di scadenza',
                    'payment-term' => 'Termine di pagamento',
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
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                        ],
                    ],
                    'secured' => [
                        'title' => 'Assicurato',
                        'fields' => [
                            'payment-method' => 'Metodo di pagamento',
                            'auto-post' => 'Pubblicazione automatica',
                            'checked' => 'Verificato',
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
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
                        ],
                    ],
                    'secured' => [
                        'title' => 'Assicurato',
                        'entries' => [
                            'payment-method' => 'Metodo di pagamento',
                            'auto-post' => 'Pubblicazione automatica',
                            'checked' => 'Verificato',
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
