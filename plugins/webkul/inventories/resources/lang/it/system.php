<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Non c\'è niente per verificare la disponibilità.',
        ],
        'cancel-move' => [
            'already-done' => 'Non è possibile annullare un movimento azionario contrassegnato come "Fatto". Crea un ritorno per invertire i movimenti avvenuti.',
        ],
        'unreserve-move' => [
            'already-done' => 'Non è possibile annullare la prenotazione di un movimento azionario contrassegnato come "Fatto".',
        ],
        'validate' => [
            'quantity-rounding-mismatch' => 'La quantità realizzata per il prodotto ":product" non rispetta la precisione di arrotondamento definita nell\'unità di misura ":unit". Modifica la quantità realizzata o la precisione di arrotondamento della tua unità di misura.',
            'no-negative-quantities' => 'Non sono ammesse quantità negative',
            'missing-lot-serial-number' => 'È necessario fornire un numero di lotto/serie per il prodotto:
:products',
        ],
        'run-procurement' => [
            'no-rule-found' => 'Nessuna regola trovata per rifornire ":product" su ":location".
Controllare la configurazione del routing sul prodotto.',
            'no-source-location' => 'Non esiste una posizione di origine definita nella regola stock: :name!',
            'no-vendor-price' => 'Non esiste un prezzo del fornitore corrispondente per generare l\'ordine di acquisto per il prodotto :product (nessun fornitore definito, quantità minima non raggiunta, date non valide, ...). Vai alla scheda prodotto e compila l\'elenco dei fornitori.',
        ],
        'return' => [
            'origin' => 'Restituzione di :operation_name',
        ],
    ],
    'move-line' => [
        'negative-quantity-not-allowed' => 'Non è consentito prenotare un importo negativo.',
    ],
    'product-quantity' => [
        'quantity-not-set' => 'La quantità o la quantità prenotata deve essere stabilita.',
        'removal-strategy-not-implemented' => 'La strategia di prelievo :strategy non è implementata.',
        'unreserve-more-than-stock' => 'Non è possibile annullare la prenotazione di più prodotti :name rispetto a quelli disponibili.',
    ],
    'product' => [
        'endless-loop-rule' => 'Configurazione della regola non valida, la seguente regola provoca un loop infinito: :name',
    ],
    'move' => [
        'quantity-rounding-mismatch' => 'La quantità realizzata per il prodotto :product non rispetta la precisione di arrotondamento definita nell\'unità di misura :unit. Modifica la quantità realizzata o la precisione di arrotondamento della tua unità di misura.',
        'split-done-or-cancel' => 'Non è possibile frazionare un movimento azionario che è stato contrassegnato come "Fatto" o "Annullato".',
        'split-draft' => 'Non è possibile dividere una mossa draft. Deve essere confermato prima.',
        'serial-already-assigned' => 'Il numero di serie è già stato assegnato al prodotto: :product, numero di serie: :serial_number',
        'cross-company' => [
            'title' => 'Non sono ammessi trasferimenti tra aziende',
            'body' => 'Un trasferimento non può spostare l\'inventario direttamente tra sedi che appartengono a società diverse (:source e :destination). I trasferimenti tra aziende non sono ancora supportati.',
        ],
    ],
    'rule' => [
        'delay-on' => 'Ritardo tra :name',
        'days' => '+ :days giorno/i',
        'time-horizon' => 'Orizzonte temporale',
    ],
];
