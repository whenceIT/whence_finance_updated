<!-- Modals -->
<div class="modal fade" id="modal-content-push" tabindex="-1" role="dialog" aria-labelledby="modal-content-push-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-content-push-label">Content Push Mechanism</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:14px;color:#333;line-height:1.5;padding:20px;background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:13px;color:#2c3e50;margin-bottom:12px;text-shadow:0 1px 0 rgba(255,255,255,0.8);">🚀 Content Push Mode:</label>
                    <div style="display:flex;border:2px solid #e1e8ed;border-radius:10px;overflow:hidden;box-shadow:0 3px 12px rgba(0,0,0,0.1);background:#fff;">
                        <label style="display:flex;align-items:center;justify-content:center;padding:12px 20px;font-size:14px;font-weight:500;border-right:1px solid #e1e8ed;transition:all 0.3s ease;cursor:pointer;flex:1;text-align:center;">
                            <input type="radio" name="content-push-mode" value="automatic" {{ $currentPushModeValue === 'automatic' ? 'checked' : '' }} style="margin-right:8px;width:18px;height:18px;"> Automatic
                        </label>
                        <label style="display:flex;align-items:center;justify-content:center;padding:12px 20px;font-size:14px;font-weight:500;border-right:1px solid transparent;transition:all 0.3s ease;cursor:pointer;flex:1;text-align:center;">
                            <input type="radio" name="content-push-mode" value="manual" {{ $currentPushModeValue === 'manual' ? 'checked' : '' }} style="margin-right:8px;width:18px;height:18px;"> Manual
                        </label>
                    </div>
                </div>

                <div id="content-setters">
                    <div style="margin-bottom:20px;">
                        <label for="content-type-select" style="display:block;font-weight:600;font-size:13px;color:#2c3e50;margin-bottom:8px;text-shadow:0 1px 0 rgba(255,255,255,0.8);">📚 Select Content Type:</label>
                        <select class="form-control" id="content-type-select" style="border:2px solid #e1e8ed;border-radius:8px;padding:12px 15px;font-size:14px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:all 0.3s ease;width:100%;height:48px;">
                            <option value="">-- Select --</option>
                            <option value="courses">🎓 Courses</option>
                            <option value="general">📖 General</option>
                        </select>
                    </div>


                    <div id="courses-list" style="display:none;margin-top:20px;background:#fff;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.08);overflow:hidden;">
                        <h6 style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 20px;margin:0;font-size:15px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;box-shadow:0 2px 8px rgba(102,126,234,0.3);">📚 Training Materials</h6>
                        <ul class="list-group" style="list-style:none;padding:0;margin:0;">
                            @php $materials = \App\Models\TrainingMaterial::all(); @endphp
                            @forelse($materials as $material)
                                <li style="padding:15px 20px;border-bottom:1px solid #f1f3f4;display:flex;justify-content:space-between;align-items:center;transition:all 0.2s ease;background:#fafbfc;hover:background:#f0f4f8;">
                                    <span style="font-weight:500;color:#2c3e50;font-size:14px;">{{ $material->title }}</span>
                                    <span style="padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;box-shadow:0 2px 8px rgba(102,126,234,0.3);min-width:80px;text-align:center;">{{ $material->material_type }}</span>
                                </li>
                            @empty
                                <li style="padding:20px;text-align:center;color:#7f8c8d;font-style:italic;background:#f8f9fa;">📭 No training materials available.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div id="general-list" style="display:none;margin-top:20px;background:#fff;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.08);overflow:hidden;">
                        <h6 style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:#fff;padding:15px 20px;margin:0;font-size:15px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;box-shadow:0 2px 8px rgba(245,87,108,0.3);">📁 General Uploads</h6>
                        <select id="topic-filter" class="form-control mb-3" style="margin:20px;border:2px solid #e1e8ed;border-radius:8px;padding:12px 15px;font-size:14px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:all 0.3s ease;width:100%;height:48px;">
                            <option value="">🌐 All Topics</option>
                            @php $topics = \App\Models\GeneralTopic::all(); @endphp
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        <ul class="list-group" style="list-style:none;padding:0;margin:0;">
                            @php $uploads = \App\Models\GeneralUpload::with('positions')->get(); @endphp
                            @forelse($uploads as $upload)
                                <li style="border-bottom:1px solid #f1f3f4;hover:background:#f8fbfc;transition:all 0.2s ease;" data-topic-id="{{ $upload->general_topic_id }}">
                                    <div data-toggle="collapse" data-target="#collapse-{{ $upload->id }}" style="cursor:pointer;padding:18px 20px;display:flex;justify-content:space-between;align-items:center;" class="d-flex justify-content-between align-items-center">
                                        <span style="font-weight:600;color:#2c3e50;font-size:14px;">📄 {{ $upload->name }}</span>
                                        <span style="padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:#6c757d;color:#fff;box-shadow:0 2px 6px rgba(108,117,125,0.3);min-width:70px;text-align:center;">{{ $upload->type_label }}</span>
                                    </div>
                                    <div id="collapse-{{ $upload->id }}" class="collapse mt-2" style="background:#f8f9fa;">
                                        <div style="padding:20px;">
                                            <p style="margin:0 0 15px 0;font-weight:600;color:#495057;font-size:14px;">👥 Assigned Positions:</p>
                                            @php $badgeClasses = ['#667eea','#48bb78','#4299e1','#ed8936','#f56565']; $index = 0; @endphp
                                            @if($upload->positions->count() > 0)
                                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                                    @foreach($upload->positions as $position)
                                                        <span style="padding:8px 14px;border-radius:25px;font-size:13px;font-weight:500;color:#fff;background:{{ $badgeClasses[$index++ % count($badgeClasses)] }};box-shadow:0 3px 12px {{ str_replace('#','', $badgeClasses[$index % count($badgeClasses)]) }}22;text-align:center;">{{ $position->name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p style="margin:0;color:#7f8c8d;font-style:italic;">📭 No positions assigned.</p>
                                            @endif
                                            <button class="btn btn-primary btn-sm mt-2" onclick="toggleAssignForm({{ $upload->id }})" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;padding:10px 20px;border-radius:25px;font-weight:600;font-size:13px;color:#fff;box-shadow:0 4px 15px rgba(102,126,234,0.4);transition:all 0.3s ease;hover:transform:translateY(-2px);">✏️ Assign Positions</button>
                                            <div id="assign-form-{{ $upload->id }}" style="display:none;margin-top:15px;background:#fff;border-radius:10px;padding:20px;box-shadow:0 4px 20px rgba(0,0,0,0.1);border:1px solid #e1e8ed;">
                                                <form style="margin:0;">
                                                    <div style="margin-bottom:15px;">
                                                        <label class="form-label" style="display:block;font-weight:600;color:#2c3e50;font-size:13px;margin-bottom:10px;">👥 Select Positions</label>
                                                        <div style="max-height:200px;overflow-y:auto;border:2px solid #e1e8ed;border-radius:8px;padding:15px;background:#fafbfc;box-shadow:inset 0 2px 8px rgba(0,0,0,0.05);">
                                                            @php $positions = \App\Models\Position::all(); @endphp
                                                            @foreach($positions as $position)
                                                                <div style="display:flex;align-items:center;padding:10px 0;border-bottom:1px solid #f1f3f4;">
                                                                    <input type="checkbox" id="position_{{ $upload->id }}_{{ $position->id }}" name="position_id[]" value="{{ $position->id }}" style="width:18px;height:18px;margin-right:12px;accent-color:#667eea;">
                                                                    <label for="position_{{ $upload->id }}_{{ $position->id }}" style="font-size:14px;color:#495057;cursor:pointer;flex:1;margin:0;">{{ $position->name }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div style="display:flex;gap:10px;">
                                                        <button type="button" class="btn btn-success btn-sm" onclick="savePositions({{ $upload->id }})" style="background:linear-gradient(135deg,#48bb78 0%,#38a169 100%);border:none;padding:12px 24px;border-radius:25px;font-weight:600;font-size:13px;color:#fff;box-shadow:0 4px 15px rgba(72,187,120,0.4);transition:all 0.3s ease;flex:1;hover:transform:translateY(-2px);">💾 Save</button>
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAssignForm({{ $upload->id }})" style="background:#6c757d;border:none;padding:12px 24px;border-radius:25px;font-weight:600;font-size:13px;color:#fff;box-shadow:0 4px 15px rgba(108,117,125,0.4);transition:all 0.3s ease;flex:1;hover:transform:translateY(-2px);">❌ Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li style="padding:25px;text-align:center;color:#7f8c8d;font-style:italic;background:#f8f9fa;border-radius:0 0 12px 12px;">📭 No general uploads available.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-fullscreen .modal-dialog {
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 0;
    }
    .modal-fullscreen .modal-content {
        height: 100%;
        border-radius: 0;
    }
</style>

<script>
    function toggleAssignForm(uploadId) {
        var form = document.getElementById('assign-form-' + uploadId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function savePositions(uploadId) {
        var checkboxes = document.querySelectorAll('#assign-form-' + uploadId + ' input[type="checkbox"]:checked');
        var positionIds = Array.from(checkboxes).map(function(cb) {
            return cb.value;
        });

        $.post('/learning/general-uploads/assign-positions', {
            upload_id: uploadId,
            position_ids: positionIds,
            _token: '{{ csrf_token() }}'
        })
        .done(function(data) {
            alert('Positions saved successfully');
            toggleAssignForm(uploadId);
            // Optionally refresh the positions display
            location.reload();
        })
        .fail(function(xhr) {
            alert('Error saving positions: ' + xhr.responseJSON.message);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {

        var currentMode = @json($currentPushModeValue);

        var select = document.getElementById('content-type-select');
        var coursesList = document.getElementById('courses-list');
        var generalList = document.getElementById('general-list');
        var contentSetters = document.getElementById('content-setters');


        contentSetters.style.display=currentMode==='automatic'?'none':'block';

        select.addEventListener('change', function() {
            var selected = this.value;
            if (selected === 'courses') {
                coursesList.style.display = 'block';
                generalList.style.display = 'none';
            } else if (selected === 'general') {
                coursesList.style.display = 'none';
                generalList.style.display = 'block';
            } else {
                coursesList.style.display = 'none';
                generalList.style.display = 'none';
            }
        });

        // Topic filter for general uploads
        var topicFilter = document.getElementById('topic-filter');
        if (topicFilter) {
            topicFilter.addEventListener('change', function() {
                var selectedTopic = this.value;
                var items = document.querySelectorAll('#general-list .list-group-item');
                items.forEach(function(item) {
                    var topicId = item.getAttribute('data-topic-id');
                    if (selectedTopic === '' || topicId == selectedTopic) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Content push mode toggle
        var radioButtons = document.querySelectorAll('input[name="content-push-mode"]');
        radioButtons.forEach(function(radio) {
            radio.addEventListener('change', function() {
                var mode = this.value;

                if(mode === 'automatic') {
                    contentSetters.style.display = 'none';
                } else {
                    contentSetters.style.display = 'block';
                }
                // Post to API
                $.post('/learning/settings/set-content-push-mode', {
                    mode: mode,
                    _token: '{{ csrf_token() }}'
                })
                .done(function(data) {
                    console.log('Mode updated:', data);
                })
                .fail(function(xhr) {
                    alert('Error updating mode: ' + xhr.responseJSON.message);
                });
            });
        });

        // Initialize based on current value if needed
        // For now, assume manual by default
    });
</script>