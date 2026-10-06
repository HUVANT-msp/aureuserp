<?php

return [
    'title' => 'Gestire i magazzini',
    'form' => [
        'enable-locations' => 'Ubicazioni',
        'enable-locations-helper-text' => 'Tieni traccia della posizione dei prodotti nel magazzino.',
        'configure-locations' => 'Imposta le posizioni',
        'enable-multi-steps-routes' => 'Percorsi a più tappe',
        'enable-multi-steps-routes-helper-text' => 'Utilizza i tuoi percorsi per gestire il trasferimento dei prodotti tra magazzini.',
        'configure-routes' => 'Configura i percorsi del magazzino',
    ],
    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Ci sono più magazzini',
                'body' => 'Non è possibile disabilitare la multiubicazione se è presente più di un magazzino.',
            ],
        ],
    ],
];
