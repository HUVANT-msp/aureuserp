<?php

return [
    'global-search' => [
        'code' => 'Codice',
        'type' => 'Tipo',
    ],
    'form' => [
        'sections' => [
            'fields' => [
                'code' => 'Codice',
                'account-name' => 'Nome dell\'account',
                'accounting' => 'Contabilità',
                'account-type' => 'Tipo di conto',
                'parent-account' => 'Conto principale',
                'parent-account-helper' => 'Seleziona un account esistente da convertire in un account secondario.',
                'default-taxes' => 'Tasse predefinite',
                'tags' => 'Etichetta',
                'journals' => 'Sezionali contabili',
                'journals-helper' => 'Suggerito automaticamente in base al tipo di account selezionato. È possibile modificare la selezione.',
                'currency' => 'Valuta',
                'deprecated' => 'Obsoleto',
                'reconcile' => 'Consenti la riconciliazione',
                'non-trade' => 'Non commerciale',
                'companies' => 'Aziende',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'code' => 'Codice',
            'account-name' => 'Nome dell\'account',
            'account-type' => 'Conto',
            'parent-account' => 'Conto principale',
            'currency' => 'Valuta',
            'journals' => 'Sezionali contabili',
            'reconcile' => 'Consenti la riconciliazione',
        ],
        'grouping' => [
            'account-type' => 'Tipo di conto',
        ],
        'filters' => [
            'account-type' => 'Tipo di conto',
            'parent-account' => 'Conto principale',
            'allow-reconcile' => 'Consenti la riconciliazione',
            'currency' => 'Valuta',
            'account-journals' => 'Sezionali contabili',
            'non-trade' => 'Non commerciale',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Conto aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Conto eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione dell\'account',
                        'body' => 'Impossibile eliminare il conto perché presenta voci contabili associate.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Account cancellati',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione degli account',
                        'body' => 'Non è stato possibile eliminare i conti perché sono associati a movimenti contabili.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'code' => 'Codice',
                'account-name' => 'Nome dell\'account',
                'accounting' => 'Contabilità',
                'account-type' => 'Tipo di conto',
                'parent-account' => 'Conto principale',
                'sub-accounts' => 'Account secondari',
                'default-taxes' => 'Tasse predefinite',
                'tags' => 'Etichetta',
                'journals' => 'Sezionali contabili',
                'currency' => 'Valuta',
                'deprecated' => 'Obsoleto',
                'reconcile' => 'Conciliazione',
                'non-trade' => 'Non commerciale',
            ],
        ],
    ],
];
