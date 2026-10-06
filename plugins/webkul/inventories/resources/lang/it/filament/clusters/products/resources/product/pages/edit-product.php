<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Errore durante l\'aggiornamento del monitoraggio',
                    'body' => 'Non è possibile modificare il monitoraggio dell\'inventario di un prodotto che è già stato utilizzato.',
                ],
                'reordering-rules' => [
                    'title' => 'Errore durante l\'aggiornamento del prodotto',
                    'body' => 'Hai ancora alcune regole di rifornimento attive su questo prodotto. Archiviali o eliminali prima.',
                ],
                'reserved' => [
                    'title' => 'Errore durante l\'aggiornamento del monitoraggio',
                    'body' => 'Non è possibile modificare il monitoraggio dell\'inventario di un prodotto attualmente prenotato in un movimento di stock. Se è necessario modificare il monitoraggio dell\'inventario, è necessario prima annullare la prenotazione del movimento delle scorte.',
                ],
                'qty-not-zero' => [
                    'title' => 'Errore durante l\'aggiornamento del monitoraggio',
                    'body' => 'La quantità disponibile deve essere impostata su zero prima di modificare il monitoraggio dell\'inventario.',
                ],
                'track-by-update' => [
                    'title' => 'Errore durante l\'aggiornamento del monitoraggio',
                    'body' => 'Ci sono prodotti in stock che non hanno un numero di lotto/serie. I numeri di lotto/serie possono essere assegnati eseguendo una rettifica dell\'inventario.',
                ],
            ],
        ],
    ],
];
