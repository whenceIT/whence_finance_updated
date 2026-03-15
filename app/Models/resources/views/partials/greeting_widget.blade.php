<style>
.box .box-header.with-border {
    background: rgba(0,0,0,0.03);
    transition: background 0.3s ease;
}

.box .box-header.with-border:hover {
    background: rgba(0,0,0,0.08);
}

.panel-collapse {
    transition: all 0.3s ease;
}


</style>

<div class="box greeting-widget" style="background: linear-gradient(135deg, #6dd5ed, #2193b0); color: white; position: relative; overflow: hidden;">
    <div class="animated-bg"></div>

    <div class="box-body" style="position: relative; z-index: 1;">
        <h3 class="box-title">👋 {{ \App\Helpers\GeneralHelper::greetingMessage() }}, {{ Sentinel::getUser()->first_name }}!</h3>
        <p class="text-muted" style="color: #f0f0f0;">Here’s what’s happening today:</p>

        

        <div class="box-group" id="accordion" style="margin-top: 10px;">
    {{-- Disbursed Today --}}
    <div class="panel box box-success">
        <div class="box-header with-border" style="cursor: pointer;" data-toggle="collapse" data-parent="#accordion" href="#collapseDisbursed">
            <h4 class="box-title" style="display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa fa-arrow-circle-down"></i> Disbursed Today</span>
                <i class="fa fa-chevron-down"></i>
            </h4>
        </div>
        <div id="collapseDisbursed" class="panel-collapse collapse in">
        <div class="box-body" style="font-size: 16px; color: #333; position: relative; padding-right: 120px;">
    💸 <strong>ZMW {{ number_format($disbursedToday ?? 0, 2) }}</strong> {{ trans_choice('general.loan', 2) }} disbursed today.

    <a href="{{url('report/loan_report/disbursed_loans')}}"
       class="btn btn-link btn-sm"
       style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%);">
        View Details <i class="fa fa-arrow-right"></i>
    </a>
</div>




        </div>
    </div>

    {{-- Due Today --}}
    <div class="panel box box-info">
        <div class="box-header with-border" style="cursor: pointer;" data-toggle="collapse" data-parent="#accordion" href="#collapseDue">
            <h4 class="box-title" style="display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa fa-calendar"></i> {{ trans_choice('general.loan', 2) }} Due Today</span>
                <i class="fa fa-chevron-down"></i>
            </h4>
        </div>
        <div id="collapseDue" class="panel-collapse collapse">
        <div class="box-body" style="font-size: 16px; color: #333; position: relative; padding-right: 120px;">
    📅 <strong>ZMW {{ number_format($dueToday ?? 0, 2) }}</strong> expected in repayments.

    <a href="{{url('report/loan_report/collection_sheet')}}"
       class="btn btn-link btn-sm"
       style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%);">
        View Details <i class="fa fa-arrow-right"></i>
    </a>
</div>


        </div>
    </div>
</div>


    </div>
</div>
