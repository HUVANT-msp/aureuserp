<?php

return [
    'setup' => [
        'title' => 'Aggiungi nota',
        'submit-title' => 'Registrati',
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
                    'title' => 'Nota registrata',
                    'body' => 'La tua nota è stata registrata con successo.',
                ],
                'error' => [
                    'title' => 'Errore durante la registrazione della nota',
                    'body' => 'Non è stato possibile registrare la tua nota',
                ],
            ],
        ],
    ],
];
