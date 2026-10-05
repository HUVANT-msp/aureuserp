<?php

namespace Huvant\Orders\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Support\Shipping;
use Symfony\Component\HttpFoundation\Response;

class DeliveryNotePdfController
{
    /** The Italian delivery note (DDT) of a validated delivery. */
    public function show(Delivery $delivery): Response
    {
        abort_unless(auth()->check(), 403);
        abort_unless($delivery->huvant_order_id && $delivery->huvant_delivery_note_number, 404);

        $delivery->load(['huvantOrder.partner.country', 'huvantOrder.deliveryAddress.country', 'huvantOrder.handDeliveryUser', 'huvantOrder.company.country', 'moves.product.uom']);

        $pdf = Pdf::loadView('huvant-orders::pdf.delivery-note', [
            'delivery' => $delivery,
            'order'    => $delivery->huvantOrder,
            'lots'     => Shipping::lotsByProduct($delivery),
        ])->setPaper('a4');

        return $pdf->stream($delivery->huvant_delivery_note_number.'.pdf');
    }
}
