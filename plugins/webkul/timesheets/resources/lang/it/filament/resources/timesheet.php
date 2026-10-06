<?php

return [
    'title' => 'Fogli ore',
    'navigation' => [
        'title' => 'Fogli ore',
    ],
    'global-search' => [
        'project' => 'Progetto',
        'task' => 'Attività',
        'date' => 'Data',
    ],
    'form' => [
        'date' => 'Data',
        'employee' => 'Collaboratore',
        'project' => 'Progetto',
        'task' => 'Attività',
        'description' => 'Descrizione',
        'time-spent' => 'Tempo impiegato',
        'time-spent-helper-text' => 'Tempo trascorso in ore (ad es. 1,5 ore significa 1 ora e 30 minuti)',
    ],
    'table' => [
        'columns' => [
            'date' => 'Data',
            'employee' => 'Collaboratore',
            'project' => 'Progetto',
            'task' => 'Attività',
            'description' => 'Descrizione',
            'time-spent' => 'Tempo impiegato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'date' => 'Data',
            'employee' => 'Collaboratore',
            'project' => 'Progetto',
            'task' => 'Attività',
            'creator' => 'Creato da',
        ],
        'filters' => [
            'date-from' => 'Dalla data',
            'date-until' => 'Alla data',
            'employee' => 'Collaboratore',
            'project' => 'Progetto',
            'task' => 'Attività',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Foglio ore aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Foglio ore eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Fogli ore eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
