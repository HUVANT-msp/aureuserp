<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done' => 'Alcuni ordini di lavoro sono già stati eseguiti, quindi questo ordine di produzione non può essere pianificato. Sarebbe un peccato sprecare tutti questi progressi, giusto?',
            'work-orders-already-started' => 'Alcuni ordini di lavoro sono già iniziati, quindi questo ordine di produzione non può essere pianificato. Sarebbe un peccato sprecare tutti questi progressi, giusto?',
        ],
    ],
    'work-center-productivity-log' => [
        'time-tracking' => 'Tempo di monitoraggio: :name',
        'no-performance-productivity-loss' => 'È necessario definire almeno una perdita di produttività non archiviata nella categoria "Prestazioni". Crealo dalla configurazione.',
    ],
    'work-center' => [
        'already-unblocked' => 'È già stato sbloccato.',
    ],
    'work-order' => [
        'unblock-work-center' => 'Sbloccare il centro di lavoro per avviare l\'ordine di lavoro.',
        'already-done-or-cancelled' => 'Impossibile avviare un ordine di lavoro già eseguito o annullato',
        'no-calendar-on-work-center' => 'Non esiste un calendario definito nel centro di lavoro :name.',
        'no-productivity-loss' => 'È necessario definire almeno una perdita di produttività nella categoria "Produttività". Crealo dalla configurazione.',
        'no-performance-loss' => 'È necessario definire almeno una perdita di produttività nella categoria "Prestazioni". Crealo dalla configurazione.',
        'impossible-to-plan' => 'Impossibile pianificare l\'ordine di lavoro. Verificare la disponibilità del centro di lavoro.',
    ],
    'order' => [
        'product-in-byproducts' => 'Non puoi avere :product come prodotto finito e nei sottoprodotti',
        'missing-lot-serial-number' => 'Devi fornire un numero di lotto/serie dei prodotti e "consumarli": :missing_products',
        'serial-number-already-produced' => 'Questo numero di serie per il prodotto :product è già stato prodotto',
        'byproduct-serial-number-already-produced' => 'Il numero di serie :number utilizzato per il sottoprodotto :product è già stato prodotto',
        'component-serial-number-consumed' => 'Il numero di serie :number utilizzato per il componente :component è già stato utilizzato',
        'components-availability' => [
            'available' => 'Disponibile',
            'not-available' => 'Non disponibile',
            'expected' => 'Pianificato :date',
        ],
    ],
];
