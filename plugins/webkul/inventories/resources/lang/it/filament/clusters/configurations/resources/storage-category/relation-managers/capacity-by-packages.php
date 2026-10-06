<?php

return [
    'title' => 'Capacità del pacchetto',
    'form' => [
        'package-type' => 'Tipo di collo',
        'qty' => 'Quantità',
    ],
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi capacità per tipo di pacchetto',
                'notification' => [
                    'title' => 'Capacità per tipo di pacchetto creato',
                    'body' => 'La capacità per tipo di pacchetto è stata aggiunta correttamente.',
                ],
            ],
        ],
        'columns' => [
            'package-type' => 'Tipo di collo',
            'qty' => 'Quantità',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacità per tipo di pacco aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Capacità per tipo di pacchetto rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
