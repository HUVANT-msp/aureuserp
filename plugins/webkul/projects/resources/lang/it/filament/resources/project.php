<?php

return [
    'navigation' => [
        'title' => 'Progetti',
    ],
    'global-search' => [
        'project-manager' => 'Responsabile del progetto',
        'customer' => 'Cliente',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'Nome del progetto...',
                    'description' => 'Descrizione',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'project-manager' => 'Responsabile del progetto',
                    'customer' => 'Cliente',
                    'start-date' => 'Data inizio',
                    'end-date' => 'Data fine',
                    'allocated-hours' => 'Ore stimate',
                    'allocated-hours-helper-text' => 'In ore (ad esempio 1,5 ore significa 1 ora e 30 minuti)',
                    'tags' => 'Etichetta',
                    'company' => 'Azienda',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'visibility' => 'Visibilità',
                    'visibility-hint-tooltip' => 'Consenti ai dipendenti di accedere al tuo progetto o alle tue attività aggiungendoli come follower. Avranno automaticamente accesso a qualsiasi attività loro assegnata.',
                    'private-description' => 'Solo utenti interni invitati.',
                    'internal-description' => 'Tutti gli utenti interni possono vedere.',
                    'public-description' => 'Utenti del portale ospite e tutti gli utenti interni.',
                    'time-management' => 'Gestione del tempo',
                    'allow-timesheets' => 'Consenti fogli ore',
                    'allow-timesheets-helper-text' => 'Registra il tempo dedicato alle attività e monitora i progressi',
                    'task-management' => 'Gestione delle attività',
                    'allow-milestones' => 'Consenti traguardi',
                    'allow-milestones-helper-text' => 'Monitorare le tappe fondamentali essenziali per raggiungere il successo.',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'customer' => 'Cliente',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'planned-date' => 'Data prevista',
            'remaining-hours' => 'Ore rimanenti',
            'project-manager' => 'Responsabile del progetto',
        ],
        'groups' => [
            'stage' => 'Fase',
            'project-manager' => 'Responsabile del progetto',
            'customer' => 'Cliente',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'name' => 'Nome',
            'visibility' => 'Visibilità',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'allow-timesheets' => 'Consenti fogli ore',
            'allow-milestones' => 'Consenti traguardi',
            'allocated-hours' => 'Ore stimate',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'stage' => 'Fase',
            'customer' => 'Cliente',
            'project-manager' => 'Responsabile del progetto',
            'company' => 'Azienda',
            'creator' => 'Creato da',
            'tags' => 'Etichetta',
        ],
        'actions' => [
            'tasks' => ':count attività',
            'milestones' => ':completed traguardi completati da :all',
            'restore' => [
                'notification' => [
                    'title' => 'Progetto restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Progetto eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Progetto eliminato definitivamente',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Il progetto non può essere eliminato definitivamente',
                        'body' => 'Il progetto è associato ad altri record.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'Nome del progetto...',
                    'description' => 'Descrizione',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'project-manager' => 'Responsabile del progetto',
                    'customer' => 'Cliente',
                    'project-timeline' => 'Cronologia del progetto',
                    'allocated-hours' => 'Ore stimate',
                    'allocated-hours-suffix' => 'Ore',
                    'remaining-hours' => 'Ore rimanenti',
                    'remaining-hours-suffix' => 'Ore',
                    'current-stage' => 'Fase attuale',
                    'tags' => 'Etichetta',
                ],
            ],
            'statistics' => [
                'title' => 'Statistiche',
                'entries' => [
                    'total-tasks' => 'Compiti totali',
                    'milestones-progress' => 'Progresso fondamentale',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-at' => 'Data creazione',
                    'created-by' => 'Creato da',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
            'settings' => [
                'title' => 'Impostazione del progetto',
                'entries' => [
                    'visibility' => 'Visibilità',
                    'timesheets-enabled' => 'Fogli ore abilitate',
                    'milestones-enabled' => 'Traguardi abilitati',
                ],
            ],
        ],
    ],
];
