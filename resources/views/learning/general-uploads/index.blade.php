@extends('layouts.learning')

@section('title', 'My Uploads - Whence Learn')

@section('content')
<div class="page-header">
    <h1>My Uploads</h1>
    <p>Manage your uploaded files</p>
</div>

<!-- Upload Button -->
<div style="margin-bottom: 30px;">
    <a href="{{ url('learning/general-uploads/create') }}" style="display: inline-block; padding: 12px 24px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; transition: background 0.3s;">
        <i class="fa fa-upload"></i> Upload New File
    </a>
</div>

<!-- Filter by Type -->
<div style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Filter by Type</label>
        <select id="type-filter" onchange="applyFilters()" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Types</option>
            <option value="video">Videos</option>
            <option value="audio">Audios</option>
            <option value="book">Books</option>
            <option value="paper">Papers</option>
            <option value="document">Documents</option>
            <option value="image">Images</option>
            <option value="other">Other</option>
        </select>
    </div>
</div>

<script>
function applyFilters() {
    var type = document.getElementById('type-filter').value;
    var url = '{{ url('learning/general-uploads') }}';
    if (type !== 'all') {
        url += '?type=' + type;
    }
    window.location.href = url;
}

// Set current filter from URL
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var type = urlParams.get('type');
    if (type) {
        document.getElementById('type-filter').value = type;
    }
});
</script>

<!-- Files Grid -->
<div class="courses-grid" id="uploads-grid">
    @forelse($uploads as $upload)
    <div class="course-card">
        <div class="course-image" style="background: {{ $upload->type_color }};">
            <i class="fa {{ $upload->icon }}"></i>
        </div>
        <div class="course-body">
            <span class="course-category">{{ ucfirst($upload->type) }}</span>
            <h3 class="course-title">{{ $upload->name }}</h3>
            <div class="course-meta">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">
                        <i class="fa fa-database"></i> {{ $upload->formatted_size }}
                    </span>
                    <span style="color: var(--text-secondary); font-size: 12px;">
                        {{ $upload->created_at->format('M d, Y') }}
                    </span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ $upload->path }}" target="_blank" style="flex: 1; display: inline-block; padding: 8px 12px; background: var(--primary-color); color: white; border-radius: 4px; text-align: center; text-decoration: none; font-size: 13px; font-weight: 500;">
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button onclick="deleteUpload({{ $upload->id }}, '{{ addslashes($upload->name) }}')" style="padding: 8px 12px; background: var(--accent-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
        <i class="fa fa-folder-open" style="font-size: 64px; color: var(--text-secondary); margin-bottom: 20px;"></i>
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
            No Uploads Yet
        </h2>
        <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 20px;">
            You haven't uploaded any files yet. Upload your first file to get started.
        </p>
        <a href="{{ url('learning/general-uploads/create') }}" style="display: inline-block; background: var(--primary-color); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
            Upload First File
        </a>
    </div>
    @endforelse
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <h3 style="margin: 0 0 15px; color: var(--text-primary); font-size: 20px; font-weight: 600;">Confirm Delete</h3>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Are you sure you want to delete "<span id="deleteFileName"></span>"? This action cannot be undone.</p>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button onclick="closeDeleteModal()" style="padding: 10px 20px; background: var(--light-bg); color: var(--text-secondary); border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">Cancel</button>
            <button id="confirmDeleteBtn" style="padding: 10px 20px; background: var(--accent-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">Delete</button>
        </div>
    </div>
</div>

<script>
var currentDeleteId = null;

function deleteUpload(id, name) {
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
            btn.innerHTML = 'Delete';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the file.');
        btn.disabled = false;
        btn.innerHTML = 'Delete';
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
