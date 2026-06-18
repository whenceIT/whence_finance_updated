@extends('layouts.master')

@section('title', 'Monthly Deposits')

@section('content')
@php
    $ledgerBlocker = \App\Helpers\BlockerHelper::ledger_blocker();
@endphp
<x-kilo-alert/>
<style>
.content {
    min-height: 250px;
}
@media (max-width: 768px) {
    .content {
        min-height: 250px;
        padding: 0px;
        margin-right: 0;
        margin-left: 0;
        padding-left: 0px;
        padding-right: 0px;
    }
}
.deposit-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    transition: 0.2s ease-in-out;
}

.deposit-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.deposit-card.completed {
    border-left: 5px solid #00a65a;
    background: #f6fffa;
}

.deposit-title {
    font-weight: 600;
    margin-bottom: 10px;
}

.deposit-header-box {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.deposit-label {
    font-weight: 600;
    margin-bottom: 5px;
}

.btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

.btn-warning {
    background-color: #f39c12;
    border-color: #e08e0b;
}

.btn-info {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

.deposit-btns {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.deposit-btns .btn {
    margin: 0;
}

.modal-header {
    background: #3c8dbc;
    color: #fff;
}

.modal-header h4 {
    color: #fff;
}

.modal-header .close {
     color: #fff;
 }

.shimmer-container {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.shimmer-row {
    display: flex;
    align-items: center;
    gap: 12px;
    animation: shimmer 1.5s infinite;
}

.shimmer-cell {
    height: 14px;
    background: linear-gradient(90deg, #e0e0e0 25%, #d0d0d0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
}

.shimmer-cell.date { width: 120px; }
.shimmer-cell.amount { width: 100px; }
.shimmer-cell.method { width: 130px; }
.shimmer-cell.reference { width: 160px; }

.locked.deposit-card {
    /* opacity: 0.6;
    pointer-events: none; */
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@keyframes shimmerCard {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
 </style>

<!-- Test with Anchor House First -->
@if( Sentinel::getUser()->role->role_id == 4 && in_array(Sentinel::getUser()->office_id, [6,8])) 
    <x-debt-blocker/>
@endif

<div class="content">
    <!-- Sentinel::getUser()->role->role_id == 4 && in_array(Sentinel::getUser()->office_id, [6,8]) -->
    @if(Sentinel::getUser()->role->role_id == 4 && in_array(Sentinel::getUser()->office_id, [6,8])) 
        @php
            $currentMonthYear = date('F Y', strtotime('now'));
        @endphp

        @include('components.office-block-skip-card', ['officeIdParam' => Sentinel::getUser()->office_id])

    @endif
    <section class="content-header">
        <div class="deposit-header-box">
            <h2 style="margin-top:0; font-weight: 700; font-size: 28px;" id="monthlyDepositsTitle">Monthly Deposits</h2>
            <p class="text-muted" style="margin-bottom:15px;">
                <i class="fa fa-info-circle"></i>
                Enter deposit for the allowed monthly deposits required for your branch. Please ensure the total amount covers the full or atleast K5,000 partial minimum required deposit for the month. Once you click "Save Deposit", it will be recorded and cannot be reversed. If you are unsure about the required amount, click "This Month Deposit" to view your current month's deposit status.
            </p>
            <hr style="border-top:1px solid #eee; margin:20px 0;">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:10px;">
                <div style="max-width:300px;">
                    <label class="deposit-label">Select Month</label>
                    <input type="month" id="monthFilter" class="form-control" max="">
                </div>

                <!-- Add a button here  -->
                <button type="button" id="viewBankDepositsBtn" class="btn btn-primary">
                    <i class="fa fa-money"></i> View Overal History
                </button>
            </div>
        </div>
    </section>
    <!-- Display a width length ads div here, to look like a realistic ad -->
    <!-- <div style="width: 98%; height: 90px; background: url('https://media.giphy.com/media/3oEjI6SIIHBdRxz40KG/200w.gif') center/cover no-repeat; border-radius: 6px; margin: 15px auto; cursor: pointer;" onclick="window.open('https://www.w3schools.com', '_blank');"></div> -->
    <section class="content">
        <div id="depositSteps">
            <div id="depositStepsShimmer" style="display: flex; flex-direction: column; gap: 20px; padding: 20px;">
                <div style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); animation: shimmerCard 1.5s infinite;">
                    <div style="height: 20px; width: 40%; background: linear-gradient(90deg, #e0e0e0 25%, #d0d0d0 50%, #e0e0e0 75%); background-size: 200% 100%; border-radius: 4px; margin-bottom: 15px;"></div>
                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div style="flex: 1; height: 50px; background: linear-gradient(90deg, #e8f4fc 25%, #d0d0d0 50%, #e8f4fc 75%); background-size: 200% 100%; border-radius: 6px;"></div>
                        <div style="flex: 1; height: 50px; background: linear-gradient(90deg, #f0f7f0 25%, #d0d0d0 50%, #f0f7f0 75%); background-size: 200% 100%; border-radius: 6px;"></div>
                        <div style="flex: 1; height: 50px; background: linear-gradient(90deg, #fff3cd 25%, #d0d0d0 50%, #fff3cd 75%); background-size: 200% 100%; border-radius: 6px;"></div>
                    </div>
                    <div style="height: 12px; width: 60%; background: linear-gradient(90deg, #e0e0e0 25%, #d0d0d0 50%, #e0e0e0 75%); background-size: 200% 100%; border-radius: 4px;"></div>
                </div>
                <div style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); animation: shimmerCard 1.5s infinite; animation-delay: 0.2s;"></div>
                <div style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); animation: shimmerCard 1.5s infinite; animation-delay: 0.4s;"></div>
            </div>
        </div>
    </section>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="depositConfirmModal" tabindex="-1" role="dialog" aria-labelledby="depositConfirmLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        <h4 class="modal-title" id="depositConfirmLabel">Confirm Deposit</h4>
      </div>
      <div class="modal-body">
        <p>Before proceeding, please confirm:</p>
        <ol>
          <li>The total amount covers the full required deposit.</li>
          <li>The deposit has gone through and will not be returned.</li>
          <li>Clicking "Confirm" means you accept responsibility for this transaction.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" id="modalConfirmBtn" class="btn btn-success">Confirm</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="viewBankDepositsModal" tabindex="-1" role="dialog" aria-labelledby="viewBankDepositsLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        <h4 class="modal-title" id="viewBankDepositsLabel">Bank Deposits with Records</h4>
      </div>
      <div class="modal-body" id="bankDepositsModalBody" style="max-height:400px; overflow-y:auto;">
        <div id="bankDepositsLoading" style="display:none; text-align:center; padding:20px;">
          <i class="fa fa-spinner fa-spin fa-2x"></i> Loading...
        </div>
        <div id="bankDepositsContent"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="thisMonthDepositModal" tabindex="-1" role="dialog" aria-labelledby="thisMonthDepositLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        <h4 class="modal-title" id="thisMonthDepositLabel">This Month Deposits</h4>
      </div>
      <div class="modal-body">
        <div id="thisMonthDepositContent" style="max-height:400px; overflow-y:auto;">
          <table class="table table-striped table-bordered" style="font-size:12px; margin:0;">
            <thead style="background:#3c8dbc; color:#fff;">
              <tr>
                <th style="padding:8px;">Date</th>
                <th style="padding:8px;">Amount</th>
                <th style="padding:8px;">Method</th>
                <th style="padding:8px;">Reference</th>
              </tr>
            </thead>
            <tbody id="thisMonthDepositTable"></tbody>
            <tfoot>
              <tr style="font-weight:bold; font-size:16px; color:#003366; background:#f8f9fa;">
                <td style="padding:8px; text-align:right;" colspan="2">Received:</td>
                <td style="padding:8px; text-align:right;" id="thisMonthDepositReceived">K0</td>
                <td style="padding:8px;"></td>
              </tr>
              <tr style="font-weight:bold; font-size:14px; color:#003366; background:#e8f4fc;">
                <td style="padding:8px; text-align:right;" colspan="2">Required:</td>
                <td style="padding:8px; text-align:right;" id="thisMonthDepositRequired">K0</td>
                <td style="padding:8px;"></td>
              </tr>
              <tr style="font-weight:bold; font-size:14px; color:#003366; background:#fff3cd;">
                <td style="padding:8px; text-align:right;" colspan="2">Balance:</td>
                <td style="padding:8px; text-align:right;" id="thisMonthDepositBalance">K0</td>
                <td style="padding:8px;"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Deposit History Modal -->
<div class="modal fade" id="depositHistoryModal" tabindex="-1" role="dialog" aria-labelledby="depositHistoryLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        <h4 class="modal-title" id="depositHistoryLabel">Deposit History</h4>
      </div>
      <div class="modal-body">
        <div id="depositHistoryContent" style="max-height:400px; overflow-y:auto;">
          <div id="depositHistoryShimmer" class="shimmer-container" style="display: none;">
            <div class="shimmer-row"><div class="shimmer-cell date"></div><div class="shimmer-cell amount"></div><div class="shimmer-cell method"></div><div class="shimmer-cell reference"></div></div>
            <div class="shimmer-row"><div class="shimmer-cell date"></div><div class="shimmer-cell amount"></div><div class="shimmer-cell method"></div><div class="shimmer-cell reference"></div></div>
            <div class="shimmer-row"><div class="shimmer-cell date"></div><div class="shimmer-cell amount"></div><div class="shimmer-cell method"></div><div class="shimmer-cell reference"></div></div>
            <div class="shimmer-row"><div class="shimmer-cell date"></div><div class="shimmer-cell amount"></div><div class="shimmer-cell method"></div><div class="shimmer-cell reference"></div></div>
            <div class="shimmer-row"><div class="shimmer-cell date"></div><div class="shimmer-cell amount"></div><div class="shimmer-cell method"></div><div class="shimmer-cell reference"></div></div>
          </div>
          <table class="table table-striped table-bordered" style="font-size:12px; margin:0;">
            <thead style="background:#3c8dbc; color:#fff;">
              <tr>
                <th style="padding:8px;">Date</th>
                <th style="padding:8px;">Amount</th>
                <th style="padding:8px;">Method</th>
                <th style="padding:8px;">Reference</th>
              </tr>
            </thead>
            <tbody id="depositHistoryTable"></tbody>
            <tfoot>
              <tr style="font-weight:bold; font-size:16px; color:#003366; background:#f8f9fa;">
                <td style="padding:8px; text-align:right;" colspan="2">Received:</td>
                <td style="padding:8px; text-align:right;" id="depositHistoryReceived">K0</td>
                <td style="padding:8px;"></td>
              </tr>
              <tr style="font-weight:bold; font-size:14px; color:#003366; background:#e8f4fc;">
                <td style="padding:8px; text-align:right;" colspan="2">Required:</td>
                <td style="padding:8px; text-align:right;" id="depositHistoryRequired">K0</td>
                <td style="padding:8px;"></td>
              </tr>
              <tr style="font-weight:bold; font-size:14px; color:#003366; background:#fff3cd;">
                <td style="padding:8px; text-align:right;" colspan="2">Balance:</td>
                <td style="padding:8px; text-align:right;" id="depositHistoryBalance">K0</td>
                <td style="padding:8px;"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function () {

    var branchId = {{ $office_id }};
    var branchName = '{{ $office_name }}';
    var userId = {{ $userId }};
    var userName = '{{ $user_name }}';
    var depositOrder = [];
    var ledgerBlocker = <?php echo json_encode($ledgerBlocker); ?>;
    // var depositApiUrl = 'http://localhost:5000';
    var depositApiUrl = 'https://lms2backend.whencefinancesystem.com';

    const MANDATORY_DEPOSIT_TYPES = [
        { id: 3, name: 'Building & Infrastructure Fee Deposits', monthly_amount: 10000.00 },
        { id: 1, name: 'Administration Department Fee Deposit', monthly_amount: 8000.00 },
        { id: 5, name: 'Statutory Payments Deposits', monthly_amount: 14500.00 }
    ];

    const OPTIONAL_DEPOSITS = [
        { id: 4, name: 'Salaries deposits', deposit_type: 5, bank: 'Access Bank Main / Salary Account' },
        { id: 6, name: 'Savings deposits', deposit_type: 6, bank: 'ABSA Account' },
        { id: 2, name: 'Managers Housing deposit', deposit_type: 7, bank: 'Access Bank Main / Salary Account' }
    ];

    function isOptionalDeposit(name) {
        return OPTIONAL_DEPOSITS.some(d => d.name === name);
    }

    function shouldLockDeposit(name) {
        // && ledgerBlocker
        // if (![32,67].includes(branchId)) {
        //     return false;
        // } else {
        //     return isOptionalDeposit(name);
        // }
        return false
    }

    var currentDepositType = null;
    var currentDepositTypeName = null;
    var currentDepositAmount = null;
    var currentReferenceNumber = null;
    var currentPaymentMethod = null;

    var now = new Date();
    var currentMonth = now.getFullYear() + '-' + 
        String(now.getMonth()+1).padStart(2,'0');
    var maxMonth = now.getFullYear() + '-' + 
        String(now.getMonth()+1).padStart(2,'0');
    
    $('#monthFilter').attr('max', maxMonth);
    $('#monthFilter').val(currentMonth);
    updateMonthTitle();

    function updateMonthTitle() {
        var selectedMonth = $('#monthFilter').val();
        if (selectedMonth) {
            var date = new Date(selectedMonth + '-01');
            var monthName = date.toLocaleString('default', { month: 'long' });
            var year = date.getFullYear();
            $('#monthlyDepositsTitle').text(monthName + ' ' + year + ' Deposits');
        }
    }

    function today() {
        var selectedMonth = $('#monthFilter').val();
        var day = new Date().getDate();
        return selectedMonth + '-' + String(day).padStart(2, '0');
    }

function lockAll() {
        // $('.deposit-item').each(function () {
        //     if (!$(this).hasClass('locked')) {
        //         $(this).find('input,button,select').prop('disabled', true);
        //         $(this).css('opacity', 0.5);
        //     }
        // });
    }

    function unlockAll() {
        $('.deposit-item').each(function () {
            if (!$(this).hasClass('locked')) {
                $(this).find('input,button,select').prop('disabled', false);
                $(this).css('opacity', 1);
            }
        });
    }

    function unlock(id) {
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .find('input,button,select').prop('disabled', false)
            .closest('.deposit-item').css('opacity', 1);
    }

    function markCompleted(id) {
        let box = $('.deposit-item[data-deposit-id="'+id+'"]');

        box.addClass('completed')
           .css('opacity', 1);

        // Re-enable fields so user can add more deposits
        box.find('input.amount, input.reference, select.payment-method, .complete-btn')
           .prop('disabled', false);
    }


        
    function loadDepositCardData(depositId, depositName, officeId, container, method) {
    var selectedMonth = $('#monthFilter').val();
    $.ajax({
        url: `${depositApiUrl}/deposit-types/${depositId}/this-month?office_id=${officeId}&month=${selectedMonth}`,
        method: 'GET',
        success: function(res) {
            var deposits = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
            var monthlyRequired = parseFloat(res.monthly_required || res.total_required || (deposits.length > 0 ? parseFloat(deposits[0].monthly_amount || 0) : 0));
            var total = 0;
            
            var monthly_amt = deposits[0]?.monthly_amount || 0;
            var d_type = deposits[0]?.deposit_type || null;
            deposits.forEach(function(d) {
                total += parseFloat(d.amount) || 0;
            });
            var balance = monthlyRequired - total;
            
            var statusText = 'Not Paid';
            var statusColor = '#e74c3c';
            if (total > 0 && monthlyRequired > total) {
                statusText = 'Partially Paid';
                statusColor = '#f39c12';
            } else if (total > 0 && monthlyRequired <= total) {
                statusText = 'Fully Paid';
                statusColor = '#27ae60';
            }
            
            var $card = $(`
                <div class="deposit-item deposit-card ${shouldLockDeposit(depositName) ? 'locked ' : ''}" data-deposit-id="${depositId}" data-office-id="${officeId}" data-method="${method || ''}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                        <h4 class="deposit-title" style="margin:0;">${depositName}</h4>
                        <span style="background:${statusColor};color:#fff;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;">${statusText}</span>
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                        ${shouldLockDeposit(depositName) ? '<span style="background: #6c757d; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px;">Locked</span>' : ''}
                    </div>
                    <div style="display: flex; flex-direction: row; gap: 10px; margin: 15px 0;">
                        <div style="flex: 1; background: #e8f4fc; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Monthly Fee</small>
                            <div style="color: #003366; font-weight: 700; font-size: 16px; margin-top: 4px;">K${monthly_amt || 0}</div>
                        </div>
                        <div style="flex: 1; background: #f0f7f0; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Current Paid</small>
                            <div style="color: #006600; font-weight: 700; font-size: 16px; margin-top: 4px;">K${total.toLocaleString() || 0}</div>
                        </div>
                        <div style="flex: 1; background: #fff3cd; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Balance</small>
                            <div style="color: #856404; font-weight: 700; font-size: 16px; margin-top: 4px;">K${monthlyRequired === 0 && balance < 0 ? (-1 * balance).toLocaleString() : balance.toLocaleString() || 0}</div>
                        </div>
                    </div>
                    <div class="deposit-btns">
                      <button class="this-month-btn btn btn-success btn-sm">This Month Deposit</button>
                      <button class="deposit-history-btn btn btn-info btn-sm">Check Deposit History</button>
                    </div>
                    <label class="deposit-label">Payment Method</label>
                    <select class="form-control payment-method">
                        <option value="">Select Method</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="zanaco_express">Zanaco Express</option>
                        <option value="mtn">MTN MoMo</option>
                        <option value="zanaco_cash">Zanaco Cash Deposit</option>
                        <option value="access">Access</option>
                        <option value="absa">Absa</option>
                        <option value="withinhere">WithinHere</option>
                    </select>
                    <br>
                    <small class="text-muted format-hint">Enter Payment Reference Number</small>
                    <input type="text" class="form-control reference" placeholder="Enter reference number" required>
                    <br>
                    <input type="number" class="form-control amount" placeholder="Enter amount to add" min="0.01" step="0.01" required>
                    <br>
                    <button class="btn btn-primary complete-btn" style="min-width: 100px;">
                        <span class="btn-text">Save Deposit</span>
                        <span class="btn-loader" style="display: none; margin-left: 8px;">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            `);
            container.append($card);
            // if ($card.hasClass('locked')) {
            //     $card.find('button, select, input').prop('disabled', true);
            //     $card.css('opacity', '0.6');
            // }
            if (method) {
                var $methodSelect = $card.find('.payment-method');
                $methodSelect.val(method);
                $methodSelect.prop('disabled', true);
                var $referenceInput = $card.find('.reference');
                var placeholder = 'Enter reference number';
                switch (method) {
                    case 'airtel':
                        placeholder = 'MP260223.0953.J76581';
                        break;
                    case 'zanaco_express':
                        placeholder = '002504072516';
                        break;
                    case 'mtn':
                        placeholder = '8704564481';
                        break;
                    case 'zanaco_cash':
                        placeholder = '0502605703255600';
                        break;
                    case 'access':
                        placeholder = 'FJB2606341708208';
                        break;
                    case 'absa':
                        placeholder = 'FJB2606341708208';
                        break;
                    case 'withinhere':
                        placeholder = '1777356230718931';
                        break;
                }
                $referenceInput.attr('placeholder', placeholder);
            }
        },
        error: function(xhr, status, error) {
            container.append(`
                <div class="deposit-item deposit-card" data-deposit-id="${depositId}" data-office-id="${officeId}">
                    <h4 class="deposit-title">${depositName}</h4>
                    <div style="display: flex; flex-direction: row; gap: 10px; margin: 15px 0;">
                        <div style="flex: 1; background: #e8f4fc; border-left: 4px solid #3c8dbc; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Monthly Fee</small>
                            <div style="color: #003366; font-weight: 700; font-size: 16px; margin-top: 4px;">K0</div>
                        </div>
                        <div style="flex: 1; background: #f0f7f0; border-left: 4px solid #27ae60; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Paid</small>
                            <div style="color: #006600; font-weight: 700; font-size: 16px; margin-top: 4px;">K0</div>
                        </div>
                        <div style="flex: 1; background: #fff3cd; border-left: 4px solid #f39c12; border-radius: 6px; padding: 12px 15px;">
                            <small style="color: #343a40; font-weight: 600; font-size: 12px;">Balance</small>
                            <div style="color: #856404; font-weight: 700; font-size: 16px; margin-top: 4px;">K0</div>
                        </div>
                    </div>
                    <div class="deposit-btns">
                      <button class="this-month-btn btn btn-primary btn-sm">This Month Deposit</button>
                      <button class="deposit-history-btn btn btn-info btn-sm">Check Deposit History</button>
                    </div>
                </div>
            `);
        }
    });
}

/* ---------- LOAD DEPOSIT TYPES ---------- */
    $('#depositStepsShimmer').show();
    $.get(`${depositApiUrl}/deposit-types`, function (res) {
        var deposits = res.data || res;
        deposits.sort(function(a, b) {
            return (a.sort_order || 0) - (b.sort_order || 0);
        });
        var container = $('#depositSteps').empty();

        deposits.forEach(function (d) {
            // depositOrder.push(d.id);
            loadDepositCardData(d.id, d.name, branchId, container, d.method);
        });
        $('#depositStepsShimmer').hide();
    }).fail(function() {
        $('#depositStepsShimmer').hide();
    });

    // // This Month button handler
    // $(document).on('click', '.this-month-btn', function() {
    //     var $card = $(this).closest('.deposit-card');
    //     var depositId = $card.data('deposit-id');
    //     var officeId = $card.data('office-id');
        
    //     var selectedMonth = $('#monthFilter').val();
    //     $.get(`${depositApiUrl}/deposit-types/${depositId}/this-month?office_id=${officeId}&month=${selectedMonth}`, function(res) {
    //         var deposits = res.data || [];
    //         var monthlyRequired = res.monthly_required || (deposits.length > 0 ? parseFloat(deposits[0].monthly_amount || 0) : 0);
    //         var $tbody = $('#thisMonthDepositTable').empty();
    //         var total = 0;
    //         var monthly_amt = deposits[0]?.monthly_amount || 0;
    //         var d_type = deposits[0]?.deposit_type || null;
    //         deposits.forEach(function(d) {
    //             var amount = parseFloat(d.amount) || 0;
    //             total += amount;
    //             var dateVal = formatDate(d.date || d.created_at);
    //             $tbody.append('<tr>' +
    //                 '<td style="padding:6px;">' + dateVal + '</td>' +
    //                 '<td style="padding:6px; text-align:right;">' + amount.toLocaleString() + '</td>' +
    //                 '<td style="padding:6px;">' + (d.bank_deposit_log_method || '-') + '</td>' +
    //                 '<td style="padding:6px; font-family:monospace; font-size:11px;">' + (d.bank_deposit_log_reference_number || '-') + '</td>' +
    //                 '</tr>');
    //         });
    //         var required = monthlyRequired * 1;
    //         var balance = required - total;
    //         $('#thisMonthDepositReceived').text('K' + total.toLocaleString());
    //         $('#thisMonthDepositRequired').text('K' + required.toLocaleString());
    //         $('#thisMonthDepositBalance').text('K' + balance.toLocaleString());
    //         $card.find('.existing-amount').text('Current Amount: K' + total.toLocaleString());
    //         $card.find('.monthly-required').text('Monthly Required: K' + required.toLocaleString());
    //         $card.find('.current-balance').text('Current Month Balance: K' + balance.toLocaleString());
    //         $('#thisMonthDepositModal .modal-title').text('This Month Deposits: ' + ($card.find('.deposit-title').text() || '-'));
    //         $('#thisMonthDepositModal').modal('show');
    //     }).fail(function(xhr, status, error) {
    //         console.error('Failed to fetch this month deposits:', error);
    //     });
    // });

    // Deposit History button handler
    $(document).on('click', '.deposit-history-btn', function() {
        var $card = $(this).closest('.deposit-card');
        var depositId = $card.data('deposit-id');
        var officeId = $card.data('office-id');
        
        $('#depositHistoryShimmer').show();
        $('#depositHistoryTable').closest('table').hide();
        
        var selectedMonth = $('#monthFilter').val();
        $.get(`${depositApiUrl}/deposit-types/${depositId}/history?office_id=${officeId}&month=${selectedMonth}`, function(res) {
            var deposits = res.data || [];
            var monthlyRequired = deposits.length > 0 ? parseFloat(deposits[0].monthly_amount || 0) : 0;
            var $tbody = $('#depositHistoryTable').empty();
            var total = 0;
            var monthly_amt = deposits[0]?.monthly_amount || 0;
            var d_type = deposits[0]?.deposit_type || null;
            deposits.forEach(function(d) {
                var amount = parseFloat(d.amount) || 0;
                total += amount;
                var dateVal = formatDate(d.date || d.created_at);
                $tbody.append('<tr>' +
                    '<td style="padding:6px;">' + dateVal + '</td>' +
                    '<td style="padding:6px; text-align:right;">' + amount.toLocaleString() + '</td>' +
                    '<td style="padding:6px;">' + (d.bank_deposit_log_method || '-') + '</td>' +
                    '<td style="padding:6px; font-family:monospace; font-size:11px;">' + (d.bank_deposit_log_reference_number || '-') + '</td>' +
                    '</tr>');
            });
            var required = monthlyRequired * 12;//for current year
            var balance = required - total;
            $('#depositHistoryReceived').text('K' + total.toLocaleString());
            $('#depositHistoryRequired').text('K' + required.toLocaleString());
            $('#depositHistoryBalance').text('K' + balance.toLocaleString());
            $('#depositHistoryModal .modal-title').text('Deposit History: ' + (deposits.length > 0 ? deposits[0].deposit_type_name || '-' : '-'));
            $('#depositHistoryModal').modal('show');
            $('#depositHistoryShimmer').hide();
            $('#depositHistoryTable').closest('table').show();
        }).fail(function() {
            $('#depositHistoryShimmer').hide();
            $('#depositHistoryTable').closest('table').show();
        });
    });

    function formatDate(dateStr) {
        if (!dateStr || dateStr === '-') return '-';
        var date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }


    /* ---------- CHECK COMPLETED ---------- */
    function checkCompletedDeposits() {

        var selectedMonth = $('#monthFilter').val();
        $('#depositStepsShimmer').show();
        $.get(`${depositApiUrl}/check-deposits-report`, {
            branch: branchId,
            date: selectedMonth
        }, function (response) {

            console.log('Deposit Check Response:', response);
            unlockAll();
            $('#depositSteps').empty();
            // depositOrder = [];
            $.get(`${depositApiUrl}/deposit-types`, function (res) {
                var deposits = res.data || res;
                deposits.sort(function(a, b) {
                    return (a.sort_order || 0) - (b.sort_order || 0);
                });
                deposits.forEach(function (d) {
                    // depositOrder.push(d.id);
                    loadDepositCardData(d.id, d.name, branchId, $('#depositSteps'), d.method);
                });
                $('#depositStepsShimmer').hide();
            }).fail(function() {
                $('#depositStepsShimmer').hide();
            });
        }).fail(function() {
            $('#depositStepsShimmer').hide();
        });
    }

    $('#monthFilter').change(function(){
        updateMonthTitle();
        checkCompletedDeposits();
    });

    /* ---------- PAYMENT METHOD FORMAT HINT ---------- */
    $(document).on('change', '.payment-method', function () {

        let box = $(this).closest('.deposit-item');
        let hint = box.find('.format-hint');
        let referenceInput = box.find('.reference');

        referenceInput.val('');

        switch ($(this).val()) {
            case 'airtel':
                hint.text('Format: MP260223.0953.J76581');
                referenceInput.attr('placeholder', 'MP260223.0953.J76581');
                break;

            case 'zanaco_express':
                hint.text('Format: 12 digit number (002504072516)');
                referenceInput.attr('placeholder', '002504072516');
                break;

            case 'mtn':
                hint.text('Format: 10 digit number (8704564481)');
                referenceInput.attr('placeholder', '8704564481');
                break;

            case 'zanaco_cash':
                hint.text('Format: 16 digit number (0502605703255600)');
                referenceInput.attr('placeholder', '0502605703255600');
                break;

            case 'access':
                hint.text('Format: FJB2606341708208');
                referenceInput.attr('placeholder', 'FJB2606341708208');
                break;

            case 'absa':
                hint.text('Format: FJB2606341708208');
                referenceInput.attr('placeholder', 'FJB2606341708208');
                break;
            case 'withinhere':
                hint.text('Format: 1777356230718931');
                referenceInput.attr('placeholder', '1777356230718931');
                break;

            default:
                hint.text('Enter Payment Reference Number');
        }
    });

/* ---------- SAVE DEPOSIT ---------- */
    $(document).on('click', '.complete-btn', function () {
        let $btn = $(this);
        let box = $btn.closest('.deposit-item');
        currentDepositType = box.data('deposit-id');
        currentDepositTypeName = box.find('.deposit-title').text();

        let raw = box.find('.amount').val();
        currentDepositAmount = parseFloat(raw);
        currentReferenceNumber = box.find('.reference').val().trim();
        let paymentMethod = box.find('.payment-method').val();
        currentPaymentMethod = paymentMethod;

        if (!paymentMethod) {
            KiloAlert.warning('Please select a payment method.');
            return;
        }

        if (!currentReferenceNumber) {
            KiloAlert.warning('Please enter a payment reference number.');
            return;
        }

        if (isNaN(currentDepositAmount) || currentDepositAmount <= 0) {
            KiloAlert.warning('Enter a valid amount to add');
            return;
        }

        // Tempro bypass
        let valid = true;

        switch (paymentMethod) {
            case 'airtel':
                valid = /^[A-Za-z]{2}\d{6}\.\d{4}\.[A-Za-z]\d{5}$/.test(currentReferenceNumber);
                break;
            case 'zanaco_express':
                valid = /^\d{12}$/.test(currentReferenceNumber);
                break;
            case 'mtn':
                valid = /^\d{10}$/.test(currentReferenceNumber);
                break;
            case 'zanaco_cash':
                valid = /^\d{16}$/.test(currentReferenceNumber);
                break;
            case 'access':
                valid = /^[A-Za-z]{3}\d{13}$/.test(currentReferenceNumber);
                break;
            case 'absa':
                valid = /^[A-Za-z]{3}\d{10}$/.test(currentReferenceNumber);
                break;
            case 'withinhere':
                valid = /^\d+$/.test(currentReferenceNumber);
                break;
        }

        if (!valid) {
            KiloAlert.warning('Invalid reference format for selected payment method.');
            return;
        }

        $btn.prop('disabled', true);
        $btn.find('.btn-text').hide();
        $btn.find('.btn-loader').show();
        $('#depositConfirmModal').modal('show');
        
        $('#modalConfirmBtn').off('click').on('click', function() {
            $(this).prop('disabled', true);
            
            $.ajax({
                url: `${depositApiUrl}/create-deposit`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    deposit_type: currentDepositType,
                    office: branchId,
                    amount: currentDepositAmount,
                    reference_number: currentReferenceNumber,
                    deposit_method: currentPaymentMethod,
                    user_id: userId,
                    date: today()
                }),
                success: function (res) {
                    KiloAlert.success(res.message || 'Deposit saved successfully');

                    // Send notification to WebSocket server
                    fetch('https://notifications.whencefinancesystem.com/emit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            event: 'deposit.created',
                            data: {
                                created_by: '{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}',
                                office_id: {{ Sentinel::getUser()->office->name ?? 'null' }},
                                amount: currentDepositAmount,
                                type: 'New Deposit requesting approval',
                                deposit: {
                                    type: currentDepositTypeName,
                                    reference: currentReferenceNumber,
                                    method: currentPaymentMethod,
                                    date: today()
                                }
                            }
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Notification sent successfully:', data);
                    })
                    .catch(error => {
                        alert('Error sending notification:', error);
                    });
                },
                error: function(res) {
                    KiloAlert.error('Failed to save deposit. Please try again. ' + (res.responseJSON?.error || ''));
                    $btn.prop('disabled', false);
                    $btn.find('.btn-text').show();
                    $btn.find('.btn-loader').hide();
                    modalConfirmBtn.prop('disabled', false);
                }
            });
            $('#depositConfirmModal').modal('hide');

            // wait 4 second to reload page
            setTimeout(function() { location.reload(); }, 5000);
        });
    });
});

function loadOfficesSettings() {
    $.get('/settings/platform/offices-settings', function(data) {
        var officeId = new URLSearchParams(window.location.search).get('office_id');
        if (officeId && data && data.OfficeName) {
            renderExemptions(data);
            return;
        }
        var tableHtml = '';
        data.forEach(function(o) {
            tableHtml += '<tr>' +
                '<td>' + o.name + '</td>' +
                '<td>' + o.code + '</td>' +
                '<td>' + (o.admin ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.building ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.statutory ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.set_up_debt ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '</tr>';
        });
        $('#offices-settings-table').html(tableHtml);
    });
}

function renderExemptions(data) {
    var exemptions = [
        {
            title: 'Administration Department Fee Deposit',
            description: data.admin ? 'Obligated to make payment to Administration Department fee deposit' : 'Excluded from making payment to Administration Department fee deposit',
            enabled: data.admin,
            color: data.admin ? '#28a745' : '#dc3545'
        },
        {
            title: 'Building & Infrastructure Fee Deposit',
            description: data.building ? 'Obligated to make payment to Building & Infrastructure fee deposits' : 'Excluded from making payment to Building & Infrastructure fee deposits',
            enabled: data.building,
            color: data.building ? '#28a745' : '#dc3545'
        },
        {
            title: 'Statutory Payments Deposit',
            description: data.statutory ? 'Obligated to make payment to Statutory payments deposits' : 'Excluded from making payment to Statutory payments deposits',
            enabled: data.statutory,
            color: data.statutory ? '#28a745' : '#dc3545'
        },
        {
            title: 'Setup Cost Debt Payment',
            description: data.set_up_debt ? 'Obligated to make payment towards K5,000 minimum debt for setup cost' : 'Excluded from making payment towards setup cost debt',
            enabled: data.set_up_debt,
            color: data.set_up_debt ? '#28a745' : '#dc3545'
        }
    ];
    
    var html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
    exemptions.forEach(function(e) {
        html += '<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 8px; background: ' + e.color + '15; border-left: 4px solid ' + e.color + ';">' +
            '<div style="flex: 1;">' +
            '<div style="font-weight: 600; color: #343a40; font-size: 14px; margin-bottom: 4px;">' + e.title + '</div>' +
            '<div style="color: #6c757d; font-size: 13px;">' + e.description + '</div>' +
            '</div>' +
            '<div style="text-align: center; min-width: 100px;">' +
            '<span style="background: ' + e.color + '; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">' +
            (e.enabled ? 'ENABLED' : 'DISABLED') +
            '</span>' +
            '</div>' +
            '</div>';
    });
    html += '</div>';
    $('#office-exemptions-body').html(html).show();
}

var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}

$('#viewBankDepositsBtn').on('click', function() {
    $('#bankDepositsModalBody').html('<div style="text-align:center; padding:20px;">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $('#viewBankDepositsModal').modal('show');

    var officeId = {{ $office_id }};
    $.ajax({
        url: '/api/bank-deposits-with-records',
        method: 'GET',
        data: { office_id: officeId },
        success: function(response) {
            var deposits = response.data || response;
            var grouped = {};
            
            deposits.forEach(function(d) {
                var date = d.date;
                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(d);
            });

            var html = '<style>' +
                    '.deposit-month { margin-bottom: 25px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }' +
                    '.deposit-month-header { background: #3c8dbc; color: white; padding: 12px 15px; font-weight: bold; font-size: 16px; }' +
                    '.deposit-group-body { padding: 0; }' +
                    '.deposit-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-bottom: 1px solid #eee; }' +
                    '.deposit-row:last-child { border-bottom: none; }' +
                    '.deposit-amount { font-weight: 600; color: #27ae60; }' +
                    '.deposit-method { color: #666; font-size: 13px; }' +
                    '.deposit-reference { color: #999; font-size: 12px; }' +
                    '</style>';

            var groupedByMonth = {};
            Object.keys(grouped).forEach(function(date) {
                var monthKey = date.substring(0, 7);
                if (!groupedByMonth[monthKey]) {
                    groupedByMonth[monthKey] = [];
                }
                groupedByMonth[monthKey].push({ date: date, deposits: grouped[date] });
            });

            Object.keys(groupedByMonth).forEach(function(monthKey) {
                var monthDate = new Date(monthKey + '-01');
                var monthName = monthDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                html += '<div class="deposit-month">' +
                    '<div class="deposit-month-header">' + monthName + '</div>' +
                    '<div class="deposit-group-body">';
                
                groupedByMonth[monthKey].forEach(function(dayGroup) {
                    var dayName = new Date(dayGroup.date).toLocaleDateString('en-US', { weekday: 'short' });
                    html += '<div style="border-bottom: 1px solid #f0f0f0; padding: 8px 15px; background: #fafafa;">' +
                        dayName + ', ' + dayGroup.date + '</div>';
                    
                    dayGroup.deposits.forEach(function(d) {
                        html += '<div class="deposit-row">' +
                            '<div style="flex: 1;">' +
                            '<div style="font-weight: 600;">K' + (parseFloat(d.amount).toLocaleString() || 0) + '</div>' +
                            '<div class="deposit-method">' + (d.deposit_method || '-') + '</div>' +
                            '<div class="deposit-reference">Ref: ' + (d.reference_number || '-') + '</div>' +
                            '</div>' +
                            '<div style="text-align: right; color: #666; font-size: 12px;">' +
                            'Type: ' + (d.deposit_type_name || d.deposit_type) +
                            '</div></div>';
                    });
                });
                
                html += '</div></div>';
            });

            if (Object.keys(groupedByMonth).length === 0) {
                html = '<div style="text-align:center; padding:40px; color:#666;">No bank deposits found with records.</div>';
            }

            $('#bankDepositsModalBody').html(html);
        },
        error: function() {
            $('#bankDepositsModalBody').html('<div style="text-align:center; padding:40px; color:#e74c3c;">Failed to load data.</div>');
        }
    });
});
</script>
@endsection



