<?php

return [
    'title' => 'Monete',
    'navigation' => [
        'title' => 'Monete',
    ],
    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informazioni sulla valuta',
                'fields' => [
                    'name' => 'Nome della valuta',
                    'name-tooltip' => 'Inserisci il nome ufficiale della valuta',
                    'symbol' => 'Simbolo di valuta',
                    'full-name' => 'Nome completo',
                    'iso-numeric' => 'Codice ISO numerico',
                ],
            ],
            'format-information' => [
                'title' => 'Impostazioni formato',
                'fields' => [
                    'decimal-places' => 'Decimali',
                    'rounding' => 'Precisione dell\'arrotondamento',
                    'rounding-helper-text' => 'Imposta la precisione dell\'arrotondamento per i calcoli valutari',
                ],
            ],
            'status-and-configuration-information' => [
                'title' => 'Stato e impostazioni',
                'fields' => [
                    'status' => 'Stato',
                ],
            ],
            'rates' => [
                'title' => 'Tassi di cambio',
                'description' => 'Gestisci i tassi di cambio storici di questa valuta rispetto alla valuta di base (USD).',
                'fields' => [
                    'name' => 'Data',
                    'unit-per-currency' => 'Unità per :currency',
                    'currency-per-unit' => ':currency per unità',
                ],
                'add-rate' => 'Aggiungi tariffa',
                'item-label' => 'Vota',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome della valuta',
            'symbol' => 'Simbolo',
            'full-name' => 'Nome completo',
            'iso-numeric' => 'Codice ISO',
            'decimal-places' => 'Decimali',
            'rounding' => 'Arrotondamento',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'name' => 'Nome',
            'status' => 'Stato',
            'decimal-places' => 'Decimali',
            'creation-date' => 'Data di creazione',
            'last-update' => 'Ultimo aggiornamento',
        ],
        'filters' => [
            'status' => 'Stato',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Valuta rimossa',
                    'body' => 'Eliminazione completata con successo.',
                    'success' => [
                        'title' => 'Valuta rimossa',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la valuta',
                        'body' => 'La valuta non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
            'deactivate' => [
                'notification' => [
                    'title' => 'La valuta non può essere disattivata',
                    'body' => 'Questa valuta è utilizzata da una o più aziende e non può essere disattivata.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Monete rimosse',
                    'body' => 'Eliminazione completata con successo.',
                    'error' => [
                        'title' => 'Impossibile eliminare le monete',
                        'body' => 'Alcune delle valute selezionate sono in uso e non possono essere eliminate.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informazioni sulla valuta',
                'entries' => [
                    'name' => 'Nome della valuta',
                    'symbol' => 'Simbolo di valuta',
                    'full-name' => 'Nome completo',
                    'iso-numeric' => 'Codice ISO numerico',
                ],
            ],
            'format-information' => [
                'title' => 'Impostazioni formato',
                'entries' => [
                    'decimal-places' => 'Decimali',
                    'rounding' => 'Precisione dell\'arrotondamento',
                ],
            ],
            'status-and-configuration-information' => [
                'title' => 'Stato e impostazioni',
                'entries' => [
                    'status' => 'Stato',
                ],
            ],
            'rates' => [
                'title' => 'Tassi di cambio',
                'entries' => [
                    'name' => 'Data',
                    'unit-per-currency' => 'Unità per :currency',
                    'currency-per-unit' => ':currency per unità',
                ],
            ],
        ],
    ],
];
