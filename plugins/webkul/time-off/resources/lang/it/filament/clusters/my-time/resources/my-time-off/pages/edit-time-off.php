<?php

return [
    'notification' => [
        'title' => 'Assenza aggiornata',
        'body' => 'Aggiornamento completato con successo.',
        'action_not_allowed' => [
            'title' => 'Azione non consentita',
            'body' => 'Non puoi modificare questa richiesta di ferie perché è in uno stato bloccato.',
        ],
        'overlap' => [
            'title' => 'Richiesta di ferie sovrapposte',
            'body' => 'Le date di assenza selezionate si sovrappongono a una richiesta esistente. Seleziona date diverse.',
        ],
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Assenza eliminata',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
