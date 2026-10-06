<?php

return [
    'title' => 'Candidato',
    'navigation' => [
        'title' => 'Candidati',
    ],
    'global-search' => [
        'department' => 'Reparto',
        'work-email' => 'E-mail di lavoro',
        'work-phone' => 'Telefono del lavoro',
    ],
    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'fields' => [
                    'evaluation-good' => 'Valutazione: Buona',
                    'evaluation-very-good' => 'Valutazione: Molto buona',
                    'evaluation-very-excellent' => 'Valutazione: Eccellente',
                    'hired' => 'Assunto',
                    'candidate-name' => 'Nome del candidato',
                    'email' => 'Ufficio postale',
                    'phone' => 'Telefono',
                    'linkedin-profile' => 'Profilo LinkedIn',
                    'recruiter' => 'Reclutatore',
                    'interviewer' => 'Intervistatore',
                    'tags' => 'Etichetta',
                    'notes' => 'Nota',
                    'hired-date' => 'Data di assunzione',
                    'job-position' => 'Ruoli aziendali',
                ],
            ],
            'education-and-availability' => [
                'title' => 'Formazione e disponibilità',
                'fields' => [
                    'degree' => 'Qualificazione',
                    'availability-date' => 'Data di disponibilità',
                ],
            ],
            'department' => [
                'title' => 'Reparto',
            ],
            'salary' => [
                'title' => 'Stipendio previsto e proposto',
                'fields' => [
                    'expected-salary' => 'Stipendio previsto',
                    'salary-proposed-extra' => 'Un altro vantaggio',
                    'proposed-salary' => 'Stipendio proposto',
                    'salary-expected-extra' => 'Un altro vantaggio',
                ],
            ],
            'source-and-medium' => [
                'title' => 'Carattere e mezzo',
                'fields' => [
                    'source' => 'Fonte',
                    'medium' => 'Medio',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'partner-name' => 'Nome del contatto',
            'applied-on' => 'Data della domanda',
            'job-position' => 'Ruolo aziendale',
            'stage' => 'Fase',
            'candidate-name' => 'Nome del candidato',
            'evaluation' => 'Valutazione',
            'application-status' => 'Stato della candidatura',
            'tags' => 'Etichetta',
            'refuse-reason' => 'Motivo del rifiuto',
            'email' => 'Email',
            'recruiter' => 'Reclutatore',
            'interviewer' => 'Intervistatore',
            'candidate-phone' => 'Telefono',
            'medium' => 'Medio',
            'source' => 'Fonte',
            'salary-expected' => 'Stipendio previsto',
            'availability-date' => 'Data di disponibilità',
        ],
        'filters' => [
            'source' => 'Fonte',
            'medium' => 'Medio',
            'candidate' => 'Candidato',
            'priority' => 'Priorità',
            'salary-proposed-extra' => 'Stipendio extra proposto',
            'salary-expected-extra' => 'Stipendio extra previsto',
            'applicant-notes' => 'Note del candidato',
            'create-date' => 'Data della domanda',
            'date-closed' => 'Data di assunzione',
            'date-last-stage-updated' => 'Aggiornamento dell\'ultima fase',
            'stage' => 'Fase',
            'job-position' => 'Ruolo aziendale',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato eliminato',
                    'body' => 'Il candidato è stato rimosso con successo.',
                ],
            ],
        ],
        'groups' => [
            'stage' => 'Fase',
            'job-position' => 'Ruolo aziendale',
            'candidate-name' => 'Nome del candidato',
            'responsible' => 'Responsabile',
            'creation-date' => 'Data di creazione',
            'hired-date' => 'Data di assunzione',
            'last-stage' => 'Ultima fase',
            'refuse-reason' => 'Motivo del rifiuto',
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Collaboratori eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Collaboratori eliminati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Dipendenti ripristinati',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'evaluation-good' => 'Valutazione: Buona',
                    'evaluation-very-good' => 'Valutazione: Molto buona',
                    'evaluation-very-excellent' => 'Valutazione: Eccellente',
                    'hired' => 'Assunto',
                    'candidate-name' => 'Nome del candidato',
                    'email' => 'Ufficio postale',
                    'phone' => 'Telefono',
                    'linkedin-profile' => 'Profilo LinkedIn',
                    'recruiter' => 'Reclutatore',
                    'interviewer' => 'Intervistatore',
                    'tags' => 'Etichetta',
                    'notes' => 'Nota',
                    'job-position' => 'Ruoli aziendali',
                ],
            ],
            'education-and-availability' => [
                'title' => 'Formazione e disponibilità',
                'entries' => [
                    'degree' => 'Qualificazione',
                    'availability-date' => 'Data di disponibilità',
                ],
            ],
            'department' => [
                'title' => 'Reparto',
            ],
            'salary' => [
                'title' => 'Stipendio previsto e proposto',
                'entries' => [
                    'expected-salary' => 'Stipendio previsto',
                    'salary-proposed-extra' => 'Un altro vantaggio',
                    'proposed-salary' => 'Stipendio proposto',
                    'salary-expected-extra' => 'Un altro vantaggio',
                ],
            ],
            'source-and-medium' => [
                'title' => 'Carattere e mezzo',
                'entries' => [
                    'source' => 'Fonte',
                    'medium' => 'Medio',
                ],
            ],
        ],
    ],
];
