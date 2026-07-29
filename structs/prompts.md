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





Salaries Condition (put inside the if statement)
isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] === 'fully paid' 
                || in_array(Sentinel::getUser()->office_id, [62, 68])





@src/components/dashboards/BranchLevelView.tsx @src/components/dashboards/BranchManagerDashboard.tsx 

Redo/update the branch level target and threshold bands/status to follow these:

| Threshold |    Cash Balance | Contribution |
| --------: | --------------: | -----------: |
|        0% |             K 0 |          0pp |
|       10% |   K 187,196,400 |         10pp |
|       20% |   K 374,392,800 |         20pp |
|       30% |   K 561,589,200 |         30pp |
|       40% |   K 748,785,600 |         40pp |
|       50% |   K 935,982,000 |         50pp |
|       60% | K 1,123,178,400 |         60pp |
|       70% | K 1,310,374,800 |         70pp |
|       80% | K 1,497,571,200 |         80pp |
|       90% | K 1,684,767,600 |         90pp |
|      100% | K 1,871,964,000 |    **100pp** |


Name:  
Phone: 0971630902
Email: ethelsibalwa38@gmail.com
NRC: 448745/16/1

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


 