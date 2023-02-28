import {Calendar} from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import "@/scss/full-calendar.scss";

import {removeClass} from "@modules/Helpers";
import {Lang} from "@modules/Lang";

export default function FullCalendar(props) {
	const {el, options, _setInitialDate} = props;
	let initialDateStr = options.initialDate || new Date();

	const nowDate = new Date();
	const nowDateMonth = nowDate.getMonth() > 9 ? nowDate.getMonth() : '0' + nowDate.getMonth();
	const param = {
		plugins: [interactionPlugin, dayGridPlugin],
		locale: document.dir === 'rtl' ? 'he' : 'en',
		direction: document.dir,
		expandRows: true,
		timeZone: 'UTC',
		headerToolbar: {
			start: 'prev title next', // will normally be on the left. if RTL, will be on the right
			//start: 'title', // will normally be on the left. if RTL, will be on the right
			center: '',
			end: 'today'
		},
		footerToolbar: {
			center: ''
		},
		dayHeaderFormat: {
			weekday: 'narrow',
		},
		buttonText: {
			today: Lang('back_to_today')
		},
		validRange: {
			start: new Date(`${nowDate.getFullYear()}-${nowDateMonth}-01T00:00:00.000Z`)
		},
		dayHeaders: true,
		fixedWeekCount: false,
		contentHeight: 'auto',
		selectable: false,
		navLinks: false, // can click day/week names to navigate views
		initialDate: nowDate,
		weekends: true,
		initialView: 'dayGridMonth',
		views: {
			dayGridMonth: {
				// showNonCurrentDates: false,
				duration: {months: 1}
			}
		},
		dateClick: function (info) {
			const currentDayEl = info.dayEl;
			if (currentDayEl.classList.contains('fc-day-past') || currentDayEl.classList.contains('fc-day-other')) {
				return false;
			}
			const currentCalendarEl = info.view.calendar.el;
			removeClass(currentCalendarEl.querySelectorAll('.fc-daygrid-day'), 'fc-day-selected');
			currentDayEl.classList.add('fc-day-selected');
			initialDateStr = info.date;

			if (_setInitialDate) {
				_setInitialDate(info.date);
			}

			// console.log('[dateClick]', info);
		},
		dayCellDidMount : function(arg){
			const calendarPrevBtn = arg.view.calendar.el.querySelector('.fc-prev-button');
			calendarPrevBtn.setAttribute('disabled', 'disabled');
			calendarPrevBtn.classList.add('fc-state-disabled');

			if (arg.el.classList.contains("fc-day-past")){
				return false;
			}

			// console.log('[dayCellDidMount]', arg, arg.view.getCurrentData());

			const argDateStr = arg.date.toISOString().slice(0,10);
			if (initialDateStr.toISOString().slice(0,10) === argDateStr) {
				arg.el.classList.add('fc-day-selected');
			}

			if (nowDate.getMonth() !== arg.view.currentStart.getMonth()) {
				calendarPrevBtn.removeAttribute('disabled');
				calendarPrevBtn.classList.remove('fc-state-disabled');
			}
		}
	};
	const settings = Object.assign({}, param, options);

	const calendar = new Calendar(el, settings);
	calendar.render();
}