<?php

return [
    'notification' => [
        'title' => 'Prodotto aggiornato',
        'body' => 'Aggiornamento completato con successo.',
    ],
    'header-actions' => [
        'update-quantity' => [
            'label' => 'Aggiorna quantità',
            'modal-heading' => 'Aggiorna la quantità del prodotto',
            'modal-submit-action-label' => 'Aggiorna',
            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Quantità a magazzino',
                ],
            ],
        ],
        'delete' => [
            'notification' => [
                'title' => 'Prodotto eliminato',
                'body' => 'Eliminazione completata con successo.',
            ],
        ],
    ],
];
