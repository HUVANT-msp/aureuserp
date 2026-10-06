<?php

return [
    'title' => 'Gestisci la fattura',
    'breadcrumb' => 'Gestisci la fattura',
    'navigation' => [
        'title' => 'Gestisci la fattura',
    ],
    'form' => [
        'invoice-policy' => [
            'label' => 'Politica di fatturazione',
            'label-help' => 'Definire la modalità di generazione delle fatture dagli ordini cliente.',
            'options' => [
                'order' => 'Genera fattura in base alle quantità ordinate',
                'delivery' => 'Genera fattura in base alle quantità consegnate',
            ],
        ],
    ],
];
