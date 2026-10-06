<?php

return [
    'notification' => [
        'title' => 'Tassa aggiornata',
        'body' => 'Aggiornamento completato con successo.',
    ],
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
                'invalid-repartition-lines' => [
                    'title' => 'Righe di consegna non valide',
                ],
            ],
        ],
    ],
];
