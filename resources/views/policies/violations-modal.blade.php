<!-- Policy Violations Modal -->
<div class="bottom-sheet-overlay" id="violationsOverlay">
    <div class="bottom-sheet" id="violationsSheet">
        <button class="bottom-sheet-close" id="closeViolationsSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title">Policy Violation Reports</h3>
            <div class="violations-container">
                <!-- Filters -->
                <div class="violations-filters">
                    <div class="filter-row">
                        <select id="violationStatus" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="escalated">Escalated</option>
                        </select>
                        <select id="violationBranch" class="form-control">
                            <option value="">All Branches</option>
                        </select>
                        <select id="violationPolicyType" class="form-control">
                            <option value="">All Policy Types</option>
                        </select>
                        <input type="date" id="violationDateFrom" class="form-control">
                        <input type="date" id="violationDateTo" class="form-control">
                        <button class="btn btn-primary" onclick="filterViolations()">Filter</button>
                        <button class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                    </div>
                </div>

                <!-- Violations List -->
                <div class="violations-list" id="violationsList">
                    <!-- Shimmer Loading -->
                    <div id="violationsShimmer" style="display:none;">
                        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:15px;margin-bottom:15px;background:#fff;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                <div style="width:55%;height:14px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:80px;height:22px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                            </div>
                            <div style="margin:5px 0;"><div style="width:35%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:28%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:30%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:25%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:10px 0 0 0;display:flex;gap:8px;flex-wrap:wrap;">
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                            </div>
                        </div>
                        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:15px;margin-bottom:15px;background:#fff;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                <div style="width:55%;height:14px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:80px;height:22px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                            </div>
                            <div style="margin:5px 0;"><div style="width:35%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:28%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:30%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:5px 0;"><div style="width:25%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div></div>
                            <div style="margin:10px 0 0 0;display:flex;gap:8px;flex-wrap:wrap;">
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                <div style="width:60px;height:24px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="empty-state" id="noViolations" style="display:none;">
                        <i class="fa fa-clipboard-list"></i>
                        <p>No violations found matching the filters.</p>
                    </div>
                </div>

                <!-- Add New Violation -->
                <div class="add-violation-section">
                    <button class="btn btn-primary" onclick="showAddViolationForm()">Report New Violation</button>
                </div>
            </div>
        </div>
    </div>
</div>
