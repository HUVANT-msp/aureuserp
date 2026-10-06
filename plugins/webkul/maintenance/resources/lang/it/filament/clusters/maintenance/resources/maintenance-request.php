<?php

return [
    'navigation' => [
        'group' => 'Manutenzione',
        'title' => 'Richieste di manutenzione',
    ],
    'form' => [
        'sections' => [
            'request' => [
                'title' => 'Applicazione',
                'fields' => [
                    'name' => 'Applicazione',
                    'name-placeholder' => 'pag. per esempio. Lo schermo non funziona',
                    'equipment' => 'Squadra',
                    'category' => 'Categoria',
                    'requested-at' => 'Data della domanda',
                    'requested-at-hint-tooltip' => 'La data in cui è stata segnalata la richiesta di manutenzione.',
                    'maintenance-type' => 'Tipo di manutenzione',
                    'recurrent' => 'Ricorrente',
                    'repeat-every' => 'Ripeti ciascuno',
                    'maintenance-type-options' => [
                        'corrective' => 'Correttivo',
                        'preventive' => 'Preventivo',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title' => 'Nota',
                        'fields' => [
                            'description' => 'Note interne',
                            'description-placeholder' => 'Note interne',
                        ],
                    ],
                    'instructions' => [
                        'title' => 'Istruzioni',
                        'fields' => [
                            'instruction-type' => 'Tipo di istruzione',
                            'instruction-type-options' => [
                                'pdf' => 'PDF',
                                'google-slide' => 'Diapositiva Google',
                                'text' => 'Testo',
                            ],
                            'instruction-pdf' => 'PDF',
                            'instruction-google-slide' => 'Diapositiva Google',
                            'instruction-text' => 'Descrizione',
                            'instruction-text-placeholder' => 'Descrizione',
                        ],
                    ],
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'team' => 'Team',
                    'responsible' => 'Responsabile',
                    'scheduled-at' => 'Data prevista',
                    'scheduled-at-hint-tooltip' => 'La data e l\'ora in cui è previsto l\'inizio dei lavori di manutenzione.',
                    'duration' => 'Durata',
                    'duration-hint-tooltip' => 'Durata prevista della manutenzione.',
                    'duration-suffix' => 'ore',
                    'priority' => 'Priorità',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Problemi',
            'creator' => 'Creato dall\'utente',
            'technician' => 'Tecnico',
            'category' => 'Categoria',
            'stage' => 'Fase',
            'company' => 'Azienda',
        ],
        'groups' => [
            'stage' => 'Fase',
            'assigned-to' => 'Assegnato a',
            'category' => 'Categoria',
            'created-by' => 'Creato da',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Richiesta di manutenzione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Richiesta di manutenzione depositata',
                    'body' => 'La richiesta di manutenzione è stata depositata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Richiesta di manutenzione eliminata',
                        'body' => 'La richiesta di manutenzione è stata rimossa definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la richiesta di manutenzione',
                        'body' => 'A questa richiesta di manutenzione fa riferimento un altro record.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Richieste di manutenzione ripristinate',
                    'body' => 'Le richieste di manutenzione selezionate sono state ripristinate con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Richieste di manutenzione archiviate',
                    'body' => 'Le richieste di manutenzione selezionate sono state archiviate con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'request' => [
                'title' => 'Applicazione',
                'entries' => [
                    'name' => 'Applicazione',
                    'equipment' => 'Squadra',
                    'category' => 'Categoria',
                    'requested-at' => 'Data della domanda',
                    'maintenance-type' => 'Tipo di manutenzione',
                    'instruction-type' => 'Tipo di istruzione',
                    'instruction-pdf' => 'PDF',
                    'instruction-google-slide' => 'Diapositiva Google',
                    'description' => 'Note interne',
                    'instruction-text' => 'Descrizione',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'team' => 'Team',
                    'responsible' => 'Responsabile',
                    'scheduled-at' => 'Data prevista',
                    'duration' => 'Durata',
                    'duration-suffix' => 'ore',
                    'priority' => 'Priorità',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
];
