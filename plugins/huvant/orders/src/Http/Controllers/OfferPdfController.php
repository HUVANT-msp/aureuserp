<?php

namespace Huvant\Orders\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class OfferPdfController
{
    /** The commercial offer in English, as the order register printed it, now with prices. */
    public function show(Order $order): Response
    {
        abort_unless(auth()->check(), 403);
        abort_unless(Orders::canSeePrices(), 403);

        $order->load(['partner.country', 'contact', 'deliveryAddress.country', 'lines.product', 'company.partner.bankAccounts.bank', 'company.country']);

        $pdf = Pdf::loadView('huvant-orders::pdf.commercial-offer', ['order' => $order])->setPaper('a4');

        $fileName = Str::of(sprintf('%s - %s - %s', $order->name, $order->offer_date->format('d-m-Y'), $order->partner?->name ?? 'Stock'))
            ->replaceMatches('/[\\\\\/:*?"<>|]+/', ' ')
            ->squish();

        return $pdf->stream($fileName.'.pdf');
    }
}
