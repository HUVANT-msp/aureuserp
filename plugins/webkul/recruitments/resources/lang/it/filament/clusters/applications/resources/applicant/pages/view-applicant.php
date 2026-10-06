<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidato eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
        'refuse' => [
            'title' => 'Motivo del rifiuto',
            'form' => [
                'fields' => [
                    'notify' => 'Avvisare',
                    'email-to' => 'Invia posta a',
                ],
            ],
            'notification' => [
                'title' => 'Candidato rifiutato',
                'body' => 'Il candidato è stato rifiutato con successo.',
            ],
        ],
        'reopen' => [
            'title' => 'Ripristina il candidato dal rifiuto',
            'notification' => [
                'title' => 'Candidato riaperto',
                'body' => 'Il candidato è stato riaperto con successo.',
            ],
        ],
        'state' => [
            'notification' => [
                'title' => 'Stato del candidato aggiornato',
                'body' => 'Aggiornamento completato con successo.',
            ],
        ],
    ],
    'mail' => [
        'application-refused' => [
            'subject' => 'La tua candidatura: :application',
        ],
    ],
];
