<?php

return [
    'title' => 'Accedi',
    'heading' => 'Accedi',
    'messages' => [
        'failed' => 'Queste credenziali non corrispondono ai nostri record.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Troppi tentativi. Riprova tra :seconds secondi.',
            'body' => 'Attendi :seconds secondi (:minutes minuti) prima di riprovare.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'E-mail',
        ],
        'password' => [
            'label' => 'Password',
        ],
        'remember' => [
            'label' => 'Ricordati di me',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Accedi',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Non hai un account?',
            'label' => 'Crea un account',
        ],
        'request_password_reset' => [
            'label' => 'Hai dimenticato la password?',
        ],
    ],
];
