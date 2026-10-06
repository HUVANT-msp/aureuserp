<?php

return [
    'setup' => [
        'title' => 'Invia messaggio',
        'submit-title' => 'Invia',
        'form' => [
            'fields' => [
                'hide-subject' => 'Nascondi oggetto',
                'add-subject' => 'Aggiungi oggetto',
                'subject' => 'Oggetto',
                'write-message-here' => 'Scrivi qui il tuo messaggio',
                'attachments-helper-text' => 'Dimensione massima del file: 10 MB. Tipi consentiti: immagini, PDF, Word, Excel, testo',
            ],
        ],
        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Messaggio inviato',
                    'body' => 'Il tuo messaggio è stato inviato con successo.',
                ],
                'error' => [
                    'title' => 'Errore durante l\'invio del messaggio',
                    'body' => 'Non è stato possibile inviare il tuo messaggio',
                ],
            ],
            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
