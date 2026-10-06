<?php

return [
    'modal' => [
        'title' => 'Orario di lavoro',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'attendance-name' => 'Nome dell\'assistenza',
                    'day-of-week' => 'Giorno della settimana',
                ],
            ],
            'timing-information' => [
                'title' => 'Informazioni sul programma',
                'fields' => [
                    'day-period' => 'Periodi della giornata',
                    'week-type' => 'Tipologia settimana',
                    'work-from' => 'Lavora da',
                    'work-to' => 'Lavorare',
                ],
            ],
            'date-information' => [
                'title' => 'Informazioni sulla data',
                'fields' => [
                    'starting-date' => 'Data di inizio',
                    'ending-date' => 'Data di fine',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'durations-days' => 'Durata (giorni)',
                    'display-type' => 'Tipo di visualizzazione',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome dell\'assistenza',
            'day-of-week' => 'Giorno della settimana',
            'day-period' => 'Periodi della giornata',
            'work-from' => 'Lavora da',
            'work-to' => 'Lavorare',
            'starting-date' => 'Data di inizio',
            'ending-date' => 'Data di fine',
            'display-type' => 'Tipo di visualizzazione',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'activity-type' => 'Tipo di attività',
            'assignment' => 'Compito',
            'assigned-to' => 'Assegnato a',
            'interval' => 'Intervallo',
            'delay-unit' => 'Unità di ritardo',
            'delay-from' => 'Ritardo da',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'display-type' => 'Tipo di visualizzazione',
            'day-of-week' => 'Giorno della settimana',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Programma di lavoro aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'create' => [
                'notification' => [
                    'title' => 'Programma di lavoro creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Orario di lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Orari di lavoro ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Orario di lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Orario di lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Orario di lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome dell\'assistenza',
                    'day-of-week' => 'Giorno della settimana',
                ],
            ],
            'timing-information' => [
                'title' => 'Informazioni sul programma',
                'entries' => [
                    'day-period' => 'Periodi della giornata',
                    'week-type' => 'Tipologia settimana',
                    'work-from' => 'Lavora da',
                    'work-to' => 'Lavorare',
                ],
            ],
            'date-information' => [
                'title' => 'Informazioni sulla data',
                'entries' => [
                    'starting-date' => 'Data di inizio',
                    'ending-date' => 'Data di fine',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'durations-days' => 'Durata (giorni)',
                    'display-type' => 'Tipo di visualizzazione',
                ],
            ],
        ],
        'note' => 'Nota',
    ],
];
