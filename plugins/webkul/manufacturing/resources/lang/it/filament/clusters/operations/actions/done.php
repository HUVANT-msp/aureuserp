<?php

return [
    'label' => 'Produci tutto',
    'partial-label' => 'Produrre',
    'modal' => [
        'consumption-warning' => [
            'heading' => 'Avviso consumi',
            'description' => 'Alcuni prodotti sono stati consumati in quantità diverse da quelle previste. Vuoi convalidare l\'ordine di produzione con le quantità attuali?',
            'form' => [
                'product' => 'Prodotto',
                'to-consume' => 'Consumare',
                'consumed' => 'Consumato',
                'uom' => 'Unità di misura',
            ],
            'actions' => [
                'confirm' => [
                    'label' => 'Conferma',
                ],
                'set-quantities' => [
                    'label' => 'Imposta le quantità e conferma',
                ],
            ],
        ],
        'produced-warning' => [
            'heading' => 'La quantità prodotta è diversa da quella prevista',
            'description' => 'La quantità prodotta è diversa da quella prevista. Vuoi confermare l\'ordine di produzione con la quantità attuale?',
        ],
    ],
    'notification' => [
        'success' => [
            'title' => 'Ordine di produzione completato',
            'body' => 'L\'ordine di produzione è stato completato con successo.',
        ],
    ],
];
