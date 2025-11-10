@extends('layouts.master')
@section('title')
    Branch Graph
@endsection
@section('content')
<?php
$reloans_given_out = 0;
 $new_loans_given_out = 0;
 $given_out_total = 0;
 $collected_total = 0;
 $reloan_payments = 0;
 $part_payments = 0;
 $full_payments = 0;
 $branch_balance_list = [];
 $branch_collections_list =  [];
 $branch_given_out_list = [];
 $reloan_list = [];
 $payment_list = [];
 $reloan_count = 0;
 $payment_count = 0;

foreach($office_loans as $office_loan_list){

    foreach($office_loan_list as $office_loan){
        foreach($office_loan->transactions as $transaction){
            if($transaction->transaction_type == 'disbursement'){
                $disbursement_interest = $transaction->debit/0.4;
                $new_loans_given_out = $new_loans_given_out + $transaction->debit + $disbursement_interest;
            }

            if($transaction->transaction_type == 'interest' && $transaction->date){
                $principal = $transaction->debit/0.4;
                $reloans_given_out = $reloans_given_out + $principal + $transaction->debit;
            }

             
        if($transaction->payment_apply_to == 'reloan_payment'){

            $reloan_count = $reloan_count + 1;
        
            $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
            $interest = $transaction->credit/0.4;
            $reloan_payments = $reloan_payments + $reloan_amount ; 
            }

            if($transaction->payment_apply_to == 'part_payment'){
                $part_payments = $part_payments + $transaction->credit;
                
                $payment_count = $payment_count + 1;
            }

                 
        if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment'){
            $full_payments = $full_payments + $transaction->credit;
            $payment_count = $payment_count + 1;
        }

        }



        }

       
        $given_out_total = $new_loans_given_out + $reloans_given_out;
        $collected_total = $reloan_payments + $part_payments + $full_payments;
    
        array_push($reloan_list,$reloan_count);
        array_push($payment_list,$payment_count);
        array_push($branch_balance_list,($given_out_total - $collected_total));
        array_push($branch_collections_list,$collected_total);
        array_push($branch_given_out_list,$given_out_total);
        $reloans_given_out = 0;
     $new_loans_given_out = 0;
     $given_out_total = 0;
     $reloan_payments = 0;
     $part_payments = 0;
     $full_payments = 0;
     $reloan_count = 0;
     $payment_count = 0;


    }
  

?>
<div>
    <canvas id='companygraph'></canvas>
</div>
@endsection
@section('footer-scripts')
<script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/gauge.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/funnel.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}"
            type="text/javascript"></script>


    <script>
         const allchrt = document.getElementById('companygraph');
         var branches =  <?php echo json_encode($office_names); ?>;
         var defaults = <?php echo json_encode($branch_balance_list); ?>;
         var given_out_values = <?php echo json_encode($branch_given_out_list); ?>;
         var collections =<?php echo json_encode($branch_collections_list); ?>;
         var reloans = <?php echo json_encode($reloan_list); ?>;
         var payments = <?php echo json_encode($payment_list); ?>;

      

    var chartI = new Chart(allchrt, {
         type: 'bar',
         data: {
            labels: branches,
            datasets: [
            {
               label: "reloans",
               data:reloans,
               borderWidth: 1,
            },
            {
               label: "part and full payments",
               data:payments,
               borderWidth: 1,
            },
        ],
         },
         options: {
            scales: {
      y: {
        beginAtZero: true
      }
    }
         },
      });
    </script>


@endsection
