<?php

return [
    'header-actions' => [
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
];
