@extends('layouts.master')

@section('title', 'Staff Survey')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Staff Survey</h3>
    </div>
    
    <div class="box-body">
        <form action="{{ route('survey.store') }}" method="POST" id="surveyForm">
            @csrf
            
            <!-- Question 1: Branch -->
            <div class="form-group">
                <label>1. Which branch are you from? <span class="text-danger">*</span></label>
                <select name="branch" class="form-control" required>
                    <option value="">Select Branch</option>
                    <option value="Zimco">Zimco</option>
                    <option value="Anchor House">Anchor House</option>
                    <option value="Kambendekela">Kambendekela</option>
                    <option value="Chilanga">Chilanga</option>
                    <option value="Kafue">Kafue</option>
                    <option value="Chongwe">Chongwe</option>
                    <option value="Chirundu">Chirundu</option>
                    <option value="Mazabuka">Mazabuka</option>
                    <option value="Monze">Monze</option>
                    <option value="Choma">Choma</option>
                    <option value="Kalomo">Kalomo</option>
                    <option value="Maamba">Maamba</option>
                    <option value="Livingstone">Livingstone</option>
                    <option value="Kabwe">Kabwe</option>
                    <option value="Kapiri Mposhi">Kapiri Mposhi</option>
                    <option value="Ndola">Ndola</option>
                    <option value="Mpongwe">Mpongwe</option>
                    <option value="Masala">Masala</option>
                    <option value="Kitwe">Kitwe</option>
                    <option value="Luanshya">Luanshya</option>
                    <option value="Chingola">Chingola</option>
                    <option value="Chambishi">Chambishi</option>
                    <option value="Mufulira">Mufulira</option>
                    <option value="Chililabombwe">Chililabombwe</option>
                    <option value="Kalulushi">Kalulushi</option>
                    <option value="Pamozi">Pamozi</option>
                    <option value="Chimwemwe">Chimwemwe</option>
                    <option value="Solwezi HQ">Solwezi HQ</option>
                    <option value="Solwezi 1">Solwezi 1</option>
                    <option value="Mitec">Mitec</option>
                    <option value="Lumwana">Lumwana</option>
                    <option value="Kalumbila">Kalumbila</option>
                    <option value="Kasama">Kasama</option>
                    <option value="Mpika">Mpika</option>
                    <option value="Mbala">Mbala</option>
                    <option value="Samfya">Samfya</option>
                    <option value="Mansa">Mansa</option>
                    <option value="Nyimba">Nyimba</option>
                    <option value="Petauke">Petauke</option>
                    <option value="Katete">Katete</option>
                    <option value="Chipata">Chipata</option>
                    <option value="Mumbwe">Mumbwe</option>
                    <option value="Kaoma">Kaoma</option>
                    <option value="Mongu">Mongu</option>
                    <option value="Senanga">Senanga</option>
                </select>
            </div>

            <!-- Question 2: Length of Service -->
            <div class="form-group">
                <label>2. Length of Service <span class="text-danger">*</span></label>
                <select name="length_of_service" class="form-control" required>
                    <option value="">Select Length of Service</option>
                    <option value="Less than 6 months">Less than 6 months</option>
                    <option value="6 months - 1 year">6 months - 1 year</option>
                    <option value="1 - 3 years">1 - 3 years</option>
                    <option value="Over 3 years">Over 3 years</option>
                </select>
            </div>

            <!-- Question 3: BMOS Consistency -->
            <div class="form-group">
                <label>3. Does your branch consistently meet Branch Minimum Operational Standards (BMOS), including availability of essential office supplies and facilities? <span class="text-danger">*</span></label>
                <select name="bmos_consistency" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes, fully">Yes, fully</option>
                    <option value="Partially">Partially</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 4: BMOS Challenges -->
            <div class="form-group">
                <label>4. Which BMOS or office supply challenges affect your branch? <span class="text-danger">*</span></label>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="Inadequate stationery (pen, paper etc..)"> Inadequate stationery (pen, paper etc..)</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="Inconsistent internet connectivity"> Inconsistent internet connectivity</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="Lack of water, sanitation, or hygiene supplies"> Lack of water, sanitation, or hygiene supplies</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="Lack of staff welfare facilities (tea/coffee, microwave, water dispenser, ect.)"> Lack of staff welfare facilities (tea/coffee, microwave, water dispenser, ect.)</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="None"> None</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="bmos_challenges[]" value="Others (please specify)"> Others (please specify)</label>
                </div>
                <input type="text" name="bmos_challenges_other" class="form-control" placeholder="Please specify other challenges" style="margin-top: 10px;">
            </div>

            <!-- Question 5: Branch Needs -->
            <div class="form-group">
                <label>5. What is the one thing you feel your branch needs the most right now to help you work more effectively? <span class="text-danger">*</span></label>
                <textarea name="branch_needs" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Question 6: Tools and Resources -->
            <div class="form-group">
                <label>6. Do you have the tools and resources needed to perform your job effectively? <span class="text-danger">*</span></label>
                <select name="tools_resources" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 7: Operational Challenges -->
            <div class="form-group">
                <label>7. Which operational challenges affect your work the most? <span class="text-danger">*</span></label>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="System or internet challenges"> System or internet challenges</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="Inadequate staffing"> Inadequate staffing</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="Transport/fleet issues"> Transport/fleet issues</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="Power outages"> Power outages</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="Client-related challenges"> Client-related challenges</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="High workload"> High workload</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="operational_challenges[]" value="Other (please specify)"> Other (please specify)</label>
                </div>
                <input type="text" name="operational_challenges_other" class="form-control" placeholder="Please specify other challenges" style="margin-top: 10px;">
            </div>

            <!-- Question 8: Supervisor Support -->
            <div class="form-group">
                <label>8. Do you receive adequate support from your immediate supervisor? <span class="text-danger">*</span></label>
                <select name="supervisor_support" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 9: Manager Communication -->
            <div class="form-group">
                <label>9. Does your immediate manager communicate effectively with you regarding work expectations, targets, and operational issues? <span class="text-danger">*</span></label>
                <select name="manager_communication" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes, consistently">Yes, consistently</option>
                    <option value="Sometimes">Sometimes</option>
                    <option value="Rarely">Rarely</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 10: Manager Communication Comments -->
            <div class="form-group">
                <label>10. Do you have any comments or concerns regarding your manager's communication with you or with clients?</label>
                <textarea name="manager_communication_comments" class="form-control" rows="3"></textarea>
            </div>

            <!-- Question 11: Leadership Challenges -->
            <div class="form-group">
                <label>11. Which leadership or management challenge do you experience? <span class="text-danger">*</span></label>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Poor communication"> Poor communication</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Unclear targets or expectations"> Unclear targets or expectations</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Favoritism or unfair treatment"> Favoritism or unfair treatment</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Lack of Feedback"> Lack of Feedback</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Delayed decision-making"> Delayed decision-making</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="None"> None</label>
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="leadership_challenges[]" value="Other (please specify)"> Other (please specify)</label>
                </div>
                <input type="text" name="leadership_challenges_other" class="form-control" placeholder="Please specify other challenges" style="margin-top: 10px;">
            </div>

            <!-- Question 12: Manager Effectiveness Rating -->
            <div class="form-group">
                <label>12. With illustrations or examples where possible, on a scale of 1 to 10 how would you rate your immediate manager's overall effectiveness? <span class="text-danger">*</span></label>
                <select name="manager_effectiveness_rating" class="form-control" required>
                    <option value="">Select Rating</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
            </div>

            <!-- Question 13: Workload Rating -->
            <div class="form-group">
                <label>13. How would you rate your current workload (Manageable, heavy, unmanageable)? Give a reason for your response. <span class="text-danger">*</span></label>
                <textarea name="workload_rating" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Question 14: Unethical Conduct -->
            <div class="form-group">
                <label>14. Have you observed or experienced any unethical or inappropriate conduct at your branch? <span class="text-danger">*</span></label>
                <select name="unethical_conduct" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 15: Policy Violation Instructions -->
            <div class="form-group">
                <label>15. Have you ever received instructions at your branch that go against company policy? <span class="text-danger">*</span></label>
                <select name="policy_violation_instructions" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 16: Policy Violation Description -->
            <div class="form-group">
                <label>16. If yes, please briefly describe the situation.</label>
                <textarea name="policy_violation_description" class="form-control" rows="3"></textarea>
            </div>

            <!-- Question 17: Top Issues -->
            <div class="form-group">
                <label>17. What are the top issues management should urgently address at your branch? <span class="text-danger">*</span></label>
                <textarea name="top_issues" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Question 18: Pending Loans Entry -->
            <div class="form-group">
                <label>18. Do you enter pending loans on the system? <span class="text-danger">*</span></label>
                <select name="pending_loans_entry" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes, immediately">Yes, immediately</option>
                    <option value="Yes, but with delays">Yes, but with delays</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 19: Longest Pending Period -->
            <div class="form-group">
                <label>19. What is the longest period a client has gone without a loan being disbursed to them after application? <span class="text-danger">*</span></label>
                <textarea name="longest_pending_period" class="form-control" rows="2" required></textarea>
            </div>

            <!-- Question 20: Missed Target Due Pending -->
            <div class="form-group">
                <label>20. In the last three months, have you missed your performance target because of pending loans? <span class="text-danger">*</span></label>
                <select name="missed_target_due_pending" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <!-- Question 21: Pending Target Explanation -->
            <div class="form-group">
                <label>21. If yes, please brief explain how pending loans affected your target.</label>
                <textarea name="pending_target_explanation" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg">Submit Survey</button>
                <a href="{{ url('dashboard') }}" class="btn btn-default btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('surveyForm').addEventListener('submit', function(e) {
    // Combine checkbox values
    const bmosChallenges = document.querySelectorAll('input[name="bmos_challenges[]"]:checked');
    const bmosChallengesArray = Array.from(bmosChallenges).map(cb => cb.value);
    const bmosOther = document.querySelector('input[name="bmos_challenges_other"]').value;
    if (bmosOther && bmosChallengesArray.includes('Others (please specify)')) {
        bmosChallengesArray.push(bmosOther);
    }
    
    const operationalChallenges = document.querySelectorAll('input[name="operational_challenges[]"]:checked');
    const operationalChallengesArray = Array.from(operationalChallenges).map(cb => cb.value);
    const operationalOther = document.querySelector('input[name="operational_challenges_other"]').value;
    if (operationalOther && operationalChallengesArray.includes('Other (please specify)')) {
        operationalChallengesArray.push(operationalOther);
    }
    
    const leadershipChallenges = document.querySelectorAll('input[name="leadership_challenges[]"]:checked');
    const leadershipChallengesArray = Array.from(leadershipChallenges).map(cb => cb.value);
    const leadershipOther = document.querySelector('input[name="leadership_challenges_other"]').value;
    if (leadershipOther && leadershipChallengesArray.includes('Other (please specify)')) {
        leadershipChallengesArray.push(leadershipOther);
    }
    
    // Create hidden inputs for combined values
    const form = this;
    
    // Remove existing hidden inputs if any
    const existingBmos = form.querySelector('input[name="bmos_challenges_combined"]');
    if (existingBmos) existingBmos.remove();
    
    const existingOperational = form.querySelector('input[name="operational_challenges_combined"]');
    if (existingOperational) existingOperational.remove();
    
    const existingLeadership = form.querySelector('input[name="leadership_challenges_combined"]');
    if (existingLeadership) existingLeadership.remove();
    
    // Add hidden inputs
    const bmosInput = document.createElement('input');
    bmosInput.type = 'hidden';
    bmosInput.name = 'bmos_challenges';
    bmosInput.value = bmosChallengesArray.join(', ');
    form.appendChild(bmosInput);
    
    const operationalInput = document.createElement('input');
    operationalInput.type = 'hidden';
    operationalInput.name = 'operational_challenges';
    operationalInput.value = operationalChallengesArray.join(', ');
    form.appendChild(operationalInput);
    
    const leadershipInput = document.createElement('input');
    leadershipInput.type = 'hidden';
    leadershipInput.name = 'leadership_challenges';
    leadershipInput.value = leadershipChallengesArray.join(', ');
    form.appendChild(leadershipInput);
});
</script>
@endsection
