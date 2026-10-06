<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company' => 'Azienda',
                'country' => 'Paese',
                'name' => 'Nome',
                'preceding-subtotal' => 'Subtotale precedente',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'company' => 'Azienda',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'name' => 'Nome',
            'preceding-subtotal' => 'Subtotale precedente',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'company' => 'Azienda',
            'country' => 'Paese',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Gruppo fiscale eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il gruppo fiscale',
                        'body' => 'Il gruppo fiscale non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Gruppi fiscali eliminati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i gruppi fiscali',
                        'body' => 'Impossibile eliminare i gruppi fiscali perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'company' => 'Azienda',
                'country' => 'Paese',
                'name' => 'Nome',
                'preceding-subtotal' => 'Subtotale precedente',
            ],
        ],
    ],
];
