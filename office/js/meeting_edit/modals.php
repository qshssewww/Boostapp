<!-- Meeting Tooltip modal -->
<script id="meetingTooltipModal" type="text/x-handlebars-template">
	{{#with customer}}
	<div class="bsapp--meeting-tooltip-info d-flex align-items-center">
		<span class="bsapp--meeting-avatar">
			<img src="{{#if avatar}}{{avatar}}{{else}}https://ui-avatars.com/api/?length=1&name={{name}}&background=f3f3f4&color=000&font-size=0.5{{/if}}"
				 onerror="if (this.src != '/office/assets/img/default-avatar.png') this.src='/office/assets/img/default-avatar.png';"
				 alt="{{name}}">
		</span>
		<div>
			<div class="d-flex align-items-center flex-wrap">
				<a href="/office/ClientProfile.php?u={{id}}"
				   class="bsapp--meeting-tooltip-title js--open-client-profile"
				   target="_blank">{{name}}
				{{#if is_embedded}}<!-- the functionality of the icons that exist today in "embedded trainees" in a calendar -->
					<i class="fal fa-star-of-life cursor-pointer" data-toggle="tooltip" data-placement="top" data-original-title="user exist today in embedded trainees"></i>
				{{/if}}
				</a>

				{{#if medical}}
				<a href="#" class="js-client-medical-icon" data-toggle="tooltip" data-placement="top" title="<?= lang('customer_card_medical_records') ?>">
					<i class="fal fa-notes-medical text-danger"></i></a>
				{{/if}}

				{{#if crm}}
				<a href="#" class="js-client-crm-icon" data-toggle="tooltip" data-placement="top" title="<?= lang('note_exists_client') ?>">
					<i class="fal fa-clipboard text-warning"></i></a>
				{{/if}}

				{{#if is_first}}
				<a href="#" data-toggle="tooltip" data-placement="top" title="<?= lang('first_class') ?>">
					<i class="fas fa-star-of-life text-info"></i></a>
				{{else if try_membership}}
				<a href="#" data-toggle="tooltip" data-placement="top" title="<?= lang('trial_lesson') ?>">
					<i class="fal fa-star-of-life text-info"></i></a>
				{{/if}}

				{{#if has_birthday}}
				<a href="#" data-toggle="tooltip" data-placement="top" title="<?= lang('celebrate_birthday_today') ?>">
					<i class="fal fa-birthday-cake text-danger"></i></a>
				{{/if}}

				{{#if regular_assignment}}
				<a href="#" data-toggle="tooltip" data-placement="top" title="<?= lang('setting_permanently') ?>">
					<i class="fal fa-sync text-info"></i></a>
				{{/if}}

				{{#if greenpass}}
				{{{greenpass}}}
				{{/if}}

				{{#if family_membership}}
				<a href="#" class="text-gray-400" data-toggle="tooltip" data-placement="top" title="<?= lang('family_membersip') ?>">
					<i class="fal fa-users"></i></a>
				{{/if}}
			</div>
			{{#if phone}}
			<div class="bsapp--meeting-tooltip-tel">
				{{#if ../customerPhoneLine}}{{../customerPhoneLine}}{{else}}{{phone}}{{/if}}
			</div>
			{{/if}}
		</div>
	</div>
	{{/with}}
	{{#if payInfoText}}
	<div class="bsapp--meeting-tooltip-pay-info {{payInfoClassName}}">{{payInfoText}}</div>
	{{/if}}
	<div class="bsapp--meeting-tooltip-description{{#if customer}} border-top{{else}}{{#if payInfoText}} border-top{{/if}}{{/if}}">
		<div class="d-flex align-items-center">
			{{#if timeDuration}}<div>{{timeDuration}}</div>{{/if}}
			{{#with status}}
				<div class="bsapp-status-tag" style="background-color: {{bg}}; color: {{color}}">
					{{{html}}}
				</div>
			{{/with}}
		</div>
		<div class="d-flex align-items-center justify-content-between titles">
			{{#if title}}<div>{{title}}</div>{{/if}}
			{{#if priceStr}}<div class="price">{{priceStr}}</div>{{/if}}
		</div>
		<div class="text-duration">
			{{#if duration}}{{duration}}{{/if}}
			{{#if coach}}<?= lang('with') ?> {{coach}}{{/if}}
		</div>
	</div>
</script>
<!-- Meeting Details modal -->
<div id="js--meeting-popup_details" class="modal fade bsapp--meeting-popup js--bsapp--meeting-modal"
	 tabindex="-1"
	 aria-labelledby="meetingPopupLabel"
	 aria-hidden="true"
	 data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
			<div class="bsapp-overlay-loader js--loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>
		</div>
    </div>
</div>
<script id="meetingTemplateModal" type="text/x-handlebars-template">
	{{#unless isSidebar}}
	<div class="bsapp-overlay-loader js--loader d-none"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>
	{{/unless}}

	<button type="button" class="close {{#unless isSidebar}}btn--close-modal{{/unless}}" aria-label="Close">
		<i class="{{#if isSidebar}}fas fa-sort-down{{else}}fal fa-times{{/if}}"></i>
	</button>

	{{#if title}}
	<div class="modal-header{{#if isSidebar}} js--toggle-modal-body{{/if}}">
		<span class="meeting--event-color" style="background-color:{{color}};"></span>
		<h4 class="modal-title">{{title}}</h4>
	</div>
	{{/if}}
	<div class="modal-body">
		<div class="d-flex flex-column">
			<div class="meeting--info meeting--info-general">
				<p class="meeting--title-small"><?= lang('meeting_details') ?></p>
				<ul class="bsapp--meeting-popup-ul">
					{{#if owner}}<!-- name of coach or guide - if the studio has more than one guide \ coach, his name will be displayed -->
						<li class="d-flex align-items-center"><i class="fal fa-user-circle"></i>{{owner}}</li>
					{{/if}}
					{{#if dateStr}}<!-- always appears -->
						<li class="d-flex align-items-center"><i class="fal fa-calendar-day"></i>{{dateStr}}</li>
					{{/if}}
					{{#if timeStr}}<!-- always appears -->
					<li class="d-flex align-items-center">
						<i class="fal fa-clock"></i>{{timeStr}}
						{{#if durationStr}} | {{durationStr}}{{/if}}
						{{#if priceTotal}} |<b> {{priceTotal}}</b>{{/if}}
					</li>
					{{/if}}
					{{#if location}}<!-- branch name - appears only when there is more than 1 branch to the studio -->
						<li class="d-flex align-items-center"><i class="fal fa-map-marker-alt"></i>
							{{location}}{{#if isSidebar}}{{#if calendar}} - {{calendar}}{{/if}}{{/if}}
						</li>
					{{/if}}
					{{#unless isSidebar}}
					{{#if calendar}}<!-- the name of the diary - appears only when there is more than 1 diary to the branch. -->
						<li class="d-flex align-items-center"><i class="fal fa-calendar-check"></i>{{calendar}}</li>
					{{/if}}
					{{/unless}}
				</ul>
			</div>

			{{#if template}}
			<div class="meeting--info meeting--info-template">
				<ol class="bsapp--meeting-popup-ul bsapp--list-decimal">
					{{#each template}}
					<li data-id="{{id}}">
						{{#if title}}<span>{{title}}</span>{{/if}}
						<span class="duration">{{#if durationStr}}{{durationStr}}{{/if}}{{#if price}} | {{price}}{{/if}}</span>
					</li>
					{{/each}}
				</ol>
			</div>
			{{/if}}

			<div class="meeting--info meeting--info-user">
				<p class="meeting--title-small"><?= lang('client_details_class') ?></p>
				{{#with customer}}
				<div class="d-flex justify-content-between">
					<div class="d-flex align-items-center">
						<span class="bsapp--meeting-avatar">
							<img src="{{#if avatar}}{{avatar}}{{else}}https://ui-avatars.com/api/?length=1&name={{name}}&background=f3f3f4&color=000&font-size=0.5{{/if}}"
								 onerror="if (this.src != '/office/assets/img/default-avatar.png') this.src='/office/assets/img/default-avatar.png';"
								 alt="{{name}}">
						</span>
						<div>
							<{{#if is_random}}span class="meeting--info-user_title"{{else}}a href="#"
							   class="meeting--info-user_title js-modal-user"
							   data-client-id="{{id}}"
							   data-activity-id="{{_currentSubscription}}"
							   data-act-id="{{classActInfo}}"{{/if}}>{{name}}
							{{#if is_embedded}}<!-- the functionality of the icons that exist today in "embedded trainees" in a calendar -->
								<i class="fal fa-star-of-life cursor-pointer" data-toggle="tooltip" data-placement="top" data-original-title="user exist today in embedded trainees"></i>
							{{/if}}
							</{{#if is_random}}span{{else}}a{{/if}}>

                        {{#if medical}}
                        <a href="#" class="mie-5 js-client-medical-icon bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('customer_card_medical_records') ?>">
                            <i class="fal fa-notes-medical text-danger"></i></a>
                        {{/if}}

                        {{#if crm}}
                        <a href="#" class="mie-5 js-client-crm-icon bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('note_exists_client') ?>">
                            <i class="fal fa-clipboard text-warning">
                            </i></a>
                        {{/if}}

                        {{#if is_first}}
                        <a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('first_class') ?>">
                            <i class="fas fa-star-of-life text-info"></i></a>
                        {{else if try_membership}}
                        <a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('trial_lesson') ?>">
                            <i class="fal fa-star-of-life text-info"></i></a>
                        {{/if}}

                        {{#if has_birthday}}
                        <a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('celebrate_birthday_today') ?>">
                            <i class="fal fa-birthday-cake text-danger"></i></a>
                        {{/if}}

                        {{#if regular_assignment}}
                        <a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="<?= lang('setting_permanently') ?>">
                            <i class="fal fa-sync text-info"></i></a>
                        {{/if}}

                        {{#if greenpass}}
                        {{{greenpass}}}
                        {{/if}}

                        {{#if family_membership}}
                        <a href="#" class="mie-5  bsapp-fs-18 text-gray-400" data-toggle="tooltip" data-placement="top" title="<?= lang('family_membersip') ?>">
                            <i class="fal fa-users"></i></a>
                        {{/if}}

							{{#if phone}}<span class="tel">{{phone}}</span>{{/if}}
						</div>
					</div>
					<div>
						<ul class="meeting--info-user_blocks d-flex bsapp--meeting-popup-ul">
							{{#unless ../isSidebar}}
							<li><a href="/office/ClientProfile.php?u={{id}}" target="_blank"><i class="fal fa-external-link"></i></a></li>
							{{/unless}}
							{{#if phoneWithCode}}
                            {{#unless is_random}}
							<li><a href="https://wa.me/{{phoneWithCodeWithoutPlus}}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a></li>
							<li><a href="tel:{{phoneWithCode}}"><i class="fal fa-phone-alt"></i></a></li>
                            {{/unless}}
							{{/if}}
						</ul>
					</div>
				</div>
				{{/with}}
			</div>

			{{#if items}}
			<div class="meeting--info meeting--info-status">
				<p class="meeting--title-small"><?= lang('meeting_status') ?></p>

				<div class="sel sel--statuses" data-arrow-icon="&#xf107">
					<select data-old-status={{currentStatus}} name="meetingStatus" id="select-status_{{id}}" data-id="{{id}}">
						{{#each items}}
						<option {{#if isSelected}}selected="selected"{{/if}} value="{{id}}" data-type="{{type}}" data-icon="{{iconClass}}" data-color="{{color}}">{{translation}}</option>
						{{/each}}
					</select>
				</div>
			</div>
			{{/if}}

			{{#if showSubscription}}
			<div class="meeting--info meeting--info-subscription">
				<p class="meeting--title-small"><?= lang('meeting_subscription_title') ?></p>

				{{#with subscription}}
					{{#if title}}<h5>{{title}}</h5>{{/if}}
					{{#if date}}<span><?= lang('valid_date_new_checkout') ?> {{date}}</span>{{/if}}
					{{#if entries}}<span><?= lang('more_entries') ?> {{entries}}</span>{{/if}}
				{{/with}}
			</div>
			{{/if}}

			{{#if showDocs}}
			<div class="meeting--info meeting--info-account">
				<p class="meeting--title-small"><?= lang('meeting_accounting_detail') ?></p>

				<ul class="meeting--info-account_blocks bsapp--meeting-popup-ul">
					{{#each clientDocs}}
					<li>
						<span class="dates">{{date}}</span>
						<span class="details">{{{textHtml}}}</span>
						<span class="name"><?= lang('meeting_by') ?> {{{nameHtml}}}</span>
					</li>
					{{/each}}
				</ul>
			</div>
			{{/if}}
		</div>
	</div>

	<div class="modal-footer">
		<div class="d-flex justify-content-end meeting--btns">
			{{#unless template}}
			<button type="button" class="btn btn-default btn--change_to_not_arrived meeting--btn border-dark" data-meeting-id="{{id}}"><?= lang('change_to_not_arrived') ?></button>
			<div class="sel sel--options fake-select"
				 data-arrow-icon="&#xf0dd" data-chosen="false">
				<select name="meetingOptions"
						id="select-status_{{id}}"
						data-meeting-id="{{id}}"
						data-meeting-status="{{currentStatus}}"
						data-meeting-repeat-type="{{repeatType}}">
					<option value="" disabled selected="selected"><?= lang('meeting_options') ?></option>

					{{#if isSidebar}}
						{{#if showApprovalOptions}}
<!--							<option value="edit_meeting">--><?//= lang('edit_meeting') ?><!--</option>-->
							<option value="order_rejection"><?= lang('order_rejection') ?></option>
						{{else}}
							<option value="change_to_not_arrived"><?= lang('change_to_not_arrived') ?></option>
<!--							<option value="change_to_close">--><?//= lang('change_to_close') ?><!--</option>-->
						{{/if}}
					{{else}}
                        {{#unless notShowBtnCancel}}<option value="meeting_cancel_text"><?= lang('meeting_cancel_text') ?></option>{{/unless}}
                        {{#unless notShowBtnEdit}}<option value="meeting_edit_details"><?= lang('meeting_edit_details') ?></option>{{/unless}}
<!--						{{#if showBtnInDebt}}<option value="meeting_user_in_debt">{{#if showBtnInDebtText}}--><?//= lang('meeting_user_in_debt') ?><!--{{ else }}--><?//= lang('complete_meeting_calendar') ?><!-- {{/if}}</option>{{/if}}-->
<!--						{{#if showBtnRegistrationSubscription}}<option value="meeting_registration_subscription">--><?//= lang('meeting_registration_subscription') ?><!--</option>{{/if}}-->
						{{#if showUnCompleteBtn}}<option value="activate_meeting_status"><?= lang('meeting_open_status') ?></option>{{/if}}
                        {{#if showUnCompleteBtnNP}}<option value="activate_meeting_status_np"><?= lang('meeting_open_status') ?></option>{{/if}}
						{{#if showScheduleNew}}<option value="meeting_schedule_new"><?= lang('meeting_schedule_new') ?></option>{{/if}}
					{{/if}}
				</select>
			</div>
			{{/unless}}
			{{#if showPayBtn}}<!-- If the balance is at 0, the button does not appear. -->
			<button type="button" class="btn btn-success meeting--btn js--meeting--btn-pay btn--cancel-type-to"
					data-id="{{id}}"
					data-type-to="{{#if notShowBtnCancel}}meeting_pay_modal{{else}}meeting_charged{{/if}}">
				<?= lang('options_charged') ?>
			</button>
			{{/if}}

			{{#if showApprovalBtn}}
			<button type="button" class="btn btn-darker meeting--btn btn--to-approve js--to-approve"
					data-id="{{id}}"><?= lang('approval') ?></button>
			{{/if}}
		</div>
	</div>
</script>
<!-- Meeting Details modal :: end -->

<!-- Meeting Helpers small modals -->
<div id="js--meeting-popup_helpers" class="modal fade bsapp--meeting-popup bsapp--meeting-popup_helpers js--bsapp--meeting-modal"
	 tabindex="-1"
	 aria-labelledby="meetingPopupHelpersLabel"
	 aria-hidden="true"
	 data-backdrop="none">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="bsapp-overlay-loader js--loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>
		</div>
	</div>
</div>
<script id="meetingHelpersModal" type="text/x-handlebars-template">
	<div class="bsapp-overlay-loader js--loader d-none"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>
	<button type="button" class="close btn--close-modal" aria-label="Close">
		<i class="fal fa-times"></i>
	</button>
	{{#if title}}
	<div class="modal-header">
		<h4 class="modal-title">{{title}}</h4>
	</div>
	{{/if}}
	<div class="modal-body {{#if isSmallBody}}modal-body--small{{/if}}" type="{{modalType}}">
		<div class="d-flex flex-column {{#unless chargedClient}}justify-content-center{{/unless}}{{#if canBeRepeated}} justify-content-lg-start{{/if}}">
			<div class="{{#unless useCenter}}text-center{{/unless}}">
				{{#if imageSrc}}
				<div class="meeting--helpers-img">
					<div><lottie-player src="{{imageSrc}}"  background="transparent" speed="1" autoplay></lottie-player></div>
				</div>
				{{/if}}

                {{#if iconClassName}}
                <div class="meeting--helpers-icon">
                    <i class="{{iconClassName}}" style="color: {{iconColor}}"></i>
                </div>
                {{/if}}

				{{#if note}}
					<p class="note note--first">{{note}}</p>
				{{/if}}

				{{#each paragraphBoldHtml as |paragraph|}}
					<p class="meeting--helpers-bold">{{{paragraph}}}</p>
				{{/each}}

				{{#if noteAfter}}
				<p class="note">{{noteAfter}}</p>
				{{/if}}

                {{#if noteRed}}
                <p class="note note--red">{{noteRed}}</p>
                {{/if}}
			</div>

			{{#if items}}
			<div class="sel sel--reasons text-start" data-arrow-icon="&#xf107">
				<select name="{{selectedName}}" data-id="{{id}}" required>
					{{#each items}}
					<option value="{{id}}" {{#if disabled}}disabled="disabled"{{/if}} {{#if selected}}selected="selected"{{/if}}>{{text}}</option>
					{{/each}}
				</select>
			</div>
			{{/if}}

			{{#if paragraph}}
			<div class="{{#unless canBeRepeated}}text-center{{/unless}}">
				<p><small>{{paragraph}}</small></p>
			</div>
			{{/if}}

			{{#if canBeRepeated}}
			<div class="form-group">
				<p><?= lang('desk_how_delete') ?></p>
				<div class="custom-control custom-radio">
					<input checked type="radio" id="js--meeting-cancel-radio-1" name="cancelMeetingRadio" value="single" class="custom-control-input">
					<label class="custom-control-label" for="js--meeting-cancel-radio-1"><?= lang('one_time_payment') ?> </label>
				</div>
				<div class="custom-control custom-radio">
					<input type="radio" id="js--meeting-cancel-radio-2" name="cancelMeetingRadio" value="multi" class="custom-control-input" data-item-event="radio">
					<label class="custom-control-label" for="js--meeting-cancel-radio-2"><?= lang('delete_meeting_series') ?></label>
				</div>
			</div>
			<div>
				<div class="form-group mb-0" data-context="js--meeting-cancel-radio-1">

				</div>

				<div class="form-group d-none mb-0" data-context="js--meeting-cancel-radio-2">
                    <div class="form-group text-danger"><?= lang('cancel_series_without_cancel_policy') ?></div>
					<label for="meeting-series-dropdown"><?= lang('series_delete_options') ?></label>
					<div class="sel sel--series" data-arrow-icon="&#xf107">
						<select name="meetingSeriesDropdown" id="meeting-series-dropdown" data-id="{{id}}">
							<option selected="selected" value="all"><?= lang('meeting_all') ?></option>
							<option value="dates" data-show="js--meeting-series-option-2"><?= lang('in_date_range') ?></option>
							<option value="quantity" data-show="js--meeting-series-option-3"><?= lang('number_of_shows') ?></option>
						</select>
					</div>

					<div>
						<div class="form-group d-none mt-10" data-context="js--meeting-series-option-2">
							<div class="d-flex mb-5 align-items-center justify-content-between">
								<span class="mie-8"><?= lang('coupon_from') ?></span>
								<input name="meetingCancelDateSince" type="date" class="form-control js-datepicker bg-light border--light">
							</div>
							<div class="d-flex mb-5 align-items-center justify-content-between">
								<span class="mie-8"><?= lang('coupon_till') ?></span>
								<input name="meetingCancelDateUntil" type="date" class="form-control js-datepicker bg-light border--light">
							</div>
						</div>

						<div class="form-group align-items-center d-none mt-10" data-context="js--meeting-series-option-3">
							<div class="d-flex align-items-center">
								<span class="mie-8"><?= lang('canel_of_desk') ?></span>
								<input name="meetingCancelQuantity" type="number" min="1" class="form-control bg-light border--light mie-8 px-6 w-25">
								<span><?= lang('future_shows_desk') ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{/if}}


            {{#if chargedClient}}
            <div class="card-charged-client">
                <p><?= lang('options_charged') ?>{{#if priceCharged}} - {{priceCharged}} ₪{{/if}}</p>
                <!--todo-bp-909 (cart) remove-beta (always false) hasToken not relevant-->
                {{#if isBeta}}
                <div></div>
                {{else}}
                    {{#if hasToken}}
                    <div class="custom-control custom-radio">
                        <input {{#unless itemsToCharged}}checked{{/unless}} type="radio" id="js--options-charged-radio-1" name="optionsChargedRadio" value="saved_card" class="custom-control-input">
                        <label class="custom-control-label" for="js--options-charged-radio-1"><?= lang('meeting_cancellation_use_saved_card') ?> </label>
                    </div>
                    {{/if}}
                {{/if}}
                {{#unless isRandomClient}}
                <div class="custom-control custom-radio">
                    <input {{#unless itemsToCharged}}{{#unless hasToken}}checked{{/unless}}{{/unless}} type="radio" id="js--options-charged-radio-2" name="optionsChargedRadio" value="debt" class="custom-control-input" data-item-event="radio">
                    <label class="custom-control-label" for="js--options-charged-radio-2">
                        {{#if isCancel}}
                        <!--todo-bp-909 (cart) remove-beta (always true)-->
                        {{#if isBeta}}
                                <?= lang('checkout_debt_invoice') ?></label>
                </div>
                <div class="custom-control custom-radio">
                    <!--todo-bp-909 (cart) is beta change after cart not beta (alwas true)-->
                    <input type="radio" id="js--options-charged-radio-cancellation-and-charge-cart" name="optionsChargedRadio" value="cancellation_and_charge_cart" class="custom-control-input" data-item-event="radio">
                    <label class="custom-control-label" for="js--options-charged-radio-cancellation-and-charge-cart">שינוי המנוי כחוב וכניסה לקופה</label>
                </div>
                        {{else}}
                                <?= lang('meeting_cancellation_user_in_debt') ?></label></div>
                            {{/if}}
                        {{else}}<?= lang('meeting_user_in_debt') ?> </label></div>{{/if}}



                {{/unless}}
                {{#unless isCancel}}
                <div class="custom-control custom-radio">
                    <!--todo-bp-909 (cart) is beta change after cart not beta (alwas true)-->
                    <input {{#if isRandomClient}}checked{{/if}} type="radio" id="js--options-charged-radio-3" name="optionsChargedRadio" value="{{#if isBeta}}move_cart{{else}}move_client_profile{{/if}}" class="custom-control-input" data-item-event="radio">
                    <label class="custom-control-label" for="js--options-charged-radio-3"><?= lang('meeting_pay_modal_link') ?></label>
                </div>
                {{/unless}}
            </div>
            {{/if}}
            {{#if itemsToCharged}}
            <div class="card-charged-client">
                <p><?= lang('membership_of_client') ?></p>
                {{#each itemsToCharged}}
                <div class="custom-control custom-radio">
                    <input {{#if checked}}checked{{/if}} type="radio" id="js--options-charged-radio-{{id}}" name="optionsChargedRadio" value="{{id}}" class="custom-control-input">
                    <label class="custom-control-label" for="js--options-charged-radio-{{id}}"><span>{{text}}</span> </label>
                </div>
                {{/each}}
            </div>
            {{/if}}

			{{#if iconLinksArr}}
				<div class="d-flex justify-content-center meeting--link-icons_content">
					{{#each iconLinksArr}}
						<a href="{{url}}" class="btn meeting--link-icons_btn{{#if copyLabel}} js--copy-to-clipboard{{/if}}"
						   data-hover="{{text}}"
						   {{#if copyLabel}}aria-label="<?= lang('copied_link') ?>"{{else}}target="_blank"{{/if}}>
							<i class="{{iconClass}}"></i>
						</a>
					{{/each}}
				</div>
			{{/if}}
		</div>

		{{#with showCheckbox}}
		<div class="bsapp--checkbox-small checkbox--position-bottom">
			<input name="{{id}}" id="{{id}}"
				   type="checkbox"
				   class="custom-control-input"
				   {{#if checked}} checked="checked"{{/if}}>
			<label for="{{id}}">{{label}}</label>
		</div>
		{{/with}}
	</div>
	{{#if hasButtons}}
	<div class="modal-footer {{#if chargedClient}}justify-content-between{{/if}}">
		{{#each buttonsArr}}
		<button type="button"
				class="btn {{#unless ../chargedClient}}btn--full{{/unless}} moria-class {{class}}"
				data-type="{{type}}"
				data-type-to="{{typeTo}}"
				{{#if typePrev}} data-type-prev="{{typePrev}}"{{/if}}>{{text}}</button>
		{{/each}}

		{{#with linkBtnObj}}
		<a href="{{url}}"
		   class="btn btn--full {{class}}">{{text}}</a>
		{{/with}}
	</div>
	{{/if}}
</script>
<!-- Meeting Helpers small modals :: end -->

<!-- Meeting Sidebar Manage modal -->
<div id="sidebarManageMeetings" class="bsapp-sidebar--manage-meetings bsapp--modal-sidebar js--bsapp--meeting-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="bsapp-overlay-loader js--loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>

		<div class="bsapp-modal-title d-flex justify-content-between">
			<h4>
				<i class="fal fa-calendar-exclamation"></i>
				<span><?= lang('cal_appointments') ?></span>
			</h4>
			<i class="bsapp-search-close js-modal-close-btn" data-modal-close="sidebar"></i>
		</div>

		<ul class="nav nav-tabs bsapp--sidebar-tabs" id="sidebarManageTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="notApprovedManage-tab" data-toggle="tab" data-target="#approvalManage" type="button" role="tab" aria-controls="approvalManage" aria-selected="true"><?= lang('pending') ?></button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="openedManage-tab" data-toggle="tab" data-target="#openedManage" type="button" role="tab" aria-controls="openedManage" aria-selected="false"><?= lang('open_meetings') ?></button>
			</li>
		</ul>

		<div class="tab-content bsapp--tab-content" id="sidebarTabContent">
			<div class="tab-pane fade show active" id="approvalManage" role="tabpanel" aria-labelledby="notApprovedManage-tab">
				<div id="approvalManageMeetingsContainer" class="tab-manage-container"
					 data-type="notApproved"></div>

				<div class="bsapp-modal-bottom-btn d-none">
					<button id="confirmAllMeetings"
							class="btn js--to-approve"
							data-type="all"
							type="button"><?= lang('confirm_all_meetings') ?></button>
				</div>
			</div>
			<div class="tab-pane fade" id="openedManage" role="tabpanel" aria-labelledby="openedManage-tab">
			<div id="openedManageMeetingsContainer" class="tab-manage-container"data-type="opened"></div>
		</div>
		</div>
	</div>
</div>
<script id="meetingSidebarTabModal" type="text/x-handlebars-template">
	{{#each items}}
		{{#if dateTitle}}
		<div class="bsapp--divider-content bsapp--divider-{{date}}">{{dateTitle}}</div>
		{{/if}}

		<div class="modal-content sidebar--manage-item sidebar--manage-item-{{date}} {{opened}}"
			 data-index="{{index}}"
			 data-type="{{type}}"
			 data-id="{{id}}"
             data-date="{{date}}"
			 data-json="{{encodeJSON}}">{{> details}}</div>
	{{/each}}
	{{#if moreBtn}}
	<div class="d-flex justify-content-center">
		<button class="btn btn-white bsapp--load-more js--load-more"
				type="button"><?= lang('load_more') ?></button>
	</div>
	{{/if}}
</script>
<!-- Meeting Sidebar Manage modal :: end -->

<!-- User modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-user" data-backdrop="static">
	<div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
		<div class="modal-content border-0 shadow-lg overflow-hidden ">
			<div id="js-modal-user-content" class="modal-body bsapp-overflow-y-auto position-relative p-0 overflow-hidden bsapp-max-h-700p" style="height:calc( 100vh - 200px );" >
			</div>
            <?php if (Auth::userCan('172') || Auth::userCan('170')): ?>
				<a href="javascript:;" onclick="modalUserPopup.addNewTextarea()" class="js-add-textarea bg-success position-absolute text-white w-50p h-50p rounded-circle d-flex justify-content-center align-items-center mb-20 shadow-lg" style="bottom:0px;margin-inline-start:calc( 100% - 80px );">
					<i class="fal fa-clipboard bsapp-fs-20"></i>
				</a>
            <?php endif;?>
		</div>
	</div>
</div>
<!-- newly added textarea html :: begin -->
<div class="js-window-loader-3 position-relative d-none">
	<div class="bsapp-overlay-loader js-loader d-flex">
		<div class="spinner-border text-primary" role="status">
			<span class="sr-only"><?= lang('loading_datatables') ?></span>
		</div>
	</div>
</div>
<div class="d-none js-html-textarea">
    <?php require dirname(dirname(dirname(__FILE__))) . "/partials-views/char-popup/modal-client-info-crm-medical.php"; ?>
</div>
<div class="js-window-loader-stripe-3 d-none">
	<div class="p-15" style="left:0;top:0;right:0;bottom:0;z-index:99;">
		<div class="overflow-hidden " style="">
			<div class="bsapp-loading-shimmer">
				<div>
					<div class="mb-15 w-100">
						<div></div>
					</div>
					<div class="mb-15 w-100">
						<div></div>
					</div>
					<div class="mb-15 w-50">
						<div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="d-none js-html-textarea-update">
	<div class="px-15">
		<div class="js-textarea-newly-added-update mb-20">
			<div class="js-window-edit-text-loader position-relative d-none" style="">
				<div class="js-loader-div-char">
					<div class="p-15 position-absolute" style="left:0;top:0;right:0;bottom:0;z-index:99;">
						<div class="overflow-hidden " style="">
							<div class="bsapp-loading-shimmer">
								<div>
									<div class="mb-15 w-100">
										<div></div>
									</div>
									<div class="mb-15 w-100">
										<div></div>
									</div>
									<div class="mb-15 w-100">
										<div></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="d-flex flex-column w-100 js-textarea-edit-mode-update">
				<div class="mb-10">
					<textarea class="form-control js-form-control-textarea-update"></textarea>
				</div>
				<div class="d-flex justify-content-between align-items-start">
					<a class="btn btn-light mie-8 js-textarea-delete-update" href="javascript:;" onclick="modalUserPopup.hideUpdateTextArea(this);"><i class="fal fa-times"></i></a>
					<div class="align-items-start d-flex justify-content-end">
						<input type="date" class="form-control bg-light border-light js-datepicker w-50 ml-10">
						<a class="btn btn-info js-textarea-add-content-update" href="javascript:;" onclick="modalUserPopup.saveUpdateTextArea(this);"><?= lang('save') ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- newly added textarea html :: end -->
<div class="modal px-0 px-sm-auto js-modal-no-close js-remove-regular-assignment" tabindex="-1" role="dialog" id="js-remove-regular-assignment-modal">
	<div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
		<div class="modal-content">
			<div class="modal-body d-flex flex-column bsapp-min-h-400p">
				<div class="d-flex justify-content-between w-100">
					<h6><?= lang('remove_recurring_booking') ?></h6>
					<a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
				</div>
				<div class="d-flex flex-column align-self-stretch mt-auto justify-content-center">
					<form id="removeOptionContainer" class="d-flex flex-wrap form-group align-items-start my-15">
						<label class="bsapp-fs-14 text-gray-500 mb-10">
							תדירות
						</label>
						<div class="form-group align-items-start mb-15">
							<div class="flex-fill custom-group-radio">
								<div class="custom-control custom-radio mb-15">
									<input type="radio" class="custom-control-input" id="removeOption" name="removeOption" value="all">
									<label class="custom-control-label d-flex align-items-center" for="removeOption"><?= lang('all_the_assignments') ?></label>
								</div>
								<div class="custom-control custom-radio mb-15">
									<input type="radio" class="custom-control-input" id="removeOption1" name="removeOption" value="by-date">
									<label class="custom-control-label d-flex align-items-center" for="removeOption1">
                                        <?= lang('between_dates'); ?>
										<input name="remove-until-date" type="date" class="form-control px-7 bg-light border-light js-datepicker mr-10 bsapp-max-w-150-p" /></label>
								</div>
								<div class="custom-control custom-radio mb-15">
									<input type="radio" class="custom-control-input" id="removeOption2" name="removeOption" value="by-quantity">
									<label class="custom-control-label d-flex align-items-center" for="removeOption2">
                                        <?= lang('number_of_shows') ?>
										<input name="remove-by-quantity" type="number" class="form-control bg-light border-light col-3 mx-10" />
									</label>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="d-flex justify-content-around mt-auto w-100">
					<a class="btn btn-light flex-fill mie-10" data-dismiss="modal"><?= lang('action_cacnel') ?></a>
					<a href="javascript:;" class="confim-btn btn btn-danger flex-fill" onclick="modalUserPopup.confirmDelete(this);" ><?= lang('a_remove_single') ?><span class="js-loader-spin mis-5" style="display:none;"><i class="fad fa-spinner-third fast-spin"></i></span></a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- User modal :: end -->

<!-- Create\Edit class modal -->
<div class="modal fade px-0 px-sm-auto js-modal-no-close bsapp--meeting-popup text-gray-700 text-start" tabindex="-1" role="dialog" id="js-meeting-popup" data-backdrop="static" >
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content overflow-auto">
			<div class="modal-body">

			</div>
		</div>
	</div>
</div>
<!-- Create\Edit class modal :: end -->

<!-- related documents popup start -->


<div id="js-related-documents" class="modal bsapp--meeting-popup js--bsapp--meeting-modal">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content meetings-overlap-content" >
            <div id="docs-related-loader" class="bsapp-overlay-loader js--loader ">
                <div class="spinner-border text-primary " role="status"><span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom border-light" style="z-index:9999">
                <div class="px-15 py-15">
                    <span id="related-docs-title" class="bsapp-fs-18 font-weight-bold"><?php echo lang('detail') ?></span>
                    <a class="docs-navigator text-underline font-weight-bold"></a>
                </div>

                <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" data-dismiss="modal">
                    <i class="fal fa-times"></i>
                </a>
            </div>

            <div id="related-docs-wrapper" class="w-100 p-15" >

            </div>

            <div class="d-flex flex-col justify-content-between p-15">
                <div class="related-docs-sum d-flex flex-column justify-content-between">
                    <p class="m-0 text-start" style="color:#828282"> <?= lang("docs_connected_total") ?></p>
                    <p class="m-0 text-start font-weight-bold mt-6" id='related-docs-balance' ></p>
                </div>

                <button id="related-docs-btn" type="button" class="btn btn-dark" style="">
                    <?= lang("docs_connected_close") ?>
                </button>

            </div>
        </div>
    </div>
</div>
<script id="related-docs-template" type="text/x-handlebars-template">
   <h6 class="text-start mb-10"><?= lang("actions")?></h6>

    <div class="d-flex flex-column p-15 overflow-y-auto " id="docs-list" >
        <div class="d-flex flex-row align-items-center justify-content-around w-100" >
            <div class="w-20 text-gray-500 font-weight-bold card-body-title"><?= lang("docs_connected_number")?></div>
            <div class="w-20 text-gray-500 font-weight-bold card-body-title"><?= lang("docs_connected_document")?></div>
            <div class="w-20 text-gray-500 font-weight-bold card-body-title"><?= lang("date")?></div>
            <div class="w-20 text-gray-500 font-weight-bold card-body-title"><?= lang("summary")?></div>
        </div>

        {{#each documents}}
        <div class="d-flex flex-row p-15 justify-content-between" id="doc"  >

        <i class="fal fa-eye" id="related-docs-eye"  data-id={{TypeNumber}} data-type={{TypeId}}></i>
        <div id="related-docs-type" class="card-body-title font-weight-bold  mr-6 first-name"  data-id={{TypeNumber}} data-type={{TypeId}}>{{TypeNumber}}</div>
        <div id="docs-type-name" class="card-body-title font-weight-bold  text-center text-overflow" >{{docHeaderTypeName}}</div>
        <div class="card-body-title font-weight-bold " style="width:75px" >{{DocDate}}</div>
        <div class="card-body-title font-weight-bold  text-overflow" style="width:90px; color: {{#if isAmountUnderZero}} red {{/if}}; direction: ltr" >₪ {{Amount}} </div>
        </div>
        {{/each}}
    </div>


    </div>
</script>

<!-- related documents popup end -->

