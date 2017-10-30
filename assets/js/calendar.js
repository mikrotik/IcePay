// JS File used in events
$(function(){
    validate_calendar_form();
    var settings = {
        customButtons: {},
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,viewFullCalendar'
        },
        editable: false,
        eventLimit: parseInt(calendar_events_limit) + 1,
        views: {
            day: {
                eventLimit: false
            }
        },
        defaultView:default_view_calendar,
        isRTL: (isRTL == 'true' ? true : false),
        eventStartEditable: false,
        timezone:timezone,
        firstDay: parseInt(calendar_first_day),
        year: moment.tz(timezone).format("YYYY"),
        month: moment.tz(timezone).format("M"),
        date: moment.tz(timezone).format("DD"),
        events:calendar_data,
        eventSources:[],
        eventLimitClick:function(cellInfo, jsEvent){
            $('#calendar').fullCalendar('gotoDate', cellInfo.date);
            $('#calendar').fullCalendar('changeView','basicDay');
        },
        eventRender: function(event, element) {
            element.attr('title', event._tooltip);
            element.attr('onclick', event.onclick);
            element.attr('data-toggle', 'tooltip');
            if (!event.url) {
                element.click(function() {
                    view_event(event.eventid);
                });
            }
        },
        dayClick: function(date, jsEvent, view) {
            $('#newEventModal').modal('show');
            $.post(admin_url+'misc/format_date',{date:moment(date.format()).locale('en').format('YYYY-MM-DD')}).done(function(formated){
                $("input[name='start'].datetimepicker").val(formated);
            });
            return false;
        },
    }
    if ($('body').hasClass('home')) {
        settings.customButtons.viewFullCalendar = {
            text: calendar_expand,
            click: function() {
                window.location.href = admin_url + 'utilities/calendar';
            }
        }
    }
    if(is_staff_member == 1){
        if (google_api != '') {
            settings.googleCalendarApiKey = google_api;
        }
        if (calendarIDs != '') {
            calendarIDs = JSON.parse(calendarIDs);
            if (calendarIDs.length != 0) {
                if (google_api != '') {
                    for (var i = 0; i < calendarIDs.length; i++) {
                        var _gcal = {};
                        _gcal.googleCalendarId = calendarIDs[i];
                        settings.eventSources.push(_gcal);
                    }
                } else {
                    console.error('You have setup Google Calendar IDs but you dont have specified Google API key. To setup Google API key navigate to Setup->Settings->Misc->Misc');
                }
            }
        }
    }

    // Init calendar
    $('#calendar').fullCalendar(settings);
    var new_event = get_url_param('new_event');
    if(new_event){
     $('#newEventModal').modal('show');
     $("input[name='start'].datetimepicker").val(get_url_param('date'));
 }
});

function view_event(id) {
    if (typeof(id) == 'undefined') {
        return;
    }
    $.post(admin_url + 'utilities/view_event/' + id).done(function(response) {
        $('#event').html(response);
        $('#viewEvent').modal('show');
        init_datepicker();
        validate_calendar_form();
    });
}

function delete_event(id) {
    $.get(admin_url + 'utilities/delete_event/' + id, function(response) {
        window.location.reload();
    }, 'json');
}

function validate_calendar_form() {
    _validate_form('._event form', {
        title: 'required',
        start: 'required'
    }, calendar_form_handler);
}

function calendar_form_handler(form) {
    $.post(form.action, $(form).serialize()).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
            setTimeout(function() {
                var location = window.location.href;
                location = location.split('?');
                window.location.href = location[0];
            }, 700);
        }
    });

    return false;
}
