<?php

return [
    'label' => 'Stampa etichette',
    'form' => [
        'fields' => [
            'quantity' => 'Quantità',
            'format' => 'Formato',
            'quantity-type' => 'Quantità da stampare',
            'quantity-type-options' => [
                'operation' => 'Quantità di operazione',
                'custom' => 'Quantità personalizzata',
            ],
            'format-options' => [
                'dymo' => 'Dymo',
                '2x7_price' => '2x7 con prezzo',
                '4x7_price' => '4x7 con prezzo',
                '4x12' => '4x12',
                '4x12_price' => '4x12 con prezzo',
            ],
        ],
    ],
];
