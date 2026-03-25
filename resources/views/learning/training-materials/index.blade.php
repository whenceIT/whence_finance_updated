@extends('layouts.learning')

@section('title', 'Training Materials - Whence Learn')

@section('content')
<!-- Professional Header with Gradient -->
<div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); border-radius: 16px; padding: 32px; margin-bottom: 30px; color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px; color: white;">
                <i class="fa fa-folder-open"></i> Training Materials
            </h1>
            <p style="font-size: 14px; opacity: 0.9; margin: 0;">Access and manage institutional learning resources</p>
        </div>
        @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
        <a href="{{ url('learning/training-materials/create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #9b59b6; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fa fa-plus"></i> Add New Material
        </a>
        @endif
    </div>
</div>

<!-- Clean Filter Section -->
<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: var(--shadow);">
    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-weight: 500;">
            <i class="fa fa-filter"></i> Filter by:
        </div>
        
        <div style="flex: 1; min-width: 180px;">
            <select id="category-filter" onchange="applyFilters()" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); font-size: 13px;">
                <option value="all">All Categories</option>
                @php $allCategories = \App\Models\CourseCategory::active()->ordered()->get(); @endphp
                @foreach($allCategories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div style="flex: 1; min-width: 180px;">
            <select id="department-filter" onchange="applyFilters()" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); font-size: 13px;">
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
        
        <div style="flex: 1; min-width: 180px;">
            <select id="type-filter" onchange="applyFilters()" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); font-size: 13px;">
                <option value="all">All Types</option>
                <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
            </select>
        </div>
        
        <div style="flex: 1; min-width: 200px;">
            <input type="text" id="search-input" placeholder="Search materials..." value="{{ request('search') }}" onkeyup="if(event.key === 'Enter') { applyFilters(); }" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); font-size: 13px;">
        </div>
    </div>
</div>

<script>
function applyFilters() {
    var url = '{{ url('learning/training-materials') }}?';
    var params = [];
    
    var category = document.getElementById('category-filter').value;
    if (category !== 'all') params.push('category=' + category);
    
    var department = document.getElementById('department-filter').value;
    if (department !== 'all') params.push('department=' + department);
    
    var type = document.getElementById('type-filter').value;
    if (type !== 'all') params.push('type=' + type);
    
    var search = document.getElementById('search-input').value;
    if (search.trim() !== '') params.push('search=' + encodeURIComponent(search));
    
    window.location.href = url + params.join('&');
}
</script>

<!-- Stats Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 30px;">
    <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: var(--shadow);">
        <div style="font-size: 32px; font-weight: 700; color: #9b59b6;">{{ $materials->total() }}</div>
        <div style="font-size: 13px; color: var(--text-secondary);">Total Materials</div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: var(--shadow);">
        <div style="font-size: 32px; font-weight: 700; color: var(--primary-color);">{{ $materials->where('is_active', true)->count() }}</div>
        <div style="font-size: 13px; color: var(--text-secondary);">Active</div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: var(--shadow);">
        <div style="font-size: 32px; font-weight: 700; color: var(--secondary-color);">{{ $materials->where('is_active', false)->count() }}</div>
        <div style="font-size: 13px; color: var(--text-secondary);">Inactive</div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: var(--shadow);">
        <div style="font-size: 32px; font-weight: 700; color: var(--accent-color);">{{ \App\Helpers\GeneralHelper::calculate_view_percentage($materials->sum('view_count')) }}%</div>
        <div style="font-size: 13px; color: var(--text-secondary);">Total Views</div>
    </div>
</div>

<!-- Materials Grid -->
<div class="courses-grid" id="materials-grid">
    @forelse($materials as $material)
    @php
        $currentUser = Sentinel::getUser();
        $canDelete = $currentUser && ($material->created_by == $currentUser->id || in_array($currentUser->roles->first()->id, ['1', '6', '4']));
        $isTrainer = $currentUser && $currentUser->istrainer == 1;
    @endphp
    <div class="course-card" style="position: relative; {{ $canDelete || $isTrainer ? 'cursor: default;' : '' }}" onclick="{{ $canDelete || $isTrainer ? '' : "window.location.href='" . url('learning/training-materials/' . $material->id) . "'" }}">
        
        <!-- Action Menu -->
        @if($canDelete || $isTrainer)
        <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
            <button onclick="toggleActionMenu({{ $material->id }})" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                <i class="fa fa-ellipsis-v" style="color: var(--text-primary);"></i>
            </button>
            <div id="action-menu-{{ $material->id }}" style="display: none; position: absolute; top: 100%; right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); min-width: 160px; overflow: hidden; margin-top: 8px;">
                <a href="{{ url('learning/training-materials/' . $material->id . '/topics') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text-primary); text-decoration: none; font-size: 13px; transition: background 0.2s;">
                    <i class="fa fa-list-alt" style="color: var(--secondary-color); width: 16px;"></i> Topics
                </a>
                <a href="{{ url('learning/training-materials/' . $material->id . '/edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text-primary); text-decoration: none; font-size: 13px; transition: background 0.2s;">
                    <i class="fa fa-edit" style="color: var(--primary-color); width: 16px;"></i> Edit
                </a>
                @if($canDelete)
                <form action="{{ url('learning/training-materials/' . $material->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete \"{{ addslashes($material->title) }}\"?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--accent-color); text-decoration: none; font-size: 13px; width: 100%; border: none; background: none; cursor: pointer; transition: background 0.2s;">
                        <i class="fa fa-trash" style="width: 16px;"></i> Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Status Badge -->
        @if(!$material->is_active)
        <span style="position: absolute; top: 12px; left: 12px; background: rgba(220, 53, 69, 0.95); color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500; z-index: 10;">
            <i class="fa fa-pause"></i> Inactive
        </span>
        @else
        <span style="position: absolute; top: 12px; left: 12px; background: rgba(40, 167, 69, 0.95); color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 500; z-index: 10;">
            <i class="fa fa-check"></i> Active
        </span>
        @endif
        
        <div class="course-image" style="background: {{ $material->type_color }}; {{ $material->poster ? 'background: none;' : '' }}">
            @if($material->poster)
                <img src="{{ $material->poster }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $material->title }}">
            @else
                <i class="fa {{ $material->icon }}" style="font-size: 48px;"></i>
            @endif
        </div>
        <div class="course-body" style="padding-bottom: 16px;">
            <span class="course-category">{{ $material->department ?? 'General' }}</span>
            <h3 class="course-title">{{ strtoupper($material->title) }}</h3>
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
            
            <!-- View Button -->
            <a href="{{ url('learning/training-materials/' . $material->id) }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; width: 100%; margin-top: 8px;">
                <i class="fa fa-eye"></i> View Details
            </a>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: var(--shadow);">
        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fa fa-folder-open" style="font-size: 48px; color: white;"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
            No Training Materials Found
        </h2>
        <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto 24px; line-height: 1.6;">
            There are no training materials available at the moment. Check back later or contact your administrator.
        </p>
        @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
        <a href="{{ url('learning/training-materials/create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 14px 32px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(155, 89, 182, 0.4);">
            <i class="fa fa-plus"></i> Add First Material
        </a>
        @endif
    </div>
    @endforelse
</div>

{{ $materials->links() }}

<script>
function toggleActionMenu(id) {
    var menu = document.getElementById('action-menu-' + id);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="action-menu-"]') && !e.target.closest('button')) {
        document.querySelectorAll('[id^="action-menu-"]').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
</script>
@endsection
