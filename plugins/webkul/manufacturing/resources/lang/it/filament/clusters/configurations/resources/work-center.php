<?php

return [
    'navigation' => [
        'title' => 'Centri di lavoro',
        'group' => 'Configurazione',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'pag. per esempio. Linea di montaggio 1',
                    'code' => 'Codice',
                    'code-placeholder' => 'pag. per esempio. LE1',
                    'working-state' => 'Stato operativo',
                    'color' => 'Colore',
                    'tags' => 'Etichetta',
                    'alternative-work-centers' => 'Centri di lavoro alternativi',
                    'company' => 'Azienda',
                    'calendar' => 'Orario di lavoro',
                ],
            ],
            'information' => [
                'title' => 'Informazioni generali',
                'fieldsets' => [
                    'production-information' => 'Informazioni sulla produzione',
                    'costing-information' => 'Informazioni sui costi',
                ],
                'fields' => [
                    'default-capacity' => 'Capacità predefinita',
                    'time-efficiency' => 'Efficienza temporale',
                    'oee-target' => 'Obiettivo OEE',
                    'costs-per-hour' => 'Costo orario',
                    'cost-suffix' => 'all\'ora',
                    'setup-time' => 'Tempo di preparazione',
                    'cleanup-time' => 'Tempo di pulizia',
                    'time-suffix' => 'minuti',
                ],
            ],
            'description' => [
                'title' => 'Descrizione',
                'fields' => [
                    'note' => 'Descrizione',
                    'note-placeholder' => 'Descrizione del centro di lavoro...',
                ],
            ],
            'specific-capacity' => [
                'title' => 'Capacità specifica',
                'fields' => [
                    'records' => 'Capacità specifica',
                ],
                'columns' => [
                    'product' => 'Prodotto',
                    'product-uom' => 'UoM',
                    'capacity' => 'Capacità',
                    'setup-time' => 'Tempo di preparazione',
                    'cleanup-time' => 'Tempo di pulizia',
                ],
                'actions' => [
                    'add' => 'Aggiungi una riga',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Codice',
            'company' => 'Azienda',
            'calendar' => 'Orario di lavoro',
            'working-state' => 'Stato operativo',
            'default-capacity' => 'Capacità',
            'time-efficiency' => 'Efficienza',
            'costs-per-hour' => 'Costo orario',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'company' => 'Azienda',
        ],
        'filters' => [
            'company' => 'Azienda',
            'working-state' => 'Stato operativo',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centro di lavoro restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Centro di lavoro archiviato',
                    'body' => 'Il centro di lavoro è stato archiviato correttamente.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centro di lavoro eliminato',
                        'body' => 'Il centro di lavoro è stato eliminato definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il centro di lavoro',
                        'body' => 'Non è possibile eliminare il centro di lavoro perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centri di lavoro restaurati',
                    'body' => 'I centri di lavoro selezionati sono stati ripristinati con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Centri di lavoro archiviati',
                    'body' => 'I centri di lavoro selezionati sono stati archiviati con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centri di lavoro eliminati',
                        'body' => 'I centri di lavoro selezionati sono stati eliminati definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i centri di lavoro',
                        'body' => 'Sono in uso uno o più centri di lavoro selezionati.',
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
                    'name' => 'Nome del centro di lavoro',
                    'code' => 'Codice',
                    'working-state' => 'Stato operativo',
                    'tags' => 'Etichetta',
                    'alternative-work-centers' => 'Centri di lavoro alternativi',
                    'company' => 'Azienda',
                    'calendar' => 'Orario di lavoro',
                ],
            ],
            'information' => [
                'title' => 'Informazioni generali',
                'fieldsets' => [
                    'production-information' => 'Informazioni sulla produzione',
                    'costing-information' => 'Informazioni sui costi',
                ],
                'entries' => [
                    'default-capacity' => 'Capacità predefinita',
                    'time-efficiency' => 'Efficienza temporale',
                    'oee-target' => 'Obiettivo OEE',
                    'costs-per-hour' => 'Costo orario',
                    'cost-suffix' => 'per centro di lavoro',
                    'setup-time' => 'Tempo di preparazione',
                    'cleanup-time' => 'Tempo di pulizia',
                    'time-suffix' => 'minuti',
                ],
            ],
            'description' => [
                'title' => 'Descrizione',
                'entries' => [
                    'note' => 'Descrizione',
                ],
            ],
            'specific-capacity' => [
                'title' => 'Capacità specifiche',
                'columns' => [
                    'product' => 'Prodotto',
                    'product-uom' => 'UoM',
                    'capacity' => 'Capacità',
                    'setup-time' => 'Tempo di preparazione',
                    'cleanup-time' => 'Tempo di pulizia',
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
