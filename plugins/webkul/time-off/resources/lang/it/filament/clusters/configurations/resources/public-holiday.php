<?php

return [
    'title' => 'Vacanze',
    'model-label' => 'Vacanza',
    'navigation' => [
        'title' => 'Vacanze',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Inserisci il nome della festività',
            'date-from' => 'Data inizio',
            'date-to' => 'Data fine',
            'color' => 'Colore',
            'calendar' => 'Calendario',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'calendar' => 'Calendario',
            'created-by' => 'Creato da',
            'date-from' => 'Data inizio',
            'date-to' => 'Data fine',
        ],
        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'created-by' => 'Creato da',
            'date-from' => 'Data inizio',
            'date-to' => 'Data fine',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'created-by' => 'Creato da',
            'date-from' => 'Data inizio',
            'date-to' => 'Data fine',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Vacanza aggiornata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Vacanza rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Festività rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'date-from' => 'Data inizio',
            'date-to' => 'Data fine',
            'color' => 'Colore',
        ],
    ],
];
