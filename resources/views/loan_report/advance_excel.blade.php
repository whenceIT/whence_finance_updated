<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }


    .style-1 {
        color: white;
        padding-left: 10pt;
        font-size: 14pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
        background-color: #339933;
    }
    .style-2 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
    .style-3 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
</style>

<div>
    <div class="col-md-12">
	    <div class="white-box">   
            <h3><b>Advances Report</b></h3>
            <hr>
	            <div class="row">
	                <div class="col-md-12">
						<div class="pull-left">
							<address>
                                @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                                <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                                    class="img-responsive" width="90"/><br> 
                                @endif
                                <p class="text-muted m-l-6">
                                    from: <b>{{$start_date}} to {{$end_date}} <br> <br>
                                </p>
							</address>
						</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Installments</th>
                                <th>Amount Paid</th>
                                <th>Remaining Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advances as $advance)
                            <tr>
                                <td>{{ $advance->id }}</td>
                                <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                                <td>{{ $advance->amount }}</td>
                                <td>{{ $advance->installments }}</td>
                                <td>{{ $advance->amount_paid }}</td>
                                <td>{{ $advance->remaining_amount }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td><b>Total</b></td>
                                <td>{{ number_format($advances->sum('amount'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </hr>
        </div>
    </div>
</div>
