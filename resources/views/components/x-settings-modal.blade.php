{{-- Settings Bottom Sheet Modal --}}
<div class="bottom-sheet-overlay" id="settingsBottomSheetOverlay">
    <div class="bottom-sheet" id="settingsBottomSheet">
        <button class="bottom-sheet-close" id="closeSettingsBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content" style="display: flex; flex-direction: column; height: 100%;">
            <h3 class="bottom-sheet-title">Settings</h3>

            <div style="display: flex; flex: 1; overflow: hidden;">
                {{-- Sidebar Navigation --}}
                <div id="settingsSidebar" style="width: 120px; background: #f8f9fa; padding: 10px 0; border-right: 1px solid #eee; overflow-y: auto;">
                    <a href="#" onclick="showSettingsSection('general'); return false;" data-section="general" class="settings-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 15px; color: #333; text-decoration: none; font-size: 13px; border-left: 3px solid transparent;">
                        <i class="fa fa-cog" style="width: 20px; text-align: center;"></i>
                        <span>General</span>
                    </a>
                    <a href="#" onclick="showSettingsSection('sms'); return false;" data-section="sms" class="settings-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 15px; color: #333; text-decoration: none; font-size: 13px; border-left: 3px solid transparent;">
                        <i class="fa fa-envelope" style="width: 20px; text-align: center;"></i>
                        <span>SMS</span>
                    </a>
                    <a href="#" onclick="showSettingsSection('notifications'); return false;" data-section="notifications" class="settings-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 15px; color: #333; text-decoration: none; font-size: 13px; border-left: 3px solid transparent;">
                        <i class="fa fa-bell" style="width: 20px; text-align: center;"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="#" onclick="showSettingsSection('deposits'); return false;" data-section="deposits" class="settings-menu-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 15px; color: #333; text-decoration: none; font-size: 13px; border-left: 3px solid transparent;">
                        <i class="fa fa-money" style="width: 20px; text-align: center;"></i>
                        <span>Deposits</span>
                    </a>
                </div>

                {{-- Content Sections --}}
                <div id="settingsContent" style="flex: 1; padding: 20px; overflow-y: auto;">
                    {{-- General Settings Section --}}
                    <div id="settings-general" class="settings-section" style="display: none;">
                        <h4 style="margin-top: 0; color: #333; font-size: 16px; margin-bottom: 15px;">General Settings</h4>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Company Name</label>
                            <input type="text" id="companyName" class="form-control" placeholder="Company Name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Company Email</label>
                            <input type="email" id="companyEmail" class="form-control" placeholder="Company Email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <button class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Save Changes</button>
                    </div>

                    {{-- SMS Settings Section --}}
                    <div id="settings-sms" class="settings-section" style="display: none;">
                        <h4 style="margin-top: 0; color: #333; font-size: 16px; margin-bottom: 15px;">SMS Settings</h4>
                        
                        {{-- SMS Tabs --}}
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                <a href="#" onclick="switchSmsTab('client'); return false;" data-tab="client" class="sms-tab-item active" style="padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px; border-bottom: 2px solid #00a65a;">Send Client Notifications</a>
                                <a href="#" onclick="switchSmsTab('branch'); return false;" data-tab="branch" class="sms-tab-item" style="padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">Send Branch Notifications</a>
                                <a href="#" onclick="switchSmsTab('gateway'); return false;" data-tab="gateway" class="sms-tab-item" style="padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">Gateway Settings</a>
                            </div>
                            
                            {{-- Send Client Notifications Tab --}}
                            <div id="sms-client" class="sms-tab-content" style="display: block;">
                                <form id="sendClientSmsForm">
                                    <div style="margin-bottom: 15px;">
                                        <label for="smsClientOffice" style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Select Office</label>
                                        <select id="smsClientOffice" name="office_id" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                                            <option value="">Select Office</option>
                                        </select>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <label for="smsClientUser" style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Select Loan Consultant</label>
                                        <select id="smsClientUser" name="user_id" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required disabled>
                                            <option value="">Select Office First</option>
                                        </select>
                                    </div>
                                    
                                    <div id="smsClientCountDisplay" style="margin-bottom: 15px; display: none;">
                                        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Clients with Active Loans</label>
                                        <div style="padding: 8px 12px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                            <span id="smsClientCountValue">-</span> <span style="color: #666; font-size: 12px;">clients with disbursed loans</span>
                                        </div>
                                    </div>
                                    
                                    
                                    <button type="submit" class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Send SMS</button>
                                    <div id="smsClientSmsResponse" style="margin-top: 10px; font-size: 13px;"></div>
                                </form>
                            </div>
                            
                            {{-- Send Branch Notifications Tab --}}
                            <div id="sms-branch" class="sms-tab-content" style="display: none;">
                                <form id="sendBranchSmsForm">
                                    <div style="margin-bottom: 15px;">
                                        <label for="smsBranchOffice" style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Select Office</label>
                                        <select id="smsBranchOffice" name="office_id" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                                            <option value="">Select Office</option>
                                        </select>
                                    </div>
                                    
                                    <div style="margin-bottom: 15px;">
                                        <label for="branchMessage" style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Message</label>
                                        <textarea id="branchMessage" name="message" class="form-control" placeholder="Enter SMS message to send to all staff in branch..." rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Send to Branch</button>
                                    <div id="smsBranchSmsResponse" style="margin-top: 10px; font-size: 13px;"></div>
                                </form>
                            </div>
                            
                            {{-- Gateway Settings Tab --}}
                            <div id="sms-gateway" class="sms-tab-content" style="display: none;">
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">SMS Enabled</label>
                                    <select id="smsEnabled" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Active Gateway</label>
                                    <select id="activeGateway" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                        <option>Select Gateway</option>
                                    </select>
                                </div>
                                <button class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Save Changes</button>
                            </div>
                        </div>
                    </div>

                    {{-- Notification Settings Section --}}
                    <div id="settings-notifications" class="settings-section" style="display: none;">
                        <h4 style="margin-top: 0; color: #333; font-size: 16px; margin-bottom: 15px;">Notification Settings</h4>
                        <div style="margin-bottom: 15px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" id="emailNotifications" checked>
                                <span>Email Notifications</span>
                            </label>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" id="smsNotifications" checked>
                                <span>SMS Notifications</span>
                            </label>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" id="pushNotifications">
                                <span>Push Notifications</span>
                            </label>
                        </div>
                        <button class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Save Changes</button>
                    </div>

                    {{-- Deposits Settings Section --}}
                    <div id="settings-deposits" class="settings-section" style="display: none;">
                        <h4 style="margin-top: 0; color: #333; font-size: 16px; margin-bottom: 15px;">Deposit Settings</h4>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Monthly Required Amount</label>
                            <input type="number" id="requiredDeposit" class="form-control" placeholder="5000" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px;">Auto-Calculate</label>
                            <select id="autoCalculate" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="1">Enabled</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                        <button class="btn btn-success btn-sm" style="background: #00a65a; border: none; padding: 8px 16px;">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
</div>
    </div>
</div>

<style>
    .settings-menu-item:hover {
        background: #e9ecef;
    }
    .settings-menu-item.active {
        background: #e9ecef;
        border-left-color: #00a65a !important;
    }
    .sms-tab-item:hover {
        background: #f0f0f0;
    }
    .sms-tab-item.active {
        border-bottom-color: #00a65a !important;
        font-weight: 600;
    }
</style>