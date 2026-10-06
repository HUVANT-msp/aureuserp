<?php

return [
    'title' => 'Competenze',
    'navigation' => [
        'title' => 'Competenze',
    ],
    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Dettagli del concorso',
                'fields' => [
                    'employee' => 'Collaboratore',
                    'skill' => 'Concorrenza',
                    'skill-level' => 'Livello',
                    'skill-type' => 'Tipo di competizione',
                ],
            ],
            'addition-information' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'created-by' => 'Creato da',
                    'updated-by' => 'Modificato da',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'employee' => 'Collaboratore',
            'skill' => 'Concorrenza',
            'skill-level' => 'Livello',
            'skill-type' => 'Tipo di competizione',
            'user' => 'Utente',
            'proficiency' => 'Dominio',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'employee' => 'Collaboratore',
            'skill' => 'Concorrenza',
            'skill-level' => 'Livello',
            'skill-type' => 'Tipo di competizione',
            'user' => 'Utente',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'employee' => 'Collaboratore',
            'skill-type' => 'Tipo di competizione',
        ],
    ],
    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Dettagli del concorso',
                'entries' => [
                    'employee' => 'Collaboratore',
                    'skill' => 'Concorrenza',
                    'skill-level' => 'Livello',
                    'skill-type' => 'Tipo di competizione',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'created-by' => 'Creato da',
                    'updated-by' => 'Modificato da',
                ],
            ],
        ],
    ],
];
