<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Dettagli dell\'attività',
                'fields' => [
                    'activity-type' => 'Tipo di attività',
                    'summary' => 'Sommario',
                    'note' => 'Nota',
                ],
            ],
            'assignment' => [
                'title' => 'Compito',
                'fields' => [
                    'assignment' => 'Compito',
                    'assignee' => 'Assegnato',
                ],
            ],
            'delay-information' => [
                'title' => 'Informazioni sul ritardo',
                'fields' => [
                    'delay-count' => 'Importo del ritardo',
                    'delay-unit' => 'Unità di ritardo',
                    'delay-from' => 'Ritardo da',
                    'delay-from-helper-text' => 'Origine del calcolo del ritardo',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'activity-type' => 'Tipo di attività',
            'summary' => 'Sommario',
            'assignment' => 'Compito',
            'assigned-to' => 'Assegnato a',
            'interval' => 'Intervallo',
            'delay-unit' => 'Unità di ritardo',
            'delay-from' => 'Ritardo da',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'activity-type' => 'Tipo di attività',
            'assignment' => 'Compito',
            'assigned-to' => 'Assegnato a',
            'interval' => 'Intervallo',
            'delay-unit' => 'Unità di ritardo',
            'delay-from' => 'Ritardo da',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'activity-type' => 'Tipo di attività',
            'activity-status' => 'Stato dell\'attività',
            'has-delay' => 'È tardi',
        ],
        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Modello del piano di attività creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Modello di attività aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Modello di attività eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modelli di attività rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
