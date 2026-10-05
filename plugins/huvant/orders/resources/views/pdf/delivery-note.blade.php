@php
    use Huvant\Orders\Enums\Fulfilment;

    $company = $order->company;
    $customer = $order->partner;
    $destination = $order->deliveryAddress ?? $customer;
    $address = fn ($record): string => collect([
        $record?->street1,
        $record?->street2,
        trim(($record?->zip ?? '').' '.($record?->city ?? '')),
        $record?->country?->name,
    ])->filter()->implode(', ');
    $quantity = fn ($value): string => rtrim(rtrim(number_format((float) $value, 4, ',', '.'), '0'), ',');
@endphp
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>{{ $delivery->huvant_delivery_note_number }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2933; }
        h1 { font-size: 16px; margin: 0; }
        .label { font-size: 8px; text-transform: uppercase; color: #616e7c; letter-spacing: .06em; }
        .box { border: 1px solid #cbd2d9; padding: 8px 10px; }
        table.row { width: 100%; }
        table.row td { vertical-align: top; }
        table.lines { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.lines th { background: #e4e7eb; font-size: 8px; text-transform: uppercase; text-align: left; padding: 6px; }
        table.lines td { border-bottom: 1px solid #e4e7eb; padding: 6px; vertical-align: top; }
        .num, table.lines th.num { text-align: right; white-space: nowrap; }
        table.transport { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.transport td { border: 1px solid #cbd2d9; padding: 6px; width: 25%; vertical-align: top; }
        .sign { height: 42px; }
    </style>
</head>
<body>
<table class="row">
    <tr>
        <td style="width: 55%">
            <strong style="font-size: 13px">{{ $company?->name }}</strong><br>
            {{ $address($company) }}<br>
            @if ($company?->tax_id)P.IVA {{ $company->tax_id }}@endif
        </td>
        <td style="width: 45%; text-align: right">
            <h1>DOCUMENTO DI TRASPORTO</h1>
            <div class="label">D.P.R. 472/1996</div>
            <div style="margin-top: 8px">
                <span class="label">Numero</span> <strong>{{ $delivery->huvant_delivery_note_number }}</strong><br>
                <span class="label">Data</span> {{ $delivery->huvant_shipped_at?->format('d/m/Y') }}<br>
                <span class="label">Ordine</span> {{ $order->order_number }}
            </div>
        </td>
    </tr>
</table>

<table class="row" style="margin-top: 14px">
    <tr>
        <td style="width: 50%; padding-right: 6px">
            <div class="box">
                <div class="label">Destinatario</div>
                <strong>{{ $customer?->name }}</strong><br>
                {{ $address($customer) }}<br>
                @if ($customer?->tax_id)P.IVA / Tax ID {{ $customer->tax_id }}@endif
            </div>
        </td>
        <td style="width: 50%; padding-left: 6px">
            <div class="box">
                <div class="label">Luogo di destinazione</div>
                <strong>{{ $destination?->name }}</strong><br>
                {{ $address($destination) }}
                @if ($order->deliveryAddress?->huvant_onsite_contact)<br>Referente: {{ $order->deliveryAddress->huvant_onsite_contact }}@endif
                @if ($order->event)<br>Evento: {{ $order->event }}@endif
            </div>
        </td>
    </tr>
</table>

<p style="margin-top: 12px"><span class="label">Causale del trasporto</span> <strong>{{ $order->supply_type->deliveryNoteReason() }}</strong></p>

<table class="lines">
    <thead>
    <tr>
        <th>Codice</th>
        <th>Descrizione</th>
        <th>Lotti</th>
        <th>HS code</th>
        <th class="num">Quantità</th>
        <th>U.M.</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($delivery->moves->groupBy('product_id') as $productId => $moves)
        @php $product = $moves->first()->product; @endphp
        <tr>
            <td>{{ $product?->reference }}</td>
            <td>{{ $product?->name }}</td>
            <td>{{ implode(', ', $lots[$productId] ?? []) }}</td>
            <td>{{ $product?->huvant_hs_code }}</td>
            <td class="num">{{ $quantity($moves->sum('quantity')) }}</td>
            <td>{{ $product?->uom?->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="transport">
    <tr>
        <td><div class="label">Trasporto a cura del</div>{{ $order->fulfilment === Fulfilment::Courier ? 'Vettore' : 'Mittente' }}</td>
        <td><div class="label">Vettore</div>
            @if ($order->fulfilment === Fulfilment::Courier){{ $delivery->huvant_carrier }}@else{{ $order->handDeliveryUser?->name }}@endif
            @if ($delivery->huvant_tracking_number)<br>Tracking {{ $delivery->huvant_tracking_number }}@endif
        </td>
        <td><div class="label">Colli</div>{{ $delivery->huvant_packages }}</td>
        <td><div class="label">Peso (kg)</div>{{ $delivery->huvant_weight_kg ? $quantity($delivery->huvant_weight_kg) : '' }}</td>
    </tr>
    <tr>
        <td colspan="2"><div class="label">Firma del conducente</div><div class="sign"></div></td>
        <td colspan="2"><div class="label">Firma del destinatario</div><div class="sign"></div></td>
    </tr>
</table>
</body>
</html>
