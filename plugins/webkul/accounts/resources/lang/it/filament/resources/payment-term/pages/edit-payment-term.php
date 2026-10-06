<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Condizioni di pagamento aggiornate',
            'body' => 'Aggiornamento completato con successo.',
        ],
        'validation-error' => [
            'title' => 'Errore di convalida',
            'body' => 'Il periodo di scadenza deve avere almeno una linea percentuale e la somma delle percentuali deve essere 100%.',
        ],
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Condizione di pagamento rimossa',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
