<?php

return [
    'navigation' => [
        'title' => 'Operazioni',
        'group' => 'Configurazione',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Operazione',
                    'name-placeholder' => 'P. per esempio. Corte',
                    'bill-of-material' => 'Distinta base',
                    'work-center' => 'Centro di lavoro',
                    'apply-on-variants' => 'Applicare nelle varianti',
                    'company' => 'Azienda',
                    'blocked-by' => 'Bloccato da',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'time-mode' => 'Calcolo della durata',
                    'time-mode-batch' => 'Basato su',
                    'time-mode-batch-prefix' => 'l\'ultimo',
                    'time-mode-batch-suffix' => 'ordini di lavoro',
                    'manual-cycle-time' => 'Durata predefinita',
                    'manual-cycle-time-suffix' => 'minuti',
                ],
            ],
            'worksheet' => [
                'title' => 'Foglio di lavoro',
                'fields' => [
                    'worksheet' => 'Foglio di lavoro',
                    'pdf' => 'PDF',
                    'google-slide' => 'Diapositiva Google',
                    'google-slide-placeholder' => 'Collegamento alla diapositiva di Google',
                    'description' => 'Descrizione',
                    'description-placeholder' => 'Descrizione dell\'operazione...',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Operazione',
            'bill-of-material' => 'Distinta base',
            'work-center' => 'Centro di lavoro',
            'time-mode' => 'Calcolo della durata',
            'manual-cycle-time' => 'Durata predefinita',
            'worksheet-type' => 'Foglio di lavoro',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'work-center' => 'Centro di lavoro',
            'time-mode' => 'Calcolo della durata',
            'worksheet-type' => 'Foglio di lavoro',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operazione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operazione archiviata',
                    'body' => 'L\'operazione è stata archiviata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operazione rimossa',
                        'body' => 'L\'operazione è stata rimossa definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare l\'operazione',
                        'body' => 'Non è possibile eliminare l\'operazione perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operazioni ripristinate',
                    'body' => 'Le operazioni selezionate sono state ripristinate con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operazioni archiviate',
                    'body' => 'Le operazioni selezionate sono state archiviate con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operazioni eliminate',
                        'body' => 'Le operazioni selezionate sono state eliminate definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le operazioni',
                        'body' => 'Una o più operazioni selezionate sono in uso.',
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
                    'name' => 'Operazione',
                    'bill-of-material' => 'Distinta base',
                    'work-center' => 'Centro di lavoro',
                    'apply-on-variants' => 'Applicare nelle varianti',
                    'company' => 'Azienda',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'time-mode' => 'Calcolo della durata',
                    'time-mode-batch' => 'Basato su',
                    'manual-cycle-time' => 'Durata predefinita',
                    'manual-cycle-time-suffix' => 'minuti',
                ],
            ],
            'worksheet' => [
                'title' => 'Foglio di lavoro',
                'entries' => [
                    'worksheet' => 'Foglio di lavoro',
                    'pdf' => 'PDF',
                    'google-slide' => 'Diapositiva Google',
                    'description' => 'Descrizione',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-by' => 'Creato da',
                    'created-at' => 'Data creazione',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
        ],
    ],
];
