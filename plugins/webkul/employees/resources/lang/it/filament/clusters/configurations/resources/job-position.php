<?php

return [
    'title' => 'Ruoli aziendali',
    'navigation' => [
        'title' => 'Ruoli aziendali',
        'group' => 'Assunzioni',
    ],
    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informazioni sull\'occupazione',
                'fields' => [
                    'job-position-title' => 'Titolo di lavoro',
                    'job-position-title-tooltip' => 'Inserisci il titolo professionale ufficiale',
                    'department' => 'Reparto',
                    'department-modal-title' => 'Crea dipartimento',
                    'company-modal-title' => 'Crea azienda',
                    'job-location' => 'Sede di lavoro',
                    'industry' => 'Settore',
                    'company' => 'Azienda',
                    'employment-type' => 'Tipo di impiego',
                    'recruiter' => 'Reclutatore',
                    'interviewer' => 'Intervistatore',
                ],
            ],
            'job-description' => [
                'title' => 'Descrizione del lavoro',
                'fields' => [
                    'job-description' => 'Descrizione del lavoro',
                    'job-requirements' => 'Requisiti di lavoro',
                ],
            ],
            'workforce-planning' => [
                'title' => 'Pianificazione del personale',
                'fields' => [
                    'recruitment-target' => 'Obiettivo di reclutamento',
                    'date-from' => 'Dalla data',
                    'date-to' => 'Data fino al',
                    'expected-skills' => 'Competenze attese',
                    'employment-type' => 'Tipo di impiego',
                    'status' => 'Stato',
                ],
            ],
            'position-status' => [
                'title' => 'Stato della posizione',
                'fields' => [
                    'status' => 'Stato',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Ruolo aziendale',
            'department' => 'Reparto',
            'job-position' => 'Ruolo aziendale',
            'company' => 'Azienda',
            'expected-employees' => 'Dipendenti attesi',
            'current-employees' => 'Dipendenti attuali',
            'status' => 'Stato',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'department' => 'Reparto',
            'employment-type' => 'Tipo di impiego',
            'job-position' => 'Ruolo aziendale',
            'company' => 'Azienda',
            'status' => 'Stato',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'job-position' => 'Ruolo aziendale',
            'company' => 'Azienda',
            'department' => 'Reparto',
            'employment-type' => 'Tipo di impiego',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Posto di lavoro restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Lavori ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Posti di lavoro eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Posti di lavoro definitivamente eliminati',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Ruoli aziendali',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informazioni sull\'occupazione',
                'entries' => [
                    'job-position-title' => 'Titolo di lavoro',
                    'department' => 'Reparto',
                    'company' => 'Azienda',
                    'employment-type' => 'Tipo di impiego',
                    'job-location' => 'Sede di lavoro',
                    'industry' => 'Settore',
                ],
            ],
            'job-description' => [
                'title' => 'Descrizione del lavoro',
                'entries' => [
                    'job-description' => 'Descrizione del lavoro',
                    'job-requirements' => 'Requisiti di lavoro',
                ],
            ],
            'work-planning' => [
                'title' => 'Pianificazione del personale',
                'entries' => [
                    'expected-employees' => 'Dipendenti attesi',
                    'current-employees' => 'Dipendenti attuali',
                    'date-from' => 'Dalla data',
                    'date-to' => 'Data fino al',
                    'recruitment-target' => 'Obiettivo di reclutamento',
                ],
            ],
            'position-status' => [
                'title' => 'Stato della posizione',
                'entries' => [
                    'status' => 'Stato',
                ],
            ],
        ],
    ],
];
