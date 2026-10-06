<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Proprietà dell\'account',
                'fields' => [
                    'income-account' => 'Conto dei redditi',
                    'income-account-hint-tooltip' => 'Questo account verrà utilizzato durante la convalida di una fattura cliente.',
                    'expense-account' => 'Conto spese',
                    'expense-account-hint-tooltip' => 'La spesa viene registrata al momento della convalida della fattura fornitore, tranne che nella contabilità anglosassone con valorizzazione di magazzino perpetua, dove viene invece riconosciuta come costo del venduto al momento della convalida della fattura cliente.',
                    'down-payment-account' => 'Conto anticipato',
                    'down-payment-account-hint-tooltip' => 'Selezionare il conto sul quale verranno registrati gli anticipi di questa categoria.',
                ],
            ],
        ],
    ],
];
