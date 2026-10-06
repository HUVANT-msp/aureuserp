<?php

return [
    'navigation' => [
        'title' => 'Categorie di unità di misura',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                ],
            ],
            'uoms' => [
                'title' => 'Unità di misura',
                'fields' => [
                    'uoms' => 'Unità',
                    'type' => 'Tipo',
                    'name' => 'Unità di misura',
                    'ratio' => 'Rapporto',
                    'rounding' => 'Precisione dell\'arrotondamento',
                ],
                'validations' => [
                    'missing-reference' => 'Questa categoria deve avere un\'unità di misura di riferimento.',
                    'multiple-references' => 'Questa categoria deve avere una sola unità di misura di riferimento.',
                    'ratio-greater-than-zero' => 'Il rapporto di conversione di un\'unità di misura non può essere zero.',
                    'rounding-greater-than-zero' => 'La precisione dell\'arrotondamento deve essere rigorosamente positiva.',
                ],
                'actions' => [
                    'add' => 'Aggiungi unità',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'uoms' => 'Unità di misura',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'created-at' => 'Data creazione',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria unità di misura aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Categoria unità di misura rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorie di unità di misura rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
