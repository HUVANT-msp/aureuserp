<?php

return [
    'title' => 'Fasi',
    'navigation' => [
        'title' => 'Fasi',
        'group' => 'Ruoli aziendali',
    ],
    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'stage-name' => 'Nome d\'arte',
                    'sort' => 'Ordine di sequenza',
                    'requirements' => 'Requisiti',
                ],
            ],
            'tooltips' => [
                'title' => 'Suggerimenti',
                'description' => 'Definire l\'etichetta personalizzata per lo stato della domanda.',
                'fields' => [
                    'gray-label' => 'Etichetta grigia',
                    'gray-label-tooltip' => 'L\'etichetta per lo stato grigio.',
                    'red-label' => 'Etichetta rossa',
                    'red-label-tooltip' => 'L\'etichetta per lo stato rosso.',
                    'green-label' => 'Etichetta verde',
                    'green-label-tooltip' => 'L\'etichetta per lo stato verde.',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'job-positions' => 'Ruoli aziendali',
                    'folded' => 'Pieghevole',
                    'hired-stage' => 'Fase di assunzione',
                    'default-stage' => 'Fase predefinita',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome d\'arte',
            'hired-stage' => 'Fase di assunzione',
            'default-stage' => 'Fase predefinita',
            'folded' => 'Pieghevole',
            'job-positions' => 'Ruoli aziendali',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome d\'arte',
            'job-position' => 'Ruolo aziendale',
            'folded' => 'Pieghevole',
            'gray-label' => 'Etichetta grigia',
            'red-label' => 'Etichetta rossa',
            'green-label' => 'Etichetta verde',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'job-position' => 'Ruolo aziendale',
            'stage-name' => 'Nome d\'arte',
            'folded' => 'Pieghevole',
            'gray-label' => 'Etichetta grigia',
            'red-label' => 'Etichetta rossa',
            'green-label' => 'Etichetta verde',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tappe eliminate',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le fasi',
                        'body' => 'Le fasi non possono essere eliminate perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tappe eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'label' => 'Nuova fase',
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'stage-name' => 'Nome d\'arte',
                    'sort' => 'Ordine di sequenza',
                    'requirements' => 'Requisiti',
                ],
            ],
            'tooltips' => [
                'title' => 'Suggerimenti',
                'description' => 'Definire l\'etichetta personalizzata per lo stato della domanda.',
                'entries' => [
                    'gray-label' => 'Etichetta grigia',
                    'gray-label-tooltip' => 'L\'etichetta per lo stato grigio.',
                    'red-label' => 'Etichetta rossa',
                    'red-label-tooltip' => 'L\'etichetta per lo stato rosso.',
                    'green-label' => 'Etichetta verde',
                    'green-label-tooltip' => 'L\'etichetta per lo stato verde.',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'job-positions' => 'Ruolo aziendale',
                    'folded' => 'Pieghevole',
                    'hired-stage' => 'Fase di assunzione',
                    'default-stage' => 'Fase predefinita',
                ],
            ],
        ],
    ],
];
