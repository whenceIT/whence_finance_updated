{{-- recursive_row.blade.php --}}
<style>
.bold-heading {
    font-weight: bold;
}

</style>
<tr>
    <td style="padding-left: {{ $level * 20 }}px;">{{ $key->gl_code }}</td>
    <td>{{ $key->name }}</td>
    <td class="hidden" align="right">{{ number_format($op_balance_dr, 2) }}</td>
    <td class="hidden" align="right">{{ number_format($op_balance_cr, 2) }}</td>
    <td align="right">{{ number_format($xdr, 2) }}</td>
    <td align="right">{{ number_format($xcr, 2) }}</td>
    <td align="right">{{ number_format($debits, 2) }}</td>
    <td align="right">{{ number_format($credits, 2) }}</td>
</tr>

{{-- Check if there are children and recursively include them --}}
@if ($key->children->count() > 0)
    @foreach($key->children as $child)
        @include('partials.recursive_row', ['key' => $child, 'level' => $level + 1])
    @endforeach
@endif
