<?php

return [
    'title' => 'Paga',
    'form' => [
        'fields' => [
            'journal' => 'Sezionale',
            'amount' => 'Importo',
            'currency' => 'Valuta',
            'payment-method-line' => 'Riga del metodo di pagamento',
            'payment-date' => 'Data di pagamento',
            'recipient-bank-account' => 'Conto bancario del destinatario',
            'communication' => 'Promemoria',
        ],
    ],
    'notifications' => [
        'payment-failed' => [
            'title' => 'Pagamento non riuscito',
        ],
    ],
];
