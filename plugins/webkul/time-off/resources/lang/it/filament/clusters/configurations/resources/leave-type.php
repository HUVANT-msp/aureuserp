<?php

return [
    'title' => 'Tipo di assenza',
    'navigation' => [
        'title' => 'Tipo di assenza',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Titolo',
                    'approval' => 'Approvazione',
                    'requires-allocation' => 'Richiede un incarico',
                    'employee-requests' => 'Richieste dei dipendenti',
                    'display-option' => 'Opzione di visualizzazione',
                ],
            ],
            'display-option' => [
                'title' => 'Opzione di visualizzazione',
                'fields' => [
                    'color' => 'Colore',
                ],
            ],
            'configuration' => [
                'title' => 'Configurazione',
                'fields' => [
                    'notified-time-off-officers' => 'Persone responsabili delle assenze comunicate',
                    'take-time-off-in' => 'Prenditi un congedo',
                    'public-holiday-included' => 'Festivi inclusi',
                    'allow-to-attach-supporting-document' => 'Consenti di allegare documento giustificativo',
                    'show-on-dashboard' => 'Mostra sul pannello',
                    'allow-negative-cap' => 'Consenti limite negativo',
                    'kind-off-time' => 'Tipo di ora',
                    'max-negative-cap' => 'Limite massimo negativo',
                    'kind-of-time' => 'Tipo di assenza',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Azienda',
            'color' => 'Colore',
            'notified-time-officers' => 'Gestori del tempo avvisati',
            'time-off-approval' => 'Approvazione dell\'assenza',
            'requires-allocation' => 'Richiede un incarico',
            'allocation-approval' => 'Approvazione dell\'incarico',
            'employee-request' => 'Applicazione dei dipendenti',
        ],
        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Azienda',
            'time-off-approval' => 'Approvazione dell\'assenza',
            'requires-allocation' => 'Richiede un incarico',
            'time-type' => 'Tipo di ora',
            'request-unit' => 'Richiedi unità',
            'created-by' => 'Creato da',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di assenza rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Tipo di assenza ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo di assenza ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di assenza rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo di assenza rimosso definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il tipo di assenza',
                        'body' => 'Il tipo di assenza non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Titolo',
                    'approval' => 'Approvazione',
                    'requires-allocation' => 'Richiede un incarico',
                    'employee-requests' => 'Richieste dei dipendenti',
                    'display-option' => 'Opzione di visualizzazione',
                ],
            ],
            'display-option' => [
                'title' => 'Opzione di visualizzazione',
                'entries' => [
                    'color' => 'Colore',
                ],
            ],
            'configuration' => [
                'title' => 'Configurazione',
                'entries' => [
                    'notified-time-off-officers' => 'Persone responsabili delle assenze comunicate',
                    'take-time-off-in' => 'Prenditi un congedo',
                    'public-holiday-included' => 'Festivi inclusi',
                    'allow-to-attach-supporting-document' => 'Consenti di allegare documento giustificativo',
                    'show-on-dashboard' => 'Mostra sul pannello',
                    'kind-off-time' => 'Tipo di ora',
                    'max-negative-cap' => 'Limite massimo negativo',
                    'kind-of-time' => 'Tipo di assenza',
                ],
            ],
        ],
    ],
];
