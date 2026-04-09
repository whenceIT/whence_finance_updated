Name,Loan ID,Type,Status,Condition,Current Worth,Date Purchased,Office
@foreach($data as $item)
{{ $item->name }},{{ optional($item->loan)->id }},{{ optional($item->type)->name }},{{ $item->status }},{{ $item->condition }},{{ $item->current_worth }},{{ optional($item->date_purchased)->format('Y-m-d') }},{{ optional($item->loan->office)->name }}
@endforeach