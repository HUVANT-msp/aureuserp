<?php

return [
    'navigation' => [
        'title' => 'Regole',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'action' => 'Azione',
                    'operation-type' => 'Tipo di operazione',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Località di destinazione',
                    'supply-method' => 'Metodo di consegna',
                    'supply-method-hint-tooltip' => 'Prelevare da stock: i prodotti vengono prelevati direttamente dallo stock disponibile nell\'ubicazione di origine.<br/>Attiva un\'altra regola: il sistema ignora lo stock disponibile e cerca una regola di stock per ricostituire l\'ubicazione di origine.<br/>Prelevare da stock; Se non è disponibile, attiva un\'altra regola: prima i prodotti vengono prelevati dallo stock disponibile. Se non ce ne sono disponibili, il sistema applica una regola di stock per portare i prodotti nell\'ubicazione di origine.',
                    'automatic-move' => 'Movimento automatico',
                    'automatic-move-hint-tooltip' => 'Operazione manuale: Crea un movimento di stock separato dopo quello corrente. Automatico senza passaggio aggiuntivo: sostituisce direttamente la posizione nel movimento originale senza aggiungere un passaggio aggiuntivo.',
                    'action-information' => [
                        'pull' => 'Quando i prodotti sono richiesti a <b>:sourceLocation</b>, :operation viene generato da <b>:destinationLocation</b> per soddisfare la domanda.',
                        'push' => 'Quando i prodotti arrivano a <b>:sourceLocation</b>, </br><b>:operation</b> viene generato per trasferirli a <b>:destinationLocation</b>.',
                        'buy' => 'Quando sono necessari prodotti in <b>:destinationLocation</b>, viene creata una richiesta di preventivo per soddisfare l\'esigenza.',
                        'manufacture' => 'Quando sono necessari prodotti in <b>:destinationLocation</b>, viene creato un ordine di produzione per soddisfare la necessità.',
                    ],
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'partner-address' => 'Indirizzo di contatto',
                    'partner-address-hint-tooltip' => 'Indirizzo dove la merce deve essere consegnata. Opzionale.',
                    'lead-time' => 'Tempi di consegna (giorni)',
                    'lead-time-hint-tooltip' => 'La data di trasferimento prevista verrà calcolata utilizzando questo tempo di consegna.',
                ],
                'fieldsets' => [
                    'applicability' => [
                        'title' => 'Applicabilità',
                        'fields' => [
                            'route' => 'Rotta',
                            'company' => 'Azienda',
                        ],
                    ],
                    'propagation' => [
                        'title' => 'Propagazione',
                        'fields' => [
                            'propagation-procurement-group' => 'Propagazione del gruppo di provisioning',
                            'propagation-procurement-group-hint-tooltip' => 'Se selezionato, l\'annullamento della mossa creata da questa regola annullerà anche la mossa successiva.',
                            'cancel-next-move' => 'Annulla la mossa successiva',
                            'warehouse-to-propagate' => 'Magazzino per propagarsi',
                            'warehouse-to-propagate-hint-tooltip' => 'Il magazzino assegnato al movimento o alla fornitura creata, che può differire dal magazzino a cui si applica questa regola (ad esempio, per le regole di rifornimento da un altro magazzino).',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'action' => 'Azione',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Località di destinazione',
            'route' => 'Rotta',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'action' => 'Azione',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Località di destinazione',
            'route' => 'Rotta',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'action' => 'Azione',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Località di destinazione',
            'route' => 'Rotta',
            'company' => 'Azienda',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Regola aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Regola restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Regola rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regola rimossa definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la regola',
                        'body' => 'La regola non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Regole ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Regole rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Regole rimosse definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le regole',
                        'body' => 'Impossibile eliminare le regole perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Dettagli della regola',
                'description' => [
                    'pull' => 'Quando i prodotti sono richiesti a <b>:sourceLocation</b>, <b>:operation</b> viene generato da <b>:destinationLocation</b> per soddisfare la domanda.',
                    'push' => 'Quando i prodotti arrivano a <b>:sourceLocation</b>, viene generato <b>:operation</b> per trasferirli a <b>:destinationLocation</b>.',
                ],
                'entries' => [
                    'name' => 'Nome della regola',
                    'action' => 'Azione',
                    'operation-type' => 'Tipo di operazione',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Località di destinazione',
                    'route' => 'Rotta',
                    'company' => 'Azienda',
                    'partner-address' => 'Indirizzo di contatto',
                    'lead-time' => 'Tempi di consegna',
                    'action-information' => 'Informazioni sull\'azione',
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
