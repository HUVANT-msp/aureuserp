<?php

return [
    'navigation' => [
        'title' => 'Colli',
        'group' => 'Magazzino',
    ],
    'global-search' => [
        'name' => 'Nome',
        'package-type' => 'Tipo di collo',
        'location' => 'Ubicazione',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'pag. per esempio. PACCO007',
                    'package-type' => 'Tipo di collo',
                    'pack-date' => 'Data di imballaggio',
                    'location' => 'Ubicazione',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'package-type' => 'Tipo di collo',
            'location' => 'Ubicazione',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'package-type' => 'Tipo di collo',
            'location' => 'Ubicazione',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'package-type' => 'Tipo di collo',
            'location' => 'Ubicazione',
            'creator' => 'Creato da',
            'company' => 'Azienda',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pacchetto eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il pacchetto',
                        'body' => 'Il pacchetto non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Stampa codice a barre',
            ],
            'print-with-content' => [
                'label' => 'Stampa il codice a barre con il contenuto',
            ],
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pacchetti rimossi',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile rimuovere i pacchetti',
                        'body' => 'I pacchetti non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Dettagli del pacchetto',
                'entries' => [
                    'name' => 'Nome del pacchetto',
                    'package-type' => 'Tipo di collo',
                    'pack-date' => 'Data di imballaggio',
                    'location' => 'Ubicazione',
                    'company' => 'Azienda',
                    'created-at' => 'Data creazione',
                    'updated-at' => 'Ultimo aggiornamento',
                ],
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
