						</div>
					</div>
                    <div class="row d-flex justify-content-center p-20">
                        <a class="mie-30 text-info text-underline" href="https://site.boostapp.co.il/terms/" target="_blank"><?= lang('login_terms_facebook') ?></a>
                        <a class="text-gray-400 text-underline" href="https://site.boostapp.co.il/privacy/" target="_blank"><?= lang('login_policy_facebook') ?></a>
                    </div>
					<div class="footer" dir="rtl">
						© <?php echo date('Y');?> כל הזכויות שמורות ל-BOOSTAPP, מערכת לניהול סטודיו
					</div>
				</div>
			</div>
		</div>
	</section>



	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script> 
	<script>
        $(function () {
            $("input[type='password'][data-eye]").each(function (i) {
                var $this = $(this);

                $this.wrap($("<div/>", {
                    style: 'position:relative'
                }));
                $this.css({
                    paddingLeft: 60
                });
                $this.after($("<div/>", {
                    html: '<i class="fad fa-eye fa-lg"></i>',
                    class: 'font-weight-bold',
                    id: 'passeye-toggle-' + i,
                    style: 'position:absolute;left:10px;top:50%;transform:translate(0,-50%);-webkit-transform:translate(0,-50%);-o-transform:translate(0,-50%);padding: 2px 7px;font-size:12px;cursor:pointer;'
                }));
                $this.after($("<input/>", {
                    type: 'hidden',
                    id: 'passeye-' + i
                }));
                $this.on("keyup paste", function () {
                    $("#passeye-" + i).val($(this).val());
                });
                $("#passeye-toggle-" + i).on("click", function () {
                    if ($this.hasClass("show")) {
                        $this.attr('type', 'password');
                        $this.removeClass("show");
                        $(this).removeClass("text-success");
                    } else {
                        $this.attr('type', 'text');
                        $this.val($("#passeye-" + i).val());
                        $this.addClass("show");
                        $(this).addClass("text-success");
                    }
                });
            });
        });
	</script>
	<?php echo View::make('modals.load')->render() ?>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3BPF8F"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</body>
</html>