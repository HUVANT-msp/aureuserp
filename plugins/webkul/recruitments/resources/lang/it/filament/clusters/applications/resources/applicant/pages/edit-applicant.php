<?php

return [
    'create-employee' => 'Crea dipendente',
    'goto-employee' => 'Vai al dipendente',
    'notification' => [
        'title' => 'Candidato aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Candidato eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Candidato eliminato',
                'body' => 'Eliminazione definitiva completata con successo.',
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
            'title' => 'Riaprire il candidato',
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
        'application-confirm' => [
            'subject' => 'La tua candidatura: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Ti è stato assegnato il candidato :applicant.',
        ],
    ],
];
