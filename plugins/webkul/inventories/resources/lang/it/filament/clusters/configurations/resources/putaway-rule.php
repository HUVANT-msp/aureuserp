<?php

return [
    'navigation' => [
        'title' => 'Regole di stoccaggio',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'fields' => [
            'in-location' => 'Quando il prodotto arriva',
            'product' => 'Prodotto',
            'product-placeholder' => 'Tutti i prodotti',
            'category' => 'Categoria prodotto',
            'category-placeholder' => 'Tutte le categorie',
            'storage-category' => 'Categoria di stoccaggio',
            'out-location' => 'Conservare',
            'sub-location' => 'Sublocazione',
            'company' => 'Azienda',
        ],
    ],
    'table' => [
        'columns' => [
            'in-location' => 'Quando il prodotto arriva',
            'product' => 'Prodotto',
            'category' => 'Categoria prodotto',
            'storage-category' => 'Categoria di stoccaggio',
            'out-location' => 'Conservare',
            'sub-location' => 'Sublocazione',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regola di archiviazione aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Regola di archiviazione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Regola di archiviazione rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Impossibile eliminare la regola di archiviazione',
                        'body' => 'La regola di archiviazione non può essere eliminata in modo permanente perché altri record fanno riferimento ad essa.',
                    ],
                    'success' => [
                        'title' => 'Regola di archiviazione eliminata definitivamente',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Regole di archiviazione ripristinate',
                    'body' => 'Le regole di archiviazione sono state ripristinate correttamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Regole di archiviazione rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Impossibile eliminare le regole di archiviazione',
                        'body' => 'Alcune regole di archiviazione non possono essere eliminate in modo permanente perché fanno riferimento ad altri record.',
                    ],
                    'success' => [
                        'title' => 'Regole di archiviazione eliminate definitivamente',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                ],
            ],
        ],
    ],
];
