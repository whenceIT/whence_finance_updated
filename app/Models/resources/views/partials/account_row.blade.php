<tr>
    <td>
        {{-- Display company code with indentation --}}
        <span class="account-indicator" style="margin-left: {{ $level * 20 }}px;">
            @if ($level === 0)
                <strong>{{ $account->company_code }} - {{ $account->gl_code }}</strong>
            @else
                {{ $account->company_code }} - {{ $account->gl_code }}
            @endif
        </span>
    </td>
    <td>
        <span class="account-indicator" style="margin-left: {{ $level * 20 }}px;">
            @if ($level === 0)
                <strong>{{ $account->name }}</strong>
            @else
                {{ $account->name }}
            @endif
        </span>
    </td>
    <td>
        @if($account->account_type == "expense")
            {{ trans_choice('general.expense', 1) }}
        @elseif($account->account_type == "asset")
            {{ trans_choice('general.asset', 1) }}
        @elseif($account->account_type == "equity")
            {{ trans_choice('general.equity', 1) }}
        @elseif($account->account_type == "liability")
            {{ trans_choice('general.liability', 1) }}
        @elseif($account->account_type == "income")
            {{ trans_choice('general.income', 1) }}
        @endif
    </td>
    
    {{-- Display balance and unreconciled balance only for child accounts, not parents --}}
    <td>
        @if($level === 0)
            {{-- Show empty if it's a parent account --}}
            &nbsp;
        @else
            {{-- Show actual balance if it's a child --}}
            {{ $account->group_total < 0 ? '('. number_format(abs($account->group_total), 2) .')' : number_format($account->group_total, 2) }}
        @endif
    </td>
    
    <td>
        @if($level === 0)
            {{-- Show empty if it's a parent account --}}
            &nbsp;
        @else
            {{-- Show actual unreconciled balance if it's a child --}}
            {{ $account->unreconciled_balance < 0 ? '('. number_format(abs($account->unreconciled_balance), 2) .')' : number_format($account->unreconciled_balance, 2) }}
        @endif
    </td>
    
    <td>{!! $account->notes !!}</td>
    <td>
        <div class="btn-group">
            <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-expanded="false"><i class="fa fa-navicon"></i></button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                @if(Sentinel::hasAccess('accounting.gl_accounts.update'))
                    <li><a href="{{ url('accounting/gl_account/' . $account->id . '/edit') }}"><i
                                class="fa fa-edit"></i> {{ trans('general.edit') }}</a></li>
                @endif
                @if(Sentinel::hasAccess('accounting.gl_accounts.delete'))
                    <li><a href="{{ url('accounting/gl_account/' . $account->id . '/delete') }}"
                           class="delete"><i class="fa fa-trash"></i> {{ trans('general.delete') }}</a></li>
                @endif
            </ul>
        </div>
    </td>
</tr>

{{-- Recursively include children --}}
@if($account->children)
    @foreach($account->children as $child)
        @include('partials.account_row', ['account' => $child, 'level' => $level + 1])
    @endforeach

    {{-- Parent total row only for level 1 (top-level) --}}
    @if($level == 0)
        <tr>
            <td colspan="7" style="border-bottom: 0px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                
            </td>
            <td>
                {{-- Empty column for the account type --}}
            </td>
            <td><strong>{{ $account->group_total < 0 ? '('. number_format(abs($account->group_total), 2) .')' : number_format($account->group_total, 2) }}</strong></td>
            <td><strong>{{ $account->unreconciled_balance < 0 ? '('. number_format(abs($account->unreconciled_balance), 2) .')' : number_format($account->unreconciled_balance, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    @endif
@endif
