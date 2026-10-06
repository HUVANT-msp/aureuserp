<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Accetta l\'invito',
        ],
    ],
    'title' => 'Registrati',
    'heading' => 'Crea un account',
    'actions' => [
        'login' => [
            'before' => 'o',
            'label' => 'accedi al tuo account',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'E-mail',
        ],
        'name' => [
            'label' => 'Nome',
        ],
        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'parola d\'ordine',
        ],
        'password_confirmation' => [
            'label' => 'Conferma la password',
        ],
        'actions' => [
            'register' => [
                'label' => 'Crea un account',
            ],
        ],
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Troppi tentativi di registrazione',
            'body' => 'Riprova tra :seconds secondi.',
        ],
    ],
];
