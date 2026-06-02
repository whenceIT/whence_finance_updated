<div class="card" style="margin: 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div class="card-header" style="cursor: pointer; background: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 12px 16px;" onclick="$(this).next().slideToggle(); $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');">
        <i class="fa fa-building" style="color: #495057;"></i> 
        <span style="font-weight: 600; color: #343a40; margin-left: 8px;">Office Exemptions</span>
        <i class="fa fa-chevron-down" style="float: right; color: #6c757d;"></i>
    </div>
    <div class="card-body" id="office-exemptions-body" style="padding: 16px; display: none;">
        <div id="office-detail-card">
            <div style="text-align:center;padding:20px;"><i class="fa fa-spinner fa-spin" style="color: #6c757d;"></i></div>
        </div>
    </div>
</div>

<script>
function renderExemptions(settings) {
    const exemptions = [
        { 
            key: 'admin', 
            enabled: settings.admin, 
            title: 'Administration Department Fee Deposit',
            description: settings.admin 
                ? 'Obligated to make payment to Administration Department fee deposit'
                : 'Excluded from making payment to Administration Department fee deposit',
            color: settings.admin ? '#28a745' : '#dc3545'
        },
        { 
            key: 'building', 
            enabled: settings.building, 
            title: 'Building & Infrastructure Fee Deposit',
            description: settings.building 
                ? 'Obligated to make payment to Building & Infrastructure fee deposits'
                : 'Excluded from making payment to Building & Infrastructure fee deposits',
            color: settings.building ? '#28a745' : '#dc3545'
        },
        { 
            key: 'statutory', 
            enabled: settings.statutory, 
            title: 'Statutory Payments Deposit',
            description: settings.statutory 
                ? 'Obligated to make payment to Statutory payments deposits'
                : 'Excluded from making payment to Statutory payments deposits',
            color: settings.statutory ? '#28a745' : '#dc3545'
        },
        { 
            key: 'set_up_debt', 
            enabled: settings.set_up_debt, 
            title: 'Setup Cost Debt Payment',
            description: settings.set_up_debt 
                ? 'Obligated to make payment towards K5,000 minimum debt for setup cost'
                : 'Excluded from making payment towards setup cost debt',
            color: settings.set_up_debt ? '#28a745' : '#dc3545'
        }
    ];

    const container = document.getElementById('office-detail-card');
    let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
    
    exemptions.forEach(ex => {
        html += `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 8px; background: ${ex.color}15; border-left: 4px solid ${ex.color};">
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #343a40; font-size: 14px; margin-bottom: 4px;">${ex.title}</div>
                    <div style="color: #6c757d; font-size: 13px;">${ex.description}</div>
                </div>
                <div style="text-align: center; min-width: 100px;">
                    <span style="background: ${ex.color}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        ${ex.enabled ? 'ENABLED' : 'DISABLED'}
                    </span>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}
</script>