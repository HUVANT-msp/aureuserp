<?php

return [
    'title' => 'Movimenti',
    'table' => [
        'columns' => [
            'date' => 'Data',
            'reference' => 'Riferimento',
            'product' => 'Prodotto',
            'package' => 'Collo',
            'lot' => 'Numeri di lotto/serie',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Località di destinazione',
            'quantity' => 'Quantità',
            'unit' => 'Unità',
            'state' => 'Stato',
            'done-by' => 'Fatto da',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sposta rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
