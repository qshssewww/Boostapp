const meetingDetailsModule = (function (module, $) {
	'use strict';

	// Public functions
	let init, createMainData, loadCalenderBox, isGrey, isMeetingType, eventClick, setAllStatus,
		setMeetingSettings, updateMeetingModal, isGradientBg, chargedOnMeetingAction;

	// Private general variables
	let isInit = false, $body, $window, settings, selSettings, meetingAllStatuses, meetingSettings;

	// Private plugin variables
	let $meetingModal, $helpersModal, $meetingContent, $helpersContent, $meetingHandlebarModal, $helpersHandlebarModal;
	let selClass, selBoxClass, selBoxOptionsClass, selPlaceholderClass, getContentHelpers, dataToSend, dataCurrentMeeting;

	// Private functions
	let initEvents, buildModal, buildModalHelpers, fetchData, modalCallback, errorCallback, setDate, setPrice, monthString, dayOfWeekToString,
		setTime, setDuration, convertHM, setTranslForStatus, createFakeDropdown, clearModal, findCurrent, toggleVisibility, toggleHeight, checkHeight,
		beforeRequestCancelMeeting, setErrorOnEl, loaderModal, setDocTypeLink, hasSuitableSubscription, sumPrices,
		dataSendParams, buildContentHelpers, setPhoneWithCountryCode, setPhoneWithCountryCodeWithoutPlus, setIconForStatus,
		beforeRequestChangeStatus, setCurrentMeetingObj, ifSidebarOpened, beforeOpenDropdown, setPhoneWithLine, getTextSubscription, hideSidebarElement,
		openOverLimitationModal;

	/*============================================================================
	   Initialise the plugin and define global options
	 ==============================================================================*/
	init = function(options) {
		if (isInit) {
			return false;
		}

		// Default settings
		settings = {
			hideClass: 'd-none',
			redClass: 'border--red',
			docsLinkPopupClass: 'meeting--doc-link',
			btnCompleteLeaveInDebtClass: 'btn--complete-leave-in-debt',
			btnCreatePayment: 'btn--create-payment',
			btnRegisterSubscriptionClass: 'btn--register-subscription',
			btnUnsubscribeFromClass: 'btn--unsubscribe-from-subscription',
			btnCancelRepeatMeetingClass: 'btn--cancel-repeat-meeting',
			btnTypeToClass: 'btn--cancel-type-to',
			btnCancelDocClass: 'btn--cancel-doc',
			btnCancelMeetingClass: 'btn--cancel-meeting',
			btnCancelWithCharge: 'btn--cancel-with-charge',
			btnChargedClient: 'btn--charged-client',
			btnCancelNoCharge: 'btn--cancel-no-charge',
			btnChangeStatusWithCharge: 'btn--change-status-with-charge',
			btnOpenNextPopup: 'btn--open-next-popup',
			btnChangeStatusNoCharge: 'btn--change-status-no-charge',
			btnChangeToNotArrived: '.btn--change_to_not_arrived',
			btnChangeToNotArrivedOption: 'btn--change_to_not_arrived_option',
			btnOrderRejection: 'btn--order-rejection',
			btnToApprove: 'js--to-approve',
			btnCloseModalClass: 'btn--close-modal',
			copyToClipboard: '.js--copy-to-clipboard',
			modal: '.js--bsapp--meeting-modal',
			meetingModal: '#js--meeting-popup_details',
			helpersModal: '#js--meeting-popup_helpers',
			sidebarManageMeetings: '#sidebarManageMeetings',
			meetingHandlebarModal: '#meetingTemplateModal',
			helpersHandlebarModal: '#meetingHelpersModal',
			modalContent: '.modal-content',
			modalBody: '.modal-body',
			modalLoader: '.js--loader',
			payBtn: '.js--meeting--btn-pay',
			blockCustomerFromOrderingName: 'blockCustomerFromOrdering',
			registerSubscriptionName: 'registerSubscriptionId',
			selectStatus: '[name="meetingStatus"]',
			selectOptions: '[name="meetingOptions"]',
			selectSeriesDropdown: '[name="meetingSeriesDropdown"]',
			radioCancelRepeat: '[name="cancelMeetingRadio"]',
			radioOptionsCharged: '[name="optionsChargedRadio"]',
			dateCancelSince: '[name="meetingCancelDateSince"]',
			dateCancelUntil: '[name="meetingCancelDateUntil"]',
			inputCancelQty: '[name="meetingCancelQuantity"]'
		};

		selSettings = {
			classActive: 'active',
			classSelected: 'selected',
			classDisabled: 'disabled',
			sel: 'sel',
			selBox: 'sel__box',
			selBoxOptions: 'sel__box__options',
			selPlaceholder: 'sel__placeholder',
			selCover: 'sel__cover'
		};

		// Override defaults with arguments
		$.extend(settings, options);

		// Select DOM elements
		$meetingModal = $(settings.meetingModal);
		$helpersModal = $(settings.helpersModal);
		$meetingHandlebarModal = $(settings.meetingHandlebarModal);
		$helpersHandlebarModal = $(settings.helpersHandlebarModal);
		$meetingContent = $(settings.meetingModal).find(settings.modalContent);
		$helpersContent = $(settings.helpersModal).find(settings.modalContent);

		selClass = '.' + selSettings.sel;
		selBoxClass = '.' + selSettings.selBox;
		selBoxOptionsClass = '.' + selSettings.selBoxOptions;
		selPlaceholderClass = '.' + selSettings.selPlaceholder;

		// General Selectors
		$body = $('body');
		$window = $(window);

		initEvents();
		isInit = true;
	};

	initEvents = function() {
		$window.resize(function() {
			const $modal = $(settings.modal + ':visible');
			if ($modal) {
				if ($modal.find(selClass).hasClass(selSettings.classActive)) {
					$modal.find(selClass).removeClass(selSettings.classActive).find(selBoxClass).css('display', 'none');
				}
				toggleHeight($modal);
			}
		});

		$(settings.modal).on('click', settings.copyToClipboard, function (e) {
			e.preventDefault();
			e.stopPropagation();
			const $el = $(this);
			$el.addClass('copied');
			if (navigator.clipboard !== undefined) {
				navigator.clipboard.writeText($el.attr('href')).then(function() {
					setTimeout(function() {
						$el.removeClass('copied');
					}, 2000);
				}, function() {
					$.error('clipboard copy failed');
				});
			}
		});

		$helpersModal.on('show.bs.modal', function(e) {
			if ($meetingModal.hasClass('show') || !ifSidebarOpened()) {
				$meetingModal.addClass('over-modal show').show();
			}
		});

		$helpersModal.on('shown.bs.modal', function(e) {
			if ($meetingModal.hasClass('show') || !ifSidebarOpened()) {
				$body.addClass('modal-open');
			}
		});

		$helpersModal.on('hide.bs.modal', function(e) {
			if ($meetingModal.hasClass('over-modal') && !$(this).hasClass('hide-meeting-modal')) {
				$meetingModal.modal("show");
			}
		});

		$helpersModal.on('hidden.bs.modal', function(e) {
			if ($meetingModal.hasClass('over-modal') || !ifSidebarOpened()) {
				$meetingModal.removeClass('over-modal');
				$body.addClass('modal-open');
			}
			if (ifSidebarOpened()) {
				$body.addClass('modal-open');
			}
		});

		$meetingModal.on('hidden.bs.modal', function(e) {
			$('.js-remove-regular-assignment').find('#js-class-data').remove();
		});
		// add a new z-index to TINY box modal
		$(settings.modal).on('click', '.' + settings.docsLinkPopupClass, function() {
			let newZIndex = 1041;
			$body.find('.tmask').css('z-index', newZIndex - 1);
			$body.find('.tbox').css('z-index', newZIndex);
		});
		// close all meeting modals
		$(settings.modal).on('click', '.' + settings.btnCloseModalClass, function() {
			const $modal = $(this).closest(settings.modal);
			if ($modal.find(selBoxClass).length > 0 && $modal.find(selBoxClass + ':visible').length > 0) {
				$modal.find(selBoxClass).css('display', 'none');
			}
			$modal.modal("hide");
		});
		// Button to show content depends on the type
		$(settings.modal).on('click', '.' + settings.btnTypeToClass, function(e) {
			e.preventDefault();
			const $el = $(this);
			if ($el.attr('data-type-to') === undefined) {
				return false;
			}

			setCurrentMeetingObj($el.attr('data-id'));
			buildModalHelpers($el.attr('data-type-to'));
		});
		// White button to create payment of the meeting depends on user credit card
		$helpersModal.on('click', '.' + settings.btnCreatePayment, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);

			if (dataCurrentMeeting == undefined) {
				return false;
			}
			dataToSend = {
				action: 'createPayment', // action to check what user start to do
				id: dataCurrentMeeting.id
			};

			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {
				// --IMPORTANT--  delete when statuses will be updated from DB
				dataCurrentMeeting.status = settings.statusCompletedId; // completed meeting
				// --IMPORTANT-- end

				// TODO: UPDATE MODAL
				updateMeetingModal();
				buildModalHelpers($el.attr('data-type-to'));
			});
		});
		// White button to complete a meeting and leave user in debt. Only an invoice will be issued, this means that the client will be in debt for the amount of the meeting cost.
		$helpersModal.on('click', '.' + settings.btnCompleteLeaveInDebtClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);

			if (dataCurrentMeeting == undefined) {
				return false;
			}
			dataToSend = {
				action: 'completeMeetingLeaveInDebt', // action to check what user start to do
				id: dataCurrentMeeting.id
			};

            dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {

				loaderModal($meetingModal);
				$modal.addClass('hide-meeting-modal').modal("hide");
				$meetingModal.modal("hide");
			});
		});
		// Green button to register an meeting for a suitable subscription that the user has
		$helpersModal.on('click', '.' + settings.btnRegisterSubscriptionClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			const $modalSelect = $modal.find('select');

			if ($modalSelect.length > 0 && $modalSelect.attr('required') === 'required' && $modalSelect.val() === null) {
				setErrorOnEl($modalSelect.siblings('.sel__placeholder'));
				return false;
			}

			if (dataCurrentMeeting == undefined) {
				return false;
			}
			const id = $modalSelect.length > 0 ? $modalSelect.attr('data-id') : dataCurrentMeeting.id;
			const idSubscr = $modalSelect.length > 0 ? $modalSelect.val() : dataCurrentMeeting.customer.MemberShipText.data[0].Id;
			dataToSend = {
				action: 'registerSubscription', // action to check what user start to do
				id: id,
				customerId: dataCurrentMeeting.customer.id,
				[settings.registerSubscriptionName]: idSubscr
			};

			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {
				loaderModal($meetingModal);
				updateMeetingModal(dataCurrentMeeting);
				$modal.modal("hide");
			});
		});
		// Red button on the canceling a subscription of meetings
		$helpersModal.on('click', '.' + settings.btnUnsubscribeFromClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);

			if (dataCurrentMeeting == undefined) {
				return false;
			}
			dataToSend = {
				action: 'unsubscribeFromSubscription', // action to check what user start to do
				id: dataCurrentMeeting.id,
				subscriptionId: dataCurrentMeeting.client_activities.id
			};

			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {
				loaderModal($meetingModal);
				updateMeetingModal(dataCurrentMeeting);
				$modal.modal("hide");
			});
		});
		// Red button on the canceling a document (invoice) of meetings
		$helpersModal.on('click', '.' + settings.btnCancelDocClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);

			const prevWas = $el.attr('data-type-prev');
			if (prevWas !== undefined) {
				dataCurrentMeeting._helpersStepPrev = prevWas;
			}

			if (dataCurrentMeeting == undefined) {
				return false;
			}

			dataToSend = {
                action: 'cancelDocuments', // action to check what user start to do
				id: dataCurrentMeeting.id,
			};

            dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {
                loaderModal($meetingModal);
                updateMeetingModal(dataCurrentMeeting);
                buildModalHelpers($el.attr('data-type-to'));
			});
		});
		// Green button on the canceling a series of meetings
		$helpersModal.on('click', '.' + settings.btnCancelRepeatMeetingClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);

			if (dataToSend.action === undefined) {
				return false;
			}

			const cancelRepeatInp = $modal.find(settings.radioCancelRepeat + ':checked');
			dataToSend.repeatType = cancelRepeatInp.val();
			if (cancelRepeatInp.val() === 'single') {

			} else {
				const selectSeriesVal = $modal.find(settings.selectSeriesDropdown).val();
				dataToSend.repeatType = selectSeriesVal;
				switch (selectSeriesVal) {
					case 'dates':
						const $sinceEl = $modal.find(settings.dateCancelSince);
						const sinceElVal = $sinceEl.val();
						const $untilEl = $modal.find(settings.dateCancelUntil);
						const untilElVal = $untilEl.val();
						if (sinceElVal === '' || untilElVal === '') {
							setErrorOnEl($modal.find('[type="date"]'));
							return false;
						}

						const sinceDate = new Date(sinceElVal);
						const untilDate = new Date(untilElVal);
						if (untilDate.getTime() < sinceDate.getTime()) {
							setErrorOnEl($modal.find('[type="date"]'), true);
							return false;
						}

						dataToSend.repeatVal = JSON.stringify({
								since: sinceElVal,
								until: untilElVal
							}
						)
						break;

					case 'quantity':
						const $qtyEl = $modal.find(settings.inputCancelQty);
						const qty = $qtyEl.val();
						if (qty === '' || ($qtyEl.attr('min') !== undefined && parseInt(qty) < parseInt($qtyEl.attr('min')))) {
							setErrorOnEl($qtyEl);
							return false;
						}

						dataToSend.repeatVal = qty;
						break;
				}
			}

			beforeRequestCancelMeeting($modal);
		});
		// Green button on the cancellation policy will make a charge and will change the status to ‘completed’.
		$helpersModal.on('click', '.' + settings.btnChangeStatusWithCharge, function() {
			const $el = $(this);
			const currentMeetingId = $meetingModal.find(settings.selectStatus) !== undefined ? $meetingModal.find(settings.selectStatus).attr('data-id') : dataCurrentMeeting.id;

			dataToSend = {
				action: 'changeStatus',
				id: currentMeetingId,
				status: settings.statusNotArrivedId,
				oldStatus: dataCurrentMeeting.status,
				cancelShare: dataCurrentMeeting.not_arrived_share ? dataCurrentMeeting.not_arrived_share : 0
			};
			beforeRequestChangeStatus(dataToSend, $el.closest(settings.modal));
		});
		// White button on the cancellation policy to just change the status to non-show. client activity will also be changed to non-show status(new status).
		$helpersModal.on('click', '.' + settings.btnChangeStatusNoCharge, function() {
			const $el = $(this);
			const currentMeetingId = ($meetingModal.find(settings.selectStatus) !== undefined && $meetingModal.find(settings.selectStatus).length !== 0)
				? $meetingModal.find(settings.selectStatus).attr('data-id') : dataCurrentMeeting.id;
			dataToSend = {
				action: 'changeStatus',
				id: currentMeetingId,
				status: settings.statusNotArrivedId,
				oldStatus: dataCurrentMeeting.status,
			};
			beforeRequestChangeStatus(dataToSend, $el.closest(settings.modal));
		});
		// White button on the cancellation policy to change the status to canceled. client activity will also be changed to canceled status.
		$helpersModal.on('click', '.' + settings.btnCancelNoCharge, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			if (dataToSend === undefined || dataToSend.action === undefined) {
				return false;
			}
			dataToSend.cancellationPolicy = 'no_charge';
			beforeRequestCancelMeeting($modal);
		});
		// Green button on the cancellation policy will make the charge and will change the status to ‘completed’.
		$helpersModal.on('click', '.' + settings.btnCancelWithCharge, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			if (dataToSend.action === undefined || dataToSend.action !== 'cancelMeeting') {
				return false;
			}
			dataToSend.cancellationPolicy = moment().isAfter(dataCurrentMeeting.start) ? dataCurrentMeeting.not_arrived_share :
				dataCurrentMeeting.cancellation_share ? dataCurrentMeeting.cancellation_share : 0;
			const optionsChargedInp = $modal.find(settings.radioOptionsCharged + ':checked');
			const idSubscription = optionsChargedInp.val();
			dataToSend.chargedSubscriptionId = idSubscription;
			if(!!parseInt(idSubscription)) {
				dataToSend.cancellationPolicy = 100;
			}
			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function (res) {
				let modalShow = $el.attr('data-type-to');
				if(res && res.showMessage){
					// not success charged client
					modalShow === 'action_done' ? buildModalHelpers('meeting_not_arrived_success_charged_not_success') : buildModalHelpers('meeting_cancel_success_charged_not_success');
				} else {
					buildModalHelpers(modalShow);
					if(dataToSend.chargedSubscriptionId === 'cancellation_and_charge_cart') {
						window.location.href = '/office/cart.php?u=' + res.clientId + '&debt=' + res.clientActivity;
					}
				}
				if (ifSidebarOpened()) {
					hideSidebarElement(dataToSend, false);
				} else {
					if(modalShow === 'action_done') {
						updateMeetingModal(dataCurrentMeeting);
					} else {
						// hide main details modal
						$helpersModal.addClass('hide-meeting-modal');
					}
				}
			});
		});

		openOverLimitationModal = function (Message) {
			$('#js-modal-over-limitation-content').html('');
			$('#js-modal-over-limitation').modal('show');
			$('#js-modal-over-limitation').css('z-index',1041);
			$.ajax({
				method: 'GET',
				url: '/office/partials-views/char-popup/modal-over-limitation.php',
				data: {
					text: Message,
					isPayment: true
				},
				success: function (content) {
					$('#js-modal-over-limitation-content').html(content);
				}
			});
		}

		// Green button on the cancellation policy will make the charge and will change the status to ‘completed’.
		$helpersModal.on('click', '.' + settings.btnChargedClient, function () {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			const optionsChargedInp = $modal.find(settings.radioOptionsCharged + ':checked');
			const idSubscription = optionsChargedInp.val();

			// check if customer has subscriptions
			if (dataCurrentMeeting.customer.hasOwnProperty('MemberShipText')) {
				for (let item of dataCurrentMeeting.customer.MemberShipText.data) {
					if (idSubscription == item.Id) {
						// check if selected subscription has restrictions
						if (item.restriction.Status != 1) {
							openOverLimitationModal(item.restriction.Message);
							return;
						}
						break;
					}
				}
			}

			chargedOnMeetingAction($el);
		});

		// Green button on the cancellation policy will make the charge and will change the status to ‘completed’.
		$helpersModal.on('click', '.' + settings.btnOpenNextPopup, function() {
			const $el = $(this);
			buildModalHelpers($el.attr('data-type-to'));
		});

		$helpersModal.on('click', '.' + settings.btnOpenNextPopup, function() {
			// const modalShow = dataCurrentMeeting.show_not_arrived_policy === true ? 'cancellation_policy_not_arrived' : 'meeting_make_sure_not_arrived';
			// debugger;//todo6
			if(dataToSend === null && dataToSend === undefined ) {
				dataToSend = {
					action: 'cancelMeeting',
					id: dataCurrentMeeting.id,
					actStatus : 2
				};
			}

			return false;
		});

		// Red button on the canceling with a reason select
		$helpersModal.on('click', '.' + settings.btnCancelMeetingClass, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			const $modalSelect = $modal.find('select');

			if ($modalSelect.length > 0 && $modalSelect.attr('required') === 'required' && $modalSelect.val() === null) {
				setErrorOnEl($modalSelect.siblings('.sel__placeholder'));
				return false;
			}
			// debugger;//todo1 ->ביטול
			dataToSend = {
				action: 'cancelMeeting', // action to check what user start to do
				actStatus : 1,
				id: $modalSelect.attr('data-id'),
				[$modalSelect.attr('name')]: $modalSelect.val()
			};

			//const meetingRepeatType = $meetingModal.find(settings.selectOptions).attr('data-meeting-repeat-type');
			const meetingRepeatType = dataCurrentMeeting['repeat_type'];
			if (meetingRepeatType === settings.repeatRegularType || meetingRepeatType === settings.repeatServelType) {
				buildModalHelpers($el.attr('data-type-to'));
			} else {
				beforeRequestCancelMeeting($modal);
			}
		});
		// Red button to reject the meeting request (will change the meeting status to rejected)
		$helpersModal.on('click', '.' + settings.btnOrderRejection, function() {
			const $el = $(this);
			if (dataCurrentMeeting === undefined) {
				return false;
			}

			const blockCustomer = $('[name="' + settings.blockCustomerFromOrderingName + '"]').prop('checked');

			dataToSend = {
				action: 'rejectMeeting', // action to check what user start to do
				id: dataCurrentMeeting.id,
				[settings.blockCustomerFromOrderingName]: blockCustomer
			};

			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $el.closest(settings.modal), function (res) {
				meetingSidebarManage.getUsersToApprove()
				if (ifSidebarOpened()) {
					let ids = [dataCurrentMeeting.id];
					if (blockCustomer) {
						ids = res.ids;
					}

					for (const id of ids) {
						const $tabEl = $('#approvalManageMeetingsContainer').find('.sidebar--manage-item[data-id="' + id + '"]');
						$tabEl.slideUp('fast');
					}

				}
				buildModalHelpers($el.attr('data-type-to'));
			});
		});
		// Waiting for approval meetings by clicking on the black button on the sidebar.
		$(settings.modal).on('click', '.' + settings.btnToApprove, function() {
			const $el = $(this);
			let $elId;
			if ($el.attr('data-type') === 'all') {
				$elId = 'all';
			} else {
				$elId = $el.attr('data-id');
			}
			if ($elId === undefined || $elId === '') {
				return false;
			}

			dataToSend = {
				action: 'approveMeeting',
				ids: $elId
			};
			dataCurrentMeeting = {};
			dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $helpersModal, function() {
				meetingSidebarManage.getUsersToApprove();
				buildModalHelpers('action_done');
				if ($el.attr('data-type') === 'all') {
					meetingSidebarManage.hideApprovalTab();
				} else {
					const $elContent = $el.closest(settings.modalContent);
					$elContent.slideUp('fast');
				}
			});
		});
		// Toggling the `.active` state on the `.sel`.
		$(settings.modal).on('click .', selClass, function() {
			const $el = $(this);
			const $elBox = $el.find(selBoxClass);
			const $modal = $el.closest(settings.modalContent);
			const $modalBody = $modal.find('.modal-body');

			if ($el.hasClass(selSettings.classDisabled)) {
				return false;
			}

			if ($el.closest(settings.modal).find(selClass).length > 1) {
				$el.closest(settings.modal).find(selClass).not($el[0]).removeClass(selSettings.classActive).find(selBoxClass).css('display', 'none');
			}

			const $elHeight = $el.innerHeight();
			const elPosition = $el.offset();
			const top = elPosition.top + $elHeight + 5;
			const toTop = $elBox.height() + 16 + $elHeight < $modal.innerHeight() && top + $elBox.height() > $modal.offset().top + $modal.innerHeight();

			// console.log(toTop, 'toTop', $elBox.height() + 16 + $elHeight, '<', $modal.innerHeight(), '&&', top + $elBox.height(), '>', $modal.offset().top + $modal.innerHeight());

			// if (toTop) {
			// 	$elBox.addClass('to-top');
			// } else {
			// 	$elBox.css({
			// 		position: 'fixed',
			// 		top: top,
			// 		left: elPosition.left,
			// 		maxWidth: $el.width()
			// 	});
			// }

			if (!toTop && checkHeight($modalBody) && !$modalBody.hasClass('modal-open')) {
				$modalBody.addClass('modal-open');
			} else {
				$modalBody.removeClass('modal-open');
			}

			$el.toggleClass(selSettings.classActive);
			if ($el.hasClass(selSettings.classActive)) {
				beforeOpenDropdown($el[0]);
				$elBox.slideDown('fast');
			} else {
				$elBox.css('display', 'none');
			}
		});
		// Toggling the `.selected` state on the options.
		$(settings.modal).on('click', selBoxOptionsClass, function() {
			const $el = $(this);
			const elHtml = $el.html();
			const index = $el.index();

			$el.siblings(selBoxOptionsClass).removeClass(selSettings.classSelected);
			$el.addClass(selSettings.classSelected);

			const $currentSel = $el.closest('.sel');
			const select = $currentSel.children('select');
			const selectIndex = select.find('option:first-child').prop('disabled') ? index + 1 : index;
			const newMeetingStatus = select.find('option').eq(selectIndex).attr('value');

			// after clicking on the 'not arrived' if is before start, not can change to not arrived, can cancel meeting, open popup on this.
			if(select.attr('name') === 'meetingStatus'
				&& newMeetingStatus === settings.statusNotArrivedId
				&& moment().isBefore(dataCurrentMeeting.start)
			) {
				buildModalHelpers('not_arrived_before_start');
				return false;
			}
			// after clicking on the 'not arrived' option there will be a popup opened, asking the user if make the charge according to the cancelation policy.
			if (select.attr('name') === 'meetingStatus'
				&& newMeetingStatus === settings.statusNotArrivedId
				&& settings.statusNotArrivedId !== dataCurrentMeeting.status) {
				const modalShow = dataCurrentMeeting.show_not_arrived_policy === true ? 'cancellation_policy_not_arrived' : 'meeting_make_sure_not_arrived';
				// debugger;//todo2
				dataToSend = {
					action: 'cancelMeeting',
					id: dataCurrentMeeting.id,
					actStatus : 2
				};
				buildModalHelpers(modalShow);
				return false;
			}

			if ($currentSel.attr('data-chosen') !== 'false') {
				$currentSel.children(selPlaceholderClass).empty();
				$currentSel.children(selPlaceholderClass).prepend($('<div>', {
					class: $el.attr('class'),
					html: elHtml
				}));
			}

			select.prop('selectedIndex', selectIndex).change();
		});
		// Toggling content when change a type of a cancellation meeting
		$helpersModal.on('change', settings.radioCancelRepeat, function() {
			const $el = $(this);
			toggleVisibility($('[data-context="' + $el.attr('id') + '"]'));
			toggleHeight($el.closest(settings.modal));
		});
		// Creating of helpers modal when click to "Options" button
		$(settings.modal).on('change', settings.selectOptions, function() {
			const $el = $(this);
			const $meetingId = $el.attr('data-meeting-id');
			if ($helpersModal.length === 0 || $meetingId === undefined) {
				return false;
			}

			const elVal = $el.val();
			setCurrentMeetingObj($meetingId);
			if (elVal === 'meeting_edit_details') {
				populateFields.meeting.populate(dataCurrentMeeting)
				return false;
			} else if (elVal === 'meeting_schedule_new') {
				populateFields.meeting.populate(dataCurrentMeeting, true);
				return false;
			} else if (elVal === 'edit_meeting') {
				eventClick('/office/ajax/MeetingDetails.php', $meetingId);
				return false;
            } else if (elVal === 'activate_meeting_status_np') {
                dataToSend = {
                    action: 'changeStatus',
                    id: $meetingId,
                    status: settings.statusOrderedId,
                    oldStatus: dataCurrentMeeting.status,
                };
                beforeRequestChangeStatus(dataToSend, $el.closest(settings.modal));
                return false;
			}

			if (elVal === 'change_to_not_arrived') {
				// debugger//todo3
				const modalShow = dataCurrentMeeting.show_not_arrived_policy === true ? 'cancellation_policy_not_arrived' : 'meeting_make_sure_not_arrived';
				dataToSend = {
					action: 'cancelMeeting',
					id: dataCurrentMeeting.id
				};
				buildModalHelpers(modalShow);
				return false;
			}

			buildModalHelpers(elVal);
		});

		// toggling cancellation policy modal while clicking "not arrrived btn"
		$(settings.modal).on('click', settings.btnChangeToNotArrived, function() {
			const $meetingId = $(this).attr('data-meeting-id');
			if ($helpersModal.length === 0 || $meetingId === undefined) return false;
			setCurrentMeetingObj($meetingId);
			// debugger;//todo4
			const modalShow = dataCurrentMeeting.show_not_arrived_policy === true ? 'cancellation_policy_not_arrived' : 'meeting_make_sure_not_arrived';
			dataToSend = { action: 'cancelMeeting', id: dataCurrentMeeting.id, actStatus : 2 };
			buildModalHelpers(modalShow);
		});

		// Toggling content depends on the series dropdown for a cancellation meeting
		$helpersModal.on('change', settings.selectSeriesDropdown, function() {
			const $el = $(this);
			const $modal = $el.closest(settings.modal);
			const showContext = $el.find('option:selected').attr('data-show');

			if ($el.val() === 'all') {
				$el.parent().next().find('[data-context]').addClass(settings.hideClass);
			}
			if (showContext === undefined || $modal.find('[data-context="'+showContext+'"]').length === 0) {
				return false;
			}

			toggleVisibility($modal.find('[data-context="'+showContext+'"]'));
			toggleHeight($modal);
		});
		// Changing of meeting status
		$meetingModal.on('change', settings.selectStatus, function() {
			const $el = $(this);
			dataToSend = {
				action: 'changeStatus',
				id: $el.attr('data-id'),
				status: $el.val(),
				oldStatus: dataCurrentMeeting.status,
			};
			beforeRequestChangeStatus(dataToSend, $el.closest(settings.modal));
		});
	};

	 beforeOpenDropdown = function($el) {
		const dropdown = $el.querySelector(selBoxClass),
			container = $el,
			offset = container.getBoundingClientRect(),
			height = container.offsetHeight,
			width = $el.offsetWidth,
			dropHeight = $(dropdown).height(),
			modal = $el.closest('.modal-content') || document.body,
			windowBounding = modal.getBoundingClientRect(),
			viewPortRight = windowBounding.right,
			viewportBottom = windowBounding.bottom,
			modalStyle = window.getComputedStyle(modal);

		let dropLeft = offset.left,
			dropTop = offset.top + height,
			dropTopWithoutHeight = offset.top - dropHeight,
			viewPortTop = windowBounding.top,
			enoughRoomBelow = dropTop + dropHeight <= viewportBottom,
			enoughRoomAbove = modalStyle.position === 'fixed' ? viewPortTop > dropTopWithoutHeight : dropTopWithoutHeight >= viewPortTop,
			dropWidth = dropdown.offsetWidth;

		const enoughRoomOnRight = function() {
				return dropLeft + dropWidth <= viewPortRight;
			},
			enoughRoomOnLeft = function() {
				return offset.left + viewPortRight + container.offsetWidth  > dropWidth;
			},
			above = !enoughRoomBelow && enoughRoomAbove;

		// console.log("below / droptop:", dropTop, "dropHeight", dropHeight, "sum", (dropTop+dropHeight)+" viewport bottom", viewportBottom, "enough?", enoughRoomBelow);
		// console.log("above / offset.top", offset.top, "dropHeight", dropHeight, "top", (offset.top-dropHeight), "scrollTop", modal.top, "enough?", enoughRoomAbove);

		if (!enoughRoomOnRight() && enoughRoomOnLeft()) {
			dropLeft = offset.left + container.offsetWidth - dropWidth;
		}

		let css =  {
			position: 'fixed',
			left: dropLeft + 'px',
			width: width + 'px'
		};

		if (modalStyle.position === 'fixed') {
			css.zIndex = parseInt(window.getComputedStyle(dropdown).zIndex) + 2;
		}

		if (above) {
			css.bottom = `calc(${window.innerHeight - offset.top - 2}px + 0.5rem)`;
			css.top = 'auto';
		} else {
			if (modalStyle.position === 'fixed') {
				dropTop = dropTop - viewPortTop;
			}
			css.top = `calc(${dropTop}px + 0.5rem)`;
			css.bottom = 'auto';
		}

		 $(dropdown).css(css);
	};

	ifSidebarOpened = function() {
		return $(settings.sidebarManageMeetings).hasClass('opened');
	};

	setCurrentMeetingObj = function($id) {
		if ((dataCurrentMeeting === undefined || dataCurrentMeeting === null || Object.keys(dataCurrentMeeting).length === 0)
			|| $(settings.sidebarManageMeetings).hasClass('opened')) {
			dataCurrentMeeting = meetingSidebarManage.getCurrentMeeting($id);
		}
	};

	dataSendParams = function(url, data, $modal = undefined, callback = undefined) {
		if ($modal !== undefined) {
			loaderModal($modal);
		}

		fetchData(url, data, $modal).then((res) => {
			if (res !== undefined) {
				if (res.Status === 'Error' || res.status === 'error' || !res.success) {
					console.log('ERROR!!!');
					errorCallback(res, $modal);
					return false;
				}
				if (callback !== undefined) {
					callback(res);
				}
				if (location.pathname === '/office/DeskPlanNew.php') {
					GetCalendarData();
				}
			}
		}).catch((error) => {
			errorCallback(error, $modal);
		});
	};

	setErrorOnEl = function($el, allEl = false) {
		if ($el.length > 1) {
			$el.each(function () {
				if ($(this).val() === '' || allEl) {
					$(this).addClass(settings.redClass);
				}
			});
		} else {
			$el.addClass(settings.redClass);
		}

		setTimeout(function() {
			if ($el.hasClass(settings.redClass)) {
				$el.removeClass(settings.redClass);
			}
		}, 2000);
	};

	hideSidebarElement = function (dataToSend, showModal = true) {
		if(showModal) {
			buildModalHelpers('meeting_cancel_success');
		}
		const $elSidebarBox = $('#openedManageMeetingsContainer').find('.sidebar--manage-item[data-id="' + dataToSend.id + '"]');
		if ($elSidebarBox.length > 0) {
			// hide an opened meeting box after change status at the options button at sidebar
			$elSidebarBox.slideUp('fast', function () {
				const itemDate = $elSidebarBox.data('date');
				// if the last for the date
				if ($('.sidebar--manage-item-' + itemDate).length === 1) {
					// delete date title
					$('.bsapp--divider-' + itemDate).slideUp('fast', function () {
						$('.bsapp--divider-' + itemDate).remove();
					});
				}

				// delete item
				$elSidebarBox.remove();
			});
		}
	};

	beforeRequestChangeStatus = function(dataToSend, $modal) {
		if (dataToSend.action !== 'changeStatus' || dataToSend.status === dataCurrentMeeting.status) {
			return false;
		}

		dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function() {
			if (ifSidebarOpened()) {
				hideSidebarElement(dataToSend);
				buildModalHelpers('action_done');
			} else if ($modal.attr('id') === $helpersModal.attr('id')) {
				$meetingModal.find(selBoxClass).css('display', 'none');
				loaderModal($meetingModal);
				$modal.addClass('hide-meeting-modal').modal("hide");
			} else {
				$modal.modal("hide");
			}
		});
	};

	beforeRequestCancelMeeting = function($modal) {
		if (dataToSend.action !== 'cancelMeeting') {
			return false;
		}

		if (dataCurrentMeeting.show_cancellation_policy !== undefined && dataCurrentMeeting.show_cancellation_policy === true && dataToSend.cancellationPolicy === undefined && (dataToSend.repeatType === undefined || dataToSend.repeatType === 'single')) {
			buildModalHelpers('cancellation_policy');
			return false;
		}
		dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function () {
			// --IMPORTANT--  delete when statuses will be updated from DB
			if(moment().isAfter(dataCurrentMeeting.start)) {
				buildModalHelpers('action_done');
				updateMeetingModal(dataCurrentMeeting);
			} else {
				buildModalHelpers('meeting_cancel_success');
			}
			// --IMPORTANT-- end
		});
	};

	checkHeight = function($el) {
		if ($el.find('> .d-flex').length === 0) {
			return false;
		}
		return $el[0].clientHeight < $el.find('> .d-flex')[0].clientHeight;
	};

	toggleHeight = function($modal) {
		const $modalBody = $modal.find(settings.modalContent).find('.modal-body');
		if ($modalBody.find('> .d-flex').length > 0 && $modalBody.find('> .d-flex').hasClass('justify-content-center')) {
			$modalBody.find('> .d-flex').css('height', (checkHeight($modalBody) ? 'auto' : '100%'));
		}
	};

	toggleVisibility = function($el) {
		$el.removeClass(settings.hideClass).siblings().addClass(settings.hideClass);
	};

	fetchData = async function(url = '', data = {}, modal = undefined) {
		try {
			const response = await fetch(url, {
				method: 'POST',
				mode: "same-origin",
				credentials: "same-origin",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(data)
			});
			const contentType = response.headers.get('content-type');
			if (!contentType || !contentType.includes('application/json')) {
				throw new TypeError("Oops, didn't get JSON");
			}
			if (!response.ok) {
				throw new Error('Something went wrong');
			}
			return await response.json(); // parse JSON response
		} catch (error) {
			console.error('[fetchData] error:', error);
			errorCallback(error, modal);
		}
	};

	loaderModal = function($modal) {
		$modal.find(settings.modalLoader).removeClass(settings.hideClass);
		$modal.find(selClass).addClass(selSettings.classDisabled);
		$modal.find(settings.selectOptions).addClass(selSettings.classDisabled);
		$modal.find(settings.payBtn).parent().addClass(selSettings.classDisabled);
	};

	clearModal = function(modal) {
		modal.find(settings.modalContent).html('<div class="bsapp-overlay-loader js--loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>' +
			'<div class="modal-header">' +
			'  <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
			'    <i class="fal fa-times"></i>' +
			'  </button>' +
			'</div>' +
			'<div class="modal-body"></div>' +
			'<div class="modal-footer"></div>');
		loaderModal(modal);
	};

	buildModalHelpers = function(type) {
		if (type === undefined) {
			return false;
		}
		const data = buildContentHelpers(type);
		modalCallback($helpersModal, $helpersHandlebarModal, data);
		if (type === 'meeting_cancel_success' && dataCurrentMeeting._helpersStepPrev === undefined) {
			setTimeout(function () {
				if (dataToSend.action !== undefined && (dataToSend.action === 'createPayment' || dataToSend.action === 'cancelMeeting')) {
					$helpersModal.addClass('hide-meeting-modal');
					$meetingModal.modal("hide");
				}
				$helpersModal.modal("hide");
			}, 5000);
		}
	};

	buildContentHelpers = function(type) {
		clearModal($helpersModal);
		//console.log('[buildContentHelpers]', type, dataCurrentMeeting);

		$helpersModal.removeClass('hide-meeting-modal').modal("show");
		$helpersContent.empty();
		if (typeof lang !== 'function') {
			return false;
		}

		const meetingSelectOptions = $meetingModal.find(settings.selectOptions);
		const meetingId = dataCurrentMeeting !== undefined && dataCurrentMeeting.id !== undefined ? dataCurrentMeeting.id : meetingSelectOptions.attr('data-meeting-id');
		const isBeta = dataCurrentMeeting !== undefined && dataCurrentMeeting.isBeta !== undefined && dataCurrentMeeting.isBeta; //todo is beta change after cart not beta (remove this)

		let select = [],
			paragraphBoldArr = [],
			buttonsArr = [],
			option = {},
			data = {},
			optionsSubscriptionToCharged = [];

		const content = getContentHelpers(type);
		if (content.title === null) {
			return false;
		}

		if (content.selectedArray !== undefined && content.selectedArray.length > 0) {
			$.each(content.selectedArray, function (index, el) {
				option = {
					id: el.id,
					selected: el.id === '' && index === 0 ? true : false,
					disabled: el.id === '' ? true : false,
					text: el.langText !== undefined ? lang(el.langText) : el.text
				}
				select.push(option);
			});
		}
		if (content.optionsChargedArray !== undefined && content.optionsChargedArray.length > 0 && (!content.isRandomClient)) {
			$.each(content.optionsChargedArray, function (index, el) {
				option = {
					id: el.id,
					checked: index === 0 ? true : false,
					text: el.langText !== undefined ? lang(el.langText) : el.text
				}
				optionsSubscriptionToCharged.push(option);
			});
		}
		if (content.paragraphBold.length > 0) {
			content.paragraphBold.forEach(el => {
				if (type === 'meeting_cancel_success' && dataToSend.action !== undefined) {
					if (dataToSend.action === 'createPayment'
						|| (dataToSend.action === 'cancelMeetingWithCharge')
						|| (dataToSend.action === 'chargedOnMeeting')) {
						el = 'billing_successful';
					} else if (dataToSend.action === 'cancelDocument') {
						el = 'action_done';
					}
				}

				let langText = lang(el);
				if (langText !== undefined && langText !== '') {
					if (type == 'activate_meeting_status' && dataCurrentMeeting.docs !== undefined && dataCurrentMeeting.docs !== null && dataCurrentMeeting.docs.length === 1
						&& langText.indexOf('{number}') > -1) {
						langText = langText.replace("{number}", setDocTypeLink(dataCurrentMeeting.docs[0]));
					}
					if (type == 'cancellation_policy_not_arrived' && dataCurrentMeeting.show_not_arrived_policy) {
						if (dataCurrentMeeting.not_arrived_share) {
							langText = langText.replace("{{percent}}", Math.round(parseInt(dataCurrentMeeting.not_arrived_share)));
							langText = langText.replace("{{amount}}", Math.round((dataCurrentMeeting.price_total * (dataCurrentMeeting.not_arrived_share / 100)) * 100) / 100);
						} else langText = lang('cancel_policy_error')
					}
					if (type == 'cancellation_policy' && dataCurrentMeeting.show_cancellation_policy) {
						if (dataCurrentMeeting.cancellation_share) {
							langText = langText.replace("{{percent}}", Math.round(parseInt(dataCurrentMeeting.cancellation_share)));
							langText = langText.replace("{{amount}}", Math.round((dataCurrentMeeting.price_total * (dataCurrentMeeting.cancellation_share / 100)) * 100) / 100);
						} else langText = lang('cancel_policy_error')
					}
				}
				paragraphBoldArr.push(langText);
			});
		}
		const linkBtnObj = content.linkBtn;
		if (linkBtnObj !== undefined && linkBtnObj.text !== undefined) {
			linkBtnObj.text = lang(linkBtnObj.text);
		}
		const showCheckbox = content.showCheckbox;
		if (showCheckbox !== undefined && showCheckbox.label !== undefined) {
			showCheckbox.label = lang(showCheckbox.label);
		 }

		if (content.buttonsArr !== undefined && content.buttonsArr.length > 0) {
			$.each(content.buttonsArr, function (index, el) {
				el.text = el.text !== undefined ? lang(el.text) : null;
				buttonsArr.push(el);
			});
		}
		let iconLinksArr = content.iconLinks;
		if (iconLinksArr !== undefined && iconLinksArr.length > 0) {
			iconLinksArr = iconLinksArr.map(obj => {
				if (obj.text !== undefined) {
					return {...obj, text: lang(obj.text)};
				}
				return obj;
			});
		}
		data = {
			id: meetingId || null,
			modalType: type,
			title: content.title !== null ? lang(content.title) : null,
			note: content.note !== undefined ? lang(content.note) : null,
			noteAfter: content.noteAfter !== undefined ? lang(content.noteAfter) : null,
			noteRed: content.noteRed !== undefined ? lang(content.noteRed) : null,
			paragraph: content.paragraph !== undefined ? lang(content.paragraph) : null,
			paragraphBoldHtml: paragraphBoldArr.length > 0 ? paragraphBoldArr : null,
			hasButtons: buttonsArr.length > 0 || linkBtnObj !== undefined,
			isSmallBody: buttonsArr.length == 2 && content.title !== null,
			buttonsArr: buttonsArr !== undefined ? buttonsArr : null,
			linkBtnObj: linkBtnObj !== undefined ? linkBtnObj : null,
			iconLinksArr: iconLinksArr !== undefined ? iconLinksArr : null,
			selectedName: content.selectedName !== undefined ? content.selectedName  : null,
			imageSrc: content.imageSrc !== undefined ? content.imageSrc : null,
			iconColor: content.iconColor,
			iconClassName: content.iconClassName,
			canBeRepeated: content.isRepeat,
			chargedClient: content.chargedClient ?? false,
			isCancel: content.isCancel,
			priceCharged: content.priceCharged ?? null,
			useCenter: content.isRepeat || content.chargedClient,
			hasToken: content.hasToken,
			items: select,
			itemsToCharged: optionsSubscriptionToCharged,
			showCheckbox,
			isRandomClient: content.isRandomClient ?? false,
			isBeta: isBeta //todo is beta change after cart not beta (remove this)
		};
		return data;
	};

	setDocTypeLink = function(data) {
		const tinyWidth = $window.width() < 1025 ? $window.width() - 34 : 750;
		const linkClass = settings.docsLinkPopupClass + (data.Refound === '1' ? ' red' : '');
		return '<a class="' + linkClass + '" href="javascript:void(0);" ' +
            'onclick="TINY.box.show({iframe:\'/office/PDF/Docs.php?DocType=' + data.TypeDoc + '&amp;DocId=' + data.TypeNumber + '\',' +
			'boxid:\'frameless\',width:' + tinyWidth + ',height:470,fixed:false,maskid:\'bluemask\',maskopacity:40,' +
            'closejs:function(){}})">' + data.TypeNumber + '</a>';
	};

	getContentHelpers = function(type) {
		let content = {
			title: type,
			buttonsArr: [],
			paragraphBold: []
		};

		if (!dataCurrentMeeting) {
			console.error('[meetingDetailsModule.getContentHelpers] there is no dataCurrentMeeting');
			content.paragraph = 'error_oops_something_went_wrong';
			return content;
		}

		const meetingStatus = dataCurrentMeeting.status;
		switch (type) {
			case 'order_rejection':
				content.paragraphBold.push('confirm_cancel_order');
				content.showCheckbox = {
					label: 'block_customer_ordering_again',
					id: settings.blockCustomerFromOrderingName,
					checked: false
				};
				content.buttonsArr.push({
					text: 'back_new_add_credit',
					class: 'btn--light ' + settings.btnCloseModalClass
				});
				content.buttonsArr.push({
					text: 'meeting_cancel_btn',
					class: 'btn--red ' + settings.btnOrderRejection,
					typeTo: 'meeting_cancel_success'
				});
				break;

			case 'meeting_pay_modal':
				if (!dataCurrentMeeting.customer) {
					console.error('[meetingDetailsModule.getContentHelpers] meeting_pay_modal: no customer data');
					content.paragraph = 'error_oops_something_went_wrong';
					break;
				}
				const customerHasCardToPay = dataCurrentMeeting.customer.token !== undefined;
				let paymentUrl = '';
				if( dataCurrentMeeting.isBeta) { //todo keep only this after cart not beta
					if(dataCurrentMeeting.docs && dataCurrentMeeting.docs.length > 0) {
						// debugger;//todo
						const docId = (dataCurrentMeeting.docs[0].DocsId === undefined) ? dataCurrentMeeting.docs[0].id : dataCurrentMeeting.docs[0].DocsId
						paymentUrl = window.location.origin + '/office/checkout.php?docId=' + docId;
					} else {
						paymentUrl = window.location.origin + '/office/cart.php?u=' + dataCurrentMeeting.customer.id + '&debt=' + dataCurrentMeeting.clientActivityId;
					}
				} else {
					paymentUrl = window.location.origin + '/office/ClientProfile.php?u=' + dataCurrentMeeting.customer.id + '&client_activity=' + dataCurrentMeeting.clientActivityId + '#user-pay';//remove this after cart not beta
				}
				content.title = '';
				content.linkBtn = {
					url: paymentUrl,
					text: 'meeting_pay_modal_link',
					class: !customerHasCardToPay ? 'btn-success' : null
				};
				//todo- false after cart not beta //todo-bp-909 (cart) remove-beta - remove this
				if (!dataCurrentMeeting.isBeta && customerHasCardToPay) {
					paymentUrl = dataCurrentMeeting.customer.token.payment_url !== undefined ? dataCurrentMeeting.customer.token.payment_url : paymentUrl;
					content.buttonsArr.push({
						text: 'meeting_pay_modal_btn',
						class: 'btn-success ' + settings.btnCreatePayment,
						typeTo: 'meeting_cancel_success'
					});
				}

				let paymentUrlWhatsText = lang('meeting_pay_whats_text');
				let customerTel = dataCurrentMeeting.customer.phone;
				if (customerTel !== undefined) {
					if (paymentUrlWhatsText !== undefined) {
						if (paymentUrlWhatsText.indexOf('{customer_name}') > -1 && dataCurrentMeeting.customer.name !== undefined) {
							paymentUrlWhatsText = paymentUrlWhatsText.replace('{customer_name}', dataCurrentMeeting.customer.name);
						}
						if (paymentUrlWhatsText.indexOf('{meeting_name}') > -1 && dataCurrentMeeting.full_title !== undefined) {
							paymentUrlWhatsText = paymentUrlWhatsText.replace('{meeting_name}', dataCurrentMeeting.full_title);
						}
						if (paymentUrlWhatsText.indexOf('{meeting_date}') > -1 && dataCurrentMeeting.start !== undefined) {
							paymentUrlWhatsText = paymentUrlWhatsText.replace('{meeting_date}', setTime(dataCurrentMeeting.start) + ', ' + setDate(dataCurrentMeeting.start));
						}
					}
					// todo: uncomment after payment link will be ready
					// content.iconLinks = [
					// 	{
					// 		text: 'copy_link2',
					// 		iconClass: 'fal fa-link',
					// 		url: paymentUrl,
					// 		copyLabel: true
					// 	}
					// ];
					// if(!dataCurrentMeeting.customer.is_random) {
					// 	let whatsappObj = {
					// 			text: 'send_link_whatsapp',
					// 			iconClass: 'fab fa-whatsapp',
					// 			url: 'https://wa.me/' + setPhoneWithCountryCodeWithoutPlus(customerTel) + '?text=' + encodeURIComponent(paymentUrlWhatsText) + '%0a' + encodeURIComponent(paymentUrl)
					// 		};
					// 	content.iconLinks.push(whatsappObj);
					//
					// }
				}
				content.paragraphBold.push('pay_attention');
				//todo- false after cart not beta //todo-bp-909 (cart) remove-beta
				if(!dataCurrentMeeting.isBeta && !dataCurrentMeeting.customer.is_random && dataCurrentMeeting.customer.token) {
					content.paragraphBold.push('meeting_pay_modal_text');
				}
				//todo- false after cart not beta //todo-bp-909 (cart) remove-beta
				dataCurrentMeeting.isBeta ? content.paragraph = 'meeting_pay_modal_text3_cart' : content.paragraph = 'meeting_pay_modal_text3';
				break;

			case 'meeting_charged':
				content.title = 'meeting_charged';
				content.paragraphBold.push('how_charged_cancellation');
				content.paragraphBold.push('choose_one_option');
				content.chargedClient = true;
				content.isRandomClient = !!dataCurrentMeeting.customer.is_random;
				content.priceCharged = dataCurrentMeeting.price_total;
				content.hasToken = !!dataCurrentMeeting.customer.token;
				const matchSubscription = hasSuitableSubscription(dataCurrentMeeting.customer.MemberShipText) ? dataCurrentMeeting.customer.MemberShipText.data : null;
				if (matchSubscription !== null && matchSubscription.length > 0) {
					let option = {};
					content.optionsChargedArray = []
					$.each(matchSubscription, function (index, el) {
						option = {
							id: el.Id,
							text: getTextSubscription(el.ItemText, el.balance, el.dateEnd)
						}
						content.optionsChargedArray.push(option);
					});
				}

				content.buttonsArr.push({
					text: 'back_new_add_credit',
					class: 'btn--light m-0 w-48 ' + settings.btnCloseModalClass
				});
				content.buttonsArr.push({
					text: 'approval',
					class: 'btn-success m-0 w-48 ' + settings.btnChargedClient,
					typeTo: 'meeting_cancel_success'
				});
				break;

			case 'meeting_cancel_reason_options':
				content.selectedName = 'meetingCancelReason';
				content.buttonsArr.push({
					text: 'meeting_cancel_btn',
					class: 'btn--red ' + settings.btnCancelMeetingClass,
					typeTo: 'meeting_cancel_repeat_type'
				});
				content.paragraphBold.push('meeting_sure_text');
				content.selectedArray = [
					{id: '', langText: 'meeting_choose_reason'},
					{id: 0, langText: 'no_reason'},
					{id: 1, langText: 'created_by_mistake'},
					{id: 2, langText: 'customer_not_available'}
				];
				break;
			case 'meeting_cancel_text':
				if (moment().isAfter(dataCurrentMeeting.start))    {
					content.title = 'meeting_cancel_text_started';
					// content.buttonsArr.push({
					// 	text: 'approval',
					// 	class: settings.btnCloseModalClass
					// });
					content.buttonsArr.push({
						text: 'meeting_cancel_btn',
						class: 'btn--red ' + settings.btnOpenNextPopup,
						typeTo: 'meeting_cancel_reason_options'});
					content.buttonsArr.push({
						text: 'change_to_not_arrived',
						typeTo: dataCurrentMeeting.show_not_arrived_policy === true ? 'cancellation_policy_not_arrived' : 'meeting_make_sure_not_arrived',
						class: settings.btnChangeToNotArrivedOption +  ' ' + settings.btnOpenNextPopup
					});
					content.paragraphBold.push('q_cancel_passed_meetings');
				} else {
					content.selectedName = 'meetingCancelReason';
					content.buttonsArr.push({
						text: 'meeting_cancel_btn',
						class: 'btn--red ' + settings.btnCancelMeetingClass,
						typeTo: 'meeting_cancel_repeat_type'
					});
					content.paragraphBold.push('meeting_sure_text');
					content.selectedArray = [
						{id: '', langText: 'meeting_choose_reason'},
						{id: 0, langText: 'no_reason'},
						{id: 1, langText: 'created_by_mistake'},
						{id: 2, langText: 'customer_not_available'}
					];
				}
				break;

			case 'cancellation_policy':
				content.title = 'title_cancellation_policy';
				content.note = 'meeting_cancelation_note';
				content.paragraphBold.push('meeting_cancelation_policy_text_with_price');
				content.noteAfter = 'meeting_cancelation_policy_text2';
				content.buttonsArr.push({
					text: 'meeting_cancelation_policy_btn',
					class: 'btn-success ' + settings.btnOpenNextPopup,
					typeTo: 'cancellation_policy_charged'
				});
				content.buttonsArr.push({
					text: 'meeting_cancelation_policy_btn2',
					class: settings.btnCancelNoCharge
				});
				break;

			case 'cancellation_policy_charged':
				content.title = 'title_cancellation_policy';
				content.paragraphBold.push('how_charged_cancellation');
				content.paragraphBold.push('choose_one_option');
				content.chargedClient = true;
				content.isRandomClient = !!dataCurrentMeeting.customer.is_random;
				content.isCancel = true;
				let isNotArrived = moment().isAfter(dataCurrentMeeting.start);
				let cancellationPolicyShare = isNotArrived ? dataCurrentMeeting.not_arrived_share : dataCurrentMeeting.cancellation_share;
				content.priceCharged = Math.round((dataCurrentMeeting.price_total * (cancellationPolicyShare / 100)) * 100) / 100;
				content.hasToken = !!dataCurrentMeeting.customer.token;
				const matchSubscriptionData = hasSuitableSubscription(dataCurrentMeeting.customer.MemberShipText) ? dataCurrentMeeting.customer.MemberShipText.data : null;
				if (matchSubscriptionData !== null && matchSubscriptionData.length > 0) {
					let option = {};
					content.optionsChargedArray = []
					$.each(matchSubscriptionData, function (index, el) {
						option = {
							id: el.Id,
							text: getTextSubscription(el.ItemText, el.balance, el.dateEnd)
						}
						content.optionsChargedArray.push(option);
					});
				}

				content.buttonsArr.push({
					text: 'back_new_add_credit',
					class: 'btn--light m-0 w-48 ' + settings.btnCloseModalClass
				});
				content.buttonsArr.push({
					text: 'approval',
					class: 'btn-success m-0 w-48 ' + settings.btnCancelWithCharge,
					typeTo: isNotArrived ? 'action_done' : 'meeting_cancel_success'
				});
				break;

			case 'cancellation_policy_not_arrived':
					content.title = 'title_cancellation_policy';
					content.note = 'no_show_popup_title';
					content.paragraphBold.push('meeting_noshow_policy_text_with_price');
					content.noteAfter = 'meeting_cancelation_policy_text2';
					content.buttonsArr.push({
						text: 'meeting_cancelation_policy_btn',
						class: 'btn-success ' + settings.btnOpenNextPopup,
						typeTo: 'cancellation_policy_charged'
					});
					content.buttonsArr.push({
						text: 'meeting_cancelation_policy_btn3',
						class: settings.btnCancelNoCharge
					});
				break;

			case 'meeting_make_sure_not_arrived':
				// if change to not_arrived, cancel meeting.
				content.title = 'not_arrived_popup_title';
				content.paragraphBold.push('not_arrived_popup_description');
				content.noteAfter = 'not_arrived_popup_alert';
				content.iconColor = '#FF0031';
				content.iconClassName = 'fal fa-exclamation-triangle';
					content.buttonsArr.push({
					text: 'not_arrived_popup_button',
					class: 'btn--red ' + settings.btnCancelNoCharge,
					typeTo: 'meeting_cancel_success'
				});
				break;

			case 'not_arrived_before_start':
				content.title = 'meeting_not_arrived_before_start_title';
				content.buttonsArr.push({
					text: 'approval',
					class: settings.btnCloseModalClass
				});
				content.paragraphBold.push('meeting_not_arrived_before_start');
				break;

			case 'meeting_cancel_repeat_type':
				content.title = 'meeting_cancel_text';
				content.buttonsArr.push({
					text: 'save',
					class: 'btn-success ' + settings.btnCancelRepeatMeetingClass
				});
				content.isRepeat = true;
				content.paragraphBold.push('meeting_series_question');
				break;

			case 'meeting_user_in_debt':
				content.title = 'meeting_user_in_debt_title';
				content.buttonsArr.push({
					text: 'meeting_user_in_debt_btn',
					class: ' ' + settings.btnCompleteLeaveInDebtClass
				});
				content.paragraphBold.push('meeting_user_in_debt_text');
				break;

			case 'meeting_cancel_success':
				content.title = false;
				//content.imageSrc = 'https://assets2.lottiefiles.com/packages/lf20_lt47vdfg.json';
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_2df6ovcx.json';
				content.paragraphBold.push('meeting_canceled_success_text');
				break;

			case 'activate_meeting_status_success':
				content.title = false;
				//content.imageSrc = 'https://assets2.lottiefiles.com/packages/lf20_lt47vdfg.json';
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_2df6ovcx.json';
				content.paragraphBold.push('meeting_refund_open_edit');
				break;

				case 'meeting_cancel_success_charged_not_success':
				content.title = false;
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_tpqrbnpl.json';
				content.paragraphBold.push('meeting_canceled_success_text');
				content.noteRed = 'meeting_charged_not_success';
				break;

			case 'meeting_not_arrived_success_charged_not_success':
				content.title = false;
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_tpqrbnpl.json';
				content.paragraphBold.push('meeting_changed_not_arrived');
				content.noteRed = 'meeting_charged_not_success';
				break;

			case 'action_done':
				content.title = false;
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_2df6ovcx.json';
				content.paragraphBold.push('action_done');
				break;

			case 'meeting_cancel_error':
				content.title = false;
				content.imageSrc = 'https://assets5.lottiefiles.com/packages/lf20_tpqrbnpl.json';
				content.paragraphBold.push(dataToSend.action === 'createPayment' || dataToSend.action === 'cancelMeetingWithCharge' || dataToSend.action ===  'chargedOnMeeting' ? 'meeting_cancel_error' : 'action_cancled');
				break;

            case 'activate_meeting_status':
                if (dataCurrentMeeting.client_activities !== undefined) {
                    // meeting_cancel_subscription part
                    content.buttonsArr.push({
                        text: 'yes_cancel',
                        class: 'btn--red ' + settings.btnUnsubscribeFromClass
                    });
                    content.paragraphBold.push('meeting_cancel_subscr_text');
                } else if (dataCurrentMeeting.docs !== null
                    && dataCurrentMeeting.docs !== undefined
                    && (dataCurrentMeeting.debt !== dataCurrentMeeting.price_total || dataCurrentMeeting.isBeta)) { // todo-bp-909 (cart) remove (dataCurrentMeeting.debt !== dataCurrentMeeting.price_total || dataCurrentMeeting.isBeta)
                    // meeting_cancel_document part
                    if (!dataCurrentMeeting.docs) {
                        console.error('[meetingDetailsModule.getContentHelpers] meeting_cancel_document: no documents data');
                        content.paragraph = 'error_oops_something_went_wrong';
                        break;
                    }
                    content.paragraph = 'meeting_cancel_document_text2';
                    content.buttonsArr.push({
                        text: 'meeting_cancel_document_btn',
                        class: 'btn--red ' + settings.btnCancelDocClass,
                        typeTo: 'activate_meeting_status_success'
                    });
                    content.paragraphBold.push(dataCurrentMeeting.docs.length > 1 ? 'meeting_cancel_document_text_all' : 'meeting_cancel_document_text');
                }

                break;

			case 'meeting_registration_subscription':
				if (!dataCurrentMeeting.customer) {
					console.error('[meetingDetailsModule.getContentHelpers] meeting_registration_subscription: no customer data');
					content.paragraph = 'error_oops_something_went_wrong';
					break;
				}
				const suitableSubscriptionData = hasSuitableSubscription(dataCurrentMeeting.customer.MemberShipText) ? dataCurrentMeeting.customer.MemberShipText.data : null;
				if (suitableSubscriptionData !== null && suitableSubscriptionData.length > 1) {
					let option = {};
					content.paragraphBold.push('meeting_register_subscription_multi_text');
					content.selectedName = settings.registerSubscriptionName;
					content.selectedArray = [
						{id: '', langText: 'select_membership'}
					];

					$.each(suitableSubscriptionData, function (index, el) {
						option = {
							id: el.Id,
							text: el.ItemText
						}
						content.selectedArray.push(option);
					});
				} else {
					content.paragraphBold.push('meeting_register_subscription_text');
				}

				content.paragraphBold.push('meeting_register_subscription_text2');
				content.paragraph = 'meeting_register_subscription_note';
				content.buttonsArr.push({
					text: 'meeting_register_subscription_btn',
					class: 'btn-success ' + settings.btnRegisterSubscriptionClass
				});
				break;

			default:
				content.title = null;
				break;
		}

		return content;
	};

	setPhoneWithCountryCode = function(customerTel) {
		return !customerTel.startsWith('+972') ? (customerTel[0] === '0' ? customerTel.substring(1) : customerTel) : customerTel;
	};
	setPhoneWithCountryCodeWithoutPlus = function(customerTel) {
		return customerTel.startsWith('+') ? customerTel.substring(1) : customerTel;
	};
	setPhoneWithLine = function(phone) {
		if (!phone) {return ''}
		const chunkAfter = phone.startsWith('972') ? 2 : 3;
		const substrPhone = phone.substring(0, chunkAfter) + '-' + phone.substring(chunkAfter);
		return phone.startsWith('972') ? '+' + substrPhone : substrPhone;
	};

	sumPrices = function(items, prop){
		return items.reduce( function(a, b){
			return a + parseFloat(b[prop]);
		}, 0);
	};

	buildModal = function(response) {
		//console.log('[buildModal]', meetingAllStatuses, response);

		if (typeof lang !== 'function') {
			return false;
		}

		$meetingContent.empty();
		const data = createMainData(response);
		modalCallback($meetingModal, $meetingHandlebarModal, data);
	};

	createMainData = function(response) {
		dataCurrentMeeting = response;

		// create object for main meetings detail modal
		let statuses = [],
			clientDocs = [],
			status = {},
			docs = {},
			subscription = {},
			data = {};

        const currentCustomer = response.customer;
        if (currentCustomer !== undefined
            && !currentCustomer.is_random
            && currentCustomer.phone !== undefined
            && currentCustomer.phone !== null) {
            currentCustomer.phoneWithCode = setPhoneWithCountryCode(currentCustomer.phone);
            currentCustomer.phoneWithCodeWithoutPlus = setPhoneWithCountryCodeWithoutPlus(currentCustomer.phone);
        }

		const currentStatus = response.status;
		const isCompleted = currentStatus === settings.statusCompletedId;
		const isNotArrived = currentStatus === settings.statusNotArrivedId;

		if (meetingAllStatuses !== undefined) {
			if (isCompleted || isNotArrived) {
				let setCurrentStatus = findCurrent(meetingAllStatuses, currentStatus);
				statuses.push({
					id: setCurrentStatus.id,
					type: setCurrentStatus.type,
					iconClass: setIconForStatus(setCurrentStatus.id),
					color: isCompleted ? "#0089FA" : setCurrentStatus.color,
					translation: setTranslForStatus(setCurrentStatus.id),
					isSelected: setCurrentStatus.id === currentStatus
				});

				// setCurrentStatus = findCurrent(meetingAllStatuses, settings.statusNotArrivedId);
				// statuses.push({
				// 	id: setCurrentStatus.id,
				// 	type: setCurrentStatus.type,
				// 	iconClass: setIconForStatus(setCurrentStatus.id),
				// 	color: setCurrentStatus.color,
				// 	translation: setTranslForStatus(setCurrentStatus.id),
				// 	isSelected: false
				// });

			} else {
				// Add each item to our handlebars.js data
				$.each(meetingAllStatuses, function (index, item) {
					if (item.id === settings.statusCompletedId || item.id === settings.statusPendingId || item.id === settings.statusWaitingId || item.id === settings.statusCanceledId) {
						return;
					}
					if (item.id === settings.statusDoneId
						// && meetingSettings.CloseWithoutInvoice !== "1"
					) {
						return; // now not need status done, this for future
					}
					status = {
						id: item.id,
						type: item.type,
						iconClass: setIconForStatus(item.id),
						color: item.color,
						translation: setTranslForStatus(item.id),
						isSelected: item.id === currentStatus
					}
					statuses.push(status);
				});
			}
		}

		const customerHasSubscription = response.client_activities !== undefined;
		const customerHasActiveSubscription = customerHasSubscription && response.client_activities.Status === settings.subscriptionStatusActive;

		const customerHasDocs = response.docs !== null && response.docs !== undefined;
		let totalPrice = response.price_total !== undefined ? parseFloat(response.price_total) : 0;
		const payPrice = customerHasDocs ? sumPrices(response.docs, "Amount") : 0;

		// need to check actually STATUS
		const showDocs = customerHasDocs && !customerHasActiveSubscription;
		if (showDocs) {
			const accountingProducedText = lang('meeting_accounting_produced');
			// db [docs]
			$.each(response.docs, function (index, item) {
				let textDoc = accountingProducedText;
				if (textDoc !== undefined) {
					if (textDoc.indexOf('{type}') > -1) {
						textDoc = textDoc.replace('{type}', item.TypeTitleSingle !== undefined ? item.TypeTitleSingle : lang('meeting_accounting_produced_default'));
					}
					//we change meeting_accounting_produced key now not use this if statement
					if (textDoc.indexOf('{price}') > -1 && item.Amount !== undefined) {
						textDoc = textDoc.replace('{price}', '<span class="d-inline unicode-plaintext">' + (item.Amount < 0 ? '-' : '') + setPrice(item.Amount)) + '</span>';
					}
					if (textDoc.indexOf('{number}') > -1 && item.TypeNumber !== undefined) {
						textDoc = textDoc.replace('{number}', setDocTypeLink(item));
					}
				}
				docs = {
					id: item.id,
					date: setDate(item.Dates) + (item.Dates !== undefined ? ' || ' + setTime(item.Dates) : null),
					textHtml: textDoc,
					nameHtml: item.display_name !== undefined ? item.display_name : null
				}
				clientDocs.push(docs);
			});
		}

		// let showSubscription = customerHasActiveSubscription;
		let showSubscription = customerHasSubscription;
		if (showSubscription) {
			subscription = {
				title: response.client_activities.ItemText,
				date: response.client_activities.TrueDate ? setDate(response.client_activities.TrueDate, false, '/') : null,
				entries: response.client_activities.entries	&& parseInt(response.client_activities.entries) > 0 ? response.client_activities.entries : null
			};
		}

		const hasSuitSubscription = response.customer !== undefined && hasSuitableSubscription(response.customer.MemberShipText);
		if (hasSuitSubscription) {
			response.customer._currentSubscription = response.customer.MemberShipText.data[0].Id;
		}

		let id = response.id;
		let title = response.full_title || response.title;
		let startDate = response.start;
		let endDate = response.end;
		let template = [];
		let itemTemp = {};

		// for the multiple meetings
		const templatesArr = response.templates;
		if (templatesArr !== undefined && templatesArr.length > 0) {
			//id = templatesArr.map(item => item.id).join(',');
			title = templatesArr.length + ' ' + lang('cal_appointments');
			startDate = templatesArr[0].start;
			endDate = templatesArr[templatesArr.length - 1].end;
			totalPrice = sumPrices(templatesArr, "price_total");
			for (let i = 0; i < templatesArr.length; i++) {
				const item = templatesArr[i];
				itemTemp = {
					id: item.id,
					title: item.title,
					durationStr: item.start != undefined ? setDuration(item.start, item.end) : null,
					price: item.price_total != undefined ? setPrice(item.price_total) : null
				};
				template.push(itemTemp);
			}
		}

        const debtPrice = response.debt;

		// add info to cancel periodic class
		const $classInfo = $('<div></div>').attr({
			id: 'js-class-data',
			'data-classid': id,
			'data-class-status': response.status,
			'data-class-date': setDate(startDate),
			'data-group-number': response.GroupNumber,
			'data-class-type-id': response.ClassTypeId,
		});

		$('.js-remove-regular-assignment .modal-body').find('#js-class-data').remove();
		$('.js-remove-regular-assignment .modal-body').append($classInfo);

		// Gather all meeting data and add to DOM
		data = {
			id,
			title,
			color: response.backgroundColor || "#FFFFFF",
			owner: response.owner || null,
			location: response.location || null,
			calendar: response.calendar_name || null,
			dateDOWStr: startDate != undefined ? dayOfWeekToString(new Date(startDate).getDay()) + ' ' + setDate(startDate) : null,
			dateStr: startDate != undefined ? setDate(startDate, true) : null,
			timeStr: startDate != undefined ? setTime(startDate) : null,
            date: startDate != undefined ? setDate(startDate, false, '-') : null,
			durationStr: startDate != undefined ? setDuration(startDate, endDate) : null,
			priceTotal: totalPrice != undefined ? setPrice(totalPrice) : null,
            priceDebt: debtPrice != 0 ? setPrice(debtPrice) : null,
            showPayBtn: debtPrice != 0 && !customerHasActiveSubscription,
			notShowBtnCancel: isCompleted || isNotArrived,
			notShowBtnEdit: isNotArrived,
			// showBtnInDebt: !isCompleted,
			showBtnInDebtText: !isCompleted && debtPrice > 0,
			// showBtnRegistrationSubscription: clientDocs.length === 0 && hasSuitSubscription,
			showScheduleNew: isCompleted || isNotArrived,
			repeatType: response.repeat_type,
			currentStatus: currentStatus,
			customer: currentCustomer,
			showSubscription: showSubscription,
			showUnCompleteBtnNP: !response.isBeta && isCompleted && totalPrice == debtPrice && !customerHasSubscription,// todo-bp-909 (cart) remove showUnCompleteBtnNP not option
            showUnCompleteBtn: (response.isBeta && response.docs != undefined && response.docs.length > 0) || isCompleted && (totalPrice != debtPrice || customerHasSubscription),// todo-bp-909 (cart) if has doc
			subscription: subscription,
			showDocs: showDocs,
			clientDocs: clientDocs,
			items: statuses,
			template: template
		};

		return data;
	};

	hasSuitableSubscription = function(memberShipText) {
		return memberShipText !== undefined && memberShipText.data.length > 0;
	};

	getTextSubscription = function (textMembership, balance, dateEnd){
		let balanceText = lang('balance_membership');
		let dateEndText = lang('date_end_membership');
		if (balance) {
			if (balanceText !== undefined && balanceText.indexOf('{{balance}}') > -1) {
				textMembership += ' ' + balanceText.replace('{{balance}}', balance);
			}
		} else if (dateEnd && dateEndText !== undefined &&  dateEndText.indexOf('{{date}}') > -1) {
			textMembership += ' ' + dateEndText.replace('{{date}}', dateEnd);
		}
		return textMembership;
	}

	modalCallback = function($modal, $handlebarModal, data) {
		if (typeof Handlebars === 'undefined') {
			return false;
		}

		let source = $handlebarModal.html(),
			template = Handlebars.compile(source);

		$modal.find(settings.modalLoader).addClass(settings.hideClass);
		$modal.find(settings.modalContent).append(template(data));

		if ($modal.find('[data-toggle="tooltip"]').length > 0) {
			$modal.find('[data-toggle="tooltip"]').tooltip();
		}

		if ($modal.find(selClass).length > 0) {
			createFakeDropdown($modal);
		}
	};

	errorCallback = function(XMLHttpRequest = null, $modal = undefined) {
		const message = XMLHttpRequest != null && XMLHttpRequest.message != undefined ? XMLHttpRequest.message : "Something went wrong";
		const showMessage = XMLHttpRequest != null && XMLHttpRequest.showMessage != undefined ? XMLHttpRequest.showMessage : '';
		let $errorModal = $modal === undefined ? $('.modal.fade:visible') : $modal;
		if ($errorModal.length > 1) {
			$errorModal = $errorModal.eq($errorModal.length - 1);
		}

		if ($errorModal.attr('id') === $helpersModal.attr('id')) {
			const type = 'meeting_cancel_error';
			const prevModalType = $helpersModal.find(settings.modalBody).length > 0 && $helpersModal.find(settings.modalBody).attr('type') !== undefined ? $helpersModal.find(settings.modalBody).attr('type') : null;
			const data = buildContentHelpers(type);

            if (showMessage !== '') {
                data.paragraph = showMessage;
            }
			modalCallback($helpersModal, $helpersHandlebarModal, data);

			if (dataToSend.resource === "sidebar" && dataToSend.action === "changeStatus") {
				return false;
			}

			if (prevModalType !== null && prevModalType !== 'meeting_cancel_success') {
				setTimeout(function () {
					buildModalHelpers(prevModalType);
				}, 5000);
			}
		} else {
			charPopup.showError($errorModal.find('.modal-content'), "An error occurred. Please try again", message, false);
			$errorModal.find(settings.modalLoader).addClass(settings.hideClass);
		}
	};

	monthString = function(num) {
		const month = [lang('january'), lang('february'), lang('march'), lang('april'), lang('may'), lang('june'), lang('july'), lang('august'), lang('september'), lang('october'), lang('november'), lang('december')];
		return month[num];
	};

	dayOfWeekToString = function(num) {
		const month = [lang('sunday'), lang('monday'), lang('tuesday'), lang('wednesday'), lang('thursday'), lang('friday'), lang('saturday')];
		return month[num];
	};

	setPrice = function(currenPrice) {
		return '₪' + Math.abs(parseFloat(currenPrice).toFixed(2).replace(/\.0+$/,''));
	};

	setDate = function(date, toString = false, divider = '.') {
		let response = '';
		const dateObj = new Date(date);
		const day = dateObj.getDate();
		const month = dateObj.getMonth();
		if (toString) {
			const monthStr = monthString(month);
			const dayOfWeek = dayOfWeekToString(dateObj.getDay());
			response = lang('day') + ' ' + dayOfWeek + ', ' + day + ' ' + monthStr;
		} else {
			response = ("0" + day).slice(-2) + divider + ("0"+(month+1)).slice(-2) + divider + dateObj.getFullYear().toString().substr(-2);
		}
		return response;
	};

	setTime = function (start, end = undefined) {
		let startD = new Date(start);
		let startMins = startD.getMinutes();
		return startD.getHours() + ':' + (startMins.toString().length == 1 ? '0' + startMins : startMins);
	};

	setDuration = function (start, end = undefined) {
		let startD = new Date(start);
		if (end != undefined) {
			let endD = new Date(end);
			const diffMs = endD.getTime() - startD.getTime();
			if (diffMs < 0) {
				return '';
			}
			const diffMins = Math.abs(diffMs) / 60000;
			const hours = diffMins > 60 ? convertHM(diffMins)[0] : null;
			const minutes = diffMins > 0 ? (diffMins <= 60 ? diffMins : convertHM(diffMins)[1]) : null;

			let durationStr = hours !== null ? hours : '';
			if (hours !== null && minutes !== null) {
				durationStr += ' ' + lang('and') + ' ';
			}
			return durationStr + (minutes !== null ? (minutes.toString().length == 1 ? '0' + minutes : minutes) + ' ' + lang('cal_class_type_minutes') : '');
		}
		return null;
	};

	convertHM = function(num) {
		const hours = (num / 60);
		let nHours = Math.floor(hours);
		const minutes = (hours - nHours) * 60;
		const nMinutes = Math.round(minutes);

		switch (nHours) {
			case 1:
				nHours = lang('hour');break;
			case 2:
				nHours = lang('two_hours');break;
			default:
				nHours = nHours + ' ' + lang('hours');
		}

		return [nHours, nMinutes != 0 ? nMinutes : null];
	};

	setIconForStatus = function(id) {
		const idStr = typeof id != "string" ? id.toString() : id;
		switch(idStr) {
			case settings.statusWaitingId: return "fal fa-pennant";
			case settings.statusOrderedId: return "fal fa-check-circle";
			case settings.statusStartedId: return "fal fa-play";
			case settings.statusCompletedId: return "fal fa-file-alt";
			case settings.statusNotArrivedId: return "fal fa-times-circle";
			case settings.statusDoneId: return "fal fa-badge-check";
			default: return "";
		}
	};

	setTranslForStatus = function (id, type = undefined) {
		const idStr = typeof id != "string" ? id.toString() : id;
		switch(idStr) {
			case settings.statusPendingId: return lang('meeting_pending');
			case settings.statusWaitingId: return lang('meeting_waiting');
			case settings.statusOrderedId: return lang('meeting_ordered');
			case settings.statusStartedId: return (type !== undefined && type === 'short' ? lang('meeting_started_short') : lang('meeting_started'));
			case settings.statusCompletedId: return lang('completed_client_profile');
			case settings.statusNotArrivedId: return lang('did_not_arrive_cal_general');
			case settings.statusDoneId: return lang('meeting_done');
			default: return "";
		}
	};

	createFakeDropdown = function ($modal) {
		/* ===== Logic for creating fake Select Boxes ===== */
		$modal.find(selClass).each(function() {
			const $current = $(this);
			const $select = $current.children('select');
			if ($select.hasClass('created')) {
				return true;
			}

			$select.addClass('created').css('display', 'none');
			const isOptionOne = $current.find('option').length === 1 ? true : false;
			const selectedIndex = $current.find('option:selected').length > 0 ? $current.find('option:selected').index() : 0;
			const selectedOption = $current.find('option').eq(selectedIndex);
			const placeholderHtml = '<span'
				+ (selectedOption.attr('data-icon') !== undefined || (selectedIndex === 0 && !selectedOption.prop('disabled')) ? ' class="' + selSettings.selBoxOptions + '"' : '')
				+ '>'
				+ (selectedOption.attr('data-icon') !== undefined ? '<i class="' + selectedOption.attr('data-icon') + '" style="color:' + selectedOption.attr('data-color') + ';"></i>' : '')
				+ selectedOption.text()
				+ '</span>';

			$current.find('option').each(function(i) {
				const $el = $(this);
				const iconClass = $el.attr('data-icon');
				const color = $el.attr('data-color');
				const isSelected = $el.is(':selected') ? true : false;

				if (i == 0) {
					$current.prepend($('<div>', {
						class: $current.attr('class').replace(/sel/g, selSettings.selBox)
					}));

					$current.prepend($('<div>', {
						class: $current.attr('class').replace(/sel/g, selSettings.selPlaceholder),
						html: placeholderHtml,
						'data-arrow-icon': $current.attr('data-chosen') === 'false' ? $current.attr('data-arrow-icon') : null,
						'data-placeholder': $el.text()
					}));

					$current.prepend($('<div>', {
						class: $current.attr('class').replace(/sel/g, selSettings.selCover)
					}));

					if ($el.prop('disabled') && $el.val() === '') {
						$current.addClass('show-placeholder');
						return;
					}
				}

				$current.children(selBoxClass).append($('<span>', {
					class: $current.attr('class').replace(/sel/g, selSettings.selBoxOptions) + (isSelected ? ' ' + selSettings.classSelected : ''),
					html: '<span>' + (iconClass !== undefined ? '<i class="' + iconClass + '" style="color:' + color + ';"></i>' : '') + $el.text() + '</span>'
				}));
			});

			if (isOptionOne) {
				$current.addClass(selSettings.classDisabled);
			}
		});
	};

	findCurrent = function(array, currId) {
		return array.find(x => x.id === currId);
	};

	loadCalenderBox = function(arg, calendarType = undefined) {
		const extendedProps = arg.event.extendedProps;
		const title = arg.event.title,
			eStartTime = moment(arg.event.start),
			eEndTime = moment(arg.event.end),
			eTimeString = eEndTime.format('HH:mm') + ' - ' + eStartTime.format('HH:mm'),
			owner = (jsTypeOfView == 2 && jsSplitView == 0) ? '' : extendedProps.owner,
			customer = extendedProps.customer,
			meetingStatus = extendedProps.status;

		const isMonth = calendarType === 'month';
		// const classTextColor = meetingStatus !== undefined && meetingStatus === settings.statusNotArrivedId ? ' text-red' : ' text-black';
		const classTextColor = ' text-black';
		const classFontSize = ' bsapp-fs-16';
		const classPIS = isMonth ? ' pis-8' : ' pis-15';
		const mainBgColor = arg.event.backgroundColor;

		if (arg.backgroundColor.length < 9) {
			arg.backgroundColor = arg.backgroundColor + "73";
		}

		let eTopContent = document.createElement('div'),
			eTooltip = document.createElement('article'),
			eGradBottomStatus = document.createElement('div'),
			eGradBottom = document.createElement('div'),
			eGradContentBar = document.createElement('div'),
			eDiffMinutes = $("#calendar-main").hasClass("bsapp-js-agenda-view") ? 60 : eEndTime.diff(eStartTime, 'minutes');

		eTopContent.className = 'd-flex flex-column' + classTextColor + classPIS;
		eGradContentBar.style.backgroundColor = mainBgColor;
		eGradContentBar.classList.add('eGradContent', 'bsapp-grad-right');
		eTopContent.appendChild(eGradContentBar);

		const setStatus = meetingStatus && meetingAllStatuses && meetingAllStatuses.length > 0 ? findCurrent(meetingAllStatuses, meetingStatus) : null;
		const eGradBottomSpan = document.createElement('span');
		const isGradient = isGradientBg(meetingStatus);
		if (setStatus) {
			eGradBottomStatus.classList.add('bsapp-status-tag');
			if (meetingStatus === settings.statusPendingId) {
				eGradBottomStatus.classList.add('flash');
			}
			eGradBottomStatus.style.color = isGradient ? mainBgColor : setStatus.color;
			eGradBottomStatus.style.backgroundColor = isGradient ? mainBgColor + "72" : setStatus.bg;
			eGradBottomSpan.textContent = setTranslForStatus(meetingStatus, 'short');
			eGradBottomStatus.appendChild(eGradBottomSpan);
		}

		// Meeting information popup (when hovering over a meeting box)
		eTooltip.classList.add('eTooltip', 'bsapp-tooltip');
		const $handlebarTooltip = $('#meetingTooltipModal');
		if ($handlebarTooltip.length > 0 && typeof Handlebars !== 'undefined') {
			let payInfoClassName = null, payInfoText = null;
			// payment status or fulfilled with a membership
			if (extendedProps.hasActiveSubscription === true) {
				payInfoText = lang('realized_via_subscription');
			} else if (extendedProps.has_debt === true) {
				payInfoClassName = 'red';
				payInfoText = lang('in_debt') + ' ' + setPrice(extendedProps.has_debtAmount);
			} else if (extendedProps.has_debt === false) {
				payInfoClassName = 'green';
				payInfoText = lang('paid');
			}

			const source = $handlebarTooltip.html(),
				template = Handlebars.compile(source);
			eTooltip.innerHTML = template({
				title,
				priceStr: setPrice(extendedProps.price_total),
				duration: arg.event.start != undefined ? setDuration(arg.event.start, arg.event.end) : null,
				coach: owner,
				payInfoClassName,
				payInfoText,
				customer,
				customerPhoneLine: customer.phone ? setPhoneWithLine(customer.phone.startsWith('+972') ? '0' + customer.phone.replace('+972', '') : customer.phone) : null,
				timeDuration: eTimeString,
				status: {
					html: eGradBottomStatus.innerHTML,
					bg: !isGradient && setStatus ? setStatus.bg : mainBgColor + "72",
					color: !isGradient && setStatus ? setStatus.color : mainBgColor
				}
			});
			if ($(eTooltip).find('[data-toggle="tooltip"]').length > 0) {
				$(eTooltip).find('[data-toggle="tooltip"]').tooltip();
			}
		}

		if (resources && resources.length > 3 && calendarType === 'timeGridWeek') {
			return [eTooltip, eTopContent];
		}

		if (customer && customer.name !== undefined && eDiffMinutes > 50) {
			let eParticipants = document.createElement('h5');
			if (title !== undefined){
				eParticipants.innerHTML = title  + '-' + customer.name;
			} else {
				eParticipants.innerHTML = customer.name;
			}
			eParticipants.className = 'bsapp-text-overflow d-inline';
			eParticipants.setAttribute('client-reg-card', arg.event.id);
			eParticipants.setAttribute('data-group-number', extendedProps.groupNumber);
			eTopContent.appendChild(eParticipants);
		}
		// if (title !== undefined) {
		// 	let eTitle = document.createElement('span');
		// 	eTitle.classList.add('eParticipants', 'bsapp-event-participants', 'bsapp-text-overflow', 'pie-9');
		// 	eTitle.setAttribute('data-group-number', extendedProps.groupNumber);
		// 	eTitle.innerHTML = '<span id="data-class-id' + arg.event.id + '" class="' + classFontSize + '"> ' + title + '</span>';
		// 	eTopContent.appendChild(eTitle);
		// }

		if (!isMonth) {
			if (arg.event.start !== undefined && arg.event.end !== undefined && eDiffMinutes > 20) {
				let eTimeContainer = document.createElement('span');
				eTimeContainer.className = 'bsapp-event-times';
				eTimeContainer.innerHTML = eTimeString;
				eTopContent.appendChild(eTimeContainer);
			}

			if (owner !== undefined && eDiffMinutes > 40) {
				let eOwner = document.createElement('div');
				eOwner.classList.add('bsapp-event-owner', 'bsapp-text-overflow');
				eOwner.innerHTML = owner;
				eTopContent.appendChild(eOwner);
			}

			if (meetingStatus !== undefined) {
				eGradBottom.classList.add('eGradBottom');
				eGradBottom.appendChild(eGradBottomStatus);

				if ((meetingStatus == settings.statusCompletedId || meetingStatus == settings.statusNotArrivedId) && extendedProps.has_debt) {
					let eIcon = document.createElement('div');
					eIcon.classList.add('bsapp-event-icon');
					eIcon.innerHTML = '<i class="fal fa-sack-dollar"></i>';
					eTopContent.appendChild(eIcon);
				}
			}
		}

		return [eTooltip, eTopContent, eGradBottom];
	};

	updateMeetingModal = function(data) {
		//console.log('[updateMeetingModal]', data, dataCurrentMeeting);
		if (dataCurrentMeeting === undefined || settings === undefined) {
			return false;
		}

		const modalOption = settings.meetingModal;
		const $modal = $(modalOption);
		const meetingId = dataCurrentMeeting.id !== undefined ? dataCurrentMeeting.id : $modal.find(settings.selectStatus).attr('data-id');
		fetchData('/office/ajax/MeetingDetails.php', {
			action:'getMeetingData',
			id: meetingId
		}).then((res) => {
			const currentObj = res;
			if (currentObj.length === 0) {
				return false;
			}

			if ($modal.length === 0 || $(modalOption + ':visible').length === 0) {
				return false;
			}


			if (dataCurrentMeeting._helpersStepPrev !== undefined) {
				buildModalHelpers(dataCurrentMeeting._helpersStepPrev);
			}

			buildModal(currentObj);
		}).catch((error) => {
			errorCallback(error, $meetingModal);
		});
	};

	isGradientBg = function(status) {
		return status === settings.statusPendingId || status === settings.statusWaitingId;
	};

	isGrey = function(status) {
		return status === settings.statusCompletedId || status === settings.statusNotArrivedId || status === settings.statusDoneId;
	};

	isMeetingType = function(type) {
		return type === '1'; // --IMPORTANT-- it's necessary to change the type to detect the meeting
	};

	setAllStatus = function(array) {
		if (meetingAllStatuses !== undefined) {
			return false;
		}
		meetingAllStatuses = array
	};

	setMeetingSettings = function (array) {
		if (meetingSettings !== undefined) {
			return false;
		}
		meetingSettings = array;
	};

	eventClick = function(url, id, status = null) {
		if ($meetingModal.length === 0) {
			return false;
		}

		if (status) {
			if (status === settings.statusPendingId) {
				return false;
			} else if (status === settings.statusWaitingId) {
				// open a manage meeting sidebar
				meetingSidebarManage.openSidebar(id);
				return false;
			}
		}

		// Start with a fresh modal content
		clearModal($meetingModal);
		$meetingModal.modal("show");

		fetchData(url, {
			action:'getMeetingData',
			id: id
		}).then((res) => {
			buildModal(res);

		}).catch((error) => {
			errorCallback(error, $meetingModal);
		});
	};

	chargedOnMeetingAction = function ($el) {
		const $modal = $el.closest(settings.modal);
		const optionsChargedInp = $modal.find(settings.radioOptionsCharged + ':checked');
		const idSubscription = optionsChargedInp.val();
		const currentMeetingId = ($meetingModal.find(settings.selectStatus) !== undefined && $meetingModal.find(settings.selectStatus).length !== 0)
			? $meetingModal.find(settings.selectStatus).attr('data-id') : dataCurrentMeeting.id;

		dataToSend = {
			action: 'chargedOnMeeting',
			id: currentMeetingId
		};
		if (idSubscription) {
			if (idSubscription === 'move_cart') {
				window.location.href = window.location.origin + '/office/cart.php?u=' + dataCurrentMeeting.customer.id + '&debt=' + dataCurrentMeeting.clientActivityId;
				return
			}else if(idSubscription === 'move_client_profile') {
				window.location.href = window.location.origin + '/office/ClientProfile.php?u=' + dataCurrentMeeting.customer.id + '&client_activity=' + dataCurrentMeeting.clientActivityId + '#user-pay';
				return
			}
			dataToSend.chargedSubscriptionId = idSubscription;
		}
		dataSendParams('/office/ajax/MeetingDetails.php', dataToSend, $modal, function () {
			if (ifSidebarOpened()) {
				hideSidebarElement(dataToSend);
			} else {
				loaderModal($meetingModal);
				updateMeetingModal(dataCurrentMeeting);
				$modal.modal("hide")
			}
		});
	};

	return {
		init: init,
		eventClick: eventClick,
		setAllStatus: setAllStatus,
		setMeetingSettings: setMeetingSettings,
		updateMeetingModal: updateMeetingModal,
		isGrey: isGrey,
		isGradientBg: isGradientBg,
		isMeetingType: isMeetingType,
		loadCalenderBox: loadCalenderBox,
		createMainData: createMainData,
		createFakeDropdown: createFakeDropdown,
		chargedOnMeetingAction: chargedOnMeetingAction,
	};
})({}, jQuery);

const meetingSidebarManage = (function (module, $) {
	'use strict';

	// Public functions
	let init, getCurrentMeeting, hideApprovalTab, openSidebar, getUsersToApprove;

	// Private general variables
	let isInit = false, $body, $window, settings, selSettings;

	const LIMIT = 10; // limit to load max 20 newest meetings
	const GLOBAL_URL = '/office/js/meeting_edit/sidebarManage'; // path to Controller

	// Private plugin variables
	let $sidebar, $tabContent, $sidebarLoader, $approvalManageContainer, $openedManageContainer;
	let selClass, itemManageClass;
	let openedDateTitle = '', setMeetingStatuses = false, setMeetingSettings = false;

	// Private functions
	let initEvents, fetchData, hideLoader, buildSidebar, buildTab, errorCallback,
		clearSidebar, buildLoadMore, toggleDropdown,setNotificationsMeetingsValue;

	/*============================================================================
	   Initialise the plugin and define global options
	 ==============================================================================*/
	init = function(options) {
		if (isInit) {
			return false;
		}
		// Default settings
		settings = {
			openedClass: 'opened',
			bodyClass: 'show--custom-cover modal-open',
			hideClass: 'd-none',
			withBtnClass: 'with-btn',
			itemClass: 'sidebar--manage-item',
			modalLoader: '.js--loader',
			loadMoreBtn: '.js--load-more',
			modalHeaderDropdown: '.js--toggle-modal-body',
			sidebar: '#sidebarManageMeetings',
			meetingHandlebars: '#meetingTemplateModal',
			meetingSidebarTabHandlebars: '#meetingSidebarTabModal',
			tabContent: '#sidebarTabContent',
			tabManageContainer: '.tab-manage-container',
			approvalManage: '#approvalManageMeetingsContainer',
			openedManage: '#openedManageMeetingsContainer',
			confirmAllBtn: '#confirmAllMeetings',
			openSidebar: '#openManageMeetingSidebar',
			closeSidebar: '[data-modal-close="sidebar"]',
		};

		selSettings = {
			classActive: 'active',
			classSelected: 'selected',
			classDisabled: 'disabled',
			sel: 'sel',
			selBox: 'sel__box'
		};

		// Override defaults with arguments
		$.extend(settings, options);

		// Select DOM elements
		$sidebar = $(settings.sidebar);
		$tabContent = $sidebar.find(settings.tabContent);
		$sidebarLoader = $sidebar.find(settings.modalLoader);
		$approvalManageContainer = $sidebar.find(settings.approvalManage);
		$openedManageContainer = $sidebar.find(settings.openedManage);

		selClass = '.' + selSettings.sel;
		itemManageClass = '.' + settings.itemClass;

		// General Selectors
		$body = $('body');
		$window = $(window);

		initEvents();
		isInit = true;
	};

	initEvents = function() {
		// to hide a pseudo dropdown when scrolling sidebar
		$sidebar.find(settings.tabManageContainer).on('scroll', function() {
			const openedSel = $(this).find(selClass);
			if (openedSel.hasClass(selSettings.classActive)) {
				openedSel.removeClass(selSettings.classActive).find('.' + selSettings.selBox).css('display', 'none');
			}
		});
		// collapse/expand each meeting
		$sidebar.on('click', settings.modalHeaderDropdown, function() {
			toggleDropdown($(this));
		});
		// by clicking load max 20 newest meetings under the last box
		$(settings.sidebar).on('click', settings.loadMoreBtn, function() {
			const $el = $(this);
			const $container = $el.closest(settings.tabManageContainer);
			const type = $container.attr('data-type');
			if (type === undefined) {
				return false;
			}
			$el.html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

			let lastDate = JSON.parse(decodeURIComponent($container.find('.sidebar--manage-item:last').attr('data-json'))).start;
			lastDate = moment(lastDate).format('YYYY-MM-DD');

			// remove
			fetchData('/office/route.php', {
				action: 'meetings/meetingsData',
				type,
				limit: LIMIT,
				lastDate: lastDate,
				page: parseInt($container.attr('data-next-page'))
			}).then((res) => {
				if (!res.success) {
					errorCallback(res.message);
					return false;
				}
				$el.parent().remove();
				buildSidebar(res);

			}).catch((error) => {
				errorCallback(error.message);
			});
		});
		// close sidebar
		$(settings.closeSidebar).on('click', function () {
			$sidebar.removeClass(settings.openedClass);
			$body.removeClass(settings.bodyClass);
		});

		document.addEventListener("mousedown", e => {
			if (e.target === document.querySelector(`${settings.sidebar}.${settings.openedClass}`)) {
				$sidebar.removeClass(settings.openedClass);
				$body.removeClass(settings.bodyClass);
			}
		});

		document.addEventListener("keyup", e => {
			if (e.key == "Escape"
				&& (e.target.closest(`${settings.sidebar}.${settings.openedClass}`) !== null || e.target.classList.contains(settings.openedClass))) {
				$sidebar.removeClass(settings.openedClass);
				$body.removeClass(settings.bodyClass);
			}
		});

		// open sidebar and create request
		$(settings.openSidebar).on('click', function () {
			openSidebar();
		});
	};

	toggleDropdown = function(el) {
		if (el.length === 0) {
			return false;
		}
		el.closest(itemManageClass).toggleClass(settings.openedClass).find('.modal-body').slideToggle();
		$sidebar.find(selClass).removeClass(selSettings.classActive).find('.' + selSettings.selBox).css('display', 'none');
	};

	openSidebar = function(toOpenId = null) {
		if ($sidebar.length === 0 || $(settings.sidebar + ':visible').length === 0) {
			return false;
		}

		$sidebar.focus();
		clearSidebar();
		$sidebar.addClass(settings.openedClass);
		$body.addClass(settings.bodyClass);

		fetchData('/office/route.php', {
			action: 'meetings/meetingsData',
			type: 'all',
			withSettings: !setMeetingStatuses || !setMeetingSettings,
			limit: LIMIT
		}).then((res) => {
			if (!res.success) {
				errorCallback(res.message);
				return false;
			}
			buildSidebar(res);
			hideLoader();

			if (toOpenId) {
				const currentEl = $('#sidebarManageMeetings').find('.tab-pane.show').find('.sidebar--manage-item[data-id="' + toOpenId + '"]');
				if (currentEl.length > 0) {
					toggleDropdown(currentEl.find('.js--toggle-modal-body'));
				}
			}

		}).catch((error) => {
			errorCallback(error.message);
		});
	};

	buildSidebar = function(res) {
		openedDateTitle = '';
		if (!setMeetingStatuses && res.MeetingStatuses !== undefined && res.MeetingStatuses.length > 0) {
			meetingDetailsModule.setAllStatus(res.MeetingStatuses);
			setMeetingStatuses = true;
		}

		if (!setMeetingSettings && res.MeetingSettings !== undefined) {
			meetingDetailsModule.setMeetingSettings(res.MeetingSettings);
			setMeetingSettings = true;
		}

		const notApprovedElms = res.notApproved;
		const openedElms = res.opened;

		if (res.studioAutoApproval !== undefined && parseInt(res.studioAutoApproval) === 1) {
			hideApprovalTab(true);
		} else if (notApprovedElms !== undefined && notApprovedElms.length > 0) {
			$('#' + $(settings.approvalManage).attr('data-type') + 'Manage-tab').parent().show();

			// in this tab, there will be a list of meeting boxes that were ordered by end-users.
			const $confirmAllBtn = $(settings.confirmAllBtn);
			buildTab(notApprovedElms, $approvalManageContainer, res.notApprovedMaxCount);
			if ($approvalManageContainer.children().length === 1) {
				$confirmAllBtn.parent().addClass(settings.hideClass);
				$approvalManageContainer.removeClass(settings.withBtnClass);
			} else {
				$confirmAllBtn.parent().removeClass(settings.hideClass);
				$approvalManageContainer.addClass(settings.withBtnClass);
			}

			if ($approvalManageContainer.parent().hasClass(settings.hideClass)) {
				$approvalManageContainer.parent().removeClass(settings.hideClass);
			}
		}

		if (openedElms !== undefined && openedElms.length > 0) {
			// in this tab, there will be a list of meeting boxes that are in open status and their start time has passed.
			buildTab(openedElms, $openedManageContainer, res.openedMaxCount);
		}

		if ($sidebar.find(selClass).length > 0) {
			meetingDetailsModule.createFakeDropdown($sidebar);
		}
	};

	buildTab = function(response, $container, maxCount = null) {
		if (typeof Handlebars === 'undefined') {
			return false;
		}

		let totalData = [];
		const containerType = $container.attr('data-type');
		const nextPage = $container.attr('data-next-page') !== undefined ? parseInt($container.attr('data-next-page')) : 1;
		const oldCount = $container.find(itemManageClass).length;

		for (let i = 0; i < response.length; i++) {
			const data = meetingDetailsModule.createMainData(response[i]);
			data.isSidebar = true;
			data.items = null;

			if (containerType === 'notApproved') {
				data.showPayBtn = false;
				data.showApprovalBtn = true;
				// will add to OPTIONS button: the edit Meeting popup and  the ‘are you sure you want to reject this meeting?' popup.
				data.showApprovalOptions = true;
			} else if (containerType === 'opened') {
				data.dateStr = false;
				if (openedDateTitle !== data.dateDOWStr) {
					openedDateTitle = data.dateDOWStr;
					data.dateTitle = openedDateTitle;
				}
			}
			data.opened = !i ? "preopen opened" : "";
			data.index = i + oldCount;
			data.type = containerType;
			data.encodeJSON = encodeURIComponent(JSON.stringify(response[i]));
			totalData.push(data);
		}

		const totalObject = {
			type: containerType,
			items: totalData,
			moreBtn: buildLoadMore(maxCount, totalData.length, $container)
		};

		const meetingSource = $(settings.meetingHandlebars).html(),
			meetingSidebarTabHandlebars = $(settings.meetingSidebarTabHandlebars).html();

		// use an old Meeting details modal as {{> details}} parameters
		Handlebars.registerPartial('details', meetingSource);
		// to show list of several meetings
		const template = Handlebars.compile(meetingSidebarTabHandlebars);
		$container.append(template(totalObject));

		const newCount = oldCount + totalData.length;
		if (maxCount !== null && maxCount > newCount) {
			$container.attr('data-max-count', maxCount).attr('data-next-page', nextPage + 1);
		}
	};

	buildLoadMore = function(maxCount, count, $container) {
		const nextPage = $container.attr('data-next-page') !== undefined ? parseInt($container.attr('data-next-page')) : 1;
		const oldCount = $container.find(itemManageClass).length;
		return maxCount !== null && maxCount > oldCount + count ? {
			maxCount,
			nextPage: nextPage + 1
		} : null;
	};

	clearSidebar = function() {
		if ($sidebarLoader.hasClass(settings.hideClass)) {
			$sidebarLoader.removeClass(settings.hideClass);
		}
		if ($approvalManageContainer.html() !== '' || $openedManageContainer.html() !== '') {
			$approvalManageContainer.empty();
			$openedManageContainer.empty();
		}
	};

	hideLoader = function() {
		$sidebarLoader.addClass(settings.hideClass);
	};

	errorCallback = function (message = null) {
		$approvalManageContainer.html('<div class="text-center p-15"><h5 class="text-secondary mb-10 js-error-title">' + (message === null ? "An error occurred. Please try again" : message) + '</h5></div>');
		hideLoader();
	};

	fetchData = async function(url = '', data = {}) {
		try {
			const response = await fetch(url, {
				method: 'POST',
				mode: "same-origin",
				credentials: "same-origin",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(data)
			});
			const contentType = response.headers.get('content-type');
			if (!contentType || !contentType.includes('application/json')) {
				throw new TypeError("Oops, didn't get JSON");
			}
			if (!response.ok) {
				throw new Error('Something went wrong');
			}
			return await response.json(); // parse JSON response
		} catch (error) {
			console.error('[fetchData] error:', error);
			errorCallback(error.message);
		}
	};

	getCurrentMeeting = function(id) {
		let currentEl = $sidebar.find('.tab-pane.show').find(itemManageClass + '[data-id="' + id + '"]');
		if (currentEl.length === 0) {
			return undefined;
		}
		const json = JSON.parse(decodeURIComponent(currentEl.attr('data-json')));
		return typeof json === 'object' && json !== null ? json : undefined;
	};

	hideApprovalTab = function(hideNavItem = false) {
		$(settings.approvalManage).empty().parent().addClass(settings.hideClass);
		$('#' + $(settings.openedManage).attr('data-type') + 'Manage-tab').click();
		if (hideNavItem) {
			$('#' + $(settings.approvalManage).attr('data-type') + 'Manage-tab').parent().hide();
		}
	};

	setNotificationsMeetingsValue=(notAprrovedUsers)=>{

		const calendarIcon = document.getElementById('calendarIcon');

		if (!notAprrovedUsers) {
			calendarIcon.classList.add("d-none");
			calendarIcon.classList.remove("d-flex");
		}
		else if (notAprrovedUsers > 99) {
			calendarIcon.classList.remove("d-none");
			calendarIcon.classList.add("d-flex");
			calendarIcon.textContent = "99+";
		} else {
			calendarIcon.classList.remove("d-none");
			calendarIcon.classList.add("d-flex");
			calendarIcon.textContent = notAprrovedUsers;
		}
	}

	getUsersToApprove = () => {

		fetchData("/office/route.php", {
			action: "meetings/meetingsWaitingCount",
		})
			.then((res) => {
				if (res.success) {
					const notAprrovedUsers = res.notApprovedMaxCount;
					setNotificationsMeetingsValue(notAprrovedUsers)
				}
			})
			.catch((error) => {
				errorCallback(error.message);
			});
	};

	return {
		init: init,
		getCurrentMeeting: getCurrentMeeting,
		hideApprovalTab: hideApprovalTab,
		openSidebar: openSidebar,
		getUsersToApprove:getUsersToApprove
	};
})({}, jQuery);

const relatedDocsPopup = (function (module, $) {
	'use strict';

	// Public functions
	let init

	// Private general variables
	let isInit = false;


	// Private functions
	let initEvents;

	/*============================================================================
	   Initialise the plugin and define global options
	 ==============================================================================*/
	init = function(options) {
		if (isInit) {
			return false;
		}

		initEvents();
		isInit = true;
	};

	initEvents = function() {


		document.querySelector('.docs-navigator').addEventListener('click', (e)=> {
			handleOpenDoc(e);
		})

		document.addEventListener('click', (e)=> {
			if ((e.target.id==='related-docs-type') || (e.target.id==='related-docs-eye')){
				handleOpenDoc(e);
			}
		})
		document.getElementById('related-docs-btn').addEventListener('click', (e)=>{
			if (e.target.isNavigator){
				const id=e.target.getAttribute('data-id');
				const link=window.location.origin + '/' + 'office/checkout.php?docId=' + `${id}`
				window.open(link, "_blank");
			}else {
				handleCloseRelDocsPopup()
			}
		})

	};

	function handleOpenDoc(e){
		const dataType=e.target.getAttribute('data-type');
		const dataId=e.target.getAttribute('data-id');
		TINY.box.show({
			iframe: `PDF/Docs.php?DocType=${dataType}&amp;DocId=${dataId}`,
			boxid: 'frameless',
			width: 750,
			height: 470,
			fixed: false,
			maskid: 'bluemask',
			maskopacity: 40,
			closejs: function () {
			}
		})
		$('body').find('.tbox').css('z-index', 1041);
		$('body').find('.tmask').css('z-index', 1041);

	}

	return {
		init: init,

	};
})({}, jQuery);

// Meetings Details modal (all pages)
// --IMPORTANT-- it's necessary to change the status / repeat meeting type IDs after the Back is ready
meetingDetailsModule.init({
	statusPendingId: '0',
	statusWaitingId: '1',
	statusOrderedId: '2',
	// statusConfirmedId: '2',
	statusStartedId: '3',
	statusCompletedId: '4',
	statusNotArrivedId: '5',
	statusDoneId: '6',
	statusCanceledId: '7',
	subscriptionStatusActive: '0',
	repeatRegularType: '1',
	repeatServelType: '2',
	repeatOneTimeType: '3'
});

// Sidebar on all pages where client can manage all the meetings that need to be taken care of
meetingSidebarManage.init();
relatedDocsPopup.init();