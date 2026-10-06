<?php

return [
    'title' => 'Sedi di lavoro',
    'navigation' => [
        'title' => 'Sedi di lavoro',
        'group' => 'Collaboratore',
    ],
    'form' => [
        'name' => 'Nome',
        'company' => 'Azienda',
        'location-type' => 'Tipo di posizione',
        'location-number' => 'Numero di posizione',
        'status' => 'Stato',
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'status' => 'Stato',
            'company' => 'Azienda',
            'location-type' => 'Tipo di posizione',
            'location-number' => 'Numero di posizione',
            'deleted-at' => 'Eliminato il',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'created-by' => 'Creato da',
            'company' => 'Azienda',
            'location-number' => 'Numero di posizione',
            'location-type' => 'Tipo di posizione',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'location-type' => 'Tipo di posizione',
            'company' => 'Azienda',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Luogo di lavoro aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Luogo di lavoro ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Luogo di lavoro eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Luogo di lavoro eliminato definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
            'empty-state' => [
                'notification' => [
                    'title' => 'Luogo di lavoro creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Luoghi di lavoro eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Luoghi di lavoro eliminati definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'name' => 'Nome',
        'company' => 'Azienda',
        'location-type' => 'Tipo di posizione',
        'location-number' => 'Numero di posizione',
        'status' => 'Stato',
    ],
];
