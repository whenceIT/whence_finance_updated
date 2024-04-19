<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//bind routes
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanApprovedEmail;
use App\Http\Controllers\MailController;

Auth::routes();
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');

Route::model('client', 'App\Models\Client');
Route::model('user', 'App\Models\User');
Route::model('permission', 'App\Models\Permission');
Route::model('office', 'App\Models\Office');
Route::model('client', 'App\Models\Client');
Route::model('client_identification_type', 'App\Models\ClientIdentificationType');
Route::model('client_profession', 'App\Models\ClientProfession');
Route::model('client_relationship', 'App\Models\ClientRelationship');
Route::model('next_of_kin', 'App\Models\ClientNextOfKin');
Route::model('note', 'App\Models\Note');
Route::model('group', 'App\Models\Group');
Route::model('gl_account', 'App\Models\GlAccount');
Route::model('gl_journal_entry', 'App\Models\GlJournalEntry');
Route::model('fund', 'App\Models\Fund');
Route::model('payment_type', 'App\Models\PaymentType');
Route::model('currency', 'App\Models\Currency');
Route::model('charge', 'App\Models\Charge');
Route::model('loan_product', 'App\Models\LoanProduct');
Route::model('loan', 'App\Models\Loan');
Route::model('loan_purpose', 'App\Models\LoanPurpose');
Route::model('collateral', 'App\Models\Collateral');
Route::model('collateral_type', 'App\Models\CollateralType');
Route::model('guarantor', 'App\Models\Guarantor');
Route::model('sms_gateway', 'App\Models\SmsGateway');
Route::model('loan_transaction', 'App\Models\LoanTransaction');
Route::model('loan_provisioning', 'App\Models\LoanProvisioningCriteria');
Route::model('savings', 'App\Models\Savings');
Route::model('savings_product', 'App\Models\SavingsProduct');
Route::model('savings_transaction', 'App\Models\SavingsTransaction');
Route::model('report_scheduler', 'App\Models\ReportScheduler');
Route::model('communication_campaign', 'App\Models\CommunicationCampaign');
Route::model('custom_field', 'App\Models\CustomField');
Route::model('asset', 'App\Models\Asset');
Route::model('asset_type', 'App\Models\AssetType');
Route::model('expense', 'App\Models\Expense');
Route::model('expense_type', 'App\Models\ExpenseType');
Route::model('other_income', 'App\Models\OtherIncome');
Route::model('other_income_type', 'App\Models\OtherIncomeType');
Route::model('expense_budget', 'App\Models\ExpenseBudget');
Route::model('payroll', 'App\Models\Payroll');
Route::model('loan_application', 'App\Models\LoanApplication');


//development routes
//route for audit trail
Route::get('migrate_seed', function () {
    \Illuminate\Support\Facades\Artisan::call("migrate");
    \Illuminate\Support\Facades\Artisan::call("db:seed");
    return redirect('/');
});
Route::get('update', function () {
    \Illuminate\Support\Facades\Artisan::call("migrate");
    return redirect('/dashboard');
});
Route::get('clear_cache', function () {
    \Illuminate\Support\Facades\Artisan::call("cache:clear");
    \Illuminate\Support\Facades\Artisan::call("view:clear");
    return redirect('/');
});

Route::get('/', [HomeController::class, 'index']);
Route::get('login', 'HomeController@login');
Route::get('create_account', 'HomeController@create_account');
Route::get('signup', 'HomeController@signup');
Route::post('create_client_user','HomeController@create_client_account');
Route::post('login', 'HomeController@process_login');
Route::post('register', 'HomeController@register');
//Route::post('create_Profile', 'HomeController@create_Profile');
Route::post('createProfile', 'HomeController@createProfile');
Route::get('logout', 'HomeController@logout');
Route::get('password_reset', 'HomeController@password_reset');
Route::post('password_reset', 'HomeController@process_password_reset');
Route::get('confirm_password_reset/{id}/{code}', 'HomeController@confirm_password_reset');
Route::post('confirm_password_reset/{id}/{code}', 'HomeController@process_confirm_password_reset');
Route::get('dashboard', [UserController::class, 'dashboard']);
Route::get('cron', 'CronController@index');
Route::get('test', 'TestController@index');

//route for users
Route::group(['prefix' => 'user'], function () {
    Route::get('data', 'UserController@index');
    Route::get('{id}/branch_page', 'UserController@branch_page');
    Route::get('{id}/province_page', 'UserController@province_page');
    Route::get('client_users/data', 'UserController@client_users_index');
    Route::get('daily_figures','UserController@daily_figures');
    Route::get('create', 'UserController@create');
    Route::post('store', 'UserController@store');
 //   Route::post('create_client_user','UserController@create_client_account');
    Route::get('{user}/edit', 'UserController@edit');
    Route::get('{user}/show', 'UserController@show');
    Route::post('{id}/update', 'UserController@update');
    Route::get('{id}/delete', 'UserController@delete');
    Route::get('edit_profile', 'UserController@edit_profile');
    Route::post('update_my_details','UserController@update_my_details');
    Route::get('my_details','UserController@my_details');
    Route::get('edit_my_details','UserController@edit_my_details');
    Route::get('profile', 'UserController@profile');
    Route::post('update_profile', 'UserController@profile_update');
    Route::get('{user}/staff_info','UserController@user_info');
    Route::any('{user}/{collection_type}/collections_stats','UserController@collections_stats');
    Route::any('{user}/{given_out_type}/given_out_stats','UserController@given_out_stats');
    Route::any('{user}/{uncollected_type}/uncollected_stats','UserController@uncollected_stats');
    Route::any('/leaderboard','UserController@leaderboard');
    //manage permissions
    Route::get('permission/data', 'UserController@indexPermission');
    Route::get('permission/create', 'UserController@createPermission');
    Route::post('permission/store', 'UserController@storePermission');
    Route::get('permission/{permission}/edit', 'UserController@editPermission');
    Route::post('permission/{id}/update', 'UserController@updatePermission');
    Route::get('permission/{id}/delete', 'UserController@deletePermission');
    //manage roles
    Route::get('role/data', 'UserController@indexRole');
    Route::get('role/create', 'UserController@createRole');
    Route::post('role/store', 'UserController@storeRole');
    Route::get('role/{id}/edit', 'UserController@editRole');
    Route::post('role/{id}/update', 'UserController@updateRole');
    Route::get('role/{id}/delete', 'UserController@deleteRole');

    //LC Cycle information
    Route::get('cycle','UserController@cycle');
    Route::post('addcycle','UserController@addCycle');
});
//route for offices
Route::group(['prefix' => 'office'], function () {
    Route::get('data', 'OfficeController@index');
    Route::get('create', 'OfficeController@create');
    Route::post('store', 'OfficeController@store');
    Route::get('{office}/edit', 'OfficeController@edit');
    Route::get('{office}/show', 'OfficeController@show');
    Route::post('{id}/update', 'OfficeController@update');
    Route::get('{id}/delete', 'OfficeController@delete');
});
//route for clients
Route::group(['prefix' => 'client'], function () {
    Route::get('data', 'ClientController@index')->name('client.data');
    Route::get('my_clients', 'ClientController@my_index');
    Route::get('branch_clients', 'ClientController@branch_index');
    Route::get('declined', 'ClientController@declined');
    Route::get('pending_approval', 'ClientController@pending_approval');
    Route::get('managers_pending_approval', 'ClientController@managers_pending_approval');
    Route::get('closed', 'ClientController@closed');
    Route::get('clients_inactive', 'ClientController@clients_inactive');
    Route::get('clients_blacklisted', 'ClientController@clients_blacklisted');
    Route::get('create', 'ClientController@create');
    Route::post('store', 'ClientController@store');
    Route::get('create_blacklist', 'ClientController@create_blacklist');
    Route::post('store_blacklist', 'ClientController@store_blacklist');
    Route::get('{client}/remove_blacklist', 'ClientController@remove_blacklist');
    Route::post('{client}/store_remove_blacklist', 'ClientController@store_remove_blacklist');
    Route::get('{client}/edit', 'ClientController@edit');
    Route::get('{client}/show', 'ClientController@show');
    Route::post('{id}/update', 'ClientController@update');
    Route::post('{id}/picture', 'ClientController@picture');
    Route::post('{id}/approve', 'ClientController@approve');
    Route::post('{id}/decline', 'ClientController@decline');
    Route::get('{id}/delete', 'ClientController@delete');
    Route::post('{id}/close', 'ClientController@close');
    Route::post('{id}/inactive', 'ClientController@inactive');
    Route::post('{id}/transfer', 'ClientController@transfer');
    Route::get('{id}/active', 'ClientController@active');
    Route::get('{id}/account', 'ClientController@account');
    //identification
    Route::post('{id}/identification/store', 'ClientController@store_client_identification');
    Route::get('identification/{id}/delete', 'ClientController@delete_client_identification');
    //document
    Route::post('{id}/document/store', 'ClientController@store_client_document');
    Route::get('document/{id}/delete', 'ClientController@delete_client_document');
    //next_of_kin
    Route::post('{id}/next_of_kin/store', 'ClientController@store_next_of_kin');
    Route::get('next_of_kin/{id}/delete', 'ClientController@delete_next_of_kin');
    Route::get('next_of_kin/{next_of_kin}/show', 'ClientController@show_next_of_kin');
    Route::get('next_of_kin/{next_of_kin}/edit', 'ClientController@edit_next_of_kin');
    Route::post('next_of_kin/{id}/update', 'ClientController@update_next_of_kin');
    //notes
    Route::post('{id}/note/store', 'ClientController@store_note');
    Route::get('note/{id}/delete', 'ClientController@delete_note');
    Route::get('note/{note}/show', 'ClientController@show_note');
    Route::get('note/{note}/edit', 'ClientController@edit_note');
    Route::post('note/{id}/update', 'ClientController@update_note');
    //client users
    Route::post('{client}/add_user', 'ClientController@add_user');
    Route::get('{id}/delete_user', 'ClientController@delete_user');

});
//route for client identification types
Route::group(['prefix' => 'client_identification_type'], function () {
    Route::get('data', 'ClientIdentificationTypeController@index');
    Route::get('create', 'ClientIdentificationTypeController@create');
    Route::post('store', 'ClientIdentificationTypeController@store');
    Route::get('{client_identification_type}/edit', 'ClientIdentificationTypeController@edit');
    Route::get('{client_identification_type}/show', 'ClientIdentificationTypeController@show');
    Route::post('{id}/update', 'ClientIdentificationTypeController@update');
    Route::get('{id}/delete', 'ClientIdentificationTypeController@delete');
});
//route for client relationship
Route::group(['prefix' => 'client_relationship'], function () {
    Route::get('data', 'ClientRelationshipController@index');
    Route::get('create', 'ClientRelationshipController@create');
    Route::post('store', 'ClientRelationshipController@store');
    Route::get('{client_relationship}/edit', 'ClientRelationshipController@edit');
    Route::get('{client_relationship}/show', 'ClientRelationshipController@show');
    Route::post('{id}/update', 'ClientRelationshipController@update');
    Route::get('{id}/delete', 'ClientRelationshipController@delete');

});
//Blacklist reasons
Route::group(['prefix' => 'blacklist_reason'], function () {
    Route::get('data', 'BlacklistReasonsController@index');
    Route::get('create', 'BlacklistReasonsController@create');
    Route::post('store', 'BlacklistReasonsController@store');
    Route::get('{reason}/edit', 'BlacklistReasonsController@edit');
    Route::get('{reason}/show', 'BlacklistReasonsController@show');
    Route::post('{reason}/update', 'BlacklistReasonsController@update');
    Route::get('{reason}/delete', 'BlacklistReasonsController@delete');

});
//route for client profession
Route::group(['prefix' => 'client_profession'], function () {
    Route::get('data', 'ClientProfessionController@index');
    Route::get('create', 'ClientProfessionController@create');
    Route::post('store', 'ClientProfessionController@store');
    Route::get('{client_profession}/edit', 'ClientProfessionController@edit');
    Route::get('{client_profession}/show', 'ClientProfessionController@show');
    Route::post('{id}/update', 'ClientProfessionController@update');
    Route::get('{id}/delete', 'ClientProfessionController@delete');
});
//route for groups
Route::group(['prefix' => 'group'], function () {
    Route::get('data', 'GroupController@index');
    Route::get('pending_approval', 'GroupController@pending_approval');
    Route::get('groups_declined', 'GroupController@declined');
    Route::get('groups_closed', 'GroupController@closed');
    Route::get('create', 'GroupController@create');
    Route::post('store', 'GroupController@store');
    Route::get('{group}/edit', 'GroupController@edit');
    Route::get('{group}/show', 'GroupController@show');
    Route::post('{id}/update', 'GroupController@update');
    Route::post('{id}/picture', 'GroupController@picture');
    Route::post('{id}/approve', 'GroupController@approve');
    Route::post('{id}/decline', 'GroupController@decline');
    Route::get('{id}/delete', 'GroupController@delete');
    //clients
    Route::post('{id}/client/store', 'GroupController@store_group_client');
    Route::get('client/{id}/delete', 'GroupController@delete_group_client');
    //document
    Route::post('{id}/document/store', 'GroupController@store_group_document');
    Route::get('document/{id}/delete', 'GroupController@delete_group_document');
    //notes
    Route::post('{id}/note/store', 'GroupController@store_note');
    Route::get('note/{id}/delete', 'GroupController@delete_note');
    Route::get('note/{note}/show', 'GroupController@show_note');
    Route::get('note/{note}/edit', 'GroupController@edit_note');
    Route::post('note/{id}/update', 'GroupController@update_note');
    //group users
    Route::post('{group}/add_user', 'GroupController@add_user');
    Route::get('{id}/delete_user', 'GroupController@delete_user');

});
//route for accounting
Route::group(['prefix' => 'accounting'], function () {
    //gl account
    Route::any('gl_account/data', 'GlAccountController@index');
    Route::get('gl_account/create', 'GlAccountController@create');
    Route::post('gl_account/store', 'GlAccountController@store');
    Route::get('gl_account/{gl_account}/edit', 'GlAccountController@edit');
    Route::get('gl_account/{gl_account}/show', 'GlAccountController@show');
    Route::post('gl_account/{id}/update', 'GlAccountController@update');
    Route::post('gl_account/{id}/picture', 'GlAccountController@picture');
    Route::post('gl_account/{id}/approve', 'GlAccountController@approve');
    Route::post('gl_account/{id}/decline', 'GlAccountController@decline');
    Route::get('gl_account/{id}/delete', 'GlAccountController@delete');
    //manual journal entries
    Route::any('journal', 'JournalController@index');
    Route::any('journal/reconstate', 'JournalController@reconstatement');
    Route::any('journal/data', 'JournalController@index');
    Route::any('journal/delete_jv', 'JournalController@delete_jv');
    Route::get('journal/create', 'JournalController@create');
    Route::any('journal/batch', 'JournalController@batch_ind');
    Route::any('journal/loans_batch', 'LoanController@ind_loan');
    Route::get('journal/create_op', 'JournalController@create_op');
    Route::post('journal/store', 'JournalController@store');
    Route::post('journal/store_batch', 'JournalController@store_batch');
    Route::post('journal/batch_loan', 'LoanController@batch_loan');
    Route::get('journal/{id}/delete_journal', 'JournalController@delete');
    Route::get('journal/del', 'JournalController@del_mul_jv');
    Route::post('journal/store_op', 'JournalController@store_op');
    Route::get('journal/{gl_journal_entry}/edit', 'JournalController@edit');
    Route::get('journal/{gl_journal_entry}/show', 'JournalController@show');
    Route::post('journal/{id}/update', 'JournalController@update');
    Route::post('journal/{id}/approve', 'JournalController@approve');
    Route::post('journal/{id}/decline', 'JournalController@decline');
    Route::post('reconciliations/calculate', 'JournalController@calculate')->middleware(['money']);


    //reconciliation
    Route::any('reconciliation/data', 'JournalController@reconciliation');
    Route::post('reconciliation/store', 'JournalController@store_reconciliation');
    //account closure
    Route::any('year_end/data', 'JournalController@year_end');
    Route::post('year_end/store', 'JournalController@store_year_end');
    Route::get('period/data', 'JournalController@period');
    Route::post('period/store', 'JournalController@store_period');
    Route::get('period/{id}/delete', 'JournalController@delete_period');
});
//route for accounting
Route::group(['prefix' => 'setting'], function () {
    //gl account
    Route::get('general', 'SettingController@general');
    Route::post('general/update', 'SettingController@update_general');
    Route::get('organisation', 'SettingController@organisation');
    Route::get('system', 'SettingController@system');


});
//route for funds
Route::group(['prefix' => 'fund'], function () {
    Route::get('data', 'FundController@index');
    Route::get('create', 'FundController@create');
    Route::post('store', 'FundController@store');
    Route::get('{fund}/edit', 'FundController@edit');
    Route::get('{fund}/show', 'FundController@show');
    Route::post('{id}/update', 'FundController@update');
    Route::get('{id}/delete', 'FundController@delete');
});
//route for funds
Route::group(['prefix' => 'payment_type'], function () {
    Route::get('data', 'PaymentTypeController@index');
    Route::get('create', 'PaymentTypeController@create');
    Route::post('store', 'PaymentTypeController@store');
    Route::get('{payment_type}/edit', 'PaymentTypeController@edit');
    Route::get('{payment_type}/show', 'PaymentTypeController@show');
    Route::post('{id}/update', 'PaymentTypeController@update');
    Route::get('{id}/delete', 'PaymentTypeController@delete');
});
//route for funds
Route::group(['prefix' => 'currency'], function () {
    Route::get('data', 'CurrencyController@index');
    Route::get('create', 'CurrencyController@create');
    Route::post('store', 'CurrencyController@store');
    Route::get('{currency}/edit', 'CurrencyController@edit');
    Route::get('{currency}/show', 'CurrencyController@show');
    Route::post('{id}/update', 'CurrencyController@update');
    Route::get('{id}/delete', 'CurrencyController@delete');
});
//route for charges
Route::group(['prefix' => 'charge'], function () {
    Route::get('data', 'ChargeController@index');
    Route::get('create', 'ChargeController@create');
    Route::post('store', 'ChargeController@store');
    Route::get('{charge}/edit', 'ChargeController@edit');
    Route::get('{charge}/show', 'ChargeController@show');
    Route::post('{id}/update', 'ChargeController@update');
    Route::get('{id}/delete', 'ChargeController@delete');
});
//route for loans
Route::group(['prefix' => 'loan'], function () {
    Route::get('data', 'LoanController@index')->name('loan.data');
    

    Route::get('my_loans', 'LoanController@my_index');
    Route::get('my_app_loans', 'LoanController@my_index_approved');
    Route::get('branch_app_loans', 'LoanController@branch_index_approved');
    Route::get('branch_loans', 'LoanController@branch_index');
    Route::get('reloan_approvals','LoanController@reloan_approvals');
    Route::get('transaction_approvals','LoanController@transaction_approvals');
    Route::get('pending_approval', 'LoanController@pending_approval');
    Route::get('managers_pending_approval', 'LoanController@managers_pending_approval');
    Route::get('awaiting_disbursement', 'LoanController@awaiting_disbursement');
    Route::get('loans_declined', 'LoanController@loans_declined');
    Route::get('loans_written_off', 'LoanController@loans_written_off');
    Route::get('loans_closed', 'LoanController@loans_closed');
    Route::get('loans_rescheduled', 'LoanController@loans_rescheduled');
    Route::get('{id}/{trans_id}/create_reloan', 'LoanController@new_reschedule_loan');
    Route::get('{id}/{trans_id}/create_transactiontt','LoanController@store_repayment');
    Route::get('{trans_id}/delete_pending_transaction','LoanController@delete_pending_transactions');
    Route::get('{trans_id}/delete_pending_transaction_fp_pp','LoanController@delete_pending_transactions_fp_pp');
    Route::get('create', 'LoanController@create');
    Route::get('create_client_loan/{client}/{loan_product}', 'LoanController@create_client_loan');
    Route::get('create_group_loan/{group}/{loan_product}', 'LoanController@create_group_loan');
    Route::post('create_client_loan/{client}/{loan_product}/store', 'LoanController@store_client_loan');
    Route::post('create_group_loan/{group}/{loan_product}/store', 'LoanController@store_group_loan');
    Route::post('store', 'LoanController@store');
    Route::post('loan_batch', 'LoanController@store_batch_loan');
    Route::get('{loan}/edit', 'LoanController@edit');
    Route::get('{loan}/activate', 'LoanController@activate');
    Route::get('{loan}/show', 'LoanController@show');
    Route::post('{id}/topup/update', 'LoanController@add_top_up');
    Route::post('client_loan/{id}/update', 'LoanController@update_client_loan');
    Route::post('group_loan/{id}/update', 'LoanController@update_group_loan');
    Route::get('{id}/delete', 'LoanController@delete');
    Route::post('{id}/approve', 'LoanController@approve_loan');
    Route::get('collections','LoanController@collections');
    Route::get('my_collections','LoanController@my_collections');
    //loan emails
    //Route::post('{id}/send_approve_email', 'LoanController@sendAppprovalEmail');
    //Make defaulted
    Route::get('{id}/set_defaulted', 'LoanController@set_defaulted');
    Route::post('{id}/decline', 'LoanController@decline_loan');
    Route::get('{id}/unapprove', 'LoanController@unapprove_loan');
    Route::post('{id}/disburse', 'LoanController@disburse_loan');
    Route::get('{id}/undisburse', 'LoanController@undisburse_loan');
    Route::post('{id}/withdraw', 'LoanController@withdraw_loan');
    Route::post('{id}/write_off', 'LoanController@write_off_loan');
    Route::post('{id}/reschedule_loan', 'LoanController@reschedule_loan');
    Route::post('{id}/change_loan_officer', 'LoanController@change_loan_officer');
    Route::get('{loan}/email_schedule', 'LoanController@email_schedule');
    Route::get('{loan}/print_schedule', 'LoanController@print_schedule');
    Route::get('{loan}/pdf_schedule', 'LoanController@pdf_schedule');
    Route::get('{loan}/print_statement', 'LoanController@print_statement');
    Route::get('{loan}/pdf_statement', 'LoanController@pdf_statement');
    Route::get('{loan}/statement', 'LoanController@statement');
    //loan calculator
    Route::get('calculator/create', 'LoanController@create_calculator');
    Route::get('calculator/create/{loan_product}', 'LoanController@create_calculator_page');
    Route::post('calculator/{loan_product}/show', 'LoanController@create_calculator_show');
    //document
    Route::post('{id}/document/store', 'LoanController@store_loan_document');
    Route::get('document/{id}/delete', 'LoanController@delete_loan_document');
    //notes
    Route::post('{id}/note/store', 'LoanController@store_note');
    Route::get('note/{id}/delete', 'LoanController@delete_note');
    Route::get('note/{note}/show', 'LoanController@show_note');
    Route::get('note/{note}/edit', 'LoanController@edit_note');
    Route::post('note/{id}/update', 'LoanController@update_note');
    //collateral
    Route::post('{id}/collateral/store', 'LoanController@store_collateral');
    Route::get('collateral/{id}/delete', 'LoanController@delete_collateral');
    Route::get('collateral/{collateral}/show', 'LoanController@show_collateral');
    Route::get('collateral/{collateral}/edit', 'LoanController@edit_collateral');
    Route::post('collateral/{id}/update', 'LoanController@update_collateral');
    //guarantor
    Route::post('{id}/guarantor/store', 'LoanController@store_guarantor');
    Route::get('guarantor/{id}/delete', 'LoanController@delete_guarantor');
    Route::get('guarantor/{guarantor}/show', 'LoanController@show_guarantor');
    Route::get('guarantor/{guarantor}/edit', 'LoanController@edit_guarantor');
    Route::post('guarantor/{id}/update', 'LoanController@update_guarantor');
    //loan products
    Route::get('product/data', 'LoanProductController@index');
    Route::get('product/create', 'LoanProductController@create');
    Route::post('product/store', 'LoanProductController@store');
    Route::get('product/{loan_product}/edit', 'LoanProductController@edit');
    Route::get('product/{loan_product}/show', 'LoanProductController@show');
    Route::post('product/{id}/update', 'LoanProductController@update');
    Route::get('product/{id}/delete', 'LoanProductController@delete');
    Route::get('product/{charge}/get_charge_detail', 'LoanProductController@get_charge_detail');
    Route::get('product/{id}/get_currency_charges', 'LoanProductController@get_currency_charges');
    //repayments
    Route::get('{loan}/repayment/create', 'LoanController@create_repayment');
    ////////////////////////////////////////////////////////////////////////////////////////
    Route::post('{id}/repayment/store', 'LoanController@transaction_fp_pp');
    Route::get('repayment/{loan_transaction}/edit', 'LoanController@edit_repayment');
    Route::post('repayment/{id}/update', 'LoanController@update_repayment');
    Route::get('repayment/{id}/reverse', 'LoanController@reverse_repayment');
    Route::post('{id}/interest/waive', 'LoanController@waive_interest');
    //transactions
    Route::get('transaction/{loan_transaction}/show', 'LoanController@show_transaction');
    Route::get('transaction/{loan_transaction}/print', 'LoanController@print_transaction');
    Route::get('transaction/{loan_transaction}/pdf', 'LoanController@pdf_transaction');
    Route::get('transaction/{loan_transaction}/waive', 'LoanController@waive_transaction');
    Route::post('{id}/charge/store', 'LoanController@store_charge');
    Route::post('{id}/refund/store', 'LoanController@store_refund');
    //loan application
    Route::get('application/data', 'LoanController@index_application');
    Route::get('my_applications/data', 'LoanController@my_applications');
    Route::get('application/{loan_application}/show', 'LoanController@show_application');
    Route::get('application/{id}/delete', 'LoanController@delete_application');
    Route::get('application/{loan_application}/edit', 'LoanController@edit_application');
    Route::post('application/{id}/update', 'LoanController@update_application');
    Route::post('application/{id}/approve', 'LoanController@approve_application');
    Route::post('application/{id}/decline', 'LoanController@decline_application');
});
//route for loan purposes
Route::group(['prefix' => 'loan_purpose'], function () {
    Route::get('data', 'LoanPurposeController@index');
    Route::get('create', 'LoanPurposeController@create');
    Route::post('store', 'LoanPurposeController@store');
    Route::get('{loan_purpose}/edit', 'LoanPurposeController@edit');
    Route::get('{loan_purpose}/show', 'LoanPurposeController@show');
    Route::post('{id}/update', 'LoanPurposeController@update');
    Route::get('{id}/delete', 'LoanPurposeController@delete');
});
//route for collateral types
Route::group(['prefix' => 'collateral_type'], function () {
    Route::get('data', 'CollateralTypeController@index');
    Route::get('create', 'CollateralTypeController@create');
    Route::post('store', 'CollateralTypeController@store');
    Route::get('{collateral_type}/edit', 'CollateralTypeController@edit');
    Route::get('{collateral_type}/show', 'CollateralTypeController@show');
    Route::post('{id}/update', 'CollateralTypeController@update');
    Route::get('{id}/delete', 'CollateralTypeController@delete');
});
//route for collateral types
Route::group(['prefix' => 'sms_gateway'], function () {
    Route::get('data', 'SmsGatewayController@index');
    Route::get('create', 'SmsGatewayController@create');
    Route::post('store', 'SmsGatewayController@store');
    Route::get('{sms_gateway}/edit', 'SmsGatewayController@edit');
    Route::get('{sms_gateway}/show', 'SmsGatewayController@show');
    Route::post('{id}/update', 'SmsGatewayController@update');
    Route::get('{id}/delete', 'SmsGatewayController@delete');
});
//route for reports
Route::group(['prefix' => 'report'], function () {
    Route::any('client_report', 'ReportController@client_report');
    Route::any('loan_report', 'ReportController@loan_report');
    Route::any('financial_report', 'ReportController@financial_report');
    Route::any('company_report', 'ReportController@company_report');
    Route::any('savings_report', 'ReportController@savings_report');
    Route::group(['prefix' => 'report_scheduler'], function () {
    Route::get('data', 'ReportSchedulerController@index');
        Route::get('create', 'ReportSchedulerController@create');
        Route::post('store', 'ReportSchedulerController@store');
        Route::get('{report_scheduler}/edit', 'ReportSchedulerController@edit');
        Route::post('{id}/update', 'ReportSchedulerController@update');
        Route::get('{id}/delete', 'ReportSchedulerController@delete');
    });

    Route::group(['prefix' => 'financial_report'], function () {
        Route::any('trial_balance', 'ReportController@trial_balance');
        Route::any('trial_balance_conso', 'ReportController@trial_balance_consolidated');
        Route::any('trial_balance/pdf', 'ReportController@trial_balance_pdf');
        Route::any('trial_balance/excel', 'ReportController@trial_balance_excel');
        Route::any('trial_balance/csv', 'ReportController@trial_balance_csv');
        Route::any('ledger_report', 'ReportController@ledger_report');
        Route::any('journals_report', 'ReportController@journals_report');
        Route::any('journals_report/pdf', 'ReportController@journals_report_pdf');
        Route::any('journals_report/excel', 'ReportController@journals_report_excel');
        Route::any('journals_report/csv', 'ReportController@journals_report_csv');
        Route::any('income_statement', 'ReportController@income_statement');
        Route::any('income_statement_conso', 'ReportController@income_statement_consolidated');
        Route::any('income_statement/pdf', 'ReportController@income_statement_pdf');
        Route::any('income_statement/excel', 'ReportController@income_statement_excel');
        Route::any('income_statement/csv', 'ReportController@income_statement_csv');
        Route::any('balance_sheet', 'ReportController@balance_sheet');
        Route::any('balance_sheet_conso', 'ReportController@balance_sheet_consolidated');
        Route::any('balance_sheet/pdf', 'ReportController@balance_sheet_pdf');
        Route::any('balance_sheet/excel', 'ReportController@balance_sheet_excel');
        Route::any('balance_sheet/csv', 'ReportController@balance_sheet_csv');
        Route::any('cash_flow', 'ReportController@cash_flow');
        Route::any('daily_transaction_report', 'ReportController@daily_transaction');
        Route::any('daily_transaction/pdf', 'ReportController@daily_transaction_pdf');
        Route::any('ledger_statement', 'ReportController@getAccountStatmentReport');
        Route::any('ledger_statement/pdf', 'ReportController@ledger_statement_pdf');
        Route::any('provisioning', 'ReportController@provisioning');
        Route::any('provisioning/pdf', 'ReportController@provisioning_pdf');
        Route::any('provisioning/excel', 'ReportController@provisioning_excel');
        Route::any('provisioning/csv', 'ReportController@provisioning_csv');
    });
    //////////////////////////////////////////////////////////REPORTS////////////////////////////////////////////////
    Route::group(['prefix' => 'loan_report'], function () {
        Route::any('expected_repayments', 'ReportController@expected_repayments');
        Route::any('expected_repayments_cro', 'ReportController@expected_repayments_cro');
        Route::any('expected_repayments/pdf', 'ReportController@expected_repayments_pdf');
        Route::any('expected_repayments/excel', 'ReportController@expected_repayments_excel');
        Route::any('expected_repayments/csv', 'ReportController@expected_repayments_csv');
        Route::any('my_repayments_report', 'ReportController@my_repayments_report');
        Route::any('repayments_report_detail', 'ReportController@repayments_report_details');
        Route::any('repayments_report', 'ReportController@repayments_report');
        Route::any('repayments_report/pdf', 'ReportController@repayments_report_pdf');
        Route::any('repayments_report_details/pdf', 'ReportController@repayments_report_details_pdf');
        Route::any('repayments_report_details/excel', 'ReportController@repayments_report_details_excel');
        Route::any('repayments_report_details/csv', 'ReportController@repayments_report_details_csv');
        Route::any('full_repayments_report/pdf', 'ReportController@full_repayments_report_pdf');
        Route::any('full_repayments_report/excel', 'ReportController@full_repayments_report_excel');
        Route::any('full_repayments_report/csv', 'ReportController@full_repayments_report_csv');
        Route::any('part_repayments_report/pdf', 'ReportController@part_repayments_report_pdf');
        Route::any('part_repayments_report/excel', 'ReportController@part_repayments_report_excel');
        Route::any('part_repayments_report/csv', 'ReportController@part_repayments_report_csv');
        Route::any('reloans_report/pdf', 'ReportController@reloans_report_pdf');
        Route::any('reloans_report/pdf', 'ReportController@reloans_report_pdf');
        Route::any('reloans_report/pdf', 'ReportController@reloans_report_pdf');
        Route::any('reloans_report/excel', 'ReportController@reloans_report_excel');
        Route::any('reloans_report/csv', 'ReportController@reloans_report_csv');
        Route::any('new_loans_report/pdf', 'ReportController@new_loans_report_pdf');
        Route::any('new_loans_report/excel', 'ReportController@new_loans_report_excel');
        Route::any('new_loans_report/csv', 'ReportController@new_loans_report_csv');
        //added route
        Route::get('expense_report/pdf', 'ReportController@expense_report_pdf');
        Route::get('expense_report/excel', 'ReportController@expense_report_excel');
        Route::get('expense_report/csv', 'ReportController@expense_report_csv');
        Route::get('/expense/expense-by-transaction-type', 'ExpenseController@expenseByTransactionType')->name('expense.expenseByTransactionType');
        //Route::post('expense_report/pdf', 'ExpenseController@expense_report_pdf');
        //Route::get('expense/data', 'ExpenseController@filter')->name('expenses.index');
        Route::get('advance_report/pdf', 'ReportController@advance_report_pdf');
        Route::get('advance_report/excel', 'ReportController@advance_report_excel');
        Route::get('advance_report/csv', 'ReportController@advance_report_csv');


        Route::any('repayments_report/excel', 'ReportController@repayments_report_excel');
        Route::any('repayments_report/csv', 'ReportController@repayments_report_csv');
        Route::any('my_collection_sheet', 'ReportController@my_collection_sheet');
        Route::any('collection_sheet', 'ReportController@collection_sheet');
        Route::any('collection_sheet/pdf', 'ReportController@collection_sheet_pdf');
        Route::any('collection_sheet/excel', 'ReportController@collection_sheet_excel');
        Route::any('collection_sheet/csv', 'ReportController@collection_sheet_csv');
        Route::any('loan_book', 'ReportController@loan_book');
        Route::any('customer_statement', 'ReportController@GetCustomerStatmentReport');
        Route::any('loan_book/pdf', 'ReportController@loan_book_pdf');
        Route::any('loan_book/excel', 'ReportController@loan_book_excel');
        Route::any('loan_book/csv', 'ReportController@loan_book_csv');
        Route::any('my_loan_book', 'ReportController@my_loan_book');
        Route::any('full_payments', 'ReportController@full_repayments');
        Route::any('arrears_report', 'ReportController@arrears_report');
        Route::any('arrears_report/pdf', 'ReportController@arrears_report_pdf');
        Route::any('arrears_report/excel', 'ReportController@arrears_report_excel');
        Route::any('arrears_report/csv', 'ReportController@arrears_report_csv');
        Route::any('age_analysis_principle', 'ReportController@age_analysis_principle');
        Route::any('age_analysis_outstanding', 'ReportController@age_analysis_outstanding');
        Route::any('age_analysis', 'ReportController@age_analysis');
        Route::any('age_analysis/pdf', 'ReportController@age_analysis_pdf');
        Route::any('age_analysis_principle/excel', 'ReportController@age_analysis_principle_excel');
        Route::any('age_analysis/csv', 'ReportController@age_analysis_csv');
        Route::any('disbursed_loans', 'ReportController@disbursed_loans');
        Route::any('disbursed_loans_pmec', 'ReportController@disbursed_loans_pmec');
        Route::any('disbursed_loans/pdf', 'ReportController@disbursed_loans_pdf');
        Route::any('disbursed_loans/excel', 'ReportController@disbursed_loans_excel');
        Route::any('disbursed_loans/csv', 'ReportController@disbursed_loans_csv');
        Route::any('loan_portfolio', 'ReportController@loan_portfolio');
        Route::any('loan_portfolio_cro', 'ReportController@loan_portfolio_cro');
        Route::any('loan_portfolio/pdf', 'ReportController@loan_portfolio_pdf');
        Route::any('loan_portfolio/excel', 'ReportController@loan_portfolio_excel');
        Route::any('loan_portfolio/csv', 'ReportController@loan_portfolio_csv');
    });
    Route::group(['prefix' => 'client_report'], function () {
        Route::any('client_numbers', 'ReportController@client_numbers');
        Route::any('client_numbers/pdf', 'ReportController@client_numbers_pdf');
        Route::any('client_numbers/excel', 'ReportController@client_numbers_excel');
        Route::any('client_numbers/csv', 'ReportController@client_numbers_csv');
        Route::any('client_listing', 'ReportController@client_listing');
        Route::any('client_listing/pdf', 'ReportController@client_listing_pdf');
        Route::any('client_listing/csv', 'ReportController@client_listing_csv');
        Route::any('top_borrowers', 'ReportController@top_borrowers');
        Route::any('top_borrowers/pdf', 'ReportController@top_borrowers_pdf');
        Route::any('top_borrowers/excel', 'ReportController@top_borrowers_excel');
        Route::any('top_borrowers/csv', 'ReportController@top_borrowers_csv');
        Route::any('borrowers_overview', 'ReportController@borrowers_overview');
        Route::any('borrowers_overview/pdf', 'ReportController@borrowers_overview_pdf');
        Route::any('borrowers_overview/excel', 'ReportController@borrowers_overview_excel');
        Route::any('borrowers_overview/csv', 'ReportController@borrowers_overview_csv');


    });
    Route::group(['prefix' => 'company_report'], function () {
        Route::any('products_summary', 'ReportController@products_summary');
        Route::any('products_summary/pdf', 'ReportController@products_summary_pdf');
        Route::any('products_summary/excel', 'ReportController@products_summary_excel');
        Route::any('products_summary/csv', 'ReportController@products_summary_csv');
        Route::any('general_report', 'ReportController@general_report');
        Route::any('top_borrowers', 'ReportController@top_borrowers');
        Route::any('top_borrowers/pdf', 'ReportController@top_borrowers_pdf');
        Route::any('top_borrowers/excel', 'ReportController@top_borrowers_excel');
        Route::any('top_borrowers/csv', 'ReportController@top_borrowers_csv');
        Route::any('borrowers_overview', 'ReportController@borrowers_overview');
        Route::any('borrowers_overview/pdf', 'ReportController@borrowers_overview_pdf');
        Route::any('borrowers_overview/excel', 'ReportController@borrowers_overview_excel');
        Route::any('borrowers_overview/csv', 'ReportController@borrowers_overview_csv');


    });
    Route::group(['prefix' => 'savings_report'], function () {
        Route::any('savings_transactions', 'ReportController@savings_transactions');
        Route::any('savings_transactions/pdf', 'ReportController@savings_transactions_pdf');
        Route::any('savings_transactions/excel', 'ReportController@savings_transactions_excel');
        Route::any('savings_transactions/csv', 'ReportController@savings_transactions_csv');
        Route::any('savings_balance', 'ReportController@savings_balance');
        Route::any('savings_balance/pdf', 'ReportController@savings_balance_pdf');
        Route::any('savings_balance/excel', 'ReportController@savings_balance_excel');
        Route::any('savings_balance/csv', 'ReportController@savings_balance_csv');
        Route::any('savings_accounts', 'ReportController@savings_accounts');
        Route::any('savings_accounts/pdf', 'ReportController@savings_accounts_pdf');
        Route::any('savings_accounts/excel', 'ReportController@savings_accounts_excel');
        Route::any('savings_accounts/csv', 'ReportController@savings_accounts_csv');


    });
    Route::any('cash_flow', 'ReportController@cash_flow');
    Route::any('collection', 'ReportController@collection_report');
    Route::any('loan_product', 'ReportController@profit_loss');

    Route::any('loan_list', 'ReportController@loan_list');
    Route::any('loan_balance', 'ReportController@loan_balance');
    Route::any('loan_arrears', 'ReportController@loan_arrears');
    Route::any('loan_transaction', 'ReportController@loan_transaction');
    Route::any('loan_classification', 'ReportController@loan_classification');
    Route::any('loan_projection', 'ReportController@loan_projection');
    //Route::any('borrower_report', 'ReportController@borrower_report');
    Route::any('collection_sheet', 'ReportController@collection_sheet');

});
//route for loan provisioning
Route::group(['prefix' => 'loan_provisioning'], function () {
    Route::get('data', 'LoanProvisioningController@index');
    Route::get('create', 'LoanProvisioningController@create');
    Route::post('store', 'LoanProvisioningController@store');
    Route::get('{loan_provisioning}/edit', 'LoanProvisioningController@edit');
    Route::get('{loan_provisioning}/show', 'LoanProvisioningController@show');
    Route::post('{id}/update', 'LoanProvisioningController@update');
    Route::get('{id}/delete', 'LoanProvisioningController@delete');
});
//route for savings
Route::group(['prefix' => 'savings'], function () {
    Route::get('data', 'SavingsController@index');
    Route::get('pending_approval', 'SavingsController@pending_approval');
    Route::get('awaiting_disbursement', 'SavingsController@awaiting_disbursement');
    Route::get('savings_declined', 'SavingsController@savings_declined');
    Route::get('savings_written_off', 'SavingsController@savings_written_off');
    Route::get('savings_closed', 'SavingsController@savings_closed');
    Route::get('savings_rescheduled', 'SavingsController@savings_rescheduled');
    Route::get('create', 'SavingsController@create');
    Route::get('create_client_savings/{client}/{savings_product}', 'SavingsController@create_client_savings');
    Route::get('create_group_savings/{group}/{savings_product}', 'SavingsController@create_group_savings');
    Route::post('create_client_savings/{client}/{savings_product}/store', 'SavingsController@store_client_savings');
    Route::post('create_group_savings/{group}/{savings_product}/store', 'SavingsController@store_group_savings');
    Route::post('store', 'SavingsController@store');
    Route::get('{savings}/edit', 'SavingsController@edit');
    Route::get('{savings}/show', 'SavingsController@show');
    Route::post('client_savings/{id}/update', 'SavingsController@update_client_savings');
    Route::post('group_savings/{id}/update', 'SavingsController@update_group_savings');
    Route::get('{id}/delete', 'SavingsController@delete');
    Route::post('{id}/approve', 'SavingsController@approve_savings');
    Route::post('{id}/decline', 'SavingsController@decline_savings');
    Route::get('{id}/unapprove', 'SavingsController@unapprove_savings');
    Route::post('{id}/disburse', 'SavingsController@disburse_savings');
    Route::get('{id}/undisburse', 'SavingsController@undisburse_savings');
    Route::post('{id}/withdraw', 'SavingsController@withdraw_savings');
    Route::post('{id}/write_off', 'SavingsController@write_off_savings');
    Route::post('{id}/change_savings_officer', 'SavingsController@change_savings_officer');
    Route::any('{savings}/email_statement', 'SavingsController@email_statement');
    Route::any('{savings}/print_statement', 'SavingsController@print_statement');
    Route::any('{savings}/pdf_statement', 'SavingsController@pdf_statement');
    //document
    Route::post('{id}/document/store', 'SavingsController@store_savings_document');
    Route::get('document/{id}/delete', 'SavingsController@delete_savings_document');
    //notes
    Route::post('{id}/note/store', 'SavingsController@store_note');
    Route::get('note/{id}/delete', 'SavingsController@delete_note');
    Route::get('note/{note}/show', 'SavingsController@show_note');
    Route::get('note/{note}/edit', 'SavingsController@edit_note');
    Route::post('note/{id}/update', 'SavingsController@update_note');
    //savings products
    Route::get('product/data', 'SavingsProductController@index');
    Route::get('product/create', 'SavingsProductController@create');
    Route::post('product/store', 'SavingsProductController@store');
    Route::get('product/{savings_product}/edit', 'SavingsProductController@edit');
    Route::get('product/{savings_product}/show', 'SavingsProductController@show');
    Route::post('product/{id}/update', 'SavingsProductController@update');
    Route::get('product/{id}/delete', 'LoanProductController@delete');
    Route::get('product/{charge}/get_charge_detail', 'SavingsProductController@get_charge_detail');
    Route::get('product/{id}/get_currency_charges', 'SavingsProductController@get_currency_charges');
    //deposits & withdrawals
    Route::get('{id}/deposit/create', 'SavingsController@create_deposit');
    Route::post('{id}/deposit/store', 'SavingsController@store_deposit');
    Route::get('deposit/{savings_transaction}/edit', 'SavingsController@edit_deposit');
    Route::post('deposit/{id}/update', 'SavingsController@update_deposit');
    Route::get('deposit/{id}/reverse', 'SavingsController@reverse_deposit');
    Route::get('{id}/withdrawal/create', 'SavingsController@create_withdrawal');
    Route::post('{id}/withdrawal/store', 'SavingsController@store_withdrawal');
    Route::get('withdrawal/{savings_transaction}/edit', 'SavingsController@edit_withdrawal');
    Route::post('withdrawal/{id}/update', 'SavingsController@update_withdrawal');
    Route::get('withdrawal/{id}/reverse', 'SavingsController@reverse_withdrawal');
    Route::post('{id}/interest/waive', 'SavingsController@waive_interest');
    //transactions
    Route::get('transaction/{savings_transaction}/show', 'SavingsController@show_transaction');
    Route::get('transaction/{savings_transaction}/print', 'SavingsController@print_transaction');
    Route::get('transaction/{savings_transaction}/pdf', 'SavingsController@pdf_transaction');
    Route::get('transaction/{savings_transaction}/waive', 'SavingsController@waive_transaction');
    Route::post('{id}/charge/store', 'SavingsController@store_charge');

});
//route for audit trail
Route::group(['prefix' => 'audit_trail'], function () {
    Route::get('data', 'AuditTrailController@index');
    Route::get('{id}/delete', 'AuditTrailController@delete');
});
//route for custom fields
Route::group(['prefix' => 'custom_field'], function () {

    Route::get('data', 'CustomFieldController@index');
    Route::get('create', 'CustomFieldController@create');
    Route::post('store', 'CustomFieldController@store');
    Route::get('{custom_field}/show', 'CustomFieldController@show');
    Route::get('{custom_field}/edit', 'CustomFieldController@edit');
    Route::post('{id}/update', 'CustomFieldController@update');
    Route::get('{id}/delete', 'CustomFieldController@delete');

});
//route for communication campaigns
Route::group(['prefix' => 'communication'], function () {

    Route::get('data', 'CommunicationController@index');
    Route::get('create', 'CommunicationController@create');
    Route::post('store', 'CommunicationController@store');
    Route::get('{communication_campaign}/show', 'CommunicationController@show');
    Route::get('{communication_campaign}/edit', 'CommunicationController@edit');
    Route::post('{id}/update', 'CommunicationController@update');
    Route::get('{id}/delete', 'CommunicationController@delete');
    Route::get('client/{client}/sms', 'CommunicationController@create_client_sms');
    Route::get('group/{group}/sms', 'CommunicationController@create_group_sms');
    Route::post('client/{client}/sms', 'CommunicationController@store_client_sms');
    Route::post('group/{group}/sms', 'CommunicationController@store_group_sms');
    Route::get('client/{client}/email', 'CommunicationController@create_client_email');
    Route::get('group/{group}/email', 'CommunicationController@create_group_email');
    Route::post('client/{client}/email', 'CommunicationController@store_client_email');
    Route::post('group/{group}/email', 'CommunicationController@store_group_email');
});
//route for assets
Route::group(['prefix' => 'asset'], function () {

    Route::get('data', 'AssetController@index');
    Route::get('create', 'AssetController@create');
    Route::post('store', 'AssetController@store');
    Route::get('{asset}/show', 'AssetController@show');
    Route::get('{asset}/edit', 'AssetController@edit');
    Route::post('{id}/update', 'AssetController@update');
    Route::get('{id}/delete', 'AssetController@delete');
    //asset types
    Route::get('type/data', 'AssetTypeController@index');
    Route::get('type/create', 'AssetTypeController@create');
    Route::post('type/store', 'AssetTypeController@store');
    Route::get('type/{asset_type}/show', 'AssetTypeController@show');
    Route::get('type/{asset_type}/edit', 'AssetTypeController@edit');
    Route::post('type/{id}/update', 'AssetTypeController@update');
    Route::get('type/{id}/delete', 'AssetTypeController@delete');

});
//route for other income
Route::group(['prefix' => 'other_income'], function () {
    Route::get('data', 'OtherIncomeController@index');
    Route::get('create', 'OtherIncomeController@create');
    Route::post('store', 'OtherIncomeController@store');
    Route::get('{other_income}/edit', 'OtherIncomeController@edit');
    Route::get('{other_income}/show', 'OtherIncomeController@show');
    Route::post('{id}/update', 'OtherIncomeController@update');
    Route::get('{id}/delete', 'OtherIncomeController@delete');
    Route::get('{id}/delete_file', 'OtherIncomeController@deleteFile');
    //income types
    Route::get('type/data', 'OtherIncomeTypeController@index');
    Route::get('type/create', 'OtherIncomeTypeController@create');
    Route::post('type/store', 'OtherIncomeTypeController@store');
    Route::get('type/{other_income_type}/edit', 'OtherIncomeTypeController@edit');
    Route::get('type/{other_income_type}/show', 'OtherIncomeTypeController@show');
    Route::post('type/{id}/update', 'OtherIncomeTypeController@update');
    Route::get('type/{id}/delete', 'OtherIncomeTypeController@delete');
});
//advances
Route::group(['prefix' => 'advance'], function () {
    Route::get('apply', 'AdvanceController@showApplyForm')->name('advances.apply');
    Route::post('submit', 'AdvanceController@submitAdvance')->name('advances.submit');
    Route::get('my_advances', 'AdvanceController@showMyAdvances')->name('advances.my_advances');
    Route::post('{id}/approve', 'AdvanceController@approve')->name('advances.approve');
    Route::post('{id}/decline', 'AdvanceController@decline')->name('advances.decline');
    Route::get('/pending_approvals', 'AdvanceController@showPendingApprovals')->name('advances.pending_approvals');
    Route::get('active_advances', 'AdvanceController@showActiveAdvances')->name('advances.active_advances');
    Route::get('closed_advances', 'AdvanceController@storeClosedAdvances')->name('advances.closed_advances');
    Route::get('declined_advances', 'AdvanceController@showDeclinedAdvances')->name('advances.declined_advances');
    Route::get('/active_advances/{id}', 'AdvanceController@showDetails')->name('advances.show');
    Route::get('/active_advances_province_manager/{id}', 'AdvanceController@showAdvancesForProvinceManager')->name('advances.active_advances_province_managers');
});

Route::group(['prefix' => 'ledger'], function () {
    Route::get('general', 'GeneralLedgerController@index')->name('ledger.general'); 
    Route::post('/store', 'GeneralLedgerController@store')->name('ledger.store');
    Route::get('summary', 'GeneralLedgerController@summary')->name('ledger.summary');
    Route::get('transactions', 'GeneralLedgerController@transactions')->name('ledger.transactions'); 
    Route::get('/ledger/{officeName}', 'GeneralLedgerController@show')->name('ledger.show');

});

//route for expenses
Route::group(['prefix' => 'expense'], function () {
    Route::get('data', 'ExpenseController@index');
    Route::get('create', 'ExpenseController@create');
    Route::post('store', 'ExpenseController@store');
    Route::get('{expense}/edit', 'ExpenseController@edit');
    Route::get('{expense}/show', 'ExpenseController@show');
    Route::post('{id}/update', 'ExpenseController@update');
    Route::post('{id}/reverse', 'ExpenseController@reverse_expense');
    Route::get('{id}/delete', 'ExpenseController@delete');
    Route::get('{id}/delete_file', 'ExpenseController@deleteFile');
    ///////////////////////////////////////////here
    Route::get('proof_of_payment/{filename}', 'ExpenseController@showProofOfPayment')->name('proof_of_payment.download');


    
    //expense types
    Route::get('type/data', 'ExpenseTypeController@index');
    Route::get('type/create', 'ExpenseTypeController@create');
    Route::post('type/store', 'ExpenseTypeController@store');
    Route::get('type/{expense_type}/edit', 'ExpenseTypeController@edit');
    Route::get('type/{expense_type}/show', 'ExpenseTypeController@show');
    Route::post('type/{id}/update', 'ExpenseTypeController@update');
    Route::get('type/{id}/delete', 'ExpenseTypeController@delete');
    //expense budgets
    Route::get('budget/data', 'ExpenseBudgetController@index');
    Route::get('budget/create', 'ExpenseBudgetController@create');
    Route::post('budget/store', 'ExpenseBudgetController@store');
    Route::get('budget/{expense_budget}/edit', 'ExpenseBudgetController@edit');
    Route::get('budget/{expense_budget}/show', 'ExpenseBudgetController@show');
    Route::post('budget/{id}/update', 'ExpenseBudgetController@update');
    Route::get('budget/{id}/delete', 'ExpenseBudgetController@delete');
    Route::any('budget/report', 'ExpenseBudgetController@report');
    Route::any('budget/report/pdf', 'ExpenseBudgetController@report_pdf');
    Route::any('budget/report/excel', 'ExpenseBudgetController@report_excel');
    Route::any('budget/report/csv', 'ExpenseBudgetController@report_csv');
});
//route for payroll
Route::group(['prefix' => 'payroll'], function () {
    Route::get('data', 'PayrollController@index');
    Route::get('create', 'PayrollController@create');
    Route::post('store', 'PayrollController@store');
    Route::post('create_new_payroll','PayrollController@create_new_payroll');
    Route::get('{payroll}/show', 'PayrollController@show');
    Route::get('{payroll}/edit', 'PayrollController@edit');
    Route::post('{id}/update', 'PayrollController@update');
    Route::get('{id}/delete', 'PayrollController@delete');
    Route::get('getUser/{id}', 'PayrollController@getUser');
    Route::get('{id}/payslip', 'PayrollController@pdfPayslip');
    Route::get('{user}/data', 'PayrollController@staffPayroll');
    Route::get('mypayslips','PayrollController@myPayslips');
    Route::get('mypayslips_old','PayrollController@myPayslipsOld');
    Route::get('payroll_list','PayrollController@payroll_list');
    Route::get('my_payroll_information','PayrollController@my_payroll_information');
    Route::post('add_payroll_information','PayrollController@add_payroll_information');
    Route::get('{id}/edit_payroll','PayrollController@edit_payroll');
    Route::get('{user}/user_payslip','PayrollController@user_payslip');
    Route::post('{id}/save_user_payslip','PayrollController@edit_payroll_information_manager');
    Route::post('{id}/save_payroll','PayrollController@save_payroll');
    Route::get('create_payroll','PayrollController@create_payroll');
    Route::any('download_payroll_excel_report', 'PayrollController@payroll_report_excel');
    Route::any('payroll_query','PayrollController@payroll_query');
    Route::get('{payroll}/payslip_old', 'PayrollController@pdfPayslipOld');

//template
    Route::any('template', 'PayrollTemplateController@index');
    Route::get('template/{id}/edit', 'PayrollTemplateController@edit');
    Route::post('template/{id}/update', 'PayrollTemplateController@update');
    Route::get('template/{id}/delete_meta', 'PayrollTemplateController@delete_meta');
    Route::post('template/{id}/add_row', 'PayrollTemplateController@add_row');
});
//route for client portal
Route::group(['prefix' => 'portal'], function () {
    //loan
    Route::get('loan/data', 'PortalController@loan_index');
    Route::get('loan/{loan}/show', 'PortalController@loan_show');
    //savings
    Route::get('savings/data', 'PortalController@savings_index');
    Route::get('savings/{savings}/show', 'PortalController@savings_show');
    //loan applications
    Route::get('loan_application/data', 'PortalController@loan_application_index');
    Route::get('loan_application/{loan_application}/show', 'PortalController@loan_application_show');
    Route::get('loan_application/create', 'PortalController@loan_application_create');
    Route::post('loan_application/store', 'PortalController@loan_application_store');

});

//Route::get('/', [MailController::class, 'sendMail']);
    
