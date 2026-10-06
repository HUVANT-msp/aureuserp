<?php

return [
    'navigation' => [
        'title' => 'Magazzini',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'pag. per esempio. Magazzino Centrale',
                    'code' => 'Nome breve',
                    'code-placeholder' => 'pag. per esempio. AC',
                    'code-hint-tooltip' => 'Il nome breve funge da identificatore del magazzino.',
                    'company' => 'Azienda',
                    'multi-warehouse-warning' => 'La creazione di un nuovo magazzino attiverà automaticamente l\'impostazione Posizioni di stoccaggio.',
                    'address' => 'Indirizzo',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'shipment-management' => 'Gestione delle spedizioni',
                    'incoming-shipments' => 'Spedizioni in entrata',
                    'incoming-shipments-hint-tooltip' => 'Percorso di ingresso predefinito da seguire',
                    'outgoing-shipments' => 'Spedizioni in uscita',
                    'outgoing-shipments-hint-tooltip' => 'Percorso di uscita predefinito da seguire',
                    'manufacture' => 'Produzione',
                    'manufacture-hint-tooltip' => 'Percorso di produzione predefinito da seguire',
                    'resupply-management' => 'Gestione del rifornimento',
                    'resupply-management-hint-tooltip' => 'I percorsi verranno generati automaticamente per rifornire questo magazzino dai magazzini selezionati.',
                    'resupply-from' => 'Rifornimento da',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Nome breve',
            'company' => 'Azienda',
            'address' => 'Indirizzo',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'address' => 'Indirizzo',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'company' => 'Azienda',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Magazzino restaurato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Magazzino eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Magazzino eliminato definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il magazzino',
                        'body' => 'Impossibile eliminare l\'archivio perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Magazzini restaurati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Magazzini eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Magazzini eliminati definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i negozi',
                        'body' => 'I magazzini non possono essere eliminati perché sono attualmente in uso.',
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
                    'name' => 'Nome del magazzino',
                    'code' => 'Codice magazzino',
                    'company' => 'Azienda',
                    'address' => 'Indirizzo',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'shipment-management' => 'Gestione delle spedizioni',
                    'incoming-shipments' => 'Spedizioni in entrata',
                    'outgoing-shipments' => 'Spedizioni in uscita',
                    'resupply-management' => 'Gestione del rifornimento',
                    'resupply-from' => 'Rifornimento da',
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
