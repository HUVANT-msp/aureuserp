@php
    $record = $getRecord();
    $share = $record->remainingShare();
    $level = $share <= 0.15 ? 'below' : ($share <= 0.35 ? 'low' : 'ok');
    $unit = $record->material?->huvant_package_unit;
@endphp
<div class="hv-lab-remaining hv-lab-{{ $level }}" title="{{ round($share * 100) }}%">
    <div class="hv-lab-remaining-track"><div class="hv-lab-remaining-fill" style="width: {{ round($share * 100, 1) }}%"></div></div>
    <span class="hv-lab-remaining-text">{{ \Huvant\Orders\Support\LabInventory::format((float) $record->remaining_quantity) }} / {{ \Huvant\Orders\Support\LabInventory::format((float) $record->initial_quantity, $unit) }}</span>
</div>
