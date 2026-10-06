<?php

return [
    'label' => 'Ritorno',
    'modal' => [
        'form' => [
            'columns' => [
                'product' => 'Prodotto',
                'quantity' => 'Quantità',
                'uom' => 'UoM',
                'excess-quantity-tooltip' => 'L\'importo da restituire è maggiore dell\'importo elaborato nell\'operazione originale.',
            ],
        ],
    ],
    'notification' => [
        'no-products' => [
            'body' => 'Non ci sono prodotti da restituire (possono essere restituite solo le righe in stato Evaduto che non sono state ancora completamente restituite).',
        ],
        'no-quantities' => [
            'body' => 'Specificare almeno una quantità diversa da zero.',
        ],
    ],
];
