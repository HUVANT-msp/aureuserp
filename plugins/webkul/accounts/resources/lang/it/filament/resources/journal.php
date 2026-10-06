<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Scritture contabili',
                'field-set' => [
                    'accounting-information' => [
                        'title' => 'Informazioni contabili',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Sequenza dedicata per le note di credito',
                            'dedicated-payment-sequence' => 'Sequenza dedicata per i pagamenti',
                            'sort-code-placeholder' => 'Immettere il codice giornale',
                            'sort-code' => 'Ordina',
                            'currency' => 'Valuta',
                            'color' => 'Colore',
                            'default-account' => 'Conto predefinito',
                            'profit-account' => 'Conto dei profitti',
                            'loss-account' => 'Conto perdite',
                            'suspense-account' => 'Conto sospeso',
                            'bank-account' => 'Conto bancario',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Numero di conto bancario',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Pagamenti in entrata',
                'add-action-label' => 'Aggiungi riga',
                'fields' => [
                    'payment-method' => 'Metodo di pagamento',
                    'display-name' => 'Nome visualizzato',
                    'account-number' => 'Conti in attesa di riscossione',
                    'relation-notes' => 'Note sulle relazioni',
                    'relation-notes-placeholder' => 'Inserisci eventuali dettagli sulla relazione',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Pagamenti in uscita',
                'add-action-label' => 'Aggiungi riga',
                'fields' => [
                    'payment-method' => 'Metodo di pagamento',
                    'display-name' => 'Nome visualizzato',
                    'account-number' => 'Conti di pagamento in sospeso',
                    'relation-notes' => 'Note sulle relazioni',
                    'relation-notes-placeholder' => 'Inserisci eventuali dettagli sulla relazione',
                ],
            ],
            'advanced-settings' => [
                'title' => 'Impostazioni avanzate',
                'fields' => [
                    'allowed-accounts' => 'Account consentiti',
                    'control-access' => 'Controllo degli accessi',
                    'payment-communication' => 'Comunicazione di pagamento',
                    'auto-check-on-post' => 'Controlla automaticamente quando pubblichi',
                    'communication-type' => 'Tipo di comunicazione',
                    'communication-standard' => 'Norma di comunicazione',
                ],
            ],
        ],
        'general' => [
            'title' => 'Informazioni generali',
            'fields' => [
                'name' => 'Nome',
                'type' => 'Tipo',
                'company' => 'Azienda',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'type' => 'Tipo',
            'code' => 'Codice',
            'currency' => 'Valuta',
            'created-by' => 'Creato da',
            'status' => 'Stato',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diario cancellato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione del diario',
                        'body' => 'Il diario non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diario cancellato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione dei diari',
                        'body' => 'I diari non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Scritture contabili',
                'field-set' => [
                    'accounting-information' => [
                        'title' => 'Informazioni contabili',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Sequenza dedicata per le note di credito',
                            'dedicated-payment-sequence' => 'Sequenza dedicata per i pagamenti',
                            'sort-code-placeholder' => 'Immettere il codice giornale',
                            'sort-code' => 'Ordina',
                            'currency' => 'Valuta',
                            'color' => 'Colore',
                            'default-account' => 'Conto predefinito',
                            'profit-account' => 'Conto dei profitti',
                            'loss-account' => 'Conto perdite',
                            'suspense-account' => 'Conto sospeso',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Numero di conto bancario',
                        'entries' => [
                            'account-number' => 'Numero di conto',
                        ],
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Pagamenti in entrata',
                'entries' => [
                    'payment-method' => 'Metodo di pagamento',
                    'display-name' => 'Nome visualizzato',
                    'account-number' => 'Conti in attesa di riscossione',
                    'relation-notes' => 'Note sulle relazioni',
                    'relation-notes-placeholder' => 'Inserisci eventuali dettagli sulla relazione',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Pagamenti in uscita',
                'entries' => [
                    'payment-method' => 'Metodo di pagamento',
                    'display-name' => 'Nome visualizzato',
                    'account-number' => 'Conti di pagamento in sospeso',
                    'relation-notes' => 'Note sulle relazioni',
                    'relation-notes-placeholder' => 'Inserisci eventuali dettagli sulla relazione',
                ],
            ],
            'advanced-settings' => [
                'title' => 'Impostazioni avanzate',
                'allowed-accounts' => [
                    'title' => 'Account consentiti',
                    'entries' => [
                        'allowed-accounts' => 'Account consentiti',
                        'control-access' => 'Controllo degli accessi',
                        'auto-check-on-post' => 'Controlla automaticamente quando pubblichi',
                    ],
                ],
                'payment-communication' => [
                    'title' => 'Comunicazione di pagamento',
                    'entries' => [
                        'communication-type' => 'Tipo di comunicazione',
                        'communication-standard' => 'Norma di comunicazione',
                    ],
                ],
            ],
        ],
        'general' => [
            'title' => 'Informazioni generali',
            'entries' => [
                'name' => 'Nome',
                'type' => 'Tipo',
                'company' => 'Azienda',
            ],
        ],
    ],
];
