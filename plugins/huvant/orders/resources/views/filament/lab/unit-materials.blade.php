@php use Huvant\Orders\Support\LabInventory; @endphp
@if ($materials->isEmpty())
    <p class="hv-lab-empty">{{ __('huvant-orders::lab.no_materials_for_piece') }}</p>
@else
    <div class="fi-ta-ctn" style="overflow-x: auto">
        <table class="fi-ta-table" style="width: 100%; font-size: 14px">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 6px 8px">{{ __('huvant-orders::lab.material') }}</th>
                    <th style="text-align: left; padding: 6px 8px">{{ __('huvant-orders::lab.lot') }}</th>
                    <th style="text-align: right; padding: 6px 8px">{{ __('huvant-orders::lab.used') }}</th>
                    <th style="text-align: left; padding: 6px 8px">{{ __('huvant-orders::lab.lot_expiry') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materials as $used)
                    <tr style="border-top: 1px solid var(--gray-200)">
                        <td style="padding: 6px 8px">{{ $used->material?->name }}</td>
                        <td style="padding: 6px 8px; font-family: ui-monospace, monospace">{{ $used->lot_number }}</td>
                        <td style="padding: 6px 8px; text-align: right">{{ LabInventory::format((float) $used->quantity, $used->material?->huvant_package_unit) }}</td>
                        <td style="padding: 6px 8px">{{ $used->lot?->expiry_date?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
