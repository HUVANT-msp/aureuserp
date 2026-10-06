<?php

return [
    'navigation' => [
        'title' => 'Ubicazioni',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'location' => 'Ubicazione',
                    'location-placeholder' => 'pag. per esempio. Riserva di riserva',
                    'parent-location' => 'Ottima posizione',
                    'parent-location-hint-tooltip' => 'La posizione principale che comprende questa posizione. Ad esempio, "Zona di spedizione" fa parte della posizione superiore "Porta 1".',
                    'external-notes' => 'Note esterne',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'location-type' => 'Tipo di posizione',
                    'company' => 'Azienda',
                    'storage-category' => 'Categoria di stoccaggio',
                    'is-scrap' => 'È una posizione di ritiro?',
                    'is-scrap-hint-tooltip' => 'Selezionare questa casella di controllo per designare questa ubicazione in cui immagazzinare merci danneggiate o in restringimento.',
                    'is-dock' => 'È una posizione portuale?',
                    'is-dock-hint-tooltip' => 'Selezionare questa casella di controllo per designare questa ubicazione in cui immagazzinare le merci pronte per la spedizione.',
                    'is-replenish' => 'È una posizione di rifornimento?',
                    'is-replenish-hint-tooltip' => 'Attiva questa funzione per recuperare tutte le quantità necessarie per il rifornimento in questa posizione.',
                    'logistics' => 'Logistica',
                    'removal-strategy' => 'Strategia di estrazione',
                    'removal-strategy-hint-tooltip' => 'Specifica il metodo predefinito per determinare lo scaffale, il lotto e l\'ubicazione esatti da cui verranno prelevati i prodotti. Questo metodo può essere applicato a livello di categoria di prodotto, con un\'alternativa di ubicazioni più elevate se non definite qui.',
                    'cyclic-counting' => 'Conteggio del ciclo',
                    'inventory-frequency' => 'Frequenza dell\'inventario',
                    'last-inventory' => 'Ultimo inventario',
                    'last-inventory-hint-tooltip' => 'Data dell\'ultimo inventario in questa posizione.',
                    'next-expected' => 'Prossimo previsto',
                    'next-expected-hint-tooltip' => 'Data del prossimo inventario pianificato secondo il calendario ciclico.',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'location' => 'Ubicazione',
            'type' => 'Tipo',
            'storage-category' => 'Categoria di stoccaggio',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'warehouse' => 'Magazzino',
            'type' => 'Tipo',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'location' => 'Ubicazione',
            'type' => 'Tipo',
            'company' => 'Azienda',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ubicazione aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Posizione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ubicazione eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Posizione eliminata definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la posizione',
                        'body' => 'La posizione non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'print' => [
                'label' => 'Stampa codice a barre',
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Luoghi ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Località cancellate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Posizioni eliminate definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le posizioni',
                        'body' => 'Le posizioni non possono essere eliminate perché sono attualmente in uso.',
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
                    'location' => 'Ubicazione',
                    'location-placeholder' => 'pag. per esempio. Riserva di riserva',
                    'parent-location' => 'Ottima posizione',
                    'parent-location-hint-tooltip' => 'La posizione principale che comprende questa posizione. Ad esempio, "Zona di spedizione" fa parte della posizione superiore "Porta 1".',
                    'external-notes' => 'Note esterne',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'location-type' => 'Tipo di posizione',
                    'company' => 'Azienda',
                    'storage-category' => 'Categoria di stoccaggio',
                    'is-scrap' => 'È una posizione di ritiro?',
                    'is-scrap-hint-tooltip' => 'Selezionare questa casella di controllo per designare questa ubicazione in cui immagazzinare merci danneggiate o in restringimento.',
                    'is-dock' => 'È una posizione portuale?',
                    'is-dock-hint-tooltip' => 'Selezionare questa casella di controllo per designare questa ubicazione in cui immagazzinare le merci pronte per la spedizione.',
                    'is-replenish' => 'È una posizione di rifornimento?',
                    'is-replenish-hint-tooltip' => 'Attiva questa funzione per recuperare tutte le quantità necessarie per il rifornimento in questa posizione.',
                    'logistics' => 'Logistica',
                    'removal-strategy' => 'Strategia di estrazione',
                    'removal-strategy-hint-tooltip' => 'Specifica il metodo predefinito per determinare lo scaffale, il lotto e l\'ubicazione esatti da cui verranno prelevati i prodotti. Questo metodo può essere applicato a livello di categoria di prodotto, con un\'alternativa di ubicazioni più elevate se non definite qui.',
                    'cyclic-counting' => 'Conteggio del ciclo',
                    'inventory-frequency' => 'Frequenza dell\'inventario',
                    'last-inventory' => 'Ultimo inventario',
                    'last-inventory-hint-tooltip' => 'Data dell\'ultimo inventario in questa posizione.',
                    'next-expected' => 'Prossimo previsto',
                    'next-expected-hint-tooltip' => 'Data del prossimo inventario pianificato secondo il calendario ciclico.',
                ],
            ],
            'additional' => [
                'title' => 'Informazioni aggiuntive',
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
