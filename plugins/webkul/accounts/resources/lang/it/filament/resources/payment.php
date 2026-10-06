<?php

return [
    'title' => 'Pagamento',
    'navigation' => [
        'title' => 'Pagamenti',
        'group' => 'Fatture',
    ],
    'global-search' => [
        'partner' => 'Partner',
        'amount' => 'Importo',
        'date' => 'Data',
    ],
    'form' => [
        'sections' => [
            'fields' => [
                'payment-type' => 'Tipo di pagamento',
                'memo' => 'Promemoria',
                'date' => 'Data',
                'amount' => 'Importo',
                'currency' => 'Valuta',
                'payment-method' => 'Metodo di pagamento',
                'customer' => 'Cliente',
                'vendor' => 'Fornitore',
                'journal' => 'Sezionale',
                'customer-bank-account' => 'Conto bancario del cliente',
                'vendor-bank-account' => 'Conto bancario del fornitore',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'date' => 'Data',
            'journal' => 'Sezionale',
            'payment-method' => 'Metodo di pagamento',
            'partner' => 'Partner',
            'amount-currency' => 'Importo (valuta)',
            'amount' => 'Importo',
            'state' => 'Stato',
            'company' => 'Azienda',
            'currency' => 'Valuta',
            'created-by' => 'Creato da',
        ],
        'groups' => [
            'name' => 'Nome',
            'company' => 'Azienda',
            'journal' => 'Sezionale',
            'partner' => 'Partner',
            'payment-method-line' => 'Riga del metodo di pagamento',
            'payment-method' => 'Metodo di pagamento',
            'partner-bank-account' => 'Contatta il conto bancario',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'company' => 'Azienda',
            'journal' => 'Sezionale',
            'customer-bank-account' => 'Conto bancario del cliente',
            'payment-method' => 'Metodo di pagamento',
            'currency' => 'Valuta',
            'partner' => 'Partner',
            'payment-method-line' => 'Riga del metodo di pagamento',
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
        'sections' => [
            'payment-information' => [
                'title' => 'Informazioni sul pagamento',
                'entries' => [
                    'state' => 'Stato',
                    'vendor' => 'Fornitore',
                    'customer' => 'Cliente',
                    'payment-type' => 'Tipo di pagamento',
                    'journal' => 'Sezionale',
                    'customer-bank-account' => 'Conto bancario del cliente',
                    'vendor-bank-account' => 'Conto bancario del fornitore',
                    'amount' => 'Importo',
                    'payment-method' => 'Metodo di pagamento',
                    'date' => 'Data',
                    'memo' => 'Promemoria',
                ],
            ],
        ],
    ],
];
