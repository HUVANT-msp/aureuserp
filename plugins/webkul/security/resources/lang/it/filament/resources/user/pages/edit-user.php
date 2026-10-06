<?php

return [
    'notification' => [
        'title' => 'Utente aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'change-password' => [
            'label' => 'Cambia password',
            'notification' => [
                'title' => 'La password è cambiata',
                'body' => 'La password è stata modificata con successo.',
            ],
            'form' => [
                'new-password' => 'Nuova password',
                'confirm-new-password' => 'Conferma la nuova password',
            ],
        ],
        'delete' => [
            'notification' => [
                'title' => 'Utente eliminato',
                'body' => 'Eliminazione completata con successo.',
                'error' => [
                    'title' => 'Impossibile eliminare l\'utente',
                    'body' => 'Questo è un utente predefinito oppure non puoi eliminarti.',
                ],
            ],
        ],
    ],
];
