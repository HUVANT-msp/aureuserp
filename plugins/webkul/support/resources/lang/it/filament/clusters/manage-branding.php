<?php

return [
    'breadcrumb' => 'Marchio',
    'title' => 'Marchio',
    'group' => 'Generale',
    'navigation' => [
        'label' => 'Marchio',
    ],
    'form' => [
        'sections' => [
            'logo' => [
                'title' => 'Logo e icona preferita',
                'description' => 'Sostituisci loghi, favicon e altezza del logo utilizzati nei pannelli di amministrazione e client. Lasciare un campo vuoto per mantenere il valore predefinito.',
            ],
            'colors' => [
                'title' => 'Colori',
                'description' => 'Sostituisci i colori del tema utilizzati nei pannelli di amministrazione e client. Lascia un colore vuoto per mantenere il valore predefinito.',
            ],
        ],
        'fields' => [
            'light-logo' => 'Marchio chiaro',
            'light-logo-helper' => 'Indicato su sfondi chiari. Sostituisce il logo predefinito.',
            'dark-logo' => 'Marchio scuro',
            'dark-logo-helper' => 'Visualizzato quando è attivata la modalità oscura.',
            'favicon' => 'Favicon',
            'favicon-helper' => 'Icona della scheda del browser.',
            'logo-height' => 'Altezza del logo',
            'logo-height-helper' => 'Un valore di altezza CSS, ad es. per esempio. 2rem o 40px.',
            'primary-color' => 'Primario',
            'gray-color' => 'Grigio',
            'danger-color' => 'Pericolo',
            'info-color' => 'Informazioni',
            'success-color' => 'Successo',
            'warning-color' => 'Avvertimento',
        ],
    ],
    'actions' => [
        'reset' => [
            'label' => 'Ripristina il valore predefinito',
        ],
    ],
];
