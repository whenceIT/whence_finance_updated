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



        // $mvlService = app(\App\Services\MVLService::class);
        // $mvlService->week1reminder();
        // $mvlService->month1reminder();


 @if($showInductionModal && $role !== 11)
                    @include('partials.induction_modal')
                @else
                    <!-- Policy Response Required Modal -->
                    <div id="policyModal"
                        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center; animation: modalFadeIn 0.4s ease-out;">
                        <div
                            style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                            <h3 style="margin-bottom: 20px; color: #333;">Policy Acknowledgment Required</h3>
                            <p style="margin-bottom: 30px; color: #666;">You have unread company policies that require your
                                acknowledgment. Please review and respond to them.</p>
                            <a href="{{ route('policies.view_policies') }}" class="btn btn-primary btn-lg"
                                style="padding: 10px 30px; font-size: 16px;">Review Policies</a>
                        </div>
                    </div>
                    <script>
                        // Prevent closing the modal
                        document.getElementById('policyModal').addEventListener('click', function (event) {
                            event.stopPropagation();
                        });
                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'Escape') {
                                event.preventDefault();
                            }
                        });
                    </script>
                    @php
                        $user = Sentinel::getUser();
                    @endphp
                @endif 
 