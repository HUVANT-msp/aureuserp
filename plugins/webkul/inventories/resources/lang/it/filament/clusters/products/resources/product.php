<?php

return [
    'table' => [
        'columns' => [
            'on-hand' => 'Giacenza disponibile',
            'forecasted' => 'Previsto',
        ],
    ],
    'navigation' => [
        'title' => 'Prodotti',
        'group' => 'Magazzino',
    ],
    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Magazzino',
                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Monitoraggio',
                        'fields' => [
                            'track-inventory' => 'Monitoraggio dell\'inventario',
                            'track-inventory-hint-tooltip' => 'Un prodotto conservabile è un prodotto che richiede la gestione dell\'inventario.',
                            'track-by' => 'Seguici',
                            'expiration-date' => 'Data di scadenza',
                            'expiration-date-hint-tooltip' => 'Se selezionato, è possibile specificare le date di scadenza del prodotto e i lotti/numeri di serie associati.',
                        ],
                    ],
                    'operation' => [
                        'title' => 'Operazioni',
                        'fields' => [
                            'routes' => 'Rotte di movimentazione',
                            'routes-hint-tooltip' => 'A seconda dei moduli installati, questa configurazione consente di definire il percorso del prodotto, come acquisto, produzione o rifornimento su richiesta.',
                        ],
                    ],
                    'logistics' => [
                        'title' => 'Logistica',
                        'fields' => [
                            'responsible' => 'Responsabile',
                            'responsible-hint-tooltip' => 'Il tempo di consegna (in giorni) rappresenta la durata promessa tra la conferma dell\'ordine di vendita e la consegna del prodotto.',
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                            'sale-delay' => 'Tempi di consegna al cliente (giorni)',
                            'sale-delay-hint-tooltip' => 'Il tempo di consegna (in giorni) rappresenta la durata promessa tra la conferma dell\'ordine di vendita e la consegna del prodotto.',
                        ],
                    ],
                    'traceability' => [
                        'title' => 'Tracciabilità',
                        'fields' => [
                            'expiration-date' => 'Data di scadenza (giorni)',
                            'expiration-date-hint-tooltip' => 'Se selezionato, è possibile impostare le date di scadenza del prodotto e dei lotti/numeri di serie associati.',
                            'best-before-date' => 'Data di scadenza (giorni)',
                            'best-before-date-hint-tooltip' => 'Il numero di giorni prima della data di scadenza in cui il prodotto inizia a deteriorarsi, sebbene sia ancora sicuro da usare. Viene calcolato in base al numero di lotto/serie.',
                            'removal-date' => 'Data di ritiro (giorni)',
                            'removal-date-hint-tooltip' => 'Il numero di giorni prima della data di scadenza in cui il prodotto deve essere rimosso dallo stock. Viene calcolato in base al numero di lotto/serie.',
                            'alert-date' => 'Data dell\'avviso (giorni)',
                            'alert-date-hint-tooltip' => 'Il numero di giorni prima della data di scadenza in cui deve essere generato un avviso per il numero di lotto/serie. Viene calcolato in base al numero di lotto/serie.',
                        ],
                    ],
                ],
            ],
            'additional' => [
                'title' => 'Ulteriori',
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Magazzino',
                'entries' => [

                ],
                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Monitoraggio',
                        'entries' => [
                            'track-inventory' => 'Monitoraggio dell\'inventario',
                            'track-by' => 'Seguici',
                            'expiration-date' => 'Data di scadenza',
                        ],
                    ],
                    'operation' => [
                        'title' => 'Operazioni',
                        'entries' => [
                            'routes' => 'Rotte di movimentazione',
                        ],
                    ],
                    'logistics' => [
                        'title' => 'Logistica',
                        'entries' => [
                            'responsible' => 'Responsabile',
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                            'sale-delay' => 'Tempi di consegna al cliente (giorni)',
                        ],
                    ],
                    'traceability' => [
                        'title' => 'Tracciabilità',
                        'entries' => [
                            'expiration-date' => 'Data di scadenza (giorni)',
                            'best-before-date' => 'Data di scadenza (giorni)',
                            'removal-date' => 'Data di ritiro (giorni)',
                            'alert-date' => 'Data dell\'avviso (giorni)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
