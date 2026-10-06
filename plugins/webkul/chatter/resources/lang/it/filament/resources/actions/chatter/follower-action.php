<?php

return [
    'setup' => [
        'title' => 'Osservatori',
        'submit-action-title' => 'Aggiungi seguace',
        'tooltip' => 'Aggiungi seguace',
        'form' => [
            'fields' => [
                'recipients' => 'Destinatari',
                'notify-user' => 'Avvisa l\'utente',
                'add-a-note' => 'Aggiungi una nota',
            ],
        ],
        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Aggiunto seguace',
                    'body' => 'Il follower è stato aggiunto con successo.',
                ],
                'partial_message' => [
                    'title' => 'Messaggio inviato con un avviso',
                    'single' => 'Il follower :count non è stato informato per mancanza di email: :names',
                    'multiple' => 'I follower :count non sono stati informati a causa della mancanza di email: :names',
                ],
                'error' => [
                    'title' => 'Errore nell\'aggiunta del follower',
                    'body' => 'Impossibile aggiungere ":partner" come follower',
                ],
            ],
            'mail' => [
                'subject' => 'Invito a seguire :model: :department',
            ],
        ],
    ],
];
