// Set datatables error throw console log
$.fn.dataTable.ext.errMode = 'throw';
// Delay function
var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

$.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'p',
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

function merge_field_format_url(url, node, on_save, name) {
    // Merge fields url
    if (url.indexOf("{") > -1 && url.indexOf("}") > -1) {
        url = '{' + url.split('{')[1];
    }
    // Return new URL
    return url;
}

// Function to slug string
function slugify(string) {
    return string
        .toString()
        .trim()
        .toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w\-]+/g, "")
        .replace(/\-\-+/g, "-")
        .replace(/^-+/, "")
        .replace(/-+$/, "");
}

var original_top_search_val,
    tab_active = get_url_param('tab'),
    tab_group = get_url_param('group'),
    side_bar = $('#side-menu'),
    content_wrapper = $('#wrapper'),
    setup_menu = $('#setup-menu-wrapper'),
    menu_href_selector,
    activity_log_table = $('.table-activity-log'),
    alerts = $('#alerts'),
    total_new_post_files = 0,
    newsfeed_posts_page = 0,
    track_load_post_likes = 0,
    track_load_comment_likes = 0,
    post_likes_total_pages = 0,
    comment_likes_total_pages = 0,
    postid = 0,
    available_reminders_table = [
        '.table-reminders',
        '.table-invoices',
        '.table-reminders-leads',
        '.table-invoices',
        '.table-estimates',
        '.table-proposals',
        '.table-expenses',
    ],
    setup_menu_item = $('#setup-menu-item');
Dropzone.options.newsFeedDropzone = false;
Dropzone.options.salesUpload = false;
$(window).on("resize click", function() {
    // Add special class to minimalize page elements when screen is less than 768px
    setBodySmall();
    // Waint until metsiMenu, collapse and other effect finish and set wrapper height
    setTimeout(function() {
        mainWrapperHeightFix();
    }, 300);
});

$(function() {

    $(document).on('focusin', function(e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    });

    if (show_setup_menu_item_only_on_hover == 1 && !is_mobile()) {
        side_bar.hover(
            function() {
                setTimeout(function() {
                    setup_menu_item.css("display", "block");
                }, 300);
            },
            function() {
                setup_menu_item.css("display", "none");
            }
        );
    }

    if (alerts.length > 0) {
        // Set timeout to remove php alerts added from flashdata
        setTimeout(function() {
            alerts.slideUp();
        }, 4000);
    }
    // Check for active tab if any found in url so we can set this tab to active - Tab active is defined on top
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }
    // Check for active tab groups (this is custom made) and not related to boostrap - tab_group is defined on top
    if (tab_group) {
        // Do not track bootstrap default tabs
        $('body').find('.nav-tabs li').not('[role="presentation"]').removeClass('active');
        // Add the class active to this group manualy so the tab can be highlighted
        $('body').find('.nav-tabs [data-group="' + tab_group + '"]').parents('li').addClass('active');
    }
    // Set datetimepicker locale
    jQuery.datetimepicker.setLocale(locale);
    // Set moment locale
    moment.locale(locale);
    // Set timezone locale
    moment().tz(timezone).format();
    // Init tinymce editors
    init_editor();
    // Dont close dropdown on timer top click
    $('body').on('click', '#started-timers-top', function(e) {
        e.stopPropagation();
    });
    // Init inputs used for tags
    init_tags_inputs();
    // Init all color pickers
    init_color_pickers();
    // Init tables offline (no serverside)
    initDataTableOffline();

    // Bootstrap switch active or inactive global function
    $('body').on('change', '.onoffswitch input', function(event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });
    /* Custom fields hyperlink */
    custom_fields_hyperlink();
    // Init lightboxes if found
    init_lightbox();
    // Init progress bars
    init_progress_bars();
    // Init datepickers
    init_datepicker();
    // Init bootstrap selectpicker
    init_selectpicker();
    // Optimize body
    setBodySmall();
    // Optimize wrapper height
    mainWrapperHeightFix();
    // Validate all form for reminders
    init_form_reminder();
    // On delete reminder reload the tables
    $('body').on('click', '.delete-reminder', function() {
        var r = confirm(confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                // Looop throug all availble reminders table to reload the data
                $.each(available_reminders_table, function(i, table) {
                    if ($.fn.DataTable.isDataTable(table)) {
                        $('body').find(table).DataTable().ajax.reload();
                    }
                });
            }, 'json');
        }
        return false;
    });

    // Search by tags from the tables for any tag clicked.
    $('body').on('click','table.dataTable tbody .tags-labels .label-tag',function(){
        $(this).parents('table').DataTable().search($(this).find('.tag').text()).draw();
        $('div.dataTables_filter input').focus();
    });

    $('[data-can-view-own],[data-can-view]').on('change', function() {
        var is_own_attr = $(this).attr('data-can-view-own');
        view_chk_selector = $(this).parents('tr').find('td input[' + (typeof is_own_attr !== typeof undefined && is_own_attr !== false ? 'data-can-view' : 'data-can-view-own') + ']');
        view_chk_selector.prop('checked', false);
        if ($(this).prop('checked') == true) {
            view_chk_selector.prop('disabled', true);
        } else {
            view_chk_selector.prop('disabled', false);
        }
    });

    /* Tasks */
    // Init single task data
    if (typeof(taskid) !== 'undefined' && taskid !== '') {
        init_task_modal(taskid);
    }

    $('body').on('change', 'input[name="checklist-box"]', function() {
        var checked = $(this).prop('checked');
        if (checked == true) {
            val = 1;
        } else {
            val = 0;
        }
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.get(admin_url + 'tasks/checkbox_action/' + listid + '/' + val);
        recalculate_checklist_items_progress();
    });

    $("body").on('keyup paste click', "textarea[name='checklist-description']", function(e) {
        do_task_checklist_items_height($(this));
    });

    $('body').on('click', '.task-single-delete-timesheet', function(e) {
        e.preventDefault();
        var r = confirm(confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var _delete_timesheet_task_id = $(this).data('task-id');
            $.get($(this).attr('href'), function() {
                init_task_modal(_delete_timesheet_task_id);
                setTimeout(function() {
                    reload_tasks_tables();
                }, 20);
            });
        }
    });

    $('body').on('click', '.task-single-add-timesheet', function(e) {
        e.preventDefault();
        var start_time = $('body').find('.task-modal-single input[name="timesheet_start_time"]').val();
        var end_time = $('body').find('.task-modal-single input[name="timesheet_end_time"]').val();
        if (start_time != '' && end_time != '') {
            var data = {};
            data.start_time = start_time;
            data.end_time = end_time;
            data.timesheet_task_id = $(this).data('task-id');
            data.timesheet_staff_id = $('body').find('.task-modal-single select[name="single_timesheet_staff_id"]').val();
            $.post(admin_url + 'tasks/log_time', data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    init_task_modal(data.timesheet_task_id);
                    alert_float('success', response.message);
                    setTimeout(function() {
                        reload_tasks_tables();
                    }, 20);
                } else {
                    alert_float('warning', response.message);
                }
            });
        }
    });

    $('body').on('click', '.copy_task_action', function() {
        var data = {};
        var copy_task_assignees, copy_task_followers, copy_task_checklist_items, copy_task_attachments;
        data.copy_from = $(this).data('task-copy-from');
        data.copy_task_assignees = $('body').find('#copy_task_assignees').prop('checked');
        data.copy_task_followers = $('body').find('#copy_task_followers').prop('checked');
        data.copy_task_checklist_items = $('body').find('#copy_task_checklist_items').prop('checked');
        data.copy_task_attachments = $('body').find('#copy_task_attachments').prop('checked');
        $.post(admin_url + 'tasks/copy', data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                init_task_modal(response.new_task_id);
                reload_tasks_tables();
            }
            alert_float(response.alert_type, response.message);
        });
        return false;
    });

    $('body').on('click', '.new-task-to-milestone', function(e) {
        e.preventDefault();
        var milestone_id = $(this).parents('.milestone-column').data('milestone-id');
        new_task(admin_url + 'tasks/task?rel_type=project&rel_id=' + project_id + '&milestone_id=' + milestone_id);
    });

    // Focus on subject when adding new task
    $('body').on('shown.bs.modal', '#_task_modal', function() {
        if ($('input[name="task_is_edit"]').length == 0) {
            tinyMCE.execCommand('mceAddControl', false, 'tinymce-task');
            $('body').find('#_task_modal input[name="name"]').focus();
        }
    });

    // Remove the tinymce description task editor
    $('body').on('hidden.bs.modal', '#_task_modal', function() {
        tinyMCE.remove('.tinymce-task');
        // Clear _ticket_message from single tickets in case user tried to convert ticket to task to prevent populating the fields again with the last ticket message click
        if (typeof(_ticket_message) != 'undefined') {
            _ticket_message = undefined;
        }
        $('body #_task_modal .datepicker').datetimepicker('destroy');
    });

    // Fix the modal left column height when task modal open
    $('body').on('shown.bs.modal', '.task-modal-single', function() {
        fix_task_modal_left_col_height();
        $(document).off('focusin.modal');
    });

    // ON hidden task single modal remove the dropzone for uplodas
    $('body').on('hidden.bs.modal', '.task-modal-single', function() {
        if (typeof(taskAttachmentDropzone) != 'undefined') {
            taskAttachmentDropzone.destroy();
        }
    });

    // Don't allow the task modal to close if lightbox is visible in for the task attachments
    // Used when user hit the ESC button
    $('body').on('hide.bs.modal', '.task-modal-single', function() {
       if($('#lightbox').is(':visible') == true){
            return false;
       }
    });

    // On focus out on the taks modal single update the tags in case changes are found
    $('body').on('blur', '.task-modal-single ul.tagit li.tagit-new input', function() {
        setTimeout(function() {
            task_single_update_tags();
        }, 100);
    });

    // when click close modal task tracking stats fix to do not close both modals
    $('body').on('click', '.close-task-stats', function() {
        $('#task-tracking-stats-modal').modal('hide');
    });

    // Update taks checklist items when focusing out
    $('body').on('blur', 'textarea[name="checklist-description"]', function() {
        var description = $(this).val();
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.post(admin_url + 'tasks/update_checklist_item', {
            description: description,
            listid: listid
        });
    });

    // Assign task to staff member
    $('body').on('change', 'select[name="select-assignees"]', function() {
        $('body').append('<div class="dt-loader"></div>');
        var data = {};
        data.assignee = $('select[name="select-assignees"]').val();
        if (data.assignee != '') {
            data.taskid = $(this).attr('data-task-id');
            $.post(admin_url + 'tasks/add_task_assignees', data).done(function(response) {
                $('body').find('.dt-loader').remove();
                response = JSON.parse(response);
                reload_tasks_tables();
                init_task_modal(data.taskid);
            });
        }
    });

    // Add follower to task
    $('body').on('change', 'select[name="select-followers"]', function() {
        var data = {};
        data.follower = $('select[name="select-followers"]').val();
        if (data.follower != '') {
            data.taskid = $(this).attr('data-task-id');
            $('body').append('<div class="dt-loader"></div>');
            $.post(admin_url + 'tasks/add_task_followers', data).done(function(response) {
                response = JSON.parse(response);
                $('body').find('.dt-loader').remove();
                init_task_modal(data.taskid);
            });
        }
    });

    // Task modal single chart for logged time by assigned users
    $('body').on('show.bs.modal', '#task-tracking-stats-modal', function() {
        var tracking_chart_selector = $('body').find('#task-tracking-stats-chart');
        setTimeout(function() {
            if (typeof(task_track_chart) != 'undefined') {
                task_track_chart.destroy();
            }
            task_track_chart = new Chart(tracking_chart_selector, {
                type: 'line',
                data: task_tracking_stats_data,
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return decimalToHM(tooltipItems.yLabel);
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                userCallback: function(label, index, labels) {
                                    return decimalToHM(label);
                                },
                            }
                        }]
                    },
                }
            });
        }, 800);
    });

    /* Tasks End */
    $('body').on('shown.bs.modal', '#sync_data_proposal_data', function() {
        if ($('#sync_data_proposal_data').data('rel-type') == 'lead') {
            $('.lead-modal .modal-content').eq(0).css('height', ($('#sync_data_proposal_data .modal-content').height() + 80) + 'px').css('overflow-x', 'hidden');
        }
    });

    $('body').on('hidden.bs.modal', '#sync_data_proposal_data', function() {
        if ($('#sync_data_proposal_data').data('rel-type') == 'lead') {
            $('.lead-modal .modal-content').prop('style', '');
        }
    });

    if (typeof(c_leadid) != 'undefined' && c_leadid != '') {
        init_lead(c_leadid);
    }

    // Status color change
    $('body').on('click', '.leads-kan-ban .cpicker', function() {
        var color = $(this).data('color');
        var status_id = $(this).parents('.panel-heading-bg').data('status-id');
        $.post(admin_url + 'leads/change_status_color', {
            color: color,
            status_id: status_id
        });
    });

    $('body').on('click', '[lead-edit]', function(e) {
        e.preventDefault();
        $('body .lead-view').toggleClass('hide');
        $('body .lead-edit').toggleClass('hide');
    });

    $('body').on('click', '.new-lead-from-status', function(e) {
        e.preventDefault();
        var status_id = $(this).parents('.kan-ban-col').data('col-status-id');
        init_lead_modal_data(undefined, admin_url + 'leads/lead?status_id=' + status_id);
    });

    $('body').on('change', 'input.include_leads_custom_fields', function() {
        var val = $(this).val();
        var fieldid = $(this).data('field-id');
        if (val == 2) {
            $('#merge_db_field_' + fieldid).removeClass('hide');
        } else {
            $('#merge_db_field_' + fieldid).addClass('hide');
        }
        if (val == 3) {
            $('#merge_db_contact_field_' + fieldid).removeClass('hide');
        } else {
            $('#merge_db_contact_field_' + fieldid).addClass('hide');
        }
    });

    $('input[name="notify_type"]').on('change', function() {
        var val = $(this).val();
        if (val == 'specific_staff') {
            $('#specific_staff_notify').removeClass('hide');
            $('#role_notify').addClass('hide');
        } else if (val == 'roles') {
            $('#specific_staff_notify').addClass('hide');
            $('#role_notify').removeClass('hide');
        } else if (val == 'assigned') {
            $('#specific_staff_notify').addClass('hide');
            $('#role_notify').addClass('hide');
        }
    });

    // Auto focus the lead name if user is adding new lead
    $('body').on('shown.bs.modal', '.lead-modal', function(e) {
        if ($('body').find('.lead-modal input[name="leadid"]').length == 0) {
            $('body').find('.lead-modal input[name="name"]').focus();
        }
    });

    // On hidden lead modal some actions need to be operated here
    $('.lead-modal').on('hidden.bs.modal', function(event) {
        $(this).data('bs.modal', null);
        $('#lead_reminder_modal').html('');
        // clear the hash
        if (!$('.lead-modal').is(':visible')) {
            history.pushState("", document.title, window.location.pathname + window.location.search);
        }
        $('body .lead-modal .datetimepicker').datetimepicker('destroy');
        if (typeof(leadAttachmentsDropzone) != 'undefined') {
            leadAttachmentsDropzone.destroy();
        }
    });

    $('body').on('hidden.bs.modal', '#convert_lead_to_client_modal', function(event) {
        $(this).data('bs.modal', null);
    });

    // Set hash on modal tab change
    $('body').on('click', '.lead-modal a[data-toggle="tab"]', function() {
        if (this.hash == '#tab_lead_profile' || this.hash == '#attachments' || this.hash == '#lead_notes') {
            window.location.hash = this.hash;
        } else {
            history.pushState("", document.title, window.location.pathname + window.location.search);
        }
    });

    // Submit notes on lead modal do ajax not the regular request
    $('body').on('submit', '.lead-modal #lead-notes', function() {
        var form = $(this);
        var data = $(form).serialize();
        $.post(form.attr('action'), data).done(function(lead_id) {
            init_lead_modal_data(lead_id);
        });
        return false;
    });

    // Add additional server params $_POST
    var LeadsServerParams = {
        "custom_view": "[name='custom_view']",
        "assigned": "[name='view_assigned']",
        "status": "[name='view_status']",
        "source": "[name='view_source']",
    }
    // Init the table
    var headers_leads = $('.table-leads').find('th');
    var not_sortable_leads = (headers_leads.length - 1);
    initDataTable('.table-leads', admin_url + 'leads?table_leads=true', [not_sortable_leads, 0], [not_sortable_leads, 0], LeadsServerParams, [10, 'DESC']);
    $.each(LeadsServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            $('.table-leads').DataTable().ajax.reload();
        });
    });

    // When adding if lead is contacted today
    $('body').on('change', 'input[name="contacted_today"]', function() {
        var checked = $(this).prop('checked');
        if (checked == false) {
            $('.lead-select-date-contacted').removeClass('hide');
        } else {
            $('.lead-select-date-contacted').addClass('hide');
        }
    });

    $('body').on('change', 'input[name="contacted_indicator"]', function() {
        var val = $(this).val();
        if (val == 'yes') {
            $('.lead-select-date-contacted').removeClass('hide');
        } else {
            $('.lead-select-date-contacted').addClass('hide');
        }
    });

    // Fix for checkboxes ID duplicate when table goes responsive
    $('body').on('click', 'table.dataTable tbody td:first-child', function() {
        var tr = $(this).parents('tr');
        var row = $(this).parents('table').DataTable().row(tr);
        if (row.child.isShown()) {
            var switchBox = $(tr).next().find('input.onoffswitch-checkbox');
            if (switchBox.length > 0) {
                var switchBoxId = Math.random().toString(16).slice(2);
                switchBox.attr('id', switchBoxId);
                switchBox.next().attr('for', switchBoxId);
            }
        }
    });

    // Show please wait text on button where data-loading-text is added
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

    // Custom close function for reminder modals in case is modal in modal
    $('body').on('click', '.close-reminder-modal', function() {
        $(".reminder-modal-" + $(this).data('rel-type') + '-' + $(this).data('rel-id')).modal('hide');
    });
    // Recalculate responsive for hidden tables
    $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
        $($.fn.dataTable.tables(true)).DataTable().responsive.recalc();
    });

    // Init are you sure on forms
    $('form').not('#single-ticket-form,#calendar-event-form,#proposal-form').areYouSure();

    // Check for active class in sidebar links
    var sidebar_links = side_bar.find('li > a');
    $.each(sidebar_links, function(i, data) {
        var href = $(data).attr('href');
        // Check if the url matches so we can add the active class
        if (location == href) {
            menu_href_selector = 'a[href="' + href + '"]';
            // Do not add on the top quick links
            side_bar.find(menu_href_selector).parents('li').not('.quick-links').addClass('active');
            // Set aria expanded to true
            side_bar.find(menu_href_selector).prop('aria-expanded', true);
            side_bar.find(menu_href_selector).parents('ul.nav-second-level').prop('aria-expanded', true);
            side_bar.find(menu_href_selector).parents('li').find('a:first-child').prop('aria-expanded', true);
        }
    });

    // For inline tinymce editors when content is blank a message is shown, on click this message should be hidden.
    $('body').on('click', '.editor-add-content-notice', function() {
        $(this).remove();
        tinymce.triggerSave();
    });

    // Check for customizer active class
    if (setup_menu.hasClass('display-block')) {
        var setup_menu_links = setup_menu.find('li > a');
        $.each(setup_menu_links, function(i, data) {
            var href = $(data).attr('href');
            if (location == href) {
                menu_href_selector = 'a[href="' + href + '"]';
                setup_menu.find(menu_href_selector).parents('li').addClass('active');
                setup_menu.find(menu_href_selector).prev('active');
                setup_menu.find(menu_href_selector).parents('ul.nav-second-level').prop('aria-expanded', true);
                setup_menu.find(menu_href_selector).parents('li').find('a:first-child').prop('aria-expanded', true);
            }
        });
    }
    // Init now metisMenu for the main admin sidebar
    side_bar.metisMenu();
    // Init setup menu
    setup_menu.metisMenu();
    // Handle minimalize sidebar menu
    $('.hide-menu').click(function(event) {
        event.preventDefault();
        if ($(window).width() < 769) {
            $("body").toggleClass("show-sidebar");
        } else {
            if ($("#setup-menu-wrapper").hasClass('display-block')) {
                $('.close-customizer').click();
            }
            $("body").toggleClass("hide-sidebar");
        }
    });
    // Check if is mobile to clone the searchbar to the body so can be visible on mobile
    if (is_mobile()) {
        content_wrapper.on('click', function() {
            if ($('body').hasClass('show-sidebar')) {
                $('body').removeClass('show-sidebar');
            }
            if (setup_menu.hasClass('display-block')) {
                $('.close-customizer').click();
            }
        });
    }

    // Top search input fetch results
    $('#search_input').on('keyup paste', function() {
        var q = $(this).val().trim();
        var search_results = $('#search_results');
        var top_search_button = $('#top_search_button button');

        if (q.length < 2) {
            return;
        }
        if (q == '') {
            content_wrapper.unhighlight();
            search_results.html('');
            original_top_search_val = '';
            top_search_button.html('<i class="fa fa-search"></i>');
            top_search_button.removeClass('search_remove');
            return;
        }
        top_search_button.html('<i class="fa fa-remove"></i>');
        top_search_button.addClass('search_remove');
        delay(function() {
            if (q == original_top_search_val) {
                return;
            }
            $.post(admin_url + 'misc/search', {
                q: q
            }).done(function(results) {
                content_wrapper.unhighlight();
                search_results.html(results);
                content_wrapper.highlight(q);
                original_top_search_val = q;
            });
        }, 700);
    });
    // Global on change for mass delete to hide all other elements for bulk actions
    $('.bulk_actions').on('change', 'input[name="mass_delete"]', function() {
        if ($(this).prop('checked') == true) {
            $('#bulk_change').find('select').selectpicker('val', '');
        }
        $('#bulk_change').toggleClass('hide');
        $('.mass_delete_separator').toggleClass('hide');
    });
    // Clear todo modal values when modal is hidden
    $('body').on('hidden.bs.modal', '#__todo', function() {
        $('#__todo').find('input[name="todoid"]').val('');
        $('#__todo').find('textarea[name="description"]').val('');
        $('#__todo .add-title').addClass('hide');
        $('#__todo .edit-title').addClass('hide');
    });

    // Focus staff todo description
    $('body').on('shown.bs.modal', '#__todo', function() {
        $('#__todo').find('textarea[name="description"]').focus();
        if ($('#__todo').find('input[name="todoid"]').val() != '') {
            $('#__todo .add-title').addClass('hide');
            $('#__todo .edit-title').removeClass('hide');
        } else {
            $('#__todo .add-title').removeClass('hide');
            $('#__todo .edit-title').addClass('hide');
        }
    });

    // Focus search input on click
    $('#top_search_button button').on('click', function() {
        $('#search_input').focus();
        if ($(this).hasClass('search_remove')) {
            $(this).html('<i class="fa fa-search"></i>');
            $(this).removeClass('search_remove');
            original_top_search_val = '';
            $('#search_results').html('');
            $('#search_input').val('');
        }
    });

    // Fix for dropdown search to close if user click anyhere on html except on dropdown
    $("body").click(function(e) {
        if (!$(e.target).parents('#top_search_dropdown').hasClass('search-results')) {
            $('#top_search_dropdown').remove();
        }
    });

    // Init tooltips
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    // Init popovers
    $('body').popover({
        selector: '[data-toggle="popover"]',
    });

    // Close all popovers if user click on body and the click is not inside the popover content area
    $('body').on('click', function(e) {
        $('[data-toggle="popover"],.manual-popover').each(function() {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    // Do not close the dropdownmenu for filter when filtering
    $('body').on('click', '._filter_data ul.dropdown-menu li a', function(e) {
        e.stopPropagation();
        e.preventDefault();
    });

    // Remove tooltip fix on body click (in case user clicked link and tooltip stays open)
    $('body').on('click', function() {
        $('.tooltip').remove();
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
    // Fix for all modals scroll..
    $('body').on('shown.bs.modal', '.modal', function() {
        $('body').addClass('modal-open');
        // Close the top timers dropdown in case user click on some task
        $('body').find('#started-timers-top').parents('li').removeClass('open');
    });

    $('body').on('hidden.bs.modal', '.modal', function(event) {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
        $(this).data('bs.modal', null);
    });

    // Activity log tables filter by date / Currently used for system activity log and ticket pipe log
    if (activity_log_table.length > 0) {
        $('.datepicker.activity-log-date').on('change', function() {
            activity_log_table.DataTable().column($(this).attr('data-column')).search($(this).val()).draw();
        });
    }
    // For expenses and recurring tasks
    $('body').on('change', '[name="repeat_every"]', function() {
        var val = $(this).val();
        if (val == 'custom') {
            $('.recurring_custom').removeClass('hide');
        } else {
            $('.recurring_custom').addClass('hide');
        }
        if (val != '') {
            $('body').find('#recurring_ends_on').removeClass('hide');
        } else {
            $('body').find('#recurring_ends_on').addClass('hide');
            $('body').find('#recurring_ends_on input').val('');
        }
    });

    $('select[name="range"]').on('change', function() {
        if ($(this).val() == 'period') {
            $('.period').removeClass('hide');
        } else {
            $('.period').addClass('hide');
            $('.period').find('input').val('');
        }
    });

    // On mass_select all select all the availble rows in the tables.
    $('body').on('change', '#mass_select_all', function() {
        var to, rows, checked;
        to = $(this).data('to-table');

        rows = $('.table-' + to).find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input').prop('checked', checked);
        });
    });
    // Init the editor for email templates where changing data is allowed
    $('body').on('show.bs.modal', '.modal.email-template', function() {
        init_editor($(this).data('editor-id'), {
            urlconverter_callback: 'merge_field_format_url'
        });
    });
    // Remove the editor inited for the email sending templates where changing the email template data is allowed
    $('body').on('hidden.bs.modal', '.modal.email-template', function() {
        tinymce.remove($(this).data('editor-id'));
    });

    // Customizer close and remove open from session
    $('.close-customizer').on('click', function(e) {
        e.preventDefault();

        setup_menu.addClass(isRTL == 'true' ? "fadeOutRight" : "fadeOutLeft");
        // Clear the session for setup menu so in reload wont be closed
        $.get(admin_url + 'misc/set_setup_menu_closed');
    });

    // Open customizer and add that is open to session
    $('.open-customizer').on('click', function(e) {
        e.preventDefault();

        if (setup_menu.hasClass(isRTL == 'true' ? "fadeOutRight" : "fadeOutLeft")) {
            setup_menu.removeClass(isRTL == 'true' ? "fadeOutRight" : "fadeOutLeft");
        }
        setup_menu.addClass(isRTL == 'true' ? "fadeInRight" : "fadeInLeft");
        setup_menu.addClass('display-block');
        // Set session that the setup menu is open in case of reload
        $.get(admin_url + 'misc/set_setup_menu_open');
        mainWrapperHeightFix();
    });
    // Change live the colors for colorpicker in kanban/pipeline
    $('body').on('click', '.cpicker', function() {
        var color = $(this).data('color');
        $(this).parents('.cpicker-wrapper').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
        if ($(this).hasClass('kanban-cpicker')) {
            $(this).parents('.panel-heading-bg').css('background', color);
            $(this).parents('.panel-heading-bg').css('border', '1px solid ' + color);
        } else if ($(this).hasClass('calendar-cpicker')) {
            $('body').find('._event input[name="color"]').val(color);
        }
    });

    // Notification profile link click
    $('body').on('click', '.notification_link', function() {
        var link = $(this).data('link');
        var not_href;
        not_href = link.split('#');
        if (!not_href[1]) {
            window.location.href = link;
        }
    });

    /* Check if postid in notification url to open the newsfeed */
    $('body').on('click', '.notifications a,.notification_link', function(e) {
        var not_href, not_href_id;
        if ($(this).hasClass('notification_link')) {
            not_href = $(this).data('link');
        } else {
            not_href = e.currentTarget.href;
        }
        not_href = not_href.split('#');
        if (not_href[1] && not_href[1].indexOf('=') > -1) {
            e.preventDefault();
            var not_href_id = not_href[1].split('=')[1];
            if (not_href[1].indexOf('postid') > -1) {
                postid = not_href_id;
                $('.open_newsfeed').click();
            } else if (not_href[1].indexOf('taskid') > -1) {
                init_task_modal(not_href_id);
            } else if (not_href[1].indexOf('leadid') > -1) {
                init_lead(not_href_id);
            }
        }
    });

    // Set notifications to read when notifictions dropdown is opened
    $('.notifications-icon,.notifications-wrapper').on('click', function() {
        clearInterval(autocheck_notifications_timer_id);
        $.post(admin_url + 'misc/set_notifications_read').done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $(".icon-notifications").addClass('hide');
                setTimeout(function() {
                    $('.notification-box.unread').removeClass('unread');
                }, 1000);
            }
        })
    });

    // Auto check for new notifications
    if (auto_check_for_new_notifications != 0) {
        var notifications_wrapper = $('#header li.notifications-wrapper');
        autocheck_notifications_timer_id = setInterval(function() {
            $.get(admin_url + 'misc/notifications_check', function(response) {
                notifications_wrapper.html(response);
            });
        }, auto_check_for_new_notifications * 1000); //time in milliseconds
    }

    // Tables
    init_table_tickets();
    init_table_announcements();
    init_table_staff_projects();

    // Ticket pipe log and system activity log
    initDataTable('.table-activity-log', window.location.href, 'undefined', 'undefined', 'undefined', [1, 'DESC']);
    if ($('.table-invoices').length > 0 || $('.table-estimates').length > 0) {
        // Invoices additional server params
        var Invoices_Estimates_ServerParams = {};
        var Invoices_Estimates_Filter = $('._hidden_inputs._filters input')
        $.each(Invoices_Estimates_Filter, function() {
            Invoices_Estimates_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        // Invoices tables
        _table_api = initDataTable('.table-invoices', admin_url + 'invoices/list_invoices', 'undefined', 'undefined', Invoices_Estimates_ServerParams, [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

        // Set year hidden for invoices
        if (_table_api) {
            _table_api.column(3).visible(false, false).columns.adjust();
        }

        // Estimates table
        _table_api = initDataTable('.table-estimates', admin_url + 'estimates/list_estimates', 'undefined', 'undefined', Invoices_Estimates_ServerParams, [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

        // Set year hidden for estimates
        if (_table_api) {
            _table_api.column(3).visible(false, false).columns.adjust();
        }
    }

    var TasksServerParams = {},
        Tasks_Filters;
    Tasks_Filters = $('._hidden_inputs._filters._tasks_filters input');
    $.each(Tasks_Filters, function() {
        TasksServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });

    // Tasks not sortable
    _tns = [($('.table-tasks').find('th').length - 1)];

    var tasks_sort = 2;
    var _tasks_table_url = admin_url + 'tasks';
    if ($('body').hasClass('tasks_page')) {
        _tns.push(0);
        tasks_sort = 3;
        _tasks_table_url += '?bulk_actions=true';
    }

    _table_api = initDataTable('.table-tasks', _tasks_table_url, _tns, _tns, TasksServerParams, [tasks_sort, 'ASC']);
    if (_table_api && $('body').hasClass('home')) {
        _table_api.column(4).visible(false, false).column(6).visible(false, false).column(5).visible(false, false).columns.adjust();
    }

    // Send file modal populate the hidden files when is shown
    $('#send_file').on('show.bs.modal', function(e) {
        $('#send_file').find('input[name="filetype"]').val($($(e.relatedTarget)).data('filetype'));
        $('#send_file').find('input[name="file_path"]').val($($(e.relatedTarget)).data('path'));
        $('#send_file').find('input[name="file_name"]').val($($(e.relatedTarget)).data('file-name'));
        if ($('input[name="email"]').length > 0) {
            $('#send_file').find('input[name="send_file_email"]').val($('input[name="email"]').val());
        }
    });

    // Set password checkbox change
    $('body').on('change', 'input[name="send_set_password_email"]', function() {
        $('body').find('.client_password_set_wrapper').toggleClass('hide');
    });

    // Todo status change checkbox click
    $('body').on('change', '.todo input[type="checkbox"]', function() {
        var finished = $(this).prop('checked') === true ? 1 : 0;
        window.location.href = admin_url + 'todo/change_todo_status/' + $(this).val() + '/' + finished;
    });

    var todos_sortable = $(".todos-sortable");
    if (todos_sortable.length > 0) {
        // Makes todos sortable
        todos_sortable = todos_sortable.sortable({
            connectWith: ".todo",
            items: "li",
            handle: '.dragger',
            appendTo: "body",
            update: function(event, ui) {
                if (this === ui.item.parent()[0]) {
                    update_todo_items();
                }
            }
        });
    }

    /* NEWSFEED JS */
    $('body').on('click', '.open_newsfeed,.close_newsfeed', function(e) {
        e.preventDefault();
        if (typeof($(this).data('close')) == 'undefined') {
            var url = admin_url + 'newsfeed/get_data';
            $.get(url, function(response) {
                $('#newsfeed').html(response);
                load_newsfeed(postid);
                init_newsfeed_form();
                init_selectpicker();
                include_lightbox();
                init_lightbox();
            });
        } else if ($(this).data('close') === true) {
            newsFeedDropzone.destroy();
            $('#newsfeed').html('');
            total_new_post_files = 0;
            newsfeed_posts_page = 0;
            track_load_post_likes = 0;
            track_load_comment_likes = 0;
            postid = undefined;
        }
        $('#newsfeed').toggleClass('hide');
        $('body').toggleClass('noscroll');
    });

    if ($('[data-newsfeed-auto]').length > 0) {
        $('.open_newsfeed').click();
    }

    // When adding comment if user press enter to submit comment too
    $("body").on('keyup', '.comment-input input', function(event) {
        if (event.keyCode == 13) {
            add_comment(this);
        }
    });

    // Showing post likes modal
    $('#modal_post_likes').on('show.bs.modal', function(e) {
        track_load_post_likes = 0;
        $('#modal_post_likes_wrapper').empty();
        $('.likes_modal .modal-footer').removeClass('hide');
        var invoker = $(e.relatedTarget);
        var postid = $(invoker).data('postid')
        post_likes_total_pages = $(invoker).data('total-pages');
        $(".load_more_post_likes").attr('data-postid', postid);
        load_post_likes(postid);
    });

    // Showing comment likes modal
    $('#modal_post_comment_likes').on('show.bs.modal', function(e) {
        $('#modal_comment_likes_wrapper').empty();
        track_load_comment_likes = 0;
        $('.likes_modal .modal-footer').removeClass('hide');
        var invoker = $(e.relatedTarget);
        var commentid = $(invoker).data('commentid');
        comment_likes_total_pages = $(invoker).data('total-pages');
        $(".load_more_post_comment_likes").attr('data-commentid', commentid);
        load_comment_likes(commentid);
    });

    // Load more post likes from modal
    $('.load_more_post_likes').on('click', function(e) {
        e.preventDefault();
        load_post_likes($(this).data('postid'));
    });

    // Load more comment likes from modal
    $('.load_more_post_comment_likes').on('click', function(e) {
        e.preventDefault();
        load_comment_likes($(this).data('commentid'));
    });

    // Add post attachment used for dropzone
    $('.add-attachments').on('click', function(e) {
        e.preventDefault();
        $('#post-attachments').toggleClass('hide');
    });

    // Set post visibility on change select by department
    $('body').on('change', '#post-visibility', function() {
        var value = $(this).val();
        if (value != null) {
            if (value.indexOf('all') > -1) {
                if (value.length > 1) {
                    value.splice(0, 1);
                    $(this).selectpicker('val', value);
                }
            }
        }
    });

    /* SALES PART JS */

    // Init invoices top stats
    init_invoices_total();
    // Init expenses total
    init_expenses_total();
    // Make items sortable
    init_estimates_total();
    // Make items sortable
    init_items_sortable();

    if ($('body').hasClass('estimates-pipeline')) {
        var estimate_id = $('input[name="estimateid"]').val();
        estimate_pipeline_open(estimate_id);
    }

    if ($('body').hasClass('proposals-pipeline')) {
        var proposal_id = $('input[name="proposalid"]').val();
        proposal_pipeline_open(proposal_id);
    }

    // Remove the disabled attribute from the currency input becuase the form dont read it
    $('body').on('submit', '._transaction_form', function() {
        // On submit re-calculate total and reorder the items for all cases
        calculate_total();
        reorder_items();
        $('select[name="currency"]').prop('disabled', false);
        $('select[name="project_id"]').prop('disabled', false);
        return true;
    });

    $('body').on('click', '.invoice-form-submit', function() {
        var form = $('.invoice-form');
        if (form.valid()) {
            if ($(this).hasClass('save-as-draft')) {
                form.find('.additional').html(hidden_input('save_as_draft', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    $('body').on('change', '[name="recurring"]', function() {
        var rec = $(this).val();
        if (rec != 0) {
            if (rec == 'custom') {
                $('.recurring_custom').removeClass('hide');
            } else {
                $('.recurring_custom').addClass('hide');
            }
            $('body').find('#recurring_ends_on').removeClass('hide');
        } else {
            $('body').find('#recurring_ends_on').addClass('hide');
            $('body').find('#recurring_ends_on input').val('');
            $('.recurring_custom').addClass('hide');
        }
    });

    // add estimate_note
    $('body').on('submit', '#estimate-notes', function() {
        var est_notes = $('#estimate-notes');
        if (est_notes.find('textarea[name="description"]').val() == '') {
            return;
        }
        var form = $(this);
        var data = $(form).serialize();
        $.post(form.attr('action'), data).done(function(estimate_id) {
            // Reload the notes
            get_estimate_notes(estimate_id);
            // Reset the note textarea value
            est_notes.find('textarea[name="description"]').val('');
        });
        return false;
    });
    // Show quantity as change we need to change on the table QTY heading for better user experience
    $('body').on('change', 'input[name="show_quantity_as"]', function() {
        $('body').find('th.qty').html($(this).data('text'));
    });

    $('body').on('change', 'div.estimate input[name="date"]', function() {
        do_prefix_year($(this).val());
    });

    $('body').on('change', 'div.invoice input[name="date"],div.estimate input[name="date"]', function() {

        var date = $(this).val();
        do_prefix_year(date);
        // This function not work on edit
        if ($('input[name="isedit"]').length > 0) {
            return;
        }
        var due_date_input_name = 'duedate';
        var due_calc_url = admin_url + 'invoices/get_due_date';
        if ($('body').find('div.estimate').length > 0) {
            due_calc_url = admin_url + 'estimates/get_due_date';
            due_date_input_name = 'expirydate';
        }
        if (date != '') {
            $.post(due_calc_url, {
                date: date
            }).done(function(formated) {
                $('input[name="' + due_date_input_name + '"]').val(formated);
            });
        } else {
            $('input[name="' + due_date_input_name + '"]').val('');
        }
    });

    $('#sales_attach_file').on('hidden.bs.modal', function(e) {
        $('#sales_uploaded_files_preview').empty();
        $('.dz-file-preview').empty();
    });

    if (typeof(Dropbox) != 'undefined') {
        if ($('#dropbox-chooser-sales').length > 0) {
            document.getElementById("dropbox-chooser-sales").appendChild(Dropbox.createChooseButton({
                success: function(files) {
                    var _data = {};
                    _data.rel_id = $('body').find('input[name="_attachment_sale_id"]').val();
                    _data.type = $('body').find('input[name="_attachment_sale_type"]').val();
                    _data.files = files;
                    _data.external = 'dropbox';
                    $.post(admin_url + 'misc/add_sales_external_attachment', _data).done(function() {
                        if (_data.type == 'invoice') {
                            init_invoice(_data.rel_id);
                        } else if (_data.type == 'estimate') {
                            if ($('body').hasClass('estimates-pipeline')) {
                                estimate_pipeline_open(_data.rel_id);
                            } else {
                                init_estimate(_data.rel_id);
                            }
                        } else if (_data.type == 'proposal') {
                            if ($('body').hasClass('proposals-pipeline')) {
                                proposal_pipeline_open(_data.rel_id);
                            } else {
                                init_proposal(_data.rel_id);
                            }
                        }
                        $('#sales_attach_file').modal('hide');
                    });
                },
                linkType: "preview",
                extensions: allowed_files.split(','),
            }));
        }
    }

    if ($('#sales-upload').length > 0) {
        new Dropzone('#sales-upload', {
            createImageThumbnails: false,
            dictFileTooBig: file_exceds_maxfile_size_in_form,
            dictDefaultMessage: drop_files_here_to_upload,
            dictFallbackMessage: browser_not_support_drag_and_drop,
            dictRemoveFile: remove_file,
            sending: function(file, xhr, formData) {
                formData.append("rel_id", $('body').find('input[name="_attachment_sale_id"]').val());
                formData.append("type", $('body').find('input[name="_attachment_sale_type"]').val());
            },
            complete: function(file) {
                this.removeFile(file);
            },
            success: function(files, response) {
                response = JSON.parse(response);
                var type = $('body').find('input[name="_attachment_sale_type"]').val()
                var dl_url, delete_function;
                dl_url = 'download/file/sales_attachment/';
                delete_function = 'delete_' + type + '_attachment';
                if (type == 'invoice') {
                    init_invoice(response.rel_id);
                } else if (type == 'estimate') {
                    if ($('body').hasClass('estimates-pipeline')) {
                        estimate_pipeline_open(response.rel_id);
                    } else {
                        init_estimate(response.rel_id);
                    }
                } else if (type == 'proposal') {
                    if ($('body').hasClass('proposals-pipeline')) {
                        proposal_pipeline_open(response.rel_id);
                    } else {
                        init_proposal(response.rel_id);
                    }
                }

                var data = '';
                if (response.success == true) {

                    data += '<div class="display-block" data-attachment-id="' + response.attachment_id + '">';
                    data += '<div class="col-md-10">';
                    data += '<div class="pull-left"><i class="attachment-icon-preview fa fa-file-o"></i></div>'
                    data += '<a href="' + site_url + dl_url + response.key + '" target="_blank">' + response.file_name + '</a>';
                    data += '<p class="text-muted">' + response.filetype + '</p>';
                    data += '</div>';
                    data += '<div class="col-md-2 text-right">';
                    data += '<a href="#" class="text-danger" onclick="' + delete_function + '(' + response.attachment_id + '); return false;"><i class="fa fa-times"></i></a>';
                    data += '</div>';
                    data += '<div class="clearfix"></div><hr/>';
                    data += '</div>';
                    $('#sales_uploaded_files_preview').append(data);
                }
            },
            maxFilesize: max_php_ini_upload_size.replace(/\D/g, ''),
            acceptedFiles: allowed_files,
            error: function(file, response) {
                alert_float('danger', response);
            },

        });
    }

    // remove the preview in the modal after hide
    $('#invoice_attach').on('hidden.bs.modal', function(e) {
        $('.dz-preview').remove();
        $('.invoice-attach-dropzone-preview').remove();
    });

    // Items modal show action
    $('body').on('show.bs.modal', '#sales_item_modal', function(event) {
        // Set validation for invoice item form
        _validate_form($('#invoice_item_form'), {
            description: 'required',
            rate: {
                required: true,
            }
        }, manage_invoice_items);
        $('.affect-warning').addClass('hide');
        var button = $(event.relatedTarget)
        var id = button.data('id');
        $('#sales_item_modal input').val('');
        $('#sales_item_modal textarea').val('');
        $('#sales_item_modal #group_id').selectpicker('val', '');
        $('select[name="tax"]').selectpicker('deselectAll');
        $('#sales_item_modal .add-title').removeClass('hide');
        $('#sales_item_modal .edit-title').addClass('hide');

        // If id found get the text from the datatable
        if (typeof(id) !== 'undefined') {
            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);
            $('#sales_item_modal .add-title').addClass('hide');
            $('#sales_item_modal .edit-title').removeClass('hide');
            $('#sales_item_modal input[name="description"]').val($(button).parents('tr').find('td').eq(0).text());
            $('#sales_item_modal input[name="rate"]').val($(button).parents('tr').find('td').eq(2).text());
            $('#sales_item_modal input[name="unit"]').val($(button).parents('tr').find('td').eq(4).text());
            $('#sales_item_modal textarea').val($(button).parents('tr').find('td').eq(1).text());
            $('#sales_item_modal #group_id').selectpicker('val', button.attr('data-group-id'));
            $('select[name="tax"]').selectpicker('val', $(button).parents('tr').find('td').eq(3).find('span').data('taxid'));
        }
    });

    $('body').on('hidden.bs.modal', '#sales_item_modal', function(event) {
        $('#item_select').selectpicker('val', '');
    });

    // Show send to email invoice modal
    $('body').on('click', '.invoice-send-to-client', function(e) {
        e.preventDefault();
        $('#invoice_send_to_client_modal').modal('show');
    });
    // Show send to email estimate modal
    $('body').on('click', '.estimate-send-to-client', function(e) {
        e.preventDefault();
        $('#estimate_send_to_client_modal').modal('show');
    });
    // Send templaate modal custom close function causing problems if is on pipeline view
    $('body').on('click', '.close-send-template-modal', function() {
        $('#estimate_send_to_client_modal').modal('hide');
        $('#proposal_send_to_customer').modal('hide');
    });
    // Include shipping show/hide details
    $('body').on('change', '#include_shipping', function() {
        if ($(this).prop('checked') == true) {
            $('#shipping_details').removeClass('hide');
        } else {
            $('#shipping_details').addClass('hide');
        }
    });
    // Init the billing and shipping details in the field - estimates and invoices
    $('body').on('click', '.save-shipping-billing', function(e) {
        init_billing_and_shipping_details();
    });

    // On change currency recalculate price and change symbol
    $('body').on('change', 'select[name="currency"]', function() {
        init_currency_symbol();
    });
    // Dont allow NO TAX and other taxes to be selected together
    $('body').on('change', 'select.tax', function() {
        var value = $(this).val();
        if (value != null) {
            if (value.indexOf('') > -1) {
                if (value.length > 1) {
                    value.splice(0, 1);
                    $(this).selectpicker('val', value);
                }
            }
        }
    });
    // Recaulciate total on these changes
    $('body').on('change', 'input[name="adjustment"],select.tax', function() {
        calculate_total();
    });
    // Discount type for estimate/invoice
    $('body').on('change', 'select[name="discount_type"]', function() {
        // if discount_type == ''
        if ($(this).val() == '') {
            $('input[name="discount_percent"]').val(0);
        }
        // Recalculate the total
        calculate_total();
    });
    // In case user enter discount percent but there is no discount type set
    $('body').on('change', 'input[name="discount_percent"]', function() {
        if ($('select[name="discount_type"]').val() == '' && $(this).val() != 0) {
            alert('Select discount type');
            $('html,body').animate({
                    scrollTop: 0
                },
                'slow');
            $('#wrapper').highlight($('label[for="discount_type"]').text());
            setTimeout(function() {
                $('#wrapper').unhighlight();
            }, 3000);
            return false;
        }
        if ($(this).valid() == true) {
            calculate_total();
        }
    });
    // Add item to preview from the dropdown for invoices estimates
    $('body').on('change', 'select[name="item_select"]', function() {

        var itemid = $(this).selectpicker('val');
        if (itemid != '' && itemid !== 'newitem') {
            add_item_to_preview(itemid);
        } else if (itemid == 'newitem') {
            // New item
            $('#sales_item_modal').modal('show');
        }
    });
    // Add task data to preview from the dropdown for invoiecs
    $('body').on('change', 'select[name="task_select"]', function() {
        var task_bill_id = $(this).selectpicker('val');
        if (task_bill_id != '') {
            add_task_to_preview_as_item(task_bill_id);
        }
    });

    // When user record payment check if is online mode
    $('body').on('change', 'select[name="paymentmode"]', function() {
        !$.isNumeric($(this).val()) ? $('.do_not_redirect').removeClass('hide') : $('.do_not_redirect').addClass('hide');
    });

    $('body').on('change', '.f_client_id select[name="clientid"]', function() {
        var val = $(this).val();
        var s_project = $('select[name="project_id"]');
        s_project.empty();
        clear_billing_and_shipping_details();
        if (val == '') {
            $('#merge').empty();
            $('#expenses_to_bill').empty();
            s_project.selectpicker('refresh');
            $('.projects-wrapper').addClass('hide');
            return false;
        }
        var current_invoice = $('body').find('input[name="merge_current_invoice"]').val();
        $.get(admin_url + 'invoices/client_change_data/' + val + '/' + current_invoice, function(response) {
            $('#merge').html(response.merge_info);
            $('#expenses_to_bill').html(response.expenses_bill_info);

            if (response.merge_info != '' || response.expenses_bill_info != '') {
                $('#invoice_top_info').removeClass('hide');
            } else {
                $('#invoice_top_info').addClass('hide');
            }

            for (var f in bs_fields) {
                if (bs_fields[f].indexOf('billing') > -1) {
                    if (bs_fields[f].indexOf('country') > -1) {
                        $('select[name="' + bs_fields[f] + '"]').selectpicker('val', response['billing_shipping'][0][bs_fields[f]]);
                    } else {
                        $('input[name="' + bs_fields[f] + '"]').val(response['billing_shipping'][0][bs_fields[f]]);
                    }
                }
            }

            if (!empty(response['billing_shipping'][0]['shipping_street'])) {
                $('input[name="include_shipping"]').prop("checked", true);
                $('input[name="include_shipping"]').change();
            }

            for (var f in bs_fields) {
                if (bs_fields[f].indexOf('shipping') > -1) {
                    if (bs_fields[f].indexOf('country') > -1) {
                        $('select[name="' + bs_fields[f] + '"]').selectpicker('val', response['billing_shipping'][0][bs_fields[f]]);
                    } else {
                        $('input[name="' + bs_fields[f] + '"]').val(response['billing_shipping'][0][bs_fields[f]]);
                    }
                }
            }

            init_billing_and_shipping_details();

            var client_currency = response['client_currency'];
            var s_currency = $('body').find('.accounting-template select[name="currency"]');
            client_currency = parseInt(client_currency);

            if (client_currency != 0) {
                s_currency.val(client_currency);
            } else {
                s_currency.val(s_currency.data('base'));
            }

            var billable_tasks_area = $('#task_select');
            if (billable_tasks_area.length > 0) {
                var option_data;
                billable_tasks_area.find('option').filter(function() {
                    return this.value || $.trim(this.value).length > 0 || $.trim(this.text).length > 0;
                }).remove();

                $.each(response['billable_tasks'], function(i, obj) {
                    option_data = ' ';
                    if (obj.started_timers == true) {
                        option_data += 'disabled class="text-danger important" data-subtext="' + invoice_task_billable_timers_found + '"';
                    } else {
                        option_data += 'data-subtext="' + obj.rel_name + '"';
                    }
                    billable_tasks_area.append('<option value="' + obj.id + '"' + option_data + '>' + obj.name + '</option>');
                });
                billable_tasks_area.selectpicker('refresh');
            }
            if (response.projects.length > 0) {
                $('.projects-wrapper').removeClass('hide');
                s_project.append('<option value=""></option>');
                $.each(response.projects, function(i, obj) {
                    s_project.append('<option value="' + obj.id + '">' + obj.name + '</option>');
                });
            } else {
                $('.projects-wrapper').addClass('hide');
            }
            s_project.selectpicker('refresh');
            s_currency.selectpicker('refresh');
            init_currency_symbol();
        }, 'json');
    });

    // When customer_id is passed init the data
    if ($('input[name="isedit"]').length == 0) {
        $('.f_client_id select[name="clientid"]').change();
    }

    $('body').on('click', '#get_shipping_from_customer_profile', function(e) {
        e.preventDefault();
        var include_shipping = $('#include_shipping');
        if (include_shipping.prop('checked') == false) {
            include_shipping.prop('checked', true);
            $('#shipping_details').removeClass('hide');
        }
        var clientid = $('select[name="clientid"]').val();
        if (clientid == '') {
            return;
        }
        $.get(admin_url + 'clients/get_customer_billing_and_shipping_details/' + clientid, function(response) {
            $('input[name="shipping_street"]').val(response[0]['shipping_street']);
            $('input[name="shipping_city"]').val(response[0]['shipping_city']);
            $('input[name="shipping_state"]').val(response[0]['shipping_state']);
            $('input[name="shipping_zip"]').val(response[0]['shipping_zip']);
            $('select[name="shipping_country"]').selectpicker('val', response[0]['shipping_country']);
        }, 'json');
    });
    if (typeof(accounting) != 'undefined') {
        // Used for formatting money
        accounting.settings.currency.decimal = decimal_separator;
        accounting.settings.currency.thousand = thousand_separator;
        accounting.settings.currency.precision = decimal_places;

        // Used for numbers
        accounting.settings.number.thousand = thousand_separator;
        accounting.settings.number.decimal = decimal_separator;
        accounting.settings.number.precision = decimal_places;

        calculate_total();
    }

    // Invoices to merge
    $('body').on('change', 'input[name="invoices_to_merge[]"]', function() {
        var checked = $(this).prop('checked');
        var _id = $(this).val();
        if (checked == true) {
            $.get(admin_url + 'invoices/get_merge_data/' + _id, function(response) {
                $.each(response.items, function(i, obj) {
                    if (obj.rel_type != '') {
                        if (obj.rel_type == 'task') {
                            $('input[name="task_id"]').val(obj.item_related_formated_for_input);
                        } else if (obj.rel_type == 'expense') {
                            $('input[name="expense_id"]').val(obj.item_related_formated_for_input);
                        }
                    }
                    add_item_to_table(obj, 'undefined', _id);
                });
            }, 'json');
        } else {
            // Remove the appended invoice to merge
            $('body').find('[data-merge-invoice="' + _id + '"]').remove();
        }
    });
    // Bill expenses to invooice on top
    $('body').on('change', 'input[name="bill_expenses[]"]', function() {
        var checked = $(this).prop('checked');
        var _id = $(this).val();
        if (checked == true) {
            $.get(admin_url + 'invoices/get_bill_expense_data/' + _id, function(response) {
                $('input[name="expense_id"]').val(_id);
                add_item_to_table(response, 'undefined', 'undefined', _id);
            }, 'json');
        } else {
            // Remove the appended expenses
            $('body').find('[data-bill-expense="' + _id + '"]').remove();
            $('body').find('#billed-expenses input[value="' + _id + '"]').remove();
        }
    });

    $('body').on('change', '.invoice_inc_expense_additional_info input', function() {
        var _data_content = $(this).attr('data-content'),
            new_desc_value,
            desc_selector = $('[data-bill-expense=' + $(this).attr('data-id') + '] .item_long_description');
        current_desc_val = desc_selector.val();
        current_desc_val = current_desc_val.trim();
        if (_data_content != '') {
            if ($(this).prop('checked') == true) {
                new_desc_value = current_desc_val + "\n" + _data_content;
                desc_selector.val(new_desc_value.trim());
            } else {
                desc_selector.val(current_desc_val.replace("\n" + _data_content, ''));
                // IN case there is no new line
                desc_selector.val(current_desc_val.replace(_data_content, ''));
            }
        }
    });

});

function include_chart_js() {
    if (typeof(Chart) == 'undefined') {
        $.getScript(site_url + 'assets/plugins/Chart.js/Chart.min.js');
    }
}

function include_lightbox() {
    if (typeof(lightbox) == 'undefined') {
        $('head').append('<link id="lightbox-css" href="' + site_url + 'assets/plugins/lightbox/css/lightbox.min.css" rel="stylesheet" />');
        $.getScript(site_url + 'assets/plugins/lightbox/js/lightbox.min.js');
    }
}
// Lightbox plugins for images
function init_lightbox(options) {
    if (typeof(lightbox) != 'undefined') {
        var _lightBoxOptions = {
            'showImageNumberLabel': false,
            resizeDuration: 200,
        };
        if (typeof(options) != 'undefined') {
            jQuery.extend(_lightBoxOptions, options);
        }
        lightbox.option(_lightBoxOptions);
    }
}
// Progress bar animation load
function init_progress_bars() {
    var progress_bars = $('.progress .progress-bar');
    progress_bars.each(function() {
        var bar = $(this);
        var perc = bar.attr("data-percent");
        bar.css('width', (perc) + '%');
        if (!bar.hasClass('no-percent-text')) {
            bar.text((perc) + '%');
        }
    });
}
// Get url params like $_GET
function get_url_param(param) {
    var vars = {};
    window.location.href.replace(location.hash, '').replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function(m, key, value) { // callback
            vars[key] = value !== undefined ? value : '';
        }
    );
    if (param) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}
// Fix for height on the wrapper
function mainWrapperHeightFix() {
    // Get and set current height
    var headerH = 63;
    var navigationH = side_bar.height();
    var contentH = $(".content").height();
    var h = $(document).outerHeight(true) - headerH + 'px';
    setup_menu.css('min-height', h);
    content_wrapper.css('min-height', $(document).outerHeight(true) - headerH + 'px');
    // Set new height when contnet height is less then navigation
    if (contentH < navigationH) {
        content_wrapper.css("min-height", navigationH + 'px');
    }
    // Set new height when contnet height is less then navigation and navigation is less then window
    if (contentH < navigationH && navigationH < $(window).height()) {
        content_wrapper.css("min-height", $(window).height() - headerH + 'px');
    }
    // Set new height when contnet is higher then navigation but less then window
    if (contentH > navigationH && contentH < $(window).height()) {
        content_wrapper.css("min-height", $(window).height() - headerH + 'px');
    }
}

function setBodySmall() {
    if ($(this).width() < 769) {
        $('body').addClass('page-small');
    } else {
        $('body').removeClass('page-small');
        $('body').removeClass('show-sidebar');
    }
}
// Generate random password
function generatePassword(field) {
    var length = 12,
        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    $(field).parents().find('input.password').val(retVal)
}
// Switch field make request
function switch_field(field) {
    var status, url, id;
    status = 0;
    if ($(field).prop('checked') == true) {
        status = 1;
    }
    url = $(field).data('switch-url');
    id = $(field).data('id');
    $.get(url + '/' + id + '/' + status);
}
// General validate form function
function _validate_form(form, form_rules, submithandler) {

    var f = $(form).validate({
        rules: form_rules,
        messages: {
            email: {
                remote: email_exists,
            },
        },
        ignore: [],
        submitHandler: function(form) {
            if (typeof(submithandler) !== 'undefined') {
                submithandler(form);
            } else {
                return true;
            }
        }
    });

    var custom_required_fields = $(form).find('[data-custom-field-required]');

    if (custom_required_fields.length > 0) {
        $.each(custom_required_fields, function() {
            $(this).rules("add", {
                required: true
            });
            var name = $(this).attr('name');
            var label = $(this).parents('.form-group').find('[for="' + name + '"]');
            if (label.length > 0) {
                if (label.find('.req').length == 0) {
                    label.prepend(' <small class="req text-danger">* </small>');
                }
            }
        });
    }

    $.each(form_rules, function(name, rule) {
        if ((rule == 'required' && !jQuery.isPlainObject(rule)) || (jQuery.isPlainObject(rule) && rule.hasOwnProperty('required'))) {
            var label = $(form).find('[for="' + name + '"]');
            if (label.length > 0) {
                if (label.find('.req').length == 0) {
                    label.prepend(' <small class="req text-danger">* </small>');
                }
            }
        }
    });

    return false;
}
// Delete option from database AJAX
function delete_option(child, id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'settings/delete_option/' + id, function(response) {
            if (response.success == true) {
                $(child).parents('.option').remove();
            }
        }, 'json');
    }
}
// Slide toggle any selector passed
function slideToggle(selector, callback) {
    if ($(selector).hasClass('hide')) {
        $(selector).removeClass('hide', 'slow');
    }
    $(selector).slideToggle();
    // Set all progress bar to 0 percent
    var progress_bars = $('.progress-bar').not('.not-dynamic');
    if (progress_bars.length > 0) {
        progress_bars.each(function() {
            $(this).css('width', 0 + '%');
            $(this).text(0 + '%');
        });
        // Init the progress bars again
        init_progress_bars();
    }
    // Possible callback after slide toggle
    if (typeof(callback) == 'function') {
        callback();
    }
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
        $('#' + aId).hide('fast', function() {
            $('#' + aId).remove();
        });
    }, 4500);
}

function init_rel_tasks_table(rel_id, rel_type, selector) {
    if (typeof(selector) == 'undefined') {
        selector = '.table-rel-tasks';
    }
    if ($('body').find(selector).length == 0) {
        return;
    }
    var TasksServerParams = {},
        not_sortable_tasks, Tasks_Filters;
    Tasks_Filters = $('._hidden_inputs._filters._tasks_filters input');
    $.each(Tasks_Filters, function() {
        TasksServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    not_sortable_tasks = ($('body').find(selector).find('th').length - 1);
    initDataTable(selector, admin_url + 'tasks/init_relation_tasks/' + rel_id + '/' + rel_type, [not_sortable_tasks], [not_sortable_tasks], TasksServerParams, [2, 'ASC']);
}

function get_dt_export_buttons(table) {

    var table_buttons_options = [{
        extend: 'collection',
        text: dt_button_export,
        className: 'btn btn-default-dt-options',
        buttons: [{
            extend: 'excelHtml5',
            text: dt_button_excel,
            footer: true,
            exportOptions: {
                columns: [':visible:not(.not-export)'],
            }
        }, {
            extend: 'csvHtml5',
            text: dt_button_csv,
            footer: true,
            exportOptions: {
                columns: [':visible:not(.not-export)']
            }
        }, {
            extend: 'pdfHtml5',
            text: dt_button_pdf,
            footer: true,
            orientation: 'landscape',
            customize: function(doc) {
                // Fix for column widths
                var table_api = $(table).DataTable();
                var columns = table_api.columns().visible();
                var columns_total = columns.length;
                var pdf_widths = [];
                var total_visible_columns = 0;
                for (i = 0; i < columns_total; i++) {
                    // Is only visible column
                    if (columns[i] == true) {
                        total_visible_columns++;
                    }
                }
                setTimeout(function() {
                    if (total_visible_columns <= 5) {
                        for (i = 0; i < total_visible_columns; i++) {
                            pdf_widths.push((735 / total_visible_columns));
                        }
                        doc.content[1].table.widths = pdf_widths;
                    }
                }, 10);
                doc.styles.tableHeader.alignment = 'left';
                doc.styles.tableHeader.margin = [5, 5, 5, 5];
                doc.pageMargins = [12, 12, 12, 12];
            },
            exportOptions: {
                columns: [':visible:not(.not-export)'],
            }
        }, {
            extend: 'print',
            text: dt_button_print,
            footer: true,
            exportOptions: {
                columns: [':visible:not(.not-export)'],
            }
        }],
    }, {
        extend: 'colvis',
        postfixButtons: ['colvisRestore'],
        className: 'btn btn-default-dt-options dt-column-visibility',
        text: dt_button_column_visibility
    }, {
        text: dt_button_reload,
        className: 'btn btn-default-dt-options',
        action: function(e, dt, node, config) {
            dt.ajax.reload();
        }
    }];
    if (show_table_columns_visibility == 0) {
        delete table_buttons_options[1];
    }
    return table_buttons_options;
}

function initDataTableOffline(dt_table) {
    var selector = '.dt-table';

    if (typeof(dt_table) !== 'undefined') {
        selector = dt_table;
    }

    var order_col, order_type, options, _buttons;
    var _options = {
        "language": dt_lang,
        "processing": true,
        'paginate': true,
        "responsive": true,
        "autoWidth": false,
        "order": [0, 'DESC'],
        "initComplete": function(settings, json) {
            var t_export = $(selector);
            var th_last_child = t_export.find('thead th:last-child');
            var th_first_child = t_export.find('thead th:first-child');
            if (th_last_child.text().trim() == options_string_translate) {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
        },
        dom: "<'mbot25'B><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
    }

    var order_col = $($(this)).attr('data-order-col');
    var order_type = $($(this)).attr('data-order-type');
    var tables = $(selector);
    $.each(tables, function() {
        options = _options;
        order_col = $(this).attr('data-order-col');
        order_type = $(this).attr('data-order-type');
        if (order_col && order_type) {
            options.order = [
                [order_col, order_type]
            ]
        }
        _buttons = get_dt_export_buttons(this);
        // Remove the reload button here because its not ajax request
        delete _buttons[2];
        options.buttons = _buttons;
        $(this).dataTable(options);
    });
}
// General function for all datatables serverside
function initDataTable(table, url, notsearchable, notsortable, fnserverparams, defaultorder) {

    var _table_name = table;
    if ($(table).length == 0) {
        return;
    }
    if (fnserverparams == 'undefined' || typeof(fnserverparams) == 'undefined') {
        fnserverparams = []
    }

    // If not order is passed order by the first column
    if (typeof(defaultorder) == 'undefined') {
        defaultorder = [
            [0, 'ASC']
        ];
    } else {
        if (defaultorder.length == 1) {
            defaultorder = [defaultorder]
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    tables_pagination_limit = parseFloat(tables_pagination_limit);

    if ($.inArray(tables_pagination_limit, length_options) == -1) {
        length_options.push(tables_pagination_limit)
        length_options_names.push(tables_pagination_limit)
    }

    length_options.sort(function(a, b) {
        return a - b;
    });
    length_options_names.sort(function(a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(dt_length_menu_all);

    var table = $('body').find(table).dataTable({
        "language": dt_lang,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 700,
        "bDeferRender": true,
        "responsive": true,
        "autoWidth": false,
        dom: "<'mbot25'B><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
        "pageLength": tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title)
            $(nRow).attr('data-toggle', aData.Data_Toggle)
        },
        "initComplete": function(settings, json) {
            var _table = $(table);
            var th_last_child = _table.find('thead th:last-child');
            var th_first_child = _table.find('thead th:first-child');
            if (th_last_child.text().trim() == options_string_translate) {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
        },
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function(d) {
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
            }
        },
        buttons: get_dt_export_buttons(table),
    });

    var tableApi = table.DataTable();

    var hiddenHeadings = $(table).find('th.not_visible');
    $.each(hiddenHeadings, function() {
        tableApi.columns(this.cellIndex).visible(false, false);
    });
    // Fix for hidden tables colspan not correct if the table is empty
    if ($(_table_name).is(':hidden')) {
        $(_table_name).find('.dataTables_empty').attr('colspan', $(_table_name).find('thead th').length);
    }

    return tableApi;
}

function task_single_update_tags() {
    var taskTags = $("#taskTags");
    $.post(admin_url + 'tasks/update_tags', {
        tags: taskTags.tagit('assignedTags'),
        task_id: taskTags.attr('data-taskid')
    });
}

function task_attachments_toggle() {
    $('body').find('.task-attachments-more').toggleClass('hide');
    $('body').find('.task-attachments-less').toggleClass('hide');
}
// Update todo items when drop happen
function update_todo_items() {
    var unfinished_items = $('.unfinished-todos li:not(.no-todos)');
    var finished = $('.finished-todos li:not(.no-todos)');
    var i = 1;
    // Refresh orders
    $.each(unfinished_items, function() {
        $(this).find('input[name="todo_order"]').val(i);
        $(this).find('input[name="finished"]').val(0);
        i++;
    });
    if (unfinished_items.length == 0) {
        $('.nav-total-todos').addClass('hide');
        $('.unfinished-todos li.no-todos').removeClass('hide');
    } else if (unfinished_items.length > 0) {
        if (!$('.unfinished-todos li.no-todos').hasClass('hide')) {
            $('.unfinished-todos li.no-todos').addClass('hide');
        }
        $('.nav-total-todos').removeClass('hide');
        $('.nav-total-todos').html(unfinished_items.length);
    }
    x = 1;
    $.each(finished, function() {
        $(this).find('input[name="todo_order"]').val(x);
        $(this).find('input[name="finished"]').val(1);
        $(this).find('input[type="checkbox"]').prop('checked', true);
        i++;
        x++;
    });
    if (finished.length == 0) {
        $('.finished-todos li.no-todos').removeClass('hide');
    } else if (finished.length > 0) {
        if (!$('.finished-todos li.no-todos').hasClass('hide')) {
            $('.finished-todos li.no-todos').addClass('hide');
        }
    }
    var update = [];
    $.each(unfinished_items, function() {
        var todo_id = $(this).find('input[name="todo_id"]').val();
        var order = $(this).find('input[name="todo_order"]').val();
        var finished = $(this).find('input[name="finished"]').val();
        var description = $(this).find('.todo-description');
        if (description.hasClass('line-throught')) {
            description.removeClass('line-throught')
        }
        $(this).find('input[type="checkbox"]').prop('checked', false);
        update.push([todo_id, order, finished])
    });
    $.each(finished, function() {
        var todo_id = $(this).find('input[name="todo_id"]').val();
        var order = $(this).find('input[name="todo_order"]').val();
        var finished = $(this).find('input[name="finished"]').val();
        var description = $(this).find('.todo-description');
        if (!description.hasClass('line-throught')) {
            description.addClass('line-throught')
        }
        update.push([todo_id, order, finished])
    });
    data = {};
    data.data = update;
    $.post(admin_url + 'todo/update_todo_items_order', data);
}
// Delete single todo item
function delete_todo_item(list, id) {
    $.get(admin_url + 'todo/delete_todo_item/' + id, function(response) {
        if (response.success == true) {
            $(list).parents('li').remove();
            update_todo_items();
        }
    }, 'json');
}
// Edit todo item
function edit_todo_item(id) {
    $.get(admin_url + 'todo/get_by_id/' + id, function(response) {
        var todo_modal = $('#__todo');
        todo_modal.find('input[name="todoid"]').val(response.todoid);
        todo_modal.find('textarea[name="description"]').val(response.description);
        todo_modal.modal('show');
    }, 'json');
}
// Date picker init with selected timeformat from settings
function init_datepicker() {
    var datepickers = $('.datepicker');
    var datetimepickers = $('.datetimepicker');
    if (datetimepickers.length == 0 && datepickers.length == 0) {
        return;
    }
    var opt;
    // Datepicker without time
    $.each(datepickers, function() {
        var opt = {
            format: date_format,
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: calendar_first_day,
        };
        // Check in case the input have date-end-date or date-min-date
        var max_date = $(this).data('date-end-date');
        var min_date = $(this).data('date-min-date');
        if (max_date) {
            opt.maxDate = max_date;
        }
        if (min_date) {
            opt.minDate = min_date;
        }
        // Init the picker
        $(this).datetimepicker(opt);
    });
    var opt_time;
    // Datepicker with time
    $.each(datetimepickers, function() {
        opt_time = {
            format: date_format + ' H:i',
            lazyInit: true,
            scrollInput: false,
            dayOfWeekStart: calendar_first_day,
        };
        // Check in case the input have date-end-date or date-min-date
        var max_date = $(this).data('date-end-date');
        var min_date = $(this).data('date-min-date');
        if (max_date) {
            opt_time.maxDate = max_date;
        }
        if (min_date) {
            opt_time.minDate = min_date;
        }
        // Init the picker
        $(this).datetimepicker(opt_time);
    });
}

// All inputs used for tags
function init_tags_inputs() {
    var __tags_inputs = $('body').find('input.tagsinput');
    __tags_inputs.tagit({
        availableTags: availableTags,
        allowSpaces: true,
        animate: false,
        placeholderText:tag_string,
        showAutocompleteOnFocus:true,
        caseSensitive: false,
        afterTagAdded: function(event, ui) {
            var tagIndexAvailable = availableTags.indexOf($.trim($(ui.tag).find('.tagit-label').text()));
            if (tagIndexAvailable > -1) {
                var _tagId = availableTagsIds[tagIndexAvailable];
                $(ui.tag).addClass('tag-id-' + _tagId);
            }
        }
    });
}

// Init color pickers
function init_color_pickers() {
    $('body').find('.colorpicker-input').colorpicker({
        format: "hex"
    });
}

// Init bootstrap select picker
function init_selectpicker() {
    $('body').find('.selectpicker').selectpicker({
        showSubtext: true,
        // noneResultsText: no_results_text_search_dropdown + ' {0}',
    });
    $('.selectpicker.auto-toggle').not('.toggled').selectpicker('toggle').addClass('toggled');
}
// Datatables custom view will fill input with the value
function dt_custom_view(value, table, custom_input_name, clear_other_filters) {
    var name;
    if (typeof(custom_input_name) == 'undefined') {
        name = 'custom_view';
    } else {
        name = custom_input_name;
    }
    if (typeof(clear_other_filters) != 'undefined') {
        $('._filters input').val('');
        $('._filter_data li.active').removeClass('active');
    }
    var _original_val = value;
    var _cinput = do_filter_active(name);
    if (_cinput != name) {
        value = "";
    }
    $('input[name="' + name + '"]').val(value);
    $(table).DataTable().ajax.reload();
}

function do_filter_active(value, parent_selector) {
    if (value != '' && typeof(value) != 'undefined') {
        $('[data-cview="all"]').parents('li').removeClass('active');
        var selector = $('[data-cview="' + value + '"]');
        if (typeof(parent_selector) != 'undefined') {
            selector = $(parent_selector + ' [data-cview="' + value + '"]');
        }
        if (!selector.parents('li').not('.dropdown-submenu').hasClass('active')) {
            selector.parents('li').addClass('active');
        } else {
            selector.parents('li').not('.dropdown-submenu').removeClass('active');
            // Remove active class from the parent dropdown if nothing selected in the child dropdown
            var parents_sub = selector.parents('li.dropdown-submenu');
            if (parents_sub.length > 0) {
                if (parents_sub.find('li.active').length == 0) {
                    parents_sub.removeClass('active');
                }
            }
            value = "";
        }
        return value;
    } else {
        $('._filters input').val('');
        $('._filter_data li.active').removeClass('active');
        $('[data-cview="all"]').parents('li').addClass('active');
        return "";
    }
}
// Called when editing member profile
function init_roles_permissions(roleid, user_changed) {
    if (typeof(roleid) == 'undefined') {
        roleid = $('select[name="role"]').val();
    }
    var isedit = $('.member > input[name="isedit"]');
    // Check if user is edit view and user has changed the dropdown permission if not only return
    if (isedit.length > 0 && typeof(roleid) !== 'undefined' && typeof(user_changed) == 'undefined') {
        return;
    }
    // Last if the roleid is blank return
    if (roleid == '') {
        return;
    }
    // Get all permissions
    var permissions = $('table.roles').find('tr');
    $.get(admin_url + 'misc/get_role_permissions_ajax/' + roleid).done(function(response) {
        response = JSON.parse(response);
        var can_view_st, can_view_own_st;
        $.each(permissions, function() {
            var permissionid = $(this).data('id');
            var row = $(this);
            $.each(response, function(i, obj) {
                if (permissionid == obj.permissionid) {
                    can_view_st = (obj.can_view == 1 ? true : false);
                    can_view_own_st = (obj.can_view_own == 1 ? true : false)
                    row.find('[data-can-view]').prop('checked', can_view_st);
                    if (can_view_st == true) {
                        row.find('[data-can-view]').change();
                    }
                    row.find('[data-can-view-own]').prop('checked', can_view_own_st);
                    if (can_view_own_st == true) {
                        row.find('[data-can-view-own]').change();
                    }
                    row.find('[data-can-edit]').prop('checked', (obj.can_edit == 1 ? true : false));
                    row.find('[data-can-create]').prop('checked', (obj.can_create == 1 ? true : false));
                    row.find('[data-can-delete]').prop('checked', (obj.can_delete == 1 ? true : false));
                }
            });
        });
    });
}

// Generate hidden input field
function hidden_input(name, val) {
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}
// Show/hide full table
function toggle_small_view(table, main_data) {
    $('body').toggleClass('small-table');
    var tablewrap = $('#small-table');
    var _visible = false;
    if (tablewrap.hasClass('col-md-5')) {
        tablewrap.removeClass('col-md-5');
        tablewrap.addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-5');
        tablewrap.removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide')
}

function stripTags(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}
// Check if field is empty
function empty(data) {
    if (typeof(data) == 'number' || typeof(data) == 'boolean') {
        return false;
    }
    if (typeof(data) == 'undefined' || data === null) {
        return true;
    }
    if (typeof(data.length) != 'undefined') {
        return data.length == 0;
    }
    var count = 0;
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            count++;
        }
    }
    return count == 0;
}
// Is mobile checker javascript
function is_mobile() {
    return _is_mobile;
}
// Main logout function check if timers found to show the warning
function logout() {
    var started_timers = $('.started-timers-top').find('li.timer').length;
    if (started_timers > 0) {
        // Timers found return false and show the modal
        $('.timers-modal-logout').modal('show');
        return false;
    } else {
        // No timer logout free
        window.location.href = site_url + 'authentication/logout';
    }
}

// Generate color rgb
function color(r, g, b) {
    return 'rgb(' + r + ',' + g + ',' + b + ')';
}

// Url builder function with parameteres
function buildUrl(url, parameters) {
    var qs = "";
    for (var key in parameters) {
        var value = parameters[key];
        qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
    }
    if (qs.length > 0) {
        qs = qs.substring(0, qs.length - 1); //chop off last "&"
        url = url + "?" + qs;
    }
    return url;
}

// Function that convert decimal logged time to HH:MM format
function decimalToHM(decimal) {
    var hrs = parseInt(Number(decimal));
    var min = Math.round((Number(decimal) - hrs) * 60);
    return (hrs < 10 ? "0" + hrs : hrs) + ':' + (min < 10 ? "0" + min : min);
}

// Init the media elfinder for tinymce browser
function elFinderBrowser(field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
        file: admin_url + 'misc/tinymce_file_browser', // use an absolute path!
        title: media_files,
        width: 900,
        height: 450,
        resizable: 'yes'
    }, {
        setUrl: function(url) {
            win.document.getElementById(field_name).value = url;
        }
    });
    return false;
}
// Function to init the tinymce editor
function init_editor(selector, settings) {
    if (typeof(selector) == 'undefined') {
        selector = '.tinymce';
    }

    var _editor_selector_check = $(selector);
    if (_editor_selector_check.length == 0) {
        return;
    }

    $.each(_editor_selector_check, function() {
        if ($(this).hasClass('tinymce-manual')) {
            $(this).removeClass('tinymce');
        }
    });

    // Original settings
    var _settings = {
        selector: selector,
        height: 400,
        theme: 'modern',
        skin: 'perfex',
        language: tinymce_lang,
        relative_urls: false,
        inline_styles: true,
        verify_html: false,
        cleanup: false,
        valid_elements: '+*[*]',
        valid_children: "+body[style], +style[type]",
        apply_source_formatting: false,
        remove_script_host: false,
        removed_menuitems: 'newdocument',
        forced_root_block: false,
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
        setup: function(ed) {
            // Default fontsize is 14
            ed.on('init', function() {
                this.getDoc().body.style.fontSize = '14px';
            });

            ed.addMenuItem('codesample', {
                text: 'Insert/Edit code sample',
                context: 'insert',
                icon: 'codesample',
                onclick: function() {
                    tinyMCE.execCommand('codesample');
                }
            });

            ed.addButton('alignment', {
                type: 'listbox',
                text: tinymce.util.I18n.translate('Alignment'),
                icon: false,
                onselect: function(e) {
                    tinyMCE.execCommand(this.value());
                },
                values: [{
                        icon: 'alignleft',
                        value: 'JustifyLeft'
                    },
                    {
                        icon: 'alignright',
                        value: 'JustifyRight'
                    },
                    {
                        icon: 'aligncenter',
                        value: 'JustifyCenter'
                    },
                    {
                        icon: 'alignjustify',
                        value: 'JustifyFull'
                    },
                ],
                onPostRender: function() {
                    // Select the firts item by default
                    this.value('JustifyLeft');
                }
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
            'advlist autoresize autolink lists link image charmap print preview hr anchor codesample',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'media nonbreaking save table contextmenu directionality',
            'paste textcolor colorpicker textpattern'
        ],
        toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | link image | bullist numlist | alignment | rtl',
        file_browser_callback: elFinderBrowser,
    };
    // Add the rtl to the settings if is true
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    // Possible settings passed to be overwrited or added
    if (typeof(settings) != 'undefined') {
        for (var key in settings) {
            _settings[key] = settings[key];
        }
    }
    // Init the editor
    var editor = tinymce.init(_settings);
    return editor;
}
// Function used to add custom bootstrap menu for setup and main menu and to add fa on front like fa fa-question
function _formatMenuIconInput(e) {
    if (typeof(e) == 'undefined') {
        return;
    }
    var _input = $(e.target);
    if (!_input.val().match(/^fa /)) {
        _input.val(
            'fa ' + _input.val()
        );
    }
}
jQuery.extend({
    highlight: function(node, re, nodeName, className) {
        if (node.nodeType === 3) {
            var match = node.data.match(re);
            if (match) {
                var highlight = document.createElement(nodeName || 'span');
                highlight.className = className || 'highlight';
                var wordNode = node.splitText(match.index);
                wordNode.splitText(match[0].length);
                var wordClone = wordNode.cloneNode(true);
                highlight.appendChild(wordClone);
                wordNode.parentNode.replaceChild(highlight, wordNode);
                return 1; //skip added node in parent
            }
        } else if ((node.nodeType === 1 && node.childNodes) && // only element nodes that have children
            !/(script|style)/i.test(node.tagName) && // ignore script and style nodes
            !(node.tagName === nodeName.toUpperCase() && node.className === className)) { // skip if already highlighted
            for (var i = 0; i < node.childNodes.length; i++) {
                i += jQuery.highlight(node.childNodes[i], re, nodeName, className);
            }
        }
        return 0;
    }
});

jQuery.fn.highlight = function(words, options) {
    var settings = {
        className: 'highlight animated flash',
        element: 'span',
        caseSensitive: false,
        wordsOnly: false
    };
    jQuery.extend(settings, options);

    if (words.constructor === String) {
        words = [words];
    }
    words = jQuery.grep(words, function(word, i) {
        return word != '';
    });
    words = jQuery.map(words, function(word, i) {
        return word.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
    });
    if (words.length == 0) {
        return this;
    };
    var flag = settings.caseSensitive ? "" : "i";
    var pattern = "(" + words.join("|") + ")";
    if (settings.wordsOnly) {
        pattern = "\\b" + pattern + "\\b";
    }
    var re = new RegExp(pattern, flag);

    return this.each(function() {
        jQuery.highlight(this, re, settings.element, settings.className);
    });
};

// Mass delete function for, parameter is table name like table-name the name only should be passed
function mass_delete(to) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        var ids = [];
        var rows = $('.table-' + to).find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids.push(checkbox.val());
            }
        });
        var data = {};
        data.ids = ids;
        $.post(admin_url + to + '/mass_delete', data).done(function() {
            window.location.reload();
        });
    }
}
// Show password on hidden input field
function showPassword(name) {
    var target = $('input[name="' + name + '"]');
    if ($(target).attr('type') == 'password' && $(target).val() != '') {
        $(target)
            .queue(function() {
                $(target).attr('type', 'text').dequeue();
            });
    } else {
        $(target).queue(function() {
            $(target).attr('type', 'password').dequeue();
        });
    }
}
// This is used for mobile where tooltip on _buttons class wrapper is found
// Will show all buttons tooltips as regular button with text
function init_btn_with_tooltips() {
    if (is_mobile()) {
        var tooltips_href_btn = $('._buttons').find('.btn-with-tooltip');
        $.each(tooltips_href_btn, function() {
            var title = $(this).attr('title');
            if (typeof(title) == 'undefined') {
                title = $(this).attr('data-title');
            }
            if (typeof(title) != 'undefined') {
                $(this).append(' ' + title);
                $(this).removeClass('btn-with-tooltip');
            }
        });
        var tooltips_group = $('._buttons').find('.btn-with-tooltip-group');
        $.each(tooltips_group, function() {
            var title = $(this).attr('title');
            if (typeof(title) == 'undefined') {
                title = $(this).attr('data-title');
            }
            if (typeof(title) != 'undefined') {
                $(this).find('.btn').eq(0).append(' ' + title);
                $(this).removeClass('btn-with-tooltip-group');
            }
        });
    }
}
// Helper hash id for estimates,invoices,proposals,expenses
function do_hash_helper(hash) {
    if (typeof(history.pushState) != "undefined") {
        var url = window.location.href;
        var obj = {
            Url: url
        };
        history.pushState(obj, '', obj.Url);
        window.location.hash = hash;
    }
}
// Knowledge base groups
$(function() {
    // Validating the knowledge group form
    _validate_form($('#kb_group_form'), {
        name: 'required'
    }, manage_kb_groups);
    // On hidden modal reset the values
    $('#kb_group_modal').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#kb_group_modal input').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
        $('#kb_group_modal input[name="group_order"]').val($('table tbody tr').length + 1);
    });
});
// Form handler function for knowledgebase group
function manage_kb_groups(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        window.location.reload();
    });
    return false;
}
// New knowledgebase group, opens modal
function new_kb_group() {
    $('#kb_group_modal').modal('show');
    $('.edit-title').addClass('hide');
}
// Edit KB group, 2 places groups view or articles view directly click on kanban
function edit_kb_group(invoker, id) {
    $('#additional').append(hidden_input('id', id));
    $('#kb_group_modal input[name="name"]').val($(invoker).data('name'));
    $('#kb_group_modal textarea[name="description"]').val($(invoker).data('description'));
    $('#kb_group_modal .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
    $('#kb_group_modal input[name="group_order"]').val($(invoker).data('order'));
    var active = $(invoker).data('active');
    if (active == 0) {
        $('input[name="disabled"]').prop('checked', true);
    } else {
        $('input[name="disabled"]').prop('checked', false);
    }
    $('#kb_group_modal').modal('show');
    $('.add-title').addClass('hide');
}
// Validate the form reminder
function init_form_reminder() {
    _validate_form($('#form-reminder'), {
        date: 'required',
        staff: 'required'
    }, reminderFormHandler);
}
// Handles reminder modal form
function reminderFormHandler(form) {
    form = $(form);
    var data = form.serialize();
    var serializeArray = $(form).serializeArray();
    $.post(form.attr('action'), data).done(function(data) {
        data = JSON.parse(data);
        alert_float(data.alert_type, data.message);
        $.each(available_reminders_table, function(i, table) {
            if ($.fn.DataTable.isDataTable(table)) {
                $('body').find(table).DataTable().ajax.reload();
            }
        });
    });
    $('.reminder-modal-' + serializeArray[1]['value'] + '-' + serializeArray[0]['value']).modal('hide');
    return false;
}
// Function to close modal manualy... needed in some modals where the data is flexible.
function close_modal_manualy(modal) {
    if ($(modal).length == 0) {
        modal = $('body').find(modal);
    } else {
        modal = $(modal);
    }
    modal.fadeOut('fast', function() {
        modal.remove();
        if (!$('body').find('.modal').is(':visible')) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }
    });
}
/* Global function for editing notes */
function toggle_edit_note(id) {
    $('body').find('[data-note-edit-textarea="' + id + '"]').toggleClass('hide');
    $('body').find('[data-note-description="' + id + '"]').toggleClass('hide');
}

function edit_note(id) {
    var description = $('body').find('[data-note-edit-textarea="' + id + '"] textarea').val();
    if (description != '') {
        $.post(admin_url + 'misc/edit_note/' + id, {
            description: description
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $('body').find('[data-note-description="' + id + '"]').html(nl2br(description));
            }
        });
        toggle_edit_note(id);
    }

}

function toggle_file_visibility(attachment_id, rel_id, invoker) {
    $.get(admin_url + 'misc/toggle_file_visibility/' + attachment_id, function(response) {
        if (response == 1) {
            $(invoker).find('i').removeClass('fa fa-toggle-off').addClass('fa fa-toggle-on');
        } else {
            $(invoker).find('i').removeClass('fa fa-toggle-on').addClass('fa fa-toggle-off');
        }
    });
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

jQuery.fn.unhighlight = function(options) {
    var settings = {
        className: 'highlight',
        element: 'span'
    };
    jQuery.extend(settings, options);
    return this.find(settings.element + "." + settings.className).each(function() {
        var parent = this.parentNode;
        parent.replaceChild(this.firstChild, this);
        parent.normalize();
    }).end();
};

function fix_kanban_height(col_px, container_px) {
    // Set the width of the kanban container
    $('body').find('div.dt-loader').remove();
    var kanbanCol = $('.kan-ban-content-wrapper');
    kanbanCol.css('max-height', (window.innerHeight - col_px) + 'px');
    $('.kan-ban-content').css('min-height', (window.innerHeight - col_px) + 'px');
    var kanbanColCount = parseInt(kanbanCol.length);
    $('.container-fluid').css('min-width', (kanbanColCount * container_px) + 'px');
}

function kanban_load_more(status_id, e, url, column_px, container_px) {
    var LoadMoreParameters = new Array();
    var search = $('input[name="search"]').val();
    var _kanban_param_val;
    var page = $(e).attr('data-page');
    var total_pages = $('[data-col-status-id="' + status_id + '"]').data('total-pages');
    if (page <= total_pages) {

        var sort_type = $('input[name="sort_type"]');
        var sort = $('input[name="sort"]').val();
        if (sort_type.length != 0 && sort_type.val() != '') {
            LoadMoreParameters['sort_by'] = sort_type.val();
            LoadMoreParameters['sort'] = sort;
        }

        if (search != '') {
            LoadMoreParameters['search'] = search;
        }

        $.each($('#kanban-params input'), function() {
            _kanban_param_val = $(this).val();
            if (_kanban_param_val != '') {
                LoadMoreParameters[$(this).attr('name')] = _kanban_param_val;
            }
        });

        LoadMoreParameters['status'] = status_id;
        LoadMoreParameters['page'] = page;
        LoadMoreParameters['page']++;

        $.get(buildUrl(admin_url + url, LoadMoreParameters), function(response) {
            page++;
            $('[data-load-status="' + status_id + '"]').before(response);
            $(e).attr('data-page', page);
            fix_kanban_height(column_px, container_px);
        });

        if (page >= total_pages - 1) {
            $(e).addClass("disabled");
        }
    }
}

function check_kanban_empty_col(selector) {
    var statuses = $('[data-col-status-id]');
    $.each(statuses, function(i, obj) {
        var total = $(obj).find(selector).length;
        if (total == 0) {
            $(obj).find('.kanban-empty').removeClass('hide');
            $(obj).find('.kanban-load-more').addClass('hide');
        } else {
            $(obj).find('.kanban-empty').addClass('hide');
        }
    });
}

function init_kanban(url, callbackUpdate, connect_with, column_px, container_px, callback_after_load) {

    if ($('#kan-ban').length == 0) {
        return;
    }

    var parameters = new Array();
    var _kanban_param_val;

    $.each($('#kanban-params input'), function() {
        _kanban_param_val = $(this).val();
        if (_kanban_param_val != '') {
            parameters[$(this).attr('name')] = _kanban_param_val;
        }
    });

    var search = $('input[name="search"]').val();
    if (search !== '') {
        parameters['search'] = search;
    }

    var sort_type = $('input[name="sort_type"]');
    var sort = $('input[name="sort"]').val();
    if (sort_type.length != 0 && sort_type.val() != '') {
        parameters['sort_by'] = sort_type.val();
        parameters['sort'] = sort;
    }

    parameters['kanban'] = true;
    var url = admin_url + url;
    url = buildUrl(url, parameters);
    delay(function() {
        $('body').append('<div class="dt-loader"></div>');
        $('#kan-ban').load(url, function() {
            fix_kanban_height(column_px, container_px);

            if (typeof(callback_after_load) != 'undefined') {
                callback_after_load();
            }

            $(".status").sortable({
                connectWith: connect_with,
                helper: 'clone',
                appendTo: '#kan-ban',
                placeholder: "ui-state-highlight-card",
                revert: 'invalid',
                scroll: true,
                scrollSensitivity: 50,
                scrollSpeed: 70,
                drag: function(event, ui) {
                    var st = parseInt($(this).data("startingScrollTop"));
                    ui.position.top -= $(this).parent().scrollTop() - st;
                },
                start: function(event, ui) {
                    $(ui.helper).addClass('tilt');
                    $(ui.helper).find('.panel-body').css('background', '#fbfbfb');
                    // Start monitoring tilt direction
                    tilt_direction($(ui.helper));
                },
                stop: function(event, ui) {
                    $(ui.helper).removeClass("tilt");
                    // Unbind temporary handlers and excess data
                    $("html").unbind('mousemove', $(ui.helper).data("move_handler"));
                    $(ui.helper).removeData("move_handler");
                },
                update: function(event, ui) {
                    callbackUpdate(ui, this);
                }
            });

            $('.status').sortable({
                cancel: '.not-sortable'
            });
        });

    }, 200);
}

function tilt_direction(item) {
    setTimeout(function() {
        var left_pos = item.position().left,
            move_handler = function(e) {
                if (e.pageX >= left_pos) {
                    item.addClass("right");
                    item.removeClass("left");
                } else {
                    item.addClass("left");
                    item.removeClass("right");
                }
                left_pos = e.pageX;
            };
        $("html").bind("mousemove", move_handler);
        item.data("move_handler", move_handler);
    }, 1000);
}

/* NEWSFEED FUNCTIONS */

// When window scroll to down load more posts
$('#newsfeed').scroll(function(e) {
    var elem = $(e.currentTarget);
    if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
        load_newsfeed();
    }
    $('#newsfeed .close_newsfeed').css('top', ($(this).scrollTop() + 20) + "px");
});

function init_newsfeed_form() {
    // Configure dropzone

    if (typeof(newsFeedDropzone) == 'undefined') {
        // Init new post form
        $('body').on('submit', '#new-post-form', function() {
            $.post(this.action, $(this).serialize()).done(function(response) {
                response = JSON.parse(response);
                if (response.postid) {
                    if (newsFeedDropzone.getQueuedFiles().length > 0) {
                        newsFeedDropzone.options.url = admin_url + 'newsfeed/add_post_attachments/' + response.postid;
                        newsFeedDropzone.processQueue();
                        return;
                    }
                    newsfeed_new_post(response.postid);
                    clear_newsfeed_post_area();
                }
            });
            return false;
        });
    }


    newsFeedDropzone = new Dropzone("#new-post-form", {
        clickable: '.add-post-attachments',
        autoProcessQueue: false,
        addRemoveLinks: true,
        parallelUploads: newsfeed_maximum_files_upload,
        maxFiles: newsfeed_maximum_files_upload,
        maxFilesize: newsfeed_maximum_file_size,
        acceptedFiles: allowed_files,
        dictDefaultMessage: drop_files_here_to_upload,
        dictFallbackMessage: browser_not_support_drag_and_drop,
        dictRemoveFile: remove_file,
        dictMaxFilesExceeded: you_can_not_upload_any_more_files,
    });


    // On post added success
    newsFeedDropzone.on('success', function(files, response) {
        total_new_post_files--;
        if (total_new_post_files == 0) {
            response = JSON.parse(response);
            newsfeed_new_post(response.postid);
            clear_newsfeed_post_area();
            newsFeedDropzone.removeAllFiles();
        }
    });
    // When drag finished
    newsFeedDropzone.on("dragover", function(file) {
        $('#new-post-form').addClass('dropzone-active')
    });

    newsFeedDropzone.on("drop", function(file) {
        $('#new-post-form').removeClass('dropzone-active')
    });
    // On error files decrement total files
    newsFeedDropzone.on("error", function(file, response) {
        total_new_post_files--;
        alert_float('danger', response);
    });
    // When user click on remove files decrement total files
    newsFeedDropzone.on('removedfile', function(file) {
        total_new_post_files--;
    });
    // On added new file increment total files variable
    newsFeedDropzone.on("addedfile", function(file) {
        // Refresh total files to zero if no files are found becuase removedFile goes to --;
        if (this.getQueuedFiles().length == 0) {
            total_new_post_files = 0;
        }
        total_new_post_files++;
    });

}
// Clear newsfeed new post area
function clear_newsfeed_post_area() {
    $('#new-post-form textarea').val('');
    $('#post-visibility').selectpicker('val', 'all');
}
// Load post likes modal
function load_post_likes(postid) {

    if (track_load_post_likes <= post_likes_total_pages) {
        $.post(admin_url + 'newsfeed/load_likes_modal', {
            page: track_load_post_likes,
            postid: postid
        }).done(function(response) {
            track_load_post_likes++
            $('#modal_post_likes_wrapper').append(response);
        });

        if (track_load_post_likes >= post_likes_total_pages - 1) {
            $('.likes_modal .modal-footer').addClass('hide');
        }
    }
}
// Load comment likes modal
function load_comment_likes(commentid) {

    if (track_load_comment_likes <= comment_likes_total_pages) {
        $.post(admin_url + 'newsfeed/load_comment_likes_model', {
            page: track_load_comment_likes,
            commentid: commentid
        }).done(function(response) {
            track_load_comment_likes++
            $('#modal_comment_likes_wrapper').append(response);
        });

        if (track_load_comment_likes >= comment_likes_total_pages - 1) {
            $('.likes_modal .modal-footer').addClass('hide');
        }
    }
}
// On click href load more comments from single post
function load_more_comments(link) {
    var postid = $(link).data('postid');
    var page = $(link).find('input[name="page"]').val();
    var total_pages = $(link).data('total-pages');

    if (page <= total_pages) {
        $.post(admin_url + 'newsfeed/init_post_comments/' + postid, {
            page: page
        }).done(function(response) {
            $(link).data('track-load-comments', page);
            $('[data-comments-postid="' + postid + '"] .load-more-comments').before(response);
        });
        page++;
        $(link).find('input[name="page"]').val(page);
        if (page >= total_pages - 1) {
            $(link).addClass('hide');
            $(link).removeClass('display-block');
        }
    }
}
// new post added append data
function newsfeed_new_post(postid) {
    var data = {};
    data.postid = postid;
    $.post(admin_url + 'newsfeed/load_newsfeed', data).done(function(response) {
        var pinned = $('#newsfeed_data').find('.pinned');
        var pinned_length = pinned.length
        if (pinned_length == 0) {
            $('#newsfeed_data').prepend(response);
        } else {
            var last_pinned = $('#newsfeed_data').find('.pinned').eq(pinned_length - 1);
            $(last_pinned).after(response);
        }
    });
}
// Init newsfeed data
function load_newsfeed(postid) {

    var data = {};
    data.page = newsfeed_posts_page;
    if (typeof(postid) != 'undefined' && postid != 0) {
        data.postid = postid;
    }
    var total_pages = $('input[name="total_pages_newsfeed"]').val();
    if (newsfeed_posts_page <= total_pages) {
        $.post(admin_url + 'newsfeed/load_newsfeed', data).done(function(response) {
            newsfeed_posts_page++
            $('#newsfeed_data').append(response);
        });
        if (newsfeed_posts_page >= total_pages - 1) {
            return;
        }
    }
}
// When user click heart button
function like_post(postid) {
    $.get(admin_url + 'newsfeed/like_post/' + postid, function(response) {
        if (response.success == true) {
            refresh_post_likes(postid);
        }
    }, 'json');
}
// Unlikes post
function unlike_post(postid) {
    $.get(admin_url + 'newsfeed/unlike_post/' + postid, function(response) {
        if (response.success == true) {
            refresh_post_likes(postid);
        }
    }, 'json');
}
// Like post comment
function like_comment(commentid, postid) {
    $.get(admin_url + 'newsfeed/like_comment/' + commentid + '/' + postid, function(response) {
        if (response.success == true) {
            $('[data-commentid="' + commentid + '"]').replaceWith(response.comment);
        }
    }, 'json');
}
// Unlike post comment
function unlike_comment(commentid, postid) {
    $.get(admin_url + 'newsfeed/unlike_comment/' + commentid + '/' + postid, function(response) {
        if (response.success == true) {
            $('[data-commentid="' + commentid + '"]').replaceWith(response.comment);
        }
    }, 'json');
}
// Add new comment to post
function add_comment(input) {
    var postid = $(input).data('postid');
    $.post(admin_url + 'newsfeed/add_comment', {
        content: $(input).val(),
        postid: postid
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            $(input).val('');
            if ($('body').find('[data-comments-postid="' + postid + '"] .post-comment').length > 0) {
                $('body').find('[data-comments-postid="' + postid + '"] .post-comment').prepend(response.comment);
            } else {
                refresh_post_comments(postid);
            }
        }
    });
}
// Removes post comment
function remove_post_comment(id, postid) {
    $.get(admin_url + 'newsfeed/remove_post_comment/' + id + '/' + postid, function(response) {
        if (response.success == true) {
            $('.comment[data-commentid="' + id + '"]').remove();
        }
    }, 'json');
}
// Refreshing only post likes
function refresh_post_likes(postid) {
    $.get(admin_url + 'newsfeed/init_post_likes/' + postid + '?refresh_post_likes=true', function(response) {
        $('[data-likes-postid="' + postid + '"]').html(response);
    });
}
// Refreshing only post comments
function refresh_post_comments(postid) {
    $.post(admin_url + 'newsfeed/init_post_comments/' + postid + '?refresh_post_comments=true').done(function(response) {
        $('[data-comments-postid="' + postid + '"]').html(response);
    });
}
// Delete post from database
function delete_post(postid) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.post(admin_url + 'newsfeed/delete_post/' + postid, function(response) {
            if (response.success == true) {
                $('[data-main-postid="' + postid + '"]').remove();
            }
        }, 'json');
    }
}
// Ping post to top
function pin_post(id) {
    $.get(admin_url + 'newsfeed/pin_newsfeed_post/' + id, function(response) {
        if (response.success == true) {
            window.location.reload();
        }
    }, 'json');
}
// Unpin post from top
function unpin_post(id) {
    $.get(admin_url + 'newsfeed/unpin_newsfeed_post/' + id, function(response) {
        if (response.success == true) {
            window.location.reload();
        }
    }, 'json');
}

/* LEADS JS */

function init_lead(id) {
    // In case header error
    if (init_lead_modal_data(id)) {
        $('.lead-modal').modal('show');
    }
}

function validate_lead_form(formHandler) {
    _validate_form($('#lead_form'), {
        name: 'required',
        status: {
            required: {
                depends: function(element) {
                    if ($('[lead-is-junk-or-lost]').length > 0) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        },
        source: 'required',
        email: {
            email: true,
            remote: {
                url: admin_url + "leads/email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('input[name="email"]').val();
                    },
                    leadid: function() {
                        return $('input[name="leadid"]').val();
                    }
                }
            }
        }
    }, formHandler);
}

function validate_lead_convert_to_client_form() {

    var rules_convert_lead = {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {
                    var sent_set_password = $('input[name="send_set_password_email"]');
                    if (sent_set_password.prop('checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
            required: true,
            email: true,
            remote: {
                url: site_url + "admin/misc/contact_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#lead_to_client_form input[name="email"]').val();
                    },
                    userid: ''
                }
            }
        }

    };
    if (company_is_required == 1) {
        rules_convert_lead.company = 'required';
    }
    _validate_form($('#lead_to_client_form'), rules_convert_lead);
}
// Lead profile data function form handler
function lead_profile_form_handler(form) {
    form = $(form);
    var data = form.serialize();
    var serializeArray = $(form).serializeArray();
    var leadid = $('.lead-modal').find('input[name="leadid"]').val();
    $.post(form.attr('action'), data).done(function(response) {
        response = JSON.parse(response);
        if (response.id) {
            leadid = response.id;
        }
        if (response.message != '') {
            alert_float('success', response.message);
        }
        if (response.proposal_warning && response.proposal_warning != false) {
            $('body').find('#lead_proposal_warning').removeClass('hide');
            $('body').find('.lead-modal').animate({
                scrollTop: 0
            }, 800);
        } else {
            init_lead_modal_data(leadid);
        }

        // If is from kanban reload the list tables
        if ($.fn.DataTable.isDataTable('.table-leads')) {
            $('.table-leads').DataTable().ajax.reload();
        }
    }).fail(function(data) {
        alert_float('danger', data.responseText);
        return false;
    });
    return false;
}

function update_all_proposal_emails_linked_to_lead(id) {
    $.post(admin_url + 'leads/update_all_proposal_emails_linked_to_lead/' + id, {
        update: true
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        init_lead_modal_data(id);
    });
}

function init_lead_modal_data(id, url) {
    if (typeof(id) == 'undefined') {
        id = '';
    }
    var _url = admin_url + 'leads/lead/' + id;
    if (typeof(url) != 'undefined') {
        _url = url;
    }
    // get the current hash
    var hash = window.location.hash;
    // clean the modal
    // $('.lead-modal .modal-content').html('');
    $.get(_url, function(response) {
        $('.lead-modal .modal-content').html(response.data);
        $('#lead_reminder_modal').html(response.reminder_data);
        $('.lead-modal').modal({
            show: true,
            backdrop: 'static'
        });
        init_selectpicker();
        init_form_reminder();
        init_datepicker();
        init_color_pickers();
        validate_lead_form(lead_profile_form_handler);

        if (hash == '#tab_lead_profile' || hash == '#attachments' || hash == '#lead_notes') {
            window.location.hash = hash;
        }
        if (id != '') {

            if (typeof(Dropbox) != 'undefined') {
                document.getElementById("dropbox-chooser-lead").appendChild(Dropbox.createChooseButton({
                    success: function(files) {
                        $.post(admin_url + 'leads/add_external_attachment', {
                            files: files,
                            lead_id: id,
                            external: 'dropbox'
                        }).done(function() {
                            init_lead_modal_data(id);
                        });
                    },
                    linkType: "preview",
                    extensions: allowed_files.split(','),
                }));
            }

            if (typeof(leadAttachmentsDropzone) != 'undefined') {
                leadAttachmentsDropzone.destroy();
            }

            leadAttachmentsDropzone = new Dropzone("#lead-attachment-upload", {
                addRemoveLinks: false,
                dictDefaultMessage: drop_files_here_to_upload,
                dictFallbackMessage: browser_not_support_drag_and_drop,
                dictRemoveFile: remove_file,
                dictMaxFilesExceeded: you_can_not_upload_any_more_files,
                sending: function(file, xhr, formData) {
                    formData.append("leadid", id);
                },
                acceptedFiles: allowed_files,
                error: function(file, response) {
                    alert_float('danger', response);
                },
                success: function(file) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        init_lead_modal_data(id);
                    }
                }
            });

            $('body').find('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
            $('#lead-latest-activity').html($('#lead_activity').find('.feed-item:last-child .text').html());
        }

    }, 'json').fail(function(data) {
        $('.lead-modal').modal('hide');
        alert_float('danger', data.responseText);
    });
}
// Kanban lead sort
function leads_kanban_sort(type) {
    var sort_type = $('input[name="sort_type"]');
    var sort = $('input[name="sort"]');
    sort_type.val(type);
    if (sort.val() == 'ASC') {
        sort.val('DESC');
    } else if (sort.val() == 'DESC') {
        sort.val('ASC');
    } else {
        sort.val('DESC');
    }

    leads_kanban();
}

function leads_kanban_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var data = {};
        data.status = $(ui.item.parent()[0]).data('lead-status-id');
        data.leadid = $(ui.item).data('lead-id');

        var order = [];
        var status = $(ui.item).parents('.leads-status').find('li')
        var i = 1;
        $.each(status, function() {
            order.push([$(this).data('lead-id'), i]);
            i++;
        });

        data.order = order;
        setTimeout(function() {
            $.post(admin_url + 'leads/update_kan_ban_lead_status', data).done(function(response) {
                check_kanban_empty_col('[data-lead-id]');
            });
        }, 200);
    }
}

function init_leads_status_sortable() {
    $("#kan-ban").sortable({
        helper: 'clone',
        item: '.kan-ban-col',
        update: function(event, ui) {
            var order = [];
            var status = $('.kan-ban-col');
            var i = 0;
            $.each(status, function() {
                order.push([$(this).data('col-status-id'), i]);
                i++;
            });
            var data = {}
            data.order = order;
            $.post(admin_url + 'leads/update_status_order', data);
        }
    });
}
// Init the leads kanban
function leads_kanban(search) {
    init_kanban('leads', leads_kanban_update, '.leads-status', 290, 360, init_leads_status_sortable);
}
// Deleting lead attachments
function delete_lead_attachment(wrapper, id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'leads/delete_attachment/' + id, function(response) {
            if (response.success == true) {
                $(wrapper).parents('.lead-attachment-wrapper').remove();
            }
        }, 'json');
    }
}
// Deleting lead note
function delete_lead_note(wrapper, id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'leads/delete_note/' + id, function(response) {
            if (response.success == true) {
                $(wrapper).parents('.lead-note').remove();
            }
        }, 'json');
    }
}
// Mark lead as lost function
function lead_mark_as_lost(lead_id) {
    $.get(admin_url + 'leads/mark_as_lost/' + lead_id, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
        }
        init_lead_modal_data(lead_id);
    }, 'json');
}
// Unmark lead as lost function
function lead_unmark_as_lost(lead_id) {
    $.get(admin_url + 'leads/unmark_as_lost/' + lead_id, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
        }
        init_lead_modal_data(lead_id);
    }, 'json');
}
// Mark lead as junk function
function lead_mark_as_junk(lead_id) {
    $.get(admin_url + 'leads/mark_as_junk/' + lead_id, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
        }
        init_lead_modal_data(lead_id);
    }, 'json');
}
// Unmark lead as junk function
function lead_unmark_as_junk(lead_id) {
    $.get(admin_url + 'leads/unmark_as_junk/' + lead_id, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
        }
        init_lead_modal_data(lead_id);
    }, 'json');
}
// Statuses function for add/edit becuase there is ability to edit the status directly from the lead kanban
$(function() {
    _validate_form($('body').find('#leads-status-form'), {
        name: 'required'
    }, manage_leads_statuses);
    $('#status').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#status input').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
        $('#status input[name="statusorder"]').val($('table tbody tr').length + 1);
    });
});
// Form handler function for leads status
function manage_leads_statuses(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        window.location.reload();
    });
    return false;
}
// Convert lead to customer
function convert_lead_to_customer(id) {
    $('.lead-modal').modal('hide');
    $.get(admin_url + 'leads/get_convert_data/' + id, function(response) {
        $('#lead_convert_to_customer').html(response);
        $('#convert_lead_to_client_modal').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });
}
// Create lead new status
function new_status() {
    $('#status').modal('show');
    $('.edit-title').addClass('hide');
}
// Edit status function which init the data to the modal
function edit_status(invoker, id) {
    $('#additional').append(hidden_input('id', id));
    $('#status input[name="name"]').val($(invoker).data('name'));
    $('#status .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
    $('#status input[name="statusorder"]').val($(invoker).data('order'));
    $('#status').modal('show');
    $('.add-title').addClass('hide');
}

function leads_bulk_action(event) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        var mass_delete = $('#mass_delete').prop('checked');
        var ids = [];
        var data = {};
        if (mass_delete == false || typeof(mass_delete) == 'undefined') {
            data.status = $('#move_to_status_leads_bulk').val();
            data.assigned = $('#assign_to_leads_bulk').val();
            data.source = $('#move_to_source_leads_bulk').val();
            data.last_contact = $('#leads_bulk_last_contact').val();
            if (typeof(data.assigned) == 'undefined') {
                data.assigned = '';
            }
            if (data.status == '' && data.assigned == '' && data.source == '' && data.last_contact == '') {
                return;
            }
        } else {
            data.mass_delete = true;
        }

        var rows = $('.table-leads').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'leads/bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}

function sync_proposals_data(rel_id, rel_type) {
    var data = {};
    var modal_sync = $('#sync_data_proposal_data');
    data.country = modal_sync.find('select[name="country"]').val();
    data.zip = modal_sync.find('input[name="zip"]').val();
    data.state = modal_sync.find('input[name="state"]').val();
    data.city = modal_sync.find('input[name="city"]').val();
    data.address = modal_sync.find('input[name="address"]').val();
    data.phone = modal_sync.find('input[name="phone"]').val();
    data.rel_id = rel_id;
    data.rel_type = rel_type;
    $.post(admin_url + 'proposals/sync_data', data).done(function(response) {
        response = JSON.parse(response);
        alert_float('success', response.message);
        modal_sync.modal('hide');
    });
}

function init_table_announcements(manual) {
    if (typeof(manual) == 'undefined' && $('body').hasClass('home')) {
        return false;
    }
    initDataTable('.table-announcements', admin_url + 'announcements', [2], [2], 'undefined', [1, 'DESC']);
}

function init_table_tickets(manual) {

    if (typeof(manual) == 'undefined' && $('body').hasClass('home')) {
        return false;
    }

    if ($('body').find('.tickets-table').length == 0) {
        return;
    }

    var tickets_not_sortable = $('.tickets-table').find('th').length - 1;
    var TicketServerParams = {},
        Tickets_Filters = $('._hidden_inputs._filters.tickets_filters input');
    var tickets_date_created_index = $('table.tickets-table thead .ticket_created_column').index();
    $.each(Tickets_Filters, function() {
        TicketServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });

    TicketServerParams['project_id'] = '[name="project_id"]';
    var _tns = [tickets_not_sortable];
    var _tickets_table_url = admin_url + 'tickets';
    if ($('body').hasClass('tickets_page')) {
        _tns.push(0);
        _tickets_table_url += '?bulk_actions=true';
    }
    _table_api = initDataTable('.tickets-table', _tickets_table_url, _tns, _tns, TicketServerParams, [tickets_date_created_index, 'DESC']);

    if (_table_api && $('body').hasClass('home')) {
        _table_api.column(tickets_not_sortable).visible(false, false).column(3).visible(false, false).column(tickets_date_created_index).visible(false,false).column(4).visible(false, false).columns.adjust();
    }
}

function init_table_staff_projects(manual) {
    if (typeof(manual) == 'undefined' && $('body').hasClass('home')) {
        return false;
    }

    if ($('body').find('.table-staff-projects').length == 0) {
        return;
    }

    var staffProjectsParams = {},
        Staff_Projects_Filters = $('._hidden_inputs._filters.staff_projects_filter input');
    $.each(Staff_Projects_Filters, function() {
        staffProjectsParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });

    initDataTable('.table-staff-projects', admin_url + 'projects/staff_projects', 'undefined', 'undefined', staffProjectsParams, [2, 'ASC']);
}

function do_task_checklist_items_height(task_checklist_items) {
    if (typeof(task_checklist_items) == 'undefined') {
        task_checklist_items = $('body').find("textarea[name='checklist-description']");
    }

    $.each(task_checklist_items, function() {
        var val = $(this).val();
        if ($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
            $(this).height(0).height(this.scrollHeight);
            $(this).parents('.checklist').height(this.scrollHeight);
        }
        if (val == '') {
            $(this).removeAttr('style');
            $(this).parents('.checklist').removeAttr('style');
        }
    });
}

function recalculate_checklist_items_progress() {
    var total_finished = $('input[name="checklist-box"]:checked').length;
    var total_checklist_items = $('input[name="checklist-box"]').length;
    var percent = 0,
        task_progress_bar = $('.task-progress-bar');
    if (total_checklist_items == 0) {
        // remove the heading for checklist items
        $('body').find('.chk-heading').remove();
        $('#task-no-checklist-items').removeClass('hide');
    } else {
        $('#task-no-checklist-items').addClass('hide');
    }
    if (total_checklist_items > 2) {
        task_progress_bar.parents('.progress').removeClass('hide');
        percent = (total_finished * 100) / total_checklist_items;
    } else {
        task_progress_bar.parents('.progress').addClass('hide');
        return false;
    }
    task_progress_bar.css('width', percent.toFixed(2) + '%');
    task_progress_bar.text(percent.toFixed(2) + '%');
}

function delete_checklist_item(id, field) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'tasks/delete_checklist_item/' + id, function(response) {
            if (response.success == true) {
                $(field).parents('.checklist').remove();
                recalculate_checklist_items_progress();
            }
        }, 'json');
    }
}

function add_task_checklist_item(task_id) {
    $.post(admin_url + 'tasks/add_checklist_item', {
        taskid: task_id
    }).done(function() {
        init_tasks_checklist_items(true, task_id);
    });
}

function init_tasks_checklist_items(is_new, task_id) {
    $.post(admin_url + 'tasks/init_checklist_items', {
        taskid: task_id
    }).done(function(data) {
        $('#checklist-items').html(data);
        if (typeof(is_new) != 'undefined') {
            $('body').find('.checklist textarea').eq(0).focus();
        }
        recalculate_checklist_items_progress();
        update_checklist_order();
    });
}

function remove_task_attachment(link, id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'tasks/remove_task_attachment/' + id, function(response) {
            if (response.success == true) {
                $(link).parents('.task-attachment-col').remove();
            }
            var att_wrap = $('body').find('.task_attachments_wrapper');
            var attachments = att_wrap.find('.task-attachment-col');
            if (attachments.length == 0) {
                att_wrap.remove();
            } else if (attachments.length == 2 && $('body').find('#show-more-less-task-attachments-col .task-attachments-more').hasClass('hide')) {
                $('body').find('#show-more-less-task-attachments-col').remove();
            }
        }, 'json');
    }
}

function add_task_comment(task_id) {
    var data = {};
    data.content = tinyMCE.activeEditor.getContent();
    data.taskid = task_id;
    $.post(admin_url + 'tasks/add_task_comment', data).done(function(response) {
        init_task_modal(task_id);
    });
}
// Deletes task comment from database
function remove_task_comment(commentid) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'tasks/remove_comment/' + commentid, function(response) {
            if (response.success == true) {
                $('[data-commentid="' + commentid + '"]').remove();
            }
        }, 'json');
    }
}
// Remove task assignee
function remove_assignee(id, task_id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'tasks/remove_assignee/' + id + '/' + task_id, function(response) {
            if (response.success == true) {
                alert_float('success', response.message);
            } else {
                alert_float('warning', response.message);
            }
            init_task_modal(task_id);
        }, 'json');
    }
}
// Remove task follower
function remove_follower(id, task_id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'tasks/remove_follower/' + id + '/' + task_id, function(response) {
            if (response.success == true) {
                alert_float('success', response.message);
                init_task_modal(task_id);
            }
        }, 'json');
    }
}

// Marking task as complete
function mark_complete(task_id) {
    task_mark_as(5, task_id, admin_url + 'tasks/mark_complete/' + task_id);
}
// Unmarking task as complete
function unmark_complete(task_id) {
    task_mark_as(4, task_id, admin_url + 'tasks/unmark_complete/' + task_id);
}

function task_mark_as(status, task_id, url) {
    if (typeof(url) == 'undefined') {
        url = admin_url + 'tasks/mark_as/' + status + '/' + task_id;
    }
    $('body').append('<div class="dt-loader"></div>');
    $.get(url, function(response) {
        $('body').find('.dt-loader').remove();
        if (response.success == true) {
            reload_tasks_tables();
            if ($('.task-modal-single').is(':visible')) {
                init_task_modal(task_id);
            }
            if ($('.tasks-kanban').length == 0) {
                alert_float('success', response.message);
            }
        }
    }, 'json');
}

function reload_tasks_tables() {

    if ($.fn.DataTable.isDataTable('.table-tasks')) {
        $('.table-tasks').DataTable().ajax.reload();
    }
    if ($.fn.DataTable.isDataTable('.table-rel-tasks')) {
        $('.table-rel-tasks').DataTable().ajax.reload();
    }
    if ($.fn.DataTable.isDataTable('.table-rel-tasks-leads')) {
        $('.table-rel-tasks-leads').DataTable().ajax.reload();
    }
    if ($.fn.DataTable.isDataTable('.table-timesheets')) {
        $('.table-timesheets').DataTable().ajax.reload();
    }
}

function make_task_public(task_id) {
    $.get(admin_url + 'tasks/make_public/' + task_id, function(response) {
        if (response.success == true) {
            reload_tasks_tables();
            init_task_modal(task_id);
        }
    }, 'json');
}

function new_task(url) {
    var _url = admin_url + 'tasks/task';
    if (typeof(url) != 'undefined') {
        _url = url;
    }
    $.get(_url, function(response) {
        $('#_task').html(response);
        $('body').find('#_task_modal').modal({
            show: true,
            backdrop: 'static'
        });
    });
}

function new_task_from_relation(table, rel_type, rel_id) {
    if (typeof(rel_type) == 'undefined' && typeof(rel_id) == 'undefined') {
        rel_id = $(table).data('new-rel-id');
        rel_type = $(table).data('new-rel-type');
    }
    var url = admin_url + 'tasks/task?rel_id=' + rel_id + '&rel_type=' + rel_type;
    new_task(url);
}

// Go to edit view
function edit_task(task_id) {
    $.get(admin_url + 'tasks/task/' + task_id, function(response) {
        $('#_task').html(response)
        $('.task-modal-single').modal('hide');
        $('body').find('#_task_modal').modal({
            show: true,
            backdrop: 'static'
        });
    });
}

function task_form_handler(form) {
    tinymce.triggerSave();
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
        }
        if (!$('body').hasClass('project')) {
            $('#_task_modal').modal('hide');
            init_task_modal(response.id);
            reload_tasks_tables();
        } else {
            $('#_task_modal').modal('hide');
            // reload page on project area
            var location = window.location.href;
            var parameters = new Array();
            location = location.split('?');
            var group = get_url_param('group');
            if (group) {
                parameters['group'] = group;
            }
            parameters['taskid'] = response.id;
            window.location.href = buildUrl(location[0], parameters)
        }
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
    return false;
}

function timer_action(e, task_id, timer_id) {
    if (typeof(timer_id) == 'undefined') {
        timer_id = '';
    }
    $(e).addClass('disabled');
    $.get(admin_url + 'tasks/timer_tracking/' + task_id + '/' + timer_id, function(response) {
        $(e).removeClass('disabled');
        if ($('.task-modal-single').is(':visible')) {
            init_task_modal(task_id);
        }
        init_timers();
        reload_tasks_tables();
    }, 'json');
}

function init_task_modal(task_id) {
    tinymce.remove('#task_comment');
    $.post(admin_url + 'tasks/get_task_data/', {
        taskid: task_id
    }).done(function(response) {
        $('.task-modal-single .data').html(response);
        init_editor('#task_comment', {
            height: 150
        });
        init_tasks_checklist_items(false, task_id);
        setTimeout(function() {
            $('.task-modal-single').modal('show');
            fix_task_modal_left_col_height();
        }, 150);
    }).fail(function(data) {
        $('.task-modal-single').modal('hide');
        alert_float('danger', data.responseText);
    });
}

function task_tracking_stats(id) {
    $.get(admin_url + 'tasks/task_tracking_stats/' + id, function(response) {
        $('body').find('#tacking-stats').html(response);
        $('#task-tracking-stats-modal').modal('toggle');
    });
}

function init_timers() {
    $.get(admin_url + 'tasks/get_staff_started_timers', function(response) {
        if (response.timers_found) {
            $('.top-timers').addClass('text-warning');
        } else {
            $('.top-timers').removeClass('text-warning');
        }
        $('.started-timers-top').html(response.html);
    }, 'json');
}

function edit_task_comment(id) {
    var edit_wrapper = $('[data-edit-comment="' + id + '"]');
    edit_wrapper.next().addClass('hide');
    edit_wrapper.removeClass('hide');
    init_editor('#task_comment_' + id, {
        height: 150,
    });
    tinymce.triggerSave();
}

function cancel_edit_comment(id) {
    var edit_wrapper = $('[data-edit-comment="' + id + '"]');
    tinymce.remove('[data-edit-comment="' + id + '"] textarea');
    edit_wrapper.addClass('hide');
    edit_wrapper.next().removeClass('hide');
}

function save_edited_comment(id, task_id) {
    tinymce.triggerSave();
    var data = {};
    data.id = id;
    data.content = $('[data-edit-comment="' + id + '"]').find('textarea').val();
    $.post(admin_url + 'tasks/edit_comment', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
            init_task_modal(task_id);
        } else {
            cancel_edit_comment(id);
        }
        tinymce.remove('[data-edit-comment="' + id + '"] textarea');
    });
}

function update_checklist_order() {
    var order = [];
    var status = $('body').find('.checklist');
    var i = 1;
    $.each(status, function() {
        order.push([$(this).data('checklist-id'), i]);
        i++;
    });
    var data = {}
    data.order = order;
    $.post(admin_url + 'tasks/update_checklist_order', data);
}

function fix_task_modal_left_col_height() {
    var left_col = $('body').find('.task-single-col-left');
    var right_col = $('body').find('.task-single-col-right');
    left_col.css('min-height', right_col.outerHeight(true) + 'px');
}

function tasks_kanban_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var status = $(ui.item.parent()[0]).data('task-status-id');
        var tasks = $(ui.item.parent()[0]).find('[data-task-id]');

        var data = {};
        data.order = [];
        var i = 0;
        $.each(tasks, function() {
            data.order.push([$(this).data('task-id'), i]);
            i++;
        });

        task_mark_as(status, $(ui.item).data('task-id'));
        check_kanban_empty_col('[data-task-id]');
        setTimeout(function() {
            $.post(admin_url + 'tasks/update_order', data);
        }, 200);
    }
}

function tasks_kanban() {
    init_kanban('tasks', tasks_kanban_update, '.tasks-status', 270, 360);
}

function edit_task_inline_description(e, id) {

    tinyMCE.remove('#task_view_description');

    if ($(e).hasClass('editor-initiated')) {
        $(e).removeClass('editor-initiated');
        return;
    }

    $(e).addClass('editor-initiated');
    tinymce.init({
        selector: '#task_view_description',
        theme: 'inlite',
        skin: 'perfex',
        auto_focus: "task_view_description",
        plugins: 'table link paste contextmenu textpattern',
        insert_toolbar: 'quicktable',
        selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
        inline: true,
        table_default_styles: {
            width: '100%'
        },
        setup: function(editor) {
            editor.on('blur', function(e) {
                if (editor.isDirty()) {
                    $.post(admin_url + 'tasks/update_task_description/' + id, {
                        description: editor.getContent()
                    });
                }
                setTimeout(function() {
                    editor.remove();
                }, 500);
            });
        }
    });
}

function tasks_bulk_action(event) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        var mass_delete = $('#mass_delete').prop('checked');
        var ids = [];
        var data = {};
        if (mass_delete == false || typeof(mass_delete) == 'undefined') {
            data.status = $('#move_to_status_tasks_bulk_action').val();
            data.priority = $('#task_bulk_priority').val();
            if (typeof(data.priority) == 'undefined') {
                data.priority = '';
            }
            if (data.status == '' && data.priority == '') {
                return;
            }
        } else {
            data.mass_delete = true;
        }
        var rows = $('.table-tasks').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'tasks/bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}

/* SALES FUNCTIONS */


// Init single invoice
function init_invoice(id) {
    var _invoiceid = $('input[name="invoiceid"]').val();
    // Check if invoice passed from url, hash is prioritized becuase is last
    if (_invoiceid != '' && !window.location.hash) {
        id = _invoiceid;
        // Clear the current invoice value in case user click on the left sidebar invoices
        $('input[name="invoiceid"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-invoices', '#invoice');
    }
    $('input[name="invoiceid"]').val(id);
    do_hash_helper(id);
    $('#invoice').load(admin_url + 'invoices/get_invoice_data_ajax/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#invoice').offset().top + 150
        }, 600);
    }
}
// Init single Estimate
function init_estimate(id) {
    var _estimateid = $('input[name="estimateid"]').val();

    // Check if estimate passed from url, hash is prioritized becuase is last
    if (_estimateid != '' && !window.location.hash) {
        id = _estimateid;
        // Clear the current estimate value in case user click on the left sidebar invoices
        $('input[name="estimateid"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }

    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-estimates', '#estimate');
    }
    $('input[name="estimateid"]').val(id);
    do_hash_helper(id);
    $('#estimate').load(admin_url + 'estimates/get_estimate_data_ajax/' + id);

    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#estimate').offset().top + 150
        }, 600);
    }
}

// Init single Estimate
function init_proposal(id) {
    var _proposal_id = $('input[name="proposal_id"]').val();
    // Check if proposal passed from url, hash is prioritized becuase is last
    if (_proposal_id != '' && !window.location.hash) {
        id = _proposal_id;
        // Clear the current proposal value in case user click on the left sidebar invoices
        $('input[name="proposal_id"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-proposals', '#proposal');
    }
    $('input[name="proposal_id"]').val(id);
    do_hash_helper(id);
    $('#proposal').load(admin_url + 'proposals/get_proposal_data_ajax/' + id);

    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#proposal').offset().top + 150
        }, 600);
    }
}

function clear_billing_and_shipping_details() {
    for (var f in bs_fields) {
        if (bs_fields[f].indexOf('country') > -1) {
            $('select[name="' + bs_fields[f] + '"]').selectpicker('val', '');
        } else {
            $('input[name="' + bs_fields[f] + '"]').val('');
        }
        if (bs_fields[f] == 'billing_country') {
            $('input[name="include_shipping"]').prop("checked", false);
            $('input[name="include_shipping"]').change();
        }
    }

    init_billing_and_shipping_details();
}

function init_billing_and_shipping_details() {
    var _f;
    var include_shipping = $('input[name="include_shipping"]').prop('checked');
    for (var f in bs_fields) {
        _f = '';
        if (bs_fields[f].indexOf('country') > -1) {
            _f = $("#" + bs_fields[f] + " option:selected").data('subtext');
        } else {
            _f = $('input[name="' + bs_fields[f] + '"]').val();
        }
        if (bs_fields[f].indexOf('shipping') > -1) {
            if (!include_shipping) {
                _f = '';
            }
        }
        if (typeof(_f) == 'undefined') {
            _f = '';
        }
        _f = (_f != '' ? _f : '--');
        $('.' + bs_fields[f]).text(_f);
    }
    $('#billing_and_shipping_details').modal('hide');
}
// Record payment function
function record_payment(id) {
    if (typeof(id) == 'undefined' || id == '') {
        return;
    }
    $('#invoice').load(admin_url + 'invoices/record_invoice_payment_ajax/' + id);
}
// Add item to preview
function add_item_to_preview(itemid) {
    $.get(admin_url + 'invoice_items/get_item_by_id/' + itemid, function(response) {
        if (!response.taxname) {
            response.taxname = '';
        }
        $('textarea[name="description"]').val(response.description);
        $('textarea[name="long_description"]').val(response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
        $('input[name="quantity"]').val(1);
        if (response.taxname && response.taxrate) {
            $('.main select.tax').selectpicker('val', response.taxname + '|' + response.taxrate);
        }
        if (response.unit) {
            $('th.qty').attr('unit', response.unit);
        }
        $('input[name="rate"]').val(response.rate);
    }, 'json');
}
// Add task to preview
function add_task_to_preview_as_item(task_id) {
    $.get(admin_url + 'tasks/get_billable_task_data/' + task_id, function(response) {
        response.taxname = $('select.main-tax').selectpicker('val');
        $('textarea[name="description"]').val(response.name);
        $('textarea[name="long_description"]').val(response.description);
        $('input[name="quantity"]').val(response.total_hours);
        $('input[name="rate"]').val(response.hourly_rate);
        $('input[name="task_id"]').val(task_id);
    }, 'json');
}
// Clear the items added to preview
function clear_main_values(default_taxes) {
    // Get the last taxes applied to be available for the next item
    var last_taxes_applied = $('table.items tbody').find('tr:last-child').find('select').selectpicker('val');
    $('textarea[name="description"]').val('');
    $('textarea[name="long_description"]').val('');
    $('input[name="quantity"]').val(1);
    $('.main select.tax').selectpicker('val', last_taxes_applied);
    $('input[name="rate"]').val('');
    $('input[name="item-search"]').val('');
    $('.item-search .dropdown-menu').html('');
    $('input[name="task_id"]').val('');
    $('th.qty').removeAttr('unit');
}

// Append the added items to the preview to the table as items
function add_item_to_table(data, itemid, merge_invoice, bill_expense) {
    // If not custom data passed get from the preview
    if (typeof(data) == 'undefined' || data == 'undefined') {
        data = get_main_values();
    }
    var table_row = '';
    var unit_placeholder = '';
    var item_key = $('body').find('tbody .item').length + 1;
    table_row += '<tr class="sortable item" data-merge-invoice="' + merge_invoice + '" data-bill-expense="' + bill_expense + '">';
    table_row += '<td class="dragger">';
    // Check if quantity is number
    if (isNaN(data.qty)) {
        data.qty = 1;
    }
    // Check if rate is number
    if (data.rate == '' || isNaN(data.rate)) {
        data.rate = 0;
    }
    var amount = data.rate * data.qty;
    amount = accounting.formatNumber(amount);
    var tax_name = 'newitems[' + item_key + '][taxname][]';
    $('body').append('<div class="dt-loader"></div>');
    var regex = /<br[^>]*>/gi;
    get_taxes_dropdown_template(tax_name, data.taxname).done(function(tax_dropdown) {
        // order input
        table_row += '<input type="hidden" class="order" name="newitems[' + item_key + '][order]">';
        table_row += '</td>';
        table_row += '<td class="bold description"><textarea name="newitems[' + item_key + '][description]" class="form-control" rows="5">' + data.description + '</textarea></td>';
        table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" class="form-control item_long_description" rows="5">' + data.long_description.replace(regex, "\n") + '</textarea></td>';
        table_row += '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="newitems[' + item_key + '][qty]" value="' + data.qty + '" class="form-control">';

        unit_placeholder = '';
        if (!data.unit || typeof(data.unit) == 'undefined') {
            unit_placeholder = lang_unit;
            data.unit = '';
        }
        table_row += '<input type="text" placeholder="' + unit_placeholder + '" name="newitems[' + item_key + '][unit]" class="form-control input-transparent text-right" value="' + data.unit + '">';

        table_row += '</td>';
        table_row += '<td class="rate"><input type="text" data-toggle="tooltip" title="' + item_field_not_formated + '" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][rate]" value="' + data.rate + '" class="form-control"></td>';
        table_row += '<td class="taxrate">' + tax_dropdown + '</td>';
        table_row += '<td class="amount">' + amount + '</td>';
        table_row += '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' + itemid + '); return false;"><i class="fa fa-trash"></i></a></td>';
        table_row += '</tr>';
        $.when($('table.items tbody').append(table_row)).then(calculate_total);
        var billed_task = $('input[name="task_id"]').val();
        var billed_expense = $('input[name="expense_id"]').val();
        if (billed_task != '' && typeof(billed_task) != 'undefined') {
            billed_tasks = billed_task.split(',');
            $.each(billed_tasks, function(i, obj) {
                $('#billed-tasks').append(hidden_input('billed_tasks[' + item_key + '][]', obj));
            });
        }

        if (billed_expense != '' && typeof(billed_expense) != 'undefined') {

            billed_expenses = billed_expense.split(',');
            $.each(billed_expenses, function(i, obj) {
                $('#billed-expenses').append(hidden_input('billed_expenses[' + item_key + '][]', obj));
            });
        }
        init_selectpicker();
        clear_main_values();
        reorder_items();
        $('body').find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');
        return true;
    });
    return false;
}
// Get taxes dropdown selectpicker template / Causing problems with ajax becuase is fetching from server
function get_taxes_dropdown_template(name, taxname) {
    jQuery.ajaxSetup({
        async: false
    });

    var d = $.post(admin_url + 'misc/get_taxes_dropdown_template/', {
        name: name,
        taxname: taxname
    });

    jQuery.ajaxSetup({
        async: true
    });

    return d;
}
// Fix for reordering the items the tables to show the full width
function fixHelperTableHelperSortable(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function init_items_sortable(preview_table) {
    var _items_sortable = $("body").find('.items tbody');
    if (_items_sortable.length == 0) {
        return;
    }
    _items_sortable.sortable({
        helper: fixHelperTableHelperSortable,
        handle: '.dragger',
        placeholder: 'ui-placeholder',
        itemPath: '> tbody',
        itemSelector: 'tr.sortable',
        items: "tr.sortable",
        update: function() {
            if (typeof(preview_table) == 'undefined') {
                reorder_items();
            } else {
                // If passed from the admin preview there is other function for re-ordering
                save_ei_items_order();
            }
        },
        sort: function(event, ui) {
            // Firefox fixer when dragging
            var $target = $(event.target);
            if (!/html|body/i.test($target.offsetParent()[0].tagName)) {
                var top = event.pageY - $target.offsetParent().offset().top - (ui.helper.outerHeight(true) / 2);
                ui.helper.css({
                    'top': top + 'px'
                });
            }
        }
    });
}
// Save the items from order from the admin preview
function save_ei_items_order() {
    var rows = $('.table.invoice-items-preview.items tbody tr,.table.estimate-items-preview.items tbody tr');
    var i = 1;
    var order = [];
    var _order_id, type;
    var item_id;
    if ($('.table.items').hasClass('invoice-items-preview')) {
        type = 'invoice';
    } else if ($('.table.items').hasClass('estimate-items-preview')) {
        type = 'estimate';
    } else {
        return false;
    }
    $.each(rows, function() {
        order.push([$(this).data('item-id'), i]);
        // update item number when reordering
        $(this).find('td.item_no').html(i);
        i++;
    });
    setTimeout(function() {
        $.post(admin_url + 'misc/update_ei_items_order/' + type, {
            data: order
        });
    }, 200);
}
// Reoder the items in table edit for estimate and invoices
function reorder_items() {
    var rows = $('.table.table-main-invoice-edit tbody tr.item,.table.table-main-estimate-edit tbody tr.item');
    var i = 1;
    $.each(rows, function() {
        $(this).find('input.order').val(i);
        i++;
    });
}
// Get the preview main values
function get_main_values() {
    var response = {};
    response.description = $('textarea[name="description"]').val();
    response.long_description = $('textarea[name="long_description"]').val();
    response.qty = $('input[name="quantity"]').val();
    response.taxname = $('.main select.tax').selectpicker('val');
    response.rate = $('input[name="rate"]').val();
    response.unit = $('th.qty').attr('unit');
    return response;
}
// Calculate invoice total - NOT RECOMENDING EDIT THIS FUNCTION BECUASE IS VERY SENSITIVE
function calculate_total() {

    var calculated_tax,
        taxrate,
        item_taxes,
        row,
        _amount,
        _tax_name,
        taxes = {},
        taxes_rows = [],
        subtotal = 0,
        total = 0,
        quantity = 1;
    total_discount_calculated = 0,
        rows = $('.table.table-main-invoice-edit tbody tr.item,.table.table-main-estimate-edit tbody tr.item'),
        adjustment = $('input[name="adjustment"]').val(),
        discount_area = $('tr#discount_percent'),
        discount_percent = $('input[name="discount_percent"]').val();
    discount_type = $('select[name="discount_type"]').val();

    $('.tax-area').remove();

    $.each(rows, function() {
        quantity = $(this).find('[data-quantity]').val();
        if (quantity == '') {
            quantity = 1;
            $(this).find('[data-quantity]').val(1);
        }
        _amount = parseFloat($(this).find('td.rate input').val()) * quantity;
        $(this).find('td.amount').html(accounting.formatNumber(_amount));
        subtotal += _amount;
        row = $(this);
        item_taxes = $(this).find('select.tax').selectpicker('val');

        if (item_taxes) {
            $.each(item_taxes, function(i, taxname) {
                taxrate = row.find('select.tax [value="' + taxname + '"]').data('taxrate');
                calculated_tax = (_amount / 100 * taxrate);
                if (!taxes.hasOwnProperty(taxname)) {
                    if (taxrate != 0) {
                        _tax_name = taxname.split('|');
                        tax_row = '<tr class="tax-area"><td>' + _tax_name[0] + '(' + taxrate + '%)</td><td id="tax_id_' + slugify(taxname) + '"></td></tr>';
                        $(discount_area).after(tax_row);
                        taxes[taxname] = calculated_tax;
                    }
                } else {
                    // Increment total from this tax
                    taxes[taxname] = taxes[taxname] += calculated_tax;
                }
            });
        }
    });

    if (discount_percent != '' && discount_type == 'before_tax') {
        // Calculate the discount total
        total_discount_calculated = (subtotal * discount_percent) / 100;
    }

    $.each(taxes, function(taxname, total_tax) {
        if (discount_percent != '' && discount_type == 'before_tax') {
            total_tax_calculated = (total_tax * discount_percent) / 100;
            total_tax = (total_tax - total_tax_calculated);
        }

        total += total_tax;
        total_tax = accounting.formatNumber(total_tax)
        $('#tax_id_' + slugify(taxname)).html(total_tax);

    });

    total = (total + subtotal);

    if (discount_percent != '' && discount_type == 'after_tax') {
        // Calculate the discount total
        total_discount_calculated = (total * discount_percent) / 100;
    }

    total = total - total_discount_calculated;
    adjustment = parseFloat(adjustment);

    // Check if adjustment not empty
    if (!isNaN(adjustment)) {
        total = total + adjustment;
    }

    // Append, format to html and display
    $('.discount_percent').html('-' + accounting.formatNumber(total_discount_calculated) + hidden_input('discount_percent', discount_percent) + hidden_input('discount_total', total_discount_calculated));
    $('.adjustment').html(accounting.formatNumber(adjustment) + hidden_input('adjustment', adjustment.toFixed(decimal_places)))
    $('.subtotal').html(subtotal = accounting.formatNumber(subtotal) + hidden_input('subtotal', subtotal.toFixed(decimal_places)));
    $('.total').html(format_money(total) + hidden_input('total', total.toFixed(decimal_places)));
}
// Deletes invoice items
function delete_item(row, itemid) {
    $(row).parents('tr').addClass('animated fadeOut', function() {
        setTimeout(function() {
            $(row).parents('tr').remove();
            calculate_total();
        }, 50)
    });
    // If is edit we need to add to input removed_items to track activity
    if ($('input[name="isedit"]').length > 0) {
        $('#removed-items').append(hidden_input('removed_items[]', itemid));
    }
}
// Format money functions
function format_money(total) {
    if (currency_placement === 'after') {
        return accounting.formatMoney(total, {
            format: "%v %s"
        });
    } else {
        return accounting.formatMoney(total);
    }
}

// Set the currency symbol for accounting
function init_currency_symbol() {
    if (typeof(accounting) != 'undefined') {
        accounting.settings.currency.symbol = $('body').find('.accounting-template select[name="currency"]').find('option:selected').data('subtext');
        calculate_total();
    }
}

function delete_invoice_attachment(id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'invoices/delete_attachment/' + id, function(success) {
            if (success == 1) {
                $('body').find('[data-attachment-id="' + id + '"]').remove();
                init_invoice($('body').find('input[name="_attachment_sale_id"]').val());
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
}

function delete_estimate_attachment(id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'estimates/delete_attachment/' + id, function(success) {
            if (success == 1) {
                $('body').find('[data-attachment-id="' + id + '"]').remove();
                var rel_id = $('body').find('input[name="_attachment_sale_id"]').val();
                if ($('body').hasClass('estimates-pipeline')) {
                    estimate_pipeline_open(rel_id)
                } else {
                    init_estimate(rel_id);
                }
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
}

function delete_proposal_attachment(id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'proposals/delete_attachment/' + id, function(success) {
            if (success == 1) {
                var rel_id = $('body').find('input[name="_attachment_sale_id"]').val();
                $('body').find('[data-attachment-id="' + id + '"]').remove();

                if ($('body').hasClass('proposals-pipeline')) {
                    proposal_pipeline_open(rel_id)
                } else {
                    init_proposal(rel_id);
                }
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
}

function init_invoices_total(manual) {

    if ($('#invoices_total').length == 0) {
        return;
    }
    var _inv_total_inline = $('.invoices-total-inline');
    var _inv_total_href_manual = $('.invoices-total');

    if ($('body').hasClass('invoices_total_manual') && typeof(manual) == 'undefined' &&
        !_inv_total_href_manual.hasClass('initialized')) {
        return;
    }

    if (_inv_total_inline.length > 0 && _inv_total_href_manual.hasClass('initialized')) {
        // On the next request won't be inline in case of currency change
        // Used on dashboard
        _inv_total_inline.removeClass('invoices-total-inline');
        return;
    }

    _inv_total_href_manual.addClass('initialized');
    var _years = $('._filters._hidden_inputs').find('input[name^="year"]');
    var years = [];
    $.each(_years, function() {
        var _y = $(this).val();
        if (_y != '') {
            years.push(_y);
        }
    });

    var _agents = $('._filters._hidden_inputs').find('input[name^="sale_agent"]');
    var agents = [];
    $.each(_agents, function() {
        var _a = $(this).val();
        if (_a != '') {
            agents.push(_a);
        }
    });

    var _modes = $('._filters._hidden_inputs').find('input[name^="invoice_payments_by_"]');
    var modes = [];
    $.each(_modes, function() {
        var _m = $(this).val();
        if (_m != '') {
            modes.push(_m);
        }
    });

    var currency = $('body').find('select[name="total_currency"]').val();
    var data = {
        currency: currency,
        years: years,
        agents: agents,
        payment_modes: modes,
        init_total: true,
    };
    var project_id = $('input[name="project_id"]').val();
    var customer_id = $('.customer_profile input[name="userid"]').val();
    if (typeof(project_id) != 'undefined') {
        data.project_id = project_id;
    } else if (typeof(customer_id) != 'undefined') {
        data.customer_id = customer_id;
    }
    $.post(admin_url + 'invoices/get_invoices_total', data).done(function(response) {
        $('#invoices_total').html(response);
    });
}

function init_estimates_total(manual) {
    if ($('#estimates_total').length == 0) {
        return;
    }

    if ($('body').hasClass('estimates_total_manual') && typeof(manual) == 'undefined' && !$('.estimates-total').hasClass('initialized')) {
        return;
    }
    $('.estimates-total').addClass('initialized');

    var currency = $('body').find('select[name="total_currency"]').val();
    var _years = $('._filters._hidden_inputs').find('input[name^="year"]');
    var years = [];
    $.each(_years, function() {
        var _y = $(this).val();
        if (_y != '') {
            years.push(_y);
        }
    });
    var _agents = $('._filters._hidden_inputs').find('input[name^="sale_agent"]');
    var agents = [];
    $.each(_agents, function() {
        var _a = $(this).val();
        if (_a != '') {
            agents.push(_a);
        }
    });

    var customer_id = '';
    var project_id = '';

    var _customer_id = $('.customer_profile input[name="userid"]').val();
    var _project_id = $('input[name="project_id"]').val();
    if (typeof(_customer_id) != 'undefined') {
        customer_id = _customer_id;
    } else if (typeof(_project_id) != 'undefined') {
        project_id = _project_id;
    }

    $.post(admin_url + 'estimates/get_estimates_total', {
        currency: currency,
        init_total: true,
        years: years,
        agents: agents,
        customer_id: customer_id,
        project_id: project_id,
    }).done(function(response) {
        $('#estimates_total').html(response);
    });
}

function init_expenses_total() {
    if ($('#expenses_total').length == 0) {
        return;
    }
    var _f
    var currency = $('select[name="expenses_total_currency"]').val();
    var _years = $('._filters._hidden_inputs').find('input[name^="year"]');
    var years = [];
    $.each(_years, function() {
        _f = $(this).val();
        if (_f != '') {
            years.push(_f);
        }
    });
    var _months = $('._filters._hidden_inputs').find('input[name^="expenses_by_month_"]');
    var months = [];
    $.each(_months, function() {
        _f = $(this).val();
        if (_f != '') {
            months.push(_f);
        }
    });
    var _categories = $('._filters._hidden_inputs').find('input[name^="expenses_by_category_"]');
    var categories = [];
    $.each(_categories, function() {
        _f = $(this).val();
        if (_f != '') {
            categories.push(_f);
        }
    });
    var customer_id = '';
    var _customer_id = $('.customer_profile input[name="userid"]').val();
    if (typeof(customer_id) != 'undefined') {
        customer_id = _customer_id;
    }

    var project_id = '';
    var _project_id = $('input[name="project_id"]').val();
    if (typeof(project_id) != 'undefined') {
        project_id = _project_id;
    }

    $.post(admin_url + 'expenses/get_expenses_total', {
        currency: currency,
        init_total: true,
        months: months,
        years: years,
        categories: categories,
        customer_id: customer_id,
        project_id: project_id,
    }).done(function(response) {
        $('#expenses_total').html(response);
    });
}

function validate_invoice_form(selector) {
    if (typeof(selector) == 'undefined') {
        selector = '#invoice-form';
    }
    _validate_form($(selector), {
        clientid: 'required',
        date: 'required',
        currency: 'required',
        number: {
            required: true,
        }
    });
    $('body').find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "invoices/validate_invoice_number",
            type: 'post',
            data: {
                number: function() {
                    return $('input[name="number"]').val();
                },
                isedit: function() {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function() {
                    return $('input[name="number"]').data('original-number');
                },
                date: function() {
                    return $('input[name="date"]').val();
                },
            }
        },
        messages: {
            remote: invoice_number_exists,
        }
    });
}

function validate_estimate_form(selector) {
    if (typeof(selector) == 'undefined') {
        selector = '#estimate-form';
    }
    _validate_form($(selector), {
        clientid: 'required',
        date: 'required',
        currency: 'required',
        number: {
            required: true
        }
    });

    $('body').find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "estimates/validate_estimate_number",
            type: 'post',
            data: {
                number: function() {
                    return $('input[name="number"]').val();
                },
                isedit: function() {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function() {
                    return $('input[name="number"]').data('original-number');
                },
                date: function() {
                    return $('input[name="date"]').val();
                },
            }
        },
        messages: {
            remote: estimate_number_exists,
        }
    });

}
// Sort estimates in the pipeline view / switching sort type by click
function estimates_pipeline_sort(type) {
    var sort = $('input[name="sort"]');
    $('input[name="sort_type"]').val(type);
    if (sort.val() == 'ASC') {
        sort.val('DESC');
    } else if (sort.val() == 'DESC') {
        sort.val('ASC');
    } else {
        sort.val('DESC');
    }
    estimate_pipeline();
}
// Sort proposals in the pipeline view / switching sort type by click
function proposal_pipeline_sort(type) {
    var sort = $('input[name="sort"]');
    $('input[name="sort_type"]').val(type);
    if (sort.val() == 'ASC') {
        sort.val('DESC');
    } else if (sort.val() == 'DESC') {
        sort.val('ASC');
    } else {
        sort.val('DESC');
    }
    proposals_pipeline();
}
// Init estimates pipeline
function estimate_pipeline() {
    init_kanban('estimates/get_pipeline', estimates_pipeline_update, '.pipeline-status', 310, 360);
}

function estimates_pipeline_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var data = {};
        data.estimateid = $(ui.item).data('estimate-id');
        data.status = $(ui.item.parent()[0]).data('status-id');
        var order = [];
        var status = $(ui.item).parents('.pipeline-status').find('li')
        var i = 1;
        $.each(status, function() {
            order.push([$(this).data('estimate-id'), i]);
            i++;
        });
        data.order = order;
        check_kanban_empty_col('[data-estimate-id]');
        $.post(admin_url + 'estimates/update_pipeline', data);
    }
}

function proposals_pipeline_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var data = {};
        data.proposalid = $(ui.item).data('proposal-id');
        data.status = $(ui.item.parent()[0]).data('status-id');
        var order = [];
        var status = $(ui.item).parents('.pipeline-status').find('li')
        var i = 1;
        $.each(status, function() {
            order.push([$(this).data('proposal-id'), i]);
            i++;
        });
        data.order = order;

        check_kanban_empty_col('[data-proposal-id]');
        $.post(admin_url + 'proposals/update_pipeline', data);
    }
}
// Init proposals pipeline
function proposals_pipeline() {
    init_kanban('proposals/get_pipeline', proposals_pipeline_update, '.pipeline-status', 310, 360);
}
// Open single proposal in pipeline
function proposal_pipeline_open(id) {
    if (id == '') {
        return;
    }
    $.get(admin_url + 'proposals/pipeline_open/' + id, function(response) {
        var visible = $('.proposal-pipeline-modal:visible').length;
        $('#proposal').html(response);
        if (visible == 0) {
            $('.proposal-pipeline-modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        } else {
            $('#proposal').find('.modal.proposal-pipeline-modal').addClass('in').css('display', 'block');
        }

    });
}
// Estimate single open in pipeline
function estimate_pipeline_open(id) {
    if (id == '') {
        return;
    }
    $.get(admin_url + 'estimates/pipeline_open/' + id, function(response) {
        var visible = $('.estimate-pipeline:visible').length;
        $('#estimate').html(response);
        if (visible == 0) {
            $('.estimate-pipeline').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        } else {
            $('#estimate').find('.modal.estimate-pipeline').addClass('in').css('display', 'block');
        }
    });
}
// Delete estimate note
function delete_estimate_note(wrapper, id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'estimates/delete_note/' + id, function(response) {
            if (response.success == true) {
                $(wrapper).parents('.estimate-note').remove();
            }
        }, 'json');
    }
}
// Get all estimate notes
function get_estimate_notes(id) {
    $.get(admin_url + 'estimates/get_notes/' + id, function(response) {
        $('#estimate_notes_area').html(response);
    });
}
// Proposal merge field into the editor
function insert_proposal_merge_field(field) {
    var key = $(field).text();
    tinymce.activeEditor.execCommand('mceInsertContent', false, key);
}
// Toggle full view for small tables like proposals
function small_table_full_view() {
    $('#small-table').toggleClass('hide');
    $('.small-table-right-col').toggleClass('col-md-12');
    $('.small-table-right-col').toggleClass('col-md-7');
}

function manage_invoice_items(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($('body').find('.accounting-template').length > 0) {
                var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                var _option = '<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>';
                if (group.length == 0) {
                    _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                    if (item_select.find('[data-group-id="0"]').length == 0) {
                        item_select.find('option:first-child').after(_option);
                    } else {
                        item_select.find('[data-group-id="0"]').after(_option);
                    }
                } else {
                    group.prepend(_option);
                }
                item_select.selectpicker('refresh');
                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload();
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function(data) {
        alert_float('danger', data.responseText);
    });
    return false;
}

function save_sales_number_settings(e) {
    var data = {};
    data.prefix = $('body').find('input[name="s_prefix"]').val();
    if (data.prefix != '') {
        $.post($(e).data('url'), data).done(function(response) {
            response = JSON.parse(response);
            if (response.success && response.message) {
                alert_float('success', response.message);
                $('#prefix').html(data.prefix);
            }
        });
    }
}

function do_prefix_year(date) {
    var date_array;
    if (date.indexOf('.') > -1) {
        date_array = date.split('.');
    } else if (date.indexOf('-') > -1) {
        date_array = date.split('-');
    } else if (date.indexOf('/') > -1) {
        date_array = date.split('/');
    }
    if (typeof(date_array) != 'undefined') {
        $.each(date_array, function(i, string) {
            if (string.length == 4) {
                $('#prefix_year').html(string);
            }
        });
    }
}

function delete_sale_activity(id) {
    var r = confirm(confirm_action_prompt);
    if (r == false) {
        return false;
    } else {
        $.get(admin_url + 'misc/delete_sale_activity/' + id, function() {
            $('body').find('[data-sale-activity-id="' + id + '"]').remove();
        });
    }
}
