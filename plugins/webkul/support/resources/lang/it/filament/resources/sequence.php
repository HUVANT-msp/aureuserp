<?php

return [
    'model-label' => 'Sequenza',
    'plural-model-label' => 'Sequenze',
    'navigation' => [
        'title' => 'Sequenze',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'description' => 'Le sequenze di giornali di giornale, magazzini e tipi di transazione vengono create automaticamente quando crei tali record; modificali qui. La creazione manuale è necessaria solo per sequenze basate su codice personalizzato.',
                'fields' => [
                    'name' => 'Nome',
                    'code' => 'Codice',
                    'code-help' => 'Identificatore tecnico utilizzato dai documenti, ad es. per esempio. ordine.di.vendita. Le sequenze create automaticamente hanno già il codice corretto.',
                    'company' => 'Azienda',
                ],
            ],
            'format' => [
                'title' => 'Numerazione',
                'fields' => [
                    'prefix' => 'Prefisso',
                    'prefix-help' => 'Indicatori: %(anno), %(y), %(mese), %(giorno). Esempio: INV/%(anno)/',
                    'suffix' => 'Suffisso',
                    'padding' => 'Riempimento numerico',
                    'next-number' => 'Prossimo numero',
                    'next-number-help' => 'Può solo essere aumentato. Per riavviare la numerazione in sicurezza, eliminare la sequenza; verrà ricreato dal numero di documento esistente più alto.',
                    'step' => 'Passo',
                    'reset-frequency' => 'Reimposta contatore',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Codice',
            'applies-to' => 'Si applica a',
            'company' => 'Azienda',
            'next-preview' => 'Numero del documento successivo',
            'next-number' => 'Prossimo numero',
            'reset-frequency' => 'Reimposta contatore',
        ],
        'variants' => [
            'refund' => 'Rimborso',
            'payment' => 'Pagamento',
            'refund-payment' => 'Rimborso + Pagamento',
        ],
        'filters' => [
            'company' => 'Azienda',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sequenza aggiornata',
                    'body' => 'Aggiornamento completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Sequenza eliminata',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sequenze cancellate',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
        ],
    ],
];
