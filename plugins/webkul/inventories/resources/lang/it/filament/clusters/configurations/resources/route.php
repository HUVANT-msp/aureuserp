<?php

return [
    'navigation' => [
        'title' => 'Rotte di movimentazione',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'route' => 'Rotta',
                    'route-placeholder' => 'pag. per esempio. Accoglienza in due fasi',
                    'company' => 'Azienda',
                ],
            ],
            'applicable-on' => [
                'title' => 'Applicabile a',
                'description' => 'Scegli le località in cui è possibile applicare questo percorso.',
                'fields' => [
                    'products' => 'Prodotti',
                    'products-hint-tooltip' => 'Se selezionato, sarà possibile scegliere questo percorso nel prodotto.',
                    'product-categories' => 'Categorie prodotto',
                    'product-categories-hint-tooltip' => 'Se selezionato, questo percorso sarà disponibile tra cui scegliere nella categoria prodotto.',
                    'warehouses' => 'Magazzini',
                    'warehouses-hint-tooltip' => 'Quando un magazzino viene assegnato a questo percorso, verrà considerato il percorso predefinito per i prodotti che transitano attraverso quel magazzino.',
                    'packaging' => 'Imballaggio',
                    'packaging-hint-tooltip' => 'Se selezionato, questo percorso sarà disponibile tra cui scegliere sulla confezione.',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'route' => 'Rotta',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'company' => 'Azienda',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Percorso aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Percorso ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Percorso eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Itinerario eliminato definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il percorso',
                        'body' => 'Impossibile eliminare il percorso perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Percorsi ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Percorsi eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Percorsi rimossi definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i percorsi',
                        'body' => 'I percorsi non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'route' => 'Rotta',
                    'route-placeholder' => 'pag. per esempio. Accoglienza in due fasi',
                    'company' => 'Azienda',
                ],
            ],
            'applicable-on' => [
                'title' => 'Applicabile a',
                'description' => 'Seleziona le località in cui è possibile applicare questo percorso.',
                'entries' => [
                    'products' => 'Prodotti',
                    'products-hint-tooltip' => 'Se selezionato, sarà possibile scegliere questo percorso nel prodotto.',
                    'product-categories' => 'Categorie prodotto',
                    'product-categories-hint-tooltip' => 'Se selezionato, questo percorso sarà disponibile tra cui scegliere nella categoria prodotto.',
                    'warehouses' => 'Magazzini',
                    'warehouses-hint-tooltip' => 'Quando un magazzino viene assegnato a questo percorso, verrà considerato il percorso predefinito per i prodotti che transitano attraverso quel magazzino.',
                    'packaging' => 'Imballaggio',
                    'packaging-hint-tooltip' => 'Se selezionato, questo percorso sarà disponibile tra cui scegliere sulla confezione.',
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
