<?php

return [
    'navigation' => [
        'title' => 'Categorie',
        'group' => 'Blog',
    ],
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Titolo della categoria...',
            'sub-title' => 'Sottotitolo',
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'sub-title' => 'Sottotitolo',
            'posts' => 'Pubblicazioni',
            'created-at' => 'Data creazione',
        ],
        'filters' => [
            'is-published' => 'È pubblicato',
            'author' => 'Autore',
            'creator' => 'Creato da',
            'category' => 'Categoria',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Categoria ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Categoria rimossa',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoria definitivamente cancellata',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la categoria',
                        'body' => 'La categoria non può essere eliminata perché è attualmente in uso.',
                    ],
                ],
            ],
            'force-delete-error' => [
                'notification' => [
                    'title' => 'Impossibile eliminare la categoria',
                    'body' => 'Non puoi eliminare questa categoria perché è associata ad alcuni post.',
                ],
                'exception' => 'Non puoi eliminare definitivamente questa categoria perché è associata ad alcuni post.',
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categorie ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Categorie rimosse',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'title' => 'Categorie rimosse definitivamente',
                    'body' => 'Eliminazione definitiva completata con successo.',
                ],
            ],
            'force-delete-error' => [
                'notification' => [
                    'title' => 'Impossibile eliminare la categoria',
                    'body' => 'Non puoi eliminare questa categoria perché è associata ad alcuni post.',
                ],
            ],
        ],
    ],
    'infolist' => [

    ],
];
