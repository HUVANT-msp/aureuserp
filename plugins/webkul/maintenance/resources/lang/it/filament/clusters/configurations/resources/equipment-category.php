<?php

return [
    'navigation' => [
        'title' => 'Categorie',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'technician' => 'Responsabile',
                    'company' => 'Azienda',
                    'note' => 'Nota',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'technician' => 'Responsabile',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'technician' => 'Responsabile',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Categoria rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorie rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Categoria creata',
                    'body' => 'Creazione completata con successo.',
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
                    'technician' => 'Responsabile',
                    'company' => 'Azienda',
                    'note' => 'Nota',
                ],
            ],
        ],
    ],
];
