<?php

return [
    'title' => 'Scritture contabili',
    'navigation' => [
        'title' => 'Scritture contabili',
    ],
    'record-sub-navigation' => [
        'payment' => 'Pagamento',
    ],
    'global-search' => [
        'number' => 'Numero',
        'partner' => 'Partner',
        'date' => 'Data della fattura',
        'due-date' => 'Data di scadenza della fattura',
    ],
    'form' => [
        'section' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'reference' => 'Riferimento',
                    'accounting-date' => 'Data contabile',
                    'journal' => 'Sezionale',
                ],
            ],
        ],
        'tabs' => [
            'lines' => [
                'title' => 'Note contabili',
                'repeater' => [
                    'title' => 'Note',
                    'add-item' => 'Aggiungi nota',
                    'columns' => [
                        'account' => 'Conto',
                        'partner' => 'Partner',
                        'label' => 'Etichetta',
                        'amount-currency' => 'Importo (valuta)',
                        'currency' => 'Valuta',
                        'taxes' => 'Imposte',
                        'debit' => 'Addebito',
                        'credit' => 'Credito',
                        'discount-amount-currency' => 'Importo dello sconto (valuta)',
                    ],
                    'fields' => [
                        'account' => 'Conto',
                        'partner' => 'Partner',
                        'label' => 'Etichetta',
                        'amount-currency' => 'Importo (valuta)',
                        'currency' => 'Valuta',
                        'taxes' => 'Imposte',
                        'debit' => 'Addebito',
                        'credit' => 'Credito',
                        'discount-amount-currency' => 'Importo dello sconto (valuta)',
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Altre informazioni',
                'fields' => [
                    'checked' => 'Verificato',
                    'company' => 'Azienda',
                    'fiscal-position' => 'Regime fiscale',
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
            'invoice-date' => 'Data della fattura',
            'date' => 'Data',
            'number' => 'Numero',
            'partner' => 'Partner',
            'reference' => 'Riferimento',
            'journal' => 'Sezionale',
            'company' => 'Azienda',
            'total' => 'Totale',
            'state' => 'Stato',
            'checked' => 'Verificato',
        ],
        'summarizers' => [
            'total' => 'Totale',
        ],
        'groups' => [
            'partner' => 'Partner',
            'journal' => 'Sezionale',
            'state' => 'Stato',
            'payment-method' => 'Metodo di pagamento',
            'date' => 'Data',
            'invoice-date' => 'Data della fattura',
            'company' => 'Azienda',
        ],
        'filters' => [
            'number' => 'Numero',
            'invoice-partner-display-name' => 'Nome del contatto per la fatturazione',
            'invoice-date' => 'Data della fattura',
            'invoice-due-date' => 'Data di scadenza della fattura',
            'invoice-origin' => 'Origine della fattura',
            'reference' => 'Riferimento',
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
                    'number' => 'Numero',
                    'reference' => 'Riferimento',
                    'accounting-date' => 'Data contabile',
                    'journal' => 'Sezionale',
                ],
            ],
        ],
        'tabs' => [
            'lines' => [
                'title' => 'Note contabili',
                'repeater' => [
                    'entries' => [
                        'account' => 'Conto',
                        'partner' => 'Partner',
                        'label' => 'Etichetta',
                        'currency' => 'Valuta',
                        'taxes' => 'Imposte',
                        'debit' => 'Addebito',
                        'credit' => 'Credito',
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
                            'fiscal-position' => 'Regime fiscale',
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
];
