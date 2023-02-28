<?php
ini_set("max_execution_time", 0);

require_once "../app/init.php";
require_once "Classes/Translations.php";
require_once "../app/helpers/MultiUserHelper.php";

if (!Auth::check()) {
    redirect_to('//' . $_SERVER['HTTP_HOST']);
}

$theme_prefix = 'bsapp';
$_SESSION['lang'] = $_COOKIE['boostapp_lang'] ?? 'he';
$pageTitle = lang('checkout');
?>

<!DOCTYPE html>
<html lang="<?= isset($_COOKIE['boostapp_lang']) ? ($_COOKIE['boostapp_lang'] == 'eng' ? 'en' : $_COOKIE['boostapp_lang']) : 'he'; ?>"
      dir="<?= isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_dir'] : 'rtl'; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">

    <title><?= (isset($pageTitle) ? $pageTitle . ' | ' : '') . Config::get('app.name') ?></title>
    <?php include '../assets/office/develop/templates/partials/meta-tags.php'; ?>

	<link href="/assets/office/fontawesome6/pro/css/fontawesome.css" rel="stylesheet">
	<link href="/assets/office/fontawesome6/pro/css/solid.css" rel="stylesheet">
	<link href="/assets/office/fontawesome6/pro/css/light.css" rel="stylesheet">

    <link href="/assets/office/dist/vendor.bundle.css" rel="stylesheet">
    <link href="/assets/office/dist/checkout.bundle.css" rel="stylesheet">

	<script src="/assets/office/dist/vendor.bundle.js" defer></script>
    <script src="/assets/office/dist/checkout.bundle.js" defer></script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-P3BPF8F');</script>
    <!-- End Google Tag Manager -->
</head>

<body id="checkoutPage" class="<?= $theme_prefix; ?>__checkout-page">

<div id="preloader" class="body-full-preloader">
	<svg class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="4" stroke-dasharray="140px" stroke-linecap="round" stroke="#00c736" cx="33" cy="33" r="30" /></svg>
</div>

<header id="header" class="fixed d-flex">
    <div class="header--bar-title centralize d-none d-lg-flex align-items-center justify-content-center">
        <h2 class="h3 title"><?= lang('checkout_invoice_payment') ?></h2>
    </div>
    <div class="header--bar-icons d-flex align-items-center justify-content-between">
        <a href="/office/cart.php"
           class="btn btn--icon icon-sm bsapp--to-cart-page js--return-to-cart-page"
           type="button"
           title="<?= lang('cart_title') ?>">
            <i class="s-20 fa-light fa-angle-right ltr--rotate"></i>
        </a>
        <h2 class="h3 title header--bar-mob-title"><?= lang('payment_single') ?></h2>
        <a href="/office"
		   class="btn btn--icon icon-sm bsapp--to-previous-page js--open-confirm-exit"
           type="button" title="<?= lang('exit_action') ?>">
            <i class="fa-light fa-xmark"></i>
        </a>
    </div>
</header>

<main id="main" class="main--container main--container-aside">
    <div class="main--section d-none d-lg-block">
        <section id="mainSectionCheck" class="main--section-check">
            <div class="checkout--pdf-container pb-0">
                <?php
                $file_contents = include(__DIR__.'/PDF/layout/docs-header.php');
                echo $file_contents;
                ?>
            </div>
            <div id="checkoutDocsPreview"></div>
        </section>
    </div>

    <aside id="summaryAside" class="main--aside main--aside-checkout h-full p-b-bar">
		<div class="aside--customer">
			<p class="bsapp--label s-14"><?= lang('client_details_class') ?></p>
			<div id="userSelect" class="d-flex justify-content-between bsapp--open-user-sidebar js--open-user-sidebar"></div>
		</div>
		<div class="aside--price">
			<div class="form--group-items">
				<label for="totalPrice" class="bsapp--label">
					<?= lang('price_to_charge') ?>
				</label>
				<div class="form--group-rel">
					<input id="totalPrice"
						   class="form--group-input bsapp--icon-input full input--big"
						   name="checkoutTotalPrice"
						   type="number"
						   data-validate-price=""
						   value="0"
						   data-price="0"
						   pattern="[0-9]([\.][0-9])?"
						   autocomplete="off"
						   step="0.01"
						   min="0">
					<span class="form--group-icon"><i class="fa-light fa-shekel-sign"></i></span>
				</div>
				<p class="error form--group-error"></p>
			</div>
		</div>
		<div id="asideCheckout" class="aside--summary aside--checkout-summary">
			<div class="aside--summary-items">
				<div class="aside--checkout-buttons">
					<button id="payCash"
							class="btn btn--primary-border btn--bigger d-flex align-items-center justify-content-between"
							data-pay-type="cash"
							type="button">
						<span><?= lang('cash') ?></span>
						<i class="fa-light fa-money-bill-simple-wave"></i>
					</button>
					<button class="js--open-half-sidebar btn btn--primary-border btn--bigger d-flex align-items-center justify-content-between"
							data-pay-type="credit"
							type="button">
						<span><?= lang('credit_card') ?></span>
						<i class="fa-light fa-credit-card"></i>
					</button>
					<button class="js--open-half-sidebar btn btn--primary-border btn--bigger d-flex align-items-center justify-content-between"
							data-pay-type="check"
							type="button">
						<span><?= lang('check') ?></span>
						<i class="fa-light fa-money-check-pen"></i>
					</button>
					<button class="js--open-half-sidebar btn btn--primary-border btn--bigger d-flex align-items-center justify-content-between"
							data-pay-type="bankTransfer"
							type="button">
						<span><?= lang('checkout_transfer') ?></span>
						<i class="fa-light fa-building-columns"></i>
					</button>
					<!--					<button class="js--open-half-sidebar btn btn--primary-border btn--bigger d-flex align-items-center justify-content-between"-->
					<!--							data-pay-type="link"-->
					<!--							type="button">-->
					<!--						<span>--><?//= lang('link') ?><!--</span>-->
					<!--						<i class="fa-light fa-link"></i>-->
					<!--					</button>-->
				</div>
			</div>
		</div>
		<div id="checkoutBottomTransaction" class="aside--checkout-transaction"></div>
		<div id="checkoutBottomOptions" class="aside--summary-total">
			<div class="aside--summary-options">
				<button id="checkoutBtnOptions"
						class="btn btn--primary-revert btn--big btn--full d-flex align-items-center justify-content-center btn--modal-dropdown"
						type="button">
					<span><?= lang('meeting_options') ?></span>
					<i class="fa-light fa-chevron-down d-none d-lg-inline-block mi-start"></i>
				</button>
			</div>
		</div>

		<div id="bsappHalfSidebar" class="bsapp--half-sidebar-payment hidden">
			<div class="modal-dialog"></div>
		</div>
    </aside>

    <div id="confirmationAside" class="main--aside main--aside-confirmation h-full d-none">
        <div class="aside--summary aside--checkout-summary aside--checkout-confirmation d-flex flex-column">
            <div class="aside--summary-items">
                <div id="confirmationAsideContent" class="aside--success-icons text-center"></div>
                <div class="aside--success-fields">
                    <div class="form--group-items">
                        <label for="generalItemName" class="bsapp--label"><?= lang('checkout_share_document') ?></label>
                        <div class="form--group-rel">
                            <input class="form--group-input full input--big js--group-input--remove"
                                   name="phone" ltr
                                   required
                                   type="tel"
                                   pattern="^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$"
                                   value=""
                                   validate-only-number
                                   placeholder="<?= lang('mob_phone_number') ?>">
                            <button class="form--group-icon form--group-icon--remove js--group-icon--remove" type="button"><svg class="svg-inline--fa fa-circle-xmark" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="circle-xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M180.7 180.7C186.9 174.4 197.1 174.4 203.3 180.7L256 233.4L308.7 180.7C314.9 174.4 325.1 174.4 331.3 180.7C337.6 186.9 337.6 197.1 331.3 203.3L278.6 256L331.3 308.7C337.6 314.9 337.6 325.1 331.3 331.3C325.1 337.6 314.9 337.6 308.7 331.3L256 278.6L203.3 331.3C197.1 337.6 186.9 337.6 180.7 331.3C174.4 325.1 174.4 314.9 180.7 308.7L233.4 256L180.7 203.3C174.4 197.1 174.4 186.9 180.7 180.7zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 32C132.3 32 32 132.3 32 256C32 379.7 132.3 480 256 480C379.7 480 480 379.7 480 256C480 132.3 379.7 32 256 32z"></path></svg><!-- <i class="fa-light fa-circle-xmark"></i> --></button>
                        </div>
                    </div>
                    <div class="aside--checkout-buttons">
                        <button id="shareSMS"
                                class="btn btn--primary-revert btn--bigger d-flex align-items-center justify-content-between"
                                title="<?= lang('message_sent') ?>"
                                type="button">
                            <span class="s-18">SMS</span>
                            <i class="fa-light fa-paper-plane"></i>
                        </button>
                        <button id="shareWhatsApp"
                           title="<?= lang('send_link_whatsapp') ?>"
                           target="_blank"
                           class="btn btn--primary-revert btn--bigger d-flex align-items-center justify-content-between">
                            <span class="s-18">WhatsApp</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="aside--summary-total">
                <div class="aside--summary-options">
                    <button id="printInvoicePdf"
                            class="btn btn--primary-border-light btn--big btn--full d-flex align-items-center justify-content-center"
                            type="button">
                        <span><?= lang('action_print') ?></span>
                        <i class="fa-light fa-print mi-start"></i>
                    </button>
                    <div class="d-flex summary-total--btns">
                        <button class="btn btn--primary-revert btn--big btn--full js--cart-clear-all-without-modal"
                                type="button">
                            <span><?= lang('class_end') ?></span>
                        </button>
                        <button id="cartSummaryOptionsAside"
                        class="cart--options-btn btn btn--primary-revert btn--icon icon-big cart-btn-options js--checkout-btn-options mt-0"
                                type="button">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="bsappHalfSidebarOverlay"></div>

<?php include '../assets/office/develop/templates/partials/modals.php'; ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3BPF8F" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</body>
</html>