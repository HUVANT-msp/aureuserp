<?php

return [
    'navigation' => [
        'title' => 'Post del blog',
    ],
    'global-search' => [
        'author' => 'Autore',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'title' => 'Titolo',
                    'sub-title' => 'Sottotitolo',
                    'title-placeholder' => 'Titolo del post...',
                    'slug' => 'Lumaca',
                    'content' => 'Contenuto',
                    'banner' => 'Stendardo',
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
                    'category' => 'Categoria',
                    'tags' => 'Etichetta',
                    'name' => 'Nome',
                    'color' => 'Colore',
                    'is-published' => 'È pubblicato',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'title' => 'Titolo',
            'slug' => 'Lumaca',
            'author' => 'Autore',
            'category' => 'Categoria',
            'creator' => 'Creato da',
            'is-published' => 'È pubblicato',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'category' => 'Categoria',
            'author' => 'Autore',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'is-published' => 'È pubblicato',
            'author' => 'Autore',
            'creator' => 'Creato da',
            'category' => 'Categoria',
            'tags' => 'Etichetta',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Post aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Pubblicazione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Messaggio eliminato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Post eliminato definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pubblicazioni restaurate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Post cancellati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Post eliminati definitivamente',
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
                    'category' => 'Categoria',
                    'tags' => 'Etichetta',
                    'name' => 'Nome',
                    'color' => 'Colore',
                    'is-published' => 'È pubblicato',
                ],
            ],
        ],
    ],
];
