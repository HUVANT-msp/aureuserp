<?php

return [
    'title' => 'Reimposta la password',
    'heading' => 'Reimposta la password',
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
            'label' => 'Nuova password',
            'validation_attribute' => 'parola d\'ordine',
        ],
        'password_confirmation' => [
            'label' => 'Conferma la nuova password',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Reimposta la password',
            ],
        ],
    ],
];
