<?php

return [
    'navigation' => [
        'title' => 'Pagine',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'title' => 'Titolo',
                    'title-placeholder' => 'Titolo della pagina...',
                    'slug' => 'Lumaca',
                    'content' => 'Contenuto',
                ],
            ],
            'seo' => [
                'title' => 'SEO',
                'fields' => [
                    'meta-title' => 'Meta titolo',
                    'meta-keywords' => 'Meta parole chiave',
                    'meta-description' => 'Meta descrizione',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'fields' => [
                    'is-header-visible' => 'Mostra il menu dell\'intestazione',
                    'is-footer-visible' => 'Mostra il menu a piè di pagina',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'title' => 'Titolo',
            'slug' => 'Lumaca',
            'creator' => 'Creato da',
            'is-published' => 'È pubblicato',
            'is-header-visible' => 'Mostra il menu dell\'intestazione',
            'is-footer-visible' => 'Mostra il menu a piè di pagina',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'is-published' => 'È pubblicato',
            'creator' => 'Creato da',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Pagina aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Pagina restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Pagina eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Pagina eliminata definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pagine restaurate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Pagine cancellate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Pagine cancellate definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'title' => 'Titolo',
                    'slug' => 'Lumaca',
                    'content' => 'Contenuto',
                    'banner' => 'Stendardo',
                ],
            ],
            'seo' => [
                'title' => 'SEO',
                'entries' => [
                    'meta-title' => 'Meta titolo',
                    'meta-keywords' => 'Meta parole chiave',
                    'meta-description' => 'Meta descrizione',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'author' => 'Autore',
                    'created-by' => 'Creato da',
                    'published-at' => 'Pubblicato il',
                    'last-updated-by' => 'Ultimo aggiornamento di',
                    'last-updated' => 'Ultimo aggiornamento su',
                    'created-at' => 'Data creazione',
                ],
            ],
            'settings' => [
                'title' => 'Impostazioni',
                'entries' => [
                    'is-header-visible' => 'Mostra il menu dell\'intestazione',
                    'is-footer-visible' => 'Mostra il menu a piè di pagina',
                ],
            ],
        ],
    ],
];
