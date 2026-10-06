<?php

return [
    'navigation' => [
        'title' => 'Fasi del compito',
    ],
    'form' => [
        'name' => 'Nome',
        'project' => 'Progetto',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'project' => 'Progetto',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'project' => 'Progetto',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'project' => 'Progetto',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Fase dell\'attività aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Fase del compito ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Fase dell\'attività eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Fase dell\'attività eliminata definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la fase dell\'attività',
                        'body' => 'Impossibile eliminare la fase dell\'attività perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Fasi dell\'attività ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Fasi delle attività rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Fasi dell\'attività eliminate definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
