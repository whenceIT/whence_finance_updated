@extends('layouts.learning')

@section('title', 'Edit Category - Whence Learn')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Edit Category</h1>
            <p>Update the course category details</p>
        </div>
        <a href="{{ url('course-categories') }}" class="btn btn-default" style="padding: 12px 24px; border-radius: 8px; border: 1px solid var(--border-color); background: white; color: var(--text-primary);">
            <i class="fa fa-arrow-left"></i> Back to Categories
        </a>
    </div>
</div>

<!-- Edit Category Form -->
<div class="box box-primary" style="background: white; border-radius: 10px; box-shadow: var(--shadow); overflow: hidden;">
    <div class="box-body" style="padding: 30px;">
        <form action="{{ url('course-categories/' . $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Category Name -->
                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                        Category Name <span style="color: red;">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                           placeholder="e.g., Financial Management"
                           value="{{ old('name', $category->name) }}">
                    @error('name')
                    <span style="color: var(--accent-color); font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Slug -->
                <div style="margin-bottom: 20px;">
                    <label for="slug" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                        Slug <span style="color: red;">*</span>
                    </label>
                    <input type="text" id="slug" name="slug" required
                           style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                           placeholder="e.g., financial-management"
                           value="{{ old('slug', $category->slug) }}">
                    @error('slug')
                    <span style="color: var(--accent-color); font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                          style="width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; transition: all 0.2s; resize: vertical;"
                          placeholder="Enter a brief description of this category">{{ old('description', $category->description) }}</textarea>
                @error('description')
                <span style="color: var(--accent-color); font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Icon -->
                <div style="margin-bottom: 20px;">
                    <label for="icon" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                        Icon <span style="color: red;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="text" id="icon" name="icon" required
                               style="width: 100%; padding: 12px 15px; padding-left: 45px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                               placeholder="e.g., fa-book"
                               value="{{ old('icon', $category->icon) }}">
                        <i class="fa {{ $category->icon }}" id="icon-preview" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--primary-color);"></i>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 12px; margin-top: 8px;">
                        <i class="fa fa-info-circle"></i> Use Font Awesome icons (e.g., fa-book, fa-graduation-cap, fa-chart-bar)
                    </p>
                    @error('icon')
                    <span style="color: var(--accent-color); font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Color -->
                <div style="margin-bottom: 20px;">
                    <label for="color" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                        Color <span style="color: red;">*</span>
                    </label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" id="color-picker" 
                               style="width: 50px; height: 42px; border: none; border-radius: 8px; cursor: pointer;"
                               value="{{ old('color', $category->color) }}">
                        <input type="text" id="color" name="color" required
                               style="flex: 1; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; transition: all 0.2s;"
                               placeholder="#4a90e2"
                               value="{{ old('color', $category->color) }}">
                    </div>
                    @error('color')
                    <span style="color: var(--accent-color); font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Sort Order (Auto-generated, not editable) -->
            <input type="hidden" id="sort_order" name="sort_order" value="{{ $category->sort_order }}">

            <!-- Active Status -->
            <div style="margin-bottom: 30px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}
                           style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
                    <span style="font-weight: 600; color: var(--text-primary);">Active</span>
                    <span style="color: var(--text-secondary); font-size: 13px; margin-left: 8px;">
                        ({{ $category->is_active ? 'Category is currently visible' : 'Category is currently hidden' }})
                    </span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 15px; justify-content: flex-start;">
                <button type="submit" class="btn btn-primary" style="padding: 14px 30px; border-radius: 8px; border: none; background: var(--primary-color); color: white; font-weight: 600; font-size: 15px; transition: all 0.2s;">
                    <i class="fa fa-save"></i> Update Category
                </button>
                <a href="{{ url('course-categories') }}" class="btn btn-default" style="padding: 14px 30px; border-radius: 8px; border: 1px solid var(--border-color); background: white; color: var(--text-primary); font-weight: 600; font-size: 15px; transition: all 0.2s;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Icon Picker Help -->
<div class="box box-default" style="background: white; border-radius: 10px; box-shadow: var(--shadow); margin-top: 20px;">
    <div class="box-header" style="padding: 15px 20px; border-bottom: 1px solid var(--border-color);">
        <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);">
            <i class="fa fa-info-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
            Available Icons
        </h4>
    </div>
    <div class="box-body" style="padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
            @php
            $icons = ['fa-book', 'fa-graduation-cap', 'fa-chart-bar', 'fa-line-chart', 'fa-pie-chart', 'fa-money', 'fa-bank', 'fa-coins', 'fa-wallet', 'fa-credit-card', 'fa-file-invoice', 'fa-calculator', 'fa-briefcase', 'fa-handshake', 'fa-users', 'fa-building', 'fa-globe', 'fa-laptop', 'fa-lightbulb', 'fa-star'];
            @endphp
            @foreach($icons as $icon)
            <div class="icon-option {{ $category->icon === $icon ? 'selected' : '' }}" 
                 data-icon="{{ $icon }}" 
                 style="padding: 15px; border: 1px solid {{ $category->icon === $icon ? 'var(--primary-color)' : 'var(--border-color)' }}; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.2s; background: {{ $category->icon === $icon ? 'rgba(74, 144, 226, 0.1)' : '' }};"
                 onclick="selectIcon('{{ $icon }}')"
                 onmouseover="$(this).css('border-color', 'var(--primary-color)'); $(this).css('background', 'rgba(74, 144, 226, 0.05)');"
                 onmouseout="$(this).css('border-color', '{{ $category->icon === $icon ? 'var(--primary-color)' : 'var(--border-color)' }}'); $(this).css('background', '{{ $category->icon === $icon ? 'rgba(74, 144, 226, 0.1)' : '' }}');">
                <i class="fa {{ $icon }}" style="font-size: 24px; color: var(--primary-color);"></i>
                <div style="font-size: 11px; color: var(--text-secondary); margin-top: 5px;">{{ str_replace('fa-', '', $icon) }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    document.getElementById('slug').value = slug;
});

// Update icon preview when typing
document.getElementById('icon').addEventListener('input', function() {
    const iconClass = this.value.trim();
    if (iconClass) {
        $('#icon-preview').removeClass().addClass('fa ' + iconClass);
    }
});

// Color picker sync
document.getElementById('color-picker').addEventListener('input', function() {
    document.getElementById('color').value = this.value;
});
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color-picker').value = this.value;
});

// Icon selection from grid
function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    $('#icon-preview').removeClass().addClass('fa ' + iconClass);
    
    // Visual feedback
    $('.icon-option').css('border-color', 'var(--border-color)');
    $('.icon-option').css('background', '');
    $('[data-icon="' + iconClass + '"]').css('border-color', 'var(--primary-color)');
    $('[data-icon="' + iconClass + '"]').css('background', 'rgba(74, 144, 226, 0.1)');
}
</script>

<style>
.form-control:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
}

.btn-primary {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover, .btn-primary:focus {
    background: #357abd !important;
    border-color: #357abd !important;
}

input[type="color"] {
    padding: 0;
}
</style>

<!-- Toastr Notifications -->
@if(Session::has('toastr_type'))
<script>
    toastr.{{ Session::get('toastr_type') }}('{{ Session::get('toastr_message') }}', '{{ Session::get('toastr_title', 'Notification') }}');
</script>
@endif
@endsection
