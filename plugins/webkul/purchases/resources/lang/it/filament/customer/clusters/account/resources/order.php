<?php

return [
    'table' => [
        'columns' => [
            'reference' => 'Riferimento',
            'total-amount' => 'Importo totale',
            'confirmation-date' => 'Data di conferma',
            'status' => 'Stato',
        ],
    ],
    'products' => [
        'columns' => [
            'product' => 'Prodotto',
            'quantity' => 'Quantità',
            'unit-price' => 'Prezzo unitario',
            'taxes' => 'Imposte',
            'discount' => 'Sconto %',
            'amount' => 'Importo',
        ],
    ],
    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Acquirente',
            ],
            'actions' => [
                'accept' => [
                    'label' => 'Accetta',
                    'notification' => [
                        'title' => 'Preventivo accettato',
                        'body' => 'La richiesta di preventivo è stata confermata con successo.',
                    ],
                    'message' => [
                        'body' => 'La richiesta di preventivo è stata confermata dal fornitore.',
                    ],
                ],
                'decline' => [
                    'label' => 'Rifiuta',
                    'notification' => [
                        'title' => 'Bilancio rifiutato',
                        'body' => 'La richiesta di preventivo è stata rifiutata con successo.',
                    ],
                    'message' => [
                        'body' => 'La richiesta di preventivo è stata rifiutata dal fornitore.',
                    ],
                ],
                'print' => [
                    'label' => 'Scarica/Stampa',
                ],
            ],
        ],
        'general' => [
            'entries' => [
                'purchase-order' => 'N. ordine di acquisto :id',
                'quotation' => 'Richiesta di preventivo n.:id',
                'order-date' => 'Data dell\'ordine',
                'from' => 'Da allora',
                'confirmation-date' => 'Data di conferma',
                'receipt-date' => 'Data della ricevuta',
                'products' => 'Prodotti',
                'untaxed-amount' => 'Importo senza tasse',
                'tax-amount' => 'Importo fiscale',
                'total' => 'Totale',
                'communication-history' => 'Storia delle comunicazioni',
            ],
        ],
    ],
];
