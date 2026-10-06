<?php

return [
    'title' => 'Attività',
    'header-actions' => [
        'create' => [
            'label' => 'Nuovo compito',
        ],
    ],
    'table' => [
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
    'tabs' => [
        'open-tasks' => 'Attività aperte',
        'my-tasks' => 'I miei compiti',
        'unassigned-tasks' => 'Compiti non assegnati',
        'closed-tasks' => 'Compiti chiusi',
        'starred-tasks' => 'Compiti in primo piano',
        'archived-tasks' => 'Attività archiviate',
    ],
];
