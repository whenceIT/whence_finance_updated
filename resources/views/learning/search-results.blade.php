@extends('layouts.learning')

@section('title', 'Search Results - Whence Learn')

<style>
.search-results-container {
    padding: 20px;
}

.search-header {
    margin-bottom: 30px;
}

.search-header h1 {
    font-size: 24px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.search-header .search-query {
    color: var(--primary-color);
    font-weight: 500;
}

.result-section {
    margin-bottom: 40px;
}

.result-section h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
}

.result-count {
    font-size: 14px;
    color: var(--text-secondary);
    margin-left: 10px;
}

.result-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.result-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid var(--border-color);
}

.result-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
}

.result-card .card-image {
    height: 140px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.result-card .card-image i {
    font-size: 36px;
    color: rgba(255,255,255,0.9);
}

.result-card .card-badge {
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

.result-card .card-body {
    padding: 15px;
}

.result-card .card-title {
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

.result-card .card-description {
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.result-card .card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-secondary);
}

.result-card .card-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.no-results-wrapper {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
}

.no-results-wrapper i {
    font-size: 64px;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.no-results-wrapper h3 {
    font-size: 24px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.no-results-wrapper p {
    color: var(--text-secondary);
    font-size: 15px;
    margin-bottom: 20px;
}

.back-to-search {
    margin-top: 20px;
}

.type-video { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.type-audio { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.type-book { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.type-paper { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.type-document { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.type-image { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.type-topic { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }

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

.result-card:hover .play-overlay {
    opacity: 1;
}

.play-button {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
}

.result-card:hover .play-button {
    transform: scale(1.1);
}

.play-button i {
    color: var(--primary-color);
    font-size: 18px;
    margin-left: 2px;
}
</style>

@section('content')
<div class="search-results-container">
    <div class="search-header">
        <h1>
            <i class="fa fa-search" style="color: var(--primary-color); margin-right: 10px;"></i>
            Search Results for "<span class="search-query">{{ $query }}</span>"
        </h1>
    </div>

    @php
        $totalTopics = $topics->count();
        $totalUploads = $uploads->count();
        $totalResults = $totalTopics + $totalUploads;
    @endphp

    @if($totalResults > 0)
        <!-- Topics Results -->
        @if($totalTopics > 0)
        <div class="result-section">
            <h2>
                <i class="fa fa-folder-open" style="color: var(--primary-color);"></i>
                Topics
                <span class="result-count">({{ $totalTopics }} found)</span>
            </h2>
            <div class="result-grid">
                @foreach($topics as $topic)
                <div class="result-card" onclick="window.location.href='{{ route('learning.watch-and-learning', ['topic' => $topic->id]) }}'">
                    <div class="card-image type-topic" style="{{ $topic->poster ? 'background: none;' : '' }}">
                        @if($topic->poster)
                            <img src="{{ $topic->poster }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $topic->name }}">
                        @else
                            <i class="fa fa-folder-open"></i>
                        @endif
                        <div class="card-badge">Topic</div>
                        <div class="play-overlay">
                            <div class="play-button">
                                <i class="fa fa-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $topic->name }}</h3>
                        @if($topic->description)
                            <p class="card-description">{{ \Illuminate\Support\Str::limit($topic->description, 80) }}</p>
                        @endif
                        <div class="card-meta">
                            <span><i class="fa fa-file"></i> {{ $topic->uploads->count() }} resources</span>
                            <span><i class="fa fa-clock-o"></i> {{ $topic->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Uploads Results -->
        @if($totalUploads > 0)
        <div class="result-section">
            <h2>
                <i class="fa fa-cloud-upload" style="color: var(--primary-color);"></i>
                Resources
                <span class="result-count">({{ $totalUploads }} found)</span>
            </h2>
            <div class="result-grid">
                @foreach($uploads as $upload)
                <div class="result-card" onclick="window.location.href='{{ route('learning.watch-and-learning', ['topic' => $upload->general_topic_id, 'upload' => $upload->id]) }}'">
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
                        <h3 class="card-title">{{ $upload->name }}</h3>
                        <div class="card-meta">
                            <span><i class="fa fa-eye"></i> {{ $upload->views_count ?? 0 }} views</span>
                            <span><i class="fa fa-clock-o"></i> {{ $upload->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @else
        <div class="no-results-wrapper">
            <i class="fa fa-search"></i>
            <h3>No Results Found</h3>
            <p>We couldn't find any topics or resources matching "{{ $query }}"</p>
            <a href="{{ url('learning') }}" class="btn btn-primary">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection