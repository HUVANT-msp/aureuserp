<?php

return [
    'title' => 'Gestisci gli account predefiniti',
    'setup' => [
        'action' => 'Configura la contabilità per questa azienda',
        'notice' => 'La contabilità non è ancora stata impostata per questa società. Utilizza "Imposta contabilità" qui sopra per creare il tuo piano dei conti, i diari e le impostazioni predefinite.',
        'notification' => [
            'title' => 'Contabilità configurata',
            'body' => 'Per questa società sono stati creati il piano dei conti, i giornali di giornale e le impostazioni predefinite.',
        ],
    ],
    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Scambio posti differenza',
            'fields' => [
                'journal' => [
                    'label' => 'Sezionale',
                ],
                'gain' => [
                    'label' => 'Profitto',
                ],
                'loss' => [
                    'label' => 'Perdita',
                ],
            ],
        ],
        'bank-transfer-and-payments' => [
            'label' => 'Bonifici e pagamenti bancari',
            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Conto sospeso bancario',
                ],
                'transfer-account' => [
                    'label' => 'Conto di trasferimento',
                ],
            ],
        ],
        'product-accounts' => [
            'label' => 'Conti di prodotto',
            'fields' => [
                'income-account' => [
                    'label' => 'Conto dei redditi',
                ],
                'expense-account' => [
                    'label' => 'Conto spese',
                ],
            ],
        ],
    ],
];
