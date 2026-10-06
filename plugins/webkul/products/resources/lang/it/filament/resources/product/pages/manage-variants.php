<?php

return [
    'title' => 'Varianti',
    'form' => [
        'date' => 'Data',
        'employee' => 'Collaboratore',
        'description' => 'Descrizione',
        'time-spent' => 'Tempo impiegato',
        'time-spent-helper-text' => 'Tempo trascorso in ore (ad es. 1,5 ore significa 1 ora e 30 minuti)',
    ],
    'table' => [
        'columns' => [
            'date' => 'Data',
            'employee' => 'Collaboratore',
            'description' => 'Descrizione',
            'time-spent' => 'Tempo impiegato',
            'time-spent-on-subtasks' => 'Tempo dedicato alle attività secondarie',
            'total-time-spent' => 'Tempo totale impiegato',
            'remaining-time' => 'Tempo rimanente',
            'variant-values' => 'Valori delle varianti',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'view' => [
                'extra-footer-actions' => [
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
                ],
            ],
        ],
    ],
];
