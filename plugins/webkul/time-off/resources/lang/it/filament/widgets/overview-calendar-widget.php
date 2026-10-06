<?php

return [
    'heading' => [
        'title' => 'Riepilogo delle assenze',
    ],
    'modal-actions' => [
        'edit' => [
            'title' => 'Modifica',
            'notification' => [
                'title' => 'Assenza aggiornata',
                'body' => 'Aggiornamento completato con successo.',
            ],
        ],
        'delete' => [
            'title' => 'Elimina',
        ],
    ],
    'view-action' => [
        'title' => 'Visualizza',
        'description' => 'Visualizza la richiesta di assenza',
    ],
    'header-actions' => [
        'create' => [
            'title' => 'Nuova assenza',
            'description' => 'Crea richiesta di ferie',
            'notification' => [
                'title' => 'Assenza creata',
                'body' => 'Creazione completata con successo.',
            ],
            'employee-not-found' => [
                'notification' => [
                    'title' => 'Dipendente non trovato',
                    'body' => 'Aggiungi un dipendente al tuo profilo prima di creare una richiesta di ferie.',
                ],
            ],
        ],
    ],
    'form' => [
        'fields' => [
            'time-off-type' => 'Tipo di assenza',
            'request-date-from' => 'Data di inizio della domanda',
            'request-date-to' => 'Data di fine della domanda',
            'period' => 'Periodo',
            'half-day' => 'Mezza giornata',
            'requested-days' => 'Richiesto (giorni/ore)',
            'description' => 'Descrizione',
        ],
    ],
    'infolist' => [
        'entries' => [
            'time-off-type' => 'Tipo di assenza',
            'request-date-from' => 'Data di inizio della domanda',
            'request-date-to' => 'Data di fine della domanda',
            'description' => 'Descrizione',
            'description-placeholder' => 'Nessuna descrizione fornita',
            'duration' => 'Durata',
            'status' => 'Stato',
        ],
    ],
];
