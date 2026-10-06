<?php

return [
    'title' => 'Prodotti modello ordine',
    'navigation' => [
        'title' => 'Prodotti modello ordine',
        'group' => 'Ordini di vendita',
    ],
    'global-search' => [
        'name' => 'Nome',
    ],
    'form' => [
        'fields' => [
            'sort' => 'Ordina',
            'order-template' => 'Modello d\'ordine',
            'company' => 'Azienda',
            'product' => 'Prodotto',
            'product-uom' => 'Unità di misura del prodotto',
            'creator' => 'Creato da',
            'display-type' => 'Tipo di visualizzazione',
            'name' => 'Nome',
            'quantity' => 'Quantità',
        ],
    ],
    'table' => [
        'columns' => [
            'sort' => 'Ordina',
            'order-template' => 'Modello d\'ordine',
            'company' => 'Azienda',
            'product' => 'Prodotto',
            'product-uom' => 'Unità di misura del prodotto',
            'created-by' => 'Creato da',
            'display-type' => 'Tipo di visualizzazione',
            'name' => 'Nome',
            'quantity' => 'Quantità',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Prodotti modello ordine aggiornati',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Prodotti modello ordine eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Prodotti modello ordine eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'sort' => 'Ordinamento',
            'order-template' => 'Modello d\'ordine',
            'company' => 'Azienda',
            'product' => 'Prodotto',
            'product-uom' => 'Unità di misura del prodotto',
            'display-type' => 'Tipo di visualizzazione',
            'name' => 'Nome',
            'quantity' => 'Quantità',
        ],
    ],
];
