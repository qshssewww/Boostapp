<?php
require_once 'app/init.php';

if (Auth::check()) redirect_to(App::url());
?>			

<?php echo View::make('header')->render() ?>

<div class="row">
	<div class="col-md-12" dir="rtl">
		<?php if (Session::has('reminder_sent')): Session::deleteFlash(); ?>
			<h4 class="card-title text-center"><?php _e('main.check_email') ?></h4>
			<?php _e('main.reminder_check_email') ?>
		<?php else: ?>
			<h4 class="card-title text-center"><?php echo _e('main.recover_pass') ?></h4>
			
			<form action="reminder" class="ajax-form" dir="rtl">				
				<div class="form-group">
			        <label for="reminder-email"><strong><?php _e('main.enter_email') ?></strong></label>
			        <input type="text" name="email" id="reminder-email" class="form-control">
			    </div>
				
				<?php if (Config::get('auth.captcha')): ?>
				    <div class="form-group recaptcha">
				    	<label for="recaptcha_response_field"><?php _e('main.enter_captcha') ?></label>
						<div id="recaptcha_widget" class="recaptcha-outer" style="display:none">
							<div id="recaptcha_image" class="recaptcha-image"></div>
						    <div class="recaptcha-controls">
								<div><a href="javascript:Recaptcha.reload()" tabindex="-1"><?php _e('main.captcha_reload') ?></a> |</div>
								<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')" tabindex="-1"><?php _e('main.captcha_listen') ?></a> |</div>
								<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')" tabindex="-1"><?php _e('main.captcha_image') ?></a> |</div>
								<div><a href="javascript:Recaptcha.showhelp()" tabindex="-1"><?php _e('main.captcha_help') ?></a></div>
							</div>
							<input type="text" name="captcha" id="recaptcha_response_field" class="form-control">
						</div>
						<script type="text/javascript">
							var RecaptchaOptions = {
							    theme : 'custom',
							    custom_theme_widget: 'recaptcha_widget'
							};
						 </script>
						<script src="http://www.google.com/recaptcha/api/challenge?k=<?php echo Config::get('services.recaptcha.public_key') ?>"></script>
				    </div>
				<?php endif ?>

			    <div class="form-group">
			    	<button type="submit" name="submit" class="btn btn-success  btn-block"><?php _e('main.continue') ?></button>
			    </div>
			</form>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('footer')->render() ?>