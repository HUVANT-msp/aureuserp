<?php

return [
    'heading' => 'Comunicazioni',
    'placeholders' => [
        'no-record-found' => 'Nessun record trovato.',
        'loading' => 'Caricamento Chatter...',
    ],
    'activity-infolist' => [
        'title' => 'Attività',
    ],
    'cancel-activity-plan-action' => [
        'title' => 'Annulla attività',
    ],
    'delete-message-action' => [
        'title' => 'Elimina messaggio',
    ],
    'edit-activity' => [
        'title' => 'Modifica attività',
        'form' => [
            'fields' => [
                'activity-plan' => 'Piano di attività',
                'plan-date' => 'Data del piano',
                'plan-summary' => 'Riepilogo del piano',
                'activity-type' => 'Tipo di attività',
                'due-date' => 'Data di scadenza',
                'summary' => 'Sommario',
                'assigned-to' => 'Assegnato a',
            ],
        ],
        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Attività aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
        ],
    ],
    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Nota originale</span>: :body</div>',
        'feedback' => '<div><span class="font-bold">Commenti</span>: <p>:feedback</p></div>',
    ],
    'mark-as-done' => [
        'title' => 'Segna come fatto',
        'actions' => [
            'done' => [
                'label' => 'Completato',
            ],
        ],
        'form' => [
            'fields' => [
                'feedback' => 'Commenti',
            ],
        ],
        'footer-actions' => [
            'label' => 'Fatto e programma il prossimo',
            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Attività contrassegnata come completata',
                        'body' => 'L\'attività è stata contrassegnata come completata con successo.',
                    ],
                ],
            ],
        ],
    ],
];
