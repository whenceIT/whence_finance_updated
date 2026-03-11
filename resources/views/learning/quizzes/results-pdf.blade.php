<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} - Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 10px 0 0;
        }
        .result-summary {
            text-align: center;
            margin: 30px 0;
        }
        .result-summary h2 {
            color: {{ $passed ? '#28a745' : '#dc3545' }};
            font-size: 24px;
            margin-bottom: 10px;
        }
        .score {
            font-size: 48px;
            font-weight: bold;
            color: {{ $passed ? '#28a745' : '#dc3545' }};
            margin: 20px 0;
        }
        .score-details {
            margin: 10px 0;
            font-size: 18px;
            color: #666;
        }
        .questions {
            margin: 30px 0;
        }
        .question {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fafafa;
        }
        .question-header {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .question-text {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .options {
            margin-left: 20px;
        }
        .option {
            margin-bottom: 8px;
            padding: 5px;
        }
        .option.correct {
            background: #d4edda;
            color: #155724;
            padding: 8px;
            border-radius: 4px;
        }
        .option.selected {
            background: #fff3cd;
            color: #856404;
            padding: 8px;
            border-radius: 4px;
        }
        .option.incorrect {
            background: #f8d7da;
            color: #721c24;
            padding: 8px;
            border-radius: 4px;
        }
        .option-label {
            font-weight: bold;
            margin-right: 8px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            margin: 20px 0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: {{ $percentage }}%;
            background: {{ $passed ? '#28a745' : '#dc3545' }};
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quiz Results</h1>
            <p>{{ $quiz->title }}</p>
            <p>{{ $quiz->topic->trainingMaterial->title ?? 'Course' }} - {{ $quiz->topic->topic_name }}</p>
        </div>

        <div class="result-summary">
            <h2>{{ $passed ? 'Congratulations!' : 'Keep Trying!' }}</h2>
            <div class="score">{{ $percentage }}%</div>
            <div class="score-details">{{ $score }} out of {{ $totalPoints }} points</div>
            <div class="score-details">Passing score: {{ $quiz->passing_score }}%</div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="score-details">
                <span class="badge {{ $passed ? 'badge-success' : 'badge-danger' }}">
                    {{ $passed ? 'Passed' : 'Failed' }}
                </span>
            </div>
        </div>

        <div class="questions">
            @foreach($quiz->questions as $qIndex => $question)
                <div class="question">
                    <div class="question-header">
                        Question {{ $qIndex + 1 }} 
                        <span style="color: #666; font-size: 14px;">({{ $question->points }} point{{ $question->points > 1 ? 's' : '' }})</span>
                    </div>
                    <div class="question-text">{{ $question->question }}</div>
                    <div class="options">
                        @foreach($question->options as $oIndex => $option)
                            <div class="option {{ 
                                $option->is_correct ? 'correct' : 
                                ($results[$question->id]['selected_option'] == $option->id ? 'selected' : '') 
                            }}">
                                <span class="option-label">{{ chr(65 + $oIndex) }}.</span> {{ $option->option_text }}
                                @if($option->is_correct)
                                    <span style="margin-left: 10px; color: #28a745;">✓ Correct</span>
                                @elseif($results[$question->id]['selected_option'] == $option->id)
                                    <span style="margin-left: 10px; color: #dc3545;">✗ Incorrect</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
            <p>Quiz Attempt #{{ $attempt->id }}</p>
        </div>
    </div>
</body>
</html>
