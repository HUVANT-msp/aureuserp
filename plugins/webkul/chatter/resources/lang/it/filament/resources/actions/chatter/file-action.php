<?php

return [
    'setup' => [
        'title' => 'Allegati',
        'tooltip' => 'Carica allegati',
        'modal-submit-action-label' => 'Carica',
        'form' => [
            'fields' => [
                'files' => 'File',
                'attachment-helper-text' => 'Dimensione massima del file: 10 MB. Tipi consentiti: immagini, PDF, Word, Excel, testo',
                'actions' => [
                    'delete' => [
                        'title' => 'File eliminato',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                ],
            ],
        ],
        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Allegati caricati',
                    'body' => 'Gli allegati sono stati caricati con successo.',
                ],
                'warning' => [
                    'title' => 'Non ci sono nuovi file',
                    'body' => 'Tutti i file sono già stati caricati.',
                ],
                'error' => [
                    'title' => 'Errore durante il caricamento dell\'allegato',
                    'body' => 'Impossibile caricare gli allegati',
                ],
            ],
        ],
    ],
];
