@extends('layouts.learning')

@section('title', 'My Uploads - Whence Learn')

@section('content')
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
        <!-- Action Menu (Three dots) -->
        <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
            <button onclick="toggleActionMenu({{ $upload->id }})" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                <i class="fa fa-ellipsis-v" style="color: var(--text-primary);"></i>
            </button>
            <div id="action-menu-{{ $upload->id }}" style="display: none; position: absolute; top: 100%; right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); min-width: 140px; overflow: hidden; margin-top: 8px;">
                <a href="{{ url('learning/general-uploads/' . $upload->id . '/edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text-primary); text-decoration: none; font-size: 13px; transition: background 0.2s;">
                    <i class="fa fa-edit" style="color: var(--primary-color); width: 16px;"></i> Edit
                </a>
                <button onclick="likeUpload({{ $upload->id }})" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text-primary); text-decoration: none; font-size: 13px; width: 100%; border: none; background: none; cursor: pointer; transition: background 0.2s;">
                    <i class="fa fa-heart" style="color: var(--accent-color); width: 16px;"></i> Like
                </button>
                <button onclick="deleteUpload({{ $upload->id }}, '{{ addslashes($upload->name) }}')" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--accent-color); text-decoration: none; font-size: 13px; width: 100%; border: none; background: none; cursor: pointer; transition: background 0.2s;">
                    <i class="fa fa-trash" style="width: 16px;"></i> Delete
                </button>
            </div>
        </div>
        
        <div class="course-image" style="background: {{ $upload->type_color }};">
            <i class="fa {{ $upload->icon }}" style="font-size: 48px;"></i>
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
                    <a href="{{ $upload->path }}" target="_blank" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 12px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">
                        <i class="fa fa-play"></i> View
                    </a>
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
function toggleActionMenu(id) {
    var menu = document.getElementById('action-menu-' + id);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close all menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-menu-btn') && !e.target.closest('[id^="action-menu-"]')) {
        document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

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
