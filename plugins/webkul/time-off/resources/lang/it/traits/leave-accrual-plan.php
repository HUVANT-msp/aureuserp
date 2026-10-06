<?php

return [
    'form' => [
        'fields' => [
            'accrual-amount' => 'Importo di accumulo',
            'accrual-value-type' => 'Tipo di valore di accumulo',
            'accrual-frequency' => 'Frequenza di accumulo',
            'accrual-day' => 'Giorno di accumulo',
            'day-of-month' => 'Giorno del mese',
            'first-day-of-month' => 'Primo giorno del mese',
            'second-day-of-month' => 'Secondo giorno del mese',
            'first-period-month' => 'Mese del primo ciclo',
            'first-period-day' => 'Giorno del primo ciclo',
            'second-period-month' => 'Mese del secondo periodo',
            'second-period-day' => 'Giorno del secondo ciclo',
            'first-period-year' => 'Anno del primo periodo',
            'cap-accrued-time' => 'Limita il tempo accumulato',
            'days' => 'Giorni',
            'start-count' => 'Conteggio iniziale',
            'start-type' => 'Tipo di avvio',
            'action-with-unused-accruals' => 'Azione con stack inutilizzati',
            'milestone-cap' => 'Limite fondamentale',
            'maximum-leave-yearly' => 'Massima assenza annuale',
            'accrual-validity' => 'Validità del cumulo',
            'accrual-validity-count' => 'Conteggio della validità dell\'accumulo',
            'accrual-validity-type' => 'Tipo di validità per competenza',
            'advanced-accrual-settings' => 'Impostazioni avanzate di accumulo',
            'after-allocation-start' => 'Dopo la data di inizio dell\'assegnazione',
            'to-date' => 'Ad oggi',
        ],
    ],
    'table' => [
        'columns' => [
            'accrual-amount' => 'Importo di accumulo',
            'accrual-value-type' => 'Tipo di valore di accumulo',
            'frequency' => 'Frequenza',
            'maximum-leave-days' => 'Giorni massimi di assenza',
        ],
        'groups' => [
            'accrual-amount' => 'Importo di accumulo',
            'accrual-value-type' => 'Tipo di valore di accumulo',
            'frequency' => 'Frequenza',
            'maximum-leave-days' => 'Giorni massimi di assenza',
        ],
        'filters' => [
            'accrual-frequency' => 'Frequenza di accumulo',
            'start-type' => 'Tipo di avvio',
            'cap-accrued-time' => 'Limita il tempo accumulato',
            'action-with-unused-accruals' => 'Azione con stack inutilizzati',
            'accrual-amount' => 'Importo di accumulo',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'header-actions' => [
            'created' => [
                'title' => 'Nuovo piano di accumulo assenze',
                'notification' => [
                    'title' => 'Piano di accumulo assenze creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Piano di accumulo assenze aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Eliminato il piano di accumulo assenze',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminati i piani di accumulo assenze',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'accrual-amount' => 'Importo di accumulo',
            'accrual-value-type' => 'Tipo di valore di accumulo',
            'accrual-frequency' => 'Frequenza di accumulo',
            'accrual-day' => 'Giorno di accumulo',
            'day-of-month' => 'Giorno del mese',
            'first-day-of-month' => 'Primo giorno del mese',
            'second-day-of-month' => 'Secondo giorno del mese',
            'first-period-month' => 'Mese del primo ciclo',
            'first-period-day' => 'Giorno del primo ciclo',
            'second-period-month' => 'Mese del secondo periodo',
            'second-period-day' => 'Giorno del secondo ciclo',
            'first-period-year' => 'Anno del primo periodo',
            'cap-accrued-time' => 'Limita il tempo accumulato',
            'days' => 'Giorni',
            'start-count' => 'Conteggio iniziale',
            'start-type' => 'Tipo di avvio',
            'action-with-unused-accruals' => 'Azione con stack inutilizzati',
            'milestone-cap' => 'Limite fondamentale',
            'maximum-leave-yearly' => 'Massima assenza annuale',
            'accrual-validity' => 'Validità del cumulo',
            'accrual-validity-count' => 'Conteggio della validità dell\'accumulo',
            'accrual-validity-type' => 'Tipo di validità per competenza',
            'advanced-accrual-settings' => 'Impostazioni avanzate di accumulo',
            'after-allocation-start' => 'Dopo la data di inizio dell\'assegnazione',
        ],
    ],
];
