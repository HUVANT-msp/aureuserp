<?php

return [
    'tabs' => [
        'all' => 'Tutti gli utenti',
        'archived' => 'Utenti archiviati',
    ],
    'header-actions' => [
        'invite' => [
            'title' => 'Invita utente',
            'modal' => [
                'submit-action-label' => 'Invita utente',
            ],
            'form' => [
                'email' => 'Email',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Utente ospite',
                    'body' => 'L\'utente è stato invitato con successo',
                ],
                'error' => [
                    'title' => 'Errore durante l\'invito dell\'utente',
                    'body' => 'Il sistema ha riscontrato un errore imprevisto durante il tentativo di inviare l\'invito dell\'utente.',
                ],
                'default-company-error' => [
                    'title' => 'Società predefinita non stabilita',
                    'body' => 'Imposta l\'azienda predefinita nelle impostazioni prima di invitare un utente.',
                ],
            ],
        ],
        'create' => [
            'label' => 'Nuovo utente',
        ],
    ],
];
