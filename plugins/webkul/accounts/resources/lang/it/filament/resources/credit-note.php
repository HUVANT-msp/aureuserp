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
                    'customer-invoice' => 'Nota di credito del cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data della fattura',
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
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
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
                    'marketing' => [
                        'title' => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campagna',
                            'medium' => 'Medio',
                            'source' => 'Fonte',
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
                    'customer-invoice' => 'Nota di credito del cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data della fattura',
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
                        'fieldset' => [
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Località Incoterm',
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
                    'marketing' => [
                        'title' => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campagna',
                            'medium' => 'Medio',
                            'source' => 'Fonte',
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
