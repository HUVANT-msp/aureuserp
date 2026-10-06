<?php

return [
    'title' => 'Ripristina bozza',
    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'Stato della registrazione contabile non valida',
                    'body' => 'Solo le registrazioni contabili registrate o stornate possono essere reimpostate allo stato bozza.',
                ],
            ],
        ],
    ],
];
