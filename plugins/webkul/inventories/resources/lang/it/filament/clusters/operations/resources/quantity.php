<?php

return [
    'navigation' => [
        'title' => 'Quantità',
        'group' => 'Impostazioni',
    ],
    'form' => [
        'fields' => [
            'location' => 'Ubicazione',
            'product' => 'Prodotto',
            'package' => 'Collo',
            'lot' => 'Numeri di lotto/serie',
            'counted-qty' => 'Quantità contata',
            'scheduled-at' => 'Previsto per',
            'storage-category' => 'Categoria di stoccaggio',
        ],
    ],
    'table' => [
        'columns' => [
            'location' => 'Ubicazione',
            'product' => 'Prodotto',
            'product-category' => 'Categoria prodotto',
            'lot' => 'Numeri di lotto/serie',
            'storage-category' => 'Categoria di stoccaggio',
            'available-quantity' => 'Quantità disponibile',
            'quantity' => 'Quantità',
            'package' => 'Collo',
            'last-counted-at' => 'Ultimo conteggio',
            'on-hand' => 'Quantità a magazzino',
            'uom' => 'UoM',
            'counted' => 'Quantità contata',
            'difference' => 'Differenza',
            'scheduled-at' => 'Previsto per',
            'user' => 'Utente',
            'company' => 'Azienda',
            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantità aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
        ],
        'groups' => [
            'product' => 'Prodotto',
            'product-category' => 'Categoria prodotto',
            'location' => 'Ubicazione',
            'storage-category' => 'Categoria di stoccaggio',
            'lot' => 'Numeri di lotto/serie',
            'company' => 'Azienda',
            'package' => 'Collo',
        ],
        'filters' => [
            'product' => 'Prodotto',
            'uom' => 'Unità di misura',
            'product-category' => 'Categoria prodotto',
            'location' => 'Ubicazione',
            'storage-category' => 'Categoria di stoccaggio',
            'lot' => 'Numeri di lotto/serie',
            'company' => 'Azienda',
            'package' => 'Collo',
            'on-hand-quantity' => 'Quantità a magazzino',
            'difference-quantity' => 'Importo della differenza',
            'incoming-at' => 'Ingresso su',
            'scheduled-at' => 'Previsto per',
            'user' => 'Utente',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'creator' => 'Creato da',
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
                        'body' => 'Esiste già una quantità per questa configurazione. Aggiorna la quantità esistente.',
                    ],
                ],
            ],
        ],
        'actions' => [
            'apply' => [
                'label' => 'Applicare',
                'notification' => [
                    'title' => 'Modifiche di quantità applicate',
                    'body' => 'Le modifiche alla quantità sono state applicate correttamente.',
                ],
            ],
            'clear' => [
                'label' => 'Elimina',
                'notification' => [
                    'title' => 'Modifiche alla quantità rimosse',
                    'body' => 'Le modifiche alla quantità sono state rimosse correttamente.',
                ],
            ],
        ],
    ],
];
