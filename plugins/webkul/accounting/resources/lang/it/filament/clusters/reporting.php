<?php

return [
    'navigation' => [
        'title' => 'Rapporti',
    ],
    'common' => [
        'from-to' => ':report - Da :from a :to',
        'expand-all' => 'Espandi tutto',
        'collapse-all' => 'Comprimi tutto',
        'account' => 'Conto',
        'date' => 'Data',
        'communication' => 'Comunicazione',
        'partner' => 'Partner',
        'journal' => 'Sezionale',
        'invoice-date' => 'Data della fattura',
        'due-date' => 'Data di scadenza',
        'debit' => 'Addebito',
        'credit' => 'Credito',
        'balance' => 'Equilibrio',
        'total' => 'Totale',
        'opening-balance' => 'Saldo di apertura',
        'initial-balance' => 'Saldo iniziale',
        'end-balance' => 'Saldo finale',
        'not-due' => 'Non sconfitto',
        'no-data' => 'Nessun dato disponibile',
        'no-accounts-transactions' => 'Non ci sono conti con transazioni in questo periodo',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Bilancio',
                'group' => 'Rapporti sullo stato',
            ],
            'actions' => [
                'export-excel' => 'Esporta in Excel',
                'export-pdf' => 'Esporta in PDF',
            ],
            'filters' => [
                'date-range' => 'Intervallo di date',
                'journals' => 'Sezionali contabili',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title' => 'BENI',
                        'total-label' => 'TOTALE ATTIVO',
                        'subsections' => [
                            'current-assets' => [
                                'title' => 'Attività correnti',
                                'total-label' => 'Totale attività correnti',
                            ],
                            'fixed-assets' => [
                                'title' => 'Immobilizzazioni',
                                'total-label' => 'Totale immobilizzazioni',
                            ],
                            'non-current-assets' => [
                                'title' => 'Attività non correnti',
                                'total-label' => 'Totale attività non correnti',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title' => 'PASSIVITÀ',
                        'total-label' => 'PASSIVITÀ Totale',
                        'subsections' => [
                            'current-liabilities' => [
                                'title' => 'Passività correnti',
                                'total-label' => 'Passività correnti totali',
                            ],
                            'non-current-liabilities' => [
                                'title' => 'Passività non correnti',
                                'total-label' => 'Totale passività non correnti',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title' => 'PATRIMONIO',
                        'total-label' => 'PATRIMONIO NETTO Totale',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title' => 'Guadagni non assegnati',
                                'current-year' => 'Guadagni non assegnati dell\'anno in corso',
                                'previous-years' => 'Guadagni non assegnati degli anni precedenti',
                                'total-label' => 'Guadagni totali non assegnati',
                            ],
                            'retained-earnings' => [
                                'title' => 'Utili non distribuiti',
                                'total-label' => 'Utili non distribuiti totali',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASSIVO + PATRIMONIO NETTO',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Profitti e perdite',
                'group' => 'Rapporti sullo stato',
            ],
            'actions' => [
                'export-excel' => 'Esporta in Excel',
                'export-pdf' => 'Esporta in PDF',
            ],
            'filters' => [
                'date-range' => 'Intervallo di date',
                'journals' => 'Sezionali contabili',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title' => 'REDDITO',
                        'total-label' => 'Reddito totale',
                        'empty-message' => 'Non sono presenti conti ricavi con transazioni in questo periodo',
                    ],
                    'expenses' => [
                        'title' => 'SPESE',
                        'total-label' => 'Spese totali',
                        'empty-message' => 'Non sono presenti conti spese con transazioni in questo periodo',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Libro mastro',
                'group' => 'Rapporti di audit',
            ],
            'actions' => [
                'export-excel' => 'Esporta in Excel',
                'export-pdf' => 'Esporta in PDF',
            ],
            'filters' => [
                'date-range' => 'Intervallo di date',
                'journals' => 'Sezionali contabili',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Bilancio di verifica',
                'group' => 'Rapporti di audit',
            ],
            'actions' => [
                'export-excel' => 'Esporta in Excel',
                'export-pdf' => 'Esporta in PDF',
            ],
            'filters' => [
                'date-range' => 'Intervallo di date',
                'journals' => 'Sezionali contabili',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Contatta il registro',
                'group' => 'Rapporti di contatto',
            ],
            'actions' => [
                'export-excel' => 'Esporta Excel',
                'export-pdf' => 'Esporta PDF',
            ],
            'filters' => [
                'date-range' => 'Intervallo di date',
                'partners' => 'Partner',
                'journals' => 'Sezionali contabili',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Invecchiamento dei crediti',
                'group' => 'Rapporti di contatto',
            ],
            'actions' => [
                'export-excel' => 'Esporta Excel',
                'export-pdf' => 'Esporta PDF',
            ],
            'filters' => [
                'as-of' => 'A partire dalla data di',
                'based-on' => 'Basato su',
                'period-length' => 'Durata del periodo (giorni)',
                'journals' => 'Sezionali contabili',
                'partners' => 'Partner',
                'entries' => 'Sedili',
                'options' => [
                    'due-date' => 'Data di scadenza',
                    'invoice-date' => 'Data della fattura',
                    'days-30' => '30 giorni',
                    'days-60' => '60 giorni',
                    'days-90' => '90 giorni',
                    'posted-entries' => 'Voci pubblicate',
                    'all-entries' => 'Tutti i posti',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Invecchiamento dei conti da pagare',
                'group' => 'Rapporti di contatto',
            ],
            'actions' => [
                'export-excel' => 'Esporta Excel',
                'export-pdf' => 'Esporta PDF',
            ],
            'filters' => [
                'as-of' => 'A partire dalla data di',
                'based-on' => 'Basato su',
                'period-length' => 'Durata del periodo (giorni)',
                'journals' => 'Sezionali contabili',
                'partners' => 'Partner',
                'entries' => 'Sedili',
                'options' => [
                    'due-date' => 'Data di scadenza',
                    'invoice-date' => 'Data della fattura',
                    'days-30' => '30 giorni',
                    'days-60' => '60 giorni',
                    'days-90' => '90 giorni',
                    'posted-entries' => 'Voci pubblicate',
                    'all-entries' => 'Tutti i posti',
                ],
            ],
        ],
    ],
];
