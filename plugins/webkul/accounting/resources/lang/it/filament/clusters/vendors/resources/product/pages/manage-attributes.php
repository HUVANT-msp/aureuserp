<?php

return [
    'title' => 'Attributi',
    'form' => [
        'attribute' => 'Attributo',
        'values' => 'Valori',
    ],
    'table' => [
        'description' => 'Avviso: l\'aggiunta o la rimozione di attributi eliminerà e ricreerà le varianti esistenti e comporterà la perdita delle potenziali personalizzazioni.',
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi attributo',
                'notification' => [
                    'title' => 'Attributo creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'columns' => [
            'attribute' => 'Attributo',
            'values' => 'Valori',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attributo aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attributo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
