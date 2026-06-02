@if(!$officeIdParam)
<div style="margin-top: 20px;">
    <button type="button" id="openExemptionListModal" class="btn btn-info btn-sm" style="border-radius:6px;">
        <i class="fa fa-list"></i> Exemption List
    </button>
</div>
@else
<div class="card" style="margin: 20px 0;">
    <div class="card-header" style="cursor: pointer;" onclick="$(this).next().slideToggle(); $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');">
        <i class="fa fa-building"></i> Office Exemptions
        <i class="fa fa-chevron-down" style="float: right;"></i>
    </div>
    <div class="card-body" id="office-exemptions-body">
        <div id="office-detail-card">
            <div class="col-md-12" style="text-align:center;padding:20px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
        </div>
    </div>
</div>

<script>
var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}
</script>
@endif