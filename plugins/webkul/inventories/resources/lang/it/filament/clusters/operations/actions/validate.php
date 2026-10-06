<?php

return [
    'label' => 'Convalidare',
    'modal-heading' => 'Creare un ordine in sospeso?',
    'modal-description' => 'Crea un ordine arretrato se i prodotti rimanenti verranno elaborati in seguito. Altrimenti, non generare un ordine arretrato.',
    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Nessun ordine residuo',
        ],
    ],
    'notification' => [
        'error' => [
            'title' => 'Convalida non riuscita',
        ],
        'warning' => [
            'lines-missing' => [
                'title' => 'Non ci sono importi riservati',
                'body' => 'Non ci sono importi riservati per il trasferimento.',
            ],
            'no-quantities-reserved' => [
                'title' => 'Non ci sono importi riservati',
                'body' => 'Non ci sono importi riservati per il trasferimento.',
            ],
            'lot-missing' => [
                'title' => 'Fornire il numero di lotto/serie',
                'body' => 'È necessario fornire un numero di lotto/serie per i prodotti :products.',
            ],
            'serial-qty' => [
                'title' => 'Numero di serie già assegnato',
                'body' => 'Il numero di serie è già stato assegnato ad un altro prodotto.',
            ],
            'partial-package' => [
                'title' => 'Impossibile spostare lo stesso contenuto del pacchetto',
                'body' => 'Non è possibile spostare lo stesso contenuto del pacco più di una volta all\'interno di un singolo trasferimento o dividere il pacco tra due posizioni.',
            ],
        ],
    ],
];
