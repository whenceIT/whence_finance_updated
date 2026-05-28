{{-- Client Search Bottom Sheet Component --}}
{{-- Usage: @include('components.client-search-bottom-sheet') --}}

<button id="clientSearchFAB" class="client-search-fab" title="Search Clients">
    <i class="fa fa-search"></i>
</button>

<!-- Client Search Bottom Sheet Modal -->
<div class="bottom-sheet-overlay" id="clientSearchBottomSheetOverlay">
    <div class="bottom-sheet" id="clientSearchBottomSheet" style="max-height: 90vh;">
        <button class="bottom-sheet-close" id="closeClientSearchBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content" style="padding: 20px;">
            <h3 class="bottom-sheet-title" style="margin-left: 40%; font-size: 20px; margin-bottom: 10px;">
                <i class="fa fa-users"></i> Client Search
            </h3>

            <!-- Search Filters -->
            <form id="clientSearchForm" style="margin-bottom: 20px;">
                <div style="margin-left: 30%; max-width: 600px;" class="row justify-content-center">
                    <div class="col-lg-12 col-md-8 col-sm-10">
                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 8px;">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                <input type="text" name="mobile" class="form-control" placeholder="Enter mobile number" style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 10px 30px; margin-right: 10px;">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" id="clearClientSearch" class="btn btn-default" style="border-radius: 25px; padding: 10px 30px;">
                        <i class="fa fa-times"></i> Clear
                    </button>
                </div>
            </form>

            <!-- Live Search Results -->
            <div id="clientSearchResults" style="display: none;">
                <hr style="margin: 15px 0;">
                <h4 style="margin-bottom: 15px; font-weight: 600;">
                    <i class="fa fa-list"></i> Results (<span id="clientSearchCount">0</span>)
                </h4>
                <div id="clientSearchResultsBody" class="client-results-grid">
                    <!-- Results populated via AJAX -->
                </div>
                <div id="clientSearchPagination" class="text-center" style="margin-top: 15px;"></div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Floating Action Button */
    .client-search-fab {
        position: fixed;
        bottom: 90px;
        right: 30px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00a04a, #007bff);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 160, 74, 0.4);
        font-size: 22px;
        cursor: pointer;
        z-index: 1040;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .client-search-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 160, 74, 0.6);
    }
    .client-search-fab:active {
        transform: scale(0.95);
    }

    /* Results Grid */
    .client-results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
        max-height: 50vh;
        overflow-y: auto;
        padding: 5px;
    }

    .client-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 18px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .client-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        border-color: #00a04a;
    }
    .client-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #00a04a, #007bff);
    }

    .client-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .client-card-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00a04a, #007bff);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .client-card-info {
        flex: 1;
        min-width: 0;
    }
    .client-card-name {
        font-size: 15px;
        font-weight: 700;
        color: #222;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .client-card-meta {
        font-size: 12px;
        color: #666;
        margin-top: 2px;
    }

    .client-card-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .client-card-detail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #555;
    }
    .client-card-detail i {
        width: 16px;
        text-align: center;
        color: #00a04a;
    }
    .client-card-detail span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .client-card-footer {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .client-card-status {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 600;
    }
    .client-card-status.active { background: #d4edda; color: #155724; }
    .client-card-status.pending { background: #fff3cd; color: #856404; }
    .client-card-status.inactive { background: #f8d7da; color: #721c24; }
    .client-card-status.closed { background: #d6d8db; color: #383d41; }
    .client-card-status.declined { background: #f5c6cb; color: #721c24; }
    .client-card-view {
        font-size: 12px;
        color: #00a04a;
        text-decoration: none;
        font-weight: 600;
    }
    .client-card-view:hover {
        color: #007bff;
    }

    /* Empty & Loading States */
    .client-search-empty {
        text-align: center;
        padding: 40px 20px;
        color: #888;
        grid-column: 1 / -1;
    }
    .client-search-empty i {
        font-size: 48px;
        margin-bottom: 10px;
        color: #ddd;
    }
    .client-search-loading {
        text-align: center;
        padding: 30px;
        color: #00a04a;
        grid-column: 1 / -1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .client-search-fab {
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
            font-size: 20px;
        }
        .client-results-grid {
            grid-template-columns: 1fr;
            max-height: 45vh;
        }
        #clientSearchBottomSheet .bottom-sheet-content {
            padding: 15px !important;
        }
        #clientSearchBottomSheet .bottom-sheet-title {
            font-size: 18px !important;
        }
    }
</style>

<script>
    (function() {
        var currentClientPage = 1;
        var lastClientSearchParams = {};

// Open bottom sheet
        function openClientSearchSheet() {
            $('#clientSearchBottomSheetOverlay').addClass('active');
            $('#clientSearchBottomSheet').addClass('active');
        }

        // Close bottom sheet
        function closeClientSearchSheet() {
            $('#clientSearchBottomSheetOverlay').removeClass('active');
            $('#clientSearchBottomSheet').removeClass('active');
        }

        // Build client card HTML
        function buildClientCard(client) {
            var initials = (client.first_name ? client.first_name.charAt(0) : '') + (client.last_name ? client.last_name.charAt(0) : '');
            var name = (client.first_name || '') + ' ' + (client.last_name || '');
            var mobile = client.mobile || '-';
            var office = client.office || '-';
            var staff = client.staff || '-';
            var staffPhone = client.staff_phone || '-';
            var loanInfo = client.loans_count > 0 ? 'Loan: KES ' + (client.principal || 0) + (client.disbursement_date ? ' | ' + client.disbursement_date : '') : '';
            var profileUrl = client.id ? '/client/' + client.id + '/show' : '#';

            return $(
                '<div class="client-card">' +
                    '<div class="client-card-header">' +
                        '<div class="client-card-avatar">' + initials + '</div>' +
                        '<div class="client-card-info">' +
                            '<p class="client-card-name">' + escapeHtml(name) + '</p>' +
                            '<p class="client-card-meta">' + escapeHtml(mobile) + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div class="client-card-body">' +
                        '<div class="client-card-detail"><i class="fa fa-building"></i><span>' + escapeHtml(office) + '</span></div>' +
                        '<div class="client-card-detail"><i class="fa fa-user"></i><span>' + escapeHtml(staff) + '</span></div>' +
                        '<div class="client-card-detail"><i class="fa fa-phone"></i><span>' + escapeHtml(staffPhone) + '</span></div>' +
                        (loanInfo ? '<div class="client-card-detail"><i class="fa fa-money"></i><span>' + escapeHtml(loanInfo) + '</span></div>' : '') +
                    '</div>' +
                    '<div class="client-card-footer">' +
                        '<a href="' + profileUrl + '" class="client-card-view">View <i class="fa fa-arrow-right"></i></a>' +
                    '</div>' +
                '</div>'
            );
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Render pagination
        function renderPagination(pagination) {
            var html = '';
            if (pagination.last_page > 1) {
                html += '<nav><ul class="pagination pagination-sm" style="margin: 0;">';
                if (pagination.prev_page_url) {
                    html += '<li><a href="#" class="client-page-link" data-page="' + (pagination.current_page - 1) + '">«</a></li>';
                }
                for (var i = 1; i <= pagination.last_page; i++) {
                    var activeClass = i === pagination.current_page ? 'active' : '';
                    html += '<li class="' + activeClass + '"><a href="#" class="client-page-link" data-page="' + i + '">' + i + '</a></li>';
                }
                if (pagination.next_page_url) {
                    html += '<li><a href="#" class="client-page-link" data-page="' + (pagination.current_page + 1) + '">»</a></li>';
                }
                html += '</ul></nav>';
            }
            return html;
        }

        // Perform search
        function performClientSearch(params, page) {
            params = params || {};
            page = page || 1;
            var $results = $('#clientSearchResults');
            var $body = $('#clientSearchResultsBody');
            var $count = $('#clientSearchCount');
            var $pagination = $('#clientSearchPagination');

            $results.show();
            $body.html('<div class="client-search-loading"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Searching...</p></div>');

            var searchData = $.extend({}, params, { page: page });

            $.ajax({
                url: '/api/search/clients',
                method: 'POST',
                data: JSON.stringify(searchData),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $body.empty();
                    if (response.data && response.data.length > 0) {
                        $count.text(response.total);
                        $.each(response.data, function(i, client) {
                            $body.append(buildClientCard(client));
                        });
                        $pagination.html(renderPagination(response));
                    } else {
                        $count.text('0');
                        $body.html(
                            '<div class="client-search-empty">' +
                                '<i class="fa fa-user-times"></i>' +
                                '<p>No clients found matching your criteria.</p>' +
                            '</div>'
                        );
                        $pagination.empty();
                    }
                },
                error: function(xhr) {
                    $count.text('0');
                    $body.html(
                        '<div class="client-search-empty">' +
                            '<i class="fa fa-exclamation-triangle"></i>' +
                            '<p>Error loading results. Please try again.</p>' +
                        '</div>'
                    );
                    $pagination.empty();
                }
            });
        }

        // Event bindings
        $('#clientSearchFAB').on('click', openClientSearchSheet);
        $('#closeClientSearchBottomSheet').on('click', closeClientSearchSheet);
        $('#clientSearchBottomSheetOverlay').on('click', function(e) {
            if (e.target === this) closeClientSearchSheet();
        });

        $('#clientSearchForm').on('submit', function(e) {
            e.preventDefault();
            var params = {};
            $(this).serializeArray().forEach(function(field) {
                if (field.value) params[field.name] = field.value;
            });
            lastClientSearchParams = params;
            currentClientPage = 1;
            performClientSearch(params, 1);
        });

        $('#clearClientSearch').on('click', function() {
            $('#clientSearchForm')[0].reset();
            $('#clientSearchResults').hide();
            $('#clientSearchResultsBody').empty();
            lastClientSearchParams = {};
            currentClientPage = 1;
        });

        // Pagination delegated clicks
        $(document).on('click', '.client-page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page) {
                currentClientPage = page;
                performClientSearch(lastClientSearchParams, page);
            }
        });
    })();
</script>