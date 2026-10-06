<?php

return [
    'title' => 'Ruolo aziendale',
    'navigation' => [
        'title' => 'Ruoli aziendali',
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'manager-name' => 'Responsabile',
            'company-name' => 'Azienda',
        ],
        'actions' => [
            'applications' => [
                'new-applications' => ':count nuovi candidati',
            ],
            'to-recruitment' => [
                'to-recruitment' => ':count da reclutare',
            ],
            'total-application' => [
                'total-application' => ':count nomination',
            ],
        ],
    ],
];
