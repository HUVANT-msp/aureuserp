<?php

return [
    'label' => 'Crea fattura',
    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'Non ci sono linee fatturabili',
                'body' => 'Non è presente alcuna linea fatturabile, assicurati che sia stato ricevuto un importo.',
            ],
            'missing-journal' => [
                'title' => 'La contabilità non è impostata',
            ],
            'success' => [
                'title' => 'Fattura fornitore creata',
                'body' => 'Creazione completata con successo.',
            ],
        ],
    ],
];
