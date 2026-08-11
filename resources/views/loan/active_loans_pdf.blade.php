<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    th, td {
        padding: 6px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #339933;
        color: white;
        font-weight: bold;
    }

    .header {
        margin-bottom: 15px;
    }

    .header img {
        width: 90px;
    }

    .header h2 {
        margin: 5px 0;
    }
</style>
<div class="header">
    @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
        <img src="{{ public_path('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}" width="90"/>
    @endif
    <h2>{{ trans_choice('general.active', 1) }} {{ trans_choice('general.loan', 2) }}</h2>
    @if(isset($query) && $query)
        <p><b>Showing results for: {{ $query }}</b></p>
    @endif
</div>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>{{ trans_choice('general.account', 1) }}#</th>
            <th>{{ trans_choice('general.branch', 1) }}</th>
            <th>{{ trans_choice('general.client', 1) }}</th>
            <th>Loan Consultant</th>
            <th>vetted_by</th>
            <th>verified_by</th>
            <th>{{ trans_choice('general.product', 1) }}</th>
            <th>{{ trans_choice('general.balance', 1) }}</th>
            <th>{{ trans_choice('general.disbursement', 1) }} {{ trans_choice('general.date', 1) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $key)
            @php
                $balance = 0;
                $debit = 0;
                $credit = 0;
            @endphp
            @foreach($key->transactions as $transaction)
                @if($transaction->transaction_type != 'specified_due_date_fee')
                    @php $debit += $transaction->debit; @endphp
                @endif
                @php $credit += $transaction->credit; @endphp
            @endforeach
            @php $balance = $debit - $credit; @endphp
            <tr>
                <td>{{ $key->id }}</td>
                <td>
                    @if(!empty($key->office))
                        {{ $key->office->name }}
                    @endif
                </td>
                <td>
                    @if($key->client_type == "client")
                        @if(!empty($key->client))
                            @if($key->client->client_type == "individual")
                                {{ $key->client->first_name }} {{ $key->client->middle_name }} {{ $key->client->last_name }}
                            @else
                                {{ $key->client->full_name }}
                            @endif
                        @endif
                    @endif
                    @if($key->client_type == "group")
                        {{ $key->group->name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->loan_officer))
                        {{ $key->loan_officer->first_name }} {{ $key->loan_officer->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->vetted_by_field))
                        {{ $key->vetted_by_field->first_name }} {{ $key->vetted_by_field->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->verified_by_field))
                        {{ $key->verified_by_field->first_name }} {{ $key->verified_by_field->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->loan_product))
                        {{ $key->loan_product->name }}
                    @endif
                </td>
                <td>{{ number_format($balance, $key->decimals) }}</td>
                <td>{{ $key->disbursement_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
