<?php

return [
    'navigation' => [
        'title' => 'Ordini di produzione',
        'group' => 'Operazioni',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                    'bill-of-material' => 'Distinta base',
                    'scheduled-date' => 'Data prevista',
                    'scheduled-end' => 'Fine prevista',
                    'responsible' => 'Responsabile',
                    'to-produce' => 'Per produrre',
                    'to-produce-placeholder' => 'Anteprima dell\'immagine',
                    'uom-placeholder' => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componenti',
                'add-action' => 'Aggiungi una riga',
                'process-note' => 'I componenti verranno generati durante la realizzazione del processo di produzione.',
                'columns' => [
                    'component' => 'Prodotto',
                    'from' => 'Da allora',
                    'to-consume' => 'Consumare',
                    'to-consume-tooltip' => 'Quantità disponibile insufficiente',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                    'forecast' => 'Previsione',
                ],
            ],
            'work-orders' => [
                'title' => 'Ordini di lavoro',
                'add-action' => 'Aggiungi una riga',
                'process-note' => 'Gli ordini di lavoro verranno generati dopo aver impostato il processo di produzione.',
                'columns' => [
                    'operation' => 'Operazione',
                    'work-center' => 'Centro di lavoro',
                    'product' => 'Prodotto',
                    'quantity-remaining' => 'Quantità rimanente',
                    'quantity-produced' => 'Quantità prodotta',
                    'start' => 'Casa',
                    'end' => 'Fine',
                    'expected-duration' => 'Durata prevista',
                    'real-duration' => 'Durata effettiva',
                    'status' => 'Stato',
                    'lot-serial' => 'Lotto/serie',
                ],
                'actions' => [
                    'open-work-order' => [
                        'tooltip' => 'Apri ordine di lavoro',
                    ],
                    'done' => [
                        'label' => 'Completato',
                    ],
                ],
            ],
            'by-products' => [
                'title' => 'Sottoprodotti',
                'process-note' => 'I sottoprodotti verranno generati durante la realizzazione del processo di produzione.',
                'columns' => [
                    'product' => 'Prodotto',
                    'to' => 'A',
                    'to-produce' => 'Per produrre',
                    'uom' => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Vari',
                'fields' => [
                    'operation-type' => 'Tipo di operazione',
                    'source' => 'Fonte',
                    'finished-products-location' => 'Ubicazione dei prodotti finiti',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'reference' => 'Riferimento',
            'start' => 'Casa',
            'end' => 'Fine',
            'deadline' => 'Scadenza',
            'product' => 'Prodotto',
            'lot-serial-number' => 'Numero di lotto/serie',
            'bill-of-material' => 'Distinta base',
            'source' => 'Fonte',
            'responsible' => 'Responsabile',
            'mo-readiness' => 'DI Disponibilità',
            'component-status' => 'Stato del componente',
            'quantity' => 'Quantità',
            'uom' => 'UoM',
            'consumption-efficiency' => 'Efficienza nei consumi',
            'expected-duration' => 'Durata prevista',
            'real-duration' => 'Durata effettiva',
            'company' => 'Azienda',
            'state' => 'Stato',
        ],
        'groups' => [
            'state' => 'Stato',
            'product' => 'Prodotto',
            'bill-of-material' => 'Distinta base',
            'responsible' => 'Responsabile',
            'deadline' => 'Scadenza',
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'product' => 'Prodotto',
                    'scheduled-date' => 'Data prevista',
                    'responsible' => 'Responsabile',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                    'bill-of-material' => 'Distinta base',
                    'operation-type' => 'Tipo di operazione',
                    'consumption-efficiency' => 'Efficienza nei consumi',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componenti',
                'process-note' => 'I componenti saranno disponibili dopo aver impostato il processo di produzione.',
                'columns' => [
                    'component' => 'Componente',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                ],
            ],
            'work-orders' => [
                'title' => 'Ordini di lavoro',
                'process-note' => 'Gli ordini di lavoro saranno disponibili dopo aver impostato il processo di produzione.',
                'columns' => [
                    'operation' => 'Operazione',
                    'work-center' => 'Centro di lavoro',
                    'product' => 'Prodotto',
                    'quantity-remaining' => 'Quantità rimanente',
                    'expected-duration' => 'Durata prevista',
                    'real-duration' => 'Durata effettiva',
                    'lot-serial' => 'Lotto/serie',
                    'start' => 'Casa',
                    'end' => 'Fine',
                ],
            ],
            'by-products' => [
                'title' => 'Sottoprodotti',
                'process-note' => 'I sottoprodotti saranno disponibili dopo aver impostato il processo di produzione.',
                'columns' => [
                    'product' => 'Prodotto',
                    'to' => 'A',
                    'to-produce' => 'Per produrre',
                    'uom' => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Vari',
                'entries' => [
                    'operation-type' => 'Tipo di operazione',
                    'source' => 'Fonte',
                    'finished-products-location' => 'Ubicazione dei prodotti finiti',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label' => 'Conferma',
                    'notification' => [
                        'title' => 'Ordine di produzione confermato',
                    ],
                ],
                'cancel' => [
                    'label' => 'Annulla',
                    'notification' => [
                        'title' => 'Ordine di produzione annullato',
                    ],
                ],
            ],
        ],
    ],
];
