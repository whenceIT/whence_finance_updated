Filters
----------
Cool work! Great.

Now, Lets add a dropdown filter to filter the Controller function queries:

 - Overall (excludes filters to return overall data) 
 - This Month (default - when page loads)
 - This Quarter
 - This Year
 - This Circle (24th of last month to 24th of this month)
 - Last Circle (24th of last of last month to 24th of last month)
 - Last Quarter
 - Last Month
 - Last Year

Custom option to have two fields (This Month (default - when page loads)):
 - select month field (defult current month)
 - select year field (defult current year)

@dd((bool)$user->has_completed_profile, (bool)$user->has_seen_induction, $showPolicyModal)

                @if(isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] === 'unpaid')


            <!-- <x-debt-blocker/> -->
 ${shouldLockDeposit(depositName, total, monthlyRequired) ? 'locked ' : ''}











Avoid such errors from happening, if there is an existing entry exist with a flash message for better UX than displaying 500 error.
 [2026-07-21 10:54:34] local.ERROR: SQLSTATE[23000]: Integrity constraint violation: 106
2 Duplicate entry '2-2706' for key 'policy_quiz_attempts.policy_quiz_attempts_policy_quiz_id_user_id_unique' 
(SQL: insert into `policy_quiz_attempts` (`policy_quiz_id`, `user_id`, `started_at`) values (2, 2706, 2026-07-21 10:54:34)) 
{"exception":"[object] (Illuminate\\Database\\QueryException(code: 23000): 
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '2-2706' for key 
'policy_quiz_attempts.policy_quiz_attempts_policy_quiz_id_user_id_unique' (SQL: insert into `policy_quiz_attempts` 
 (`policy_quiz_id`, `user_id`, `started_at`) values (2, 2706, 2026-07-21 10:54:34)) at /var/www/html/whence_finance_updated/vendor/laravel/framework/
src/Illuminate/Database/Connection.php:760)


isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] != 'fully paid'


 