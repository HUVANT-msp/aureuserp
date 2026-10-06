<?php

return [
    'notification' => [
        'title' => 'Prodotto aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'print' => [
            'label' => 'Stampa etichette',
            'form' => [
                'fields' => [
                    'quantity' => 'Numero di etichette',
                    'format' => 'Formato',
                    'format-options' => [
                        'dymo' => 'Dymo',
                        '2x7_price' => '2x7 con prezzo',
                        '4x7_price' => '4x7 con prezzo',
                        '4x12' => '4x12',
                        '4x12_price' => '4x12 con prezzo',
                    ],
                ],
            ],
        ],
        'delete' => [
            'notification' => [
                'title' => 'Prodotto eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
