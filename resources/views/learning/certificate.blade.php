<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $material->title }} - Certificate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .certificate-container {
            background: white;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            padding: 60px 80px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 2px solid #d4af37;
            border-radius: 4px;
        }

        .certificate-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 1px solid #e0e0e0;
            pointer-events: none;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .certificate-seal {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #d4af37 0%, #f4e4b7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            border: 3px solid #b8941f;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            letter-spacing: 3px;
        }

        .certificate-subtitle {
            font-size: 20px;
            color: #7f8c8d;
            font-style: italic;
        }

        .certificate-body {
            text-align: center;
            margin: 40px 0;
        }

        .certificate-intro {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .certificate-name {
            font-size: 56px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid #d4af37;
            display: inline-block;
            padding: 0 40px 15px;
        }

        .certificate-course {
            font-size: 28px;
            color: #34495e;
            margin: 30px 0 20px;
            font-weight: 600;
        }

        .certificate-description {
            font-size: 18px;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        .certificate-details {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            font-size: 16px;
            color: #34495e;
        }

        .certificate-detail {
            text-align: center;
        }

        .certificate-detail-label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .certificate-detail-value {
            font-style: italic;
        }

        .certificate-footer {
            margin-top: 60px;
            text-align: center;
        }

        .certificate-signatures {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            width: 200px;
            height: 2px;
            background: #34495e;
            margin: 0 auto 10px;
        }

        .signature-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .signature-title {
            font-size: 14px;
            color: #7f8c8d;
            font-style: italic;
        }

        .certificate-badge {
            width: 60px;
            height: 60px;
            margin: 20px auto;
            background: linear-gradient(135deg, #d4af37 0%, #f4e4b7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            border: 2px solid #b8941f;
        }

        .certificate-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(212, 175, 55, 0.08);
            pointer-events: none;
            z-index: 0;
        }

        .certificate-actions {
            margin-top: 40px;
            text-align: center;
        }

        .certificate-actions button {
            background: #34495e;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .certificate-actions button:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .certificate-container {
                box-shadow: none;
                border: none;
                padding: 40px;
                min-height: auto;
            }

            .certificate-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-watermark">CERTIFICATE</div>
        <div class="certificate-border"></div>

        <div class="certificate-header">
            <div class="certificate-seal">
                <i class="fa fa-certificate"></i>
            </div>
            <h1 class="certificate-title">CERTIFICATE</h1>
            <p class="certificate-subtitle">OF ACHIEVEMENT</p>
        </div>

        <div class="certificate-body">
            <p class="certificate-intro">This is to certify that</p>
            
            <h2 class="certificate-name">{{ $targetUser->first_name }} {{ $targetUser->last_name }}</h2>
            
            <p class="certificate-intro">has successfully completed the course</p>
            
            <h3 class="certificate-course">{{ $material->title }}</h3>
            
            <p class="certificate-description">
                {{ $material->description }}
            </p>

            <div class="certificate-details">
                <div class="certificate-detail">
                    <div class="certificate-detail-label">Enrollment Date</div>
                    <div class="certificate-detail-value">{{ $enrollment->enrolled_at->format('F d, Y') }}</div>
                </div>
                <div class="certificate-detail">
                    <div class="certificate-detail-label">Completion Date</div>
                    <div class="certificate-detail-value">{{ $enrollment->completed_at ? $enrollment->completed_at->format('F d, Y') : 'In Progress' }}</div>
                </div>
                <div class="certificate-detail">
                    <div class="certificate-detail-label">Progress</div>
                    <div class="certificate-detail-value">{{ $enrollment->progress }}%</div>
                </div>
            </div>
        </div>

        <div class="certificate-footer">
            <div class="certificate-badge">
                <i class="fa fa-check"></i>
            </div>
            
            <div class="certificate-signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">________________________</div>
                    <div class="signature-title">Course Instructor</div>
                </div>
                
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">________________________</div>
                    <div class="signature-title">Training Director</div>
                </div>
            </div>

            @if($isAdmin)
                <p style="font-size: 14px; color: #7f8c8d; margin-top: 20px;">
                    <i class="fa fa-info-circle"></i> This is a preview certificate (Admin only)
                </p>
            @endif
        </div>

        <div class="certificate-actions">
            <button onclick="window.print()">
                <i class="fa fa-print"></i> Print Certificate
            </button>
            <button onclick="window.close()">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></script>
    <script>
        // Auto-focus on print button when page loads
        window.addEventListener('load', function() {
            // Add any additional functionality if needed
        });
    </script>
</body>
</html>
