<?php

return [
    'label' => 'Tag',
    'form' => [
        'fields' => [
            'type' => 'Tipo di etichette',
            'quantity' => 'Quantità',
            'format' => 'Formato',
            'layout' => 'Progettazione di etichette',
            'quantity-type' => 'Quantità da stampare',
            'quantity-type-options' => [
                'operation' => 'Quantità di operazione',
                'custom' => 'Quantità personalizzata',
                'per-slot' => 'Uno per lotto/NS',
                'per-unit' => 'Uno per unità',
            ],
            'type-options' => [
                'product' => 'Etichette dei prodotti',
                'lot' => 'Etichette lotto/NS',
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
