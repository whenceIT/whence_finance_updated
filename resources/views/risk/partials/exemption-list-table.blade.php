<div class="card" style="margin: 20px 0;">
    <div class="card-header" style="background: #667eea; color: #fff; padding: 12px 20px; font-weight: 600;">
        <i class="fa fa-list"></i> Deposit Month Exemptions
    </div>
    <div class="card-body" style="padding: 0;">
        @php
            $exemptions = \App\Models\DepositMonthExemption::with(['office', 'depositType'])->get();
            $depositTypes = \App\Models\DepositType::orderBy('name')->get();
            $offices = \App\Models\Office::orderBy('name')->get();
        @endphp
        
        @if($exemptions->isEmpty())
            <p style="padding: 20px; color: #666; font-style: italic;">No exemptions configured.</p>
        @else
            <table class="table" style="margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8fc;">
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Office</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Deposit Type</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Months</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Amount Exempted</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exemptions as $exemption)
                        @php
                            $office = $exemption->office;
                            $depositType = $exemption->depositType;
                            $months = $exemption->months ?? [];
                            $monthlyAmount = $depositType ? $depositType->monthly_amount : 0;
                            $amountExcluded = $monthlyAmount * $exemption->no_months_exclude;
                            $depositTypeName = $depositType ? $depositType->name : 'All Types';
                        @endphp
                        <tr>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7; font-weight: 500;">
                                {{ $office ? $office->name : 'Unknown Office' }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                <span style="display: inline-block; background: #667eea; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 12px;">
                                    {{ $depositTypeName }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                @if(!empty($months))
                                    @foreach($months as $m)
                                        <span style="display: inline-block; background: #667eea; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin: 2px; margin-right: 4px;">
                                            {{ ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m-1] ?? $m }}
                                        </span>
                                    @endforeach
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                K{{ number_format($amountExcluded, 2) }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openEditExemptModal({{ json_encode($exemption) }})">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@include('risk.partials.deposit-exempt-modal', ['depositTypes' => $depositTypes, 'offices' => $offices])