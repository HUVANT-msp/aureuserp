<?php

return [
    'form' => [
        'factor-percent' => 'Percentuale del fattore',
        'factor-ratio' => 'Rapporto dei fattori',
        'repartition-type' => 'Tipo di distribuzione',
        'document-type' => 'Tipo di documento',
        'account' => 'Conto',
        'tax' => 'Imposta',
        'tax-closing-entry' => 'Voce di chiusura fiscale',
    ],
    'table' => [
        'columns' => [
            'factor-percent' => 'Percentuale del fattore (%)',
            'account' => 'Conto',
            'tax' => 'Imposta',
            'company' => 'Azienda',
            'repartition-type' => 'Tipo di distribuzione',
            'document-type' => 'Tipo di documento',
            'tax-closing-entry' => 'Voce di chiusura fiscale',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Distribuzione fiscale aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminata la distribuzione fiscale',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Creazione della distribuzione fiscale',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
];
