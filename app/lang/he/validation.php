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
	"alpha_dash"       => "שדה :attribute יכול להכיל אותיות ומספרים בלבד ללא רווחים.",
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
	"digits"           => "שדה :attribute חייב להכיל :digits ספרות.",
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
	"required_if"      => "שדה :attribute הינו שדה חובה",
	"required_with"    => "שדה :attribute הינו שדה חובה",
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
		'new_password'     => array('required' => 'אנא הקלד סיסמה חדשה לאיפוס.'),
		'current_password' => array('required' => 'אנא הקלד סיסמה נוכחית על מנת לאפס סיסמה.'),
		'reminder_email'   => array('exists' => 'לא נמצא חשבון עם דואר אלקטרוני זה.'),
		'reminder' => array(
			'required' => 'הקישור אינו תקף, אנא צור חדש.', 
			'exists' => 'הקישור אינו תקף, אנא צור חדש.', 
			'valid' => 'הקישור אינו תקף, אנא צור חדש.'
		),
		'activation_key' => array(
			'required' => 'הקישור אינו תקף, אנא צור חדש.',
			'exists' => 'הקישור אינו תקף, אנא צור חדש.'
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
		'name'       => 'שם',
		'first_name' => 'שם פרטי',
		'last_name'  => 'שם משפחה',
		'firstname' => 'שם פרטי',
		'lastname'  => 'שם מפשחה',
		'username'   => 'שם משתמש',
		'email'      => 'דואר אלקטרוני',
		'url'        => 'אתר',
		'password'   => 'סיסמה',
		'pass1'   => 'סיסמה',
		'role'       => 'הרשאה',
		'status'     => 'סטטוס חשבון',
		'reminder_email' => 'דואר אלקטרוני',
		'captcha' => 'Captcha',
		'to' => 'אל',
		'subject' => 'נושא',
		'message' => 'הודעה',
		'type' => 'Type',
		'id' => 'ID',
		'order' => 'Display Order',
		'DepartmentName' => 'מחלקה בקופה',
		'DepartmentSite' => 'כותרת באתר',
		'client' => 'בחר לקוח',
		'shuvarid' => 'מספר תעודה ידנית',
		'Item1' => 'בחר פריט',
		'SizeName' => 'מידה',
		'ColorName' => 'צבע',
		
		'CompanyName' => 'שם העסק',
		'BusinessType' => 'סוג העסק',
		'CompanyId' => 'תעודת זהות',
		'JobsRole1' => 'תפקיד\מחלקה',
		'ContactName1' => 'שם איש קשר',
		'ContactMobile1' => 'נייד',
		'PaymentRole' => 'תנאי תשלום',
		'SupplierType' => 'סיווג העסק',
        'TempIdPOSCancelDocs' => 'לא נמצאו פריטים במסמך',
		
		'ContactMobile' => 'טלפון סלולרי',
		'SetDate' => 'תאריך התחלה',
        'Day' => 'יום',
        'SetTime' => 'שעת התחלה',
        'SetToTime' => 'שעת סיום',
        'ActivityId ' => 'סוג מנוי',
        'membership_type' => 'סוג מנוי',
        'ItemName' => 'כותרת',
        'ItemPrice' => 'מחיר',
        
        'Email' => 'דואר אלקטרוני',
        'FirstName' => 'שם פרטי',
        'LastName' => 'שם משפחה',
        'Dob' => 'תאריך לידה',
        'phone' => 'טלפון סלולרי',
		'Type' => 'כותרת',
		
		//// העשייה שלי
	 
	 'ArchiveTask' => 'יומן העשייה שלי',
	 'TaskType' => 'סוג עשייה',
	 'TaskType1' => 'משימה',
	 'TaskType2' => 'אירוע',
	 'TaskType3' => 'פרויקט',
	 'TaskType4' => 'הזדמנות',
	 
	 'TaskGoal' => 'חבר עשייה למטרה',
	 'TaskGoal1' => 'ללא מטרה',
	 'TaskGoal2' => '(מטרה אימונית)',
	 
	 'TaskTitle' => 'כותרת',
	 'StartDate' => 'תאריך התחלה',
	 'StartTime' => 'שעת התחלה',
	 'EndDate' => 'תאריך סיום',
	 'EndTime' => 'שעת סיום',
	 'AllDay' => 'אירוע של יום שלם?',
	 'AllDay1' => 'כן',
	 'AllDay2' => 'לא',
	 'priority_1' => 'דרגת חשיבות',
	 'priority_2' => 'נמוכה',
	 'priority_3' => 'בינונית',
	 'priority_4' => 'גבוהה',
	 'AreaOfLife' => 'תחומי חיים',
	 
	 'TaskDetails' => 'פרטי הפעילות',
	 'Remarks' => 'הערות',
	 
	 'TaskStatus' => 'סטטוס עשייה',
	 'TaskStatus1' => 'לא הושלם',
	 'TaskStatus2' => 'הושלם',
	 
	 'Challenge' => 'פעולה מאתגרת?',
	 'Challenge2' => 'פעולה מאתגרת',
	 'sync' => 'סנכרן פעילות עם Google Calendar/Outlook',
	 
	 'AddTask' => 'הוסף >>',
	 'UpdateTask' => 'עדכן >>',
	 
	 'Color' => 'צבע',
	 'Color1' => 'ברירת מחדל',
	 'Color2' => 'כחול מודגש',
	 'Color3' => 'כחול',
	 'Color4' => 'טורקיז',
	 'Color5' => 'ירוק',
	 'Color6' => 'ירוק מודגש',
	 'Color7' => 'צהוב',
	 'Color8' => 'כתום',
	 'Color9' => 'אדום',
	 'Color10' => 'אדום מודגש',
	 'Color11' => 'סגול',
	 'Color12' => 'אפור',
	 
	 
	 
	 'Client' => 'לקוח',
	 'Dates' => 'תאריך',
	 'DocTempId' => 'פריט/שירות',



	 'Title' => 'כותרת',
	 'TitleUrl' => 'כותרת לדף',
	 'ItemId' => 'פריט',
	 'Content' => 'תוכן',
	 'ImageLink' => 'לוגו',
	 'TypePage' => 'סוג עמוד',
	 'Amount' => 'סכום',

        'GuideId' => 'מאמן ראשי',
        'ClassName' => 'שם שיעור',
        'MaxClient' => 'מקסימום משתתפים',
        'Floor' => 'יומן',
        'ClassNameType' => 'סוג שיעור',
        'ReminderAmount' => 'תזמון תזכורת',
        'ReminderType' => 'תזמון תזכורת',
        'purchaseAmount' => 'מחיר הרשמה',
        'CancelPeriodAmount' => 'תזמון ביטול',
        'CancelPeriodType' => 'תזמון ביטול',
        'MinClassNum' => 'כמות מינימום משתתפים',
        'ClassTimeCheck' => 'תזמון בדיקת מינימום משתתפים',
        'ClassTimeTypeCheck' => 'תזמון בדיקת מינימום משתתפים',
        'templates' => 'מפגשים'

	),

);
