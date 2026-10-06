<?php

return [
    'navigation' => [
        'title' => 'Team',
    ],
    'form' => [
        'name' => 'Nome',
        'company' => 'Azienda',
        'users' => 'Membri del team',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company' => 'Azienda',
            'users' => 'Membri del team',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attrezzatura aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Attrezzatura restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Squadra eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attrezzatura eliminata definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare definitivamente il computer',
                        'body' => 'Il computer è in uso e non può essere eliminato in modo permanente.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attrezzatura rinnovata',
                    'body' => 'I computer sono stati ripristinati con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Squadre eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
