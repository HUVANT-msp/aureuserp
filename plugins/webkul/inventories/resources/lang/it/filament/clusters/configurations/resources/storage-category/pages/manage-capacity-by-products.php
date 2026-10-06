<?php

return [
    'title' => 'Capacità del prodotto',
    'form' => [
        'product' => 'Prodotto',
        'qty' => 'Quantità',
    ],
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi capacità per prodotto',
                'notification' => [
                    'title' => 'Capacità del prodotto creata',
                    'body' => 'La capacità per prodotto è stata aggiunta correttamente.',
                ],
            ],
        ],
        'columns' => [
            'product' => 'Prodotto',
            'qty' => 'Quantità',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacità aggiornata per prodotto',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Capacità del prodotto eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
