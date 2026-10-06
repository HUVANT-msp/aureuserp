<?php

return [
    'navigation' => [
        'title' => 'Tipi di operazione',
        'group' => 'Gestione del magazzino',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type' => 'Tipo di operazione',
                    'operator-type-placeholder' => 'pag. per esempio. Ricevimenti',
                ],
            ],
            'applicable-on' => [
                'title' => 'Applicabile a',
                'description' => 'Seleziona le località in cui è possibile applicare questo percorso.',
                'fields' => [

                ],
            ],
        ],
        'tabs' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'operator-type' => 'Tipo di operazione',
                    'sequence-prefix' => 'Prefisso di sequenza',
                    'generate-shipping-labels' => 'Genera etichette di spedizione',
                    'warehouse' => 'Magazzino',
                    'show-reception-report' => 'Mostra il rapporto di ricezione durante la convalida',
                    'show-reception-report-hint-tooltip' => 'Se selezionato, il sistema mostrerà automaticamente il report di ricevimento al momento della validazione, ogni volta che ci saranno movimenti da assegnare.',
                    'company' => 'Azienda',
                    'return-type' => 'Tipo di reso',
                    'create-backorder' => 'Crea ordine residuo',
                    'move-type' => 'Tipo di movimento',
                    'move-type-hint-tooltip' => 'A meno che non sia definita dal documento di origine, questa servirà come politica di raccolta predefinita per questo tipo di operazione.',
                ],
                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lotti/numeri di serie',
                        'fields' => [
                            'create-new' => 'Crea nuovo',
                            'create-new-hint-tooltip' => 'Se selezionato, il sistema presumerà che tu voglia creare nuovi lotti/matricole, permettendoti di inserirli in un campo di testo.',
                            'use-existing' => 'Usa esistente',
                            'use-existing-hint-tooltip' => 'Se selezionato è possibile scegliere lotti/numeri di serie oppure scegliere di non assegnarne alcuno. Ciò consente di creare stock senza lotto o senza restrizioni sul lotto utilizzato.',
                        ],
                    ],
                    'locations' => [
                        'title' => 'Ubicazioni',
                        'fields' => [
                            'source-location' => 'Posizione di origine',
                            'source-location-hint-tooltip' => 'Questa è la posizione di origine predefinita quando si crea manualmente questa operazione. Tuttavia, può essere modificato in seguito e i percorsi possono assegnare una posizione predefinita diversa.',
                            'destination-location' => 'Località di destinazione',
                            'destination-location-hint-tooltip' => 'Questa è la posizione di destinazione predefinita per le operazioni create manualmente. Tuttavia, può essere modificato in seguito e i percorsi possono assegnare una posizione predefinita diversa.',
                        ],
                    ],
                    'packages' => [
                        'title' => 'Colli',
                        'fields' => [
                            'show-entire-package' => 'Spostare l\'intero pacco',
                            'show-entire-package-hint-tooltip' => 'Se selezionato, puoi spostare interi pacchi.',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',
                'fieldsets' => [
                    'print-on-validation' => [
                        'title' => 'Stampa dopo la convalida',
                        'fields' => [
                            'delivery-slip' => 'Prova di consegna',
                            'delivery-slip-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente la ricevuta di consegna una volta convalidato il ritiro.',
                            'return-slip' => 'Ricevuta di ritorno',
                            'return-slip-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente la ricevuta di ritorno al momento della convalida del ritiro.',
                            'product-labels' => 'Etichette dei prodotti',
                            'product-labels-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente le etichette dei prodotti al momento della convalida del ritiro.',
                            'lots-labels' => 'Etichette lotto/NS',
                            'lots-labels-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente le etichette del numero di lotto/serie una volta convalidato il ritiro.',
                            'reception-report' => 'Rapporto di accoglienza',
                            'reception-report-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente il report di ricevuta quando l\'incasso sarà validato e conterrà movimenti assegnati.',
                            'reception-report-labels' => 'Ricezione delle etichette dei report',
                            'reception-report-labels-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente le etichette del report di ricezione una volta convalidato il ritiro.',
                            'package-content' => 'Contenuto della confezione',
                            'package-content-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente i dettagli del pacco e il suo contenuto al momento della convalida del ritiro.',
                        ],
                    ],
                    'print-on-pack' => [
                        'title' => 'Stampa durante l\'"Imballaggio"',
                        'fields' => [
                            'package-label' => 'Etichetta del pacchetto',
                            'package-label-hint-tooltip' => 'Se selezionato, il sistema stamperà automaticamente l\'etichetta del pacco quando si utilizza il pulsante "Pacchetto".',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'name' => 'Nome',
            'warehouse' => 'Magazzino',
            'company' => 'Azienda',
            'deleted-at' => 'Eliminato il',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'groups' => [
            'type' => 'Tipo',
            'warehouse' => 'Magazzino',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'type' => 'Tipo',
            'warehouse' => 'Magazzino',
            'company' => 'Azienda',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo di operazione ripristinata',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipo di operazione rimosso',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo di operazione rimosso in modo permanente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il tipo di operazione',
                        'body' => 'Impossibile eliminare il tipo di operazione perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipi di operazioni ripristinate',
                    'body' => 'Ripristino completato con successo.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Tipi di operazione rimossi',
                    'body' => 'Eliminazione completata con successo.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipi di operazioni rimossi in modo permanente',
                        'body' => 'Eliminazione definitiva completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i tipi di operazione',
                        'body' => 'I tipi di operazione non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
        'empty-actions' => [
            'create' => [
                'label' => 'Crea tipo di operazione',
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informazioni generali',
                'entries' => [
                    'name' => 'Nome',
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
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'type' => 'Tipo di operazione',
                    'sequence_code' => 'Codice di sequenza',
                    'print_label' => 'Stampa etichetta',
                    'warehouse' => 'Magazzino',
                    'reservation_method' => 'Metodo di prenotazione',
                    'auto_show_reception_report' => 'Mostra automaticamente il rapporto di ricevuta',
                    'company' => 'Azienda',
                    'return_operation_type' => 'Tipo di operazione di reso',
                    'create_backorder' => 'Crea ordine residuo',
                    'move_type' => 'Tipo di movimento',
                ],
                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lotti',
                        'entries' => [
                            'use_create_lots' => 'Crea batch',
                            'use_existing_lots' => 'Utilizza batch esistenti',
                        ],
                    ],
                    'locations' => [
                        'title' => 'Ubicazioni',
                        'entries' => [
                            'source_location' => 'Posizione di origine',
                            'destination_location' => 'Località di destinazione',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',
                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Stampa dopo la convalida',
                        'entries' => [
                            'auto_print_delivery_slip' => 'Stampa automaticamente la prova di consegna',
                            'auto_print_return_slip' => 'Stampa automaticamente la ricevuta di ritorno',
                            'auto_print_product_labels' => 'Stampa automaticamente le etichette dei prodotti',
                            'auto_print_lot_labels' => 'Stampa automaticamente le etichette batch',
                            'auto_print_reception_report' => 'Stampa automaticamente il rapporto di ricezione',
                            'auto_print_reception_report_labels' => 'Stampa automaticamente le etichette del rapporto sulle ricevute',
                            'auto_print_packages' => 'Stampa i pacchetti automaticamente',
                        ],
                    ],
                    'print_on_pack' => [
                        'title' => 'Stampa durante l\'imballaggio',
                        'entries' => [
                            'auto_print_package_label' => 'Stampa automaticamente l\'etichetta del pacco',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
