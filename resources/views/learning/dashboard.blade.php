@extends('layouts.learning')

@section('title', 'Dashboard - Whence Learn')

<!-- Video.js CDN -->
<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.css" />

@section('content')

<!-- Player Container (hidden by default, shown when playing media) -->
<div id="dashboard-player" style="display: none; margin-bottom: 20px;">
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow);">
        <div style="position: relative; background: #000; min-height: 400px;" id="player-wrapper">
            <!-- Player content loaded here -->
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="player-title" style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);"></h3>
                <span id="player-type" style="font-size: 13px; color: var(--text-secondary);"></span>
            </div>
            <button onclick="closePlayer()" style="padding: 8px 16px; background: var(--light-bg); color: var(--text-secondary); border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div style="margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">
    <button onclick="switchTab('courses')" id="tab-courses" style="padding: 10px 24px; background: var(--primary-color); color: white; border: none; border-radius: 6px 6px 0 0; cursor: pointer; font-weight: 600; font-size: 15px;">
        <i class="fa fa-book"></i> Courses
    </button>
    <button onclick="switchTab('uploads')" id="tab-uploads" style="padding: 10px 24px; background: transparent; color: var(--text-secondary); border: none; border-radius: 6px 6px 0 0; cursor: pointer; font-weight: 500; font-size: 15px;">
        <i class="fa fa-cloud-upload"></i> General Uploads
    </button>
</div>

<!-- Courses Section -->
<div id="courses-section">
    <!-- Continue Learning Section -->
    @if($stats['in_progress'] > 0)
    <div style="margin-bottom: 40px;">
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
            <i class="fa fa-play-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
            Continue Learning
        </h2>
        <div class="courses-grid">
            @foreach($courses as $course)
                @if($course['enrolled'] && $course['progress'] > 0 && $course['progress'] < 100)
                <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
                    <div class="course-image">
                        <i class="fa {{ $course['icon'] }}"></i>
                    </div>
                    <div class="course-body">
                        <span class="course-category">{{ $course['category'] }}</span>
                        <h3 class="course-title">{{ $course['title'] }}</h3>
                        <p class="course-description">{{ $course['description'] }}</p>
                        <div class="course-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                            </div>
                            <div class="progress-text">{{ $course['progress'] }}% Complete</div>
                        </div>
                        <div class="course-meta">
                            <div class="course-stats">
                                <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
                                <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- All Courses Section -->
    <div>
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
            <i class="fa fa-graduation-cap" style="color: var(--primary-color); margin-right: 10px;"></i>
            All Courses
        </h2>
        <div class="courses-grid">
            @foreach($courses as $course)
            <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
                <div class="course-image">
                    <i class="fa {{ $course['icon'] }}"></i>
                </div>
                <div class="course-body">
                    <span class="course-category">{{ $course['category'] }}</span>
                    <h3 class="course-title">{{ $course['title'] }}</h3>
                    <p class="course-description">{{ $course['description'] }}</p>
                    @if($course['enrolled'])
                    <div class="course-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                        </div>
                    </div>
                    @endif
                        <div class="course-meta">
                            <div class="course-stats">
                                <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
                                <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                            </div>
                            @if(!$course['enrolled'])
                            <button onclick="event.stopPropagation(); openEnrollModal({{ $course['id'] }}, '{{ $course['title'] }}')" style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                                Enroll Now
                            </button>
                            @endif
                        </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Welcome Message for New Users -->
    @if(!$courses)
    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; margin-top: 40px;">
        <i class="fa fa-rocket" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
            Welcome to Whence Learn!
        </h2>
        <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 30px;">
            Start your learning journey by enrolling in one of our courses. Develop new skills and advance your career with our comprehensive training programs.
        </p>
        <a href="{{ url('learning/courses') }}" style="display: inline-block; background: var(--primary-color); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
            Browse All Courses
        </a>
    </div>
    @endif
</div>

<!-- General Uploads Section (YouTube-like with inline player) -->
<div id="uploads-section" style="display: none;">
    <!-- Filter by Type -->
    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button onclick="filterUploads('all')" class="filter-btn active" data-filter="all" style="padding: 8px 16px; background: var(--primary-color); color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            All
        </button>
        <button onclick="filterUploads('video')" class="filter-btn" data-filter="video" style="padding: 8px 16px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            <i class="fa fa-video-camera"></i> Videos
        </button>
        <button onclick="filterUploads('audio')" class="filter-btn" data-filter="audio" style="padding: 8px 16px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            <i class="fa fa-headphones"></i> Audio
        </button>
        <button onclick="filterUploads('book')" class="filter-btn" data-filter="book" style="padding: 8px 16px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            <i class="fa fa-book"></i> Books
        </button>
        <button onclick="filterUploads('paper')" class="filter-btn" data-filter="paper" style="padding: 8px 16px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            <i class="fa fa-file-text"></i> Papers
        </button>
        <button onclick="filterUploads('document')" class="filter-btn" data-filter="document" style="padding: 8px 16px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 500;">
            <i class="fa fa-file-word-o"></i> Documents
        </button>
    </div>

    <!-- Uploads Grid (YouTube-style) - click to play inline -->
    <div class="courses-grid" id="uploads-grid">
        @forelse($uploads as $upload)
        <div class="course-card upload-card" data-type="{{ $upload->type }}" 
             data-id="{{ $upload->id }}"
             data-path="{{ $upload->path }}"
             data-name="{{ $upload->name }}"
             data-type="{{ $upload->type }}"
             data-size="{{ $upload->formatted_size }}"
             data-poster="{{ $upload->poster ?? '' }}"
             onclick="playMedia('{{ $upload->type }}', '{{ $upload->path }}', '{{ addslashes($upload->name) }}', '{{ $upload->formatted_size }}', '{{ $upload->poster ?? '' }}')"
             style="cursor: pointer;">
            <div class="course-image" style="{{ $upload->type === 'video' && $upload->poster ? 'background-image: url(' . $upload->poster . '); background-size: cover; background-position: center;' : 'background: ' . $upload->type_color . ';' }} position: relative;">
                @if($upload->type === 'video')
                <!-- Video Play Button Overlay -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2);">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;">
                        <i class="fa fa-play" style="color: var(--primary-color); font-size: 24px; margin-left: 4px;"></i>
                    </div>
                </div>
                @elseif($upload->type === 'audio')
                <!-- Audio Play Button Overlay -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2);">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;">
                        <i class="fa fa-headphones" style="color: var(--secondary-color); font-size: 24px;"></i>
                    </div>
                </div>
                @elseif(in_array($upload->type, ['book', 'paper']))
                <!-- Document Preview Icon -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2);">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.9); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <i class="fa fa-file-pdf-o" style="color: #e74c3c; font-size: 28px;"></i>
                    </div>
                </div>
                @elseif($upload->type === 'document')
                <!-- Document Preview Icon -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2);">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.9); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <i class="fa fa-file-word-o" style="color: #3498db; font-size: 28px;"></i>
                    </div>
                </div>
                @else
                <i class="fa {{ $upload->icon }}"></i>
                @endif
            </div>
            <div class="course-body">
                <span class="course-category">{{ ucfirst($upload->type) }}</span>
                <h3 class="course-title" style="font-size: 14px; line-height: 1.4;">{{ $upload->name }}</h3>
                <div class="course-meta" style="margin-top: 8px;">
                    <div class="course-stats">
                        <span><i class="fa fa-database"></i> {{ $upload->formatted_size }}</span>
                        <span>{{ $upload->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
            <i class="fa fa-cloud-upload" style="font-size: 64px; color: var(--text-secondary); margin-bottom: 20px;"></i>
            <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
                No Uploads Yet
            </h2>
            <p style="color: var(--text-secondary); font-size: 16px;">
                There are no general uploads available. Check back later or contact your administrator.
            </p>
        </div>
        @endforelse
    </div>
</div>

<script>
var currentTab = 'courses';
var currentFilter = 'all';

function switchTab(tab) {
    currentTab = tab;
    
    // Update tab buttons
    document.getElementById('tab-courses').style.background = tab === 'courses' ? 'var(--primary-color)' : 'transparent';
    document.getElementById('tab-courses').style.color = tab === 'courses' ? 'white' : 'var(--text-secondary)';
    document.getElementById('tab-uploads').style.background = tab === 'uploads' ? 'var(--primary-color)' : 'transparent';
    document.getElementById('tab-uploads').style.color = tab === 'uploads' ? 'white' : 'var(--text-secondary)';
    
    // Show/hide sections
    document.getElementById('courses-section').style.display = tab === 'courses' ? 'block' : 'none';
    document.getElementById('uploads-section').style.display = tab === 'uploads' ? 'block' : 'none';
}

function filterUploads(type) {
    currentFilter = type;
    
    // Update filter buttons
    var buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(function(btn) {
        if (btn.dataset.filter === type) {
            btn.style.background = 'var(--primary-color)';
            btn.style.color = 'white';
            btn.style.borderColor = 'var(--primary-color)';
        } else {
            btn.style.background = 'white';
            btn.style.color = 'var(--text-primary)';
            btn.style.borderColor = 'var(--border-color)';
        }
    });
    
    // Filter uploads
    var cards = document.querySelectorAll('.upload-card');
    cards.forEach(function(card) {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Play media inline (YouTube-like)
function playMedia(type, path, name, size, poster = '') {
    // Show player container
    var playerContainer = document.getElementById('dashboard-player');
    playerContainer.style.display = 'block';
    playerContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Update title and type
    document.getElementById('player-title').textContent = name;
    document.getElementById('player-type').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' • ' + size;
    
    // Get player wrapper
    var wrapper = document.getElementById('player-wrapper');
    wrapper.innerHTML = '';
    
    if (type === 'video') {
        // Video player with poster if available
        var posterAttr = poster ? `poster="${poster}"` : '';
        wrapper.innerHTML = `
            <video id="dashboard-video-player" class="video-js vjs-big-play-centered vjs-theme-city" controls preload="auto" style="width: 100%; height: 400px;" ${posterAttr}>
                <source src="${path}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        `;
        videojs('dashboard-video-player', {
            controls: true,
            autoplay: true,
            preload: 'auto',
            fluid: true,
            playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2]
        });
    } else if (type === 'audio') {
        // Audio player with custom styling
        wrapper.style.background = 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)';
        wrapper.style.padding = '60px 20px';
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        wrapper.innerHTML = `
            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-music" style="font-size: 48px; color: var(--primary-color);"></i>
                </div>
                <audio id="dashboard-audio-player" controls style="width: 100%; max-width: 500px;">
                    <source src="${path}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        `;
        videojs('dashboard-audio-player', {
            controls: true,
            autoplay: true,
            preload: 'auto',
            fluid: true
        });
    } else if (type === 'book' || type === 'paper') {
        // PDF/Document preview using Google Docs viewer
        wrapper.innerHTML = `
            <div style="position: relative; height: 100%;">
                <iframe 
                    src="https://docs.google.com/gview?url=${encodeURIComponent(path)}&embedded=true"
                    style="width:100%;height:600px;border:none;"
                    allowfullscreen>
                </iframe>
            </div>
        `;
    } else if (type === 'document') {
        // Office document preview
        var ext = name.split('.').pop().toLowerCase();
        if (['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(ext)) {
            wrapper.innerHTML = `
                <div style="position: relative; height: 100%;">
                    <iframe 
                        src="https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(path)}"
                        style="width:100%;height:600px;border:none;"
                        allowfullscreen>
                    </iframe>
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px;">
                    <i class="fa fa-file" style="font-size: 80px; color: var(--text-secondary); margin-bottom: 20px;"></i>
                    <p style="color: var(--text-secondary);">Preview not available for this file type.</p>
                </div>
            `;
        }
    } else if (type === 'image') {
        // Image preview
        wrapper.style.background = '#000';
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        wrapper.innerHTML = `
            <img src="${path}" alt="${name}" style="max-width: 100%; max-height: 600px; object-fit: contain;">
        `;
    } else {
        // Generic preview
        wrapper.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px;">
                <i class="fa fa-file" style="font-size: 80px; color: var(--text-secondary); margin-bottom: 20px;"></i>
                <p style="color: var(--text-secondary);">Preview not available for this file type.</p>
            </div>
        `;
    }
}

function closePlayer() {
    var playerContainer = document.getElementById('dashboard-player');
    playerContainer.style.display = 'none';
    
    // Stop any playing media
    var wrapper = document.getElementById('player-wrapper');
    wrapper.innerHTML = '';
}

// Check for URL parameter to set initial tab
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var tab = urlParams.get('tab');
    if (tab === 'uploads') {
        switchTab('uploads');
    }
});
</script>

<style>
.filter-btn {
    transition: all 0.2s ease;
}
.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection
