<?php
require_once 'app/init.php';

if (Auth::check() || (empty($_GET['reminder']) && !Session::has('password_updated'))) {
	redirect_to(App::url());
}
?>

<?php echo View::make('header')->render() ?>

<div class="row">
	<div class="col-md-12" dir="rtl">
		
		<?php if (Session::has('password_updated')): Session::deleteFlash(); ?>
			<h4 class="card-title text-center"><?php echo _e('main.reset_success') ?></h4>
			<p><?php _e('main.reset_success_msg') ?></p><br>
			<p><a href="/index.php" class="btn btn-info btn-block"><?php _e('main.login') ?></a></p>
		<?php else: ?>
			<h4 class="card-title text-center"><?php echo _e('main.recover_pass') ?></h4>
			
			<form action="reset" class="ajax-form clearfix" dir="rtl">
				<div class="form-group">
	                <label for="reset-pass1"><strong><?php _e('main.newpassword') ?></strong></label>
	                <input type="password" name="pass1" id="reset-pass1" class="form-control">
	            </div>
	            
	            <div class="form-group">
	                <label for="reset-pass2"><strong><?php _e('main.newpassword_confirmation') ?></strong></label>
	                <input type="password" name="pass2" id="reset-pass2" class="form-control">
	            </div>
	            
	            <div class="form-group pull-left">
					<button type="submit" name="submit" class="btn btn-success  btn-block"><?php _e('main.change_pass') ?></button>
				</div>
				
				<input type="hidden" name="reminder" value="<?php echo escape($_GET['reminder']) ?>">
			</form>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('footer')->render() ?>