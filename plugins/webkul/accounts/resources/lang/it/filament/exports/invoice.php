<?php

return [
    'columns' => [
        'number' => 'Numero',
        'state' => 'Stato',
        'customer' => 'Cliente',
        'invoice-date' => 'Data della fattura',
        'due-date' => 'Data di scadenza',
        'tax-excluded' => 'Tasse escluse',
        'tax' => 'Imposta',
        'total' => 'Totale',
        'amount-due' => 'Importo in sospeso',
        'payment-state' => 'Stato del pagamento',
        'checked' => 'Verificato',
        'accounting-date' => 'Data contabile',
        'source-document' => 'Documento di origine',
        'reference' => 'Riferimento',
        'sales-person' => 'Venditore',
        'invoice-currency' => 'Valuta della fattura',
    ],
    'values' => [
        'yes' => 'Sì',
        'no' => 'No',
    ],
    'notification' => [
        'completed' => 'L\'esportazione della fattura è stata completata e le righe :count sono state esportate.',
        'failed' => 'Impossibile esportare :count riga/e.',
    ],
];
