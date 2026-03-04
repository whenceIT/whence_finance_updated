@extends('layouts.learning')

@section('title', $material->title . ' - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $material->title, 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<!-- Material Header -->
<div style="background: linear-gradient(135deg, {{ $material->type_color }} 0%, {{ $material->type_color }} 100%); padding: 40px; border-radius: 12px; color: white; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
            <i class="fa {{ $material->icon }}" style="font-size: 40px;"></i>
        </div>
        <div style="flex: 1;">
            <span style="background: rgba(255,255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                {{ $material->department ?? 'General' }}
            </span>
            <h1 style="font-size: 32px; font-weight: 700; margin: 10px 0;">{{ $material->title }}</h1>
        </div>
    </div>
    <div style="display: flex; gap: 30px; font-size: 16px;">
        <span><i class="fa fa-eye"></i> {{ $material->view_count }} views</span>
        <span><i class="fa fa-download"></i> {{ $material->download_count }} downloads</span>
        <span><i class="fa fa-calendar"></i> {{ $material->created_at->format('M j, Y') }}</span>
    </div>
</div>

<!-- Material Details -->
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); margin-bottom: 30px;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-info-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
        Material Details
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Description</h3>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                {{ $material->description ?: 'No description available.' }}
            </p>
        </div>
        
        <div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">File Information</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; padding: 10px; background: var(--light-bg); border-radius: 6px;">
                    <i class="fa fa-file-o" style="color: var(--primary-color); margin-right: 10px; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $material->file_name }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">{{ $material->human_file_size }}</div>
                    </div>
                </div>
                
                @if($material->duration)
                <div style="display: flex; align-items: center; padding: 10px; background: var(--light-bg); border-radius: 6px;">
                    <i class="fa fa-clock-o" style="color: var(--secondary-color); margin-right: 10px; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Duration</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">{{ $material->human_duration }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Classification</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; padding: 10px; background: var(--light-bg); border-radius: 6px;">
                    <i class="fa fa-tag" style="color: var(--accent-color); margin-right: 10px; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Type</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: capitalize;">{{ $material->material_type }}</div>
                    </div>
                </div>
                
                @if($material->categories && $material->categories->count() > 0)
                <div style="display: flex; align-items: flex-start; padding: 10px; background: var(--light-bg); border-radius: 6px;">
                    <i class="fa fa-folder" style="color: var(--primary-color); margin-right: 10px; font-size: 20px; margin-top: 2px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Categories</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">
                            @foreach($material->categories as $cat)
                                <span style="display: inline-block; background: rgba(74, 144, 226, 0.1); color: var(--primary-color); padding: 2px 8px; border-radius: 4px; margin-right: 4px; margin-bottom: 4px;">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="display: flex; gap: 15px; margin-bottom: 30px;">
    @if($material->material_type == 'audio' || $material->material_type == 'video')
    <a href="{{ asset($material->file_path) }}" target="_blank" style="flex: 1; padding: 15px 30px; background: var(--primary-color); color: white; border: none; border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        <i class="fa fa-play"></i> {{ $material->material_type == 'audio' ? 'Listen' : 'Watch' }} Now
    </a>
    @endif
    
    <a href="{{ url('learning/training-materials/' . $material->id . '/download') }}" style="flex: 1; padding: 15px 30px; background: var(--secondary-color); color: white; border: none; border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        <i class="fa fa-download"></i> Download
    </a>
    
    @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
    <a href="{{ url('learning/training-materials/' . $material->id . '/edit') }}" style="flex: 1; padding: 15px 30px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        <i class="fa fa-edit"></i> Edit
    </a>
    @endif
</div>

<!-- Related Materials -->
@if($material->categories && $material->categories->count() > 0)
@php
$relatedMaterials = \App\Models\TrainingMaterial::whereHas('categories', function ($query) use ($material) {
    $query->whereIn('course_category_id', $material->categories->pluck('id'));
})->where('id', '!=', $material->id)->limit(6)->get();
@endphp

@if($relatedMaterials->count() > 0)
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-link" style="color: var(--primary-color); margin-right: 10px;"></i>
        More in 
        @foreach($material->categories as $index => $cat)
            "{{ $cat->name }}"{{ !$loop->last ? ', ' : '' }}
        @endforeach
    </h3>
    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
        Explore other training materials in the same categories.
    </p>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
        @foreach($relatedMaterials as $related)
        <a href="{{ url('learning/training-materials/' . $related->id) }}" style="display: block; text-decoration: none; color: inherit;">
            <div style="background: var(--light-bg); border-radius: 8px; padding: 15px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fa {{ $related->icon }}" style="font-size: 24px; color: {{ $related->type_color }};"></i>
                    <span style="background: rgba(255,255,255,0.9); padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">{{ $related->department }}</span>
                </div>
                <h4 style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $related->title }}</h4>
                <p style="font-size: 11px; color: var(--text-secondary); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $related->description ?: 'No description' }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endif
@endsection
