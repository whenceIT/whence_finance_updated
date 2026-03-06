@extends('layouts.learning')

@section('title', 'Training Materials - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Training Materials</h1>
    <p>Access and manage institutional learning resources</p>
</div>

<!-- Filters -->
<div style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
    @php
    $allCategories = \App\Models\CourseCategory::active()->ordered()->get();
    @endphp
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Category</label>
        <select id="category-filter" onchange="applyFilters()" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Categories</option>
            @foreach($allCategories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Department</label>
        <select id="department-filter" onchange="applyFilters()" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Departments</option>
            <option value="Operations" {{ request('department') == 'Operations' ? 'selected' : '' }}>Operations</option>
            <option value="Recoveries" {{ request('department') == 'Recoveries' ? 'selected' : '' }}>Recoveries</option>
            <option value="Administration" {{ request('department') == 'Administration' ? 'selected' : '' }}>Administration</option>
            <option value="Finance" {{ request('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
            <option value="IT" {{ request('department') == 'IT' ? 'selected' : '' }}>IT</option>
            <option value="HR" {{ request('department') == 'HR' ? 'selected' : '' }}>HR</option>
            <option value="Legal" {{ request('department') == 'Legal' ? 'selected' : '' }}>Legal</option>
            <option value="Compliance" {{ request('department') == 'Compliance' ? 'selected' : '' }}>Compliance</option>
            <option value="General" {{ request('department') == 'General' ? 'selected' : '' }}>General</option>
        </select>
    </div>
    
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Material Type</label>
        <select id="type-filter" onchange="applyFilters()" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Types</option>
            <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
            <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
        </select>
    </div>
    
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Search</label>
        <input type="text" id="search-input" placeholder="Search materials..." value="{{ request('search') }}" onkeyup="if(event.key === 'Enter') { applyFilters(); }" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
    </div>
    
    @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">&nbsp;</label>
        <a href="{{ url('learning/training-materials/create') }}" style="display: inline-block; padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; transition: background 0.3s;">
            <i class="fa fa-plus"></i> Add New Material
        </a>
    </div>
    @endif
</div>

<script>
function applyFilters() {
    var url = '{{ url('learning/training-materials') }}?';
    var params = [];
    
    var category = document.getElementById('category-filter').value;
    if (category !== 'all') {
        params.push('category=' + category);
    }
    
    var department = document.getElementById('department-filter').value;
    if (department !== 'all') {
        params.push('department=' + department);
    }
    
    var type = document.getElementById('type-filter').value;
    if (type !== 'all') {
        params.push('type=' + type);
    }
    
    var search = document.getElementById('search-input').value;
    if (search.trim() !== '') {
        params.push('search=' + encodeURIComponent(search));
    }
    
    if (params.length > 0) {
        window.location.href = url + params.join('&');
    } else {
        window.location.href = url;
    }
}
</script>

<!-- Materials Grid -->
<div class="courses-grid" id="materials-grid">
    @forelse($materials as $material)
    @php
        $currentUser = Sentinel::getUser();
        $canDelete = $currentUser && ($material->created_by == $currentUser->id || in_array($currentUser->roles->first()->id, ['1', '6', '4']));
        $isTrainer = $currentUser && $currentUser->istrainer == 1;
    @endphp
    <div class="course-card" style="position: relative; {{ $canDelete || $isTrainer ? 'cursor: default;' : '' }}" onclick="{{ $canDelete || $isTrainer ? '' : "window.location.href='" . url('learning/training-materials/' . $material->id) . "'" }}">
        {{-- Delete button for creator/admin --}}
        @if($canDelete || !$isAdmin)
        <div style="position: absolute; top: 10px; right: 10px; z-index: 100; display: flex; gap: 5px;" onclick="event.stopPropagation();">
            {{-- Manage Topics & Quizzes button for trainers --}}
            @if($isTrainer || !$isAdmin)
            <a href="{{ url('learning/training-materials/' . $material->id . '/topics') }}" style="background: rgba(40, 167, 69, 0.95); color: white; border: none; border-radius: 5%; width: 100px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.3s; text-decoration: none;" title="Manage Course, Topics & Quizzes" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                Manage &nbsp;<i class="fa fa-list-alt" style="font-size: 16px;"></i>
            </a>
            @endif
            <form action="{{ url('learning/training-materials/' . $material->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete \"{{ addslashes($material->title) }}\"? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: rgba(220, 53, 69, 0.95); color: white; border: none; border-radius: 50%; width: 36px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.3s;" title="Delete this material" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fa fa-trash" style="font-size: 16px;"></i>
                </button>
            </form>
        </div>
        @elseif($isTrainer || !$isAdmin)
        <div style="position: absolute; top: 10px; right: 10px; z-index: 100;" onclick="event.stopPropagation();">
            <a href="{{ url('learning/training-materials/' . $material->id . '/topics') }}" style="background: rgba(40, 167, 69, 0.95); color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.3s; text-decoration: none;" title="Manage Topics & Quizzes" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                <i class="fa fa-list-alt" style="font-size: 16px;"></i>
            </a>
        </div>
        @endif
        <div class="course-image" style="background: {{ $material->type_color }};">
            <i class="fa {{ $material->icon }}"></i>
        </div>
        <div class="course-body">
            <span class="course-category">{{ $material->department ?? 'General' }}</span>
            <h3 class="course-title">{{ $material->title }}</h3>
            <p class="course-description">{{ $material->description ?: 'No description available.' }}</p>
            
            @if($material->categories && $material->categories->count() > 0)
            <div style="margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 5px;">
                @foreach($material->categories as $cat)
                <span style="background: var(--light-bg); color: var(--primary-color); padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                    <i class="fa fa-tag"></i> {{ $cat->name }}
                </span>
                @endforeach
            </div>
            @endif
            @if(!$material->is_active)
            <span style="background: rgba(255, 107, 107, 0.1); color: var(--accent-color); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                <i class="fa fa-pause"></i> Inactive
            </span>
            @else
            <span style="background: rgba(40, 167, 69, 0.1); color: var(--success-color); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                <i class="fa fa-check"></i> Active
            </span>
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
        <i class="fa fa-folder-open" style="font-size: 64px; color: var(--text-secondary); margin-bottom: 20px;"></i>
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
            No Training Materials Found
        </h2>
        <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto;">
            There are no training materials available at the moment. Check back later or contact your administrator.
        </p>
        @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
        <a href="{{ url('learning/training-materials/create') }}" style="display: inline-block; margin-top: 20px; background: var(--primary-color); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
            Add First Material
        </a>
        @endif
    </div>
    @endforelse
</div>

{{ $materials->links() }}
@endsection
