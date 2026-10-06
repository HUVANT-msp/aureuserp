<?php

return [
    'navigation' => [
        'title' => 'Prodotti',
        'group' => 'Magazzino',
    ],
    'global-search' => [
        'partner' => 'Partner',
        'origin' => 'Origine',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'receive-from' => 'Ricevi da',
                    'contact' => 'Contatto',
                    'delivery-address' => 'Indirizzo di consegna',
                    'operation-type' => 'Tipo di operazione',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Località di destinazione',
                ],
            ],
            'additional-fields' => [
                'title' => 'Informazioni aggiuntive',
            ],
        ],
        'tabs' => [
            'operations' => [
                'title' => 'Operazioni',
                'columns' => [
                    'product' => 'Prodotto',
                    'final-location' => 'Posizione finale',
                    'description' => 'Descrizione',
                    'scheduled-at' => 'Previsto per',
                    'deadline' => 'Scadenza',
                    'packaging' => 'Imballaggio',
                    'demand' => 'Domanda',
                    'quantity' => 'Quantità',
                    'insufficient-stock-tooltip' => 'Quantità disponibile insufficiente',
                    'unit' => 'Unità',
                    'picked' => 'Raccolto',
                ],
                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Prodotto aperto',
                    ],
                ],
                'fields' => [
                    'product' => 'Prodotto',
                    'final-location' => 'Posizione finale',
                    'description' => 'Descrizione',
                    'scheduled-at' => 'Previsto per',
                    'deadline' => 'Scadenza',
                    'packaging' => 'Imballaggio',
                    'demand' => 'Domanda',
                    'quantity' => 'Quantità',
                    'unit' => 'Unità',
                    'picked' => 'Raccolto',
                    'lines' => [
                        'modal-heading' => 'Gestire i movimenti di magazzino',
                        'modal-submit-action-label' => 'Salva',
                        'add-line' => 'Aggiungi riga',
                        'actions' => [
                            'generate' => 'Genera serie/lotti',
                            'import' => 'Importa serie/lotti',
                        ],
                        'fields' => [
                            'lot' => 'Numero di lotto/serie',
                            'pick-from' => 'Raccogliere da',
                            'location' => 'Conservare',
                            'package' => 'Pacchetto obiettivo',
                            'quantity' => 'Quantità',
                            'uom' => 'Unità di misura',
                            'first-lot' => 'Primo numero di lotto',
                            'quantity-per-lot' => 'Quantità per lotto',
                            'quantity-received' => 'Importo ricevuto',
                            'keep-current-lines' => 'Mantenere le linee attuali',
                            'serials' => 'Lotti/numeri di serie',
                            'serials-helper' => 'Un numero di lotto/serie per riga.',
                        ],
                    ],
                ],
            ],
            'additional' => [
                'title' => 'Ulteriori',
                'fields' => [
                    'responsible' => 'Responsabile',
                    'shipping-policy' => 'Politica di spedizione',
                    'shipping-policy-hint-tooltip' => 'Definisce se la merce deve essere consegnata parzialmente o tutta in una volta.',
                    'scheduled-at' => 'Previsto per',
                    'scheduled-at-hint-tooltip' => 'Il tempo pianificato per elaborare la prima parte della spedizione. L\'impostazione manuale di un valore qui lo applicherà come data prevista per tutti i movimenti di stock.',
                    'source-document' => 'Documento di origine',
                    'source-document-hint-tooltip' => 'Riferimento al documento',
                ],
            ],
            'note' => [
                'title' => 'Nota',
                'fields' => [

                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'favorite' => 'Preferito',
            'reference' => 'Riferimento',
            'from' => 'Da allora',
            'to' => 'A',
            'contact' => 'Contatto',
            'responsible' => 'Responsabile',
            'scheduled-at' => 'Previsto per',
            'deadline' => 'Scadenza',
            'closed-at' => 'Chiuso',
            'source-document' => 'Documento di origine',
            'operation-type' => 'Tipo di operazione',
            'company' => 'Azienda',
            'state' => 'Stato',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'state' => 'Stato',
            'source-document' => 'Documento di origine',
            'operation-type' => 'Tipo di operazione',
            'scheduled-at' => 'Pianifica il',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'operation-type' => 'Tipo di operazione',
            'name' => 'Nome',
            'state' => 'Stato',
            'partner' => 'Partner',
            'responsible' => 'Responsabile',
            'owner' => 'Proprietario',
            'source-location' => 'Posizione di origine',
            'destination-location' => 'Località di destinazione',
            'deadline' => 'Scadenza',
            'scheduled-at' => 'Previsto per',
            'closed-at' => 'Chiuso',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
            'company' => 'Azienda',
            'creator' => 'Creato da',
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'contact' => 'Contatto',
                    'operation-type' => 'Tipo di operazione',
                    'source-location' => 'Posizione di origine',
                    'destination-location' => 'Località di destinazione',
                ],
            ],
        ],
        'tabs' => [
            'operations' => [
                'title' => 'Operazioni',
                'entries' => [
                    'product' => 'Prodotto',
                    'final-location' => 'Posizione finale',
                    'description' => 'Descrizione',
                    'scheduled-at' => 'Previsto per',
                    'deadline' => 'Scadenza',
                    'packaging' => 'Imballaggio',
                    'demand' => 'Domanda',
                    'quantity' => 'Quantità',
                    'unit' => 'Unità',
                    'picked' => 'Raccolto',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'responsible' => 'Responsabile',
                    'shipping-policy' => 'Politica di spedizione',
                    'scheduled-at' => 'Previsto per',
                    'source-document' => 'Documento di origine',
                ],
            ],
            'note' => [
                'title' => 'Nota',
            ],
        ],
    ],
    'tabs' => [
        'todo' => 'Fare',
        'my' => 'I miei trasferimenti',
        'starred' => 'In primo piano',
        'draft' => 'Bozza',
        'waiting' => 'Aspettando',
        'ready' => 'Pronto',
        'late' => 'In ritardo',
        'done' => 'Completato',
        'canceled' => 'Annullato',
        'back-orders' => 'Ordini pendenti',
    ],
    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Avviso di precisione dell\'unità di misura',
            'body' => 'Stai utilizzando un\'unità di misura più piccola di quella utilizzata per conservare questo prodotto. Ciò può causare problemi di arrotondamento negli importi riservati. Prendi in considerazione l\'utilizzo dell\'unità di misura più piccola per la valutazione delle azioni o la riduzione della precisione dell\'arrotondamento dell\'unità di base.',
        ],
    ],
];
