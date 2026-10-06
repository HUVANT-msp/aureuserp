<?php

return [
    'title' => 'Fogli ore',
    'form' => [
        'date' => 'Data',
        'employee' => 'Collaboratore',
        'description' => 'Descrizione',
        'time-spent' => 'Tempo impiegato',
        'time-spent-helper-text' => 'Tempo trascorso in ore (ad es. 1,5 ore significa 1 ora e 30 minuti)',
    ],
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi foglio ore',
                'notification' => [
                    'title' => 'Foglio ore creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'columns' => [
            'date' => 'Data',
            'employee' => 'Collaboratore',
            'description' => 'Descrizione',
            'time-spent' => 'Tempo impiegato',
            'time-spent-on-subtasks' => 'Tempo dedicato alle attività secondarie',
            'total-time-spent' => 'Tempo totale impiegato',
            'remaining-time' => 'Tempo rimanente',
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
    ],
];
