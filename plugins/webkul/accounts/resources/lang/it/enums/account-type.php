<?php

return [
    'assets' => [
        'label' => 'Beni',
        'options' => [
            'receivable' => 'Creditibile',
            'cash' => 'Banca e contanti',
            'current' => 'Attività correnti',
            'non-current' => 'Attività non correnti',
            'prepayments' => 'Pagamenti anticipati',
            'fixed' => 'Immobilizzazioni',
        ],
    ],
    'liabilities' => [
        'label' => 'Passività',
        'options' => [
            'payable' => 'Per pagare',
            'credit-card' => 'Carta di credito',
            'current' => 'Passività correnti',
            'non-current' => 'Passività non correnti',
        ],
    ],
    'equity' => [
        'label' => 'Patrimonio',
        'options' => [
            'equity' => 'Patrimonio',
            'unaffected' => 'Risultati dell\'anno in corso',
        ],
    ],
    'income' => [
        'label' => 'Reddito',
        'options' => [
            'income' => 'Reddito',
            'other' => 'Altri redditi',
        ],
    ],
    'expenses' => [
        'label' => 'Spese',
        'options' => [
            'expense' => 'Spese',
            'depreciation' => 'Ammortamento',
            'direct-cost' => 'Costo del venduto',
        ],
    ],
    'off-balance' => [
        'label' => 'Fuori equilibrio',
        'options' => [
            'off-balance' => 'Fuori equilibrio',
        ],
    ],
];
