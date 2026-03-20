<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('staff_surveys')) {
            Schema::create('staff_surveys', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                
                // Question 1: Branch
                $table->string('branch');
                
                // Question 2: Length of Service
                $table->string('length_of_service');
                
                // Question 3: BMOS Consistency
                $table->string('bmos_consistency');
                
                // Question 4: BMOS Challenges
                $table->text('bmos_challenges')->nullable();
                
                // Question 5: Branch Needs
                $table->text('branch_needs')->nullable();
                
                // Question 6: Tools and Resources
                $table->string('tools_resources');
                
                // Question 7: Operational Challenges
                $table->text('operational_challenges')->nullable();
                
                // Question 8: Supervisor Support
                $table->string('supervisor_support');
                
                // Question 9: Manager Communication
                $table->string('manager_communication');
                
                // Question 10: Manager Communication Comments
                $table->text('manager_communication_comments')->nullable();
                
                // Question 11: Leadership Challenges
                $table->text('leadership_challenges')->nullable();
                
                // Question 12: Manager Effectiveness Rating
                $table->integer('manager_effectiveness_rating');
                
                // Question 13: Workload Rating
                $table->text('workload_rating')->nullable();
                
                // Question 14: Unethical Conduct
                $table->string('unethical_conduct');
                
                // Question 15: Policy Violation Instructions
                $table->string('policy_violation_instructions');
                
                // Question 16: Policy Violation Description
                $table->text('policy_violation_description')->nullable();
                
                // Question 17: Top Issues
                $table->text('top_issues')->nullable();
                
                // Question 18: Pending Loans Entry
                $table->string('pending_loans_entry');
                
                // Question 19: Longest Pending Period
                $table->text('longest_pending_period')->nullable();
                
                // Question 20: Missed Target Due Pending
                $table->string('missed_target_due_pending');
                
                // Question 21: Pending Target Explanation
                $table->text('pending_target_explanation')->nullable();
                
                $table->timestamps();
                $table->engine = 'InnoDB';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_surveys');
    }
};
