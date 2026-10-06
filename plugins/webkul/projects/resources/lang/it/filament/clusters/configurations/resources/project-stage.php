<?php

return [
    'navigation' => [
        'title' => 'Fasi del progetto',
    ],
    'form' => [
        'name' => 'Nome',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'is-completed' => 'Completato',
            'project' => 'Progetto',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'is-completed' => 'Completato',
            'project' => 'Progetto',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Fase del progetto aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Fase di progetto ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Fase del progetto rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Fase del progetto eliminata definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la fase del progetto',
                        'body' => 'La fase del progetto non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Fasi del progetto ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Fasi del progetto rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Fasi del progetto eliminate definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
];
