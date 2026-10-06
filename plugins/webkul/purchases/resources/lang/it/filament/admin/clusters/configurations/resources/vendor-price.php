<?php

return [
    'navigation' => [
        'title' => 'Listini prezzi fornitori',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'fields' => [
                    'vendor' => 'Fornitore',
                    'vendor-product-name' => 'Nome del prodotto del fornitore',
                    'vendor-product-name-tooltip' => 'Il nome del prodotto del fornitore apparirà sulla richiesta di preventivo. Lascia vuoto per utilizzare il nome del prodotto interno.',
                    'vendor-product-code' => 'Codice prodotto del fornitore',
                    'vendor-product-code-tooltip' => 'Il codice prodotto del fornitore apparirà sulla richiesta di preventivo. Lasciare vuoto per utilizzare il codice interno.',
                    'delay' => 'Tempi di consegna (giorni)',
                    'delay-tooltip' => 'Il tempo di consegna (in giorni) dalla conferma dell\'ordine di acquisto al ricevimento del prodotto in magazzino. Viene utilizzato dal pianificatore per la pianificazione automatica degli ordini d\'acquisto.',
                ],
            ],
            'prices' => [
                'title' => 'Prezzi',
                'fields' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'quantity-tooltip' => 'La quantità minima richiesta per acquistare da questo fornitore e qualificarsi per il prezzo specificato. È espresso nell\'unità di misura del prodotto del fornitore o, se non definita, nell\'unità di misura predefinita del prodotto.',
                    'unit-price' => 'Prezzo unitario',
                    'unit-price-tooltip' => 'Il prezzo per unità del prodotto di questo fornitore, espresso nell\'unità di misura del prodotto del fornitore o, se non definita, nell\'unità di misura predefinita del prodotto.',
                    'currency' => 'Valuta',
                    'valid-from' => 'Valido dal',
                    'valid-to' => 'Valido fino al',
                    'discount' => 'Sconto (%)',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'vendor' => 'Fornitore',
            'vendor-product-name' => 'Nome del prodotto del fornitore',
            'vendor-product-code' => 'Codice prodotto del fornitore',
            'delay' => 'Tempi di consegna (giorni)',
            'product' => 'Prodotto',
            'quantity' => 'Quantità',
            'unit-price' => 'Prezzo unitario',
            'currency' => 'Valuta',
            'valid-from' => 'Valido dal',
            'valid-to' => 'Valido fino al',
            'discount' => 'Sconto (%)',
            'company' => 'Azienda',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'filters' => [
            'vendor' => 'Filtra per fornitore',
            'product' => 'Filtra per prodotto',
            'currency' => 'Filtra per valuta',
            'company' => 'Filtra per azienda',
            'price-from' => 'Prezzo minimo',
            'price-to' => 'Prezzo massimo',
            'min-qty-from' => 'Quantità minima da',
            'min-qty-to' => 'Quantità minima fino a',
            'starts-from' => 'Data di validità dal',
            'ends-before' => 'Data di validità fino al',
            'created-from' => 'Creato da',
            'created-until' => 'Creato fino a',
        ],
        'groups' => [
            'vendor' => 'Fornitore',
            'product' => 'Prodotto',
            'created-at' => 'Data creazione',
            'updated-at' => 'Ultima modifica',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prezzo del fornitore rimosso',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare il prezzo del fornitore',
                        'body' => 'Il prezzo del fornitore non può essere eliminato perché è attualmente in uso.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prezzi dei fornitori rimossi',
                        'body' => 'Eliminazione completata con successo.',
                    ],
                    'error' => [
                        'title' => 'Impossibile eliminare i prezzi dei fornitori',
                        'body' => 'I prezzi dei fornitori non possono essere eliminati perché sono attualmente in uso.',
                    ],
                ],
            ],
        ],
    ],
    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Generale',
                'entries' => [
                    'vendor' => 'Fornitore',
                    'vendor-product-name' => 'Nome del prodotto del fornitore',
                    'vendor-product-name-tooltip' => 'Il nome del prodotto del fornitore apparirà sulla richiesta di preventivo. Lascia vuoto per utilizzare il nome del prodotto interno.',
                    'vendor-product-code' => 'Codice prodotto del fornitore',
                    'vendor-product-code-tooltip' => 'Il codice prodotto del fornitore apparirà sulla richiesta di preventivo. Lasciare vuoto per utilizzare il codice interno.',
                    'delay' => 'Tempi di consegna (giorni)',
                    'delay-tooltip' => 'Il tempo di consegna (in giorni) dalla conferma dell\'ordine di acquisto al ricevimento del prodotto in magazzino. Viene utilizzato dal pianificatore per la pianificazione automatica degli ordini d\'acquisto.',
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
            'prices' => [
                'title' => 'Prezzi',
                'entries' => [
                    'product' => 'Prodotto',
                    'quantity' => 'Quantità',
                    'quantity-tooltip' => 'La quantità minima richiesta per acquistare da questo fornitore e qualificarsi per il prezzo specificato. È espresso nell\'unità di misura del prodotto del fornitore o, se non definita, nell\'unità di misura predefinita del prodotto.',
                    'unit-price' => 'Prezzo unitario',
                    'unit-price-tooltip' => 'Il prezzo per unità del prodotto di questo fornitore, espresso nell\'unità di misura del prodotto del fornitore o, se non definita, nell\'unità di misura predefinita del prodotto.',
                    'currency' => 'Valuta',
                    'valid-from' => 'Valido dal',
                    'valid-to' => 'Valido fino al',
                    'discount' => 'Sconto (%)',
                    'company' => 'Azienda',
                ],
            ],
        ],
    ],
];
