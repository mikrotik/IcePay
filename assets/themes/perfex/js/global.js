 $(window).on('load', function() {
     var height = $(document).outerHeight(true);
     $('.proposal-right').height(height + 'px');
 });
 $(function() {

     $.validator.setDefaults({
         highlight: function(element) {
             $(element).closest('.form-group').addClass('has-error');
         },
         unhighlight: function(element) {
             $(element).closest('.form-group').removeClass('has-error');
         },
         errorElement: 'span',
         errorClass: 'text-danger',
          errorPlacement: function(error, element) {
            if (element.parent('.input-group').length || element.parents('.chk').length) {
                if (!element.parents('.chk').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element.parents('.chk'));
                }
            } else {
                error.insertAfter(element);
            }
        },
     });
     init_progress_bars();
     init_color_pickers();
     jQuery.datetimepicker.setLocale(locale);
     init_datepicker();

     $('body').tooltip({
         selector: '[data-toggle="tooltip"]'
     });
     // Init popovers
     $('body').popover({
         selector: '[data-toggle="popover"]'
     });
     // Close all popovers if user click on body and the click is not inside the popover content area
     $('body').on('click', function(e) {
         $('[data-toggle="popover"]').each(function() {
             //the 'is' for buttons that trigger popups
             //the 'has' for icons within a button that triggers a popup
             if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                 $(this).popover('hide');
             }
         });
     });

    // Add are you sure on all delete links (onclick is not included here)
    $('body').on('click', '._delete', function(e) {
        var r = confirm(confirm_action_prompt);
        if (r == true) {
            return true;
        } else {
            return false;
        }
    });

    $('.article_useful_buttons button').on('click', function(e) {
         e.preventDefault();
         var data = {};
         data.answer = $(this).data('answer');
         data.articleid = $('input[name="articleid"]').val();
         $.post(site_url+'clients/add_kb_answer', data).done(function(response) {
             response = JSON.parse(response);
             if (response.success == true) {
                 $(this).focusout();
             }
             $('.answer_response').html(response.message);
         });
     });

     $('body').on('click', '[data-loading-text]', function() {
         var form = $(this).data('form');
         if (form != null) {
             if ($(form).valid()) {
                 $(this).button('loading');
             }
         } else {
             $(this).button('loading');
         }
     });

     $('#survey_form').validate();
     var survey_fields_required = $('#survey_form').find('[data-required="1"]');
     $.each(survey_fields_required, function() {
       $(this).rules("add", {
           required: true
       });
       var name = $(this).data('for');
       var label = $(this).parents('.form-group').find('[for="' + name + '"]');
       if (label.length > 0) {
        if (label.find('.req').length == 0) {
            label.prepend(' <small class="req text-danger">* </small>');
        }
    }
});

 });

 function init_progress_bars() {
     setTimeout(function() {
         $('.progress .progress-bar').each(function() {
             var bar = $(this);
             var perc = bar.attr("data-percent");
             var current_perc = 0;
             var progress = setInterval(function() {
                 if (current_perc >= perc) {
                     clearInterval(progress);
                     if (perc == 0) {
                         bar.css('width', 0 + '%');
                     }
                 } else {
                     current_perc += 1;
                     bar.css('width', (current_perc) + '%');
                 }
                 if (!bar.hasClass('no-percent-text')) {
                     bar.text((current_perc) + '%');
                 }
             }, 10);
         });
     }, 300);
 }
function init_color_pickers() {
    $('body').find('.colorpicker-input').colorpicker({
        format: "hex"
    });
}
 function init_datepicker() {
     var datepickers = $('.datepicker');
     var datetimepickers = $('.datetimepicker');
     var opt;
     $.each(datepickers, function() {
         var opt = {
             format: date_format,
             timepicker: false,
             lazyInit: true,
             scrollInput:false,
             dayOfWeekStart:calendar_first_day,
         };
         var max_date = $(this).data('date-end-date');
         var min_date = $(this).data('date-min-date');
         if (max_date) {
             opt.maxDate = max_date;
         }
         if (min_date) {
             opt.minDate = min_date;
         }
         $(this).datetimepicker(opt);
     });
     var opt_time;
     $.each(datetimepickers, function() {
         opt_time = {
             format: date_format + ' H:i',
             lazyInit: true,
             scrollInput:false,
             dayOfWeekStart:calendar_first_day,
         };
         var max_date = $(this).data('date-end-date');
         var min_date = $(this).data('date-min-date');
         if (max_date) {
             opt_time.maxDate = max_date;
         }
         if (min_date) {
             opt_time.minDate = min_date;
         }
         $(this).datetimepicker(opt_time);
     });
     $('.calendar-icon').on('click', function() {
         $(this).parents('.date').find('.datepicker').datetimepicker('show');
         $(this).parents('.date').find('.datetimepicker').datetimepicker('show');
     });
 }

 function is_mobile() {
     if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
         return true;
     }
     return false;
 }

// Generate float alert
function alert_float(type, message) {
    var aId, el;
    aId = $('body').find('float-alert').length;
    aId++;
    aId = 'alert_float_' + aId;
    el = $('<div id="' + aId + '" class="float-alert animated fadeInRight col-xs-11 col-sm-4 alert alert-' + type + '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="fa fa-bell-o" data-notify="icon"></span><span class="alert-title">' + message + '</span></div>');
    $('body').append(el);

    setTimeout(function() {
        $('#' + aId).hide('fast', function(){ $('#' + aId).remove(); });
    }, 4000);
}
