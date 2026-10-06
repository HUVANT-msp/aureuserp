<?php

return [
    'title' => 'Gestire la tracciabilità',
    'form' => [
        'enable-lots-serial-numbers' => 'Lotti e numeri di serie',
        'enable-lots-serial-numbers-helper-text' => 'Ottieni la tracciabilità completa dai fornitori ai clienti.',
        'configure-lots' => 'Imposta i batch',
        'enable-expiration-dates' => 'Date di scadenza',
        'enable-expiration-dates-helper-text' => 'Imposta le date di scadenza su lotti e numeri di serie.',
        'display-on-delivery-slips' => 'Mostrare nella prova di consegna',
        'display-on-delivery-slips-helper-text' => 'I lotti e i numeri di serie appariranno sulle ricevute di consegna',
        'display-expiration-dates-on-delivery-slips' => 'Mostra le date di scadenza nella prova di consegna',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Le date di scadenza appariranno sulla ricevuta di consegna',
        'enable-consignments' => 'Stanziamenti',
        'enable-consignments-helper-text' => 'Assegnare il proprietario ai prodotti archiviati',
    ],
    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'I prodotti sono disponibili con il monitoraggio del numero di lotto/serie abilitato.',
                'body' => 'Disattiva innanzitutto il tracciamento su tutti i prodotti prima di disattivare questa impostazione.',
            ],
        ],
    ],
];
