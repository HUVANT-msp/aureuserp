<?php

return [
    'title' => 'Ubicazioni',
    'table' => [
        'columns' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'storage-category' => 'Categoria di stoccaggio',
            'quantity' => 'Quantità',
            'package' => 'Collo',
            'on-hand' => 'Quantità a magazzino',
            'unit' => 'Unità',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Importo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
