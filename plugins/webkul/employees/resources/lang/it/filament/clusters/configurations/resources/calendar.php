<?php

return [
    'title' => 'Orari di lavoro',
    'navigation' => [
        'title' => 'Orari di lavoro',
        'group' => 'Collaboratore',
    ],
    'groups' => [
        'status' => 'Stato',
        'created-by' => 'Creato da',
        'created-at' => 'Data creazione',
        'updated-at' => 'Ultima modifica',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'schedule-name' => 'Nome del programma',
                    'schedule-name-tooltip' => 'Immettere un nome descrittivo per il programma di lavoro.',
                    'timezone' => 'Fuso orario',
                    'timezone-tooltip' => 'Selezionare il fuso orario per l\'orario di lavoro.',
                    'company' => 'Azienda',
                ],
            ],
            'configuration' => [
                'title' => 'Impostazione dell\'orario di lavoro',
                'fields' => [
                    'hours-per-day' => 'Ore al giorno',
                    'hours-per-day-suffix' => 'Ore',
                    'full-time-required-hours' => 'Orario richiesto full-time',
                    'full-time-required-hours-suffix' => 'Ore settimanali',
                ],
            ],
            'flexibility' => [
                'title' => 'Flessibilità',
                'fields' => [
                    'status' => 'Stato',
                    'two-weeks-calendar' => 'Calendario di due settimane',
                    'two-weeks-calendar-tooltip' => 'Attivare l\'orario di lavoro alternato di due settimane.',
                    'flexible-hours' => 'Programma flessibile',
                    'flexible-hours-tooltip' => 'Consentire ai dipendenti di avere un orario di lavoro flessibile.',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome del programma',
            'timezone' => 'Fuso orario',
            'company' => 'Azienda',
            'flexible-hours' => 'Programma flessibile',
            'status' => 'Stato',
            'daily-hours' => 'Orari giornalieri',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'company' => 'Azienda',
            'is-active' => 'Stato',
            'two-week-calendar' => 'Calendario di due settimane',
            'flexible-hours' => 'Programma flessibile',
            'timezone' => 'Fuso orario',
            'name' => 'Nome del programma',
            'attendance' => 'Presenza',
            'created-by' => 'Creato da',
            'daily-hours' => 'Orari giornalieri',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Nome del programma',
            'status' => 'Stato',
            'timezone' => 'Fuso orario',
            'flexible-hours' => 'Programma flessibile',
            'daily-hours' => 'Orari giornalieri',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Piano del calendario ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Piano del calendario rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Piano di calendario eliminato definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Piani del calendario ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Piani del calendario eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Piani di calendario eliminati definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome',
                    'schedule-name' => 'Nome del programma',
                    'schedule-name-tooltip' => 'Immettere un nome descrittivo per il programma di lavoro.',
                    'timezone' => 'Fuso orario',
                    'timezone-tooltip' => 'Selezionare il fuso orario per l\'orario di lavoro.',
                    'company' => 'Azienda',
                ],
            ],
            'configuration' => [
                'title' => 'Impostazione dell\'orario di lavoro',
                'entries' => [
                    'hours-per-day' => 'Ore al giorno',
                    'hours-per-day-suffix' => 'Ore',
                    'full-time-required-hours' => 'Orario richiesto full-time',
                    'full-time-required-hours-suffix' => 'Ore settimanali',
                ],
            ],
            'flexibility' => [
                'title' => 'Flessibilità',
                'entries' => [
                    'status' => 'Stato',
                    'two-weeks-calendar' => 'Calendario di due settimane',
                    'two-weeks-calendar-tooltip' => 'Attivare l\'orario di lavoro alternato di due settimane.',
                    'flexible-hours' => 'Programma flessibile',
                    'flexible-hours-tooltip' => 'Consentire ai dipendenti di avere un orario di lavoro flessibile.',
                ],
            ],
        ],
    ],
];
