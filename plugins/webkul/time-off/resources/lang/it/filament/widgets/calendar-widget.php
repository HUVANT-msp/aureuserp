<?php

return [
    'heading' => [
        'title' => 'Richieste di assenza',
    ],
    'modal-actions' => [
        'edit' => [
            'title' => 'Modifica',
            'duration-display' => ':count giorno lavorativo|:count giorni lavorativi',
            'duration-display-with-weekend' => ':count giorno feriale (+ :weekend giorno del fine settimana)|:count giorno feriale (+ :weekend giorno del fine settimana)',
            'notification' => [
                'title' => 'Assenza aggiornata',
                'body' => 'Aggiornamento completato con successo.',
            ],
        ],
        'delete' => [
            'title' => 'Elimina',
        ],
    ],
    'config' => [
        'button-text' => [
            'today' => 'Oggi',
            'month' => 'Mese',
            'week' => 'Settimana',
            'list' => 'Elenco',
        ],
    ],
    'view-action' => [
        'title' => 'Visualizza',
        'description' => 'Visualizza la richiesta di assenza',
    ],
    'notifications' => [
        'employee-not-found' => [
            'title' => 'Dipendente non trovato',
            'body' => 'Aggiungi un dipendente al tuo profilo prima di richiedere un congedo.',
        ],
        'error' => [
            'title' => 'Qualcosa è andato storto',
            'body' => 'Non è stato possibile elaborare la tua richiesta di ferie. Per favore riprova.',
        ],
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
            'success' => [
                'notification' => [
                    'title' => 'Assenza creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'form' => [
        'title' => 'Richiesta di assenza',
        'description' => 'Crea o modifica la tua richiesta di ferie con i seguenti dettagli:',
        'fields' => [
            'time-off-type' => 'Tipo di assenza',
            'time-off-type-placeholder' => 'Seleziona un tipo di assenza',
            'time-off-type-helper' => 'Seleziona il tipo di congedo che stai richiedendo.',
            'request-date-from' => 'Data di inizio della domanda',
            'request-date-to' => 'Data di fine della domanda',
            'period' => 'Periodo',
            'half-day' => 'Mezza giornata',
            'half-day-helper' => 'Attivarsi per mezza giornata di assenza.',
            'requested-days' => 'Richiesto (giorni/ore)',
            'description' => 'Descrizione',
            'description-placeholder' => 'Nessuna descrizione fornita',
            'description-helper' => 'Si prega di fornire una breve descrizione della richiesta di ferie.',
            'duration' => 'Durata',
            'please-select-dates' => 'Seleziona la data della domanda da e a.',
        ],
    ],
    'infolist' => [
        'title' => 'Dettagli dell\'assenza',
        'description' => 'Questi sono i dettagli della tua richiesta di ferie:',
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
    'events' => [
        'title' => ':name il :status: :days giorno/i',
    ],
];
