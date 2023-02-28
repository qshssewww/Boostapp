<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"         => "The :attribute must be accepted.",
	"active_url"       => "The :attribute is not a valid URL.",
	"after"            => "The :attribute must be a date after :date.",
	"alpha"            => "The :attribute may only contain letters.",
	"alpha_dash"       => "The :attribute may only contain letters, numbers, and dashes.",
	"alpha_num"        => "The :attribute may only contain letters and numbers.",
	"array"            => "The :attribute must be an array.",
	"before"           => "The :attribute must be a date before :date.",
	"between"          => array(
		"numeric" => "The :attribute must be between :min and :max.",
		"file"    => "The :attribute must be between :min and :max kilobytes.",
		"string"  => "The :attribute must be between :min and :max characters.",
		"array"   => "The :attribute must have between :min and :max items.",
	),
	"boolean"          => "The :attribute field must be true or false",
	"confirmed"        => "The :attribute confirmation does not match.",
	"date"             => "The :attribute is not a valid date.",
	"date_format"      => "The :attribute does not match the format :format.",
	"different"        => "The :attribute and :other must be different.",
	"digits"           => "The :attribute must be :digits digits.",
	"digits_between"   => "The :attribute must be between :min and :max digits.",
	"email"            => "The :attribute format is invalid.",
	"exists"           => "The selected :attribute is invalid.",
	"image"            => "The :attribute must be an image.",
	"in"               => "The selected :attribute is invalid.",
	"integer"          => "The :attribute must be an integer.",
	"ip"               => "The :attribute must be a valid IP address.",
	"max"              => array(
		"numeric" => "The :attribute may not be greater than :max.",
		"file"    => "The :attribute may not be greater than :max kilobytes.",
		"string"  => "The :attribute may not be greater than :max characters.",
		"array"   => "The :attribute may not have more than :max items.",
	),
	"mimes"            => "The :attribute must be a file of type: :values.",
	"min"              => array(
		"numeric" => "The :attribute must be at least :min.",
		"file"    => "The :attribute must be at least :min kilobytes.",
		"string"  => "The :attribute must be at least :min characters.",
		"array"   => "The :attribute must have at least :min items.",
	),
	"not_in"           => "The selected :attribute is invalid.",
	"numeric"          => ":attribute חייב להיות מספרים בלבד",
	"regex"            => "The :attribute format is invalid.",
	"required"         => "שדה :attribute הינו שדה חובה.",
	"required_if"      => "The :attribute field is required when :other is :value.",
	"required_with"    => "The :attribute field is required when :values is present.",
	"required_without" => "The :attribute field is required when :values is not present.",
	"same"             => "The :attribute and :other must match.",
	"size"             => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobytes.",
		"string"  => "The :attribute must be :size characters.",
		"array"   => "The :attribute must contain :size items.",
	),
	"unique"           => "שדה :attribute נמצא בשימוש",
	"url"              => "The :attribute format is invalid.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'new_password'     => array('required' => lang('new_password_validation')),
		'current_password' => array('required' => lang('current_password_validation')),
		'reminder_email'   => array('exists' => lang('account_not_found_validation')),
		'reminder' => array(
			'required' => lang('fix_url_validation'), 
			'exists' => lang('error_url_validation'), 
			'valid' => lang('valid_url_validation')
		),
		'activation_key' => array(
			'required' => lang('valid_url_validation'),
			'exists' => lang('valid_url_validation')
		),
		'captcha' => array(
			'captcha' => 'The Captcha field is invalid.',
		),
		'assignment' => array(
			'required' => 'The Assignment field is required.',
			'valid_assignment' => 'The Assignment field is invalid.'
		),
		'id' => array(
			'unique_field' => 'The :attribute has already been taken.'
		),
		'to' => array(
			'exists' => "The user you're trying to message does not exist.",
		),
		'to_user' => array(
			'exists' => 'The selected user was not found.',
		),
		'to_group' => array(
			'required' => 'The "To User" or "To Group" is required.',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
		'name'       => lang('class_table_name'),
		'first_name' => lang('first_name'),
		'last_name'  => lang('last_name'),
		'firstname' => lang('first_name'),
		'lastname'  => lang('last_name'),
		'username'   => lang('username_single'),
		'email'      => lang('email'),
		'url'        => lang('site'),
		'password'   => lang('password_single'),
		'pass1'   => lang('password_single'),
		'role'       => lang('permission_table'),
		'status'     => lang('account_status_admin'),
		'reminder_email' => lang('email'),
		'captcha' => 'Captcha',
		'to' => lang('to_validation'),
		'subject' => lang('subject'),
		'message' => lang('message_main'),
		'type' => 'Type',
		'id' => 'ID',
		'order' => 'Display Order',
		'DepartmentName' => lang('checkout_dep_validation'),
		'DepartmentSite' => lang('website_title_validation'),
		'client' => lang('choose_client'),
		'shuvarid' => lang('manual_certificate_number'),
		'Item1' => lang('select_item'),
		'SizeName' => lang('size_shop_render'),
		'ColorName' => lang('color_shop_render'),
		
		'CompanyName' => lang('business_name_validation'),
		'BusinessType' => lang('business_type_validation'),
		'CompanyId' => lang('license_number'),
		'JobsRole1' => lang('role_dep_validation'),
		'ContactName1' => lang('contact_name_validation'),
		'ContactMobile1' => lang('phone'),
		'PaymentRole' => lang('payment_condition'),
		'SupplierType' => lang('business_class_validation'),
		
				 //// העשייה שלי
	 
	 'ArchiveTask' => lang('my_tasks_main'),
	 'TaskType' => lang('task_type_main'),
	 'TaskType1' => lang('task_single'),
	 'TaskType2' => lang('event_main'),
	 'TaskType3' => lang('project_main'),
	 'TaskType4' => lang('oppertunity_main'),
	 
	 'TaskGoal' => lang('task_goal_main'),
	 'TaskGoal1' => lang('without_goal_main'),
	 'TaskGoal2' => lang('train_goal_main'),
	 
	 'TaskTitle' => lang('table_title'),
	 'StartDate' => lang('start_date'),
	 'StartTime' => lang('begin_time'),
	 'EndDate' => lang('finish_date'),
	 'EndTime' => lang('finish_time'),
	 'AllDay' => lang('all_day_event_main'),
	 'AllDay1' => lang('yes'),
	 'AllDay2' => lang('no'),
	 'priority_1' => lang('priority_rank_main'),
	 'priority_2' => lang('low'),
	 'priority_3' => lang('medium'),
	 'priority_4' => lang('high'),
	 'AreaOfLife' => lang('life_area_main'),
	 
	 'TaskDetails' => lang('activity_info_main'),
	 'Remarks' => lang('notes_two'),
	 
	 'TaskStatus' => lang('task_status_main'),
	 'TaskStatus1' => lang('not_completed_main'),
	 'TaskStatus2' => lang('task_completed_main'),
	 
	 'Challenge' => lang('challange_task_main'),
	 'Challenge2' => lang('challange_two_task_main'),
	 'sync' => lang('sync_outlook_main'),
	 
	 'AddTask' => lang('add_task_main'),
	 'UpdateTask' => lang('update_task_main'),
	 
	 'Color' => lang('activity_color_main'),
	 'Color1' => lang('default_main'),
	 'Color2' => lang('bold_blue_main'),
	 'Color3' => lang('blue_color'),
	 'Color4' => lang('turquoise_color'),
	 'Color5' => lang('green_color'),
	 'Color6' => lang('bold_blue_color'),
	 'Color7' => lang('yellow_color'),
	 'Color8' => lang('orange_color'),
	 'Color9' => lang('red_color'),
	 'Color10' => lang('bold_red_color'),
	 'Color11' => lang('purple_color'),
	 'Color12' => lang('grey_color'),
	 
	 
	 
	 'Client' => lang('a_client_main'),
	 'Dates' => lang('date'),
	 'DocTempId' => lang('service_main'),
	 
	
	
	 'Title' => lang('table_title'),
	 'TitleUrl' => lang('page_title_validation'),
	 'ItemId' => lang('item_single'),
	 'Content' => lang('contet_single'),
	 'ImageLink' => lang('a_logo_validation'),
	 'TypePage' => lang('page_type'),
	 'Amount' => lang('summary'),

	),

);
