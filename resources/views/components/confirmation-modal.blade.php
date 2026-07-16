<div class="modal fade" id="{{ $modalId ?? 'confirmationModal' }}" tabindex="-1" role="dialog"
     aria-labelledby="{{ $modalId ?? 'confirmationModal' }}Label" aria-hidden="true"{{ $modalExtras ?? '' }}>
    <div class="modal-dialog modal-sm" role="document" style="margin-top:15vh;">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding:24px 24px 16px;">
                <div style="width:52px;height:52px;border-radius:50%;background:#fdf3f2;
                            margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa {{ $icon ?? 'fa-exclamation-triangle' }}" style="font-size:22px;color:{{ $iconColor ?? '#d9534f' }};"></i>
                </div>
                <h4 id="{{ $modalId ?? 'confirmationModal' }}Label" style="margin:0 0 6px;font-size:15px;font-weight:600;color:#333;">
                    {{ $title ?? 'Confirm action?' }}
                </h4>
                <p style="margin:0;color:#999;font-size:13px;">{{ $message ?? 'This action cannot be undone.' }}</p>
            </div>
            <div class="modal-footer" style="padding:8px 16px 16px;border-top:none;justify-content:center;gap:8px;">
                <button type="button" class="btn btn-default btn-flat btn-xs"
                        style="min-width:74px;" data-dismiss="modal">{{ $cancelText ?? 'Cancel' }}</button>
                <button type="button" id="{{ $confirmBtnId ?? 'confirmActionBtn' }}"
                        class="btn {{ $confirmBtnClass ?? 'btn-danger' }} btn-flat btn-xs"
                        style="min-width:74px;">{{ $confirmText ?? 'Confirm' }}</button>
            </div>
        </div>
    </div>
</div>