@extends('layouts.learning')

@section('title', 'Training Materials - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Training Materials</h1>
    <p>Access and manage institutional learning resources</p>
</div>

<!-- Filters -->
<div style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Department</label>
        <select id="department-filter" onchange="window.location.href='{{ url('learning/training-materials') }}?department=' + this.value" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Departments</option>
            <option value="Operations">Operations</option>
            <option value="Recoveries">Recoveries</option>
            <option value="Administration">Administration</option>
            <option value="Finance">Finance</option>
            <option value="IT">IT</option>
            <option value="HR">HR</option>
            <option value="Legal">Legal</option>
            <option value="Compliance">Compliance</option>
            <option value="General">General</option>
        </select>
    </div>
    
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Material Type</label>
        <select id="type-filter" onchange="window.location.href='{{ url('learning/training-materials') }}?type=' + this.value" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
            <option value="all">All Types</option>
            <option value="document">Documents</option>
            <option value="audio">Audio</option>
            <option value="video">Videos</option>
        </select>
    </div>
    
    <div style="flex: 1; min-width: 200px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Search</label>
        <input type="text" id="search-input" placeholder="Search materials..." style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: white;">
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

<!-- Materials Grid -->
<div class="courses-grid" id="materials-grid">
    @forelse($materials as $material)
    @php
        $currentUser = Sentinel::getUser();
        $canDelete = $currentUser && ($material->created_by == $currentUser->id || in_array($currentUser->roles->first()->id, ['1', '6', '4']));
    @endphp
    <div class="course-card" style="position: relative; {{ $canDelete ? 'cursor: default;' : '' }}" onclick="{{ $canDelete ? '' : "window.location.href='" . url('learning/training-materials/' . $material->id) . "'" }}">
        {{-- Delete button for creator/admin --}}
        @if($canDelete)
        <div style="position: absolute; top: 10px; right: 10px; z-index: 100;" onclick="event.stopPropagation();">
            <form action="{{ url('learning/training-materials/' . $material->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete \"{{ addslashes($material->title) }}\"? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: rgba(220, 53, 69, 0.95); color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.3s;" title="Delete this material" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fa fa-trash" style="font-size: 16px;"></i>
                </button>
            </form>
        </div>
        @endif
        <div class="course-image" style="background: {{ $material->type_color }};">
            <i class="fa {{ $material->icon }}"></i>
        </div>
        <div class="course-body">
            <span class="course-category">{{ $material->department ?? 'General' }}</span>
            <h3 class="course-title">{{ $material->title }}</h3>
            <p class="course-description">{{ $material->description ?: 'No description available.' }}</p>
            
            @if($material->category)
            <div style="margin-bottom: 10px;">
                <span style="background: var(--light-bg); color: var(--text-secondary); padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                    <i class="fa fa-tag"></i> {{ $material->category }}
                </span>
            </div>
            @endif
            
            <div class="course-meta">
                <div class="course-stats">
                    <span><i class="fa fa-file-o"></i> {{ $material->human_file_size }}</span>
                    @if($material->duration)
                    <span><i class="fa fa-clock-o"></i> {{ $material->human_duration }}</span>
                    @endif
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    @if($material->target_role != 'all')
                    <span style="background: rgba(74, 144, 226, 0.1); color: var(--primary-color); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                        <i class="fa fa-users"></i> Role: {{ $material->target_role }}
                    </span>
                    @endif
                    @if(!$material->is_active)
                    <span style="background: rgba(255, 107, 107, 0.1); color: var(--accent-color); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                        <i class="fa fa-pause"></i> Inactive
                    </span>
                    @endif
                </div>
            </div>
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
