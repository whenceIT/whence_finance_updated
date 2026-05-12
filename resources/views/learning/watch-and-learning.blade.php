@extends('layouts.learning')

@section('title', 'Watch and Learning - Whence Learn')

@php
$user = Sentinel::getUser();
$isAdmin = $user->roles->first() && $user->roles->first()->id == 1;
@endphp

<!-- Toastr Notifications -->
@if(Session::has('toastr_type'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.{{ Session::get('toastr_type') }}('{{ Session::get('toastr_message') }}', '{{ Session::get('toastr_title', 'Notification') }}');
    });
</script>
@endif

<!-- Video.js CDN -->
<link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet">
<script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>

@section('content')
<!-- Player Container (hidden by default, shown when playing media) -->
<div id="dashboard-player" style="display: none; margin-bottom: 20px;">
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); position: relative;">
        <div style="position: relative; background: #000; height: 600px;" id="player-wrapper">
            <!-- Player content loaded here -->
        </div>
        <!-- Thumbs up animation -->
        <div id="thumbs-up-animation" class="thumbs-up-animation">
            <i class="fa fa-thumbs-up"></i>
        </div>
        <!-- Accessibility ribbon -->
        <div id="accessibility-ribbon" class="accessibility-ribbon">
            LMS is now fully accessible
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

<!-- Document Preview Container (hidden by default) -->
<div id="document-preview" style="display: none; margin-bottom: 20px;">
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow);">
        <div style="padding: 15px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="document-title" style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);"></h3>
                <span id="document-type" style="font-size: 13px; color: var(--text-secondary);"></span>
            </div>
            <button onclick="closeDocumentPreview()" style="padding: 8px 16px; background: var(--light-bg); color: var(--text-secondary); border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fa fa-times"></i> Close
            </button>
        </div>
        <div style="position: relative;" id="document-wrapper">
            <!-- Document content loaded here -->
        </div>
    </div>
</div>
<!-- Professional Header with Gradient -->
<div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); border-radius: 16px; padding: 32px; margin-bottom: 10px; color: white; position: relative; overflow: hidden;">
    @if(isset($topicPoster))
    <div style="position: absolute; top: 0; right: 0; bottom: 0; width: 550px; background-image: url('{{ $topicPoster }}'); background-size: cover; background-position: center; opacity: 0.15; z-index: 0;"></div>
    @endif
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; position: relative; z-index: 1;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px; color: white;">
                <i class="fa fa-play-circle"></i> Watch and Learning
                @if(isset($topicName))
                <span style="font-size: 20px; opacity: 0.9; margin-left: 10px;">- {{ $topicName }}</span>
                @endif
            </h1>
            <p style="font-size: 14px; opacity: 0.9; margin: 0;">
                Access and watch your learning resources.
                @if(isset($topicName))
                <br><span style="font-size: 12px; opacity: 0.8;">Viewing resources for topic: {{ $topicName }}</span>
                @endif
            </p>
            @php
                $totalViews = collect($uploads)->sum('views_count');
            @endphp
            @if($totalViews > 0)
            <div style="display: flex;">
                <div style="font-size: 18px; font-weight: 700; line-height: 1;">{{ \App\Helpers\GeneralHelper::calculate_view_percentage($totalViews) }}% engagement</div>
            </div>
            @endif
        </div>
        @if($isAdmin)
        <a href="{{ url('learning/general-uploads/create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: var(--primary-color); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fa fa-upload"></i> Upload New File
        </a>
        @endif
    </div>
</div>



<!-- Clean Filter Section -->
<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 10px; box-shadow: var(--shadow);">
    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-weight: 500;">
            <i class="fa fa-filter"></i> Filter by Type:
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button onclick="applyFilter('all')" class="filter-btn active" data-type="all" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-th"></i> All
            </button>
            <button onclick="applyFilter('video')" class="filter-btn" data-type="video" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-video-camera"></i> Videos
            </button>
            <button onclick="applyFilter('audio')" class="filter-btn" data-type="audio" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-music"></i> Audios
            </button>
            <button onclick="applyFilter('book')" class="filter-btn" data-type="book" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-book"></i> Books
            </button>
            <button onclick="applyFilter('paper')" class="filter-btn" data-type="paper" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-file-text"></i> Papers
            </button>
            <button onclick="applyFilter('document')" class="filter-btn" data-type="document" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-file"></i> Documents
            </button>
            <button onclick="applyFilter('image')" class="filter-btn" data-type="image" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-image"></i> Images
            </button>
            <button onclick="applyFilter('other')" class="filter-btn" data-type="other" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                <i class="fa fa-ellipsis-h"></i> Other
            </button>
        </div>
    </div>
</div>

<style>
.filter-btn {
    color: var(--text-secondary);
}
.filter-btn:hover {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}
.filter-btn.active {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}

/* Thumbs up animation */
.thumbs-up-animation {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: rgba(39, 174, 96, 0.9);
    color: white;
    padding: 12px 16px;
    border-radius: 50px;
    font-size: 24px;
    opacity: 0;
    transform: translateY(20px) scale(0.8);
    transition: all 0.3s ease;
    pointer-events: none;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.thumbs-up-animation.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.thumbs-up-animation.fly-up {
    animation: flyUp 2s ease-out forwards;
}

@keyframes flyUp {
    0% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateY(-100px) scale(1.2);
    }
}

/* Accessibility ribbon */
.accessibility-ribbon {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%) translateY(-100%);
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    opacity: 0;
    transition: all 0.5s ease;
    z-index: 15;
    white-space: nowrap;
}

.accessibility-ribbon.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.accessibility-ribbon.hide {
    opacity: 0;
    transform: translateX(-50%) translateY(-100%);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<script>
function applyFilter(type) {
    var url = '{{ url('learning/general-uploads/watch-and-learning') }}';
    var params = new URLSearchParams(window.location.search);
    
    if (type !== 'all') {
        params.set('type', type);
    } else {
        params.delete('type');
    }
    
    var queryString = params.toString();
    if (queryString) {
        url += '?' + queryString;
    }
    
    window.location.href = url;
}

// Set current filter from URL
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var type = urlParams.get('type') || 'all';
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-type') === type) {
            btn.classList.add('active');
        }
    });
});
</script>


<!-- Files Grid -->
    <div class="courses-grid" id="uploads-grid">
    @forelse($uploads as $upload)
    <div class="course-card" style="position: relative;">
        <div class="course-image" style="background: {{ $upload->type_color }}; {{ $upload->poster ? 'background: none;' : '' }}">
            @if($upload->poster)
                <img src="{{ $upload->poster }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $upload->name }}">
            @else
                <i class="fa {{ $upload->icon }}" style="font-size: 48px;"></i>
            @endif
        </div>
        <div class="course-body" style="padding-bottom: 16px;">
            <span class="course-category">{{ ucfirst($upload->type) }}</span>
            <h3 class="course-title" style="font-size: 15px;">{{ $upload->name }}</h3>
            <div class="course-meta">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">
                        <i class="fa fa-eye"></i> {{ \App\Helpers\GeneralHelper::calculate_view_percentage($upload->views_count ?? 0) }}% viewers
                    </span>                    
                    <span style="color: var(--text-secondary); font-size: 12px;">
                        {{ $upload->created_at->toFormattedDateString() }}
                    </span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button onclick="playMedia({{ json_encode($upload->type) }}, {{ json_encode($upload->path) }}, {{ json_encode($upload->name) }}, {{ json_encode($upload->formatted_size ?? 'N/A') }}, {{ json_encode($upload->poster ?? '') }}, {{ $upload->id }})" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 12px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                        <i class="fa fa-play"></i> Watch / View
                    </button>
                    @if($isAdmin)
                    <a href="{{ url('learning/general-uploads/' . $upload->id . '/edit') }}" style="width: 36px; height: 36px; border-radius: 6px; background: var(--light-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-primary); text-decoration: none; transition: background 0.2s;" title="Edit">
                        <i class="fa fa-edit" style="color: var(--primary-color);"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: var(--shadow);">
        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fa fa-play-circle" style="font-size: 48px; color: white;"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
            No Resources Found
        </h2>
        <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto 24px; line-height: 1.6;">
            There are no resources available for this topic yet. Check back later or explore other topics.
        </p>
    </div>
    @endforelse
</div>

<script>
// Variables for tracking engagement time
let currentUploadId = null;
let startTime = null;
let hasIncremented = false;
let hasCompletedEngagement = false;
let engagementTimer = null;
let ENGAGEMENT_THRESHOLD_MS = 250000; // Default 4 minutes, will be adjusted based on media duration

// Function to start engagement tracking
function startEngagementTracking(uploadId) {
    if (currentUploadId !== uploadId) {
        stopEngagementTracking();
        currentUploadId = uploadId;
        startTime = Date.now();
        hasIncremented = false;
        hasCompletedEngagement = false;

        // Check every 5 seconds if engagement threshold has passed, then increment with actual accumulated duration
        engagementTimer = setInterval(() => {
            console.log('Checking engagement time for upload ID:', uploadId);
            console.log('hasIncremented:', hasIncremented, 'startTime:', startTime, 'elapsed (ms):', (Date.now() - startTime), 'threshold (ms):', ENGAGEMENT_THRESHOLD_MS);
            console.log('Condition result:', !hasIncremented && startTime && (Date.now() - startTime) >= ENGAGEMENT_THRESHOLD_MS);
            if (!hasIncremented && startTime && (Date.now() - startTime) >= ENGAGEMENT_THRESHOLD_MS) {
                const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
                incrementView(uploadId, {duration: elapsedSeconds, opened: true});
                hasIncremented = true;
                showThumbsUpAnimation();
            }
            // else continue to update the view duration with elapsedSeconds on the server every 5 seconds if the user is still engaged
            else if (startTime) {
                const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
                if (elapsedSeconds > 0) {
                    incrementView(uploadId, {duration: elapsedSeconds});
                }
            }
        }, 5000);
    }
}

// Function to stop engagement tracking
function stopEngagementTracking() {
    if (engagementTimer) {
        clearInterval(engagementTimer);
        engagementTimer = null;
    }

    if (currentUploadId && startTime && hasIncremented && !hasCompletedEngagement) {
        const totalElapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
        const thresholdSeconds = Math.floor(ENGAGEMENT_THRESHOLD_MS / 1000);
        const extraSeconds = Math.max(0, totalElapsedSeconds - thresholdSeconds);
        if (extraSeconds > 0) {
            incrementView(currentUploadId, {duration: extraSeconds});
        }
    }

    currentUploadId = null;
    startTime = null;
    hasIncremented = false;
    hasCompletedEngagement = false;
}

// Function to track completion for uploads
function handleUploadCompletion(uploadId) {
    if (!currentUploadId || hasCompletedEngagement) {
        return;
    }

    hasCompletedEngagement = true;
    const totalElapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
    const payload = {opened: true, completion_status: 'completed'};

    if (!hasIncremented) {
        payload.duration = totalElapsedSeconds;
        hasIncremented = true;
    } else {
        const thresholdSeconds = Math.floor(ENGAGEMENT_THRESHOLD_MS / 1000);
        const extraSeconds = Math.max(0, totalElapsedSeconds - thresholdSeconds);
        if (extraSeconds > 0) {
            payload.duration = extraSeconds;
        }
    }

    incrementView(uploadId, payload);
}

// Function to increment view count and update engagement data
function incrementView(uploadId, payload = {duration: 0, opened: true}) {
    fetch('{{ url('learning/general-uploads') }}/' + uploadId + '/increment-view', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDailyLearning();
        }
    })
    .catch(error => console.error('Error incrementing view:', error));
}


// Function to update daily learning
function updateDailyLearning() {
    fetch('{{ url('learning/general-uploads/update-daily-learning') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Daily learning updated');
        }
    })
    .catch(error => console.error('Error updating daily learning:', error));
}

// Play media inline (YouTube-like)
function playMedia(type, path, name, size, poster = '', uploadId) {
    // Set engagement threshold based on media type
    if (type === 'document' || type === 'pdf') {
        ENGAGEMENT_THRESHOLD_MS = 600000; // 10 minutes for documents
    } else {
        ENGAGEMENT_THRESHOLD_MS = 420000; // Default 7 minutes for video/audio, adjusted later if shorter
    }

    // Start engagement tracking
    startEngagementTracking(uploadId);

    var ext = name.split('.').pop().toLowerCase();
    var isOfficeDoc = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(ext);
    var isPDF = ext === 'pdf';

    // For documents (DOCX, PPT, PDF), use separate document preview container
    if (isOfficeDoc || isPDF) {
        showDocumentPreview(type, path, name, size, uploadId);
        return;
    }
    
    // For other media types, use the original player container
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
            <video id="dashboard-video-player" class="video-js vjs-big-play-centered vjs-theme-city" controls preload="auto" style="width: 100%; height: 100%;" ${posterAttr}>
                <source src="${path}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        `;
        const videoPlayer = videojs('dashboard-video-player', {
            controls: true,
            autoplay: true,
            preload: 'auto',
            fluid: true,
            fill: true,
            playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2]
        });

        videoPlayer.ready(() => {
            const duration = videoPlayer.duration();
            if (duration > 0) {
                ENGAGEMENT_THRESHOLD_MS = Math.min(ENGAGEMENT_THRESHOLD_MS, duration * 1000 / 2); // Half the duration
            }
        });

        var videoElement = document.getElementById('dashboard-video-player');
        if (videoElement) {
            videoElement.addEventListener('ended', function() {
                handleUploadCompletion(uploadId);
            });
        }
    } else if (type === 'audio') {
        // Audio player with enhanced custom styling
        wrapper.style.background = 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)';
        wrapper.style.padding = '40px 20px';
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        wrapper.innerHTML = `
            <div style="text-align: center; width: 100%; max-width: 600px;">
                <div style="width: 140px; height: 140px; background: rgba(255,255,255,0.1); border-radius: 50%; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center; animation: pulse 2s infinite;">
                    <i class="fa fa-music" style="font-size: 56px; color: var(--primary-color);"></i>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <audio id="dashboard-audio-player" controls style="width: 100%;">
                        <source src="${path}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        `;

        const audioElement = document.getElementById('dashboard-audio-player');
        if (audioElement) {
            audioElement.addEventListener('loadedmetadata', () => {
                const duration = audioElement.duration;
                if (duration > 0) {
                    ENGAGEMENT_THRESHOLD_MS = Math.min(ENGAGEMENT_THRESHOLD_MS, duration * 1000 / 2);
                }
            });
            audioElement.addEventListener('ended', function() {
                handleUploadCompletion(uploadId);
            });
        }
        videojs('dashboard-audio-player', {
            controls: true,
            autoplay: false,
            preload: 'auto',
            fluid: true,
            playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 2],
            plugins: {
                volumeBar: {
                    vertical: true
                }
            }
        });
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

// Show document preview in separate container
function showDocumentPreview(type, path, name, size, uploadId) {
    // Start engagement tracking
    startEngagementTracking(uploadId);

    var ext = name.split('.').pop().toLowerCase();
    var isOfficeDoc = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(ext);
    var isPDF = ext === 'pdf';

    // Show document preview container
    var documentContainer = document.getElementById('document-preview');
    documentContainer.style.display = 'block';
    documentContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Update title and type
    document.getElementById('document-title').textContent = name;
    document.getElementById('document-type').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' • ' + size;
    
    // Get document wrapper
    var wrapper = document.getElementById('document-wrapper');
    wrapper.innerHTML = '';
    
    if (isOfficeDoc || isPDF) {
        // Choose appropriate viewer based on file type
        var viewerUrl = '';
        if (isOfficeDoc) {
            viewerUrl = `https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(path)}`;
        } else if (isPDF) {
            viewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(path)}&embedded=true`;
        }
        
        wrapper.innerHTML = `
            <div style="position: relative;">
                <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                    <button onclick="fullscreenDocumentPreview()" style="padding: 8px 16px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; transition: background 0.3s;">
                        <i class="fa fa-expand"></i> Fullscreen
                    </button>
                </div>
                <iframe 
                    src="${viewerUrl}"
                    style="width:100%;height:800px;border:none;"
                    allowfullscreen
                    >
                </iframe>
            </div>
        `;
    } else {
        // Preview not available for other document types
        wrapper.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px;">
                <i class="fa fa-file" style="font-size: 80px; color: var(--text-secondary); margin-bottom: 20px;"></i>
                <p style="color: var(--text-secondary);">Preview not available for this file type.</p>
            </div>
        `;
    }
}

// Close document preview
function closeDocumentPreview() {
    var documentContainer = document.getElementById('document-preview');
    documentContainer.style.display = 'none';

    // Clear document wrapper
    var wrapper = document.getElementById('document-wrapper');
    wrapper.innerHTML = '';

    // Stop engagement tracking
    stopEngagementTracking();
}

// Fullscreen document preview
function fullscreenDocumentPreview() {
    var wrapper = document.getElementById('document-wrapper');
    var iframe = wrapper.querySelector('iframe');
    
    if (iframe && iframe.requestFullscreen) {
        iframe.requestFullscreen();
    } else if (iframe && iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen();
    } else if (iframe && iframe.mozRequestFullScreen) {
        iframe.mozRequestFullScreen();
    } else if (iframe && iframe.msRequestFullscreen) {
        iframe.msRequestFullscreen();
    }
}

function closePlayer() {
    var playerContainer = document.getElementById('dashboard-player');
    playerContainer.style.display = 'none';

    // Stop any playing media
    var wrapper = document.getElementById('player-wrapper');
    wrapper.innerHTML = '';

    // Stop engagement tracking
    stopEngagementTracking();
}

// Function to show thumbs up animation and accessibility ribbon
function showThumbsUpAnimation() {
    // Show thumbs up
    const animation = document.getElementById('thumbs-up-animation');
    if (animation) {
        animation.classList.add('show');
        animation.classList.remove('fly-up');
        setTimeout(() => {
            animation.classList.add('fly-up');
            animation.classList.remove('show');
            setTimeout(() => {
                animation.classList.remove('fly-up');
            }, 2000); // Animation duration
        }, 1000); // Show for 1 second before flying up
    }

    // Show accessibility ribbon
    const ribbon = document.getElementById('accessibility-ribbon');
    if (ribbon) {
        ribbon.classList.add('show');
        ribbon.classList.remove('hide');
        setTimeout(() => {
            ribbon.classList.add('hide');
            ribbon.classList.remove('show');
        }, 4000); // Show for 4 seconds
    }
}

// Stop tracking when page unloads
window.addEventListener('beforeunload', function() {
    stopEngagementTracking();
});

// Ensure playMedia is globally available
window.playMedia = playMedia;

@if(isset($autoPlayUpload))
    document.addEventListener('DOMContentLoaded', function() {
        playMedia(
            '{{ $autoPlayUpload->type }}', 
            '{{ $autoPlayUpload->path }}', 
            '{{ addslashes($autoPlayUpload->name) }}', 
            '{{ $autoPlayUpload->formatted_size ?? 'N/A' }}', 
            '{{ $autoPlayUpload->poster ?? '' }}', 
            {{ $autoPlayUpload->id }}
        );
    });
@endif
</script>
@endsection
