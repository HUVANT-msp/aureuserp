<?php

return [
    'navigation' => [
        'title' => 'Categorie di stoccaggio',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'allow-new-products' => 'Consenti nuovi prodotti',
                    'max-weight' => 'Peso massimo',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'allow-new-products' => 'Consenti nuovi prodotti',
            'max-weight' => 'Peso massimo',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'allow-new-products' => 'Consenti nuovi prodotti',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categoria di archiviazione rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorie di archiviazione rimosse',
                    'body' => 'Eliminazione completata con successo.',
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
                    'allow-new-products' => 'Consenti nuovi prodotti',
                    'max-weight' => 'Peso massimo',
                    'company' => 'Azienda',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-by' => 'Creato da',
                    'created-at' => 'Data creazione',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
        ],
    ],
];
