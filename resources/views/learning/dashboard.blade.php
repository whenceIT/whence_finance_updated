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
    margin-bottom: 25px;
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
        <i class="fa fa-star"></i> Featured
    </button>
    <button onclick="switchTab('all')" id="tab-all" class="tab-pill">
        <i class="fa fa-th-large"></i> All
    </button>
    <button onclick="switchTab('courses')" id="tab-courses" class="tab-pill">
        <i class="fa fa-graduation-cap"></i> Courses
    </button>
    <button onclick="switchTab('uploads')" id="tab-uploads" class="tab-pill">
        <i class="fa fa-cloud-upload"></i> Uploads
    </button>
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
                        <div class="content-card" onclick='window.location.href="{{ url('learning/general-uploads?topic=' . $topic['id']) }}"'>
                                <div class="card-image" style="{{ $topic['poster'] ? 'background: none;' : 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' }} height: 160px; display: flex; align-items: center; justify-content: center;">
                                        @if($topic['poster'])
                                                <img src="{{ $topic['poster'] }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $topic['name'] }}">
                                        @else
                                                <i class="fa fa-folder-open" style="color: white; font-size: 48px;"></i>
                                        @endif
                                </div>
                                <div class="card-body" style="text-align: center;">
                                        <h3 class="card-title" style="color: var(--text-primary); margin: 0 0 8px 0; font-size: 18px; font-weight: 600; line-height: 1.3;">{{ $topic['name'] }}</h3>
                                        <p style="color: var(--text-secondary); font-size: 13px; margin: 0;">{{ count($topic['uploads']) }} resources</p>
                                </div>
                        </div>
                </div>
        @endforeach
        
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
              <div class="content-card" data-type="course" data-title="{{ $course['title'] }}" data-category="{{ $course['category'] }}" data-progress="{{ $course['progress'] }}" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
               <div class="card-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); {{ isset($course['poster']) && $course['poster'] ? 'background: none;' : '' }}">
                 @if(isset($course['poster']) && $course['poster'])
                       <img src="{{ $course['poster'] }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $course['title'] }}">
                 @else
                       <i class="fa {{ $course['icon'] ?? 'fa-graduation-cap' }}"></i>
                 @endif
                 <div class="card-badge">Course</div>
                 <div class="play-overlay">
                       <div class="play-button">
                             <i class="fa fa-play"></i>
                       </div>
                 </div>
               </div>
               <div class="card-body">
                       <div class="card-category">{{ $course['category'] }}</div>
                       <h3 class="card-title">{{ $course['title'] }}</h3>
                       @if($course['enrolled'] && $course['progress'] > 0)
                       <div class="progress-bar">
                             <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                       </div>
                       @endif
                       <div class="card-meta">
                             <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                             <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
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
                                      <span>{{ $upload->created_at->format('M d, Y') }}</span>
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
    
    // Set default tab to 'featured'
    var randomTab = 'featured';
    
    // Check for URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var validTabs = ['all', 'courses', 'in_progress', 'uploads', 'video', 'audio', 'book', 'document', 'featured'];
    if (tabParam && validTabs.includes(tabParam)) {
        randomTab = tabParam;
    }
    
    switchTab(randomTab);
    
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
    var visibleCount = 0;
    var hasVisibleCards = false;
    
    // Handle featured tab - show featured topics container, hide content grid
    if (currentTab === 'featured') {
        grid.style.display = 'none';
        featuredContainer.style.display = 'block';
        
        // Search through featured topics
        var topicCards = document.querySelectorAll('.topic-card');
        var visibleTopics = 0;
        
        topicCards.forEach(function(card) {
            var topicTitle = (card.dataset.title || '').toLowerCase();
            
            if (searchQuery === '' || topicTitle.includes(searchQuery)) {
                card.style.display = '';
                visibleTopics++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update count for featured tab
        document.getElementById('content-count').textContent = 'Showing ' + visibleTopics + ' items';
        
        // Show/hide no results message for topics
        var noTopicsResults = document.getElementById('no-topics-results');
        if (visibleTopics === 0) {
            noTopicsResults.style.display = 'block';
        } else {
            noTopicsResults.style.display = 'none';
        }
        
        // Hide other no results message
        document.getElementById('no-results').style.display = 'none';
        return;
    }
    
    // For all other tabs, show content grid, hide featured container
    grid.style.display = 'grid';
    featuredContainer.style.display = 'none';
    
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
        
        if (currentTab === 'all') {
            showByTab = true;
        } else if (currentTab === 'courses') {
            showByTab = cardType === 'course';
        } else if (currentTab === 'in_progress') {
            showByTab = cardType === 'course' && cardProgress > 0 && cardProgress < 100;
        } else if (currentTab === 'uploads') {
            showByTab = cardType !== 'course';
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
    
    // Update count
    document.getElementById('content-count').textContent = 'Showing ' + visibleCount + ' items';
    
    // Show/hide no results message
    var noResults = document.getElementById('no-results');
    if (!hasVisibleCards) {
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
