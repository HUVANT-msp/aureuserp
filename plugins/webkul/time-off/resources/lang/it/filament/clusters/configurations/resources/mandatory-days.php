<?php

return [
    'title' => 'Giorni obbligatori',
    'model-label' => 'Giorno obbligatorio',
    'navigation' => [
        'title' => 'Ferie obbligatorie',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'color' => 'Colore',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'created-by' => 'Creato da',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
        ],
        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'created-by' => 'Creato da',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
        ],
        'groups' => [
            'name' => 'Nome',
            'company-name' => 'Nome dell\'azienda',
            'created-by' => 'Creato da',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Giorno obbligatorio aggiornato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Giorno obbligatorio rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati i giorni obbligatori',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'color' => 'Colore',
        ],
    ],
];
