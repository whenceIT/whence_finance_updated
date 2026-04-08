@extends('layouts.learning')

@section('title', 'Dashboard - Whence Learn')

<!-- Video.js CDN -->
<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.css" />

<style>
/* Tab Pills Styles */
.tab-pills-container {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 3px;
    padding: 15px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.tab-pill {
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f0f0f0;
    color: var(--text-secondary);
}

.tab-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.tab-pill.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
}

.tab-pill i {
    font-size: 12px;
}

/* Search Bar Styles */
.search-container {
    margin-bottom: 25px;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 14px 20px 14px 45px;
    font-size: 15px;
    border: 2px solid var(--border-color);
    border-radius: 50px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: var(--shadow);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 4px 20px rgba(52, 152, 219, 0.2);
}

.search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    font-size: 16px;
}

/* Filter Pills */
.filter-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.filter-pill {
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid var(--border-color);
    border-radius: 50px;
    cursor: pointer;
    background: white;
    color: var(--text-secondary);
    transition: all 0.2s ease;
}

.filter-pill:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.filter-pill.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Unified Content Grid */
.unified-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

/* Content Card (for both courses and uploads) */
.content-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
}

.content-card .card-image {
    height: 160px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content-card .card-image i {
    font-size: 48px;
    color: rgba(255,255,255,0.9);
}

.content-card .card-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 10px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 50px;
    background: rgba(0,0,0,0.7);
    color: white;
}

.content-card .card-body {
    padding: 15px;
}

.content-card .card-category {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--primary-color);
    margin-bottom: 6px;
}

.content-card .card-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.content-card .card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-secondary);
}

.content-card .card-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.content-card .progress-bar {
    height: 4px;
    background: #eee;
    border-radius: 2px;
    margin-top: 12px;
    overflow: hidden;
}

.content-card .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    grid-column: 1 / -1;
}

.no-results i {
    font-size: 64px;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.no-results h3 {
    font-size: 24px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.no-results p {
    color: var(--text-secondary);
    font-size: 15px;
}

/* Content Type Icons Background */
.type-video { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.type-audio { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.type-book { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.type-paper { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.type-document { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.type-course { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }

/* Progress Badge - Sleek design with gradient */
.progress-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #2d5a27 0%, #3d7a32 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(45, 90, 39, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}

.progress-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(45, 90, 39, 0.4);
}

.progress-badge i {
    font-size: 12px;
    opacity: 0.9;
}

/* Topic Views Badge */
.topic-views-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(230, 126, 34, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}

.topic-views-badge i {
    font-size: 12px;
    opacity: 0.9;
}

/* Play Button Overlay */
.play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.content-card:hover .play-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
}

.content-card:hover .play-button {
    transform: scale(1.1);
}

.play-button i {
    color: var(--primary-color);
    font-size: 20px;
    margin-left: 3px;
}

/* Load More Button */
.load-more-container {
    text-align: center;
    padding: 30px 20px;
    grid-column: 1 / -1;
}

.load-more-btn {
    padding: 12px 40px;
    font-size: 14px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
}

.load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.5);
}

.load-more-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.load-more-btn .fa-spinner {
    margin-right: 8px;
}

/* Course Card Enhanced Design */
.course-card {
    position: relative;
    height: 300px;
    overflow: hidden;
}

.course-bg-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}

.course-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(0,0,0,0.7) 0%,
        rgba(0,0,0,0.4) 50%,
        rgba(0,0,0,0.2) 100%
    );
    backdrop-filter: blur(1px);
    -webkit-backdrop-filter: blur(1px);
    z-index: 2;
    display: flex;
    align-items: flex-end;
}

.course-content {
    padding: 20px;
    width: 100%;
    color: white;
    z-index: 3;
}

.course-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 50px;
    background: rgba(255,255,255,0.9);
    color: var(--text-primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    z-index: 4;
}

.course-category {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: rgb(255, 196, 0);
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.course-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-progress {
    margin-bottom: 15px;
}

.course-progress .progress-bar {
    height: 3px;
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
    margin-bottom: 6px;
    overflow: hidden;
}

.course-progress .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #fff, rgba(255,255,255,0.8));
    border-radius: 2px;
}

.course-progress .progress-text {
    font-size: 11px;
    color: rgba(255,255,255,0.9);
    font-weight: 600;
}

.course-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: rgba(255,255,255,0.9);
    font-weight: 500;
}

.meta-item i {
    opacity: 0.8;
}

.course-card .play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 5;
}

.course-card:hover .play-overlay {
    opacity: 1;
}

.course-card:hover .course-overlay {
    background: linear-gradient(
        135deg,
        rgba(0,0,0,0.8) 0%,
        rgba(0,0,0,0.5) 50%,
        rgba(0,0,0,0.3) 100%
    );
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}

/* All Tab Layout - Show both featured and content grid */
#featured-topics-container + .unified-grid {
    margin-top: 20px;
}

/* Topic Folder Card Enhanced Design */
.topic-folder-card {
    position: relative;
    height: 280px;
    overflow: hidden;
}

.topic-bg-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}

.topic-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(0,0,0,0.75) 0%,
        rgba(0,0,0,0.5) 50%,
        rgba(0,0,0,0.25) 100%
    );
    z-index: 2;
    display: flex;
    align-items: flex-end;
}

.topic-content {
    padding: 20px;
    width: 100%;
    color: white;
    z-index: 3;
}

.topic-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 50px;
    background: rgba(255,255,255,0.9);
    color: var(--text-primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    z-index: 4;
}

.topic-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 15px;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.topic-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: auto;
}

.topic-meta .meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.9);
    font-weight: 500;
}

.topic-meta .meta-item i {
    opacity: 0.8;
    font-size: 14px;
}

.topic-folder-card .play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 5;
}

.topic-folder-card:hover .play-overlay {
    opacity: 1;
}

.topic-folder-card .play-button i {
    color: var(--primary-color);
    font-size: 24px;
}

.topic-folder-card:hover .topic-overlay {
    background: linear-gradient(
        135deg,
        rgba(0,0,0,0.85) 0%,
        rgba(0,0,0,0.6) 50%,
        rgba(0,0,0,0.35) 100%
    );
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}
</style>

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

<!-- Document Preview Container (hidden by default) -->
<div id="document-preview" style="display: none; margin-bottom: 20px;">
    <div style="position:absolute;top:0;left:0;width:100%;height:90px;background:#f3f2f1;z-index:9999;pointer-events:auto;"></div>
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
        </div>
      
    </div>      
    <!-- Overlay to hide the toolbar -->
    <!-- Document content loaded here -->
</div>

<!-- Tab Pills Container -->
<div class="tab-pills-container">
    <button onclick="switchTab('featured')" id="tab-featured" class="tab-pill">
        <i class="fa fa-star"></i> Featured Topics
    </button>
    <!-- <button onclick="switchTab('all')" id="tab-all" class="tab-pill">
        <i class="fa fa-th-large"></i> All
    </button> -->
    <button onclick="switchTab('courses')" id="tab-courses" class="tab-pill">
        <i class="fa fa-graduation-cap"></i> Training Courses 
    </button>
    <!-- <button onclick="switchTab('uploads')" id="tab-uploads" class="tab-pill">
        <i class="fa fa-cloud-upload"></i> Uploads
    </button> -->
</div>

<!-- Search Bar -->
<div class="search-container">
    <i class="fa fa-search search-icon"></i>
    <input type="text" id="search-input" class="search-input" placeholder="Search courses, videos, audio, books, documents..." oninput="handleSearch(this.value)">
</div>

<!-- Filter Pills (for uploads) -->
<div class="filter-pills" id="type-filters" style="display: none;">
    <button onclick="filterByType('all')" class="filter-pill active" data-filter="all">All Types</button>
    <button onclick="filterByType('video')" class="filter-pill" data-filter="video">
        <i class="fa fa-video-camera"></i> Videos
    </button>
    <button onclick="filterByType('audio')" class="filter-pill" data-filter="audio">
        <i class="fa fa-headphones"></i> Audio
    </button>
    <button onclick="filterByType('book')" class="filter-pill" data-filter="book">
        <i class="fa fa-book"></i> Books
    </button>
    <button onclick="filterByType('paper')" class="filter-pill" data-filter="paper">
        <i class="fa fa-file-text"></i> Papers
    </button>
    <button onclick="filterByType('document')" class="filter-pill" data-filter="document">
        <i class="fa fa-file-word-o"></i> Documents
    </button>
</div>


<!-- Content Count -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 id="content-title" style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin: 0;">
        <i class="fa fa-th-large" style="color: var(--primary-color); margin-right: 10px;"></i>
        Featured Topics
    </h2>
    <span id="content-count" style="font-size: 13px; color: var(--text-secondary);">
    Showing {{ $isFeaturedTab ? count($topicsWithUploads) : (count($courses) + $uploads->count()) }} items
    </span>
</div>

<!-- Featured Topics Container (for featured tab) -->
<div id="featured-topics-container">
        <!-- General Topics as Folders -->
        @foreach($topicsWithUploads as $topic)
        <div class="col-12 col-md-6 col-lg-3 topic-card" style="margin-bottom: 20px;" data-title="{{ $topic['name'] }}">
            <div class="content-card topic-folder-card" onclick='window.location.href="{{ route('learning.watch-and-learning', ['topic' => $topic['id']]) }}"'>
                <!-- Full Background Image or Gradient -->
                <div class="topic-bg-image" style="{{ isset($topic['poster']) && $topic['poster'] ? 'background-image: url(\'' . $topic['poster'] . '\');' : 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' }}">
                    @if(!isset($topic['poster']) || !$topic['poster'])
                        <i class="fa fa-folder-open" style="font-size: 48px; color: rgba(255,255,255,0.9);"></i>
                    @endif
                </div>

                <!-- Gradient Blur Overlay -->
                <div class="topic-overlay">
                    <div class="topic-content">
                        <!-- Badge -->
                        <div class="topic-badge">Topic</div>

                        <!-- Title -->
                        <h3 class="topic-title">{{ $topic['name'] }}</h3>

                        <!-- Meta Information -->
                        <div class="topic-meta">
                            <div class="meta-item">
                                    <i class="fa fa-file"></i>
                                    <span>{{ count($topic['uploads']) }} resources</span>
                            </div>
                            @php
                                $totalTopicViews = collect($topic['uploads'])->sum('views_count');
                            @endphp
                            <div class="meta-item">
                                <span class="topic-views-badge">
                                    <i class="fa fa-eye"></i>
                                    <span>{{ \App\Helpers\GeneralHelper::calculate_view_percentage($totalTopicViews) }}% views</span>
                                </span>
                            </div>
                        </div>

                        <!-- Play Overlay -->
                        <div class="play-overlay">
                            <div class="play-button">
                                <i class="fa fa-folder-open"></i>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
        
    <!-- Browse by Content Type Segment -->
    <div class="content-segment" id="type-segment" style="margin-top: 50px; margin-bottom: 50px; padding: 0 15px; width: 100%; clear: both;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
            <h2 style="font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 12px;">
                <div style="width: 45px; height: 45px; border-radius: 12px; background: rgba(52, 152, 219, 0.1); display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-cubes" style="color: var(--primary-color); font-size: 20px;"></i>
                </div>
                Browse by Content Type
            </h2>
            <div style="font-size: 14px; color: var(--text-secondary); font-weight: 500;">
                <i class="fa fa-info-circle" style="margin-right: 5px;"></i> Explore our resource library
            </div>
        </div>

        <!-- Videos Section -->
        @if(count($groupedVideos) > 0)
        <div class="type-section" data-type="video" style="margin-bottom: 45px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-video-camera" style="color: #667eea;"></i> Videos to Watch
                    <span style="font-size: 12px; font-weight: 500; color: var(--text-secondary); background: #f0f0f0; padding: 2px 10px; border-radius: 20px;">{{ count($groupedVideos) }}</span>
                </h3>
                <a href="{{ url('learning/general-uploads/watch-and-learning?type=video') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='5px'">
                    See all <i class="fa fa-arrow-right"></i>
                </a>
            </div>
            <div class="unified-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($groupedVideos as $video)
                <div class="content-card" data-title="{{ $video->name }}" data-category="Video" onclick='window.location.href="{{ route('learning.watch-and-learning', ['topic' => $video->general_topic_id, 'upload' => $video->id]) }}"'>
                    <div class="card-image type-video" style="{{ $video->poster ? 'background: none;' : '' }}">
                        @if($video->poster)
                            <img src="{{ $video->poster }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $video->name }}">
                        @else
                            <i class="fa fa-video-camera" style="font-size: 40px; color: white;"></i>
                        @endif
                        <div class="card-badge">Video</div>
                        <div class="play-overlay">
                            <div class="play-button"><i class="fa fa-play"></i></div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <h3 class="card-title" style="margin-bottom: 12px; height: 42px; overflow: hidden;">{{ $video->name }}</h3>
                        <div class="card-meta">
                            <span><i class="fa fa-clock-o"></i> {{ $video->created_at->diffForHumans() }}</span>
                            <span><i class="fa fa-eye"></i> {{ $video->views_count ?? 0 }} views</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Audios Section -->
        @if(count($groupedAudios) > 0)
        <div class="type-section" data-type="audio" style="margin-bottom: 45px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-headphones" style="color: #f093fb;"></i> Listen to Audios
                    <span style="font-size: 12px; font-weight: 500; color: var(--text-secondary); background: #f0f0f0; padding: 2px 10px; border-radius: 20px;">{{ count($groupedAudios) }}</span>
                </h3>
                <a href="{{ url('learning/general-uploads/watch-and-learning?type=audio') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='5px'">
                    See all <i class="fa fa-arrow-right"></i>
                </a>
            </div>
            <div class="unified-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($groupedAudios as $audio)
                <div class="content-card" data-title="{{ $audio->name }}" data-category="Audio" onclick='window.location.href="{{ route('learning.watch-and-learning', ['topic' => $audio->general_topic_id, 'upload' => $audio->id]) }}"'>
                    <div class="card-image type-audio">
                        <i class="fa fa-headphones" style="font-size: 40px; color: white;"></i>
                        <div class="card-badge">Audio</div>
                        <div class="play-overlay">
                            <div class="play-button"><i class="fa fa-play"></i></div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <h3 class="card-title" style="margin-bottom: 12px; height: 42px; overflow: hidden;">{{ $audio->name }}</h3>
                        <div class="card-meta">
                            <span><i class="fa fa-clock-o"></i> {{ $audio->created_at->diffForHumans() }}</span>
                            <span><i class="fa fa-eye"></i> {{ $audio->views_count ?? 0 }} views</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Documents Section -->
        @if(count($groupedDocuments) > 0)
        <div class="type-section" data-type="document" style="margin-bottom: 45px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-file-text-o" style="color: #fa709a;"></i> Documents and Job Description Checklists
                    <span style="font-size: 12px; font-weight: 500; color: var(--text-secondary); background: #f0f0f0; padding: 2px 10px; border-radius: 20px;">{{ count($groupedDocuments) }}</span>
                </h3>
                <a href="{{ url('learning/general-uploads/watch-and-learning?type=document') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='5px'">
                    See all <i class="fa fa-arrow-right"></i>
                </a>
            </div>
            <div class="unified-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($groupedDocuments as $doc)
                <div class="content-card" data-title="{{ $doc->name }}" data-category="{{ ucfirst($doc->type) }}" onclick='window.location.href="{{ route('learning.watch-and-learning', ['topic' => $doc->general_topic_id, 'upload' => $doc->id]) }}"'>
                    <div class="card-image type-document">
                        <i class="fa {{ $doc->icon ?? 'fa-file-text-o' }}" style="font-size: 40px; color: white;"></i>
                        <div class="card-badge">{{ ucfirst($doc->type) }}</div>
                        <div class="play-overlay">
                            <div class="play-button"><i class="fa fa-eye"></i></div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <h3 class="card-title" style="margin-bottom: 12px; height: 42px; overflow: hidden;">{{ $doc->name }}</h3>
                        <div class="card-meta">
                            <span><i class="fa fa-clock-o"></i> {{ $doc->created_at->diffForHumans() }}</span>
                            <span><i class="fa fa-eye"></i> {{ $doc->views_count ?? 0 }} views</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
        
    <!-- No Results Message for Topics -->
    <div class="no-results" id="no-topics-results" style="display: none;">
        <i class="fa fa-search"></i>
        <h3>No Topics Found</h3>
        <p>Try adjusting your search query</p>
    </div>
    
    <!-- Load More Button for Featured Tab -->
    @if($isFeaturedTab)
    <div class="load-more-container" id="load-more-container">
        <button id="load-more-btn" class="load-more-btn" onclick="loadMore()">
            <i class="fa fa-plus"></i> Load More
        </button>
    </div>
    @endif
</div>


<!-- Unified Content Grid -->
<div class="unified-grid" id="content-grid">
      @if(!$isFeaturedTab)
              <!-- Course Cards -->
              @foreach($courses as $course)
              <div class="content-card course-card" data-type="course" data-title="{{ $course['title'] }}" data-category="{{ $course['category'] }}" data-progress="{{ $course['progress'] }}" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
                            <!-- Full Background Image or Gradient -->
                            <div class="course-bg-image" style="{{ isset($course['poster']) && $course['poster'] ? 'background-image: url(\'' . $course['poster'] . '\');' : 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' }}">
                              @if(!isset($course['poster']) || !$course['poster'])
                                    <i class="fa {{ $course['icon'] ?? 'fa-graduation-cap' }}" style="font-size: 48px; color: rgba(255,255,255,0.9);"></i>
                              @endif
                            </div>
             
                            <!-- Gradient Blur Overlay -->
                            <div class="course-overlay">
                              <div class="course-content">
                                <!-- Badge -->
                                <div class="course-badge">Course</div>
             
                                <!-- Category -->
                                <div class="course-category">{{ $course['category'] }}</div>
             
                                <!-- Title -->
                                <h3 class="course-title text-white" style="color: white;">{{ $course['title'] }}</h3>
             
                                <!-- Progress Bar (if enrolled and in progress) -->
                                @if($course['enrolled'] && $course['progress'] > 0)
                                <div class="course-progress">
                                  <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                                  </div>
                                  <span class="progress-text">{{ $course['progress'] }}% Complete</span>
                                </div>
                                @endif
             
                                <span class="progress-badge">
                                    <i class="fa fa-chart-line"></i>
                                    @php
                                        $courseModel = \App\Models\TrainingMaterial::with('allTopics')->find($course['id']);
                                        $totalTopicViews = $courseModel ? $courseModel->allTopics->sum('view_count') : 0;
                                    @endphp
                                    {{ \App\Helpers\GeneralHelper::calculate_view_percentage($totalTopicViews) }}% viewed
                                </span>
             
                                <!-- Play Overlay -->
                                <div class="play-overlay">
                                  <div class="play-button">
                                    <i class="fa fa-play"></i>
                                  </div>
                                </div>
                              </div>
                            </div>
                           </div>
              @endforeach
      @endif
      
      <!-- Regular Upload Cards (for other tabs) -->
      @if(!$isFeaturedTab)
              @foreach($uploads as $upload)
              <div class="content-card" data-type="{{ $upload->type }}" data-title="{{ $upload->name }}" data-category="{{ ucfirst($upload->type) }}" data-progress="0"
                       onclick="playMedia('{{ $upload->type }}', '{{ $upload->path }}', '{{ addslashes($upload->name) }}', '{{ $upload->formatted_size ?? 'N/A' }}', '{{ $upload->poster ?? '' }}')">
                      <div class="card-image type-{{ $upload->type }}" style="{{ $upload->poster ? 'background: none;' : '' }}">
                              @if($upload->poster)
                                      <img src="{{ $upload->poster }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $upload->name }}">
                              @else
                                      <i class="fa {{ $upload->icon ?? 'fa-file' }}"></i>
                              @endif
                              <div class="card-badge">{{ ucfirst($upload->type) }}</div>
                              <div class="play-overlay">
                                      <div class="play-button">
                                              <i class="fa fa-play"></i>
                                      </div>
                              </div>
                      </div>
                      <div class="card-body">
                              <div class="card-category">{{ ucfirst($upload->type) }}</div>
                              <h3 class="card-title">{{ $upload->name }}</h3>
                              <div class="card-meta">
                                      <span><i class="fa fa-database"></i> {{ $upload->formatted_size ?? 'N/A' }}</span>
                                      <span><i class="fa fa-eye"></i> {{ \App\Helpers\GeneralHelper::calculate_view_percentage($upload->views_count ?? 0) }}% views</span>
                              </div>
                      </div>
              </div>
              @endforeach
      @endif

    <!-- No Results Message (hidden by default) -->
    <div class="no-results" id="no-results" style="display: none;">
        <i class="fa fa-search"></i>
        <h3>No Results Found</h3>
        <p>Try adjusting your search or filter criteria</p>
    </div>
    
    <!-- Load More Button for Other Tabs -->
    @if(!$isFeaturedTab)
    <div class="load-more-container" id="load-more-container">
        <button id="load-more-btn" class="load-more-btn" onclick="loadMore()">
            <i class="fa fa-plus"></i> Load More
        </button>
    </div>
    @endif
</div>

<script>
var currentTab = 'all';
var currentFilter = 'all';
var searchQuery = '';
var allCards = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Collect all cards for filtering
    var grid = document.getElementById('content-grid');
    allCards = Array.from(grid.querySelectorAll('.content-card'));

    // Set default tab to 'featured' (shows featured topics)
    var defaultTab = 'featured';

    // Check for URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var validTabs = ['all', 'courses', 'in_progress', 'uploads', 'video', 'audio', 'book', 'document', 'featured'];
    if (tabParam && validTabs.includes(tabParam)) {
        defaultTab = tabParam;
    }

    switchTab(defaultTab);

    // Hide load more button if there are no more items to load
    const totalItems = {{ $isFeaturedTab ? count($topicsWithUploads) : (count($courses) + $uploads->count()) }};
    const perPage = {{ $perPage }};

    if (totalItems < perPage) {
        const loadMoreContainer = document.getElementById('load-more-container');
        if (loadMoreContainer) {
            loadMoreContainer.style.display = 'none';
        }
    }
});

function switchTab(tab) {
    currentTab = tab;

    // Update tab pills
    var tabs = document.querySelectorAll('.tab-pill');
    tabs.forEach(function(t) {
        t.classList.remove('active');
    });
    document.getElementById('tab-' + tab).classList.add('active');

    // Show/hide type filters
    var typeFilters = document.getElementById('type-filters');
    if (tab === 'uploads' || ['video', 'audio', 'book', 'paper', 'document'].includes(tab)) {
        typeFilters.style.display = 'flex';
    } else {
        typeFilters.style.display = 'none';
    }

    // Update title
    var titles = {
        'all': 'All Learning Materials',
        'courses': 'All Courses',
        'in_progress': 'Continue Learning',
        'uploads': 'General Uploads',
        'video': 'Video Materials',
        'audio': 'Audio Materials',
        'book': 'Books',
        'document': 'Documents',
        'featured': 'Featured Topics'
    };
    document.getElementById('content-title').innerHTML = '<i class="fa fa-th-large" style="color: var(--primary-color); margin-right: 10px;"></i>' + titles[tab];

    // Reset filter pills
    filterByType('all');

    // Apply filters
    applyFilters();
}

function filterByType(type) {
    currentFilter = type;
    
    // Update filter pills
    var pills = document.querySelectorAll('.filter-pill');
    pills.forEach(function(p) {
        p.classList.remove('active');
        if (p.dataset.filter === type) {
            p.classList.add('active');
        }
    });
    
    // Apply filters
    applyFilters();
}

function handleSearch(query) {
    searchQuery = query.toLowerCase().trim();
    applyFilters();
}

function applyFilters() {
    var grid = document.getElementById('content-grid');
    var featuredContainer = document.getElementById('featured-topics-container');
    var typeSegment = document.getElementById('type-segment');
    var visibleCount = 0;
    var hasVisibleCards = false;
    var visibleTopics = 0;
    var totalVisibleInSegment = 0;

    // 1. Filter Featured Topics (Topic Cards)
    var topicCards = document.querySelectorAll('.topic-card');
    topicCards.forEach(function(card) {
        var topicTitle = (card.dataset.title || '').toLowerCase();
        var showTopic = searchQuery === '' || topicTitle.includes(searchQuery);
        
        // Visibility depends on tab (only shown in 'featured' or 'all')
        var isTopicTab = (currentTab === 'featured' || currentTab === 'all');
        
        if (showTopic && isTopicTab) {
            card.style.display = '';
            visibleTopics++;
        } else {
            card.style.display = 'none';
        }
    });

    // 2. Filter Content Type Segment (discovery sections)
    if (typeSegment) {
        var sections = typeSegment.querySelectorAll('.type-section');
        
        // Define which segments are relevant for which tabs
        // 'featured', 'all', 'uploads' show everything upload-related
        // Specific media tabs only show their relevant section
        var isUploadTab = (currentTab === 'featured' || currentTab === 'all' || currentTab === 'uploads');
        
        sections.forEach(function(section) {
            var sectionType = section.dataset.type; // I might need to add this dataset attribute to the HTML
            var sectionCards = section.querySelectorAll('.content-card');
            var visibleInSection = 0;
            
            // Determine if this section is relevant for current tab
            var isSectionRelevant = isUploadTab || (currentTab === sectionType);
            
            // If tab is 'courses' or 'in_progress', don't show any upload segments
            if (currentTab === 'courses' || currentTab === 'in_progress') isSectionRelevant = false;

            sectionCards.forEach(function(card) {
                var cardTitle = (card.dataset.title || '').toLowerCase();
                var cardCategory = (card.dataset.category || '').toLowerCase();
                
                var matchesSearch = searchQuery === '' || 
                                    cardTitle.includes(searchQuery) || 
                                    cardCategory.includes(searchQuery);
                
                if (matchesSearch && isSectionRelevant) {
                    card.style.display = '';
                    visibleInSection++;
                    totalVisibleInSegment++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide specific section based on relevance and matching cards
            section.style.display = (visibleInSection > 0) ? 'block' : 'none';
        });
        
        // Segment container visibility
        var showSegment = (totalVisibleInSegment > 0 && searchQuery !== '') || (searchQuery === '' && isUploadTab);

        // Hide entirely on specialized tabs unless there are search matches
        if (currentTab === 'courses' || currentTab === 'in_progress') showSegment = false;
        
        typeSegment.style.display = showSegment ? 'block' : 'none';
    }

    // 3. Filter Content Grid (Unified Grid)
    allCards.forEach(function(card) {
        var cardType = card.dataset.type;
        var cardTitle = (card.dataset.title || '').toLowerCase();
        var cardCategory = (card.dataset.category || '').toLowerCase();
        var cardProgress = parseInt(card.dataset.progress) || 0;

        var showByTab = false;
        var showByFilter = currentFilter === 'all' || cardType === currentFilter;
        var showBySearch = searchQuery === '' ||
                            cardTitle.includes(searchQuery) ||
                            cardCategory.includes(searchQuery);

        if (currentTab === 'featured') {
            showByTab = false; // Grid is hidden on featured tab
        } else if (currentTab === 'all') {
            showByTab = true;
        } else if (currentTab === 'courses') {
            showByTab = cardType === 'course';
        } else if (currentTab === 'in_progress') {
            showByTab = cardType === 'course' && cardProgress > 0 && cardProgress < 100;
        } else if (currentTab === 'uploads') {
            showByTab = cardType !== 'course';
            showByFilter = true; // Show all upload types on uploads tab
        } else {
            showByTab = cardType === currentTab;
        }

        if (showByTab && showByFilter && showBySearch) {
            card.style.display = '';
            visibleCount++;
            hasVisibleCards = true;
        } else {
            card.style.display = 'none';
        }
    });

    // 4. Manage Container Visibility
    grid.style.display = (currentTab === 'featured') ? 'none' : (hasVisibleCards ? 'grid' : 'none');
    featuredContainer.style.display = (currentTab === 'featured' || currentTab === 'all') ? 'block' : 'none';

    // 5. Update Results Count
    var totalVisible = visibleTopics + visibleCount + totalVisibleInSegment;
    document.getElementById('content-count').textContent = 'Showing ' + totalVisible + ' items';

    // 6. Manage No Results Messages
    var noTopicsResults = document.getElementById('no-topics-results');
    var noResults = document.getElementById('no-results');

    // Show topic "no results" only on relevant tabs
    if ((currentTab === 'featured' || currentTab === 'all') && visibleTopics === 0 && totalVisibleInSegment === 0 && searchQuery !== '') {
        noTopicsResults.style.display = 'block';
    } else {
        noTopicsResults.style.display = 'none';
    }

    // Show grid "no results" only if everything is empty
    if (!hasVisibleCards && totalVisibleInSegment === 0 && searchQuery !== '' && currentTab !== 'featured') {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Play media inline (YouTube-like)
function playMedia(type, path, name, size, poster = '') {
    var ext = name.split('.').pop().toLowerCase();
    var isOfficeDoc = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(ext);
    var isPDF = ext === 'pdf';
    
    // For documents (DOCX, PPT, PDF), use separate document preview container
    if (isOfficeDoc || isPDF) {
        showDocumentPreview(type, path, name, size);
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
function showDocumentPreview(type, path, name, size) {
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
            viewerUrl = `https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(path)}&action=embedview&wdAllowInteractivity=0`;
        } else if (isPDF) {
            viewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(path)}&embedded=true&chrome=false`;
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
                <!-- Overlay to hide the toolbar -->
                <div style="position:absolute;top:0;left:0;width:100%;height:90px;background:#f3f2f1;z-index:1001;pointer-events:auto;"></div>
                ${isOfficeDoc ? '<div style="position:absolute;top:0;left:0;width:100%;height:50px;background:#f3f2f1;z-index:1001;pointer-events:auto;"></div>' : ''}
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
}

// Debounce function for search
function debounce(func, wait) {
    var timeout;
    return function executedFunction(...args) {
        var later = function() {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Override handleSearch with debounced version
var debouncedSearch = debounce(function(query) {
    handleSearch(query);
}, 300);

document.getElementById('search-input').addEventListener('input', function(e) {
    debouncedSearch(e.target.value);
});

// Load more functionality
function loadMore() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    loadMoreBtn.disabled = true;
    loadMoreBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    
    const nextPage = parseInt('{{ $page }}') + 1;
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', nextPage);
    
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}
</script>

@endsection
