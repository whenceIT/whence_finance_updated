@extends('layouts.learning')

@section('title', 'My Uploads - Whence Learn')

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
<!-- Professional Header with Gradient -->
<div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); border-radius: 16px; padding: 32px; margin-bottom: 30px; color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px; color: white;">
                <i class="fa fa-cloud-upload"></i> My Uploads
            </h1>
            <p style="font-size: 14px; opacity: 0.9; margin: 0;">Manage and organize your uploaded files</p>
        </div>
        <a href="{{ url('learning/general-uploads/create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: var(--primary-color); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fa fa-upload"></i> Upload New File
        </a>
    </div>
</div>

<!-- Clean Filter Section -->
<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: var(--shadow);">
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
</style>

<script>
function applyFilter(type) {
    var url = '{{ url('learning/general-uploads') }}';
    if (type !== 'all') {
        url += '?type=' + type;
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
                        <i class="fa fa-database"></i> {{ $upload->formatted_size }}
                    </span>
                    <span style="color: var(--text-secondary); font-size: 12px;">
                        {{ $upload->created_at->format('M d, Y') }}
                    </span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button onclick="playMedia('{{ $upload->type }}', '{{ $upload->path }}', '{{ addslashes($upload->name) }}', '{{ $upload->formatted_size ?? 'N/A' }}', '{{ $upload->poster ?? '' }}')" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 12px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                        <i class="fa fa-play"></i> View
                    </button>
                    <!-- Action Buttons -->
                    <a href="{{ url('learning/general-uploads/' . $upload->id . '/edit') }}" style="width: 36px; height: 36px; border-radius: 6px; background: var(--light-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-primary); text-decoration: none; transition: background 0.2s;" title="Edit">
                        <i class="fa fa-edit" style="color: var(--primary-color);"></i>
                    </a>
                    <button onclick="likeUpload({{ $upload->id }})" style="width: 36px; height: 36px; border-radius: 6px; background: var(--light-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-primary); border: none; cursor: pointer; transition: background 0.2s;" title="Like">
                        <i class="fa fa-heart" style="color: var(--accent-color);"></i>
                    </button>
                    <button onclick="deleteUpload({{ $upload->id }}, '{{ addslashes($upload->name) }}')" style="width: 36px; height: 36px; border-radius: 6px; background: var(--light-bg); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--accent-color); border: none; cursor: pointer; transition: background 0.2s;" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: var(--shadow);">
        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fa fa-cloud-upload" style="font-size: 48px; color: white;"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
            No Uploads Yet
        </h2>
        <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto 24px; line-height: 1.6;">
            Start building your knowledge library by uploading your first file. Share videos, audios, books, papers, and more.
        </p>
        <a href="{{ url('learning/general-uploads/create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); color: white; padding: 14px 32px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(74, 144, 226, 0.4);">
            <i class="fa fa-upload"></i> Upload Your First File
        </a>
    </div>
    @endforelse
</div>

<script>
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



function likeUpload(id) {
    // Like functionality - can be expanded later
    toastr.success('File liked!', 'Success');
}
</script>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 100000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 420px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255, 107, 107, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fa fa-trash" style="font-size: 28px; color: var(--accent-color);"></i>
            </div>
            <h3 style="margin: 0 0 8px; color: var(--text-primary); font-size: 22px; font-weight: 600;">Delete File?</h3>
            <p style="color: var(--text-secondary); margin: 0;">Are you sure you want to delete "<span id="deleteFileName" style="font-weight: 600; color: var(--text-primary);"></span>"? This action cannot be undone.</p>
        </div>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeDeleteModal()" style="padding: 12px 24px; background: var(--light-bg); color: var(--text-secondary); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: background 0.2s;">Cancel</button>
            <button id="confirmDeleteBtn" style="padding: 12px 24px; background: var(--accent-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: background 0.2s;">Delete File</button>
        </div>
    </div>
</div>

<script>
var currentDeleteId = null;

function deleteUpload(id, name) {
    // Close any open action menus first
    document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
        menu.style.display = 'none';
    });
    
    currentDeleteId = id;
    document.getElementById('deleteFileName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!currentDeleteId) return;
    
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
    
    fetch('{{ url('learning/general-uploads') }}/' + currentDeleteId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = 'Delete File';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the file.');
        btn.disabled = false;
        btn.innerHTML = 'Delete File';
    });
});

// Close modal on outside click
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection
