<?php
// I'll wait for view_file result to find the exact line.

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
use App\Http\Controllers\PerformanceMetricsController;
use App\Http\Controllers\InductionController;
use App\Http\Controllers\LoanReportController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LearningSettingController;
use App\Http\Controllers\TrainingMaterialController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\GeneralUploadsController;
use App\Http\Controllers\GeneralTopicsController;
use App\Http\Controllers\StaffSurveyController;
use App\Http\Controllers\Recoveries\RecoveryDashboardController;
use App\Http\Controllers\Recoveries\RecoveryCaseController;
use App\Http\Controllers\Recoveries\RecoveryNudgeController;
use App\Http\Controllers\Recoveries\RecoverySpecialistController;
use App\Http\Controllers\Recoveries\RecoveryReportController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DistrictRegionalController;
use Firebase\JWT\Key;

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

// Recovery model bindings
Route::model('recovery_case', 'App\Models\RecoveryCase');
Route::model('recovery_payment', 'App\Models\RecoveryPayment');
Route::model('recovery_document', 'App\Models\RecoveryDocument');
Route::model('recovery_nudge', 'App\Models\RecoveryNudge');
Route::model('recovery_activity', 'App\Models\RecoveryActivity');
Route::model('recovery_specialist_target', 'App\Models\RecoverySpecialistTarget');

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


// routes/web.php
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

Route::get('/get-loan-officers-by-office/{officeId}', [LoanReportController::class, 'getLoanOfficersByOffice']);
Route::get('/get-loan-officers-by-province/{provinceId}', [LoanReportController::class, 'getLoanOfficersByProvince']);
Route::get('/get-all-loan-officers', [LoanReportController::class, 'getAllLoanOfficers']);

Route::post('/nextjs-login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    // Find user
    $user = Sentinel::findByCredentials($credentials);

    // Validate credentials
    if (!$user || !Sentinel::validateCredentials($user, $credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Log in user and start session
    Sentinel::login($user, true);
    $request->session()->regenerate(); // Important: regenerate session ID

    // Laravel automatically sets 'laravel_session' cookie in response
    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
        ]
    ]);
});

Route::get('/', [HomeController::class, 'index']);
Route::get('login', 'HomeController@login')->name('login');
Route::get('create_account', 'HomeController@create_account');
Route::get('signup', 'HomeController@signup');
Route::post('create_client_user', 'HomeController@create_client_account');
Route::post('login', 'HomeController@process_login');
Route::post('register', 'HomeController@register');
//Route::post('create_Profile', 'HomeController@create_Profile');
Route::post('createProfile', 'HomeController@createProfile');
Route::get('logout', 'HomeController@logout');
Route::get('password_reset', 'HomeController@password_reset');
Route::post('password_reset', 'HomeController@process_password_reset');
Route::get('confirm_password_reset/{id}/{code}', 'HomeController@confirm_password_reset');
Route::post('confirm_password_reset/{id}/{code}', 'HomeController@process_confirm_password_reset');
Route::get('payroll_loan', 'HomeController@payroll_loan');
//Route::get('payroll_loan/create-step-one','HomeController@createStepOne');
Route::any('create_payroll_loan_application', 'HomeController@create_payroll_loan_application');
Route::get('dashboard', [UserController::class, 'dashboard']);
Route::get('cron', 'CronController@index');
Route::get('test', 'TestController@index');
Route::post('induction/mark_as_seen', [InductionController::class, 'markAsSeen']);
Route::post('induction/toggle_checklist_item', [InductionController::class, 'toggleChecklistItem']);

// Learning Management System Routes
Route::group(['prefix' => 'learning', 'middleware' => 'sentinel'], function () {
    // Test route for PDF generation
    Route::get('/test-pdf', function () {
        $pdf = \PDF::loadHTML('<h1>Test PDF Generation</h1><p>This is a test to see if dompdf works.</p>');
        return $pdf->download('test.pdf');
    })->name('learning.test-pdf');
    Route::get('/', [LearningController::class, 'index'])->name('learning.dashboard');
    Route::get('/courses', [LearningController::class, 'courses'])->name('learning.courses');
    Route::get('/calendar', [LearningController::class, 'calendar'])->name('learning.calendar');
    Route::get('/progress', [LearningController::class, 'progress'])->name('learning.progress');
    Route::get('/certificates', [LearningController::class, 'certificates'])->name('learning.certificates');
    Route::get('/course/{id}', [LearningController::class, 'showCourse'])->name('learning.course');
     Route::get('/course/{id}/classroom', [LearningController::class, 'classroom'])->name('learning.course.classroom');
     Route::get('/course/{id}/classroom', [LearningController::class, 'classroom'])->name('learning.classroom');
    Route::post('/course/{id}/complete', [LearningController::class, 'completeCourse'])->name('learning.course.complete');
    Route::post('/course/{id}/complete-topic', [LearningController::class, 'completeTopic'])->name('learning.course.complete-topic');
    Route::post('/enroll/{id}', [LearningController::class, 'enroll'])->name('learning.enroll');
    Route::post('/unenroll/{id}', [LearningController::class, 'unenroll'])->name('learning.unenroll');
    Route::get('/course/{id}/certificate', [LearningController::class, 'generateCertificate'])->name('learning.course.certificate');
    Route::get('/course/{id}/certificate/{userId}', [LearningController::class, 'generateCertificate'])->name('learning.course.certificate.user');
    
    // Settings Routes
    Route::get('/settings', [LearningSettingController::class, 'index'])->name('learning.settings');
      Route::get('/settings/general-topics', [GeneralTopicsController::class, 'index'])->name('learning.settings.general-topics.index');
        Route::get('/settings/general-topics/create', [GeneralTopicsController::class, 'create'])->name('learning.settings.general-topics.create');
        Route::post('/settings/general-topics', [GeneralTopicsController::class, 'store'])->name('learning.settings.general-topics.store');
        Route::get('/settings/general-topics/{id}/edit', [GeneralTopicsController::class, 'edit'])->name('learning.settings.general-topics.edit');
        Route::put('/settings/general-topics/{id}', [GeneralTopicsController::class, 'update'])->name('learning.settings.general-topics.update');
        Route::delete('/settings/general-topics/{id}', [GeneralTopicsController::class, 'destroy'])->name('learning.settings.general-topics.destroy');
 
      // Analytics
      Route::get('/analytics', [App\Http\Controllers\LearningAnalyticsController::class, 'index'])->name('learning.analytics');
      Route::get('/analytics/viewers', [App\Http\Controllers\LearningAnalyticsController::class, 'getItemViewers'])->name('learning.analytics.viewers');
    Route::get('/settings/categories', [LearningSettingController::class, 'categories'])->name('learning.settings.categories');
    Route::get('/settings/students', [LearningSettingController::class, 'students'])->name('learning.settings.students');
    Route::get('/settings/teachers', [LearningSettingController::class, 'teachers'])->name('learning.settings.teachers');
    Route::get('/settings/platform', [LearningSettingController::class, 'platform'])->name('learning.settings.platform');
    Route::get('/settings/courses', [LearningSettingController::class, 'courses'])->name('learning.settings.courses');
    Route::get('/settings/courses/{id}/details', [LearningSettingController::class, 'getCourseDetails'])->name('learning.settings.courses.details');
    Route::get('/settings/courses/resource-preview', [LearningSettingController::class, 'resourcePreview'])->name('learning.settings.courses.resource-preview');
    
    // Trainer Management Routes
    Route::get('/api/all-roles', [LearningSettingController::class, 'getAllRoles']);
    Route::get('/api/users-by-role/{roleId}', [LearningSettingController::class, 'getUsersByRole']);
    Route::get('/api/roles-by-office/{officeId}', [LearningSettingController::class, 'getRolesByOffice']);
    Route::get('/api/users-by-office-role/{officeId}/{roleId}', [LearningSettingController::class, 'getUsersByOfficeRole']);
    Route::post('/settings/teachers/update-trainer', [LearningSettingController::class, 'updateTrainerStatus'])->name('learning.settings.teachers.update-trainer');
    Route::delete('/settings/teachers/remove-trainer/{userId}', [LearningSettingController::class, 'removeTrainerStatus'])->name('learning.settings.teachers.remove-trainer');
});

// Course Categories Management Routes
Route::group(['prefix' => 'course-categories', 'middleware' => 'sentinel'], function () {
    Route::get('/', [CourseCategoryController::class, 'index'])->name('course-categories.index');
    Route::get('/create', [CourseCategoryController::class, 'create'])->name('course-categories.create');
    Route::post('/', [CourseCategoryController::class, 'store'])->name('course-categories.store');
    Route::get('/{id}/edit', [CourseCategoryController::class, 'edit'])->name('course-categories.edit');
    Route::put('/{id}', [CourseCategoryController::class, 'update'])->name('course-categories.update');
    Route::delete('/{id}', [CourseCategoryController::class, 'destroy'])->name('course-categories.destroy');
    Route::post('/{id}/toggle-status', [CourseCategoryController::class, 'toggleStatus'])->name('course-categories.toggle-status');
});

 // Training Materials Management Routes
 Route::group(['prefix' => 'learning/training-materials', 'middleware' => 'sentinel'], function () {
     Route::get('/', [TrainingMaterialController::class, 'index'])->name('learning.training-materials.index');
     Route::get('/create', [TrainingMaterialController::class, 'create'])->name('learning.training-materials.create');
     Route::post('/', [TrainingMaterialController::class, 'store'])->name('learning.training-materials.store');
     // Chunk upload routes - must come before routes with parameters
     Route::post('/upload-chunk', [TrainingMaterialController::class, 'uploadChunk'])->name('learning.training-materials.upload-chunk');
     Route::post('/merge-chunks', [TrainingMaterialController::class, 'mergeChunks'])->name('learning.training-materials.merge-chunks');
     // New routes for course creation process - must come before routes with parameters
     Route::post('/store-course-info', [TrainingMaterialController::class, 'storeCourseInfo'])->name('learning.training-materials.store-course-info');
     Route::get('/{materialId}/add-topics', [TrainingMaterialController::class, 'showAddTopics'])->name('learning.training-materials.add-topics');
     Route::post('/{materialId}/store-topic', [TrainingMaterialController::class, 'storeTopic'])->name('learning.training-materials.store-topic');
     Route::get('/{materialId}/remove-topic/{topicId}', [TrainingMaterialController::class, 'removeTopic'])->name('learning.training-materials.remove-topic');
     // Edit topic route
     Route::get('/topic/{topicId}/edit', [TrainingMaterialController::class, 'editTopic'])->name('learning.training-materials.edit-topic');
     Route::put('/topic/{topicId}/update', [TrainingMaterialController::class, 'updateTopic'])->name('learning.training-materials.update-topic');
     // Routes with parameters
     Route::get('/{id}', [TrainingMaterialController::class, 'show'])->name('learning.training-materials.show');
     Route::get('/{id}/edit', [TrainingMaterialController::class, 'edit'])->name('learning.training-materials.edit');
     Route::put('/{id}', [TrainingMaterialController::class, 'update'])->name('learning.training-materials.update');
     Route::delete('/{id}', [TrainingMaterialController::class, 'destroy'])->name('learning.training-materials.destroy');
     Route::get('/{id}/download', [TrainingMaterialController::class, 'download'])->name('learning.training-materials.download');
     Route::post('/{id}/toggle-status', [TrainingMaterialController::class, 'toggleStatus'])->name('learning.training-materials.toggle-status');
     Route::get('/{id}/quiz', [QuizController::class, 'index'])->name('learning.quizzes.index');
     Route::get('/topic/{topicId}/quiz/manage', [QuizController::class, 'manage'])->name('learning.quizzes.manage');
     Route::post('/topic/{topicId}/quiz/save', [QuizController::class, 'save'])->name('learning.quizzes.save');
     Route::delete('/quiz/{quizId}/delete', [QuizController::class, 'delete'])->name('learning.quizzes.delete');
     Route::get('/quiz/{quizId}/questions', [QuizController::class, 'getQuestions'])->name('learning.quizzes.questions');
     
     // Quiz taking routes for students
     Route::get('/quiz/{quizId}/take', [QuizController::class, 'take'])->name('learning.quizzes.take');
     Route::post('/quiz/{quizId}/submit', [QuizController::class, 'submit'])->name('learning.quizzes.submit');
     Route::post('/quiz/{quizId}/submit-preview', [QuizController::class, 'submitPreview'])->name('learning.quizzes.submit-preview');
     
      // Topics management route for trainers
     Route::get('/{materialId}/topics', [TrainingMaterialController::class, 'topics'])->name('learning.training-materials.topics');
     Route::post('/topic/{topicId}/increment-view', [TrainingMaterialController::class, 'incrementTopicView'])->name('learning.training-materials.increment-topic-view');
 });

 // General Uploads Management Routes
    Route::group(['prefix' => 'learning/general-uploads', 'middleware' => 'sentinel'], function () {
        Route::get('/', [GeneralUploadsController::class, 'index'])->name('learning.general-uploads.index');
        Route::get('/watch-and-learning', [GeneralUploadsController::class, 'watchAndLearning'])->name('learning.watch-and-learning');
        Route::get('/create', [GeneralUploadsController::class, 'create'])->name('learning.general-uploads.create');
     Route::post('/', [GeneralUploadsController::class, 'store'])->name('learning.general-uploads.store');
     // Chunk upload routes - must come before routes with parameters
     Route::post('/upload-chunk', [GeneralUploadsController::class, 'uploadChunk'])->name('learning.general-uploads.upload-chunk');
     Route::post('/merge-chunks', [GeneralUploadsController::class, 'mergeChunks'])->name('learning.general-uploads.merge-chunks');
     // Routes with parameters
     Route::get('/{id}', [GeneralUploadsController::class, 'show'])->name('learning.general-uploads.show');
     Route::get('/{id}/edit', [GeneralUploadsController::class, 'edit'])->name('learning.general-uploads.edit');
     Route::put('/{id}', [GeneralUploadsController::class, 'update'])->name('learning.general-uploads.update');
     Route::delete('/{id}', [GeneralUploadsController::class, 'destroy'])->name('learning.general-uploads.destroy');
     Route::get('/{id}/download', [GeneralUploadsController::class, 'download'])->name('learning.general-uploads.download');
     Route::post('/{id}/toggle-status', [GeneralUploadsController::class, 'toggleStatus'])->name('learning.general-uploads.toggle-status');
     Route::post('/{id}/like', [GeneralUploadsController::class, 'like'])->name('learning.general-uploads.like');
     Route::post('/{id}/increment-view', [GeneralUploadsController::class, 'incrementView'])->name('learning.general-uploads.increment-view');
 });


Route::group(['prefix' => 'hr'],function(){
    Route::get('employees','HRController@employees');
    Route::get('{id}/employee','HRController@employee');
});

//route for users
Route::group(['prefix' => 'user'], function () {
    Route::get('data', 'UserController@index');
    Route::get('manager_performance','UserController@manager_performance');
    Route::get('{id}/branch_page', 'UserController@branch_page');
    Route::get('poadashboard','UserController@poadashboard');
    Route::get('pmdashboard','UserController@pmdashboard');
    Route::get('{id}/province_page', 'UserController@province_page');
    Route::any('{id}/qr-download', 'UserController@downloadSingleQR');
    Route::get('client_users/data', 'UserController@client_users_index');
    Route::get('daily_figures', 'UserController@daily_figures');
    Route::get('create', 'UserController@create');
    Route::post('store', 'UserController@store');
    Route::get('detailed_dashboard', 'UserController@detailed_dashboard');
    Route::any('/create_carry_over', 'UserController@create_carry_over');
    Route::any('/carry_over_approvals','UserController@carry_over_approvals');
    Route::any('{id}/approve_carry_over','UserController@approve_carry_over');
    Route::any('{id}/decline_carry_over','UserController@decline_carry_over');
    Route::any('mandatory_verification','UserController@mandatory_verification');
    Route::any('verify_numbers','UserController@verify_numbers');
    Route::any('transfers','UserController@transfers');
    Route::any('transfer_clients','UserController@transfer_clients');
    // Route::post('create_client_user','UserController@create_client_account');
    Route::get('{user}/edit', 'UserController@edit');
    Route::get('{user}/show', 'UserController@show');
    //active-inactive users
    Route::post('user/{id}/toggle-status', 'UserController@toggleStatus')->name('user.toggleStatus');
    Route::get('/user/inactive', 'UserController@inactive')->name('user.inactive');
    Route::post('{id}/update', 'UserController@update');
    Route::get('{id}/delete', 'UserController@delete');
    Route::get('edit_profile', 'UserController@edit_profile');
    Route::post('update_my_details', 'UserController@update_my_details');
    Route::get('my_details', 'UserController@my_details');
    Route::get('edit_my_details', 'UserController@edit_my_details');
    Route::get('profile', 'UserController@profile');
    Route::post('update_profile', 'UserController@profile_update');
    Route::post('profile_completion', 'UserController@profile_completion');
    Route::get('{user}/staff_info', 'UserController@user_info');
    Route::get('search', 'UserController@search');
    Route::any('{user}/{collection_type}/collections_stats', 'UserController@collections_stats');
    Route::any('{user}/{given_out_type}/given_out_stats', 'UserController@given_out_stats');
    Route::any('{user}/{uncollected_type}/uncollected_stats', 'UserController@uncollected_stats');
    Route::any('/leaderboard', 'UserController@leaderboard');
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


    //appraisals
    Route::any('appraisal_forms', 'UserController@appraisal_forms');
    Route::any('my_appraisal_forms', 'UserController@my_appraisal_forms');
    Route::any('appraisal_results', 'UserController@appraisal_results');
    Route::any('{id}/{form}/appraisal_result', 'UserController@appraisal_result');
    Route::any('{id}/appraisal_form', 'UserController@appraisal_form');
    Route::any('{id}/my_appraisal', 'UserController@my_appraisal');
    Route::any('{id}/submit_appraisal', 'UserController@submit_appraisal');
    Route::any('create_form', 'UserController@create_form');
    Route::any('{id}/add_section', 'UserController@add_section');
    Route::any('{section_id}/{id}/add_question', 'UserController@add_question');

    //LC Cycle information
    Route::get('cycle', 'UserController@cycle');
    Route::get('mandatory_cycle','UserController@mandatory_cycle');
    Route::post('addcycle', 'UserController@addCycle');
    Route::any('performance_information', 'UserController@performance_information');
    Route::any('branch_performance','UserController@branch_performance');
    Route::get('get_offices_by_province/{id}', 'UserController@get_offices_by_province');
    Route::get('get_districts_by_province/{id}', 'UserController@get_districts_by_province');
    Route::get('get_district_regionals_by_district/{id}', 'UserController@get_district_regionals_by_district');


     Route::get('branch_deposits','UserController@branch_deposits');
     Route::get('deposit_logs','UserController@deposit_logs');
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
    Route::any('{id}/location/store','ClientController@store_client_location');
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

    Route::get('get-staffs', 'ClientController@getStaffs');
    Route::get('search', 'ClientController@search');
    Route::get('staff/{staff_id}/clients', 'ClientController@staff_clients');
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
    Route::get('gl_account/export', 'GlAccountController@export');
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
    Route::get('add_fund_transfers_and_payments','JournalController@add_fund_transfers_and_payments');
    Route::any('store_fund_transfers_and_payments','JournalController@store_funds_transfers_and_payments');
    Route::get('pending_fund_movements','JournalController@fund_movement_approvals');
    Route::any('{id}/approve_fund', 'JournalController@approve_fund');
    Route::any('{id}/reject_fund', 'JournalController@reject_fund');
    Route::any('{id}/show_fund_movements','JournalController@show_fund_movement');

});
//route for accounting
Route::group(['prefix' => 'setting'], function () {
    //gl account
    Route::get('general', 'SettingController@general');
    Route::post('general/update', 'SettingController@update_general');
    Route::get('organisation', 'SettingController@organisation');
    Route::get('system', 'SettingController@system');
    Route::get('fail_safe', 'SettingController@fail_safe');


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
    Route::any('branch_uncollected', 'LoanController@branch_uncollected');
    Route::get('branch_app_loans', 'LoanController@branch_index_approved');
    Route::get('branch_loans', 'LoanController@branch_index');
    Route::get('reloan_approvals', 'LoanController@reloan_approvals');
    Route::get('transaction_approvals', 'LoanController@transaction_approvals');
    Route::get('recoveries_approvals', 'Recoveries\RecoveryCaseController@recoveriesApprovals');
    Route::get('recoveries_approve/{id}', 'Recoveries\RecoveryCaseController@recoveriesApprove');
    Route::get('recoveries_decline/{id}', 'Recoveries\RecoveryCaseController@recoveriesDecline');
    Route::get('recovery_case_approvals', 'Recoveries\RecoveryCaseController@recoveryCaseApprovals');
    Route::get('recovery_case_approve/{id}', 'Recoveries\RecoveryCaseController@recoveryCaseApprove');
    Route::get('recovery_case_decline/{id}', 'Recoveries\RecoveryCaseController@recoveryCaseDecline');
    Route::get('top_up_approvals', 'LoanController@top_up_approvals');
    //waiver changes
    Route::get('/waiver_approvals', 'LoanController@showWaiver')->name('loan.waiver_approvals');
    Route::get('loan/waiver-approvals', 'LoanController@showWaiver')->name('waiver.approvals');
    Route::post('loan/approve-waiver/{id}', 'LoanController@approveWaiver')->name('waiver.approve');
    Route::post('loan/decline-waiver/{id}', 'LoanController@declineWaiver')->name('waiver.decline');


    Route::get('dormant_loans','LoanController@dormant_loans');
    Route::get('pending_approval', 'LoanController@pending_approval');
    Route::get('managers_pending_approval', 'LoanController@managers_pending_approval');
    Route::get('awaiting_disbursement', 'LoanController@awaiting_disbursement');
    Route::get('loans_declined', 'LoanController@loans_declined');
    Route::get('loans_written_off', 'LoanController@loans_written_off');
    Route::get('loans_closed', 'LoanController@loans_closed')->name('loan.loans_closed');
    Route::get('loans_rescheduled', 'LoanController@loans_rescheduled');
    Route::get('{id}/{trans_id}/create_reloan', 'LoanController@new_reschedule_loan');
    Route::get('{id}/{trans_id}/create_transactiontt', 'LoanController@store_repayment');
    Route::get('{trans_id}/delete_pending_transaction', 'LoanController@delete_pending_transactions');
    Route::get('{trans_id}/delete_pending_transaction_fp_pp', 'LoanController@delete_pending_transactions_fp_pp');
    Route::get('create', 'LoanController@create');
    Route::get('create_client_loan/{client}/{loan_product}', 'LoanController@create_client_loan');
    Route::get('create_group_loan/{group}/{loan_product}', 'LoanController@create_group_loan');
    Route::post('create_client_loan/{client}/{loan_product}/store', 'LoanController@store_client_loan');
    Route::post('create_group_loan/{group}/{loan_product}/store', 'LoanController@store_group_loan');
    Route::post('store', 'LoanController@store');
    Route::post('loan_batch', 'LoanController@store_batch_loan');
    Route::get('{loan}/edit', 'LoanController@edit');
    Route::get('{loan}/activate', 'LoanController@activate');
    Route::get('{loan}/show', 'LoanController@show')->name('show');
    Route::post('{id}/topup/update', 'LoanController@add_top_up_request');
    Route::any('{id}/{trans_id}/approve_top_up', 'LoanController@approve_top_up');
    Route::any('{id}/decline_top_up', 'LoanController@decline_top_up');
    Route::post('client_loan/{id}/update', 'LoanController@update_client_loan');
    Route::post('group_loan/{id}/update', 'LoanController@update_group_loan');
    Route::get('{id}/delete', 'LoanController@delete');
    Route::post('{id}/approve', 'LoanController@approve_loan');
    Route::get('collections', 'LoanController@collections');
    Route::any('new_collections', 'LoanController@new_collections');
    Route::any('expected_collections', 'LoanController@expected_collections');
    Route::any('my_collections', 'LoanController@my_collections');
    Route::any('my_expected_collections', 'LoanController@my_expected_collections');
    Route::any('{id}/detailed_collections', 'LoanController@detailed_collections');
    Route::any('adjust_next_repayment', 'LoanController@adjust_next_repayment');
    Route::any('notification_test', 'LoanController@notification_test');
    Route::get('payroll_loan/pending_list', 'LoanController@pending_list');
    Route::get('payroll_loan/approved_list', 'LoanController@approved_list');
    Route::get('payroll_loan/declined_list', 'LoanController@declined_list');
    Route::get('payroll_loan/{id}/payroll_applicant', 'LoanController@payroll_applicant');
    Route::get('payroll_loan/{id}/approve_applicant', 'LoanController@approve_applicant');
    Route::get('payroll_loan/{id}/decline_applicant', 'LoanController@decline_applicant');
    Route::get('branch_graph', 'LoanController@branch_graph');
    Route::get('lusaka_graph', 'LoanController@lusaka_graph');
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
    Route::post('{id}/change_branch', 'LoanController@change_branch');
    Route::get('{loan}/email_schedule', 'LoanController@email_schedule');
    Route::get('{loan}/email_statement', 'LoanController@email_statement');
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
    //new charges
    Route::post('{id}/charge/store', 'LoanController@store_charge');
    Route::get('loan/charge_approvals', 'LoanController@showPendingCharges')->name('loan.charge_approvals');
    Route::post('loan/approve-charge/{id}', 'LoanController@approveCharge')->name('charge.approve');
    Route::post('loan/decline-charge/{id}', 'LoanController@declineCharge')->name('charge.decline');

    //loan application
    Route::get('application/data', 'LoanController@index_application');
    Route::get('my_applications/data', 'LoanController@my_applications');
    Route::get('application/{loan_application}/show', 'LoanController@show_application');
    Route::get('application/{id}/delete', 'LoanController@delete_application');
    Route::get('application/{loan_application}/edit', 'LoanController@edit_application');
    Route::post('application/{id}/update', 'LoanController@update_application');
    Route::post('application/{id}/approve', 'LoanController@approve_application');
    Route::post('application/{id}/decline', 'LoanController@decline_application');
    // AJAX endpoints for select2
    Route::get('ajax/clients', 'LoanController@ajaxClients');
    Route::get('ajax/groups', 'LoanController@ajaxGroups');
    Route::get('ajax/loan_products', 'LoanController@ajaxLoanProducts');
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
        Route::any('workings', 'ReportController@workings');
        Route::any('statement_of_comp_income', 'ReportController@statement_of_comp_income');
        Route::any('statement_of_financial_position', 'ReportController@statement_of_financial_position');
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
        Route::any('collections_report/pdf', 'ReportController@collections_report_pdf');
        Route::any('collections_report/excel', 'ReportController@collections_report_excel');
        Route::any('expected_collections_report/pdf', 'ReportController@expected_collections_report_pdf');
        Route::any('expected_collections_report/excel', 'ReportController@expected_collections_report_excel');
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
        Route::get(
            '/expense/expense-by-transaction-type',
            'ExpenseController@expenseByTransactionType'
        )->name('expense.expenseByTransactionType');
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
    Route::get('user/{user_id}', 'AuditTrailController@user_audit');
    Route::post('quick_audit', 'AuditTrailController@quickAudit');
    Route::get('{id}/delete', 'AuditTrailController@delete');
    Route::get('log/{id}','AuditTrailController@audit_log');
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

//route for tickets
Route::group(['prefix' => 'ticket'], function () {
    Route::get('/', 'TicketController@index');
    Route::get('index', 'TicketController@index');
    Route::get('create', 'TicketController@create');
    Route::get('create_dashboard_ticket', 'TicketController@create_dashboard_ticket');
    Route::post('store', 'TicketController@store');
    Route::post('store_dashboard_ticket','TicketController@store_dashboard_ticket');
    Route::post('reassign', 'TicketController@reassign');
    Route::post('reject', 'TicketController@reject');
    Route::match(['post', 'put'], '{id}/update', 'TicketController@update');
    Route::get('users', 'TicketController@usersByOfficeRole');
    Route::get('offices', 'TicketController@officesByParent');
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
    Route::post('{id}/close', 'AdvanceController@closeAdvance')->name('advances.close');
    Route::post('submit-top-up/{id}', 'AdvanceController@submitTopUp')->name('advances.submitTopUp');
    Route::post('approve/{id}', 'AdvanceController@approveTopUp')->name('topups.approve');
    Route::post('decline/{id}', 'AdvanceController@declineTopUp')->name('topups.decline');
    Route::get(
        '/topups_pending_approval',
        'AdvanceController@topupPendingApprovals'
    )->name('advances.topups_pending_approval');
    Route::get(
        '/active_advances_province_manager/{id}',
        'AdvanceController@showAdvancesForProvinceManager'
    )->name('advances.active_advances_province_managers');
});

//annual leave
Route::group(['prefix' => 'leave'], function () {

    Route::get('my_leave_days', 'LeaveController@myLeavedays')->name('leave.my_leave_days');
    Route::get('apply', 'LeaveController@applyForLeave')->name('leave.apply');
    Route::post('submit', 'LeaveController@submitLeave')->name('leave.submit');
    Route::get('/pending_leave_approvals', 'LeaveController@showPendingApprovals')->name('leave.pending_leave_approvals');
    Route::post('{id}/approve', 'LeaveController@approve')->name('leave.approve');
    Route::post('{id}/decline', 'LeaveController@decline')->name('leave.decline');
    Route::get('active_leave', 'LeaveController@showActiveLeave')->name('leave.active_leave');
    Route::get('/active_leave/{id}', 'LeaveController@showDetails')->name('leave.show');
    Route::get('declined_leave', 'LeaveController@showDeclinedLeave')->name('leave.declined_leave');
});

Route::group(['prefix' => 'ledger'], function () {
    // Route::get('general', 'GeneralLedgerController@index')->name('ledger.general');
    Route::post('/store/{officeName}', 'GeneralLedgerController@store')->name('ledger.store');
    Route::get('summary', 'GeneralLedgerController@summary')->name('ledger.summary');
    Route::get('transactions', 'GeneralLedgerController@transactions')->name('ledger.transactions');
    Route::get('/ledger/{officeName}', 'GeneralLedgerController@show')->name('ledger.show');
    Route::get('ledger_report_pdf', 'GeneralLedgerController@generateReport')->name('ledger.ledger_report_pdf');
    Route::get('ledger_report_excel', 'GeneralLedgerController@generateExcelReport')->name('ledger.ledger_report_excel');
    Route::get('all_report_excel', 'GeneralLedgerController@allgenerateExcelReport')->name('ledger.all_report_excel');
    Route::post('income_store', 'GeneralLedgerController@income_store')->name('ledger.income_store');
});

Route::group(['prefix' => 'performance_metrics'], function () {
    Route::get('/', [PerformanceMetricsController::class, 'index'])->name('performance_metrics.index');
    Route::get('/targets', [PerformanceMetricsController::class, 'targets'])->name('performance_metrics.targets');
    Route::get('/uncollected', [
        PerformanceMetricsController::class,
        'uncollected'
    ])->name('performance_metrics.uncollected');
    Route::get('/low_performance', [
        PerformanceMetricsController::class,
        'lowPerformance'
    ])->name('performance_metrics.low_performance');
    Route::get('/defaulted', [PerformanceMetricsController::class, 'defaulted'])->name('performance_metrics.defaulted');
});

//resignation routes
Route::group(['prefix' => 'resignation'], function () {
    Route::get('create', 'ResignationController@create')->name('resignation.create');
    Route::post('store', 'ResignationController@store')->name('resignation.store');
    Route::get('cancel/{id}', 'ResignationController@cancel')->name('resignation.cancel');
    Route::get('my', 'ResignationController@myResignations')->name('resignation.my');
    Route::get('manager/pending', 'ResignationController@managerPending')->name('resignation.manager.pending');
    Route::get('admin/pending', 'ResignationController@adminPending')->name('resignation.admin.pending');
    Route::post('manager/approve/{id}', 'ResignationController@managerApprove')->name('resignation.manager.approve');
    Route::post('admin/approve/{id}', 'ResignationController@adminApprove')->name('resignation.admin.approve');
    Route::get('approved', 'ResignationController@approved')->name('resignation.approved');
    Route::get('declined', 'ResignationController@declined')->name('resignation.declined');
    Route::get('show/{id}', 'ResignationController@show')->name('resignation.show');
});

//company policies
Route::group(['prefix' => 'policies'], function () {

    Route::get('view_policies', 'PolicyController@viewPolicies')->name('policies.view_policies');
    Route::get('user_responses', 'PolicyController@userResponses')->name('policies.user_responses');
    Route::get('add_policies', 'PolicyController@addPolicies')->name('policies.add_policies');
    Route::post('store_policies', 'PolicyController@storePolicies')->name('policies.store_policies');
    Route::post('{policy_id}/respond', 'PolicyController@respondToPolicy')->name('policies.respond');
    Route::get('search-users', 'PolicyController@searchUsers');
    Route::get('user-responses/{userId}', 'PolicyController@getUserResponses');
    Route::get('declined-responses', 'PolicyController@getDeclinedResponses');
    Route::post('reset-response/{userId}/{policyId}', 'PolicyController@resetUserResponse');
    Route::get('{policyId}/delete', 'PolicyController@deletePolicy')->name('policies.delete');
});

//staff survey routes
Route::group(['prefix' => 'survey'], function () {
    Route::get('/', [StaffSurveyController::class, 'show'])->name('survey.show');
    Route::post('/', [StaffSurveyController::class, 'store'])->name('survey.store');
    Route::get('thankyou', [StaffSurveyController::class, 'thankyou'])->name('survey.thankyou');
    Route::get('responses', [StaffSurveyController::class, 'index'])->name('survey.responses');
    Route::get('responses/{id}', [StaffSurveyController::class, 'showUserSurvey'])->name('survey.show_response');
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
    Route::get('proof_of_payment/{filename}', 'ExpenseController@showProofOfPayment')->name('proof_of_payment.download');

    //expense types
    Route::get('type/data', 'ExpenseTypeController@index');
    Route::get('type/create', 'ExpenseTypeController@create');
    Route::post('type/store', 'ExpenseTypeController@store');
    Route::get('type/{expense_type}/edit', 'ExpenseTypeController@edit');
    Route::get('type/{expense_type}/show', 'ExpenseTypeController@show');
    Route::post('type/{id}/update', 'ExpenseTypeController@update');
    Route::get('type/{id}/delete', 'ExpenseTypeController@delete');
    Route::get('type/{id}/get_gl_accounts', 'ExpenseTypeController@get_gl_accounts');
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
// District Management Routes
Route::prefix('districts')->group(function () {
    Route::get('/', [DistrictController::class, 'index']);
    Route::post('/', [DistrictController::class, 'store']);
    Route::get('/{id}', [DistrictController::class, 'show']);
    Route::put('/{id}', [DistrictController::class, 'update']);
    Route::delete('/{id}', [DistrictController::class, 'destroy']);
    Route::get('/stats', [DistrictController::class, 'getDistrictsWithStats']);
});

Route::prefix('district-regionals')->group(function () {
    Route::get('/', [DistrictRegionalController::class, 'index']);
    Route::post('/', [DistrictRegionalController::class, 'store']);
    Route::get('/{id}', [DistrictRegionalController::class, 'show']);
    Route::put('/{id}', [DistrictRegionalController::class, 'update']);
    Route::delete('/{id}', [DistrictRegionalController::class, 'destroy']);
    Route::get('/stats', [DistrictRegionalController::class, 'getDistrictRegionalsWithStats']);
});

//route for payroll
Route::group(['prefix' => 'payroll'], function () {
    Route::get('data', 'PayrollController@index');
    Route::any('lc_information', 'PayrollController@lc_information');
    Route::any('{user}/lc_information', 'PayrollController@lc_info');
    Route::get('create', 'PayrollController@create');
    Route::post('store', 'PayrollController@store');
    Route::any('create_wage_bill', 'PayrollController@create_wage_bill');
    Route::any('company_payroll','PayrollController@company_payroll');
    Route::post('create_new_payroll', 'PayrollController@create_new_payroll');
    Route::get('{payroll}/show', 'PayrollController@show');
    Route::get('{payroll}/edit', 'PayrollController@edit');
    Route::post('{id}/update', 'PayrollController@update');
    Route::get('export','PayrollController@company_payroll_excel');
    Route::get('{id}/delete', 'PayrollController@delete');
    Route::get('getUser/{id}', 'PayrollController@getUser');
    Route::get('{id}/payslip', 'PayrollController@pdfPayslip');
    Route::get('{user}/data', 'PayrollController@staffPayroll');
    Route::get('mypayslips', 'PayrollController@myPayslips');
    Route::get('mypayslips_old', 'PayrollController@myPayslipsOld');
    Route::get('payroll_list', 'PayrollController@payroll_list');
    Route::get('my_payroll_information', 'PayrollController@my_payroll_information');
    Route::post('add_payroll_information', 'PayrollController@add_payroll_information');
    Route::get('{id}/edit_payroll', 'PayrollController@edit_payroll');
    Route::get('{user}/user_payslip', 'PayrollController@user_payslip');
    Route::post('{id}/save_user_payslip', 'PayrollController@edit_payroll_information_manager');
    Route::post('{id}/save_payroll', 'PayrollController@save_payroll');
    Route::get('create_payroll', 'PayrollController@create_payroll');
    Route::any('store_new_payroll', 'PayrollController@storeNewPayroll');
    Route::any('{id}/edit_new_payroll', 'PayrollController@edit_new_payroll');
    Route::any('{id}/save_edit_new_payroll', 'PayrollController@save_edit_new_payroll');
    Route::any('download_payroll_excel_report', 'PayrollController@payroll_report_excel');
    Route::any('payroll_query', 'PayrollController@payroll_query');
    Route::get('{payroll}/payslip_old', 'PayrollController@pdfPayslipOld');
    Route::any('submit_payroll', 'PayrollController@submit_payroll');
    Route::any('{id}/submit_payroll', 'PayrollController@submit_single_payroll');
    Route::any('payroll_pending_approval', 'PayrollController@payroll_pending_approval');
    Route::any('{id}/payroll_pending_approval', 'PayrollController@single_payroll_pending_approval');
    Route::any('{id}/approve_payroll', 'PayrollController@approve_payroll');
    Route::any('{id}/approve_single_payroll', 'PayrollController@approve_single_payroll');
    Route::any('{id}/submit_single_payroll', 'PayrollController@submit_single_payroll');

    //template
    Route::any('template', 'PayrollTemplateController@index');
    Route::get('template/{id}/edit', 'PayrollTemplateController@edit');
    Route::post('template/{id}/update', 'PayrollTemplateController@update');
    Route::get('template/{id}/delete_meta', 'PayrollTemplateController@delete_meta');
    Route::post('template/{id}/add_row', 'PayrollTemplateController@add_row');
    Route::post('template/{id}/add_row_deduction', 'PayrollTemplateController@add_row_deduction');
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


//HYBRID ROUTES

// Prefix all hybrid routes
Route::group(['prefix' => 'hybrid'], function () {

    // Expected Collections
    Route::any('expected_collections', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@expected_collections');
    });

    // My Collections
    Route::any('my_collections', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@my_collections');
    });

    // My Expected Collections
    Route::any('my_expected_collections', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@my_expected_collections');
    });

    // Branch Uncollected
    Route::any('branch_uncollected', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@branch_uncollected');
    });

    // Appraisal Forms
    Route::any('appraisal_forms', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@appraisal_forms');
    });

    // My Appraisal Forms
    Route::any('my_appraisal_forms', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@my_appraisal_forms');
    });


    Route::any('payroll_loan/pending_list', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@pending_list');
    });

    Route::any('payroll_loan/approved_list', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@approved_list');
    });


    Route::any('payroll_loan/declined_list', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@declined_list');
    });



    Route::any('my_applications/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@my_applications');
    });


    Route::any('client/closed', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ClientController@closed');
    });

    Route::any('client/clients_inactive', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ClientController@clients_inactive');
    });

    Route::any('client/clients_blacklisted', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ClientController@clients_blacklisted');
    });

    Route::any('client/declined', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ClientController@declined');
    });


    Route::any('gl_account/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\GlAccountController@index');
    });


    Route::any('journal/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\JournalController@index');
    });


    Route::any('journal/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\JournalController@create');
    });


    Route::any('reconciliation/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\JournalController@reconciliation');
    });

    Route::any('period/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\JournalController@period');
    });


    Route::any('loan_report', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ReportController@loan_report');
    });

    Route::any('financial_report', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ReportController@financial_report');
    });


    Route::any('savings_report', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ReportController@savings_report');
    });


    Route::any('communication/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\CommunicationController@index');
    });

    Route::any('communication/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\CommunicationController@create');
    });


    Route::any('asset/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\AssetController@index');
    });

    Route::any('asset/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\AssetController@create');
    });

    Route::any('asset/type/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\AssetTypeController@index');
    });


    Route::any('expenses/type/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ExpenseTypeController@index');
    });

    Route::any('expenses/budget/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ExpenseBudgetController@index');
    });

    Route::any('expenses/budget/report', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ExpenseBudgetController@report');
    });

    Route::any('other_income/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\OtherIncomeController@index');
    });

    Route::any('other_income/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\OtherIncomeController@create');
    });

    Route::any('other_income/type/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\OtherIncomeTypeController@index');
    });


    Route::any('create_wage_bill', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollController@create_wage_bill');
    });


    Route::any('payroll_list', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollController@payroll_list');
    });

    Route::any('payroll_pending_approval', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollController@payroll_pending_approval');
    });

    Route::any('template', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollTemplateController@index');
    });

    Route::any('lc_information', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollController@lc_information');
    });


    Route::any('custom_fields/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\CustomFieldController@index');
    });


    Route::any('custom_fields/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\CustomFieldController@create');
    });


    Route::any('mypayslips', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\PayrollController@myPayslips');
    });


    Route::any('users/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@index');
    });


    Route::any('client_users/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@client_users_index');
    });


    Route::any('role/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@indexRole');
    });


    Route::any('users/create', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@create');
    });

    Route::any('audit_trail/data', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\AuditTrailController@index');
    });


    Route::any('settings/general', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\SettingController@general');
    });


    Route::any('settings/organisation', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\SettingController@organisation');
    });


    Route::any('settings/fail_safe', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\SettingController@fail_safe');
    });

    Route::any('/dashboard', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@dashboard');
    });

    Route::any('/whatsapp_clients', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\ClientController@whatsapp_clients');
    });

    Route::any('detailed_dashboard', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@detailed_dashboard');
    });


    Route::any('provinces_dashboard', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@provinces_dashboard');
    });


    Route::any('daily_figures', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\UserController@daily_figures');
    });

    Route::any('new_collections', function (Request $request) {
        return handleHybridRoute($request, 'App\Http\Controllers\LoanController@new_collections');
    });

    Route::get('institution_metrics', 'PerformanceMetricsController@institutionMetrics')->name('performance.institution_metrics');

});
// Collateral Module Routes
Route::get('collateral', 'CollateralController@index')->name('collateral.index');
Route::get('collateral/create', 'CollateralController@create')->name('collateral.create');
Route::post('collateral', 'CollateralController@store')->name('collateral.store');
Route::get('collateral/analytics/executive', 'CollateralController@analyticsExecutive')->name('collateral.analytics.executive');
Route::get('collateral/analytics/provincial', 'CollateralController@analyticsProvincial')->name('collateral.analytics.provincial');
Route::get('collateral/analytics/district', 'CollateralController@analyticsDistrict')->name('collateral.analytics.district');
Route::get('collateral/analytics/branch', 'CollateralController@analyticsBranch')->name('collateral.analytics.branch');
Route::get('collateral/report', 'CollateralController@report')->name('collateral.report');
Route::post('collateral/report/export', 'CollateralController@exportCsv')->name('collateral.export');
Route::get('collateral/approvals', 'CollateralApprovalController@queue')->name('collateral.approvals.queue');
Route::post('collateral/approvals/{collateral_status_change_request}/approve', 'CollateralApprovalController@approve')->name('collateral.approvals.approve');
Route::post('collateral/approvals/{collateral_status_change_request}/reject', 'CollateralApprovalController@reject')->name('collateral.approvals.reject');
Route::get('collateral/{collateral}', 'CollateralController@show')->name('collateral.show');
Route::get('collateral/{collateral}/edit', 'CollateralController@edit')->name('collateral.edit');
Route::put('collateral/{collateral}', 'CollateralController@update')->name('collateral.update');
Route::delete('collateral/{collateral}', 'CollateralController@destroy')->name('collateral.destroy');
Route::put('collateral/{collateral}', 'CollateralController@update')->name('collateral.update');
Route::post('collateral/{collateral}/request-status-change', 'CollateralApprovalController@requestChange')->name('collateral.request_change');
Route::post('collateral/{collateral}/change-status', 'CollateralApprovalController@directChange')->name('collateral.direct_change');

// ============================================
// RECOVERY MODULE ROUTES
// ============================================
Route::group(['prefix' => 'recovery'], function () {

    Route::get('overview', 'Recoveries\RecoveryDashboardController@overview');

    Route::group(['prefix' => 'case'], function () {
        Route::get('data',                 'Recoveries\RecoveryCaseController@index');
        Route::get('create',               'Recoveries\RecoveryCaseController@create');
        Route::post('store',               'Recoveries\RecoveryCaseController@store');
        Route::get('{id}/show',            'Recoveries\RecoveryCaseController@show');
        Route::get('{id}/edit',            'Recoveries\RecoveryCaseController@edit');
        Route::post('{id}/update',         'Recoveries\RecoveryCaseController@update');
        Route::get('{id}/delete',          'Recoveries\RecoveryCaseController@destroy');
        Route::delete('{id}',               'Recoveries\RecoveryCaseController@destroy');
        Route::post('{id}/status',         'Recoveries\RecoveryCaseController@updateStatus');
        Route::post('{id}/payment/store',  'Recoveries\RecoveryCaseController@recordPayment');
        Route::post('{id}/document/store', 'Recoveries\RecoveryCaseController@uploadDocument');
        Route::post('{id}/assign',         'Recoveries\RecoveryCaseController@assign');
        Route::post('{id}/cost/store',      'Recoveries\RecoveryCaseController@recordCost');
        Route::get('{id}/document/{document_id}/download', 'Recoveries\RecoveryCaseController@downloadDocument');
        Route::get('{id}/document/{document_id}/delete',   'Recoveries\RecoveryCaseController@deleteDocument');
        Route::get('cross_branch',         'Recoveries\RecoveryCaseController@crossBranch');
        Route::get('escalated',            'Recoveries\RecoveryCaseController@escalated');
        Route::get('dormant',              'Recoveries\RecoveryCaseController@dormant');
        Route::get('legal',                'Recoveries\RecoveryCaseController@legal');
        Route::get('skip_trace',           'Recoveries\RecoveryCaseController@skipTrace');
        Route::get('resolved',             'Recoveries\RecoveryCaseController@resolved');
    });

    // Nudge routes
    Route::get('nudge/compose',   'Recoveries\RecoveryNudgeController@compose');
    Route::post('nudge/send-bulk','Recoveries\RecoveryNudgeController@sendBulk');
    Route::post('case/{id}/nudge/send', 'Recoveries\RecoveryNudgeController@sendSingle');

    Route::group(['prefix' => 'specialist'], function () {
        Route::get('data',                           'Recoveries\RecoverySpecialistController@index');
        Route::get('{id}/show',                      'Recoveries\RecoverySpecialistController@show');
        Route::post('{id}/target/store',             'Recoveries\RecoverySpecialistController@setTarget');
        Route::get('{id}/target/{target_id}/delete', 'Recoveries\RecoverySpecialistController@deleteTarget');
        Route::post('store',                         'Recoveries\RecoverySpecialistController@store');
    });

    Route::group(['prefix' => 'report'], function () {
        // Monthly
        Route::any('overview',              'Recoveries\RecoveryReportController@monthly');
        Route::any('overview/pdf',          'Recoveries\RecoveryReportController@monthlyPdf');
        Route::any('overview/excel',        'Recoveries\RecoveryReportController@monthlyExcel');
        // Attribution
        Route::any('attribution',           'Recoveries\RecoveryReportController@attribution');
        Route::any('attribution/pdf',       'Recoveries\RecoveryReportController@attributionPdf');
        Route::any('attribution/excel',     'Recoveries\RecoveryReportController@attributionExcel');
        // Specialists
        Route::any('specialist_report',     'Recoveries\RecoveryReportController@specialists');
        Route::any('specialist_report/pdf', 'Recoveries\RecoveryReportController@specialistsPdf');
        Route::any('specialist_report/excel','Recoveries\RecoveryReportController@specialistsExcel');
    });

});

/**
 * Helper function for hybrid JWT login and controller call
 */
function handleHybridRoute(Request $request, $controllerMethod)
{
    $token = $request->query('token');
    if (!$token) {
        return response()->json(['success' => false, 'message' => 'Token missing'], 401);
    }

    try {
        $payload = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Invalid token: ' . $e->getMessage()], 401);
    }

    $user = Sentinel::findById($payload->id);
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    Sentinel::login($user); // sets laravel_session

    // Call the controller method with the current request
    return app()->call($controllerMethod, ['request' => $request]);
}