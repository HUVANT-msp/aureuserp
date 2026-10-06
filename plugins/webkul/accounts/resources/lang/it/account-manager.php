<?php

return [
    'post-action-validate' => [
        'customer-required' => 'Fornire un cliente valido per continuare la convalida della fattura cliente.',
        'vendor-required' => 'Fornire un fornitore valido per continuare la convalida della fattura fornitore.',
        'bank-archived' => 'Il conto bancario del contatto associato a questa fattura è in archivio.',
        'negative-amount' => 'La fattura non può essere confermata con un importo totale negativo.',
        'date-required' => 'Si prega di fornire una data di fattura/rimborso valida per continuare la convalida della fattura/rimborso.',
        'currency-archived' => 'Non è possibile confermare una fattura con una valuta registrata.',
        'account-deprecated' => 'Una o più righe di questa fattura utilizzano conti obsoleti.',
        'lines-required' => 'Aggiungi almeno una riga alla fattura.',
        'draft-state-required' => 'Possono essere confermate solo le fatture in stato di bozza.',
        'journal-archived' => 'Non è possibile confermare una fattura con un giornale archiviato.',
    ],
    'documents' => [
        'titles' => [
            'invoice' => 'ID fattura n. :name',
            'bill' => 'ID fattura fornitore n. :name',
            'refund' => 'ID rimborso n.:name',
            'credit-note' => 'ID nota di credito n. :name',
        ],
        'labels' => [
            'invoice-date' => 'Data della fattura',
            'bill-date' => 'Data',
            'refund-date' => 'Data del rimborso',
            'credit-note-date' => 'Data della nota di credito',
            'source' => 'Fonte',
            'due-date' => 'Data di scadenza',
            'product' => 'Prodotto',
            'quantity' => 'Quantità',
            'unit' => 'Unità',
            'unit-price' => 'Prezzo unitario',
            'subtotal' => 'Subtotale',
            'tax' => 'Imposta',
            'discount' => 'Sconto',
            'grand-total' => 'Totale generale',
            'payment-information' => 'Informazioni sul pagamento',
            'payment-communication' => 'Comunicazione di pagamento',
            'account-details' => 'in questi dettagli dell\'account:',
        ],
    ],
];
