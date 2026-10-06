<?php

namespace Huvant\Orders\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\ManufacturingFlow;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ManufacturingReportPdfController
{
    public function show(Order $order): Response
    {
        abort_unless(auth()->check(), 403);
        abort_unless($order->manufacturing_managed_at, 404);

        $order->load(['partner', 'manufacturingManagedBy']);
        $entries = ManufacturingFlow::entries($order);
        $pdf = Pdf::loadView('huvant-orders::pdf.manufacturing-report', compact('order', 'entries'))->setPaper('a4');
        $fileName = Str::of(__('huvant-orders::manufacturing.report_filename', ['order' => $order->order_number]))
            ->replaceMatches('~[\\\\/:*?"<>|]+~', ' ')
            ->squish();

        return $pdf->download($fileName.'.pdf');
    }
}
