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



                @if(isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] === 'unpaid')


            <!-- <x-debt-blocker/> -->
 ${shouldLockDeposit(depositName, total, monthlyRequired) ? 'locked ' : ''}





 