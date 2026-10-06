<?php

return [
    'label' => 'Invia l\'ordine di acquisto tramite e-mail',
    'form' => [
        'fields' => [
            'to' => 'A',
            'subject' => 'Oggetto',
            'message' => 'Messaggio',
        ],
    ],
    'action' => [
        'notification' => [
            'success' => [
                'title' => 'E-mail inviata',
                'body' => 'L\'e-mail è stata inviata con successo.',
            ],
            'warning' => [
                'title' => 'Alcune email non sono state inviate',
                'body' => 'Alcuni fornitori non riceveranno l\'e-mail perché il loro indirizzo e-mail non è disponibile.',
            ],
            'danger' => [
                'title' => 'E-mail non inviata',
                'body' => 'Aggiungi un indirizzo email ai provider selezionati e riprova.',
            ],
        ],
    ],
];
