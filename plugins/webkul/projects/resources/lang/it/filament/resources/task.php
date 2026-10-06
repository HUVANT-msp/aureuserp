<?php

return [
    'title' => 'Attività',
    'navigation' => [
        'title' => 'Attività',
    ],
    'global-search' => [
        'project' => 'Progetto',
        'customer' => 'Cliente',
        'milestone' => 'Milestone',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'title' => 'Titolo',
                    'title-placeholder' => 'Titolo dell\'attività...',
                    'tags' => 'Etichetta',
                    'name' => 'Nome',
                    'color' => 'Colore',
                    'description' => 'Descrizione',
                    'project' => 'Progetto',
                    'status' => 'Stato',
                    'start_date' => 'Data inizio',
                    'end_date' => 'Data fine',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'project' => 'Progetto',
                    'milestone' => 'Milestone',
                    'milestone-hint-text' => 'Fornisci automaticamente i servizi al raggiungimento di un traguardo collegandolo a una riga dell\'ordine di vendita.',
                    'name' => 'Nome',
                    'deadline' => 'Scadenza',
                    'is-completed' => 'Completato',
                    'customer' => 'Cliente',
                    'assignees' => 'Assegnato',
                    'allocated-hours' => 'Ore stimate',
                    'allocated-hours-helper-text' => 'In ore (ad esempio 1,5 ore significa 1 ora e 30 minuti)',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'id' => 'ID',
            'priority' => 'Priorità',
            'state' => 'Stato',
            'new-state' => 'Nuovo stato',
            'update-state' => 'Aggiorna stato',
            'title' => 'Titolo',
            'project' => 'Progetto',
            'project-placeholder' => 'Compito privato',
            'milestone' => 'Milestone',
            'customer' => 'Cliente',
            'assignees' => 'Assegnato',
            'allocated-time' => 'Tempo assegnato',
            'time-spent' => 'Tempo impiegato',
            'time-remaining' => 'Tempo rimanente',
            'progress' => 'Avanzamento',
            'deadline' => 'Scadenza',
            'tags' => 'Etichetta',
            'stage' => 'Fase',
        ],
        'groups' => [
            'state' => 'Stato',
            'project' => 'Progetto',
            'milestone' => 'Milestone',
            'customer' => 'Cliente',
            'deadline' => 'Scadenza',
            'stage' => 'Fase',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'title' => 'Titolo',
            'priority' => 'Priorità',
            'low' => 'Basso',
            'high' => 'Alto',
            'state' => 'Stato',
            'tags' => 'Etichetta',
            'allocated-hours' => 'Ore stimate',
            'total-hours-spent' => 'Ore totali trascorse',
            'remaining-hours' => 'Ore rimanenti',
            'overtime' => 'Straordinario',
            'progress' => 'Avanzamento',
            'deadline' => 'Scadenza',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'assignees' => 'Assegnato',
            'customer' => 'Cliente',
            'project' => 'Progetto',
            'stage' => 'Fase',
            'milestone' => 'Milestone',
            'company' => 'Azienda',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'update-state' => [
                'modal-heading' => 'Aggiorna lo stato dell\'attività',
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Attività ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attività eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Attività eliminata definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attività ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Attività eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Attività eliminate definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'title' => 'Titolo',
                    'state' => 'Stato',
                    'tags' => 'Etichetta',
                    'priority' => 'Priorità',
                    'description' => 'Descrizione',
                ],
            ],
            'project-information' => [
                'title' => 'Informazioni sul progetto',
                'entries' => [
                    'project' => 'Progetto',
                    'milestone' => 'Milestone',
                    'customer' => 'Cliente',
                    'assignees' => 'Assegnato',
                    'deadline' => 'Scadenza',
                    'stage' => 'Fase',
                ],
            ],
            'time-tracking' => [
                'title' => 'Tracciamento tempo',
                'entries' => [
                    'allocated-time' => 'Tempo assegnato',
                    'time-spent' => 'Tempo impiegato',
                    'time-spent-suffix' => 'Ore',
                    'time-remaining' => 'Tempo rimanente',
                    'time-remaining-suffix' => 'Ore',
                    'progress' => 'Avanzamento',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-at' => 'Data creazione',
                    'created-by' => 'Creato da',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
            'statistics' => [
                'title' => 'Statistiche',
                'entries' => [
                    'sub-tasks' => 'Attività secondarie',
                    'timesheet-entries' => 'Voci della foglio ore',
                ],
            ],
        ],
    ],
];
