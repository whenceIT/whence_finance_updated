@props([
    'currentStatus' => 'pledged',
    'showHeader' => true,
])

@php
    $allStatuses = [
        'pledged' => 'Pledged',
        'seizure_pending' => 'Seizure Pending',
        'seized_inventory' => 'Seized/Inventory',
        'valuation_completed' => 'Valuation Completed',
        'listed_for_sale' => 'Listed for Sale',
        'sold' => 'Sold',
        'written_off' => 'Written Off',
        'released' => 'Released',
    ];
    $statusLabels = [
        'pledged' => 'Collateral attached to an active loan.',
        'seizure_pending' => 'Initiated by Branch Manager, awaiting approval and handover.',
        'seized_inventory' => 'Physically taken and in central inventory, awaiting evaluation.',
        'valuation_completed' => 'Independent valuation recorded, not yet sold.',
        'listed_for_sale' => 'Asset is being marketed.',
        'sold' => 'Asset sold and proceeds received.',
        'written_off' => 'Asset unsaleable and removed from inventory.',
        'released' => 'Asset returned to borrower.',
    ];
    $currentStep = $currentStatus;
    $stepKeys = array_keys($allStatuses);
    $currentIndex = array_search($currentStep, $stepKeys, true);
@endphp

<div class="cd-timeline" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 10px;">
        @foreach($allStatuses as $key => $label)
            @php
                $isCurrent = $key === $currentStep;
                $isPast = $currentIndex !== false && array_search($key, $stepKeys, true) < $currentIndex;
                $stepIndex = array_search($key, $stepKeys, true) + 1;
            @endphp
            <div style="display: flex; align-items: center; flex-shrink: 0; min-width: 90px; flex-direction: column; text-align: center; position: relative;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: @if($isCurrent) #000c3c @elseif($isPast) #28a745 @else #e2e8f0 @endif; color: @if($isCurrent) #fff @elseif($isPast) #fff @else #6b7280 @endif; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-bottom: 6px; z-index: 2;">
                    @if($isPast)
                        <i class="fa fa-check"></i>
                    @else
                        {{ $stepIndex }}
                    @endif
                </div>
                <div style="font-size: 11px; font-weight: {{ $isCurrent ? '700' : '500' }}; color: {{ $isCurrent ? '#000c3c' : ($isPast ? '#1e8e5a' : '#6b7280') }}; white-space: nowrap;">{{ $label }}</div>
                @if(!$isCurrent && !$isPast && $loop->last)
                    <div style="font-size: 10px; color: #8a94a6; white-space: nowrap; margin-top: 2px;">End</div>
                @endif
            </div>
            @if(!$loop->last)
                <div style="flex: 1; height: 2px; background: {{ $isPast ? '#28a745' : '#e2e8f0' }}; margin: 0 4px; min-width: 20px; align-self: flex-start; margin-top: 14px;"></div>
            @endif
        @endforeach
    </div>
    @if($showHeader)
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 18px; margin-top: 12px; display: flex; align-items: flex-start; gap: 12px;">
        <i class="fa fa-lightbulb-o" style="color: #6366f1; font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
        <div>
            <div style="font-size: 13.5px; color: #1e293b; line-height: 1.6;">
                <strong>Current Stage: {{ $allStatuses[$currentStep] ?? 'Pledged' }}</strong> — {{ $statusLabels[$currentStep] ?? $statusLabels['pledged'] }}
            </div>
            <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">This is the first step in the Loan Collateral Workflow.</div>
        </div>
    </div>
    @endif
</div>
