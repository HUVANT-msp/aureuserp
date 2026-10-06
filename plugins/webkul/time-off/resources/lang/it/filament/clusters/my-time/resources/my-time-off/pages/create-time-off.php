<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Assenza creata',
            'body' => 'Creazione completata con successo.',
        ],
        'overlap' => [
            'title' => 'Richiesta di ferie sovrapposte',
            'body' => 'Le date di assenza selezionate si sovrappongono a una richiesta esistente. Seleziona date diverse.',
        ],
        'warning' => [
            'title' => 'Non hai un account dipendente',
            'body' => 'Non hai un account dipendente. Contatta l\'amministratore.',
        ],
        'invalid_half_day_leave' => [
            'title' => 'Richiesta ferie non valida',
            'body' => 'L\'assenza di mezza giornata può essere richiesta solo per un solo giorno.',
        ],
        'leave_request_denied_no_allocation' => [
            'title' => 'Richiesta di assenza respinta',
            'body' => 'Non hai assenze assegnate per :leaveType.',
        ],
        'leave_request_denied_insufficient_balance' => [
            'title' => 'Richiesta di assenza respinta',
            'body' => 'Saldo assenze insufficiente. Hai :available_balance giorno/i disponibile/i. Richiesto: :requested_days giorno/i.',
        ],
    ],
];
