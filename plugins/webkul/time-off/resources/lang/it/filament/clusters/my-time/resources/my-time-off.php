<?php

return [
    'title' => 'Assenze',
    'model-label' => 'Le mie assenze',
    'navigation' => [
        'title' => 'Le mie assenze',
    ],
    'form' => [
        'fields' => [
            'time-off-type' => 'Tipo di assenza',
            'date' => 'Data',
            'dates' => 'Date',
            'request-date-from' => 'Data di inizio della domanda',
            'request-date-to' => 'Data di fine della domanda',
            'description' => 'Descrizione',
            'period' => 'Periodo',
            'half-day' => 'Mezza giornata',
            'requested-days' => 'Richiesto (giorni/ore)',
            'attachment' => 'In allegato',
            'day' => ':day giorno',
            'days' => ':days giorno/i',
        ],
    ],
    'table' => [
        'columns' => [
            'employee-name' => 'Collaboratore',
            'time-off-type' => 'Tipo di assenza',
            'description' => 'Descrizione',
            'date-from' => 'Dalla data',
            'date-to' => 'Data fino al',
            'duration' => 'Durata',
            'status' => 'Stato',
        ],
        'groups' => [
            'employee-name' => 'Collaboratore',
            'time-off-type' => 'Tipo di assenza',
            'status' => 'Stato',
            'start-date' => 'Data inizio',
            'start-to' => 'Data fine',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Convalidare',
                    'approve' => 'Approvare',
                ],
                'notification' => [
                    'title' => 'Assenza approvata',
                    'body' => 'L\'assenza è stata approvata con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Assenza eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'refused' => [
                'title' => 'Rifiuta',
                'notification' => [
                    'title' => 'L\'assenza rifiutata',
                    'body' => 'L\'assenza è stata respinta con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Assenze eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'time-off-type' => 'Tipo di assenza',
            'date' => 'Data',
            'dates' => 'Date',
            'request-date-from' => 'Data di inizio della domanda',
            'request-date-to' => 'Data di fine della domanda',
            'description' => 'Descrizione',
            'period' => 'Periodo',
            'half-day' => 'Mezza giornata',
            'requested-days' => 'Richiesto (giorni/ore)',
            'attachment' => 'In allegato',
            'day' => ':day giorno',
            'days' => ':days giorno/i',
        ],
    ],
];
