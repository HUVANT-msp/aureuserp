<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'sections' => [
                    'branch-information' => [
                        'title' => 'Informazioni sulla filiale',
                        'fields' => [
                            'company-name' => 'Nome dell\'azienda',
                            'registration-number' => 'Numero di registrazione',
                            'tax-id' => 'Codice fiscale',
                            'tax-id-tooltip' => 'Il codice fiscale è un identificativo univoco della tua azienda.',
                            'color' => 'Colore',
                            'company-id' => 'Identificativo dell\'azienda',
                            'company-id-tooltip' => 'L\'ID azienda è un identificatore univoco della tua azienda.',
                        ],
                    ],
                    'branding' => [
                        'title' => 'Marchio',
                        'fields' => [
                            'branch-logo' => 'Marchio della filiale',
                        ],
                    ],
                ],
            ],
            'address-information' => [
                'title' => 'Informazioni sull\'indirizzo',
                'sections' => [
                    'address-information' => [
                        'title' => 'Informazioni sull\'indirizzo',
                        'fields' => [
                            'street1' => 'Via 1',
                            'street2' => 'Via 2',
                            'city' => 'Città',
                            'zip' => 'Codice postale',
                            'country' => 'Paese',
                            'country-currency-name' => 'Nome della valuta',
                            'country-phone-code' => 'Codice telefonico',
                            'country-code' => 'Codice',
                            'country-name' => 'Nome del paese',
                            'country-state-required' => 'Stato richiesto',
                            'country-zip-required' => 'Codice postale richiesto',
                            'country-create' => 'Crea paese',
                            'state' => 'Stato',
                            'state-name' => 'Nome dello stato',
                            'state-code' => 'Codice dello stato',
                            'zip-code' => 'Codice postale',
                            'state-create' => 'Crea stato',
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
                            'currency-create' => 'Crea valuta',
                            'company-foundation-date' => 'Data di fondazione dell\'azienda',
                            'status' => 'Stato',
                        ],
                    ],
                ],
            ],
            'contact-information' => [
                'title' => 'Informazioni di contatto',
                'sections' => [
                    'contact-information' => [
                        'title' => 'Informazioni di contatto',
                        'fields' => [
                            'email-address' => 'Indirizzo e-mail',
                            'phone-number' => 'Numero di telefono',
                            'mobile-number' => 'Numero di telefono',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'logo' => 'Marchio',
            'company-name' => 'Nome della filiale',
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
            'company-name' => 'Nome della filiale',
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
            'trashed' => 'Eliminato',
            'status' => 'Stato',
            'country' => 'Paese',
        ],
        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Filiale creata',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ramo aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ramo rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Filiale restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rami restaurati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Rami rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Rami rimossi definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'sections' => [
                    'branch-information' => [
                        'title' => 'Informazioni sulla filiale',
                        'entries' => [
                            'company-name' => 'Nome dell\'azienda',
                            'registration-number' => 'Numero di registrazione',
                            'tax-id' => 'Codice fiscale',
                            'registration-number-tooltip' => 'Il codice fiscale è un identificativo univoco della tua azienda.',
                            'color' => 'Colore',
                        ],
                    ],
                    'branding' => [
                        'title' => 'Marchio',
                        'entries' => [
                            'branch-logo' => 'Marchio della filiale',
                        ],
                    ],
                ],
            ],
            'address-information' => [
                'title' => 'Informazioni sull\'indirizzo',
                'sections' => [
                    'address-information' => [
                        'title' => 'Informazioni sull\'indirizzo',
                        'entries' => [
                            'street1' => 'Via 1',
                            'street2' => 'Via 2',
                            'city' => 'Città',
                            'zip' => 'Codice postale',
                            'country' => 'Paese',
                            'country-currency-name' => 'Nome della valuta',
                            'country-phone-code' => 'Codice telefonico',
                            'country-code' => 'Codice',
                            'country-name' => 'Nome del paese',
                            'country-state-required' => 'Stato richiesto',
                            'country-zip-required' => 'Codice postale richiesto',
                            'country-create' => 'Crea paese',
                            'state' => 'Stato',
                            'state-name' => 'Nome dello stato',
                            'state-code' => 'Codice dello stato',
                            'zip-code' => 'Codice postale',
                            'state-create' => 'Crea stato',
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
                            'currency-create' => 'Crea valuta',
                            'company-foundation-date' => 'Data di fondazione dell\'azienda',
                            'status' => 'Stato',
                        ],
                    ],
                ],
            ],
            'contact-information' => [
                'title' => 'Informazioni di contatto',
                'sections' => [
                    'contact-information' => [
                        'title' => 'Informazioni di contatto',
                        'entries' => [
                            'email-address' => 'Indirizzo e-mail',
                            'phone-number' => 'Numero di telefono',
                            'mobile-number' => 'Numero di telefono',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
