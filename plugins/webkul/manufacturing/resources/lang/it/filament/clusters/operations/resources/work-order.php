<?php

return [
    'navigation' => [
        'title' => 'Ordini di lavoro',
        'group' => 'Operazioni',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'work-order' => 'Ordine di lavoro',
                    'work-center' => 'Centro di lavoro',
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'manufacturing-order' => 'Ordine di produzione',
                    'lot-serial' => 'Numero di lotto/serie',
                    'start-date' => 'Data inizio',
                    'end-date' => 'Data fine',
                    'date-range-separator' => 'a',
                    'expected-duration' => 'Durata prevista',
                    'duration-suffix' => 'minuti',
                    'real-duration' => 'Durata effettiva',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title' => 'Tracciamento tempo',
                'add-action' => 'Aggiungi una riga',
                'columns' => [
                    'user' => 'Utente',
                    'duration' => 'Durata',
                    'start-date' => 'Data inizio',
                    'end-date' => 'Data fine',
                    'productivity' => 'Produttività',
                ],
                'footer' => [
                    'real-duration' => 'Durata effettiva',
                ],
            ],
            'components' => [
                'title' => 'Componenti',
                'add-action' => 'Aggiungi una riga',
                'columns' => [
                    'product' => 'Prodotto',
                    'from' => 'Da allora',
                    'to-consume' => 'Consumare',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title' => 'Istruzioni di lavoro',
                'entries' => [
                    'operation' => 'Operazione',
                    'worksheet' => 'Foglio di lavoro',
                ],
            ],
            'blocked-by' => [
                'title' => 'Bloccato da',
                'fields' => [
                    'work-orders' => 'Ordini di lavoro',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'operation' => 'Operazione',
            'work-center' => 'Centro di lavoro',
            'manufacturing-order' => 'Ordine di produzione',
            'product' => 'Prodotto',
            'quantity-remaining' => 'Quantità rimanente',
            'lot-serial' => 'Lotto/serie',
            'start' => 'Casa',
            'end' => 'Fine',
            'expected-duration' => 'Durata prevista',
            'real-duration' => 'Durata effettiva',
            'status' => 'Stato',
        ],
        'groups' => [
            'status' => 'Stato',
            'work-center' => 'Centro di lavoro',
            'manufacturing-order' => 'Ordine di produzione',
            'product' => 'Prodotto',
            'start' => 'Casa',
            'end' => 'Fine',
        ],
        'filters' => [
            'work-order' => 'Ordine di lavoro',
            'status' => 'Stato',
            'operation' => 'Operazione',
            'work-center' => 'Centro di lavoro',
            'manufacturing-order' => 'Ordine di produzione',
            'product' => 'Prodotto',
            'start' => 'Casa',
            'end' => 'Fine',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'work-order' => 'Ordine di lavoro',
                    'work-center' => 'Centro di lavoro',
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'manufacturing-order' => 'Ordine di produzione',
                    'lot-serial' => 'Numero di lotto/serie',
                    'start-date' => 'Data inizio',
                    'end-date' => 'Data fine',
                    'expected-duration' => 'Durata prevista',
                    'real-duration' => 'Durata effettiva',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title' => 'Tracciamento tempo',
                'footer' => [
                    'real-duration' => 'Durata effettiva',
                ],
            ],
            'components' => [
                'title' => 'Componenti',
            ],
            'work-instruction' => [
                'title' => 'Istruzioni di lavoro',
                'entries' => [
                    'operation' => 'Operazione',
                    'worksheet' => 'Foglio di lavoro',
                ],
            ],
            'blocked-by' => [
                'title' => 'Bloccato da',
                'columns' => [
                    'work-order' => 'Ordine di lavoro',
                    'work-center' => 'Centro di lavoro',
                    'status' => 'Stato',
                ],
            ],
        ],
    ],
    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Nuovo ordine di lavoro',
                ],
            ],
        ],
    ],
];
