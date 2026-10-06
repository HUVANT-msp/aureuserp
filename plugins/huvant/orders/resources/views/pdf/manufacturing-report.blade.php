<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('huvant-orders::manufacturing.manufacturing_report') }} · {{ $order->order_number }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #111827; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.4; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        .meta { margin: 0 0 18px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 7px 6px; background: #f3f4f6; color: #374151; font-size: 8px; text-align: left; text-transform: uppercase; }
        td { padding: 8px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .code { font-family: DejaVu Sans Mono, monospace; font-weight: bold; }
        .materials { margin: 5px 0 0; padding: 0; list-style: none; color: #374151; }
        .materials li { margin: 2px 0; }
        .status { font-weight: bold; }
        footer { margin-top: 18px; color: #6b7280; font-size: 8px; }
    </style>
</head>
<body>
    <h1>{{ __('huvant-orders::manufacturing.manufacturing_report') }}</h1>
    <p class="meta">
        {{ $order->order_number }} · {{ $order->partner?->name ?? __('huvant-orders::manufacturing.stock_order') }}<br>
        {{ __('huvant-orders::manufacturing.managed_by', ['name' => $order->manufacturingManagedBy?->name ?? '—']) }} ·
        {{ $order->manufacturing_managed_at?->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('huvant-orders::manufacturing.product') }}</th>
                <th>{{ __('huvant-orders::manufacturing.source') }}</th>
                <th>{{ __('huvant-orders::manufacturing.serial') }}</th>
                <th>{{ __('huvant-orders::manufacturing.production_and_expiry') }}</th>
                <th>{{ __('huvant-orders::manufacturing.raw_material_lots') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry->position }}</td>
                    <td><strong>{{ $entry->product->name }}</strong><br>{{ $entry->product->reference }}</td>
                    <td class="status">{{ $entry->source?->getLabel() ?? '—' }}</td>
                    <td class="code">{{ $entry->productUnit?->code ?? '—' }}</td>
                    <td>
                        {{ __('huvant-orders::manufacturing.produced_short', ['date' => $entry->productUnit?->production_date?->format('d/m/Y') ?? '—']) }}<br>
                        {{ __('huvant-orders::manufacturing.expires_short', ['date' => $entry->productUnit?->expiry_date?->format('d/m/Y') ?? '—']) }}<br>
                        {{ __('huvant-orders::manufacturing.made_by_short', ['name' => $entry->productUnit?->creator?->name ?? '—']) }}
                    </td>
                    <td>
                        @if ($entry->productUnit?->materials->isNotEmpty())
                            <ul class="materials">
                                @foreach ($entry->productUnit->materials as $used)
                                    <li><strong>{{ $used->material?->name }}</strong> · {{ $used->lot_number }} · {{ \Huvant\Orders\Support\LabInventory::format((float) $used->quantity, $used->material?->huvant_package_unit) }}</li>
                                @endforeach
                            </ul>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>{{ __('huvant-orders::manufacturing.report_generated_at', ['date' => now()->format('d/m/Y H:i')]) }}</footer>
</body>
</html>
