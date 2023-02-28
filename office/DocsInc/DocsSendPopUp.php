
<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="SendInvMailPOPUP" tabindex="-1">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title">שליחת חשבונית במייל</h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="SendMailToClientAgain"  class="ajax-form clearfix">
 <input type="hidden" name="Id">
<div id="resultSendInvMail">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="SendInvSMSPOPUP" tabindex="-1">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title">שליחת חשבונית בהודעת SMS</h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="SendSMSToClientAgain"  class="ajax-form clearfix">
 <input type="hidden" name="Id">
<div id="resultSendInvSMS">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->
