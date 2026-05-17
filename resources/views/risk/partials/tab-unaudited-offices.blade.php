@if(count($unauditedOffices) === 0)
    <div class="alert alert-info">All offices have been audited.</div>
@else
    <div class="row">
        @foreach($unauditedOffices as $office)
            <div class="col-md-4 col-sm-6" style="margin-bottom:20px;">
                <div class="panel panel-default" style="border-left:4px solid #95a5a6;box-shadow:0 2px 4px rgba(0,0,0,.08);">
                    <div class="panel-body" style="padding:15px;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                            <div>
                                <h4 style="margin:0 0 5px 0;font-size:16px;font-weight:600;color:#2c3e50;">
                                    {{ $office->name }}
                                </h4>
                                @if($office->external_id)
                                    <div style="font-size:12px;color:#7f8c8d;">
                                        Code: {{ $office->external_id }}
                                    </div>
                                @endif
                            </div>
                            <span class="label label-default" style="font-size:11px;padding:4px 8px;">
                                Not Audited
                            </span>
                        </div>

                        <div style="margin-top:15px;padding-top:12px;border-top:1px solid #ecf0f1;">
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#7f8c8d;margin-bottom:8px;">
                                <span>Province:</span>
                                <span style="color:#2c3e50;font-weight:500;">
                                    {{ $office->province->name ?? 'N/A' }}
                                </span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#7f8c8d;margin-bottom:8px;">
                                <span>District:</span>
                                <span style="color:#2c3e50;font-weight:500;">
                                    {{ $office->district->name ?? 'N/A' }}
                                </span>
                            </div>
                            @if($office->manager_id)
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#7f8c8d;">
                                    <span>Manager:</span>
                                    <span style="color:#2c3e50;font-weight:500;">
                                        {{ $office->manager->first_name ?? '' }} {{ $office->manager->last_name ?? '' }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div style="margin-top:15px;text-align:center;">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#auditChecklistModal" onclick="preSelectOffice({{ $office->id }})">
                                <i class="fa fa-clipboard"></i> Start Audit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
