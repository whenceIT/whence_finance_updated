@extends('layouts.master')

@section('title')
  {{ trans('general.dashboard') }}
@endsection

<style>
@keyframes scroll-left { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
.marquee-wrapper { overflow: hidden; width: 100%; margin-bottom: 20px; }
.marquee-track { display: inline-flex; white-space: nowrap; align-items: center; }

@keyframes scroll-left { 0% { transform: translateX(0%); } 100% { transform: translateX(-100%); } }

.animated-bg{
  position:absolute; top:-50%; left:-50%;
  width:200%; height:200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 20%, transparent 20%);
  background-size:20px 20px;
  animation: move-bg 30s linear infinite;
  z-index:0;
}
@keyframes move-bg { from { transform: translate(0,0); } to { transform: translate(50%,50%); } }
</style>

@section('content')

  {{-- =========================
      SEARCH BOX
  ========================= --}}
  <div class="box box-primary">
    <div class="box-header with-border">
      <form method="GET" action="{{ url()->current() }}" style="display:flex;">
        <div class="input-group" style="width:100%;">
          <span class="input-group-addon"><i class="fa fa-search"></i></span>
          <input type="text" name="search" id="search" class="form-control"
                 placeholder="Search by client name, NRC, Transaction ID..."
                 value="{{ request('search') }}">
        </div>

        @if(request('search'))
          <a href="{{ url()->current() }}" class="btn btn-default" style="margin-left:10px;">Clear</a>
        @endif
      </form>
    </div>

    @if(request('search'))
      <div class="box-body">
        <h4>🔎 Results for: <em>{{ request('search') }}</em></h4>

        @if(isset($clients) && $clients->isNotEmpty())
          <h5><i class="fa fa-user"></i> Clients</h5>
          <ul class="list-group mb-3">
            @foreach($clients as $client)
              <a href="{{ url('client/'.$client->id.'/show') }}" class="list-group-item">
                {{ $client->first_name }} {{ $client->last_name }} {{ $client->full_name }}
                <span class="text-muted"> | {{ $client->external_id ?? 'NRC not set' }}</span>
              </a>
            @endforeach
          </ul>
        @endif

        @if(isset($transactions) && $transactions->isNotEmpty())
          <h5><i class="fa fa-exchange"></i> Transactions</h5>
          <ul class="list-group">
            @foreach($transactions as $tx)
              <a href="{{ url('journal/'.$tx->id.'/show') }}" class="list-group-item">
                Transaction ID: <strong>{{ optional($tx->office)->name }}</strong>
                <span class="text-muted">
                  | {{ $tx->transaction_type ?? 'No description' }}
                  | ZMW {{ $tx->credit ?? $tx->debit }}
                </span>
              </a>
            @endforeach
          </ul>
        @endif

        @if((!isset($clients) || $clients->isEmpty()) && (!isset($transactions) || $transactions->isEmpty()))
          <p class="text-muted">No results found.</p>
        @endif
      </div>
    @endif
  </div>

  {{-- =========================
      GREETING + WEATHER
  ========================= --}}
  <div class="row">
    <div class="col-md-8 col-sm-12">
      @include('partials.greeting_widget')
    </div>
    <div class="col-md-4 col-sm-12">
      @include('partials.weather_widget')
    </div>
  </div>

  <div class="marquee-wrapper" style="overflow:hidden; white-space:nowrap;">
    <div class="marquee-track d-inline-flex" style="animation: scroll-left 25s linear infinite; gap: 15px;">
      {{-- Put any scrolling info boxes here --}}
    </div>
  </div>

  {{-- =========================
      CLIENT vs STAFF/ADMIN
      NOTE:
      "Admin" for cumulative = role admin OR permission offices.view
  ========================= --}}
  @php
    // ✅ Your requested rule:
    // - Role "admin" OR permission "offices.view" => cumulative figures + no filter box
    $isCumulativeUser = Sentinel::inRole('admin') || Sentinel::hasAccess('offices.view');

    // ✅ Strict display values
    $disbursedValue  = $isCumulativeUser ? (float)($disbursedTotal  ?? 0) : (float)($disbursedInPeriod ?? 0);
    $repaymentsValue = $isCumulativeUser ? (float)($repaymentsTotal ?? 0) : (float)($repaymentsInPeriod ?? 0);

    // These are still period-based unless you also pass totals for them
    $outstandingValue = (float)($totalOutstanding ?? 0);
    $arrearsValue     = (float)($arrearsInPeriod ?? 0);

    // Safe defaults for progress bars
    $changePercentage = isset($changePercentage) ? round((float)$changePercentage, 2) : 0;
    $changeDirection  = $changeDirection ?? null;

    $repaymentChangePercentage = isset($repaymentChangePercentage) ? round((float)$repaymentChangePercentage, 2) : 0;
    $repaymentChangeDirection  = $repaymentChangeDirection ?? null;

    $outstandingChange          = isset($outstandingChange) ? round((float)$outstandingChange, 2) : 0;
    $outstandingChangeDirection = $outstandingChangeDirection ?? null;

    $arrearsChangePercentage = isset($arrearsChangePercentage) ? round((float)$arrearsChangePercentage, 2) : 0;
    $arrearsChangeDirection  = $arrearsChangeDirection ?? null;

    $disbursedBarWidth = min(abs($changePercentage), 100);
    $disbursedBarColor = $changePercentage >= 0 ? 'progress-bar-success' : 'progress-bar-danger';

    $repaymentBarWidth = min(abs($repaymentChangePercentage), 100);
    $repaymentBarColor = $repaymentChangePercentage >= 0 ? 'progress-bar-success' : 'progress-bar-danger';

    $outstandingBarWidth = min(abs($outstandingChange), 100);
    $outstandingBarColor = $outstandingChange >= 0 ? 'progress-bar-success' : 'progress-bar-danger';

    $arrearsBarWidth = min(abs($arrearsChangePercentage), 100);
    $arrearsBarColor = $arrearsChangePercentage >= 0 ? 'progress-bar-success' : 'progress-bar-danger';
  @endphp

  @if(Sentinel::inRole('client'))

    {{-- =========================
        CLIENT DASHBOARD BOXES
    ========================= --}}
    <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans_choice('general.loan',2) }} {{ trans_choice('general.disbursed',1) }}</span>
            <span class="info-box-number">{{ number_format(\App\Helpers\GeneralHelper::client_total_disbursed_loans_amount(Sentinel::getUser()->id), 2) }}</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans_choice('general.total',2) }} {{ trans_choice('general.outstanding',2) }}</span>
            <span class="info-box-number">{{ number_format(\App\Helpers\GeneralHelper::client_total_loans_outstanding_amount(Sentinel::getUser()->id), 2) }}</span>
          </div>
        </div>
      </div>

      <div class="clearfix visible-sm-block"></div>

      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-red"><i class="fa fa-minus"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans_choice('general.in',2) }} {{ trans_choice('general.arrears',2) }}</span>
            <span class="info-box-number">{{ number_format(\App\Helpers\GeneralHelper::client_total_loans_overdue_amount(Sentinel::getUser()->id), 2) }}</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">{{ trans_choice('general.savings',2) }} {{ trans_choice('general.balance',1) }}</span>
            <span class="info-box-number">{{ number_format(\App\Helpers\GeneralHelper::total_client_savings_account_balance(Sentinel::getUser()->id), 2) }}</span>
          </div>
        </div>
      </div>
    </div>

  @else

    {{-- =========================
        STAFF/ADMIN FILTER
        hidden for cumulative users (admin + offices.view)
    ========================= --}}
    @if(!$isCumulativeUser)
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Statistics</h3>
        </div>

        <div class="box-body">
          <form method="GET" action="{{ url()->current() }}" class="form-horizontal">
            <div class="form-group">
              <label for="date_range" class="control-label col-md-2">
                {{ trans_choice('general.period', 1) }}
              </label>

              <div class="col-md-8">
                <input type="text"
                       class="form-control daterangepicker-field"
                       id="date_range"
                       style="width:100%;"
                       value="{{ $start_date }} to {{ $end_date }}"
                       required readonly />

                <input type="hidden" name="start_date" id="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" id="end_date" value="{{ $end_date }}">
              </div>

              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fa fa-filter"></i> Apply
                </button>

                @if(request()->has('start_date') || request()->has('end_date'))
                  <a href="{{ url()->current() }}" class="btn btn-link btn-sm text-danger" style="margin-top:5px;">
                    <i class="fa fa-times-circle"></i> Clear
                  </a>
                @endif
              </div>
            </div>
          </form>
        </div>
      </div>
    @endif

    {{-- =========================
        TOP 4 BOXES
    ========================= --}}
    <div class="row">

      @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon" style="display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-money" style="font-size:28px;"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">
                {{ trans_choice('general.loan', 2) }} {{ trans_choice('general.disbursed', 1) }}
                @if($isCumulativeUser) <small>(Cumulative)</small> @endif
              </span>

              <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_disbursed_loans_amount(),2)}}</span>

              @if(!$isCumulativeUser)
                <div class="progress">
                  <div class="progress-bar {{ $disbursedBarColor }}" style="width: {{ $disbursedBarWidth }}%">
                    {{ $changePercentage > 0 ? '+' : '' }}{{ $changePercentage }}%
                  </div>
                </div>
                <span class="progress-description">
                  @if($changeDirection === 'increase')
                    <i class="fa fa-arrow-up text-white"></i> {{ abs($changePercentage) }}% Increase from last period
                  @elseif($changeDirection === 'drop')
                    <i class="fa fa-arrow-down text-red"></i> {{ abs($changePercentage) }}% Drop from last period
                  @endif
                </span>
              @endif
            </div>
          </div>
        </div>
      @endif

      @if(Sentinel::hasAccess('dashboard.total_repayments'))
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon" style="display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-money" style="font-size:28px;"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">
                {{ trans_choice('general.total', 2) }} {{ trans_choice('general.repayment', 2) }}
                @if($isCumulativeUser) <small>(Cumulative)</small> @endif
              </span>

              <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_repayments_amount(),2)}}</span>

              @if(!$isCumulativeUser)
                <div class="progress">
                  <div class="progress-bar {{ $repaymentBarColor }}" style="width: {{ $repaymentBarWidth }}%"></div>
                </div>
                <span class="progress-description">
                  @if($repaymentChangeDirection === 'increase')
                    <i class="fa fa-arrow-up text-green"></i> {{ abs($repaymentChangePercentage) }}% Increase from last period
                  @elseif($repaymentChangeDirection === 'drop')
                    <i class="fa fa-arrow-down text-red"></i> {{ abs($repaymentChangePercentage) }}% Drop from last period
                  @endif
                </span>
              @endif
            </div>
          </div>
        </div>
      @endif

      @if(Sentinel::hasAccess('dashboard.total_outstanding'))
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon" style="display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-money" style="font-size:28px;"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">
                {{ trans_choice('general.total', 2) }} {{ trans_choice('general.outstanding', 2) }}
              </span>

              <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_outstanding_amount(),2)}}</span>

              @if(!$isCumulativeUser)
                <div class="progress">
                  <div class="progress-bar {{ $outstandingBarColor }}" style="width: {{ $outstandingBarWidth }}%"></div>
                </div>
                <span class="progress-description">
                  @if($outstandingChangeDirection === 'increase')
                    <i class="fa fa-arrow-up text-green"></i> {{ abs($outstandingChange) }}% Increase from last period
                  @elseif($outstandingChangeDirection === 'drop')
                    <i class="fa fa-arrow-down text-red"></i> {{ abs($outstandingChange) }}% Drop from last period
                  @endif
                </span>
              @endif
            </div>
          </div>
        </div>
      @endif

      @if(Sentinel::hasAccess('dashboard.amount_in_arrears'))
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon" style="display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-money" style="font-size:28px;"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">
                {{ trans_choice('general.in', 2) }} {{ trans_choice('general.arrears', 2) }}
              </span>

              <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_overdue_amount(),2)}}</span>

              @if(!$isCumulativeUser)
                <div class="progress">
                  <div class="progress-bar {{ $arrearsBarColor }}" style="width: {{ $arrearsBarWidth }}%"></div>
                </div>
                <span class="progress-description">
                  @if($arrearsChangeDirection === 'increase')
                    <i class="fa fa-arrow-up text-white"></i> {{ abs($arrearsChangePercentage) }}% Increase from last period
                  @elseif($arrearsChangeDirection === 'drop')
                    <i class="fa fa-arrow-down text-white"></i> {{ abs($arrearsChangePercentage) }}% Drop from last period
                  @endif
                </span>
              @endif
            </div>
          </div>
        </div>
      @endif

    </div>

    {{-- =========================
        REST OF STAFF DASHBOARD
    ========================= --}}
    <div class="row">
      @if(Sentinel::hasAccess('dashboard.loans_status_overview'))
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{{ trans_choice('general.loan',2) }} {{ trans_choice('general.status',1) }} {{ trans_choice('general.overview',2) }}</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="loans_status_graph" style="height:300px;"></div>
            </div>
          </div>
        </div>
      @endif

      @if(Sentinel::hasAccess('dashboard.clients_overview'))
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{{ trans_choice('general.client',2) }} {{ trans_choice('general.overview',2) }}</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="registered_clients_graph" style="height:300px;"></div>
            </div>
          </div>
        </div>
      @endif

      @if(Sentinel::hasAccess('dashboard.savings_balances_overview'))
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><strong>Top Collectors - Interest Wise</strong></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              @foreach($topCollectors as $collector)
                @php
                  $maxInterest = $topCollectors->max('interest_collected');
                  $percentage = $maxInterest > 0 ? round(($collector->interest_collected / $maxInterest) * 100, 1) : 0;

                  $badges = ['🥇', '🥈', '🥉', '🏅', '🎖️'];
                  $colors = ['progress-bar-yellow', 'progress-bar-blue', 'progress-bar-green', 'progress-bar-aqua', 'progress-bar-red'];

                  $index = $loop->index;
                  $badge = $badges[$index] ?? '🏅';
                  $barColor = $colors[$index] ?? 'progress-bar-default';
                @endphp

                <div class="progress-group">
                  <span class="progress-text">{{ $badge }} {{ $collector->first_name }} {{ $collector->last_name }}</span>
                  <span class="progress-number"><b>{{ number_format($collector->interest_collected, 2) }}</b></span>
                  <div class="progress sm">
                    <div class="progress-bar {{ $barColor }}" style="width: {{ $percentage }}%"></div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif
    </div>

    <div class="row">
      @if(Sentinel::hasAccess('dashboard.collection_statistics'))
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{{ trans_choice('general.collection',1) }} {{ trans_choice('general.statistic',2) }}</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>

            <div class="box-body">
              <div class="row text-center">
                <?php
                $target = 0;
                foreach (\App\Models\LoanRepaymentSchedule::where('year', date("Y"))->where('month', date("m"))->get() as $key) {
                  $target = $target + $key->principal - $key->principal_waived - $key->principal_written_off
                    + $key->interest - $key->interest_waived - $key->interest_written_off
                    + $key->fees - $key->fees_waived - $key->fees_written_off
                    + $key->penalty - $key->penalty_waived - $key->penalty_written_off;
                }

                $paid_this_month = \App\Models\LoanTransaction::where('transaction_type', 'repayment')
                  ->where('reversed', 0)->where('year', date("Y"))->where('month', date("m"))->sum('credit');

                $percent = $target > 0 ? round(($paid_this_month / $target) * 100) : 0;
                ?>

                <div class="col-md-4">
                  <div class="content-group">
                    <h5 class="text-semibold no-margin">
                      {{ number_format(\App\Models\LoanTransaction::where('transaction_type','repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}
                    </h5>
                    <span class="text-muted text-size-small">{{ trans_choice('general.today',1) }}</span>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="content-group">
                    <h5 class="text-semibold no-margin">
                      {{ number_format(\App\Models\LoanTransaction::where('transaction_type','repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }}
                    </h5>
                    <span class="text-muted text-size-small">{{ trans_choice('general.last',1) }} {{ trans_choice('general.week',1) }}</span>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="content-group">
                    <h5 class="text-semibold no-margin">{{ number_format($paid_this_month,2) }}</h5>
                    <span class="text-muted text-size-small">{{ trans_choice('general.this',1) }} {{ trans_choice('general.month',1) }}</span>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="text-center">
                    <h5 class="text-semibold">{{ trans_choice('general.monthly',1) }} {{ trans_choice('general.target',1) }}</h5>
                  </div>
                  <div class="progress" data-toggle="tooltip" title="{{ trans_choice('general.target',1) }} : {{ number_format($target,2) }}">
                    <div class="progress-bar progress-bar-success progress-bar-striped active" style="width: {{ $percent }}%">
                      <span>{{ $percent }}% {{ trans_choice('general.complete',1) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <h3 class="text-center">{{ trans_choice('general.collection',1) }} {{ trans_choice('general.overview',2) }}</h3>
                  <div id="collection_statistics_graph" style="height:300px;"></div>
                </div>
              </div>

            </div>
          </div>
        </div>
      @endif

      <div class="col-md-4">
        <div class="row">
          <?php $fees_penalty = \App\Helpers\GeneralHelper::fees_penalty_earned_paid(); ?>

          @if(Sentinel::hasAccess('dashboard.fees_earned'))
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-thumbs-up"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">{{ trans_choice('general.fee',2) }} {{ trans_choice('general.earned',1) }}</span>
                  <span class="info-box-number">{{ number_format($fees_penalty["fees"],2) }}</span>
                </div>
              </div>
            </div>
          @endif

          @if(Sentinel::hasAccess('dashboard.fees_paid'))
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">{{ trans_choice('general.fee',2) }} {{ trans_choice('general.paid',1) }}</span>
                  <span class="info-box-number">{{ number_format($fees_penalty["fees_paid"],2) }}</span>
                </div>
              </div>
            </div>
          @endif

          @if(Sentinel::hasAccess('dashboard.penalties_earned'))
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-thumbs-up"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">{{ trans_choice('general.penalty',2) }} {{ trans_choice('general.earned',1) }}</span>
                  <span class="info-box-number">{{ number_format($fees_penalty["penalty"],2) }}</span>
                </div>
              </div>
            </div>
          @endif

          @if(Sentinel::hasAccess('dashboard.penalties_paid'))
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">{{ trans_choice('general.penalty',2) }} {{ trans_choice('general.paid',1) }}</span>
                  <span class="info-box-number">{{ number_format($fees_penalty["penalty_paid"],2) }}</span>
                </div>
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>

  @endif {{-- end client vs staff --}}

@endsection

@section('footer-scripts')

  <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/amcharts/serial.js') }}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/amcharts/pie.js') }}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/amcharts/funnel.js') }}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}" type="text/javascript"></script>
  <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}" type="text/javascript"></script>

  @if(!Sentinel::inRole('client'))
    <script>
      // Graphs
      AmCharts.makeChart("registered_clients_graph", {
        "type": "funnel",
        "theme": "light",
        "dataProvider": {!! \App\Helpers\GeneralHelper::client_numbers_graph() !!},
        "balloon": { "fixedPosition": false },
        "valueField": "value",
        "titleField": "title",
        "marginRight": 130,
        "marginLeft": 0,
        "startX": 0,
        "rotate": true,
        "labelPosition": "right",
        "balloonText": "[[title]]: [[value]] [[description]]",
        "export": { "enabled": true, "libs": { "path": "{{ asset('assets/plugins/amcharts/plugins/export/libs') }}/" } }
      });

      AmCharts.makeChart("loans_status_graph", {
        "type": "pie",
        "theme": "light",
        "dataProvider": {!! \App\Helpers\GeneralHelper::loans_status_graph() !!},
        "balloon": { "fixedPosition": false },
        "valueField": "value",
        "titleField": "title",
        "marginRight": 20,
        "marginLeft": 20,
        "radius": 60,
        "startX": 0,
        "fontSize": 10,
        "rotate": true,
        "labelPosition": "right",
        "balloonText": "[[title]]: [[value]] [[description]]",
        "export": { "enabled": true, "libs": { "path": "{{ asset('assets/plugins/amcharts/plugins/export/libs') }}/" } }
      });

      AmCharts.makeChart("collection_statistics_graph", {
        "type": "serial",
        "theme": "light",
        "autoMargins": true,
        "marginLeft": 30,
        "marginRight": 8,
        "marginTop": 10,
        "marginBottom": 26,
        "fontFamily": 'Open Sans',
        "color": '#888',
        "dataProvider": {!! \App\Helpers\GeneralHelper::collection_overview_graph() !!},
        "valueAxes": [{ "axisAlpha": 0 }],
        "startDuration": 1,
        "graphs": [{
          "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
          "bullet": "round",
          "bulletSize": 8,
          "lineColor": "#370fc6",
          "lineThickness": 4,
          "negativeLineColor": "#0dd102",
          "title": "{{ trans_choice('general.actual',1) }}",
          "type": "smoothedLine",
          "valueField": "actual"
        }, {
          "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
          "bullet": "round",
          "bulletSize": 8,
          "lineColor": "#d1655d",
          "lineThickness": 4,
          "negativeLineColor": "#d1cf0d",
          "title": "{{ trans_choice('general.expected',2) }}",
          "type": "smoothedLine",
          "valueField": "expected"
        }],
        "categoryField": "month",
        "categoryAxis": { "gridPosition": "start", "axisAlpha": 0, "tickLength": 0, "labelRotation": 30 },
        "export": { "enabled": true, "libs": { "path": "{{ asset('assets/plugins/amcharts/plugins/export/libs') }}/" } },
        "legend": { "position": "bottom", "marginRight": 100, "autoMargins": false }
      });
    </script>

    @php
      // ✅ must match main rule
      $isCumulativeFooter = Sentinel::inRole('admin') || Sentinel::hasAccess('offices.view');
    @endphp

    {{-- Date range picker only needed when filter exists --}}
    @if(!$isCumulativeFooter)
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
      <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

      <script>
        $(function () {
          $('.daterangepicker-field').daterangepicker({
            opens: 'left',
            autoUpdateInput: true,
            locale: { format: 'YYYY-MM-DD' },
            startDate: '{{ $start_date }}',
            endDate: '{{ $end_date }}',
            ranges: {
              'Today': [moment(), moment()],
              'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
              'This Week': [moment().startOf('week'), moment().endOf('week')],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
              'This Year': [moment().startOf('year'), moment().endOf('year')]
            }
          }, function (start, end) {
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
            $('#date_range').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
          });
        });
      </script>
    @endif
  @endif

@endsection
