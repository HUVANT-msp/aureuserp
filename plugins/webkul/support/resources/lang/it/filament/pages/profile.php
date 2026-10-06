<?php

return [
    'title' => 'Profilo',
    'heading' => 'Profilo',
    'subheading' => 'Gestisci le impostazioni e le preferenze dell\'account.',
    'information_section' => 'Informazioni sul profilo',
    'information_description' => 'Aggiorna le informazioni del profilo e l\'e-mail dell\'account.',
    'notification' => [
        'success' => [
            'title' => 'Profilo aggiornato',
            'body' => 'Aggiornamento completato con successo.',
        ],
        'error' => [
            'title' => 'Errore durante l\'aggiornamento del profilo',
            'body' => 'Si è verificato un errore durante l\'aggiornamento del profilo.',
        ],
        'validation-error' => [
            'title' => 'Errore di convalida',
        ],
    ],
    'actions' => [
        'save' => 'Salva modifiche',
    ],
    'fields' => [
        'avatar' => 'Foto del profilo',
        'name' => 'Nome',
        'email' => 'Email',
        'language' => 'Lingua preferita',
        'language_helper' => 'L\'interfaccia di amministrazione verrà visualizzata in questa lingua.',
    ],
    'password' => [
        'section' => 'Aggiorna password',
        'description' => 'Assicurati che l\'account utilizzi una password lunga e casuale per maggiore sicurezza.',
        'current' => 'Password attuale',
        'new' => 'Nuova password',
        'confirm' => 'Conferma la password',
        'helper' => 'Deve contenere almeno 8 caratteri.',
        'errors' => [
            'current-required' => 'È richiesta la password attuale.',
            'current-incorrect' => 'La password attuale non è corretta. Per favore riprova.',
            'same-as-current' => 'La nuova password deve essere diversa dalla password attuale.',
        ],
        'current-helper' => 'Inserisci la password attuale per verificare la tua identità.',
        'notification' => [
            'success' => [
                'title' => 'Password aggiornata',
                'body' => 'Aggiornamento completato con successo.',
            ],
            'error' => [
                'title' => 'Errore durante l\'aggiornamento della password',
                'body' => 'Si è verificato un errore durante l\'aggiornamento della password.',
            ],
        ],
    ],
];
