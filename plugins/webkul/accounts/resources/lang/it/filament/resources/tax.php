<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Nome',
                'tax-type' => 'Tipologia fiscale',
                'tax-computation' => 'Calcolo delle imposte',
                'tax-scope' => 'Ambito fiscale',
                'status' => 'Stato',
                'amount' => 'Importo',
                'formula' => 'Formula',
                'formula-helper-text' => 'Solo aritmetica: numeri, + - * / ( ), le funzioni :functions e queste variabili: :variables',
                'children-taxes' => 'Tasse sui bambini',
                'children-taxes-helper-text' => 'L\'importo di questa tassa è la somma delle tasse sui figli.',
                'children-taxes-type-mismatch' => 'Le tasse per bambini devono essere di tipo fiscale :type o non avere alcun tipo di tassa. Questi non sono: :taxes',
            ],
            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Righe di distribuzione delle fatture',
                ],
                'refund-repartition-lines' => [
                    'label' => 'Linee di distribuzione del rimborso',
                ],
                'fields' => [
                    'type' => 'Tipo',
                    'factor-percent' => 'Fattore %',
                    'account' => 'Conto',
                ],
            ],
            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opzioni avanzate',
                    'fields' => [
                        'invoice-label' => 'Etichetta della fattura',
                        'tax-group' => 'Gruppo fiscale',
                        'country' => 'Paese',
                        'include-in-price' => 'Incluso nel prezzo',
                        'include-base-amount' => 'Influisce sulla successiva base imponibile',
                        'is-base-affected' => 'Base interessata da imposte precedenti',
                    ],
                ],
                'fields' => [
                    'description' => 'Descrizione',
                    'legal-notes' => 'Note legali',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'amount-type' => 'Tipo di importo',
            'company' => 'Azienda',
            'tax-group' => 'Gruppo fiscale',
            'country' => 'Paese',
            'tax-type' => 'Tipologia fiscale',
            'tax-scope' => 'Ambito fiscale',
            'invoice-label' => 'Etichetta della fattura',
            'tax-exigibility' => 'Esecutività fiscale',
            'price-include-override' => 'Cancellazione dell\'inclusione nel prezzo',
            'amount' => 'Importo',
            'status' => 'Stato',
            'include-base-amount' => 'Includere l\'importo base',
            'is-base-affected' => 'Base interessata',
        ],
        'groups' => [
            'name' => 'Nome',
            'company' => 'Azienda',
            'tax-group' => 'Gruppo fiscale',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'type-tax-use' => 'Tipologia di utilizzo fiscale',
            'tax-scope' => 'Ambito fiscale',
            'amount-type' => 'Tipo di importo',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Eliminazione delle tasse',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile rimuovere l\'imposta',
                        'body' => 'L\'imposta non può essere rimossa perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Eliminate le tasse',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Le tasse non potevano essere rimosse',
                        'body' => 'Le tasse non possono essere rimosse perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Righe di consegna non valide',
                    ],
                ],
            ],
            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Righe di consegna non valide',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Nome',
                'tax-type' => 'Tipologia fiscale',
                'tax-computation' => 'Calcolo delle imposte',
                'tax-scope' => 'Ambito fiscale',
                'status' => 'Stato',
                'amount' => 'Importo',
                'formula' => 'Formula',
                'children-taxes' => 'Tasse sui bambini',
            ],
            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opzioni avanzate',
                    'entries' => [
                        'invoice-label' => 'Etichetta della fattura',
                        'tax-group' => 'Gruppo fiscale',
                        'country' => 'Paese',
                        'include-in-price' => 'Incluso nel prezzo',
                        'include-base-amount' => 'Includere l\'importo base',
                        'is-base-affected' => 'Base interessata',
                    ],
                ],
                'description-and-legal-notes' => [
                    'title' => 'Descrizione e note legali della fattura',
                    'entries' => [
                        'description' => 'Descrizione',
                        'legal-notes' => 'Note legali',
                    ],
                ],
            ],
        ],
    ],
];
