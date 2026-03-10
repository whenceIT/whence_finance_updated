<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management System Introduction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .content h2 {
            color: #3498db;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .content ul {
            margin-bottom: 15px;
            padding-left: 20px;
        }
        .content li {
            margin-bottom: 5px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            color: #777;
            font-size: 14px;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Learning Management System (LMS) for Trainers</h1>
        </div>
        <div class="content">
            <p>Dear Trainers,</p>
            <p>We are pleased to introduce our new Learning Management System (LMS), designed specifically to enhance your training experience and streamline your teaching workflow.</p>
            
            <h2>Key Features:</h2>
            <ul>
                <li><strong>Easy Course Management:</strong> Create and manage courses with a simple, intuitive interface.</li>
                <li><strong>Topic Organization:</strong> Break down courses into manageable topics for better learning outcomes.</li>
                <li><strong>Quiz Creation:</strong> Build interactive quizzes with multiple-choice questions and automatic grading.</li>
                <li><strong>Progress Tracking:</strong> Monitor students' progress and quiz results in real-time.</li>
                <li><strong>Certificate Generation:</strong> Automatically generate completion certificates for successful students.</li>
                <li><strong>Material Upload:</strong> Upload various types of training materials (PDFs, videos, documents, etc.).</li>
                <li><strong>Role-Based Access:</strong> Secure access with different roles for trainers, students, and administrators.</li>
            </ul>
            
            <h2>How to Get Started:</h2>
            <ol>
                <li><strong>Access the System:</strong> Visit <a href="{{ config('app.url') }}/learning" style="color: #3498db;">{{ config('app.url') }}/learning</a></li>
                <li><strong>Login:</strong> Use your existing credentials (same as the main Whence Finance Services system)</li>
                <li><strong>Create a Course:</strong> Click on "Create Course" to start building your training material</li>
                <li><strong>Add Topics:</strong> Break your course into topics and upload relevant materials</li>
                <li><strong>Add Quizzes:</strong> Create quizzes for each topic to assess understanding</li>
                <li><strong>Publish:</strong> Make your course available to students</li>
            </ol>
            
            <h2>Support:</h2>
            <p>We understand that getting familiar with a new system takes time. If you have any questions or need assistance:</p>
            <ul>
                <li>Check the <a href="{{ config('app.url') }}/help" style="color: #3498db;">online help documentation</a></li>
                <li>Contact our support team at support@whencefinance.com</li>
                <li>Schedule a training session with our LMS administrator</li>
            </ul>
            
            <p>We believe this system will significantly improve the training process for both you and your students. Your feedback is invaluable as we continue to enhance and refine the LMS.</p>
            
            <p>Thank you for your continued dedication to training and development.</p>
            
            <p>Sincerely,</p>
            <p>The Whence Finance Services IT Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Whence Finance Services. All rights reserved.</p>
            <p>This email was sent to registered trainers as part of our LMS introduction.</p>
        </div>
    </div>
</body>
</html>
