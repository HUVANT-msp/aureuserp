<?php

return [
    'form' => [
        'fields' => [
            'tax-source' => 'Imposta sull\'origine',
            'tax-destination' => 'Tassa di destinazione',
        ],
    ],
    'table' => [
        'columns' => [
            'tax-source' => 'Imposta sull\'origine',
            'tax-destination' => 'Tassa di destinazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Condizioni di pagamento aggiornate',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Condizione di pagamento rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Condizione di pagamento creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'tax-source' => 'Imposta sull\'origine',
            'tax-destination' => 'Tassa di destinazione',
        ],
    ],
];
