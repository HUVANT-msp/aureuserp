<?php

return [
    'form' => [
        'value' => 'Valore',
        'due' => 'Maturità',
        'delay-due' => 'Scadenza del termine',
        'delay-type' => 'Tipo di termine',
        'days-on-the-next-month' => 'Giorni del mese successivo',
        'days' => 'Giorni',
        'payment-term' => 'Termine di pagamento',
    ],
    'table' => [
        'columns' => [
            'due' => 'Maturità',
            'value' => 'Valore',
            'value-amount' => 'Importo del valore',
            'after' => 'Dopo',
            'delay-type' => 'Tipo di termine',
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
];
