<?php

return [
    'title' => 'Aziende',
    'navigation' => [
        'title' => 'Aziende',
    ],
    'global-search' => [
        'email' => 'Email',
    ],
    'form' => [
        'sections' => [
            'company-information' => [
                'title' => 'Informazioni aziendali',
                'fields' => [
                    'name' => 'Nome dell\'azienda',
                    'registration-number' => 'Numero di registrazione',
                    'company-id' => 'Identificativo dell\'azienda',
                    'tax-id' => 'Codice fiscale',
                    'tax-id-tooltip' => 'Il codice fiscale è un identificativo univoco della tua azienda.',
                    'website' => 'Sito web',
                ],
            ],
            'address-information' => [
                'title' => 'Informazioni sull\'indirizzo',
                'fields' => [
                    'street1' => 'Via 1',
                    'street2' => 'Via 2',
                    'city' => 'Città',
                    'zipcode' => 'Codice postale',
                    'country' => 'Paese',
                    'currency-name' => 'Nome della valuta',
                    'phone-code' => 'Codice telefonico',
                    'code' => 'Codice',
                    'country-name' => 'Nome del paese',
                    'state-required' => 'Stato richiesto',
                    'zip-required' => 'Codice postale richiesto',
                    'create-country' => 'Crea paese',
                    'state' => 'Stato',
                    'state-name' => 'Nome dello stato',
                    'state-code' => 'Codice dello stato',
                    'create-state' => 'Crea stato',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'fields' => [
                    'default-currency' => 'Valuta predefinita',
                    'currency-name' => 'Nome della valuta',
                    'currency-full-name' => 'Nome completo della valuta',
                    'currency-symbol' => 'Simbolo di valuta',
                    'currency-iso-numeric' => 'Codice ISO numerico della valuta',
                    'currency-decimal-places' => 'Decimali di valuta',
                    'currency-rounding' => 'Arrotondamento valutario',
                    'currency-status' => 'Stato della moneta',
                    'company-foundation-date' => 'Data di fondazione dell\'azienda',
                    'currency-create' => 'Crea valuta',
                    'status' => 'Stato',
                ],
            ],
            'branding' => [
                'title' => 'Marchio',
                'fields' => [
                    'company-logo' => 'Logo aziendale',
                    'color' => 'Colore',
                ],
            ],
            'contact-information' => [
                'title' => 'Informazioni di contatto',
                'fields' => [
                    'email' => 'Indirizzo e-mail',
                    'phone' => 'Numero di telefono',
                    'mobile' => 'Numero di telefono',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'logo' => 'Marchio',
            'company-name' => 'Nome dell\'azienda',
            'branches' => 'Rami',
            'email' => 'Email',
            'city' => 'Città',
            'country' => 'Paese',
            'currency' => 'Valuta',
            'status' => 'Stato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'company-name' => 'Nome dell\'azienda',
            'city' => 'Città',
            'country' => 'Paese',
            'state' => 'Stato',
            'email' => 'Email',
            'phone' => 'Telefono',
            'currency' => 'Valuta',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'status' => 'Stato',
            'country' => 'Paese',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Azienda modificata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Azienda cancellata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Azienda restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aziende restaurate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Aziende eliminate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Aziende rimosse definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Aziende create',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'company-information' => [
                'title' => 'Informazioni aziendali',
                'entries' => [
                    'name' => 'Nome dell\'azienda',
                    'registration-number' => 'Numero di registrazione',
                    'company-id' => 'Identificativo dell\'azienda',
                    'tax-id' => 'Codice fiscale',
                    'tax-id-tooltip' => 'Il codice fiscale è un identificativo univoco della tua azienda.',
                    'website' => 'Sito web',
                ],
            ],
            'address-information' => [
                'title' => 'Informazioni sull\'indirizzo',
                'entries' => [
                    'street1' => 'Via 1',
                    'street2' => 'Via 2',
                    'city' => 'Città',
                    'zipcode' => 'Codice postale',
                    'country' => 'Paese',
                    'currency-name' => 'Nome della valuta',
                    'phone-code' => 'Codice telefonico',
                    'code' => 'Codice',
                    'country-name' => 'Nome del paese',
                    'state-required' => 'Stato richiesto',
                    'zip-required' => 'Codice postale richiesto',
                    'create-country' => 'Crea paese',
                    'state' => 'Stato',
                    'state-name' => 'Nome dello stato',
                    'state-code' => 'Codice dello stato',
                    'create-state' => 'Crea stato',
                ],
            ],
            'additional-information' => [
                'title' => 'Informazioni aggiuntive',
                'entries' => [
                    'default-currency' => 'Valuta predefinita',
                    'currency-name' => 'Nome della valuta',
                    'currency-full-name' => 'Nome completo della valuta',
                    'currency-symbol' => 'Simbolo di valuta',
                    'currency-iso-numeric' => 'Codice ISO numerico della valuta',
                    'currency-decimal-places' => 'Decimali di valuta',
                    'currency-rounding' => 'Arrotondamento valutario',
                    'currency-status' => 'Stato della moneta',
                    'company-foundation-date' => 'Data di fondazione dell\'azienda',
                    'currency-create' => 'Crea valuta',
                    'status' => 'Stato',
                ],
            ],
            'branding' => [
                'title' => 'Marchio',
                'entries' => [
                    'company-logo' => 'Logo aziendale',
                    'color' => 'Colore',
                ],
            ],
            'contact-information' => [
                'title' => 'Informazioni di contatto',
                'entries' => [
                    'email' => 'Indirizzo e-mail',
                    'phone' => 'Numero di telefono',
                    'mobile' => 'Numero di telefono',
                ],
            ],
        ],
    ],
];
