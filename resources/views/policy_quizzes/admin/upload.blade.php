@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Upload Questions for {{ $quiz->title }}</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">CSV Upload</h3>
                        <div class="box-tools">
                            <a href="{{ route('admin.policy-quizzes.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Quizzes
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('policy.quizzes.upload.questions', $quiz->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="box-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fa fa-check"></i> {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fa fa-times"></i> {{ session('error') }}
                                </div>
                            @endif
                            
                            <div class="alert alert-info">
                                <h4><i class="fa fa-info-circle"></i> CSV Format Instructions</h4>
                                <p>Upload a CSV file with the following columns:</p>
                                <ul>
                                    <li><strong>Question</strong> (required): The question text</li>
                                    <li><strong>Option A</strong> (required): First option</li>
                                    <li><strong>Option B</strong> (required): Second option</li>
                                    <li><strong>Option C</strong> (required): Third option</li>
                                    <li><strong>Option D</strong> (required): Fourth option</li>
                                    <li><strong>Correct Answer</strong> (required): Must be A, B, C, or D (case insensitive)</li>
                                    <li><strong>Policy Link</strong> (optional): URL to relevant policy document</li>
                                    <li><strong>Explanation</strong> (optional): Explanation of correct answer</li>
                                </ul>
                                <p>Note: Existing questions will be replaced.</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="csv_file">CSV File *</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                <small class="text-muted">Only CSV files (.csv or .txt) are accepted</small>
                            </div>
                            
                            <!-- Current Questions Stats -->
                            <div class="well">
                                <h4>Current Quiz Status</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Quiz Title:</strong> {{ $quiz->title }}</p>
                                        <p><strong>Required Questions:</strong> {{ $quiz->max_questions }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Current Questions:</strong> {{ $quiz->questions()->count() }}</p>
                                        <p><strong>Status:</strong> 
                                            @if($quiz->questions()->count() >= $quiz->max_questions)
                                                <span class="label label-success">Ready</span>
                                            @else
                                                <span class="label label-warning">Need {{ $quiz->max_questions - $quiz->questions()->count() }} more</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Quiz Open:</strong> {{ $quiz->open_date->format('M d, Y H:i') }}</p>
                                        <p><strong>Quiz Close:</strong> {{ $quiz->close_date->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- CSV Template Download -->
                            <div class="well">
                                <h4><i class="fa fa-download"></i> Download Template</h4>
                                <p>Download a template CSV file to get started:</p>
                                <a href="data:text/csv;charset=utf-8,Question,Option A,Option B,Option C,Option D,Correct Answer,Policy Link,Explanation%0AWhat is the company's policy on remote work?,Work from home is allowed,Work only in office,Hybrid model,A,B,%2Fpolicies%2Fremote-work,Remote work requires manager approval%0AHow many sick days per year?,5 days,10 days,15 days,Unlimited,B,%2Fpolicies%2Fleave-policy,Employees get 10 paid sick days%0AWhat is the dress code?,Formal attire,Casual,Business casual,No specific code,C,%2Fpolicies%2Fdress-code,Office requires business casual attire" 
                                   download="quiz_questions_template.csv" class="btn btn-success">
                                    <i class="fa fa-download"></i> Download CSV Template
                                </a>
                            </div>
                            
                            <!-- Sample Preview -->
                            <div class="well">
                                <h4><i class="fa fa-eye"></i> Sample CSV Data</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Question</th>
                                                <th>Option A</th>
                                                <th>Option B</th>
                                                <th>Option C</th>
                                                <th>Option D</th>
                                                <th>Correct Answer</th>
                                                <th>Policy Link</th>
                                                <th>Explanation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>What is the company's policy on remote work?</td>
                                                <td>Work from home is allowed</td>
                                                <td>Work only in office</td>
                                                <td>Hybrid model</td>
                                                <td>A</td>
                                                <td>B</td>
                                                <td>/policies/remote-work</td>
                                                <td>Remote work requires manager approval</td>
                                            </tr>
                                            <tr>
                                                <td>How many sick days per year?</td>
                                                <td>5 days</td>
                                                <td>10 days</td>
                                                <td>15 days</td>
                                                <td>Unlimited</td>
                                                <td>B</td>
                                                <td>/policies/leave-policy</td>
                                                <td>Employees get 10 paid sick days</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-upload"></i> Upload Questions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection