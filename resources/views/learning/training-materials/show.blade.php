@extends('layouts.learning')

@section('title', $material->title . ' - Whence Learn')

@section('content')
<!-- Back Button -->
<div style="margin-bottom: 20px;">
    <a href="{{ url('learning/training-materials') }}" style="display: inline-flex; align-items: center; padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        <i class="fa fa-arrow-left"></i> Back to Materials
    </a>
</div>

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
                
                @if($material->category)
                <div style="display: flex; align-items: center; padding: 10px; background: var(--light-bg); border-radius: 6px;">
                    <i class="fa fa-folder" style="color: var(--primary-color); margin-right: 10px; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">Category</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">{{ $material->category }}</div>
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
@if($material->category)
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-link" style="color: var(--primary-color); margin-right: 10px;"></i>
        More in "{{ $material->category }}"
    </h3>
    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
        Explore other training materials in the same category.
    </p>
    <a href="{{ url('learning/training-materials') }}?category={{ $material->category }}" style="display: inline-block; padding: 12px 30px; background: var(--light-bg); color: var(--primary-color); border: 1px solid var(--primary-color); border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        View All {{ $material->category }} Materials
    </a>
</div>
@endif
@endsection
