/**
 * Created by Tj on 4/23/2018.
 */
if (jQuery().iCheck) {
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
}
if (jQuery().wysihtml5) {
    $('.wysihtml5').wysihtml5({});
}
if (jQuery().datepicker) {
    $('.date-picker').datepicker({
        orientation: "left",
        autoclose: true,
        format: "yyyy-mm-dd"
    });
    $('.year-picker').datepicker({
        orientation: "left",
        autoclose: true,
        format: "yyyy",
        viewMode: 'years',
        minViewMode: 'years',
        startView: "decade"
    });
    //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
}
$('.time-picker').datetimepicker({
    format: 'HH:mm'
});
if (jQuery().TouchSpin) {
    $(".touchspin").TouchSpin({
        buttondown_class: 'btn blue',
        buttonup_class: 'btn blue',
        min: 0,
        max: 10000000000,
        step: 0.01,
        decimals: 2,
        boostat: 5,
        maxboostedstep: 1,
        prefix: ''
    });
}
if (jQuery().select2) {
    $(".select2").select2({

    });
}
$(".fancybox").fancybox();
$('.delete').on('click', function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: '',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancel'
    }).then(function () {
        window.location = href;
    })
});
$('.confirm').on('click', function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: '',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancel'
    }).then(function () {
        window.location = href;
    })
});
tinyMCE.init({
    selector: ".tinymce",
    theme: "modern",
    link_list: [
        {title: 'My page 1', value: 'http://www.tinymce.com'},
        {title: 'My page 2', value: 'http://www.tecrail.com'}
    ],
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
        "table contextmenu directionality emoticons paste textcolor code "
    ],
    relative_urls: false,
    remove_script_host: false,
    convert_urls: false,
    browser_spellcheck: true,
    image_advtab: true,
    toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
    toolbar2: "image | link unlink anchor | print preview code  | youtube | qrcode | flickr | picasa | forecolor backcolor | easyColorPicker"
});
$('[data-toggle="tooltip"]').tooltip();
$(document).ready(function(){
    $(".validate").validate({
        rules: {
            field: {
                required: true,
                step: 10
            }
        }, highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

});
$(document).on("ajaxComplete", function () {
    if (jQuery().iCheck) {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    }
    if (jQuery().wysihtml5) {
        $('.wysihtml5').wysihtml5({});
    }
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            orientation: "left",
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }
    $('.time-picker').datetimepicker({
        format: 'HH:mm'
    });
    if (jQuery().TouchSpin) {
        $(".touchspin").TouchSpin({
            buttondown_class: 'btn blue',
            buttonup_class: 'btn blue',
            min: 0,
            max: 10000000000,
            step: 0.01,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 1,
            prefix: ''
        });
    }
    if (jQuery().select2) {
        $(".select2").select2({

        });
    }
    $(".fancybox").fancybox();
    $('.delete').on('click', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: 'Are you sure?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel'
        }).then(function () {
            window.location = href;
        })
    });
    $('.confirm').on('click', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: 'Are you sure?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel'
        }).then(function () {
            window.location = href;
        })
    });
    tinyMCE.init({
        selector: ".tinymce",
        theme: "modern",
        link_list: [
            {title: 'My page 1', value: 'http://www.tinymce.com'},
            {title: 'My page 2', value: 'http://www.tecrail.com'}
        ],
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking spellchecker",
            "table contextmenu directionality emoticons paste textcolor code "
        ],
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        browser_spellcheck: true,
        image_advtab: true,
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "image | link unlink anchor | print preview code  | youtube | qrcode | flickr | picasa | forecolor backcolor | easyColorPicker"
    });
    $('[data-toggle="tooltip"]').tooltip();



});
