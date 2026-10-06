<?php

return [
    'title' => 'Ho dimenticato la password',
    'heading' => 'Ho dimenticato la password',
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
        'actions' => [
            'request' => [
                'label' => 'Invia collegamento di reimpostazione',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Accedi di nuovo',
        ],
    ],
];
