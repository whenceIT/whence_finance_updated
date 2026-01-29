@extends('layouts.master')

@section('title', 'Survey Response Details')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Survey Response Details</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('survey.responses') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    
    <div class="box-body">
        @if($survey->user)
        <div class="alert alert-info">
            <strong>Staff Member:</strong> {{ $survey->user->first_name }} {{ $survey->user->last_name }}<br>
            <strong>Email:</strong> {{ $survey->user->email }}<br>
            <strong>Office:</strong> {{ $survey->user->office ? $survey->user->office->name : 'N/A' }}<br>
            <strong>Submitted:</strong> {{ $survey->created_at->format('d M Y, H:i') }}
        </div>
        @endif
        
        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid box-default">
                    <div class="box-header with-border">
                        <h4 class="box-title">General Information</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Branch</th>
                                <td>{{ $survey->branch }}</td>
                            </tr>
                            <tr>
                                <th>Length of Service</th>
                                <td>{{ $survey->length_of_service }}</td>
                            </tr>
                            <tr>
                                <th>BMOS Consistency</th>
                                <td>{{ $survey->bmos_consistency }}</td>
                            </tr>
                            <tr>
                                <th>BMOS Challenges</th>
                                <td>{{ $survey->bmos_challenges ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Branch Needs</th>
                                <td>{{ $survey->branch_needs ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="box box-solid box-default">
                    <div class="box-header with-border">
                        <h4 class="box-title">Work Environment</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Tools & Resources</th>
                                <td>{{ $survey->tools_resources }}</td>
                            </tr>
                            <tr>
                                <th>Operational Challenges</th>
                                <td>{{ $survey->operational_challenges ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Supervisor Support</th>
                                <td>{{ $survey->supervisor_support }}</td>
                            </tr>
                            <tr>
                                <th>Manager Communication</th>
                                <td>{{ $survey->manager_communication }}</td>
                            </tr>
                            <tr>
                                <th>Manager Communication Comments</th>
                                <td>{{ $survey->manager_communication_comments ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="box box-solid box-default">
                    <div class="box-header with-border">
                        <h4 class="box-title">Leadership & Management</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Leadership Challenges</th>
                                <td>{{ $survey->leadership_challenges ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Manager Effectiveness Rating</th>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" style="width: {{ $survey->manager_effectiveness_rating * 10 }}%">
                                            {{ $survey->manager_effectiveness_rating }}/10
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Workload Rating</th>
                                <td>{{ $survey->workload_rating ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="box box-solid box-default">
                    <div class="box-header with-border">
                        <h4 class="box-title">Ethics & Compliance</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Unethical Conduct Observed</th>
                                <td>{{ $survey->unethical_conduct }}</td>
                            </tr>
                            <tr>
                                <th>Policy Violation Instructions</th>
                                <td>{{ $survey->policy_violation_instructions }}</td>
                            </tr>
                            <tr>
                                <th>Policy Violation Description</th>
                                <td>{{ $survey->policy_violation_description ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Top Issues</th>
                                <td>{{ $survey->top_issues ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid box-default">
                    <div class="box-header with-border">
                        <h4 class="box-title">Loan Operations</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Pending Loans Entry</th>
                                <td>{{ $survey->pending_loans_entry }}</td>
                            </tr>
                            <tr>
                                <th>Longest Pending Period</th>
                                <td>{{ $survey->longest_pending_period ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Missed Target Due to Pending Loans</th>
                                <td>{{ $survey->missed_target_due_pending }}</td>
                            </tr>
                            <tr>
                                <th>Pending Target Explanation</th>
                                <td>{{ $survey->pending_target_explanation ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
