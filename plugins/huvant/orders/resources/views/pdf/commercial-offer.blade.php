@php
    $company = $order->company;
    $companyPartner = $company?->partner;
    $bankAccount = $companyPartner?->bankAccounts->first();
    $customer = $order->partner;
    $shipTo = $order->deliveryAddress;
    $money = fn (float $amount): string => '€ '.number_format($amount, 2, ',', '.');
    $address = fn ($record): string => collect([
        $record?->street1,
        $record?->street2,
        trim(($record?->zip ?? '').' '.($record?->city ?? '')),
        $record?->country?->name,
    ])->filter()->implode(', ');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Commercial Offer {{ $order->name }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2933; }
        h1 { font-size: 18px; margin: 0; letter-spacing: .04em; }
        .muted { color: #616e7c; }
        .row { width: 100%; }
        .row td { vertical-align: top; }
        .box { border: 1px solid #cbd2d9; padding: 8px 10px; }
        .label { font-size: 8px; text-transform: uppercase; color: #616e7c; letter-spacing: .06em; }
        table.lines { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.lines th { background: #e4e7eb; font-size: 8px; text-transform: uppercase; text-align: left; padding: 6px; }
        table.lines td { border-bottom: 1px solid #e4e7eb; padding: 6px; }
        .num, table.lines th.num { text-align: right; white-space: nowrap; }
        table.totals { width: 45%; margin-left: 55%; margin-top: 10px; border-collapse: collapse; }
        table.totals td { padding: 4px 6px; }
        table.totals tr.total td { font-weight: bold; border-top: 1px solid #1f2933; }
        .legal { font-size: 7.5px; color: #616e7c; margin-top: 18px; }
    </style>
</head>
<body>
<table class="row">
    <tr>
        <td style="width: 55%">
            <strong style="font-size: 13px">{{ $company?->name }}</strong><br>
            {{ $address($company) }}<br>
            @if ($company?->email){{ $company->email }}<br>@endif
            @if ($companyPartner?->huvant_pec)PEC: {{ $companyPartner->huvant_pec }}<br>@endif
            @if ($company?->tax_id)VAT: {{ $company->tax_id }}<br>@endif
            @if ($companyPartner?->huvant_sdi_code)SDI recipient code: {{ $companyPartner->huvant_sdi_code }}<br>@endif
            @if ($bankAccount)
                {{ $bankAccount->bank?->name }}<br>
                IBAN {{ $bankAccount->account_number }}
            @endif
        </td>
        <td style="width: 45%; text-align: right">
            <h1>COMMERCIAL OFFER</h1>
            <div style="margin-top: 8px">
                <span class="label">Number</span> <strong>{{ $order->name }}</strong><br>
                <span class="label">Date</span> {{ $order->offer_date->format('d/m/Y') }}<br>
                @if ($order->validity_date)<span class="label">Valid until</span> {{ $order->validity_date->format('d/m/Y') }}@endif
            </div>
        </td>
    </tr>
</table>

<table class="row" style="margin-top: 16px">
    <tr>
        <td style="width: 50%; padding-right: 6px">
            <div class="box">
                <div class="label">To</div>
                <strong>{{ $customer?->name ?? '—' }}</strong><br>
                {{ $address($customer) }}<br>
                @if ($customer?->tax_id)VAT / Tax ID: {{ $customer->tax_id }}<br>@endif
                @if ($order->contact)
                    Attn: {{ $order->contact->name }}<br>
                    @if ($order->contact->email){{ $order->contact->email }}<br>@endif
                    @if ($order->contact->phone){{ $order->contact->phone }}@endif
                @endif
            </div>
        </td>
        <td style="width: 50%; padding-left: 6px">
            <div class="box">
                <div class="label">Ship to</div>
                @if ($shipTo)
                    <strong>{{ $shipTo->name }}</strong><br>
                    {{ $address($shipTo) }}<br>
                    @if ($shipTo->huvant_onsite_contact)Contact: {{ $shipTo->huvant_onsite_contact }}@endif
                    @if ($shipTo->phone) · {{ $shipTo->phone }}@endif
                @else
                    Registered office
                @endif
                @if ($order->event)<br>Event: {{ $order->event }}@endif
            </div>
        </td>
    </tr>
</table>

@if ($order->payment_terms || $order->subject)
    <p style="margin-top: 12px">
        @if ($order->payment_terms)<span class="label">Payment</span> {{ $order->payment_terms }}<br>@endif
        @if ($order->subject)<strong>{{ $order->subject }}</strong>@endif
    </p>
@endif

<table class="lines">
    <thead>
    <tr>
        <th>Activity details</th>
        <th>Code</th>
        <th class="num">Q.ty</th>
        <th class="num">Unit price</th>
        <th class="num">Discount</th>
        <th class="num">Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->lines as $line)
        <tr>
            <td>{{ $line->product?->name }}@if ($line->description)<br><span class="muted">{{ $line->description }}</span>@endif</td>
            <td>{{ $line->product?->reference }}</td>
            <td class="num">{{ rtrim(rtrim(number_format((float) $line->quantity, 4, ',', '.'), '0'), ',') }}</td>
            <td class="num">{{ $money((float) $line->unit_price) }}</td>
            <td class="num">{{ (float) $line->discount > 0 ? rtrim(rtrim(number_format((float) $line->discount, 2, ',', '.'), '0'), ',').'%' : '' }}</td>
            <td class="num">{{ $money($line->subtotal()) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotal</td><td class="num">{{ $money($order->untaxedAmount()) }}</td></tr>
    <tr><td>VAT {{ rtrim(rtrim(number_format((float) $order->vat_rate, 2, ',', '.'), '0'), ',') }}%</td><td class="num">{{ $money($order->taxAmount()) }}</td></tr>
    <tr class="total"><td>Total</td><td class="num">{{ $money($order->totalAmount()) }}</td></tr>
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
