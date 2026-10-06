<?php

return [
    'title' => 'Candidato',
    'navigation' => [
        'title' => 'Candidati',
    ],
    'global-search' => [
        'email-from' => 'E-mail del mittente',
        'phone' => 'Telefono',
        'company' => 'Azienda',
        'degree' => 'Qualificazione',
    ],
    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informazioni di base',
                'fields' => [
                    'full-name' => 'Nome completo',
                    'email' => 'Indirizzo e-mail',
                    'phone' => 'Numero di telefono',
                    'linkedin' => 'Profilo LinkedIn',
                    'contact' => 'Contatto',
                ],
            ],
            'additional-details' => [
                'title' => 'Ulteriori dettagli',
                'fields' => [
                    'company' => 'Azienda',
                    'degree' => 'Qualificazione',
                    'tags' => 'Etichetta',
                    'manager' => 'Responsabile',
                    'availability-date' => 'Data di disponibilità',
                    'priority-options' => [
                        'low' => 'Basso',
                        'medium' => 'Medio',
                        'high' => 'Alto',
                    ],
                ],
            ],
            'status-and-evaluation' => [
                'title' => 'Stato',
                'fields' => [
                    'active' => 'Attivo',
                    'evaluation' => 'Valutazione',
                ],
            ],
            'communication' => [
                'title' => 'Comunicazione',
                'fields' => [
                    'cc-email' => 'Posta CC',
                    'email-bounced' => 'Posta respinta',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome completo',
            'tags' => 'Etichetta',
            'evaluation' => 'Valutazione',
        ],
        'filters' => [
            'company' => 'Azienda',
            'partner-name' => 'Contatto',
            'degree' => 'Qualificazione',
            'manager-name' => 'Responsabile',
        ],
        'groups' => [
            'manager-name' => 'Responsabile',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidato creato',
                        'body' => 'Creazione completata con successo.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidati eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informazioni di base',
                'entries' => [
                    'full-name' => 'Nome completo',
                    'email' => 'Indirizzo e-mail',
                    'phone' => 'Numero di telefono',
                    'linkedin' => 'Profilo LinkedIn',
                    'contact' => 'Contatto',
                ],
            ],
            'additional-details' => [
                'title' => 'Ulteriori dettagli',
                'entries' => [
                    'company' => 'Azienda',
                    'degree' => 'Qualificazione',
                    'tags' => 'Etichetta',
                    'manager' => 'Responsabile',
                    'availability-date' => 'Data di disponibilità',
                    'priority-options' => [
                        'low' => 'Basso',
                        'medium' => 'Medio',
                        'high' => 'Alto',
                    ],
                ],
            ],
            'status-and-evaluation' => [
                'title' => 'Stato',
                'entries' => [
                    'active' => 'Attivo',
                    'evaluation' => 'Valutazione',
                ],
            ],
            'communication' => [
                'title' => 'Comunicazione',
                'entries' => [
                    'cc-email' => 'Posta CC',
                    'email-bounced' => 'Posta respinta',
                ],
            ],
        ],
    ],
];
