<?php

return [
    'navigation' => [
        'title' => 'Squadra',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'note' => 'Descrizione',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'category' => 'Categoria dell\'attrezzatura',
                    'team' => 'Squadra di manutenzione',
                    'company' => 'Azienda',
                    'technician' => 'Tecnico',
                    'owner' => 'Proprietario',
                    'location' => 'Utilizzato sul posto',
                ],
            ],
            'product-information' => [
                'title' => 'Informazioni sul prodotto',
                'fields' => [
                    'partner' => 'Fornitore',
                    'partner-ref' => 'Riferimento del fornitore',
                    'model' => 'Modello',
                    'serial-no' => 'Numero di serie',
                    'effective-date' => 'Data di entrata in vigore',
                    'effective-date-hint-tooltip' => 'Viene utilizzato come punto di partenza per calcolare il tempo medio tra i guasti.',
                    'cost' => 'Costo',
                    'warranty-date' => 'Data di scadenza della garanzia',
                ],
            ],
            'maintenance' => [
                'title' => 'Manutenzione',
                'fields' => [
                    'expected-mtbf' => 'Tempo medio previsto tra i guasti',
                ],
                'suffixes' => [
                    'days' => 'giorni',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome della squadra',
            'owner' => 'Proprietario',
            'serial-no' => 'Numero di serie',
            'category' => 'Categoria dell\'attrezzatura',
            'technician' => 'Tecnico',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'category' => 'Categoria dell\'attrezzatura',
            'team' => 'Squadra di manutenzione',
            'technician' => 'Tecnico',
        ],
        'groups' => [
            'category' => 'Categoria dell\'attrezzatura',
            'owner' => 'Proprietario',
            'technician' => 'Tecnico',
            'vendor' => 'Fornitore',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attrezzatura aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Attrezzatura restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Squadra archiviata',
                    'body' => 'L\'attrezzatura è stata archiviata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Squadra eliminata',
                        'body' => 'Il computer è stato rimosso definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il computer',
                        'body' => 'Questa apparecchiatura è referenziata da un altro record.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attrezzatura restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Squadra archiviata',
                    'body' => 'Il computer selezionato è stato archiviato con successo.',
                ],
            ],
        ],
        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Squadra creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome',
                    'note' => 'Descrizione',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'category' => 'Categoria dell\'attrezzatura',
                    'team' => 'Squadra di manutenzione',
                    'company' => 'Azienda',
                    'technician' => 'Tecnico',
                    'owner' => 'Proprietario',
                    'location' => 'Utilizzato sul posto',
                ],
            ],
            'product-information' => [
                'title' => 'Informazioni sul prodotto',
                'entries' => [
                    'partner' => 'Fornitore',
                    'partner-ref' => 'Riferimento del fornitore',
                    'model' => 'Modello',
                    'serial-no' => 'Numero di serie',
                    'effective-date' => 'Data di entrata in vigore',
                    'cost' => 'Costo',
                    'warranty-date' => 'Data di scadenza della garanzia',
                ],
            ],
            'maintenance' => [
                'title' => 'Manutenzione',
                'entries' => [
                    'expected-mtbf' => 'Tempo medio previsto tra i guasti',
                    'maintenance-count' => 'Numero di manutenzioni',
                    'maintenance-open-count' => 'Numero di manutenzioni aperte',
                    'assigned-at' => 'Data di incarico',
                    'scraped-at' => 'Data di smaltimento',
                ],
                'suffixes' => [
                    'days' => 'giorni',
                ],
            ],
        ],
    ],
];
