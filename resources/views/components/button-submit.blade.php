
<button type="submit" class="btn btn-primary" id="save-btn">
    <span class="spinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i> </span>
    <span class="text">Save</span>
</button>
<script>
$(document).ready(function() {
    $('#save-btn').click(function() {
        $(this).prop('disabled', true);
        $(this).find('.spinner').show();
        $(this).find('.text').text('Saving...');
    });
});
</script>
