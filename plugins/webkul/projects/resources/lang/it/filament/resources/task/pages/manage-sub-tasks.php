<?php

return [
    'title' => 'Attività secondarie',
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi attività secondaria',
                'notification' => [
                    'title' => 'Attività creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attività ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attività eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Attività eliminata definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
