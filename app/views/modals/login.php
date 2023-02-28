<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="login" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</button>
					<h4 class="modal-title"><?php _e('main.login') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>

					<div class="form-group">
		                <label for="email"><?php _e('main.email_username') ?></label>
		                <input type="text" name="email" id="email" class="form-control">
		            </div>
		            
		            <div class="form-group">
		                <label for="password"><?php _e('main.password') ?></label>
		                <input type="password" name="password" id="password" class="form-control">
		            </div>
                    
                    <input type="hidden" name="TypeLogin" value="0">
                
		           
                   <input type="hidden" name="AppURL" value="<?php echo App::url();?>">
                   
		            <div class="form-group">
		                <div class="checkbox">
			                <label><input type="checkbox" name="remember" value="1"> <?php _e('main.remember') ?></label>
			            </div>
		            </div>

		         
				</div>
				<div class="modal-footer">
					<div class="pull-left">
						<button type="submit" class="btn btn-primary"><?php _e('main.login') ?></button>
					</div>
	
				</div>
			</form>
		</div>
	</div>
</div>