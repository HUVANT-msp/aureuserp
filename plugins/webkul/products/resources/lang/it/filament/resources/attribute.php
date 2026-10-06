<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'type' => 'Tipo',
                ],
            ],
            'options' => [
                'title' => 'Opzioni',
                'fields' => [
                    'name' => 'Nome',
                    'color' => 'Colore',
                    'extra-price' => 'Prezzo aggiuntivo',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'type' => 'Tipo',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'type' => 'Tipo',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'type' => 'Tipo',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attributo ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attributo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attributo rimosso definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile rimuovere l\'attributo',
                        'body' => 'Impossibile eliminare l\'attributo perché è utilizzato dal prodotto: :products',
                        'more' => '... +:count altro',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attributi ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attributi rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attributi rimossi permanentemente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'partial' => [
                        'title' => 'Impossibile rimuovere alcuni attributi',
                        'body' => 'Impossibile eliminare l\'attributo ":attributes" perché è utilizzato dal prodotto: :products',
                    ],
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
                    'type' => 'Tipo',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'creator' => 'Creato da',
                    'created_at' => 'Data creazione',
                    'updated_at' => 'Ultimo aggiornamento su',
                ],
            ],
        ],
    ],
];
