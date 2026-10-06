<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title' => 'Titolo',
                'name' => 'Nome',
                'type' => 'Tipo',
                'create-type' => 'Crea tipo',
                'duration' => 'Durata',
                'start-date' => 'Data inizio',
                'end-date' => 'Data fine',
                'display-type' => 'Tipo di visualizzazione',
                'description' => 'Descrizione',
                'attachments' => 'Allegati',
                'file' => 'File',
                'file-helper-text' => 'Formati accettati: PDF, DOC, DOCX, TXT, PNG, JPEG e WEBP. Massimo 10 MB per file.',
                'attachment-name' => 'Etichetta',
                'add-attachment' => 'Aggiungi allegato',
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'title' => 'Titolo',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'display-type' => 'Tipo di visualizzazione',
            'description' => 'Descrizione',
            'created-by' => 'Creato da',
            'attachments' => 'Allegati',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'group-by-type' => 'Raggruppa per tipo',
            'group-by-display-type' => 'Raggruppa per tipo di visualizzazione',
        ],
        'header-actions' => [
            'add-resume' => 'Aggiungi curriculum',
        ],
        'filters' => [
            'type' => 'Tipo',
            'start-date-from' => 'Data di inizio da',
            'start-date-to' => 'Data di inizio fino al',
            'created-from' => 'Creato da',
            'created-to' => 'Creato fino a',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Curriculum aggiornato',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'create' => [
                'notification' => [
                    'title' => 'Curriculum vitae creato',
                    'body' => 'Creazione completata con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Curriculum cancellato',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Curriculum cancellati',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
    'infolist' => [
        'entries' => [
            'title' => 'Titolo',
            'display-type' => 'Tipo di visualizzazione',
            'type' => 'Tipo',
            'description' => 'Descrizione',
            'duration' => 'Durata',
            'start-date' => 'Data inizio',
            'end-date' => 'Data fine',
            'attachments' => 'Allegati',
            'file' => 'File',
            'attachment-name' => 'Etichetta',
        ],
    ],
];
