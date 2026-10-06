<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term' => 'Termine di pagamento',
                'company' => 'Azienda',
                'company-placeholder' => 'Tutte le aziende',
                'early-discount' => 'Sconto pagamento immediato',
                'discount-days-prefix' => 'se pagato entro',
                'discount-days-suffix' => 'giorni',
                'reduced-tax' => 'Tassa ridotta',
                'note' => 'Nota',
                'status' => 'Stato',
            ],
        ],
        'tabs' => [
            'due-terms' => [
                'title' => 'Date di scadenza',
                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value' => 'Valore',
                            'due' => 'Maturità',
                            'delay-type' => 'Tipo di termine',
                            'days-on-the-next-month' => 'Giorni del mese successivo',
                            'days' => 'Giorni',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'payment-term' => 'Termine di pagamento',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'company-name' => 'Nome dell\'azienda',
            'discount-days' => 'Giorni di sconto',
            'early-pay-discount' => 'Sconto pagamento immediato',
            'payment-term' => 'Termine di pagamento',
            'display-on-invoice' => 'Mostrare in fattura',
            'early-discount' => 'Sconto pagamento immediato',
            'discount-percentage' => 'Percentuale di sconto',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condizione di pagamento ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Condizione di pagamento rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condizione di pagamento rimossa definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione permanente del termine di pagamento',
                        'body' => 'Non è stato possibile eliminare definitivamente la condizione di pagamento poiché è associata a registrazioni contabili.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Termini di pagamento ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Termini di pagamento rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Termini di pagamento rimossi definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Errore durante l\'eliminazione permanente dei termini di pagamento',
                        'body' => 'Non è stato possibile eliminare definitivamente i termini di pagamento poiché sono associati a registrazioni contabili.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term' => 'Termine di pagamento',
                'early-discount' => 'Sconto pagamento immediato',
                'discount-percentage' => 'Percentuale di sconto',
                'discount-days-prefix' => 'se pagato entro',
                'discount-days-suffix' => 'giorni',
                'reduced-tax' => 'Tassa ridotta',
                'note' => 'Nota',
                'status' => 'Stato',
            ],
        ],
        'tabs' => [
            'due-terms' => [
                'title' => 'Date di scadenza',
                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value' => 'Valore',
                            'due' => 'Maturità',
                            'delay-type' => 'Tipo di termine',
                            'days-on-the-next-month' => 'Giorni del mese successivo',
                            'days' => 'Giorni',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
