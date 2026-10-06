<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Categoria rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare la categoria',
                    'body' => 'La categoria non può essere eliminata perché è in uso.',
                ],
            ],
        ],
    ],
];
