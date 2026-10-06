<?php

return [
    'group' => [
        'label' => 'Accesso al portale',
    ],
    'grant' => [
        'label' => 'Concedere l\'accesso al portale',
        'modal' => [
            'heading' => 'Concedere l\'accesso al portale',
            'description' => 'Verrà inviata un\'e-mail di invito a :email con un collegamento per impostare una password per il portale clienti.',
        ],
        'notification' => [
            'email-missing' => [
                'title' => 'È richiesto un indirizzo e-mail',
                'body' => 'Aggiungi un indirizzo email a questo contatto prima di concedere l\'accesso al portale.',
            ],
            'email-taken' => [
                'title' => 'L\'indirizzo email è già in uso',
                'body' => 'Un altro contatto con accesso al portale sta già utilizzando questo indirizzo email.',
            ],
            'granted' => [
                'title' => 'Accesso al portale concesso',
                'body' => 'Un invito a impostare una password è stato inviato a :email.',
            ],
            'email-failed' => [
                'title' => 'Accesso al portale concesso, ma non è stato possibile inviare l\'e-mail di invito',
            ],
        ],
    ],
    'change-password' => [
        'label' => 'Cambia password',
        'modal' => [
            'heading' => 'Modifica password portale',
            'description' => 'Imposta una nuova password del portale clienti per :email.',
        ],
        'form' => [
            'password' => [
                'label' => 'Nuova password',
            ],
            'password-confirmation' => [
                'label' => 'Conferma la nuova password',
            ],
        ],
        'notification' => [
            'changed' => [
                'title' => 'Password del portale modificata',
                'body' => 'Il contatto può ora accedere al portale clienti con la nuova password.',
            ],
        ],
    ],
    'password-reset' => [
        'label' => 'Invia reimpostazione password',
        'modal' => [
            'heading' => 'Invia reimpostazione password',
            'description' => 'Un collegamento per la reimpostazione della password verrà inviato a :email.',
        ],
        'notification' => [
            'sent' => [
                'title' => 'Link per la reimpostazione della password inviato',
                'body' => 'Un collegamento per la reimpostazione della password è stato inviato a :email.',
            ],
            'failed' => [
                'title' => 'Impossibile inviare il collegamento per la reimpostazione della password',
            ],
        ],
    ],
    'revoke' => [
        'label' => 'Revoca l\'accesso al portale',
        'modal' => [
            'heading' => 'Revoca l\'accesso al portale',
            'description' => 'Il contatto non sarà più in grado di accedere al portale clienti. Eventuali collegamenti in sospeso per la reimpostazione della password verranno invalidati.',
        ],
        'notification' => [
            'revoked' => [
                'title' => 'Accesso al portale revocato',
                'body' => 'Il contatto non può più accedere al portale clienti.',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'portal-access' => 'Accesso al portale',
        ],
        'filters' => [
            'portal-access' => 'Accesso al portale',
        ],
    ],
    'infolist' => [
        'section' => [
            'title' => 'Portale clienti',
        ],
        'entries' => [
            'status' => [
                'label' => 'Accesso al portale',
                'granted' => 'Concesso',
                'none' => 'Non concesso',
            ],
            'email-verified-at' => [
                'label' => 'E-mail verificata',
                'placeholder' => 'Mai',
            ],
            'last-login-at' => [
                'label' => 'Ultimo accesso al portale',
                'placeholder' => 'Mai',
            ],
        ],
    ],
];
