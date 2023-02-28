<div class="ip-modal text-start" id="green_pass_modal" tabindex="-1" data-backdrop="static">
    <div class="ip-modal-dialog w-600p">
        <div class="ip-modal-content">
            <div class="ip-modal-header d-flex justify-content-between">
                <h4 class="ip-modal-title"><i class="fas fa-badge-check"></i> <?php echo lang('green_passport') ?></h4>
                <a class="ip-close js-greenpass-dismiss" title="Close"  aria-hidden="true" style="">&times;</a>
            </div>
            <div class="ip-modal-body">
                <input type="hidden" id="clientId" value="<?php echo $Supplier->id ?>">
                <div class="text-start">
                    <div class="d-flex align-items-center mb-20">
                    <div class="mie-9 max-w-32">
                        <img class="rounded-circle img-fluid" alt="<?php echo htmlentities($Supplier->CompanyName); ?>" src="<?php echo (empty($CheckUserApp->UploadImage)) ? 'https://ui-avatars.com/api/?name='.$Supplier->LastName.'+'.$Supplier->FirstName.'&background='.hexcode($Supplier->CompanyName).'&color=ffffff&font-size=0.55&size=32' : get_appboostapp_domain().'/camera/uploads/large/'.$CheckUserApp->UploadImage; ?>">
                    </div>
                    <div>
                        <div>
                        <span class="font-weight-bold "><?= $Supplier->CompanyName ?></span>
                        <?php echo $modalIconStatus ?? '' ?>
                        </div>
                        
                        <span class="text-gray-400 unicode-plaintext bsapp-fs-14 font-weight-bold "><?= !empty($Supplier->ContactMobile) ? $Supplier->ContactMobile : '' ?></span>
                    </div>
                    </div>

                    <div class="px-15 bsapp-fs-14">
                    <span class="font-weight-bold"><?= lang('green_passport') ?></span>
                    <div class="d-flex flex-column mt-7 mb-20">
                        <div class="mb-10 w-250p border rounded p-8 js-greenpass-checkbox-div <?= !$Supplier->greenPassStatus ? 'active-checkbox-selection' : '' ?> ">
                        <div class="pretty p-default p-round p-thick mr-0">
                            <input type="radio" value="0"  name="greenPassStatus" <?= !$Supplier->greenPassStatus ? 'checked' : '' ?>/>
                            <div class="state p-danger">
                                <label class="font-weight-bold pretty-checbox-lbl"><?= lang('no_green_passport') ?></label>
                            </div>
                        </div>
                        </div>
                        <div class="mb-10 bsapp-min-w-250p width-fit-important border rounded p-8 js-greenpass-checkbox-div <?= $Supplier->greenPassStatus == 1 ? 'active-checkbox-selection' : '' ?>">
                        <div class="pretty p-default p-round p-thick mr-0">
                            <input type="radio" value="1" name="greenPassStatus" <?= $Supplier->greenPassStatus == 1 ? 'checked' : '' ?>/>
                            <div class="state p-warning">
                                <label class="font-weight-bold pretty-checbox-lbl"><?= lang('green_passport_pending_client') ?></label>
                            </div>
                        </div>
                        </div>
                        <div class="mb-10 w-250p border rounded p-8 js-greenpass-checkbox-div <?= $Supplier->greenPassStatus == 2 ? 'active-checkbox-selection' : '' ?>">
                        <div class="pretty p-default p-round p-thick mr-0">
                            <input type="radio" value="2" name="greenPassStatus" <?= $Supplier->greenPassStatus == 2 ? 'checked' : '' ?>/>
                            <div class="state p-success">
                                <label class="font-weight-bold pretty-checbox-lbl"><?= lang('confirmed_green_passport_client') ?></label>
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="js-greenpass-exp-div mb-20  <?= !$Supplier->greenPassStatus ? 'd-none' : '' ?>">
                        <span class="font-weight-bold"><?= lang('expires_at') ?></span>
                        <div class="w-250p">
                        <input type="date" class="form-control-custom js-greenpass-exp-date" value="<?= !empty($Supplier->greenPassValid) ? $Supplier->greenPassValid : '' ?>">
                        </div>
                    </div>

                    </div>
                        
                </div>
                <div class="text-center text-danger d-none js-greenpass-err">
                    <span><?= lang('action_cancled') ?></span>
                </div>
                
            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light ip-close js-greenpass-dismiss"><?php _e('main.close') ?></button>
                <div class="ip-actions">
                    <a class="btn btn-primary text-white js-greenpass-submit-btn"><?php _e('main.save_changes') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function GreenPassIcon(status,clientId) {
        if(status == 0){
            let greenPassText = "<?= lang('no_green_passport') ?>";
            let cssClass = 'text-danger';
            let coronaIcon = '<i data-id="' + clientId +'" class="js-green-pass-icon far fa-badge cursor-pointer mis-5 ' + cssClass + '" title="' + greenPassText +'"></i>';
            return coronaIcon;
        }
        if(status == 1){
            let greenPassText = "<?= lang('green_passport_pending_client') ?>";
            let cssClass = 'text-orange';
            let coronaIcon = '<i data-id="' + clientId +'" class="js-green-pass-icon far fa-badge-check cursor-pointer mis-5 ' + cssClass + '" title="' + greenPassText +'"></i>';
            return coronaIcon;
        }
        if(status == 2){
            let greenPassText = "<?= lang('confirmed_green_passport_client') ?>";
            let cssClass ='text-success';
            let coronaIcon = '<i data-id="' + clientId +'" class="js-green-pass-icon fas fa-badge-check cursor-pointer mis-5 ' + cssClass + '" title="' + greenPassText +'"></i>';
            return coronaIcon;
        }
    }

    function updateClientProfile(status, date) {
        var elm = $('.js-greenpass-content');
        if(status == 0) {
            var greenPassText = "<?= lang('no_green_pass') ?>";
            var cssClass = 'text-danger';
            var badgeIcon = '<i class="far fa-badge fa-lg"></i>';
            
        }
        if(status == 1) {
            var greenPassText = "<?= lang('green_pass_pending_notice') ?>";
            var cssClass = 'text-orange';
            var badgeIcon = '<i class="far fa-badge-check fa-lg"></i>';

        }
        if(status == 2) {
            var greenPassText = "<?= lang('green_pass_confirmed_notice') ?>";
            var cssClass ='text-success';
            var badgeIcon = '<i class="fas fa-badge-check fa-lg"></i>';

        }

        elm.find('.js-greenpass-text-color').removeClass('text-success').removeClass('text-orange').removeClass('text-danger').addClass(cssClass);
        elm.find('.js-greenpass-text-color i').remove();
        elm.find('.js-greenpass-text-color').prepend(badgeIcon);
        elm.find('.js-greenpass-text').text(greenPassText);

        if(date && status != 0) {
            if(elm.find('.js-greenpass-date').length) {
                elm.find('.js-greenpass-date-span').text(date);
                elm.find('.js-greenpass-date').removeClass('text-danger').addClass('text-black');
            } else {
                var html = '<span class="font-weight-bold bsapp-fs-13 line-1-5 js-greenpass-date text-black"><?php echo lang('expires_at').': ' ?><span class="js-greenpass-date-span">'+date+'</span></span>';
                elm.find('.js-greenpass-text').after(html);
            }

            var d = Date.parse(date);
            var current = Date.parse(new Date());
            if(d < current) {
                elm.find('.js-greenpass-date').removeClass('text-black').addClass('text-danger');
            }

        } else if(status == 0) {
            elm.find('.js-greenpass-date').remove();
        }

    }

    $(document).ready(function() {
        $('input[name="greenPassStatus"]').on('change', function() {
            $('.js-greenpass-checkbox-div').removeClass('active-checkbox-selection');
            $(this).closest('.js-greenpass-checkbox-div').addClass('active-checkbox-selection');
            var value = $(this).val();
            if(value == 0) {
                $('.js-greenpass-exp-div').addClass('d-none');
            } else {
                $('.js-greenpass-exp-div').removeClass('d-none');
            }
        });

        $('.js-greenpass-submit-btn').on('click', function() {
            $(this).addClass('disabled');
            if(!$(this).children('i').length) {
                $(this).append(' <i class="far fa-spinner-third fast-spin">');
            }
            var input = $('input[name="greenPassStatus"]:checked').val();
            var date = $('input.js-greenpass-exp-date');
            var d = new Date(date.val());
            var current = new Date();
            if (!input) {
                input = 0;
            } 
            if((!date.val() || d < current) && !$('.js-greenpass-exp-div').hasClass('d-none')) {
                date.addClass('border-red-required');
                $(this).removeClass('disabled');
                $('.js-greenpass-submit-btn i').remove();
                return;
            }
            var data = {
                status: input,
                date: date.val(),
                clientId: $('#clientId').val()
            }
            $.ajax({
                url: '/office/action/greenPassUpdate.php',
                type: 'POST',
                data: data,
                success: function(response) {
                    var res = JSON.parse(response);

                    if(res.success) {
                        if(!$("#reportGreen").length && !$('#greenPassModalClientList').length) {
                            console.log('clientProfile');
                            updateClientProfile(data.status, data.date);

                        }

                        $('#green_pass_modal').modal('hide');
                        $('.js-greenpass-submit-btn').removeClass('disabled');
                        $('.js-greenpass-submit-btn i').remove();

                        if ( $("#reportGreen").length ) {
                            $('#reportGreen').DataTable().ajax.reload();
                            $('#green_pass_modal').remove();
                        }
                        if($('#greenPassModalClientList').length){
                            let client = $('#clientId').val();
                            $('.js-green-pass-icon[data-id="'+client+'"]').replaceWith(GreenPassIcon(input,client));
                            $("#green_pass_modal").remove();

                        }
                    
                    } else {
                        $('.js-greenpass-submit-btn').removeClass('disabled');
                        $('.js-greenpass-submit-btn i').remove();
                        if(res.msg != "") {
                            $('.js-greenpass-err span').text(res.msg).parent().removeClass('d-none');
                            setTimeout(() => {
                                $('.js-greenpass-err span').text('').parent().addClass('d-none');
                            }, 3000);
                        }

                    }

                    
                },
                error: function(err) {
                    $('.js-greenpass-submit-btn').removeClass('disabled');
                    $('.js-greenpass-submit-btn i').remove();
                }
            });

        });

        $('body').on('input', 'input.js-greenpass-exp-date', function() {
            if($(this).val() && !$('.js-greenpass-exp-div').hasClass('d-none')) {
                $(this).removeClass('border-red-required');
            }
        });

        $("body").on('click', '.js-greenpass-dismiss', function() {
            $('#green_pass_modal').modal('hide');
            if($("#reportGreen").length || $('#greenPassModalClientList').length) {
                $("#green_pass_modal").remove();
            }
        })
    });
    
</script>
