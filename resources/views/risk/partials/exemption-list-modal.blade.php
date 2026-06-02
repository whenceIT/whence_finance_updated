@if(!$officeIdParam)
<div style="margin-top: 20px;">
    <button type="button" id="openExemptionListModal" class="btn btn-info btn-sm" style="border-radius:6px;">
        <i class="fa fa-list"></i> Exemption List
    </button>
</div>
@else
@include('components.office-exemptions', ['settings' => \App\Models\PlatformSetting::getBranchDepositSettings($officeIdParam), 'title' => 'Office Exemptions'])
@endif