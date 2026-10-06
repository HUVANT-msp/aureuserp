<?php

return [
    'title' => 'Utenti',
    'navigation' => [
        'title' => 'Utenti',
    ],
    'global-search' => [
        'email' => 'Email',
    ],
    'form' => [
        'validation' => [
            'cannot-remove-last-admin' => 'Non è possibile rimuovere il ruolo di amministratore dall\'ultimo utente amministratore.',
            'first-user-must-be-admin' => 'Al primo utente del sistema deve essere assegnato un ruolo di amministratore.',
        ],
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'name' => 'Nome',
                    'email' => 'Email',
                    'password' => 'Password',
                    'password-confirmation' => 'Conferma password',
                ],
            ],
            'permissions' => [
                'title' => 'Permessi',
                'fields' => [
                    'roles' => 'Ruoli',
                    'permissions' => 'Permessi',
                    'resource-permission' => 'Autorizzazione delle risorse',
                    'resource-permission-self-change-disabled' => 'Non puoi modificare l\'autorizzazione della tua risorsa. Chiedi a un altro amministratore di aggiornarlo.',
                    'teams' => 'Team',
                ],
            ],
            'avatar' => [
                'title' => 'Avatar',
            ],
            'lang-and-status' => [
                'title' => 'Lingua e status',
                'fields' => [
                    'language' => 'Lingua preferita',
                    'status' => 'Stato',
                ],
            ],
            'multi-company' => [
                'title' => 'Multi-azienda',
                'allowed-companies' => 'Aziende autorizzate',
                'default-company' => 'Azienda predefinita',
                'default-company-not-allowed' => 'La società predefinita deve essere una delle società consentite.',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'avatar' => 'Avatar',
            'name' => 'Nome',
            'email' => 'Email',
            'teams' => 'Team',
            'role' => 'Ruolo',
            'resource-permission' => 'Autorizzazione delle risorse',
            'default-company' => 'Azienda predefinita',
            'allowed-company' => 'Azienda autorizzata',
            'created-by' => 'Creato da',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'resource-permission' => 'Autorizzazione delle risorse',
            'teams' => 'Team',
            'roles' => 'Ruoli',
            'default-company' => 'Azienda predefinita',
            'allowed-companies' => 'Aziende autorizzate',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Utente modificato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Utente eliminato',
                    'body' => 'Eliminazione completata con successo.',
                    'error' => [
                        'title' => 'Impossibile eliminare l\'utente',
                        'body' => 'Questo è un utente predefinito oppure non puoi eliminarti.',
                    ],
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Utente ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Utenti ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Utenti eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Utenti eliminati definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                    'error' => [
                        'title' => 'Impossibile eliminare l\'utente',
                        'body' => 'L\'utente non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Utenti creati',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome',
                    'email' => 'Email',
                    'password' => 'Password',
                    'password-confirmation' => 'Conferma password',
                ],
            ],
            'permissions' => [
                'title' => 'Permessi',
                'entries' => [
                    'roles' => 'Ruoli',
                    'permissions' => 'Permessi',
                    'resource-permission' => 'Autorizzazione delle risorse',
                    'teams' => 'Team',
                ],
            ],
            'avatar' => [
                'title' => 'Avatar',
            ],
            'lang-and-status' => [
                'title' => 'Lingua e status',
                'entries' => [
                    'language' => 'Lingua preferita',
                    'status' => 'Stato',
                ],
            ],
            'multi-company' => [
                'title' => 'Multi-azienda',
                'allowed-companies' => 'Aziende autorizzate',
                'default-company' => 'Azienda predefinita',
                'default-company-not-allowed' => 'La società predefinita deve essere una delle società consentite.',
            ],
        ],
    ],
];
