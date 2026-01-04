@php
    $user = Sentinel::getUser();
    $checklistItems = [];
    $totalCount = 0;
    $completedCount = 0;
    $shouldShow = false;

    if ($user) {
        // We use Eloquent to get the user and their checklist
        $u = \App\Models\User::find($user->id);
        if ($u) {
            $checklistItems = $u->inductionChecklist;
            
            // Check real-time policy status
            $policiesCompleted = \App\Models\InductionChecklist::hasCompletedPolicies($user->id);
            
            foreach ($checklistItems as $item) {
                if ($item->item == 'Review and sign pending company policies.') {
                    $item->completed = $policiesCompleted;
                }
            }

            $totalCount = $checklistItems->count();
            $completedCount = $checklistItems->where('completed', true)->count();

            // Only show if there are items and not all are completed
            if ($totalCount > 0 && $completedCount < $totalCount) {
                $shouldShow = true;
            }
        }
    }
@endphp

@if($shouldShow)
    <div id="inductionChecklistPopup" style="
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            animation: slideInBottomPopup 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        ">
        <style>
            @keyframes slideInBottomPopup {
                from {
                    transform: translateY(100%) translateX(20%);
                    opacity: 0;
                }

                to {
                    transform: translateY(0) translateX(0);
                    opacity: 1;
                }
            }

            .checklist-progress-container {
                height: 6px;
                background: #e9ecef;
                border-radius: 3px;
                margin: 10px 0 15px 0;
                overflow: hidden;
            }

            .checklist-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #00a04a, #00c853);
                border-radius: 3px;
                transition: width 0.3s ease;
            }

            .checklist-item {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 8px;
                font-size: 13px;
                line-height: 1.4;
            }

            .checklist-item i {
                margin-top: 2px;
                font-size: 14px;
            }

            .checklist-item.completed {
                color: #888;
                text-decoration: line-through;
            }

            .checklist-item.pending {
                color: #000041;
                font-weight: 500;
            }

            .close-checklist {
                position: absolute;
                top: 10px;
                right: 10px;
                border: none;
                background: none;
                cursor: pointer;
                color: #999;
                font-size: 14px;
            }

            .close-checklist:hover {
                color: #333;
            }
        </style>

        <button class="close-checklist" onclick="document.getElementById('inductionChecklistPopup').style.display = 'none'">
            <i class="fa fa-times"></i>
        </button>

        <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #000041;">Induction Progress</h4>
        <div style="font-size: 12px; color: #666; margin-top: 3px;">
            {{ $completedCount }} of {{ $totalCount }} tasks completed
        </div>

        <div class="checklist-progress-container">
            <div class="checklist-progress-bar" style="width: {{ ($completedCount / $totalCount) * 100 }}%;"></div>
        </div>

        <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
            @foreach($checklistItems as $item)
                <div class="checklist-item {{ $item->completed ? 'completed' : 'pending' }}">
                    <input disabled type="checkbox" {{ $item->completed ? 'checked' : '' }}
                        onchange="toggleChecklistItem({{ $item->id }}, this.checked)" style="margin-top: 3px; cursor: pointer;">
                    <span>{{ $item->item }}</span>
                </div>
            @endforeach
        </div>

        <script>
            function toggleChecklistItem(itemId, completed) {
                fetch('{{ url("induction/toggle_checklist_item") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        completed: completed ? 1 : 0
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error updating status.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>

        <script>
            (function(){
                var popup = document.getElementById('inductionChecklistPopup');
                if(popup){
                    var closeTimer = setTimeout(function(){
                        popup.style.display = 'none';
                    }, 15000);
                    // pause auto-close while user is interacting
                    popup.addEventListener('mouseenter', function(){ clearTimeout(closeTimer); });
                    popup.addEventListener('mouseleave', function(){ closeTimer = setTimeout(function(){ popup.style.display = 'none'; }, 3000); });
                }
            })();
        </script>

        @if($completedCount < $totalCount)
            <div style="margin-top: 15px; text-align: center;">
                <a href="{{ route('policies.view_policies') }}" style="
                            display: inline-block;
                            padding: 8px 15px;
                            background: #000041;
                            color: white;
                            border-radius: 6px;
                            font-size: 12px;
                            font-weight: 700;
                            text-decoration: none;
                            box-shadow: 0 4px 10px rgba(0,0,65,0.2);
                        ">Continue Onboarding</a>
            </div>
        @endif
    </div>
@endif