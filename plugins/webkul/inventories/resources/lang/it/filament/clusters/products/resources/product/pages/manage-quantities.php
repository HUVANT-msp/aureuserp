<?php

return [
    'title' => 'Quantità',
    'tabs' => [
        'internal-locations' => 'Ubicazioni interne',
        'transit-locations' => 'Ubicazioni di transito',
        'on-hand' => 'Giacenza disponibile',
        'to-count' => 'Raccontare',
        'to-apply' => 'Per candidarsi',
    ],
    'form' => [
        'fields' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'package' => 'Collo',
            'lot' => 'Numeri di lotto/serie',
            'on-hand-qty' => 'Quantità a magazzino',
            'storage-category' => 'Categoria di stoccaggio',
        ],
    ],
    'table' => [
        'columns' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'lot' => 'Numeri di lotto/serie',
            'storage-category' => 'Categoria di stoccaggio',
            'quantity' => 'Quantità',
            'package' => 'Collo',
            'on-hand' => 'Quantità a magazzino',
            'unit' => 'Unità',
            'reserved-quantity' => 'Quantità riservata',
            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantità aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
        ],
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi quantità',
                'notification' => [
                    'title' => 'Quantità aggiunta',
                    'body' => 'La quantità è stata aggiunta correttamente.',
                ],
                'before' => [
                    'notification' => [
                        'title' => 'La quantità esiste già',
                        'body' => 'Esiste già una quantità per la stessa configurazione. Aggiorna la quantità esistente.',
                    ],
                ],
            ],
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Importo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
