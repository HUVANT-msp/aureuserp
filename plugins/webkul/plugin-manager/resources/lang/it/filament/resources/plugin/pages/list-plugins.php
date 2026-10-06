<?php

return [
    'navigation' => [
        'title' => 'Moduli',
    ],
    'tabs' => [
        'apps' => 'Applicazioni',
        'extra' => 'Extra',
        'installed' => 'Installato',
        'not-installed' => 'Non installato',
    ],
    'header-actions' => [
        'sync' => [
            'label' => 'Sincronizza i plugin disponibili',
            'modal-heading' => 'Sincronizza i plugin',
            'modal-description' => 'Questo cercherà e registrerà tutti i nuovi plugin trovati.',
            'modal-submit-action-label' => 'Sincronizza i plugin',
            'notification' => [
                'success' => [
                    'title' => 'Plugin sincronizzati correttamente',
                    'body' => 'Sono stati trovati e sincronizzati nuovi plug-in :count.',
                ],
                'error' => [
                    'title' => 'Errore durante la sincronizzazione dei plugin',
                    'body' => 'Si è verificato un errore (:error) durante la sincronizzazione dei plug-in. Per favore riprova.',
                ],
            ],
        ],
    ],
];
