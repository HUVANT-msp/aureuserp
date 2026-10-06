<?php

return [
    'setup' => [
        'title' => 'Pianifica attività',
        'submit-action-title' => 'Programma',
        'form' => [
            'fields' => [
                'activity-plan' => 'Piano di attività',
                'plan-date' => 'Data del piano',
                'plan-summary' => 'Riepilogo del piano',
                'activity-type' => 'Tipo di attività',
                'due-date' => 'Data di scadenza',
                'summary' => 'Sommario',
                'assigned-to' => 'Assegnato a',
                'log-note' => 'Registra la nota',
            ],
        ],
        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Attività creata',
                    'body' => 'L\'attività è stata creata.',
                ],
                'warning' => [
                    'title' => 'Non ci sono nuovi file',
                    'body' => 'Tutti i file sono già stati caricati.',
                ],
                'error' => [
                    'title' => 'Errore durante la creazione dell\'attività',
                    'body' => 'Impossibile creare l\'attività',
                ],
            ],
        ],
    ],
];
