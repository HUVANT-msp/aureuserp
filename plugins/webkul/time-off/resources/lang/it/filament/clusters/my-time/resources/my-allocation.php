<?php

return [
    'title' => 'Il mio incarico',
    'model-label' => 'Il mio incarico',
    'navigation' => [
        'title' => 'Il mio incarico',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Tipologia di assenza (da inizio validità a fine validità/senza limiti)',
            'time-off-type' => 'Tipo di assenza',
            'allocation-type' => 'Tipo di incarico',
            'validity-period' => 'Periodo di validità',
            'date-from' => 'Dalla data',
            'date-to' => 'Data fino al',
            'date-to-placeholder' => 'Nessun limite',
            'allocation' => 'Compito',
            'allocation-suffix' => 'Numero di giorni',
            'reason' => 'Motivo',
        ],
    ],
    'table' => [
        'columns' => [
            'time-off-type' => 'Tipo di assenza',
            'amount' => 'Importo',
            'allocation-type' => 'Tipo di incarico',
            'status' => 'Stato',
        ],
        'groups' => [
            'time-off-type' => 'Tipo di assenza',
            'employee-name' => 'Nome del dipendente',
            'allocation-type' => 'Tipo di incarico',
            'status' => 'Stato',
            'start-date' => 'Data inizio',
        ],
        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Convalidare',
                    'approve' => 'Approvare',
                ],
                'notification' => [
                    'title' => 'Incarico approvato',
                    'body' => 'L\'incarico è stato approvato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Compito eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'refused' => [
                'title' => 'Rifiuta',
                'notification' => [
                    'title' => 'Incarico rifiutato',
                    'body' => 'L\'incarico è stato rifiutato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Compiti cancellati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title' => 'Dettagli dell\'incarico',
                'entries' => [
                    'name' => 'Nome',
                    'time-off-type' => 'Tipo di assenza',
                    'allocation-type' => 'Tipo di incarico',
                ],
            ],
            'validity-period' => [
                'title' => 'Periodo di validità',
                'entries' => [
                    'date-from' => 'Dalla data',
                    'date-to' => 'Data fino al',
                    'reason' => 'Motivo',
                ],
            ],
            'allocation-status' => [
                'title' => 'Stato dell\'assegnazione',
                'entries' => [
                    'date-to-placeholder' => 'Nessun limite',
                    'allocation' => 'Numero di giorni',
                    'allocation-value' => ':days numero di giorni',
                    'state' => 'Stato',
                ],
            ],
        ],
    ],
];
