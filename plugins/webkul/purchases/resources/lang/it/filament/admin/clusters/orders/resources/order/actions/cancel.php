<?php

return [
    'label' => 'Annulla',
    'action' => [
        'notification' => [
            'warning' => [
                'receipts' => [
                    'title' => 'L\'ordine non può essere annullato',
                    'body' => 'L\'ordine non può essere annullato perché contiene ricevute già effettuate.',
                ],
                'bills' => [
                    'title' => 'L\'ordine non può essere annullato',
                    'body' => 'L\'ordine non può essere annullato. È necessario prima annullare le relative fatture fornitore.',
                ],
            ],
            'success' => [
                'title' => 'Ordine annullato',
                'body' => 'L\'ordine è stato annullato con successo.',
            ],
        ],
    ],
];
