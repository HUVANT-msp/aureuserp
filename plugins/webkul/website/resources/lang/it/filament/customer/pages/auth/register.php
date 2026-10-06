<?php

return [
    'title' => 'Registrati',
    'heading' => 'Registrati',
    'notifications' => [
        'throttled' => [
            'title' => 'Troppi tentativi. Riprova tra :seconds secondi.',
            'body' => 'Attendi :seconds secondi (:minutes minuti) prima di riprovare.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nome',
        ],
        'email' => [
            'label' => 'E-mail',
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
    'actions' => [
        'login' => [
            'before' => 'Hai già un account?',
            'label' => 'Accedi',
        ],
    ],
];
