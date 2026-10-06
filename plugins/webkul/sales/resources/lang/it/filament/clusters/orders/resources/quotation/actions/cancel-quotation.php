<?php

return [
    'title' => 'Annulla',
    'modal' => [
        'heading' => 'Annulla preventivo',
        'description' => 'Sei sicuro di voler annullare questo preventivo?',
    ],
    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Invia e annulla',
            'notification' => [
                'cancelled' => [
                    'title' => 'Budget annullato',
                    'body' => 'Il preventivo è stato annullato e l\'email è stata inviata correttamente.',
                ],
            ],
        ],
        'cancel' => [
            'title' => 'Annulla',
            'notification' => [
                'cancelled' => [
                    'title' => 'Budget annullato',
                    'body' => 'Il preventivo è stato cancellato con successo.',
                ],
            ],
        ],
        'close' => [
            'title' => 'Chiudi',
        ],
    ],
    'form' => [
        'fields' => [
            'partner' => 'Partner',
            'subject' => 'Oggetto',
            'subject-placeholder' => 'Oggetto',
            'subject-default' => 'Il preventivo :name è stato annullato per l\'ordine di vendita n.:id',
            'description' => 'Descrizione',
            'description-default' => 'Gentile <b>:partner_name</b>, <br/><br/>ti informiamo che il tuo ordine di vendita <b>:name</b> è stato annullato. Di conseguenza, a questo ordine non verranno applicati ulteriori addebiti. Se è necessario un rimborso, verrà elaborato il prima possibile.<br/><br/>Se hai domande o hai bisogno di ulteriore assistenza, non esitare a contattarci.',
        ],
    ],
];
