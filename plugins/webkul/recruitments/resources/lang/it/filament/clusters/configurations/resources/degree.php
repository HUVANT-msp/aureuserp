<?php

return [
    'title' => 'Qualifiche',
    'navigation' => [
        'title' => 'Qualifiche',
        'group' => 'Candidature',
    ],
    'groups' => [
        'status' => 'Stato',
        'created-by' => 'Creato da',
        'created-at' => 'Data creazione',
        'updated-at' => 'Ultima modifica',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Inserisci il nome del corso di laurea',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'employee' => 'Collaboratore',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Qualifica aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Laurea eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Qualifiche eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Laurea creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
    ],
];
