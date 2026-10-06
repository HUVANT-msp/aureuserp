<?php

return [
    'navigation' => [
        'title' => 'Rifornimento',
        'group' => 'Approvvigionamento',
    ],
    'form' => [
        'fields' => [

        ],
    ],
    'table' => [
        'columns' => [
            'product' => 'Prodotto',
            'location' => 'Ubicazione',
            'route' => 'Rotta',
            'vendor' => 'Fornitore',
            'trigger' => 'Innesco',
            'on-hand' => 'Giacenza disponibile',
            'min' => 'Minimo',
            'max' => 'Massimo',
            'multiple-quantity' => 'Quantità multipla',
            'to-order' => 'Su richiesta',
            'uom' => 'UoM',
            'company' => 'Azienda',
        ],
        'groups' => [
            'location' => 'Ubicazione',
            'product' => 'Prodotto',
            'category' => 'Categoria',
        ],
        'filters' => [

        ],
        'header-actions' => [
            'create' => [
                'label' => 'Aggiungi rifornimento',
                'notification' => [
                    'title' => 'Aggiunto rifornimento',
                    'body' => 'Il rifornimento è stato aggiunto con successo.',
                ],
                'before' => [
                    'notification' => [
                        'title' => 'Il rifornimento esiste già',
                        'body' => 'Esiste già un rifornimento per questa configurazione. Aggiorna il rifornimento esistente.',
                    ],
                ],
            ],
        ],
        'actions' => [

        ],
    ],
];
