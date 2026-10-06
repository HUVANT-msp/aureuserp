<?php

return [
    'notification' => [
        'title' => 'Restringimento aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'validate' => [
            'label' => 'Convalidare',
            'notification' => [
                'warning' => [
                    'title' => 'Scorte insufficienti',
                    'body' => 'La contrazione non dispone di scorte sufficienti per la convalida.',
                ],
                'success' => [
                    'title' => 'Contrazione contrassegnata come realizzata',
                    'body' => 'Il restringimento è stato contrassegnato come riuscito.',
                ],
            ],
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Eliminazione degli sprechi',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Le perdite non potevano essere eliminate',
                    'body' => 'Il restringimento non può essere eliminato perché è in uso.',
                ],
            ],
        ],
    ],
];
