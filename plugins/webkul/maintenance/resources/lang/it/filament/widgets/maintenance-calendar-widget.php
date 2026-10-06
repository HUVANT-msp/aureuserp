<?php

return [
    'heading' => [
        'title' => 'Programma di manutenzione',
    ],
    'config' => [
        'button-text' => [
            'today' => 'Oggi',
            'year' => 'Anno',
            'month' => 'Mese',
            'week' => 'Settimana',
            'list' => 'Elenco',
        ],
    ],
    'header-actions' => [
        'create' => [
            'label' => 'Nuova richiesta',
            'modal-heading' => 'Nuova richiesta di manutenzione',
            'notification' => [
                'success' => [
                    'title' => 'Richiesta di manutenzione creata',
                    'body' => 'Creazione completata con successo.',
                ],
                'error' => [
                    'title' => 'Impossibile creare la richiesta di manutenzione',
                    'body' => 'Per prima cosa crea una fase e una squadra di manutenzione.',
                ],
            ],
        ],
    ],
    'view-action' => [
        'label' => 'Visualizza',
    ],
    'modal-actions' => [
        'edit' => [
            'label' => 'Modifica',
        ],
    ],
    'form' => [
        'fields' => [
            'subject' => 'Oggetto',
            'scheduled-at' => 'Previsto per',
        ],
    ],
    'infolist' => [
        'title' => 'Richiesta di manutenzione',
        'entries' => [
            'subject' => 'Oggetto',
            'date' => 'Data',
            'time' => 'Tempo',
            'technician' => 'Tecnico',
            'priority' => 'Priorità',
            'maintenance-type' => 'Tipo di manutenzione',
            'stage' => 'Fase',
        ],
    ],
];
