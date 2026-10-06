<?php

return [
    'title' => 'Varianti',
    'form' => [
        'date' => 'Data',
        'employee' => 'Collaboratore',
        'description' => 'Descrizione',
        'time-spent' => 'Tempo impiegato',
        'time-spent-helper-text' => 'Tempo trascorso in ore (ad es. 1,5 ore significa 1 ora e 30 minuti)',
    ],
    'table' => [
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
            'delete' => [
                'notification' => [
                    'title' => 'Variante rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
