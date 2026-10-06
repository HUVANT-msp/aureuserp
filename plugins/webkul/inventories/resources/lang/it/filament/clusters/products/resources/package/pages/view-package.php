<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
            'actions' => [
                'without-content' => [
                    'label' => 'Stampa codice a barre',
                ],
                'with-content' => [
                    'label' => 'Stampa il codice a barre con il contenuto',
                ],
            ],
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pacchetto eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare il pacchetto',
                    'body' => 'Il pacchetto non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
