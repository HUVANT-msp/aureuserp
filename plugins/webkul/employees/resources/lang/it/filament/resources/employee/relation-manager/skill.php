<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type' => 'Tipo di competizione',
                'skill' => 'Concorrenza',
                'skill-level' => 'Livello di competizione',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'skill-type' => 'Tipo di competizione',
            'skill' => 'Concorrenza',
            'skill-level' => 'Livello di competizione',
            'level-percent' => 'Percentuale del livello',
            'created-by' => 'Creato da',
            'user' => 'Utente',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'skill-type' => 'Tipo di competizione',
        ],
        'header-actions' => [
            'add-skill' => 'Aggiungi concorrenza',
        ],
        'filters' => [
            'activity-type' => 'Tipo di attività',
            'activity-status' => 'Stato dell\'attività',
            'has-delay' => 'È tardi',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Concorrenza aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'create' => [
                'notification' => [
                    'title' => 'Concorso creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminata la concorrenza',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Concorsi rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'skill-type' => 'Tipo di competizione',
            'skill' => 'Concorrenza',
            'skill-level' => 'Livello di competizione',
            'level-percent' => 'Percentuale del livello',
        ],
    ],
];
