<?php

return array(
	'noreply' => 'Please do not reply to this message; it was sent from an unmonitored email address.',
	
	'activation_subject' => 'Please activate your account',
	'activation_message' => '<p>Hi,</p><p>Please confirm your email address by clicking on the link below:</p>',
	'confirm_email' => 'Confirm my email address',
	
	'reminder_subject' => lang('restore_password_emails'),
	'reminder_message' => '<p>'.lang('hello_two_ajax').'</p><p>'.lang('new_restore_password_emails').' <br> '.lang('click_restore_emails').'</p>',
	'reset_password' => lang('password_emails'),
	
	'new_user_subject' => lang('system_login_emails'),
	'new_user_message' => '<p>'.lang('hello_two_ajax').'</p><p>'.lang('login_cred_emails').'</p>
					<p>שם משתמש: :username</p>
					<p>סיסמה: :password</p>
					<p>:url</p>',

	'new_message_subject' => 'התקבלה הודעה חדשה מ- :user',
	'new_message' => '<p>למענה עבור ל- <a href=":link">הודעות פרטיות</a>.</p>
					<p>תוכן ההודעה: <br> <i>:message</i></p>',
);