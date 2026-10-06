<?php

return [
    'navigation' => [
        'title' => 'Tipi di collo',
        'group' => 'Spedizione',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'barcode' => 'Codice a barre',
                    'company' => 'Azienda',
                    'weight' => 'Peso',
                    'max-weight' => 'Peso massimo',
                    'fieldsets' => [
                        'size' => [
                            'title' => 'Dimensioni',
                            'fields' => [
                                'length' => 'Lunghezza',
                                'width' => 'Larghezza',
                                'height' => 'Alto',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'barcode' => 'Codice a barre',
            'weight' => 'Peso',
            'max-weight' => 'Peso massimo',
            'width' => 'Larghezza',
            'height' => 'Alto',
            'length' => 'Lunghezza',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di pacchetto rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di pacchetto rimosso',
                    'body' => 'Eliminazione completata con successo.',
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
                    'fieldsets' => [
                        'size' => [
                            'title' => 'Dimensioni della confezione',
                            'entries' => [
                                'length' => 'Lunghezza',
                                'width' => 'Larghezza',
                                'height' => 'Alto',
                            ],
                        ],
                    ],
                    'weight' => 'Peso base',
                    'max-weight' => 'Peso massimo',
                    'barcode' => 'Codice a barre',
                    'company' => 'Azienda',
                    'created-at' => 'Data creazione',
                    'updated-at' => 'Ultimo aggiornamento',
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
