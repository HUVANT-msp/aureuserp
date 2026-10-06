<?php

return [
    'title' => 'Invia tramite e-mail',
    'resend-title' => 'Invia nuovamente tramite e-mail',
    'quotation' => 'bilancio',
    'quotations' => 'budget',
    'modal' => [
        'heading' => 'Invia preventivo via e-mail',
    ],
    'form' => [
        'fields' => [
            'partners' => 'Partner',
            'subject' => 'Oggetto',
            'description' => 'Descrizione',
            'attachment' => 'In allegato',
        ],
    ],
    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Nessun destinatario selezionato',
                    'body' => 'Seleziona almeno un contatto a cui inviare i preventivi.',
                ],
                'all_success' => [
                    'title' => 'Preventivi inviati!',
                    'body' => 'I tuoi :plural sono stati consegnati con successo a: :recipients',
                ],
                'all_failed' => [
                    'title' => 'Impossibile inviare preventivi',
                    'body' => 'Si sono verificati problemi nell\'invio dei tuoi preventivi: :failures',
                ],
                'partial_success' => [
                    'title' => 'Alcuni preventivi inviati',
                    'sent_part' => 'Consegnato con successo a: :recipients',
                    'failed_part' => 'Impossibile consegnare a: :failures',
                ],
                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],
];
