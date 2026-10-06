<?php

return [
    'title' => 'Reparti',
    'navigation' => [
        'title' => 'Reparti',
        'group' => 'Collaboratori',
    ],
    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Tipo di attività',
                    'name-tooltip' => 'Inserisci il nome ufficiale del tipo di attività',
                    'action' => 'Azione',
                    'default-user' => 'Utente predefinito',
                    'summary' => 'Sommario',
                    'note' => 'Nota',
                ],
            ],
            'delay-information' => [
                'title' => 'Informazioni sul ritardo',
                'fields' => [
                    'delay-count' => 'Importo del ritardo',
                    'delay-unit' => 'Unità di ritardo',
                    'delay-form' => 'Origine del ritardo',
                    'delay-form-helper-text' => 'Origine del calcolo del ritardo',
                ],
            ],
            'advanced-information' => [
                'title' => 'Informazioni avanzate',
                'fields' => [
                    'icon' => 'Icona',
                    'decoration-type' => 'Tipo di decorazione',
                    'chaining-type' => 'Tipo di concatenamento',
                    'suggest' => 'Suggerisci',
                    'trigger' => 'Innesco',
                ],
            ],
            'status-and-configuration-information' => [
                'title' => 'Stato e impostazioni',
                'fields' => [
                    'status' => 'Stato',
                    'keep-done-activities' => 'Mantenere le attività completate',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Tipo di attività',
            'summary' => 'Sommario',
            'planned-in' => 'Pianificato',
            'type' => 'Tipo',
            'action' => 'Azione',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'action-category' => 'Categoria di azione',
            'status' => 'Stato',
            'delay-count' => 'Importo del ritardo',
            'delay-unit' => 'Unità di ritardo',
            'delay-source' => 'Origine del ritardo',
            'associated-model' => 'Modello associato',
            'chaining-type' => 'Tipo di concatenamento',
            'decoration-type' => 'Tipo di decorazione',
            'default-user' => 'Utente predefinito',
            'creation-date' => 'Data di creazione',
            'last-update' => 'Ultimo aggiornamento',
        ],
        'filters' => [
            'action' => 'Azione',
            'status' => 'Stato',
            'has-delay' => 'È tardi',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo di attività ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di attività rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo di attività rimosso definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il tipo di attività',
                        'body' => 'Il tipo di attività non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipi di attività ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipi di attività eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Tipi di attività rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Tipo di attività',
                    'name-tooltip' => 'Inserisci il nome ufficiale del tipo di attività',
                    'action' => 'Azione',
                    'default-user' => 'Utente predefinito',
                    'plugin' => 'Modulo',
                    'summary' => 'Sommario',
                    'note' => 'Nota',
                ],
            ],
            'delay-information' => [
                'title' => 'Informazioni sul ritardo',
                'entries' => [
                    'delay-count' => 'Importo del ritardo',
                    'delay-unit' => 'Unità di ritardo',
                    'delay-form' => 'Origine del ritardo',
                    'delay-form-helper-text' => 'Origine del calcolo del ritardo',
                ],
            ],
            'advanced-information' => [
                'title' => 'Informazioni avanzate',
                'entries' => [
                    'icon' => 'Icona',
                    'decoration-type' => 'Tipo di decorazione',
                    'chaining-type' => 'Tipo di concatenamento',
                    'suggest' => 'Suggerisci',
                    'trigger' => 'Innesco',
                ],
            ],
            'status-and-configuration-information' => [
                'title' => 'Stato e impostazioni',
                'entries' => [
                    'status' => 'Stato',
                    'keep-done-activities' => 'Mantenere le attività completate',
                ],
            ],
        ],
    ],
];
