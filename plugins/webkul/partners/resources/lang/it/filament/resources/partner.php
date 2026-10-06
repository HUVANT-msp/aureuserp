<?php

return [
    'global-search' => [
        'email' => 'Email',
        'phone' => 'Telefono',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'company' => 'Azienda',
                    'avatar' => 'Avatar',
                    'tax-id' => 'Codice fiscale',
                    'job-title' => 'Titolo di lavoro',
                    'phone' => 'Telefono',
                    'mobile' => 'Cellulare',
                    'email' => 'Email',
                    'website' => 'Sito web',
                    'title' => 'Titolo',
                    'name' => 'Nome',
                    'short-name' => 'Nome breve',
                    'tags' => 'Etichetta',
                    'color' => 'Colore',
                ],
                'address' => [
                    'title' => 'Indirizzo',
                    'fields' => [
                        'street1' => 'Via 1',
                        'street2' => 'Via 2',
                        'city' => 'Città',
                        'zip' => 'CAP',
                        'state' => 'Stato',
                        'country' => 'Paese',
                        'name' => 'Nome',
                        'code' => 'Codice',
                    ],
                ],
            ],
        ],
        'tabs' => [
            'sales-purchase' => [
                'title' => 'Vendite e acquisti',
                'fields' => [
                    'responsible' => 'Responsabile',
                    'responsible-hint-text' => 'Questo è il venditore interno responsabile di questo cliente',
                    'company-id' => 'Identificativo dell\'azienda',
                    'company-id-hint-text' => 'Il numero di registrazione della società, utilizzato se diverso dal codice fiscale. Deve essere univoco tra tutti i contatti all\'interno dello stesso Paese.',
                    'reference' => 'Riferimento',
                    'industry' => 'Settore',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'parent' => 'In alto',
            'work-email' => 'E-mail di lavoro',
            'work-phone' => 'Telefono del lavoro',
        ],
        'groups' => [
            'account-type' => 'Tipo di conto',
            'parent' => 'In alto',
            'title' => 'Titolo',
            'job-title' => 'Titolo di lavoro',
            'industry' => 'Settore',
        ],
        'filters' => [
            'account-type' => 'Tipo di conto',
            'name' => 'Nome',
            'email' => 'Email',
            'parent' => 'In alto',
            'title' => 'Titolo',
            'tax-id' => 'Codice fiscale',
            'phone' => 'Telefono',
            'mobile' => 'Cellulare',
            'job-title' => 'Titolo di lavoro',
            'website' => 'Sito web',
            'company-registry' => 'Registro delle imprese',
            'responsible' => 'Responsabile',
            'reference' => 'Riferimento',
            'creator' => 'Creato da',
            'company' => 'Azienda',
            'industry' => 'Settore',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contatto aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Contatto ripristinato',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Contatto eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contatto eliminato definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il contatto',
                        'body' => 'Il contatto non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contatti ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Contatti eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contatti eliminati definitivamente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i contatti',
                        'body' => 'I contatti non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'company' => 'Azienda',
                    'avatar' => 'Avatar',
                    'tax-id' => 'Codice fiscale',
                    'job-title' => 'Titolo di lavoro',
                    'phone' => 'Telefono',
                    'mobile' => 'Cellulare',
                    'email' => 'Email',
                    'website' => 'Sito web',
                    'title' => 'Titolo',
                    'name' => 'Nome',
                    'short-name' => 'Nome breve',
                    'tags' => 'Etichetta',
                ],
                'address' => [
                    'title' => 'Indirizzo',
                    'fields' => [
                        'street1' => 'Via 1',
                        'street2' => 'Via 2',
                        'city' => 'Città',
                        'zip' => 'CAP',
                        'state' => 'Stato',
                        'country' => 'Paese',
                        'name' => 'Nome',
                        'code' => 'Codice',
                    ],
                ],
            ],
        ],
        'tabs' => [
            'sales-purchase' => [
                'title' => 'Vendite e acquisti',
                'fields' => [
                    'responsible' => 'Responsabile',
                    'responsible-hint-text' => 'Questo è il venditore interno responsabile di questo cliente',
                    'company-id' => 'Identificativo dell\'azienda',
                    'company-id-hint-text' => 'Il numero di registrazione dell\'azienda. Usalo se è diverso dal Codice Fiscale. Deve essere univoco tra tutti i contatti nello stesso Paese',
                    'reference' => 'Riferimento',
                    'industry' => 'Settore',
                ],
            ],
        ],
    ],
];
