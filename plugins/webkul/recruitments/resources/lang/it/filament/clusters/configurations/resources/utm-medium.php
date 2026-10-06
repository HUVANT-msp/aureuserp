<?php

return [
    'title' => 'Media',
    'navigation' => [
        'title' => 'Media',
        'group' => 'UTM',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Immettere il nome del supporto',
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
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mezzo aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Metà rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Supporto rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Mezzo creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
    ],
];
