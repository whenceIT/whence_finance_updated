<!-- SMS Modal -->
<div id="sms-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1001;">
    <div style="background: white; padding: 20px; border-radius: 10px; width: 90%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <h4 style="margin: 0 0 15px 0; color: #333;">Send SMS Notification</h4>
        <form id="sms-form">
            <div style="margin-bottom: 15px;">
                <label for="sms-type" style="display: block; margin-bottom: 5px; font-weight: bold;">Message Type:</label>
                <select id="sms-type" name="message_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                    <option value="single">Single SMS Notifications</option>
                    <option value="overdue">Overdue Reminder</option>
                    <option value="balances">Balances Reminder</option>
                </select>
            </div>
            <div id="single-sms-fields">
                <div style="margin-bottom: 15px;">
                    <label for="sms-phone" style="display: block; margin-bottom: 5px; font-weight: bold;">Phone Number:</label>
                    <input type="text" id="sms-phone" name="phone" placeholder="Enter phone number" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sms-message" style="display: block; margin-bottom: 5px; font-weight: bold;">Message:</label>
                    <textarea id="sms-message" name="message" placeholder="Enter message" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;" required></textarea>
                </div>
            </div>
            <div id="bulk-sms-fields" style="display: none;">
                <label for="sms-office" style="display: block; margin-bottom: 5px; font-weight: bold;">Office:</label>
                <select id="sms-office" name="office_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
                <div id="user-select" style="display: none; margin-top: 10px;">
                    <label for="sms-user" style="display: block; margin-bottom: 5px; font-weight: bold;">User:</label>
                    <select id="sms-user" name="user_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                        <option value="">Select User</option>
                    </select>
                </div>
            </div>
            <p class="sample-text" style="display: none;">Dear Customer, this is a reminder that your loan of ZMW 0 is overdue. Kindly make your payment to avoid penalties or further legal action. For assistance, contact 0972654596.</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" id="sms-cancel" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 10px 20px; background: #00a65a; color: white; border: none; border-radius: 5px; cursor: pointer;">Send</button>
            </div>
        </form>
        <div id="sms-response" style="margin-top: 15px; display: none;"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function resetModal() {
            $('#sms-form')[0].reset();
            $('#user-select').hide();
            $('#sms-user').empty().append('<option value="">Select User</option>').removeAttr('required');
            $('#sms-response').hide().empty();
            // Restore field visibility to default (single SMS)
            $('#single-sms-fields').show();
            $('#bulk-sms-fields').hide();
            $('#sms-phone').attr('required', 'required');
            $('#sms-message').attr('required', 'required');
            $('#sms-office').removeAttr('required');
            $('.sample-text').hide();
        }

        $('#sms-floating-btn').on('click', function() {
            $('#sms-modal').css('display', 'flex');
        });

        $('#sms-cancel').on('click', function() {
            $('#sms-modal').hide();
            resetModal();
        });

        $('#sms-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
                resetModal();
            }
        });

        // Toggle fields based on message type
        $('#sms-type').on('change', function() {
            if ($(this).val() === 'overdue' || $(this).val() === 'balances') {
                $('#single-sms-fields').hide();
                $('#bulk-sms-fields').show();
                $('#sms-phone').removeAttr('required');
                $('#sms-message').removeAttr('required');
                $('#sms-office').attr('required', 'required');
                $('.sample-text').show();
                // Reset user select when switching type
                $('#user-select').hide();
                $('#sms-user').empty().append('<option value="">Select User</option>').removeAttr('required');
                $('#sms-office').val('');
            } else {
                $('#single-sms-fields').show();
                $('#bulk-sms-fields').hide();
                $('#sms-phone').attr('required', 'required');
                $('#sms-message').attr('required', 'required');
                $('#sms-office').removeAttr('required');
                $('.sample-text').hide();
                $('#user-select').hide();
                $('#sms-user').removeAttr('required');
            }
        });

        // Fetch users when office is selected
        $('#sms-office').on('change', function() {
            var officeId = $(this).val();
            if (officeId) {
                $('#user-select').hide();
                $('#sms-user').empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '/api/get-office-users/' + officeId,
                    method: 'GET',
                    success: function(users) {
                        var $userSelect = $('#sms-user');
                        $userSelect.empty().append('<option value="">Select User</option>');
                        if (users.length === 0) {
                            $userSelect.append('<option value="" disabled>No users found for this office</option>');
                        } else {
                            $.each(users, function(i, user) {
                                $userSelect.append('<option value="' + user.id + '">' + user.name + '</option>');
                            });
                        }
                        $('#user-select').show();
                        $userSelect.attr('required', 'required');
                    },
                    error: function() {
                        $('#sms-user').empty().append('<option value="">Select User</option>');
                        $('#user-select').show();
                        alert('Failed to load users for this office. Please try again.');
                    }
                });
            } else {
                $('#user-select').hide();
                $('#sms-user').empty().append('<option value="">Select User</option>').removeAttr('required');
            }
        });

        $('#sms-form').on('submit', function(e) {
            e.preventDefault();

            var messageType = $('#sms-type').val();
            var userId      = $('#sms-user').val();
            var formData    = $(this).serialize();
            var url;

            if (messageType === 'single') {
                url = '/api/send-sms';
            } else if (userId) {
                // User selected — send to that officer's clients only
                url = '/api/send-officer-sms';
            } else {
                // Office selected but no user — send to whole office
                url = '/api/send-bulk-sms';
            }

            $('#sms-response').html('<div style="color: #007bff;"><i class="fa fa-spinner fa-spin"></i> Sending...</div>').show();
            $('[type=submit]', this).prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        var detail = response.messages_sent !== undefined
                            ? ' (' + response.messages_sent + ' of ' + response.total_loans + ' sent)'
                            : '';
                        $('#sms-response').html('<div style="color: #28a745;"><i class="fa fa-check"></i> SMS sent successfully!' + detail + '</div>');
                        resetModal();
                        $('#sms-modal').hide();
                    } else {
                        $('#sms-response').html('<div style="color: #dc3545;"><i class="fa fa-times"></i> Error: ' + (response.error || 'Unknown error') + '</div>');
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to send SMS';
                    $('#sms-response').html('<div style="color: #dc3545;"><i class="fa fa-times"></i> Error: ' + msg + '</div>');
                },
                complete: function() {
                    $('[type=submit]', '#sms-form').prop('disabled', false);
                }
            });
        });
    });
</script>
