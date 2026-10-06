<?php

return [
    'title' => 'Modello di bilancio',
    'navigation' => [
        'title' => 'Modello di bilancio',
        'group' => 'Ordini di vendita',
    ],
    'form' => [
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
                'fields' => [
                    'products' => 'Prodotti',
                    'name' => 'Nome',
                    'quantity' => 'Quantità',
                ],
            ],
            'terms-and-conditions' => [
                'title' => 'Termini e condizioni',
                'fields' => [
                    'note-placeholder' => 'Annotare i termini e le condizioni per i preventivi.',
                ],
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'quotation-validity' => 'Validità del bilancio',
                    'sale-journal' => 'Diario delle vendite',
                ],
            ],
            'signature-and-payment' => [
                'title' => 'Firma e pagamenti',
                'fields' => [
                    'online-signature' => 'Firma in linea',
                    'online-payment' => 'Pagamento in linea',
                    'prepayment-percentage' => 'Percentuale di pagamento anticipato',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'created-by' => 'Creato da',
            'company' => 'Azienda',
            'name' => 'Nome',
            'number-of-days' => 'Numero di giorni',
            'journal' => 'Diario delle vendite',
            'signature-required' => 'Firma richiesta',
            'payment-required' => 'Pagamento richiesto',
            'prepayment-percentage' => 'Percentuale di pagamento anticipato',
        ],
        'groups' => [
            'company' => 'Azienda',
            'name' => 'Nome',
            'journal' => 'Sezionale',
        ],
        'filters' => [
            'created-by' => 'Creato da',
            'company' => 'Azienda',
            'name' => 'Nome',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modello di preventivo eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modello di preventivo eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Prodotti',
            ],
            'terms-and-conditions' => [
                'title' => 'Termini e condizioni',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
            ],
            'signature_and_payment' => [
                'title' => 'Firma e pagamento',
            ],
        ],
        'entries' => [
            'product' => 'Prodotto',
            'description' => 'Descrizione',
            'quantity' => 'Quantità',
            'unit-price' => 'Prezzo unitario',
            'section-name' => 'Nome della sezione',
            'note-title' => 'Titolo della nota',
            'name' => 'Nome del modello',
            'quotation-validity' => 'Validità del bilancio',
            'sale-journal' => 'Diario delle vendite',
            'online-signature' => 'Firma in linea',
            'online-payment' => 'Pagamento in linea',
            'prepayment-percentage' => 'Percentuale di pagamento anticipato',
        ],
    ],
];
