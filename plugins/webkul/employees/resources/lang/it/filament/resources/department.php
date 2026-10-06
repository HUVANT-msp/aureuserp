<?php

return [
    'title' => 'Reparti',
    'navigation' => [
        'title' => 'Reparti',
    ],
    'global-search' => [
        'department-manager' => 'Responsabile',
        'company' => 'Azienda',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'manager' => 'Responsabile',
                    'parent-department' => 'Dipartimento superiore',
                    'manager-placeholder' => 'Seleziona responsabile',
                    'company' => 'Azienda',
                    'company-placeholder' => 'Seleziona azienda',
                    'color' => 'Colore',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'description' => 'Ulteriori informazioni su questo dipartimento.',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'manager-name' => 'Responsabile',
            'company-name' => 'Azienda',
        ],
        'groups' => [
            'name' => 'Nome',
            'manager' => 'Responsabile',
            'company' => 'Azienda',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'name' => 'Nome',
            'manager-name' => 'Responsabile',
            'company-name' => 'Azienda',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Appartamento restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Dipartimento rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Dipartimento definitivamente rimosso',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Appartamenti restaurati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Dipartimenti rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Dipartimenti rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'name' => 'Nome',
                    'manager' => 'Responsabile',
                    'company' => 'Azienda',
                    'color' => 'Colore',
                    'hierarchy-title' => 'Organizzazione del dipartimento',
                ],
            ],
        ],
    ],
];
