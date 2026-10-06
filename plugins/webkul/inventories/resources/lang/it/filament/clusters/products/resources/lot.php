<?php

return [
    'navigation' => [
        'title' => 'Lotti / Numeri di serie',
        'group' => 'Magazzino',
    ],
    'global-search' => [
        'ref' => 'Riferimento',
        'product' => 'Prodotto',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'pag. per esempio. LOTTO/0001/20121',
                    'product' => 'Prodotto',
                    'product-hint-tooltip' => 'Il prodotto associato a questo lotto/numero di serie. Non può essere modificato se è già stato spostato.',
                    'company' => 'Azienda',
                    'reference' => 'Riferimento',
                    'reference-hint-tooltip' => 'Un numero di riferimento interno, se diverso dal numero di lotto/serie del produttore.',
                    'description' => 'Descrizione',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'product' => 'Prodotto',
            'on-hand-qty' => 'Quantità a magazzino',
            'reference' => 'Codice articolo / Riferimento',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'creator' => 'Creato da',
            'company' => 'Azienda',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lotto eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il batch',
                        'body' => 'Il batch non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'print' => [
                'label' => 'Stampa codice a barre',
            ],
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lotti eliminati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i batch',
                        'body' => 'I pacchetti non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Dettagli del lotto',
                'entries' => [
                    'name' => 'Nome del lotto',
                    'product' => 'Prodotto',
                    'reference' => 'Riferimento',
                    'description' => 'Descrizione',
                    'on-hand-qty' => 'Quantità disponibile',
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
