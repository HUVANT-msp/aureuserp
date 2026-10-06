<?php

return [
    'title' => 'Gestire le operazioni',
    'form' => [
        'enable-work-orders' => [
            'label' => 'Ordini di lavoro',
            'helper-text' => 'Eseguire operazioni nei centri di lavoro designati.',
            'link-text' => 'Istituire centri di lavoro',
        ],
        'enable-work-order-dependencies' => [
            'label' => 'Dipendenze dall\'ordine di lavoro',
            'helper-text' => 'Definire l\'ordine in cui devono essere elaborati gli ordini di lavoro. Attiva questa funzione dalla scheda Varie di ogni LdM.',
        ],
        'enable-byproducts' => [
            'label' => 'Sottoprodotti',
            'helper-text' => 'Genera sottoprodotti durante la produzione (A + B → C + D).',
        ],
    ],
];
