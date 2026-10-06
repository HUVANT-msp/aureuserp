<?php

return [
    'title' => 'Piano di accumulo',
    'navigation' => [
        'title' => 'Piano di accumulo',
    ],
    'form' => [
        'fields' => [
            'name' => 'Titolo',
            'is-based-on-worked-time' => 'Si basa sul tempo lavorato',
            'accrued-gain-time' => 'Tempo di guadagno accumulato',
            'carry-over-time' => 'Tempo di trasferimento',
            'carry-over-date' => 'Data del trasferimento',
            'status' => 'Stato',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'levels' => 'Livelli',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminato il piano di accumulo',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Eliminato il piano di accumulo',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'basic-information' => 'Informazioni di base',
        ],
        'entries' => [
            'name' => 'Nome',
            'is-based-on-worked-time' => 'Si basa sul tempo lavorato',
            'accrued-gain-time' => 'Tempo di guadagno accumulato',
            'carry-over-time' => 'Tempo di trasferimento',
            'carry-over-day' => 'Giorno del trasferimento',
            'carry-over-month' => 'Mese di trasferimento',
        ],
    ],
];
