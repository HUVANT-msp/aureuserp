@php
    // Laid out like the "Fattura Proforma" sheet of the order register (V.15.7.1): letterhead, logo, blue line table.
    $letterhead = [
        'Headquarter. Via S.Lattuada 16 - 20135 Milan',
        'H Operational. Viale Ortles 22/4 - 20139 Milan',
        'g.melli@huvant.com',
        'Pec: huvant@postecert.it',
        'CF/P.IVA: 12849880963',
        'Cod. Destinatario T9K4ZHO',
        'UNICREDIT SPA - Milano S.GOTTARDO',
        'Iban  IT69U0200801604000106708536',
    ];
    $customer = $order->partner;
    $shipTo = $order->deliveryAddress;
    $money = fn (float $amount): string => number_format($amount, 2, '.', ',');
    $number = fn (float $value, int $decimals): string => rtrim(rtrim(number_format($value, $decimals, '.', ','), '0'), '.');
    $address = fn ($record): string => collect([
        $record?->street1,
        $record?->street2,
        trim(($record?->zip ?? '').' '.($record?->city ?? '')),
        $record?->country?->name,
    ])->filter()->implode(', ');
    $hasDiscount = $order->lines->contains(fn ($line): bool => (float) $line->discount > 0);
    $blankRows = max(0, 12 - $order->lines->count());
    $columns = $hasDiscount ? 6 : 5;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Commercial Offer {{ $order->name }}</title>
    <style>
        @page { margin: 34px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #001b36; line-height: 1.35; }
        table { border-collapse: collapse; }
        .head { width: 100%; border-bottom: 2.5px solid #001b36; }
        .head td { vertical-align: top; padding: 0 0 6px; }
        .title { font-size: 19px; color: #00b0f0; margin: 8px 0 2px 50%; }
        .parties { width: 100%; }
        .parties td { vertical-align: top; }
        .label { font-weight: bold; }
        .to-label { font-weight: bold; background: #f8f8f8; padding: 1px 3px; margin-bottom: 2px; }
        .meta td { padding: 0 14px 0 0; font-weight: bold; }
        .contact-mail { color: #2e5c8a; text-decoration: underline; }
        .contact-phone { color: #615a22; }
        table.lines { width: 100%; margin-top: 14px; }
        table.lines th { background: #003366; color: #fff; font-weight: bold; text-align: center; padding: 3px 4px; }
        table.lines td { border-top: 1px solid #00b0f0; border-bottom: 1px solid #00b0f0; padding: 2px 4px; height: 13px; }
        table.lines td.input { background: #ffffcc; text-align: center; }
        table.lines td.amount { background: #f8f8f8; text-align: right; font-weight: bold; white-space: nowrap; }
        table.lines tr.last td { border-bottom: none; }
        .muted { color: #52606d; font-size: 8px; }
        table.totals { width: 100%; }
        table.totals td { padding: 1px 4px; }
        table.totals td.caption { text-align: right; }
        table.totals td.value { background: #f8f8f8; text-align: right; white-space: nowrap; }
        table.totals tr.total td { font-weight: bold; }
        .legal { font-size: 7px; color: #52606d; margin-top: 22px; }
    </style>
</head>
<body>
<table class="head">
    <tr>
        <td style="width: 45%">
            <strong>HUVANT SRL</strong><br>
            @foreach ($letterhead as $line){{ $line }}<br>@endforeach
        </td>
        <td style="width: 55%; text-align: right">
            <img src="{{ $logo }}" alt="Huvant" style="height: 98px">
        </td>
    </tr>
</table>

<div class="title">Commercial Offer</div>

<table class="parties">
    <tr>
        <td style="width: 50%; padding-right: 14px">
            <div class="to-label">TO</div>
            {{ $customer?->name ?? '—' }}<br>
            @if ($address($customer)){{ $address($customer) }}<br>@endif
            @if ($customer?->tax_id)VAT / Tax ID: {{ $customer->tax_id }}<br>@endif
            @if ($order->contact)
                Attn: {{ $order->contact->name }}<br>
                @if ($order->contact->email)<span class="contact-mail">{{ $order->contact->email }}</span><br>@endif
                @if ($order->contact->phone)<span class="contact-phone">{{ $order->contact->phone }}</span>@endif
            @endif
        </td>
        <td style="width: 50%">
            <table class="meta">
                <tr><td>NUMBER:</td><td>{{ $order->name }}</td></tr>
                <tr><td>DATE:</td><td>{{ $order->offer_date->format('d/m/Y') }}</td></tr>
                @if ($order->validity_date)<tr><td>VALID UNTIL:</td><td>{{ $order->validity_date->format('d/m/Y') }}</td></tr>@endif
            </table>
            @if ($shipTo || $order->event)
                <div style="margin-top: 8px">
                    @if ($shipTo)
                        <span class="label">Ship to</span><br>
                        {{ $shipTo->name }}<br>
                        @if ($address($shipTo)){{ $address($shipTo) }}<br>@endif
                        @if ($shipTo->huvant_onsite_contact)Contact: {{ $shipTo->huvant_onsite_contact }}@endif
                        @if ($shipTo->phone) · {{ $shipTo->phone }}@endif
                        @if ($shipTo->huvant_onsite_contact || $shipTo->phone)<br>@endif
                    @endif
                    @if ($order->event)<span class="label">Event:</span> {{ $order->event }}@endif
                </div>
            @endif
        </td>
    </tr>
</table>

@if ($order->payment_terms || $order->subject)
    <p style="margin: 14px 0 0">
        @if ($order->payment_terms)<span class="label">Payment</span><br>{{ $order->payment_terms }}<br>@endif
        @if ($order->subject)<strong>{{ $order->subject }}</strong>@endif
    </p>
@endif

<table class="lines">
    <thead>
    <tr>
        <th style="width: {{ $hasDiscount ? 38 : 45 }}%">ACTIVITY DETAILS</th>
        <th style="width: 9%">Q.TY</th>
        <th style="width: 16%">UNIT PRICE €</th>
        @if ($hasDiscount)<th style="width: 9%">DISC.</th>@endif
        <th style="width: 15%">COD</th>
        <th style="width: 13%">AMOUNT</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->lines as $line)
        <tr @class(['last' => $loop->last && $blankRows === 0])>
            <td>{{ $line->product?->name }}@if ($line->description)<br><span class="muted">{{ $line->description }}</span>@endif</td>
            <td class="input">{{ $number((float) $line->quantity, 4) }}</td>
            <td class="input">{{ $money((float) $line->unit_price) }}</td>
            @if ($hasDiscount)<td class="input">{{ (float) $line->discount > 0 ? $number((float) $line->discount, 2).'%' : '' }}</td>@endif
            <td class="input">{{ $line->proformaCode() }}</td>
            <td class="amount">{{ $money($line->subtotal()) }}</td>
        </tr>
    @endforeach
    @for ($i = 0; $i < $blankRows; $i++)
        <tr @class(['last' => $i === $blankRows - 1])>
            <td></td>
            @for ($c = 0; $c < $columns - 2; $c++)<td class="input"></td>@endfor
            <td class="amount"></td>
        </tr>
    @endfor
    </tbody>
</table>

<table class="totals">
    <tr>
        <td style="width: {{ $hasDiscount ? 47 : 54 }}%"></td>
        <td class="caption" style="width: 16%">SUBTOTAL</td>
        <td style="width: 17%"></td>
        <td class="value" style="width: 13%">€ {{ $money($order->untaxedAmount()) }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="caption">VAT</td>
        <td style="text-align: right">{{ $number((float) $order->vat_rate, 2) }}%</td>
        <td class="value">{{ $money($order->taxAmount()) }}</td>
    </tr>
    <tr class="total">
        <td></td>
        <td class="caption">TOTAL</td>
        <td style="text-align: right">€</td>
        <td class="value">{{ $money($order->totalAmount()) }}</td>
    </tr>
</table>

@if ($order->notes)
    <p style="margin-top: 14px"><span class="label">Notes</span><br>{!! nl2br(e($order->notes)) !!}</p>
@endif

<div class="legal">
    VAT will be applied totally, partially or not at all, only after the check carried out in the VAT Information Exchange System (VIES).<br>
    This document does not constitute an invoice pursuant to art. 21 of Presidential Decree 633/72 and therefore does not generate tax liability
    for the provider. At the same time as payment, a regular invoice will be issued with VAT highlighted.
</div>
</body>
</html>
