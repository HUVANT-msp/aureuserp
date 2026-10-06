<?php

return [
    'navigation' => [
        'title' => 'Distinte base',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'reference' => 'Riferimento',
                    'reference-placeholder' => 'ad es. LdM-001',
                    'product' => 'Prodotto',
                    'product-variant' => 'Variante prodotto',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                    'operation-type' => 'Tipo di operazione',
                    'company' => 'Azienda',
                    'type' => 'Tipo distinta base',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Vari',
                'fields' => [
                    'kit-information' => 'Informazioni sul kit',
                    'kit-information-content' => 'Una distinta base di tipo kit viene utilizzata per raggruppare i componenti in trasferimenti o vendite, anziché essere prodotti utilizzando un ordine di produzione.',
                    'manufacturing-lead-time' => 'Tempi di produzione',
                    'days-to-prepare-manufacturing-order' => 'Giorni per preparare l\'ordine di produzione',
                    'days-suffix' => 'giorni',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componenti',
                'add-action' => 'Aggiungi una riga',
                'columns' => [
                    'component' => 'Componente',
                    'apply-on-variants' => 'Applicare nelle varianti',
                    'consumed-in-operation' => 'Consumato durante il funzionamento',
                    'highlight-consumption' => 'Evidenziare i consumi',
                    'quantity' => 'Quantità',
                    'uom' => 'Unità di misura del prodotto',
                ],
                'validation' => [
                    'component-different-from-product' => 'Il componente deve essere diverso dal prodotto in fase di fabbricazione.',
                ],
                'create-form' => [
                    'fields' => [
                        'name' => 'Nome',
                        'type' => 'Tipo',
                        'category' => 'Categoria',
                        'company' => 'Azienda',
                        'uom' => 'UoM',
                        'uom-placeholder' => 'UoM',
                    ],
                ],
            ],
            'operations' => [
                'title' => 'Operazioni',
                'add-action' => 'Aggiungi una riga',
                'actions' => [
                    'edit' => 'Modifica operazione',
                    'copy-existing' => 'Copia le operazioni esistenti',
                    'copy-existing-fields' => [
                        'operation' => 'Operazione',
                    ],
                ],
                'columns' => [
                    'operation' => 'Operazione',
                    'work-center' => 'Centro di lavoro',
                    'time-mode' => 'Calcolo della durata',
                    'time-mode-batch' => 'Calcolato nell\'ultimo',
                    'company' => 'Azienda',
                    'apply-on-variants' => 'Applicare nelle varianti',
                    'duration' => 'Durata (minuti)',
                ],
            ],
            'by-products' => [
                'title' => 'Sottoprodotti',
                'add-action' => 'Aggiungi una riga',
                'columns' => [
                    'product' => 'Sottoprodotto',
                    'quantity' => 'Quantità',
                    'uom' => 'Unità di misura',
                    'operation' => 'Prodotto in funzione',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Vari',
                'fields' => [
                    'ready-to-produce' => 'Preparazione per la produzione',
                    'routing' => 'Ciclo di lavoro',
                    'consumption' => 'Consumo flessibile',
                    'operation-dependencies' => 'Dipendenze delle operazioni',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'reference' => 'Riferimento',
            'product' => 'Prodotto',
            'quantity' => 'Quantità',
            'uom' => 'UoM',
            'type' => 'Tipo distinta base',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'product' => 'Prodotto',
            'type' => 'Tipo distinta base',
            'company' => 'Azienda',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Distinta base restaurata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Elenco dei materiali archiviati',
                    'body' => 'La distinta base è stata archiviata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Distinta base eliminata',
                        'body' => 'La distinta base è stata eliminata definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare la distinta base',
                        'body' => 'La distinta base non può essere eliminata perché è in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Distinte base ripristinate',
                    'body' => 'Le distinte base selezionate sono state ripristinate con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Distinte base archiviate',
                    'body' => 'Le distinte base selezionate sono state archiviate con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Distinte base eliminate',
                        'body' => 'Le distinte base selezionate sono state eliminate definitivamente.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare le distinte base',
                        'body' => 'Sono in uso una o più distinte base selezionate.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'reference' => 'Riferimento',
                    'product' => 'Prodotto',
                    'product-variant' => 'Variante prodotto',
                    'quantity' => 'Quantità',
                    'uom' => 'UoM',
                    'operation-type' => 'Tipo di operazione',
                    'company' => 'Azienda',
                    'type' => 'Tipo distinta base',
                ],
            ],
            'record-information' => [
                'title' => 'Informazioni record',
                'entries' => [
                    'created-by' => 'Creato da',
                    'created-at' => 'Data creazione',
                    'last-updated' => 'Ultimo aggiornamento',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componenti',
                'entries' => [
                    'component' => 'Componente',
                    'operation' => 'Operazione',
                    'quantity' => 'Quantità',
                    'uom' => 'Unità di misura del prodotto',
                ],
            ],
            'operations' => [
                'title' => 'Operazioni',
                'entries' => [
                    'operation' => 'Operazione',
                    'work-center' => 'Centro di lavoro',
                    'time-mode' => 'Calcolo della durata',
                    'duration' => 'Durata (minuti)',
                ],
            ],
            'by-products' => [
                'title' => 'Sottoprodotti',
                'entries' => [
                    'product' => 'Sottoprodotto',
                    'quantity' => 'Quantità',
                    'uom' => 'Unità di misura',
                    'operation' => 'Prodotto in funzione',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Vari',
                'entries' => [
                    'kit-information' => 'Informazioni sul kit',
                    'kit-information-content' => 'Una distinta base di tipo kit viene utilizzata per raggruppare i componenti in trasferimenti o vendite, anziché essere prodotti utilizzando un ordine di produzione.',
                    'ready-to-produce' => 'Preparazione per la produzione',
                    'routing' => 'Ciclo di lavoro',
                    'consumption' => 'Consumo flessibile',
                    'operation-dependencies' => 'Dipendenze delle operazioni',
                    'manufacturing-lead-time' => 'Tempi di produzione',
                    'days-to-prepare-manufacturing-order' => 'Giorni per preparare l\'ordine di produzione',
                    'days-suffix' => 'giorni',
                ],
            ],
        ],
    ],
];
