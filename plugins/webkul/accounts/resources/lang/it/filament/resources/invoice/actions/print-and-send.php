<?php

return [
    'title' => 'Stampa e invia',
    'modal' => [
        'title' => 'Anteprima della fattura',
        'form' => [
            'partners' => 'Cliente',
            'subject' => 'Oggetto',
            'description' => 'Descrizione',
            'files' => 'In allegato',
        ],
        'action' => [
            'submit' => [
                'title' => 'Invia',
            ],
        ],
        'notification' => [
            'invoice-sent' => [
                'title' => 'Fattura inviata',
                'body' => 'La fattura è stata inviata con successo.',
            ],
        ],
    ],
];
