var firstday = '';
var lastday = '';
var currDate = moment()._d;
var calendarMain = null;
var view = "dayGridMonth"
var calendarSidebar = null
var ClassesSession = [];
var ClassesForFilter = [];
var studioClassSetting = [];
var locations = '';
var owner = '';
var title = '';
var tasks = 1;
var init = true;
var SaveFilter = null;
var direction = "ltr";
var resources = false;
var branch_id = 0;
var headerToolbar;
var cal_data;
var js_last_window_height;
var jsSplitView;
var jsTypeOfView;
var jsMobileView;
var jsMobileSplitView;
var jsMobileTypeOfView;
var locales = 'en';
var nowIndicator;

let tooltipInstance;

function loadCalendar(data) {

    calendarMainEl = document.getElementById('calendar-main');

    let doubleClick
    let clickTimer
    var start = 'prev next title';
    headerToolbar = {
        left: 'title prev next ',
        center: '',
        right: ''
    };
    if ($("html").attr("dir") == "rtl") {
        direction = 'rtl';
        start = 'prev next';
        locales = 'he';
    }
    var scrollTime = moment().format("HH:mm:ss");
    var calendarOptions = {
        initialView: view,
        schedulerLicenseKey: '0056382838-fcs-1615122005',
        loading: function (bool) {
            if (!bool) {
                if ($('.select2-calendar-select.select2-hidden-accessible').length == 0) {
                    calendarViewSwitch();
                }
            }
        },
        fixedWeekCount: false,
        contentHeight: 'auto',
        initialDate: currDate,
        direction: direction,
        locale: locales,
        //scrollTime: scrollTime,
        titleFormat: {
            year: 'numeric',
            month: 'short'
        },
        /*headerToolbar: {
         //start: start,
         //center: 'title',
         //end: 'today'
         },*/
        headerToolbar: false,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        },
        slotDuration: '01:00:00',
        slotMinTime: jsTimeFrom,
        slotMaxTime: jsTimeTo,
        events: data,
        eventDisplay: 'block',
        eventClick: function (arg) {
            const isMeeting = meetingDetailsModule.isMeetingType(arg.event.extendedProps.type);
            const isTask = (arg.event.extendedProps.type == 2);
            if (isMeeting) {
                if ($(arg.jsEvent.target).hasClass('js--open-client-profile')) {
                    return false;
                }
                meetingDetailsModule.eventClick('/office/ajax/MeetingDetails.php', arg.event.id, arg.event.extendedProps.status);
            } else if (arg.event.extendedProps.maxMembers == 0) {
                const $popup = $("#js-block-event-popup");
                $popup.modal("show").showModalLoader();

                $.ajax({
                    url: 'new-block-event.php?option=edit&id=' + arg.event.id,
                    method: 'GET',
                    success: function (res) {
                        $popup.find('.modal-content').html(res);
                    }
                });
            } else if (isTask) {
                handleNewTask(arg.event.id);
            } else {
                $("#js-char-popup").modal("show");
                $("#js-char-popup-content").html($(".js-char-shimming-loader").html());

                $.ajax({
                    url: '/office/characteristics-popup.php?id=' + arg.event.id,
                    type: 'GET',
                    success: function (response) {
                        var jsonObj = $.parseJSON(response);
                        $("#js-char-popup-content").html(jsonObj.js_char_popup_content);
                        $("#js-modal-device-add .modal-body").html(jsonObj.js_modal_device_add);
                    },
                    error: function (response) {
                        /* showing error :: begin **/
                        charPopup.showError($("#js-char-popup-content"), "An error occurred. Please try again", "Something went wrong", false);
                        /* showing error :: end **/
                    }
                });
            }
        },
        eventContent: function (arg) {
            const isMeeting = meetingDetailsModule.isMeetingType(arg.event.extendedProps.type);
            const displayNodes = isMeeting ? meetingDetailsModule.loadCalenderBox(arg, view) : lessonCreatedContent(arg);
            return {domNodes: displayNodes};
        },
        eventClassNames: function (arg) {
            //var eventClasses = 'color-' + arg.event.extendedProps.eColor;
            const currentStatus = arg.event.extendedProps.status;
            var eventClasses = 'color-' + arg.event.backgroundColor;

            const isMeeting = meetingDetailsModule.isMeetingType(arg.event.extendedProps.type);
            const isTask = (arg.event.extendedProps.type == 2);

            if (isMeeting) {
                eventClasses += " meeting--event-type meeting--event-type_" + arg.event.id;
            } else if (arg.event.extendedProps.maxMembers == 0) {
                eventClasses += " block--event-type block--event-type_" + arg.event.id;
            } else if (isTask) {
                eventClasses += " task--event-type task--event-type_" + arg.event.id;
            } else {
                eventClasses += " lesson--event-type";
            }

            if (isMeeting) {
                if (meetingDetailsModule.isGrey(currentStatus)) {
                    eventClasses += ' event--status-grey';
                } else if (meetingDetailsModule.isGradientBg(currentStatus)) {
                    eventClasses += ' event--status-gradient';
                }
            } else {
                if (arg.event.extendedProps.isCancelled) {
                    eventClasses += ' event-cancelled';
                } else if (currentStatus == 1 && !isTask) {
                    eventClasses += '  event-completed';
                }
            }

            return [eventClasses]
        },
        dayMaxEvents: true,
        dateClick: function (info) {
            let singleClick = info.date.format();
            if(doubleClick==singleClick){
                let callback = function (){
                    fieldEvents.meetingActions.show()
                    $('.datepicker').datepicker('setDate', new Date(info.date.format('yyyy-mm-dd'))).trigger('change')
                    if (info.view.type != "dayGridMonth") $('.js-schedule-select').val(info.date.format('HH:MM')).trigger('change')
                    fieldEvents.meetingActions.init()
                    fieldEvents.classActions.init()
                }
                if (info.resource) {
                    callback = function (){
                        fieldEvents.meetingActions.show()
                        $('.datepicker').datepicker('setDate', new Date(info.date.format('yyyy-mm-dd'))).trigger('change')
                        if (info.view.type != "dayGridMonth") $('.js-schedule-select').val(info.date.format('HH:MM')).trigger('change') //todo: update meeting start time
                        $(`[name="${info.resource.extendedProps.type}"][value="${info.resource.id}"]`).prop('checked', true)
                        fieldEvents.meetingActions.init()
                        fieldEvents.classActions.init()
                    }
                }
                OpenClassPopup(null, 0, callback)
                doubleClick = null;
            } else {
                doubleClick= info.date.format();
                clearInterval(clickTimer);
                clickTimer = setInterval(function(){
                    doubleClick = null;
                    clearInterval(clickTimer);
                }, 500);
            }
        },
        views: {
            month: {// MONTHLY VIEW
                dayMaxEvents: 5,
                eventContent: function (arg) {
                    var displayNodes;
                    const isMeeting = meetingDetailsModule.isMeetingType(arg.event.extendedProps.type);
                    const isTask = (arg.event.extendedProps.type == 2);

                    if (isMeeting) {
                        //displayNodes = meetingCreatedContent(arg, 'month');
                        displayNodes = meetingDetailsModule.loadCalenderBox(arg, 'month');

                    } else {
                        var eTitle = document.createElement('h5'),
                            eParticipants = document.createElement('div'),
                            currentParticipants = arg.event.extendedProps.members,
                            eUserIcon = '',
                            ePause = document.createElement('div');
                        eTitle.innerHTML = arg.event.title;
                        eTitle.className = 'text-truncate ' + (isTask || arg.event.extendedProps.maxMembers == 0 ? 'text-black' : 'text-white');
                        eTitle.style.width = "75px";
                        eTitle.setAttribute('data-group-number', arg.event.extendedProps.groupNumber);
                        eTitle.setAttribute('data-class-id', arg.event.id);
                        arg.event.extendedProps.minMembers > currentParticipants ? eUserIcon = '<i class="fal fa-user-check"></i>' : eUserIcon = '<i class="fal fa-user-minus"></i>';
                        eParticipants.className = ' bsapp-event-participants';
                        eParticipants.innerHTML = '<span class="badge">' + `<span id="client-reg-card${arg.event.id}">${currentParticipants}</span>` + '/' + arg.event.extendedProps.maxMembers + eUserIcon + '</span>';
                        if (isTask || arg.event.extendedProps.maxMembers == 0) {
                            eParticipants.innerHTML = '';
                        }
                        displayNodes = [eTitle];
                        // displayNodes.push(eParticipants);
                        // Event States (Completed / Cancelled)
                        if (arg.event.extendedProps.status == 1 && (!arg.event.extendedProps.isCancelled)) {
                            ePause.innerHTML = '';
                            ePause.style.display = "none";
                            ePause.className = 'bsapp-event-state ';
                            // displayNodes.push(ePause);
                        } else if (arg.event.extendedProps.isCancelled) {
                            eTitle.className = 'text-truncate text-dark';
                            ePause.innerHTML = lang('canceled');
                            ePause.className = 'bsapp-event-state ';
                            eParticipants.innerHTML = '';
                            // eParticipants.innerHTML = '<span class="badge">' + currentParticipants + eUserIcon + '</span>';
                            //displayNodes.push(ePause);
                        }

                        var IconsData = document.createElement('div')
                        IconsData.appendChild(eParticipants);
                        IconsData.appendChild(ePause);
                        IconsData.className = 'fc-event-main px-0 text-end';
                        IconsData.style.paddingLeft = "0px";
                        IconsData.style.paddingRight = "0px";
                        displayNodes.push(IconsData)
                    }

                    return {domNodes: displayNodes};
                }
            },
            week: {
                allDaySlot: false,
                slotEventOverlap: false,
                nowIndicator: nowIndicator
            },
            day: {
                allDaySlot: false,
                dayHeaderContent: (args) => {
                    return moment(args.date).format('dddd Do')
                },
                slotEventOverlap: false,
                nowIndicator: nowIndicator,
                nowIndicatorContent: function () {
                    return moment().format('HH:mm');
                }
            },
            timeGridThreeDay: {
                allDaySlot: false,
                type: 'timeGrid',
                duration: {days: 3},
                slotEventOverlap: false,
                nowIndicator: nowIndicator
            },
            resource_timeGridThreeDay: {
                allDaySlot: false,
                type: 'resourceTimeGrid',
                duration: {days: 3},
                slotEventOverlap: false,
                nowIndicator: nowIndicator,
                buttonText: '3 days'
            },
            resource_timeGridDay: {
                allDaySlot: false,
                dayHeaderContent: (args) => {
                    return moment(args.date).format('dddd Do')
                },
                slotEventOverlap: false,
                nowIndicator: nowIndicator,
                nowIndicatorContent: function () {
                    return moment().format('HH:mm');
                },
                type: 'resourceTimeGrid',
                duration: {days: 1},
                buttonText: ''
            },
            resource_timeGridWeek: {
                allDaySlot: false,
                type: 'resourceTimeGrid',
                slotEventOverlap: false,
                duration: {week: true},
                nowIndicator: nowIndicator
            }
        },
        eventMouseLeave: function(mouseEnterInfo) {
            const {event, el, jsEvent, view} = mouseEnterInfo;
            const isMeeting = meetingDetailsModule.isMeetingType(event.extendedProps.type);
            if (isMeeting) {
                const parentCalEl = $(el).parent();
                if (parentCalEl) {
                    // return inherit z-index value
                    const ind = +(parentCalEl.css('z-index')) || 'auto';
                    parentCalEl.css('z-index', ind === 'auto' ? ind : ind - 10);
                }
            }
        },
        eventMouseEnter: function(mouseEnterInfo) {
            const {event, el, jsEvent, view} = mouseEnterInfo;
            const isMeeting = meetingDetailsModule.isMeetingType(event.extendedProps.type);
            if (isMeeting) {
                const eTooltipEl = $(el).find('.eTooltip');
                const eTooltipHeightHalf = eTooltipEl.innerHeight() / 2;
                const elRect = el.getBoundingClientRect();
                const toLeft = document.dir === 'ltr';
                eTooltipEl.css({
                    left: toLeft ? (elRect.x - eTooltipEl.innerWidth()) : (elRect.x + elRect.width) + 'px',
                    top: elRect.y + (elRect.height / 2) - eTooltipHeightHalf + 'px'
                }).addClass(toLeft ? 'left' : 'right');
                const parentCalEl = $(el).parent();
                if (parentCalEl) {
                    const ind = +(parentCalEl.css('z-index')) || 0;
                    parentCalEl.css('z-index', ind + 10);
                }
            }
        }
    };

    if ($(window).width() > 768) {
        if (resources != false) {
            if (view == 'dayGridMonth') {
                calendarOptions['initialView'] = view;
                calendarOptions['resources'] = false;
            } else {
                calendarOptions['initialView'] = 'resource_' + view;
                calendarOptions['resources'] = resources;
                calendarOptions['datesAboveResources'] = true;
            }
        }
    } else {
        calendarOptions['initialView'] = jsMobileView;

        if (resources != false) {
            if (jsMobileView == 'timeGridDay') {
                calendarOptions['initialView'] = 'resource_' + jsMobileView;
                calendarOptions['resources'] = resources;
                calendarOptions['datesAboveResources'] = true;
                //$('[name="js_sm_calendar_settings"]').prop("checked", false);
                //$("#js-sm-daily-view").prop("checked", true);
                $("[name='js_sm_split_settings'][value='" + jsMobileSplitView + "']").prop("checked", true)
            } else {
                calendarOptions['initialView'] = jsMobileView;
                calendarOptions['resources'] = false;

                //$('[name="js_sm_calendar_settings"]').prop("checked", false);
                //$("[name='js_sm_split_settings']").prop("checked", false);
                if (jsMobileView == 'timeGridWeek') {
                    $("#js-sm-weekly-view").prop("checked", true);
                }
                if (jsMobileView == 'dayGridMonth') {
                    $("#js-sm-monthly-view").prop("checked", true);
                }
                if (jsMobileView == 'timeGridThreeDay') {
                    $("#js-sm-3day-view").prop("checked", true);
                }
            }
        }
    }
    calendarMain = new FullCalendar.Calendar(calendarMainEl, calendarOptions);
    calendarMain.render();

    // hide only end for split meetings
    const splitEnd = $(calendarMainEl).find(".fc-timegrid-event.fc-event-end.meeting--event-type").not('.fc-event-start');
    splitEnd.each(function () {
        this.parentNode.classList.add("d-none");
    });

    if (resources != false) {
        if (view == "timeGridDay") {
            $(".js-resource-day-heading").remove();
            var js_colspan = $("th.fc-resource").length;
            var js_formatted_day = moment(currDate).format("dddd Do");
            $("th.fc-resource:first").parent("tr").before('<tr class="js-resource-day-heading"><th class="fc-timegrid-axis"><div class="fc-timegrid-axis-frame"></div></th><th class="fc-col-header-cell" colspan="' + js_colspan + '"  ><div class="fc-scrollgrid-sync-inner"><span class="fc-col-header-cell-cushion">' + js_formatted_day + '</span></div></th></tr>');
        } else {
            if ($(".js-resource-day-heading").length > 0) {
                $(".js-resource-day-heading").remove();
            }
        }

    }
    calendarSidebar = new FullCalendar.Calendar(document.getElementById("js-calendar-sm"), {
        initialView: 'dayGridMonth',
        schedulerLicenseKey: '0056382838-fcs-1615122005',
        contentHeight: 'auto',
        direction: direction,
        locale: locales,
        initialDate: currDate,
        headerToolbar: headerToolbar,
        views: {
            dayGridMonth: {

                duration: {months: 1}
            }
        },
        dayHeaderFormat: {
            weekday: 'narrow',
        },
        dateClick: function (info) {
            currDate = info.date;
            SetDate()
            GetCalendarData();
        }
    });
    calendarSidebar.render();
    $(calendarSidebar.el).find('[data-date]').removeClass("bsapp-js-active-date");
    $(calendarSidebar.el).find('[data-date="' + moment(currDate).format("YYYY-MM-DD") + '"]').addClass("bsapp-js-active-date");
    $("#js-dynamic-styles").html('[data-date="' + moment(currDate).format("YYYY-MM-DD") + '"] .fc-daygrid-day-number{ background:var(--primary);width:24px;height:24px;display:flex;justify-content:center;align-items:center;border-radius:50%;color:white !important;}');
    /* calendarMainEl.querySelector('.fc-today-button').addEventListener('click', function () {
     currDate = new Date(calendarMain.getDate())
     SetDate()
     GetCalendarData()
     });
     */
    var date_range = moment(firstday).format("MMM dd") != moment(lastday).format("MMM dd") ? moment(firstday).format("MMM DD") + ' - ' + moment(lastday).format("MMM DD") : moment(firstday).format("MMM DD");

    $("#js-modal-show-calendar span").html(date_range);
    $("#js-calendar-date-range span").html(date_range);
    if (direction == 'rtl') {
        $("#js-calendar-sm").find(".fc-toolbar-chunk:not(:first)").attr("style", 'display:none !important;');
    } else {
        $("#js-calendar-sm").find(".fc-toolbar-chunk:not(:first)").attr("style", 'display:none !important;');
    }
    if ($(window).width() < 768) {
        if (moment(currDate).format("YYYY-MM-DD") != moment().format("YYYY-MM-DD")) {
            $(".js-back-to-today").show();
        } else {
            $(".js-back-to-today").hide();
        }
    } else {
        $(".js-back-to-today").hide();
    }
    //setTimeout(function () {
    adjustScrollHeight();
    //}, 500);
}

function lessonCreatedContent(arg) {
    const isTask = (arg.event.extendedProps.type == 2);
    var eTopContent = document.createElement('div'),
        eGradContent = document.createElement('div'),
        eGradBottom = document.createElement('div'),
        eTitle = document.createElement('h5'),
        eOwner = document.createElement('span'),
        eStartTime = moment(arg.event.start).format('HH:mm'),
        eEndTime = moment(arg.event.end).format('HH:mm'),
        eTimeContainer = document.createElement('span'),
        eParticipants = document.createElement('div'),
        currentParticipants = arg.event.extendedProps.members,
        eInfo = document.createElement('div'),
        eParticipantsDiv = document.createElement('div'),
        eParticipantsNames = document.createElement('div'),
        eIcons = document.createElement('div'),
        ePause = document.createElement('div'),
        eUserIcon = '';
    eTopContent.className = 'd-flex flex-column pis-' + (arg.event.extendedProps.maxMembers == 0 ? "15" : "9");
    eTitle.innerHTML = arg.event.title;
    eTitle.className = 'bsapp-text-overflow';
    eTitle.style.width = ' calc( 100% - 1px ) ';
    eTitle.setAttribute('data-group-number', arg.event.extendedProps.groupNumber);
    eTitle.setAttribute('data-class-id', arg.event.id);
    if (isTask) {
        const ePriority = document.createElement('span');
        ePriority.classList.add('text-danger', 'font-weight-bold', 'pis-5');
        ePriority.textContent = '!!!'.substring(0, parseInt(arg.event.extendedProps.priority) + 1);
        eTitle.appendChild(ePriority);
    }
    eOwner.className = 'mt-0 bsapp-event-owner bsapp-text-overflow bsapp-fs-14';
    eOwner.style.width = ' calc( 100% - 1px ) ';
    eOwner.innerHTML = arg.event.extendedProps.owner;
    if (arg.event.extendedProps.ExtraGuideId != 0) {
        eOwner.innerHTML += ' +';
    }
    eOwner.innerHTML = (jsTypeOfView == 2 && jsSplitView == 0) ? '' : eOwner.innerHTML;
    arg.event.extendedProps.minMembers > currentParticipants ? eUserIcon = '<i class="fal fa-user-check bsapp-fs-16"></i>' : eUserIcon = '<i class="fal fa-user bsapp-fs-16"></i>';
    eParticipants.className = ' bsapp-event-participants pie-9';
    // eParticipants.innerHTML = '<span class="badge">' + '<span id="client-reg-card'+ arg.event.id+'">'+ currentParticipants + '</span>' + '/' + '<span>'+arg.event.extendedProps.maxMembers + '</span><span>' + eUserIcon + '</span></span>';
    eParticipants.innerHTML =
        '<span class=""><span>' + eUserIcon + '<span id="client-reg-card' + arg.event.id + '" class="bsapp-fs-14"> ' + ' ' + currentParticipants + '</span>'+ '/' +'<span id="client-max-card' + arg.event.id + '">' + arg.event.extendedProps.maxMembers + '</span></span></span>';
    eInfo.className = 'pie-9 d-flex justify-content-between w-100 align-items-center';
    eIcons.className = 'bsapp-event-icons bsapp-text-overflow ';
    if ($(window).width() > 578) {
        eGradContent.className = ' bsapp-grad-end ';
        eGradContent.style.color = arg.event.backgroundColor;
        eGradBottom.className = ' bsapp-grad-bottom ';
        eGradBottom.style.color = arg.event.backgroundColor;
    }

    // Event Info Icons
    if (arg.event.extendedProps.isHidden) {
        //if (1 == 1) {
        var eIconHidden = document.createElement('i');
        eIconHidden.classList.add('far', 'fa-eye-slash', 'mis-5', 'bsapp-fs-16');
        eIcons.appendChild(eIconHidden)
    }

    if (arg.event.extendedProps.isAlarm) {
        //if (1 == 1) {
        var eIconAlarm = document.createElement('i');
        eIconAlarm.classList.add('fal', 'fa-alarm-clock', 'mis-5', 'bsapp-fs-16');
        eIcons.appendChild(eIconAlarm)
    }

    if ((parseInt(arg.event.extendedProps.regularMembersCount) >= parseInt(arg.event.extendedProps.maxMembers)) && (parseInt(arg.event.extendedProps.regularMembersCount) > 0)) {
        var eIconGuaranteed = document.createElement('i');
        eIconGuaranteed.classList.add('far', 'fa-shield-check', 'mis-5', 'bsapp-fs-16');
        eIcons.appendChild(eIconGuaranteed)
    }
    if (arg.event.extendedProps.liveClass) {
        var eIconGuaranteed = document.createElement('i');
        eIconGuaranteed.classList.add('far', 'fa-video', 'mis-5', 'bsapp-fs-16');
        eIcons.appendChild(eIconGuaranteed);
    } else if (arg.event.extendedProps.is_zoom_class) {
        var eIconGuaranteed = document.createElement('i');
        eIconGuaranteed.classList.add('far', 'fa-play-circle', 'mis-5', 'bsapp-fs-16');
        eIcons.appendChild(eIconGuaranteed);
    }
    /*if ((arg.event.extendedProps.minMembers <= arg.event.extendedProps.regularMembers) && (arg.event.extendedProps.regularMembers != 0)) {
	 var eIconGuaranteed = document.createElement('i');
	 eIconGuaranteed.classList.add('far', 'fa-shield-check', 'mie-4', 'bsapp-fs-16', 'text-white');
	 eIcons.appendChild(eIconGuaranteed)
	 }*/

    // Event Participants Waiting list

    //if (1 == 1) {
    if (parseInt(arg.event.extendedProps.waitingCount) > 0) {
        var eIconPause = document.createElement('i');
        eIconPause.classList.add('far', 'fa-pause-circle', 'bsapp-fs-16', 'mie-4');
        ePause.className = ' bsapp-event-pause  bsapp-min-w-50p d-flex align-items-center text-white';
        ePause.innerHTML = ' <span id="client-waiting-card' + arg.event.id + '"  class="bsapp-fs-14" > ' + arg.event.extendedProps.waitingCount + '</span>';

        ePause.prepend(eIconPause);
    } else {
        var eCounterPause = document.createElement('span');
        eCounterPause.id = `client-waiting-card${arg.event.id}`;
        ePause.append(eCounterPause);
    }

    // Event States (Completed / Cancelled)
    if (arg.event.extendedProps.isCancelled) {
        ePause.innerHTML = '<span class="text-danger bsapp-fs-14 font-weight-bold">' + lang('canceled') + '</span>';
        ePause.className = 'bsapp-event-pause  bsapp-min-w-50p d-flex align-items-center text-danger';
        eIcons.className = 'bsapp-event-icons bsapp-text-overflow text-danger d-flex justify-content-end ';
        //eParticipants.innerHTML = '<span class="badge">' + currentParticipants + eUserIcon + '</span>';
        //eParticipants.innerHTML = currentParticipants + eUserIcon;
        eParticipants.style.opacity = '0';
        eGradContent.style.color = ' transparent ';
        eGradBottom.style.color = ' transparent ';
    } else if (!isTask && arg.event.extendedProps.status == 1) {
        ePause.innerHTML = '<span class="text-dark bsapp-fs-14 font-weight-bold">' + lang('completed_client_profile') + '</span>';
        ePause.className = 'bsapp-event-pause  bsapp-min-w-50p d-flex align-items-center text-dark '
        eGradContent.style.color = ' transparent ';
        eGradBottom.style.color = ' transparent ';
        eIcons.className = 'bsapp-event-icons bsapp-text-overflow text-dark d-flex justify-content-end ';
    } else {
        eTopContent.className = ' text-' + (isTask || arg.event.extendedProps.maxMembers == 0 ? "black" : "white") + ' d-flex flex-column pis-' + (arg.event.extendedProps.maxMembers == 0 ? "15" : "9");
        // eInfo.style.opacity = "0.5";
        ePause.className = 'bsapp-event-pause  bsapp-min-w-50p d-flex align-items-center text-white '
        eIcons.className = 'bsapp-event-icons bsapp-text-overflow text-white d-flex justify-content-end ';
    }

    eIcons.style.width = ' calc( 100% - 30px )';

    if (isTask || arg.event.extendedProps.maxMembers == 0) {
        eParticipants.innerHTML = '';
        eGradBottom.style.color = ' transparent ';
    }

    // if (parseInt(arg.event.extendedProps.waitingCount) <= 0) {
    //     ePause.classList.remove('d-flex');
    //     ePause.classList.add('d-none');
    // }
    ePause.classList.add('ePause');
    eIcons.classList.add('eIcons');
    eParticipants.classList.add('eParticipants');
    eGradContent.classList.add('eGradContent');
    eGradBottom.classList.add('eGradBottom');

    const eGradBottomStatus = document.createElement('div');
    const eGradBottomSpan = document.createElement('span');
    if (isTask) {
        eGradBottomStatus.classList.add('bsapp-status-tag');
        eGradBottomStatus.style.color = 'white';
        eGradBottomStatus.style.backgroundColor = arg.event.extendedProps.statusColor;
        eGradBottomSpan.textContent = arg.event.extendedProps.statusName;
        eGradBottomStatus.appendChild(eGradBottomSpan);
        eGradBottom.appendChild(eGradBottomStatus);
    }

    if (arg.event.extendedProps.maxMembers == 1 && arg.event.extendedProps.members == 1) {
        for (name of arg.event.extendedProps.membersNames) {
            let nameDiv = document.createElement('div');
            nameDiv.innerHTML = `<i class="fal fa-user bsapp-fs-16"></i> ${name}`;
            nameDiv.className = 'pie-9 bsapp-fs-15 text-truncate';
            eParticipants.innerHTML = "";
            eParticipants.appendChild(nameDiv)
        }
    }

    eInfo.append(ePause, eIcons);
    eTimeContainer.className = 'bsapp-event-times';
    eTimeContainer.innerHTML = eStartTime + '-' + eEndTime;

    if (arg.event.extendedProps.maxMembers == 0) {
        const eGradContentLine = document.createElement('div');

        eGradContentLine.classList.add('bsapp-grad-right');
        eGradContentLine.classList.add('eGradContent');

        eTopContent.appendChild(eGradContentLine);
    }

    eTopContent.appendChild(eTitle);
    eTopContent.appendChild(eTimeContainer);
    eTopContent.appendChild(eOwner);
    eTopContent.appendChild(eParticipants);
    if (arg.event.extendedProps.maxMembers != 0) {
        eTopContent.appendChild(eInfo);
    }
    eTopContent.appendChild(eGradContent);

    return [eTopContent, eGradBottom];
}

function setUpSidebarCalendars(headerToolbar, direction, currDate) {
    if ($("#js-modal-calendar-selector").hasClass("js-scrollcal-initialised") == false) {
        if (direction == "rtl") {
            $(".js-calendar-heading-for-rtl").show();
            $(".js-calendar-heading-for-ltr").hide();
        } else {
            $(".js-calendar-heading-for-rtl").hide();
            $(".js-calendar-heading-for-ltr").show();
        }
        for (let i = 3; i > -3; i--) {
            var js_id = "cal-" + new Date().getTime();
            var now = currDate;
            var currdate = moment().subtract(i, 'months');
            if (i > 0) {
                var html = '<div class="mb-30 js-sm-calendar js-prev-months  d-md-none" id="' + js_id + '"   data-time="' + currdate._d.getTime() + '"></div>';
            } else {
                if (i == 0) {
                    var html = '<div class="mb-30 js-sm-calendar  js-curr-month " id="' + js_id + '"   data-time="' + currdate._d.getTime() + '" ></div>';
                } else {
                    var html = '<div class="mb-30 js-sm-calendar   js-next-months d-md-none" id="' + js_id + '"   data-time="' + currdate._d.getTime() + '" ></div>';
                }
            }
            $(".js-swiper-calendar").append(html);
            createSidebarCalendar(js_id, currdate._d, headerToolbar, direction);
        }
        $("#js-modal-calendar-selector").addClass("js-scrollcal-initialised");
    }
}

let calendarSidebar2;
function createSidebarCalendar(js_id, curr_date, headerToolbar, direction) {

    calendarSidebar2 = new FullCalendar.Calendar($(".js-calendar-copy .js-calendar-copy-id")[0], {
        initialView: 'dayGridMonth',
        schedulerLicenseKey: '0056382838-fcs-1615122005',
        fixedWeekCount: false,
        contentHeight: 'auto',
        direction: direction,
        initialDate: curr_date,
        headerToolbar: headerToolbar,
        views: {
            dayGridMonth: {
                fixedWeekCount: false,
                duration: {months: 1}
            }
        }
    });
    calendarSidebar2.render();
    calendarSidebar2 = null;
    if (direction == 'rtl') {
        $(".js-calendar-copy  .js-calendar-copy-id").find(".fc-toolbar-chunk:not(:first)").attr("style", 'display:none !important;');
    } else {
        $(".js-calendar-copy  .js-calendar-copy-id").find(".fc-toolbar-chunk:not(:last)").attr("style", 'display:none !important;');
    }
    $("#" + js_id).html($(".js-calendar-copy").html());

}
function populateFilters(data, FilterState) {
    if (Object.values(data).flat().length == 0){
        $('#calendarFilters-all').addClass('d-none');
        $('#calendarFilters-none').removeClass('d-none').closest('.bsapp-calendar-sidebar').addClass('calendar--filters-none');

    } else {
        $('#calendarFilters-all').removeClass('d-none');
        $('#calendarFilters-none').addClass('d-none').closest('.bsapp-calendar-sidebar').removeClass('calendar--filters-none');
    }


    var filters = ['location', 'owner', 'title'];
    if (FilterState != null) {
        var locationCheck = FilterState.Locations.split(",")
        var ownerCheck = FilterState.Coaches.split(",")
        var titleCheck = FilterState.Classes.split(",")
        var checked = false;
    } else {
        var checked = true;
    }

    var f_key;
    var f_key_id;
    var f_key_title;
    var filter_type;
    var branch_data = '';
    for (var i = 0; i < filters.length; i++) {
        var key = filters[i];
        if (key == 'title') {
            f_key = 'classesTypes';
            f_key_id = 'id';
            f_key_title = 'Type';
            filter_type = data[f_key];
        } else if (key == 'owner') {
            f_key = 'coaches';
            f_key_id = 'id';
            f_key_title = 'display_name';
            filter_type = data[f_key];
        } else if (key == 'location') {
            f_key = 'locations';
            f_key_id = 'id';
            f_key_title = 'title';
            filter_type = data[f_key];
            branch_data = ' filter_type[index]["brandId"] ';
        }


        $('#calendarFilters-' + filters[i] + ' ul li').remove();
        for (var index = 0; index < filter_type.length; index++) {
            if (FilterState != null) {
                switch (filters[i]) {
                    case "location":
                        if (!locationCheck.includes(filter_type[index][f_key_id]))
                            checked = true;
                        break;
                    case "owner":
                        if (!ownerCheck.includes(filter_type[index][f_key_id]))
                            checked = true;
                        break;
                    case "title":
                        if (!titleCheck.includes(filter_type[index][f_key_id]))
                            checked = true;
                        break;
                }
            }

            var markup = '<li><div class="custom-control custom-checkbox"  data-branch-id = "' + eval(branch_data) + '"><input  type="checkbox" class="fillter-check custom-control-input" value="' + filter_type[index]['title'] + '"  id="js-filter-check-' + key + '-' + filter_type[index][f_key_id] + '" data-id="' + filter_type[index][f_key_id] + '" data-type="' + filters[i] + '"  '

            if (checked) {
                markup += 'checked> <label  class="custom-control-label pt-5"  for="js-filter-check-' + key + '-' + filter_type[index][f_key_id] + '"  >' + filter_type[index][f_key_title] + '</label></div></li>';
            } else {
                markup += '> <label  class="custom-control-label pt-5"  for="js-filter-check-' + key + '-' + filter_type[index][f_key_id] + '">' + filter_type[index][f_key_title] + '</label></div></li>';
            }

            $('#calendarFilters-' + filters[i] + ' ul').append(markup);
            if (FilterState != null) {
                checked = false;
            }

        }
    }
}

function calendarViewSwitch() {
    if ($("html").attr("dir") == 'rtl') {
        $('#calendar-main .fc-prev-button').before('<div class="w-150p mx-auto"><select class="select2-calendar-select" name="calendar-view-select" required><option value="3" data-view="timeGridDay">Daily</option><option value="4" data-view="timeGridThreeDay">3 Days</option><option value="2" data-view="timeGridWeek">Weekly</option><option value="1" data-view="dayGridMonth" selected>Monthly</option></select></div>');
    } else {
        $('#calendar-main .fc-prev-button').before('<div class="w-150p mx-auto"><select class="select2-calendar-select" name="calendar-view-select" required><option value="3" data-view="timeGridDay">Daily</option><option value="4" data-view="timeGridThreeDay">3 Days</option><option value="2" data-view="timeGridWeek">Weekly</option><option value="1" data-view="dayGridMonth" selected>Monthly</option></select></div>');
    }

    var sel_val = view;

    if (view.indexOf("resource_") != -1) {
        sel_val = view.substr(9);
    }

    var val = $(".select2-calendar-select [data-view=" + sel_val + "]").val()

    $(".select2-calendar-select").val(val);
    $('.select2-calendar-select').select2({
        minimumResultsForSearch: -1,
        theme: "bsapp-dropdown bsapp-outline-gray-300"
    });
}


function uniqueFilters(data, key) {
    var flags = [],
        output = [];
    for (var i = 0; i < data.length; i++) {
        if (flags[data[i][key]])
            continue;
        flags[data[i][key]] = true;
        output.push(data[i][key]);
    }
    return output;
}
$(document).ready(function () {

    LoadCalendarData(calendar_data);
    hideShimmingSidebarLoader();

    var headerToolbar = headerToolbar = {
        left: '',
        center: '',
        right: 'prev next title'
    };
    if ($("html").attr("dir") == "rtl") {
        direction = 'rtl';
        start = 'next prev title';
        headerToolbar = {
            left: 'title  prev next',
            center: '',
            right: ''
        };
    }

    var JSlastScrollTop = 0;
    $(".modal .js-filter-calendar-scrollable").scroll(function (event) {
        if ($(window).width() < 768) {
            var st = $(this).scrollTop();
            if (st > JSlastScrollTop) {

                if ($(".js-swiper-calendar .js-sm-calendar:last").offset().top < 600) {

                    var now = new Date(parseInt($(".js-swiper-calendar .js-sm-calendar:last").attr("data-time")));

                    var currdate = new Date(now.setMonth(now.getMonth() + 1));
                    var js_id = "cal-" + new Date().getTime();
                    var html = '<div class="mb-30 js-sm-calendar   bsapp-sm-calendar-modal-view js-next-months d-md-none" id="' + js_id + '"  data-time="' + currdate.getTime() + '"></div>';
                    $(".js-swiper-calendar").append(html);
                    createSidebarCalendar(js_id, currdate, headerToolbar, direction);
                }


            } else if (st == JSlastScrollTop)
            {
//do nothing
//In IE this is an important condition because there seems to be some instances where the last scrollTop is equal to the new one
            } else {
                if ($(".js-swiper-calendar .js-sm-calendar:first").offset().top > -50) {

                    var now = new Date(parseInt($(".js-swiper-calendar .js-sm-calendar:first").attr("data-time")));

                    var currdate = new Date(now.setMonth(now.getMonth() - 1));
                    var js_id = "cal-" + new Date().getTime();
                    var html = '<div class="mb-30 js-sm-calendar  bsapp-sm-calendar-modal-view js-prev-months  d-md-none" id="' + js_id + '"  data-time="' + currdate.getTime() + '"></div>';
                    $(".js-swiper-calendar").prepend(html);
                    createSidebarCalendar(js_id, currdate, headerToolbar, direction);
                }

            }
            JSlastScrollTop = st;

        }
    });

    if ($(window).width() < 768) {
        setUpSidebarCalendars(headerToolbar, direction, currDate);
    }

    $("body").on("click", ".js-btn-today", function () {
        currDate = moment()._d;
        SetDate();
        GetCalendarData();
    });
    $("body").on("click", ".js-calendar-main-prev", function () {
        calendarMain.prev();
        currDate = new Date(calendarMain.getDate())
        SetDate()
        GetCalendarData();
    });
    $("body").on("click", ".js-calendar-main-next", function () {
        calendarMain.next();
        currDate = new Date(calendarMain.getDate())
        SetDate()
        GetCalendarData();
    });

    $("body").on("click", ".js-sm-calendar [data-date] .fc-daygrid-day-frame", function () {
        currDate = $(this).parents("[data-date]").attr("data-date");
        SetDate();
        GetCalendarData();
        $(".js-sm-calendar").find('[data-date]').removeClass("bsapp-js-active-date");
        $(".js-sm-calendar").find('[data-date="' + moment(currDate).format("YYYY-MM-DD") + '"]').addClass("bsapp-js-active-date");
        $("#js-dynamic-styles").html('[data-date="' + moment(currDate).format("YYYY-MM-DD") + '"] .fc-daygrid-day-number{ background:var(--primary);width:24px;height:24px;display:flex;justify-content:center;alisgn-items:center;border-radius:50%;color:white;}');
        $("#js-modal-calendar-selector").modal("hide");
    })
    $("body").on("click", "#js-modal-show-calendar", function () {
        setUpSidebarCalendars(headerToolbar, direction, currDate);

        $("#js-modal-calendar-selector").modal("show");

        $(".js-sm-calendar").find('[data-date]').removeClass("bsapp-js-active-date");
        $(".js-sm-calendar").find('[data-date="' + moment(currDate).format("YYYY-MM-DD") + '"]').addClass("bsapp-js-active-date");
        $(".js-sm-calendar [data-date='" + moment(currDate).format("YYYY-MM-DD") + "']").parents(".js-sm-calendar")[0].scrollIntoView({behavior: "auto", block: "center", inline: "nearest"});

    });
    $("body").on("click", "#js-modal-show-filters", function () {
        $(".js-modal-view-filter").show();
        $(".js-modal-view-calendar").hide();
        $("#js-modal-calendar-filter").modal("show");
    });

    $("#js-modal-calendar-filter").on("show.bs.modal", function () {
        RestoreFilterState();
    })

    $("body").on("click", ".js-back-to-today", function () {
        //$(".js-sm-calendar [data-date='" + moment().format("YYYY-MM-DD") + "']").trigger("click");
        currDate = moment()._d;
        SetDate();
        GetCalendarData();

        $("#js-modal-calendar-selector").modal("hide");
        //$(".js-sm-calendar [data-date='" + moment().format("YYYY-MM-DD") + "']").parents(".js-sm-calendar")[0].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
    });

    $("body").on("click", "[name='js_sm_calendar_settings']", function () {
        $("[name='js_sm_split_settings']").prop("checked", false);
        if ($(this).prop("checked") == true) {
            var this_view_val = $(this).val();
            jsMobileView = $(".select2-calendar-select option[value='" + this_view_val + "']").attr("data-view");

            currDate = new Date(calendarMain.getDate())
            SetDate();
            if ($(this).val() == '2' || $(this).val() == '4' || $(this).val() == '3') {

            } else if ($(this).val() == "agenda") {
                jsMobileTypeOfView = 2;
                jsMobileSplitView = 1;
                jsMobileView = 'timeGridDay';
            } else {
                jsMobileTypeOfView = 1;
                jsMobileSplitView = 1;
            }
            GetCalendarData();

        }
    });
    $("body").on("click", "[name='js_sm_split_settings']", function () {
        $("[name='js_sm_calendar_settings']").prop("checked", false);
        if ($(this).prop("checked") == true) {
            jsMobileView = 'timeGridDay';
            SetDate();
            $("#js-sm-daily-view").prop("checked", true)
            jsMobileSplitView = $(this).val();
            //jsMobileTypeOfView = 1;
        }
        GetCalendarData();

    });
    $(function () {
        // Bind the swipeleftHandler callback function to the swipe event on div.box
        $.event.special.swipe.horizontalDistanceThreshold = 80;
        $("#calendar-main").on("swipeleft", swipeleftHandler);
        $("#calendar-main").on("swiperight", swiperightHandler);
        // Callback function references the event target and adds the 'swipeleft' class to it
        function swipeleftHandler(event) {
            if ($(window).width() < 768) {
                if ($("html").attr("dir") == "rtl") {
                    calendarMain.prev();
                } else {
                    calendarMain.next();
                }
                $("#calendar-main").addClass("animated slideInRight");
                currDate = new Date(calendarMain.getDate())
                SetDate()
                GetCalendarData();
            }
        }
        function swiperightHandler(event) {
            if ($(window).width() < 768) {
                if ($("html").attr("dir") == "rtl") {
                    calendarMain.next();
                } else {
                    calendarMain.prev();
                }
                $("#calendar-main").addClass("animated slideInLeft");
                currDate = new Date(calendarMain.getDate())
                SetDate()
                GetCalendarData();
            }
        }

        $("#calendar-main").bind("animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd", function () {
            $(this).removeClass("animated");
            $(this).removeClass("slideInRight");
            $(this).removeClass("slideInLeft");
        })

        /*  $("#js-modal-calendar-filter").on("shown.bs.modal", function () {
         setTimeout(function () {
         setBranches(calendar_data.branches);
         }, 400)
         });*/
    });
    $(".js-sm-calendar-settings").on("click", function () {
        $("#calendarViewSettings").click();
    });

    $(".js-copy-link-meeting-booking").on("click", function (e){
        e.preventDefault();

        let studioUrl = calendar_data.StudioUrl;
        let link = js_application_url + '/meeting-booking.php?GetUrl=' + studioUrl;

        document.addEventListener('copy', function(e) {
            e.clipboardData.setData('text/plain', link);
            e.preventDefault();
        }, true);

        let input = document.createElement('textarea');
        input.value = link;
        document.body.appendChild(input);
        input.select();
        input.setSelectionRange(0, 99999); /*For mobile devices*/
        document.execCommand('copy');
        document.body.removeChild(input);

        $(this).find('i:first').attr('title', 'Copied!');
        $(this).find('i:first').tooltip('show');
        $(this).find('i:first').removeClass("fa-link").addClass("fa-check text-success");

        setTimeout(() => {
            $(this).find('i:first').removeClass("fa-check text-success").addClass("fa-link");
            $(this).find('i:first').tooltip('dispose');
            $(this).find('i:first').attr('title', '');
        }, 2000);

    })


    $("body").on("change", "#js-time-from , #js-time-to", function () {
        if ($("#js-time-from").val() > $("#js-time-to").val()) {
            $("#js-time-from , #js-time-to").removeClass("border-light").addClass("border-danger");
            $(".js-time-error").remove();
            $(this).parent().after('<span class="text-danger mt-4 js-time-error">' + lang('not_valid_time') + '</span>');
            $(".btn-save-calendar-settings").addClass("disabled");
        } else {
            $("#js-time-from , #js-time-to").removeClass("border-danger").addClass("border-light");
            $(".btn-save-calendar-settings").removeClass("disabled");
            $(".js-time-error").remove();
        }
    });

});
$(document).on("change", ".select2-calendar-select", function () {

    var selected = $(this).find(':selected')
    view = selected.data('view');

    if (view == 'dayGridMonth') {
        jsTypeOfView = 1;
        jsSplitView = 1;
    }

    SetDate()
    GetCalendarData();
});

$(document).on("change", ".js-select-branches", function () {
    branch_id = $(this).val();
    /* if ($(window).width() < 767) {
     $(this).parents(".modal").modal("hide");
     }*/
    GetCalendarData();
})

$(document).on("click", "#calendar-main .fc-prev-button, #calendar-main  .fc-next-button", function () {

    currDate = new Date(calendarMain.getDate())
    SetDate()
    GetCalendarData();
})
function SetDate() {
    if ($(window).width() < 768) {
        var l_view = jsMobileView;
    } else {
        var l_view = view;
    }
    switch (l_view) {
        case "dayGridMonth":
            SetFirstLastdayOfMonth(currDate)
            break;
        case "timeGridWeek":
            SetFirstLastdayOfweek(currDate)
            break;
        case "timeGridDay":
            SetFirstLastdayOfDay(currDate)
            break;
        case "timeGridThreeDay":
            SetFirstLastdayOfThreeDay(currDate)
            break;
        case "resource_timeGridWeek":
            SetFirstLastdayOfweek(currDate)
            break;
        case "resource_timeGridDay":
            SetFirstLastdayOfDay(currDate)
            break;
        case "resource_timeGridThreeDay":
            SetFirstLastdayOfThreeDay(currDate)
            break;
        default:
            SetFirstLastdayOfMonth(Date)
            break;
    }
}
function GetCalendarData() {
    showShimmingLoader();

    apiProps = {
        fun: 'GetClassesByStudioByDate',
        branchId: branch_id,
        Locations: locations.toString(),
        Coaches: owner.toString(),
        Classes: title.toString(),
        ViewState: view,
        StartDate: moment(firstday).format('YYYY-MM-DD'),
        EndDate: moment(lastday).format('YYYY-MM-DD'),
        ViewDate: moment(currDate).format('YYYY-MM-DD'),
        SplitView: jsSplitView,
        TypeOfView: jsTypeOfView,
        MobileView: jsMobileView,
        MobileSplitView: jsMobileSplitView,
        MobileTypeOfView: jsMobileTypeOfView,
        TimeFrom: jsTimeFrom,
        TimeTo: jsTimeTo,
        ScreenWidth: $(window).width()
    }
    postApi('CalendarView', apiProps, 'LoadCalendarData')
}
function LoadCalendarData(data) {
    hideShimmingLoader()
    calendar_data = data;
    $('#total_classes').html(data.Stats.TotalClasses)
    $('#total_trainers').html(data.Stats.TotalTrainers)

    view = data.FilterState == null ? view : data.FilterState.ViewState
    if (data.FilterState != null) {
        firstday = (moment(data.FilterState.StartDate)._d == "Invalid Date") ? moment()._d : moment(data.FilterState.StartDate)._d;
        lastday = (moment(data.FilterState.EndDate)._d == "Invalid Date") ? moment()._d : moment(data.FilterState.EndDate)._d;
        currDate = (moment(data.FilterState.ViewDate)._d == "Invalid Date") ? moment()._d : moment(data.FilterState.ViewDate)._d;
        locations = data.FilterState.Locations;
        title = data.FilterState.Classes;
        owner = data.FilterState.Coaches;
    }

    jsSplitView = data.viewType.SplitView;
    jsTypeOfView = data.viewType.TypeOfView;

    jsMobileView = data.MobileView.view;
    jsMobileSplitView = data.MobileView.SplitView;
    jsMobileTypeOfView = data.MobileView.TypeOfView;

    if(jsTypeOfView == 1) {
        jsTimeTo = data.TimeTo;
        jsTimeFrom = data.TimeFrom;
    } else {
        jsTimeTo = '23:59';
        jsTimeFrom = '00:00';
    }

    $("#js-time-from").val(jsTimeFrom);
    $("#js-time-to").val(jsTimeTo);

    studioClassSetting = data.viewType;
    ClassesSession = data.Classes;
    if (data.resources.length > 0) {
        resources = data.resources;
    } else {
        resources = false;
    }

    if ($(window).width() < 768) {
        if (jsMobileSplitView == 1) {
            resources = false;
        }
    } else {
        if (jsSplitView == 1) {
            resources = false;
        }
    }

    populateFilters(data.filters, data.FilterState);
    if ($(window).width() > 768) {
        if (data.viewType.TypeOfView == 2) {
            $("#calendar-main").addClass("bsapp-js-agenda-view");
            nowIndicator = false;
        } else {
            nowIndicator = true;
            $("#calendar-main").removeClass("bsapp-js-agenda-view");
        }
    } else {
        if (data.MobileView.TypeOfView == 2) {
            $("#calendar-main").addClass("bsapp-js-agenda-view");
            nowIndicator = false;
        } else {
            nowIndicator = true;
            $("#calendar-main").removeClass("bsapp-js-agenda-view");
        }
    }
    /*  if ($(window).width() > 767) {
     setBranches(data.branches);
     }

     if ($("#js-modal-calendar-filter.show").length == 1) {
     setBranches(data.branches);
     }
     */
    setBranches(data.branches);
    loadCalendar(data.Classes);
    meetingDetailsModule.setAllStatus(data.MeetingStatuses);
    meetingDetailsModule.setMeetingSettings(data.MeetingSettings);
    //console.log('loadCalendar data=', data);
}

var js_selected_branch = 0;
function setBranches(branches) {
    var html = '';
    $(".js-select-branches.select2-hidden-accessible").select2("destroy");
    var js_first_branch_id;
    if (branches.length > 0) {
        //$(".js-branches-box").hide();
        $(branches).each(function (x) {
            js_selected_branch = (branches[x].selected == 1) ? branches[x].id : js_selected_branch;
            html += '<option value="' + branches[x].id + '"  ' + ((branches[x].selected == 1) ? " selected " : "") + '>' + branches[x].name + '</option>';
        });

        $(".js-select-branches").html(html).select2({
            theme: "bsapp-dropdown bsapp-no-arrow bsapp-branches-select",
            minimumResultsForSearch: -1,
            dropdownParent: $("#js-modal-calendar-filter"),
            templateSelection: function (item) {
                $item = $('<div class="d-flex justify-content-between bg-dark text-white align-items-center w-100"><div><span><i class="fal fa-location-circle mx-6 bsapp-fs-18"></i></span>' + item.text + '</div><div><span class="bsapp-fs-20" title=""><i class="fal fa-angle-down"></i></span></div></div>');
                return $item;
            }
        });
        js_first_branch_id = branches[0].id;
        if (branches.length == 1) {
            $(".js-select-branches.select2-hidden-accessible").select2("destroy");
            $(".js-select-branches").hide();
        }

    } else {
        $(".js-select-branches").hide();
        //$(".js-branches-box").show();
    }



    if (js_selected_branch == js_first_branch_id) {
        //$("#calendarFilters-location").find(".custom-checkbox[data-branch-id !='" + js_selected_branch + "']").filter(".custom-checkbox[data-branch-id !='0']").children("input").prop("checked", false).parents(".custom-checkbox").hide();
        $("#calendarFilters-location").find(".custom-checkbox[data-branch-id !='" + js_selected_branch + "']").filter(".custom-checkbox[data-branch-id !='0']").hide();
        $("#calendarFilters-location .custom-checkbox[data-branch-id='" + js_selected_branch + "']").show();
        $("#calendarFilters-location .custom-checkbox[data-branch-id='0']").show();
    } else {
        //$("#calendarFilters-location .custom-checkbox[data-branch-id !='" + js_selected_branch + "'] input").prop("checked", false).parents(".custom-checkbox").hide();
        $("#calendarFilters-location .custom-checkbox[data-branch-id !='" + js_selected_branch + "']").hide();
        $("#calendarFilters-location .custom-checkbox[data-branch-id='" + js_selected_branch + "']").show();
    }
}

function SetFirstLastdayOfweek(StrDate = new Date().toString()) {


    var firstdayobj = moment(currDate).startOf('week');
    var lastdayobj = moment(currDate).endOf('week');
    firstday = firstdayobj._d;

    lastday = lastdayobj._d;

}
function SetFirstLastdayOfMonth(StrDate = new Date().toString()) {

    var firstdayobj = moment(currDate).startOf('month');
    firstday = firstdayobj._d;
    var lastdayobj = moment(currDate).endOf('month');
    lastday = lastdayobj._d;
}


function SetFirstLastdayOfDay(StrDate = new Date().toString()) {

    firstday = moment(currDate)._d;
    lastday = moment(currDate)._d;
}
function SetFirstLastdayOfThreeDay(StrDate = new Date().toString()) {
    firstday = moment(currDate)._d;
    lastdayobj = moment(firstday).add(2, 'days');
    lastday = lastdayobj._d
}
$(document).on("change", ".fillter-check", function () {
    $(".js-div-stats").removeClass("d-flex").addClass("d-none");
    $(".js-div-filter-apply").removeClass("d-none").addClass("d-flex");
});

function FilterDataByChecked() {
    ClassesSession.forEach(function (elm, index) {
        if (title.includes(elm.titleId) && owner.includes(elm.ownerId) && locations.includes(elm.locationId)) {
            ClassesForFilter.push(elm)
        }
    })
}

function setFilterData() {
    var checkedFilter = $(".fillter-check").not(":checked");
    locations = [];
    owner = [];
    title = [];
    tasks = 1;
    ClassesForFilter = [];
    $(checkedFilter).each(function () {
        switch ($(this).attr('data-type')) {
            case "location":
                locations.push($(this).attr('data-id'));
                break;
            case "owner":
                owner.push($(this).attr('data-id'));
                break;
            case "title":
                title.push($(this).attr('data-id'));
                break;
            case "tasks":
                tasks = 0;
                break;
        }
    })

}

function RestoreFilterState() {
    populateFilters(calendar_data.filters, calendar_data.FilterState);
    $(".js-div-stats").removeClass("d-none").addClass("d-flex");
    $(".js-div-filter-apply").removeClass("d-flex").addClass("d-none");
}
function SaveFilterState() {
    setFilterData();
    showShimmingLoader();


    apiProps = {
        fun: 'GetClassesByStudioByDate',
        branchId: branch_id,
        Locations: locations.toString(),
        Coaches: owner.toString(),
        Classes: title.toString(),
        Tasks: tasks,
        ViewState: view,
        StartDate: moment(firstday).format('YYYY-MM-DD'),
        EndDate: moment(lastday).format('YYYY-MM-DD'),
        ViewDate: moment(currDate).format('YYYY-MM-DD'),
        TypeOfView: jsTypeOfView,
        SplitView: jsSplitView,
        MobileView: jsMobileView,
        MobileSplitView: jsMobileSplitView,
        MobileTypeOfView: jsMobileTypeOfView,
        TimeFrom: jsTimeFrom,
        TimeTo: jsTimeTo,
        ScreenWidth: $(window).width()
    }
    postApi('CalendarView', apiProps, 'LoadFilterCallback')
}
function LoadFilterCallback(data) {
    LoadCalendarData(data);
    hideShimmingLoader();


    $(".js-div-stats").removeClass("d-none").addClass("d-flex");
    $(".js-div-filter-apply").removeClass("d-flex").addClass("d-none");

}

function showShimmingSidebarLoader() {
    $(".js-loading-sidebar-calendar-shimmer").show();
    $(".js-side-filters").addClass("d-none");
}
function showShimmingLoader() {
    $("#js-modal-calendar-filter.show").modal("hide");
    $(".js-calendar-custom-header").removeClass("d-md-flex");
    $(".js-loading-calendar-shimmer").show();
    $("#calendar-main").addClass("d-none");
    $("#calendarSettings").removeClass("d-flex").addClass("d-none");

}

function hideShimmingSidebarLoader() {
    $(".js-loading-sidebar-calendar-shimmer").hide();
    $(".js-side-filters").removeClass("d-none");
}

function hideShimmingLoader() {
    $(".js-loading-calendar-shimmer").hide();
    $("#calendar-main").removeClass("d-none");
    $("#calendarSettings").removeClass("d-none").addClass("d-flex");
    $(".js-calendar-custom-header").addClass("d-md-flex");

}

function timeRangeVisibility(){
    const viewOptions = $('.calendarSettings-display-options');
    if(viewOptions.find('#calendar-type-view').prop('checked') == true){
        viewOptions.find('li:last-child').slideDown(200);
    } else {
        viewOptions.find('li:last-child').slideUp(200);
    }
}

function adjustScrollHeight() {
    var calendar_main_h, act_height;
    var calendarMainEl = $("#calendar-main");
    var header_height = $(".js-calendar-custom-header").height();
    var tbl_header = calendarMainEl.find(".fc-col-header ").height();
    var mis = '';
    var dir = 'ltr';
    var posn = 'right';
    if ($("html").attr("dir") == "rtl") {
        dir = 'rtl';
        mis = 'margin-inline-start:1px;';
        posn = 'left';
    }

    calendarMainEl.find("tbody div.fc-scroller:first").addClass("js-the-scroller").attr("style", "");
    var isAgenda = calendarMainEl.hasClass("bsapp-js-agenda-view");
    var initScroll = isAgenda && view != undefined && (view == 'timeGridWeek' || view == 'timeGridThreeDay' || view == 'timeGridDay');
    var highestTimeGridCol = 0;
    calendarMainEl.find('.fc-timegrid-col-events').each(function() {
        var itemH = $(this).height();
        highestTimeGridCol = itemH > highestTimeGridCol ? itemH : highestTimeGridCol;
    });

    if ($(window).width() < 768) {
        calendar_main_h = $(window).height() - 53 - 64 - 80 - header_height;
        act_height = calendar_main_h - tbl_header;
        calendarMainEl.find("tbody div.fc-scroller:first").addClass("js-the-scroller").attr("style", "");
        $('.js-the-scroller').height('calc( ' + act_height + 'px - env(safe-area-inset-bottom))').addClass("bsapp-scroll");

        initScroll = initScroll && act_height > highestTimeGridCol ? false : true;
        if (initScroll) {
            $('.js-the-scroller').addClass("bsapp-overflow-y-auto");
        } else {
            $('.js-the-scroller').removeClass("bsapp-overflow-y-auto");
        }

    } else {
        calendar_main_h = $(".bsapp-calendar-main").height();
        act_height = calendar_main_h - header_height - tbl_header - 30;
        $(".js-div-calendar-main").height($(".js-div-calendar-main").height() + 30);
        $(".js-the-scroller").attr("style", "height:" + act_height + "px !important; " + mis);

        initScroll = initScroll && act_height > highestTimeGridCol ? false : true;
        if (initScroll) {
            $(".js-the-scroller").attr("style", "overflow-y:auto;overflow-x:hidden;");
            $('.js-the-scroller').slimScroll({
                position: posn,
                height: act_height + 'px',
                size: "4px",
                railVisible: true,
                //  start: $('#calendar-main [data-time="' + js_scroll_time + '"]'),
                alwaysVisible: true
            });
        } else {
            $('.js-the-scroller').slimScroll({destroy: true});
        }

        var styles = $(".fc-scroller-harness .slimScrollDiv").attr("style");
        $(".fc-scroller-harness .slimScrollDiv").attr("style", styles + ' position:unset !important; direction: ' + dir + ' !important;');
    }
    js_last_window_height = $(window).height();

    calendarMainEl.find('.fc-timegrid-axis').attr('aria-hidden', 'true');
    if (isAgenda) {
        calendarMainEl.find('colgroup').addClass('d-none');
        calendarMainEl.find('colgroup').find('col').css('width', 0);
        calendarMainEl.find('.fc-timegrid-axis').addClass('d-none');
        calendarMainEl.find('.fc-timegrid-slots').addClass('d-none');
        calendarMainEl.find('.fc-timegrid-body').css('height', highestTimeGridCol + 'px');
    } else {
        calendarMainEl.find('.fc-timegrid-slots').removeClass('d-none');
        calendarMainEl.find('.fc-timegrid-axis').removeClass('d-none');
        calendarMainEl.find('.fc-timegrid-slots').removeClass('d-none');
        calendarMainEl.find('colgroup').find('col').css('width', '55px');
        calendarMainEl.find('colgroup').removeClass('d-none');
        calendarMainEl.find('.fc-timegrid-body').css('height', 'auto');
    }
    setTimeout(function() {
        calendarMain.render();
    }, 100);

    //var js_scroll_time = moment().format("HH:00:00");

    //if ($('#calendar-main [data-time="' + js_scroll_time + '"]').length > 0) {
    //    $('#calendar-main [data-time="' + js_scroll_time + '"]')[0].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
    //  $("#calendar-main .slimScrollBar").css("top", $('#calendar-main [data-time="' + js_scroll_time + '"]').offset().top)
    //  }


    /*
     var js_scroll_time = moment().format("HH:00:00");
     if ($('#calendar-main [data-time="' + js_scroll_time + '"]').length > 0) {
     jsSlimScrollTo.slimScroll({
     scrollTo: $('#calendar-main [data-time="' + js_scroll_time + '"]').offset().top
     })
     }
     */


}
