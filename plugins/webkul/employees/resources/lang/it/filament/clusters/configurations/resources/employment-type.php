<?php

return [
    'title' => 'Tipi di impiego',
    'navigation' => [
        'title' => 'Tipi di impiego',
        'group' => 'Assunzioni',
    ],
    'form' => [
        'fields' => [
            'name' => 'Tipo di impiego',
            'code' => 'Codice',
            'country' => 'Paese',
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Tipo di impiego',
            'code' => 'Codice',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Tipo di impiego',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Tipo di impiego',
            'country' => 'Paese',
            'code' => 'Codice',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tipo di impiego',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di lavoro rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminate le tipologie di impiego',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipi di impiego',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'name' => 'Tipo di impiego',
            'code' => 'Codice',
            'country' => 'Paese',
        ],
    ],
];
