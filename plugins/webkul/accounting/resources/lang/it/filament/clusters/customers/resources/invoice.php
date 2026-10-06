<?php

return [
    'title' => 'Fattura',
    'navigation' => [
        'title' => 'Fatture',
    ],
    'global-search' => [
        'customer' => 'Cliente',
        'date' => 'Data',
        'due-date' => 'Data di scadenza',
        'amount' => 'Importo',
    ],
    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Prodotto aperto',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
