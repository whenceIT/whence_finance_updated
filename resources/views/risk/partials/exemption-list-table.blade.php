<div class="card" style="margin: 20px 0;">
    <div class="card-header" style="background: #667eea; color: #fff; padding: 12px 20px; font-weight: 600;">
        <i class="fa fa-list"></i> Deposit Month Exemptions
    </div>
    <div class="card-body" style="padding: 0;">
        @php
            $exemptions = \App\Models\DepositMonthExemption::with(['office', 'depositType'])->get();
            $grouped = $exemptions->groupBy('office_id');
        @endphp
        
        @if($exemptions->isEmpty())
            <p style="padding: 20px; color: #666; font-style: italic;">No exemptions configured.</p>
        @else
            <table class="table" style="margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8fc;">
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Office</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Deposit Types</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Exemptions</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Total Months Excluded</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Months</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grouped as $officeId => $officeExemptions)
                        @php
                            $office = $officeExemptions->first()->office;
                            $depositTypes = $officeExemptions->pluck('depositType.name')->map(function($name) {
                                return $name ?? 'All Types';
                            })->unique()->values()->toArray();
                            $totalExemptions = $officeExemptions->count();
                            $totalMonthsExcluded = $officeExemptions->sum('no_months_exclude');
                        @endphp
                        <tr>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7; font-weight: 500;">
                                {{ $office->name ?? 'Unknown Office' }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                @foreach($depositTypes as $type)
                                    <span style="display: inline-block; background: #667eea; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 12px; margin: 2px; margin-right: 4px;">
                                        {{ $type }}
                                    </span>
                                @endforeach
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $totalExemptions }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $totalMonthsExcluded }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                @php
                                    $allMonths = [];
                                    foreach($officeExemptions as $ex) {
                                        if(!empty($ex->months)) {
                                            $allMonths = array_merge($allMonths, $ex->months);
                                        }
                                    }
                                    $uniqueMonths = array_unique($allMonths);
                                @endphp
                                @if(!empty($uniqueMonths))
                                    {{ implode(', ', array_map(function($m) { return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m-1] ?? $m; }, $uniqueMonths)) }}
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <!-- Add an Edit Button -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>