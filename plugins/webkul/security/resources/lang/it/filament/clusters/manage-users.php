<?php

return [
    'breadcrumb' => 'Gestisci gli utenti',
    'title' => 'Gestisci gli utenti',
    'group' => 'Generale',
    'navigation' => [
        'label' => 'Gestisci gli utenti',
    ],
    'form' => [
        'enable-user-invitation' => [
            'label' => 'Abilita l\'invito dell\'utente',
            'helper-text' => 'Consenti agli utenti di invitare altri utenti all\'app.',
        ],
        'enable-reset-password' => [
            'label' => 'Abilita la reimpostazione della password',
            'helper-text' => 'Consenti agli utenti di reimpostare la propria password.',
        ],
        'default-role' => [
            'label' => 'Ruolo predefinito',
            'helper-text' => 'Il ruolo predefinito assegnato ai nuovi utenti.',
        ],
        'default-company' => [
            'label' => 'Azienda predefinita',
            'helper-text' => 'La società predefinita assegnata ai nuovi utenti.',
        ],
    ],
];
