<?php

return [
    'title' => 'Partner',
    'navigation' => [
        'title' => 'Partner',
    ],
    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Vendite',
                        'fields' => [
                            'sales-person' => 'Venditore',
                            'payment-terms' => 'Termini di pagamento',
                            'payment-method' => 'Metodo di pagamento',
                        ],
                    ],
                    'purchase' => [
                        'title' => 'Acquisto',
                        'fields' => [
                            'payment-terms' => 'Termini di pagamento',
                            'payment-method' => 'Metodo di pagamento',
                        ],
                    ],
                    'fiscal-information' => [
                        'title' => 'Informazioni fiscali',
                        'fields' => [
                            'fiscal-position' => 'Regime fiscale',
                        ],
                    ],
                ],
            ],
            'invoicing' => [
                'title' => 'Fatturazione',
                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Fatture di vendita',
                        'fields' => [
                            'invoice-sending-method' => 'Metodo di invio della fattura',
                            'invoice-edi-format-store' => 'Formato fattura elettronica',
                            'peppol-eas' => 'Indirizzo Peppol',
                            'endpoint' => 'Punto finale',
                        ],
                    ],
                    'accounting-entries' => [
                        'title' => 'Registrazioni contabili',
                        'fields' => [
                            'account-receivable' => 'Conto clienti',
                            'account-payable' => 'Conto da pagare',
                        ],
                    ],
                    'automation' => [
                        'title' => 'Automazione',
                        'fields' => [
                            'auto-post-bills' => 'Registra automaticamente le fatture dei fornitori',
                            'ignore-abnormal-invoice-amount' => 'Ignorare l\'importo anomalo della fattura',
                            'ignore-abnormal-invoice-date' => 'Ignorare la data anomala della fattura',
                        ],
                    ],
                ],
            ],
            'internal-notes' => [
                'title' => 'Note interne',
            ],
        ],
    ],
    'infolist' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Vendite',
                        'entries' => [
                            'sales-person' => 'Venditore',
                            'payment-terms' => 'Termini di pagamento',
                            'payment-method' => 'Metodo di pagamento',
                        ],
                    ],
                    'purchase' => [
                        'title' => 'Acquisto',
                        'entries' => [
                            'payment-terms' => 'Termini di pagamento',
                            'payment-method' => 'Metodo di pagamento',
                        ],
                    ],
                    'fiscal-information' => [
                        'title' => 'Informazioni fiscali',
                        'entries' => [
                            'fiscal-position' => 'Regime fiscale',
                        ],
                    ],
                ],
            ],
            'invoicing' => [
                'title' => 'Fatturazione',
                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Fatture di vendita',
                        'entries' => [
                            'invoice-sending-method' => 'Metodo di invio della fattura',
                            'invoice-edi-format-store' => 'Formato fattura elettronica',
                            'peppol-eas' => 'Indirizzo Peppol',
                            'endpoint' => 'Punto finale',
                        ],
                    ],
                    'accounting-entries' => [
                        'title' => 'Registrazioni contabili',
                        'entries' => [
                            'account-receivable' => 'Conto clienti',
                            'account-payable' => 'Conto da pagare',
                        ],
                    ],
                    'automation' => [
                        'title' => 'Automazione',
                        'entries' => [
                            'auto-post-bills' => 'Registra automaticamente le fatture dei fornitori',
                            'ignore-abnormal-invoice-amount' => 'Ignorare l\'importo anomalo della fattura',
                            'ignore-abnormal-invoice-date' => 'Ignorare la data anomala della fattura',
                        ],
                    ],
                ],
            ],
            'internal-notes' => [
                'title' => 'Note interne',
            ],
        ],
    ],
];
