<div class="card" style="margin: 20px 0;">
    <div class="card-header" style="background: #667eea; color: #fff; padding: 12px 20px; font-weight: 600;">
        <i class="fa fa-list"></i> Deposit Month Exemptions
    </div>
    <div class="card-body" style="padding: 0;">
        @php
            $exemptions = \App\Models\DepositMonthExemption::with(['office', 'depositType'])->get();
        @endphp
        
        @if($exemptions->isEmpty())
            <p style="padding: 20px; color: #666; font-style: italic;">No exemptions configured.</p>
        @else
            <table class="table" style="margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8fc;">
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">ID</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Office</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Deposit Type</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Months Excluded</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Months</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exemptions as $ex)
                        <tr>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $ex->id }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{App\Models\DepositMonthExemption::office_name($ex->id)}}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $ex->depositType->name ?? 'All Types' }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $ex->no_months_exclude }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                @if($ex->months && count($ex->months) > 0)
                                    {{ implode(', ', array_map(function($m) { return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m-1] ?? $m; }, $ex->months)) }}
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7; color: #888;">
                                {{ $ex->created_at->format('Y-m-d') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>