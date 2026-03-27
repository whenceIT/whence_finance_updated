@php
    $unviewedTopics = \App\Models\GeneralTopic::getUnviewedForCurrentUser(5);
@endphp

@if(isset($unviewedTopics) && count($unviewedTopics) > 0)
    <!-- Training Hub Advisor - Slide-in Bottom Sheet -->
    <div id="trainingHubAdvisor" class="training-hub-advisor-sheet">
        <div class="advisor-header" onclick="toggleTrainingHubAdvisor()">
            <div class="advisor-title">
                <div class="logo-container">
                    <svg class="learning-logo" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="30" fill="url(#grad1)" stroke="white" stroke-width="2"/>
                        <path d="M20 44V24L32 18L44 24V44L32 50L20 44Z" fill="white" opacity="0.9"/>
                        <path d="M32 18V38" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M20 24L32 30L44 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="32" cy="38" r="4" fill="#FFD700"/>
                        <defs>
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#667eea"/>
                                <stop offset="100%" style="stop-color:#764ba2"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="title-text">
                    <h4>Training Hub Advisor</h4>
                    <span class="subtitle">Your personalized learning journey</span>
                </div>
                <div class="toggle-indicator">
                    <i class="fa fa-chevron-up"></i>
                </div>
            </div>
            <button type="button" class="advisor-close" onclick="event.stopPropagation(); toggleTrainingHubAdvisor()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="advisor-content">
            <div class="content-badge">
                <i class="fa fa-lightbulb-o"></i>
                <span>{{ isset($unviewedTopics) ? count($unviewedTopics) : 0 }} new topics to explore</span>
            </div>
            <p class="intro-text">Based on your position and current ledger performance, here are training topics you haven't viewed yet:</p>
            <ul class="advisor-topics-list">
            @foreach($unviewedTopics as $topic)
                <li class="topic-item">
                    <a href="{{ url('/learning/general-uploads?topic=' . $topic->id) }}" class="topic-link">
                        <div class="topic-icon">
                            <i class="fa fa-book"></i>
                        </div>
                        <div class="topic-details">
                            <span class="topic-name">{{ $topic->name }}</span>
                            @if($topic->description)
                                <span class="topic-desc">{{ $topic->description }}</span>
                            @endif
                        </div>
                        <div class="topic-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>
                </li>
            @endforeach
            </ul>
        </div>
        <div class="advisor-footer">
            <a href="{{ url('/learning') }}" class="view-all-link">
                <span>View all training materials</span>
                <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <style>
        /* Slide-in Bottom Sheet Styles */
        .training-hub-advisor-sheet {
            position: fixed;
            bottom: -500px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -20px 60px rgba(102, 126, 234, 0.25);
            z-index: 9999;
            transition: bottom 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            overflow: hidden;
        }

        .training-hub-advisor-sheet.show {
            bottom: 0;
        }

        /* Collapsed state - only header visible */
        .training-hub-advisor-sheet.collapsed {
            bottom: -460px;
        }

        .advisor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
        }

        .advisor-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-container {
            flex-shrink: 0;
        }

        .learning-logo {
            width: 48px;
            height: 48px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .title-text h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .toggle-indicator {
            color: white;
            font-size: 14px;
            transition: transform 0.3s ease;
            margin-left: 10px;
        }

        .training-hub-advisor-sheet.collapsed .toggle-indicator {
            transform: rotate(180deg);
        }

        .subtitle {
            font-size: 12px;
            opacity: 0.85;
            display: block;
            margin-top: 2px;
        }

        .advisor-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .advisor-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .advisor-content {
            padding: 20px 24px;
            background: #f8f9ff;
        }

        .content-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .content-badge i {
            color: #FFD700;
        }

        .intro-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .advisor-topics-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .topic-item {
            margin-bottom: 10px;
            opacity: 0;
            animation: slideUp 0.4s ease forwards;
        }

        .topic-item:nth-child(1) { animation-delay: 0.1s; }
        .topic-item:nth-child(2) { animation-delay: 0.2s; }
        .topic-item:nth-child(3) { animation-delay: 0.3s; }
        .topic-item:nth-child(4) { animation-delay: 0.4s; }
        .topic-item:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .topic-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: white;
            border-radius: 14px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .topic-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            border-color: #667eea40;
        }

        .topic-icon {
            width: 15px;
            height: 15px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .topic-icon i {
            color: white;
            font-size: 16px;
        }

        .topic-details {
            flex: 1;
            min-width: 0;
        }

        .topic-name {
            display: block;
            color: #1f2937;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .topic-desc {
            display: block;
            color: #9ca3af;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topic-arrow {
            color: #d1d5db;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .topic-link:hover .topic-arrow {
            color: #667eea;
            transform: translateX(4px);
        }

        .advisor-footer {
            padding: 16px 24px 20px;
            background: #f8f9ff;
            border-top: 1px solid #e5e7eb;
        }

        .view-all-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .view-all-link:hover {
            color: #764ba2;
        }

        .view-all-link i {
            transition: transform 0.3s ease;
        }

        .view-all-link:hover i {
            transform: translateX(4px);
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            .training-hub-advisor-sheet {
                max-width: 100%;
                border-radius: 20px 20px 0 0;
            }

            .advisor-header {
                padding: 16px 20px;
            }

            .learning-logo {
                width: 40px;
                height: 40px;
            }

            .title-text h4 {
                font-size: 16px;
            }

            .advisor-content {
                padding: 16px 20px;
            }

            .topic-link {
                padding: 12px 14px;
            }

            .topic-icon {
                width: 36px;
                height: 36px;
            }
        }
    </style>

    <script>
        // Open the slide-in sheet on page load with animation
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var sheet = document.getElementById('trainingHubAdvisor');
                if (sheet) {
                    sheet.classList.add('show');
                }
            }, 600);
        });

        // Function to close the sheet (collapses to header only)
        function closeTrainingHubAdvisor() {
            toggleTrainingHubAdvisor();
        }

        // Function to toggle the sheet (open/close)
        function toggleTrainingHubAdvisor() {
            var sheet = document.getElementById('trainingHubAdvisor');
            if (sheet) {
                if (sheet.classList.contains('show')) {
                    sheet.classList.remove('show');
                    sheet.classList.add('collapsed');
                } else {
                    sheet.classList.add('show');
                    sheet.classList.remove('collapsed');
                }
            }
        }
    </script>
@endif