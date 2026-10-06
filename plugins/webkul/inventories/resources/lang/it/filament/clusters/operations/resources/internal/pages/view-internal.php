<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Stampa',
        ],
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Trasferimento interno rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile eliminare il trasferimento interno',
                    'body' => 'Il trasferimento interno non può essere eliminato perché è attualmente in uso.',
                ],
            ],
        ],
    ],
];
