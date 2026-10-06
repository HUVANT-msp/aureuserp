<?php

return [
    'title' => 'Squadre di vendita',
    'navigation' => [
        'title' => 'Squadre di vendita',
    ],
    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Team di vendita',
                'status' => 'Stato',
                'fieldset' => [
                    'team-details' => [
                        'title' => 'Dettagli della squadra',
                        'fields' => [
                            'team-leader' => 'Caposquadra',
                            'company' => 'Azienda',
                            'invoiced-target' => 'Obiettivo fatturato',
                            'invoiced-target-suffix' => '/ Mese',
                            'color' => 'Colore',
                            'members' => 'Membri',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'company' => 'Azienda',
            'team-leader' => 'Caposquadra',
            'name' => 'Nome',
            'status' => 'Stato',
            'invoiced-target' => 'Obiettivo fatturato',
            'color' => 'Colore',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'name' => 'Nome',
            'team-leader' => 'Caposquadra',
            'company' => 'Azienda',
            'created-by' => 'Creato da',
            'updated-at' => 'Ultima modifica',
            'created-at' => 'Data creazione',
        ],
        'groups' => [
            'name' => 'Nome',
            'company' => 'Compagnia',
            'team-leader' => 'Caposquadra',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Team di vendita ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminato il team di vendita',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Il team di vendita è stato rimosso definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attrezzature di vendita rinnovate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati i team di vendita',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Team di vendita rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Creati team di vendita',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Team di vendita',
                'status' => 'Stato',
                'fieldset' => [
                    'team-details' => [
                        'title' => 'Dettagli della squadra',
                        'entries' => [
                            'team-leader' => 'Caposquadra',
                            'company' => 'Azienda',
                            'invoiced-target' => 'Obiettivo fatturato',
                            'invoiced-target-suffix' => '/ Mese',
                            'color' => 'Colore',
                            'members' => 'Membri',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
