<style>
    table { width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:10px; }
    th, td { border:1px solid #ddd; padding:6px; }
    th { font-weight:bold; }
</style>

<table>
     <thead>
            <tr>
                <th>{{trans_choice('general.name',1)}}</th>
                <th>{{trans_choice('general.office',1)}}</th>
                <th>{{trans_choice('general.staff',1)}}</th>
                <th>{{trans_choice('general.phone',1)}}</th>

                <th>ID</th>
                <th>Bank Acc Name</th>

                <th>Next of Kin</th>
                <th>NOK Phone</th>
                <th>NOK Relationship</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $c)
                @php
                    $id = $c->identifications->first(); // latest because ordered desc
                    $nok = $c->next_of_kin->first();    // first because ordered asc
                @endphp
                <tr>
                    <td>
                        @if($c->client_type=="individual")
                            {{$c->first_name}} {{$c->middle_name}} {{$c->last_name}}
                        @else
                            {{$c->full_name}}
                        @endif
                    </td>
                    <td>{{ $c->office->name ?? '' }}</td>
                    <td>{{ ($c->staff->first_name ?? '') . ' ' . ($c->staff->last_name ?? '') }}</td>
                    <td>{{ $c->mobile ?? '' }}</td>

                    <td>{{ $id->name ?? '' }}</td>
                    <td>{{ $c->bank_account_number ?? '' }}</td>

                    <td>{{ ($nok->first_name ?? '') . ' ' . ($nok->last_name ?? '') }}</td>
                    <td>{{ $nok->phone ?? '' }}</td>
                    @php
    $rel = $nok->relationship ?? null;

    // If relationship is JSON string, decode it
    if (is_string($rel)) {
        $decoded = json_decode($rel, true);
        $relName = is_array($decoded) ? ($decoded['name'] ?? $rel) : $rel;
    }
    // If relationship is already an array/object
    elseif (is_array($rel)) {
        $relName = $rel['name'] ?? '';
    } elseif (is_object($rel)) {
        $relName = $rel->name ?? '';
    } else {
        $relName = '';
    }
@endphp

<td>{{ $relName }}</td>

                </tr>
            @endforeach
            </tbody>
</table>
