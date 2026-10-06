<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Eliminazione delle tasse',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile rimuovere l\'imposta',
                    'body' => 'L\'imposta non può essere rimossa perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
