<?php

return [
    'global-search' => [
        'zip-from' => 'Codice postale da',
        'zip-to' => 'Codice postale a',
        'name' => 'Nome',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'foreign-vat' => 'NIF estero',
            'country' => 'Paese',
            'country-group' => 'Gruppo di paesi',
            'zip-from' => 'Codice postale da',
            'zip-to' => 'Codice postale a',
            'detect-automatically' => 'Rileva automaticamente',
            'notes' => 'Nota',
            'company' => 'Azienda',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account' => 'Conto di origine',
                        'destination-account' => 'Conto di destinazione',
                    ],
                ],
            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source' => 'Imposta sull\'origine',
                        'tax-destination' => 'Tassa di destinazione',
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company' => 'Azienda',
            'country' => 'Paese',
            'country-group' => 'Gruppo di paesi',
            'created-by' => 'Creato da',
            'zip-from' => 'Codice postale da',
            'zip-to' => 'Codice postale a',
            'status' => 'Stato',
            'detect-automatically' => 'Rileva automaticamente',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Condizione di pagamento rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posizione fiscale eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'foreign-vat' => 'NIF estero',
            'country' => 'Paese',
            'country-group' => 'Gruppo di paesi',
            'zip-from' => 'Codice postale da',
            'zip-to' => 'Codice postale a',
            'detect-automatically' => 'Rileva automaticamente',
            'notes' => 'Nota',
        ],
    ],
];
