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
$pageTitle = lang('cart_title');
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
    <link href="/assets/office/dist/cart.bundle.css" rel="stylesheet">

	<script src="/assets/office/dist/vendor.bundle.js" defer></script>
	<script src="/assets/office/dist/cart.bundle.js" defer></script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-P3BPF8F');</script>
    <!-- End Google Tag Manager -->
</head>

<body id="cartPage" class="<?= $theme_prefix; ?>__cart-page">

<div id="preloader" class="body-full-preloader">
	<svg class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="4" stroke-dasharray="140px" stroke-linecap="round" stroke="#00c736" cx="33" cy="33" r="30" /></svg>
</div>

<header id="header" class="fixed d-flex align-items-center justify-content-between">
    <div class="header--bar-title d-flex align-items-center">
        <i class="fa-light fa-cash-register"></i>
        <h2 class="h3 title"><?= lang('cart_title') ?></h2>
    </div>
    <a href="/office" class="btn btn--icon icon-sm bsapp--to-previous-page js--open-confirm-exit"
       type="button" title="<?= lang('exit_action') ?>">
        <i class="fa-light fa-xmark"></i>
    </a>
</header>

<main id="main" class="main--container main--container-aside">
    <div class="main--section">
        <div class="nav d-flex justify-content-lg-between flex-column flex-lg-row-reverse">
            <div class="cart--nav-buttons d-flex align-items-center">
                <button class="btn btn--icon-lg btn--plus d-flex align-items-center"
                        data-open-modal="bsappItemGeneralModal"
                        type="button">
                    <span><?= lang('general_item') ?></span>
                    <i class="fa-light fa-plus"></i>
                </button>
                <button id="openSearchModal"
                        class="btn btn--icon btn--search"
                        type="button">
                    <i class="fa-light fa-magnifying-glass"></i>
                    <span class="d-lg-none"><?= lang('search_looking_for') ?></span>
                </button>
                <button id="openDocsDropdown"
                        class="btn btn--icon btn--file"
                        type="button">
                    <i class="fa-light fa-file"></i>
                </button>
            </div>
            <ul id="cartNavCategories" class="bsapp--tabs category--tabs"></ul>
        </div>
        <section id="cartSubcategories" class="bsapp--tab-content"></section>
    </div>
    <aside id="summaryAside" class="main--aside h-full">
		<div class="aside--header d-flex d-lg-none align-items-center justify-content-between">
			<button class="btn btn--icon icon-sm bsapp--to-cart-items js--to-cart-items"
					type="button" title="<?= lang('exit_action') ?>">
				<i class="fa-light fa-angle-right ltr--rotate s-20"></i>
			</button>
			<h4><?= lang('cart_order_details') ?></h4>
			<a href="/office" class="btn btn--icon icon-sm bsapp--to-previous-page js--open-confirm-exit"
			   type="button" title="<?= lang('exit_action') ?>">
				<i class="fa-light fa-xmark"></i>
			</a>
		</div>
		<div class="aside--cart-container h-full">
			<div class="aside--customer">
				<p class="bsapp--label s-14"><?= lang('client_details_class') ?></p>
				<div id="userSelect" class="d-flex justify-content-between bsapp--open-user-sidebar js--open-user-sidebar"></div>
			</div>
			<div id="asideSummary" class="aside--summary"></div>
		</div>
    </aside>
</main>

<!-- Modal - add a general item -->
<div id="bsappItemGeneralModal" class="bsapp--modal bsapp--popup" data-animation="scaleInOut">
    <div class="modal-dialog">
        <div class="modal--preloader js--modal-preloader d-none">
            <div class="spinner-border"><span class="sr-only"><?= lang('loading') ?></span></div>
        </div>
        <header class="modal-header p-0 flex-column">
            <div class="modal-header_content d-flex align-items-center justify-content-between">
                <h3 class="h3"><?= lang('add_general_item') ?></h3>
                <button class="close-modal js--close-modal" aria-label="close modal" data-close="" type="button">
                    <i class="fa-light fa-xmark"></i>
                </button>
            </div>
        </header>
        <section class="modal-body p-0">
            <div class="form--group-items">
                <label for="generalItemName" class="bsapp--label"><?= lang('item_name') ?></label>
                <div class="form--group-rel">
                    <input id="generalItemName"
                           class="form--group-input full js--group-input--remove"
                           name="itemName"
                           type="text"
                           required
                           value="<?= lang('general') ?>"
                           placeholder="<?= lang('type_item_name') ?>">
                    <button class="form--group-icon form--group-icon--remove js--group-icon--remove"
                            type="button"><i class="fa-light fa-circle-xmark"></i></button>
                </div>
            </div>
            <div class="form--group-items">
                <label for="generalItemPrice" class="bsapp--label"><?= lang('summary') ?></label>
                <div class="form--group-rel">
                    <input id="generalItemPrice"
                           class="form--group-input bsapp--icon-input full text-secondary"
                           name="generalItemPrice"
                           type="text"
                           value="0.00"
						   step="0.01"
						   pattern="[0-9]+([\.,][0-9]+)?"
                           data-price="0.00"
                           data-current-operand=""
                           placeholder="<?= lang('summary') ?>"
                           required>
                    <span class="form--group-icon"><i class="fa-light fa-shekel-sign"></i></span>
                </div>
            </div>
            <div class="form--group-numbers">
                <div class="form--grid js--form-grid">
                    <button data-number>1</button>
                    <button data-number>2</button>
                    <button data-number>3</button>
                    <button data-number>4</button>
                    <button data-number>5</button>
                    <button data-number>6</button>
                    <button data-number>7</button>
                    <button data-number>8</button>
                    <button data-number>9</button>
                    <button data-number>.</button>
                    <button data-number>0</button>
                    <button data-delete class="form--group-remove">
                        <i class="fa-light fa-delete-right"></i></button>
                </div>
            </div>
        </section>
		<footer class="modal-footer btn--revert btn--to-half-mob without--b-top d-flex justify-content-between justify-content-md-start">
            <button id="addGeneralItem"
                    class="btn btn--primary btn--big"
                    type="button"><?= lang('save') ?></button>
            <button class="btn btn--primary-revert btn--big close-modal js--close-modal"
                    type="button"><?= lang('action_cacnel') ?></button>
        </footer>
    </div>
</div>
<!-- Modal - calendar lessons -->
<div id="bsappLessonItemModal" class="bsapp--modal bsapp--popup footer--big-full-mob" data-animation="scaleInOut">
    <div class="modal-dialog">
        <div class="modal--preloader js--modal-preloader d-none">
            <div class="spinner-border"><span class="sr-only"><?= lang('loading') ?></span></div>
        </div>
        <header class="modal-header p-0 flex-column">
            <div class="modal-header_content d-flex align-items-center justify-content-between">
                <h3 class="h3"><?= lang('select_class') ?></h3>
                <button class="close-modal js--close-modal" aria-label="close modal" data-close="" type="button"><i class="fal fa-times"></i></button>
            </div>
        </header>
        <section class="modal-body without-footer p-0">
            <div id="calendarContent" class="bsapp--calendar--content"></div>
            <div id="lessonsContent" class="bsapp--lessons--content"></div>
        </section>
    </div>
</div>
<!-- Modal - search by items -->
<div id="bsappSearchModal" class="bsapp--modal bsapp--popup without-p bsapp--popup-search">
    <div class="modal-dialog modal-dialog--full">
        <header class="modal-header without--b-bottom d-flex">
            <button class="close-modal js--group-icon--close-modal" aria-label="close modal" data-close="" type="button"><i class="fal fa-times"></i></button>
        </header>
        <section class="modal-body without-footer p-0">
            <div class="bsapp--search-form">
                <div class="bsapp--search-container">
                    <div class="form--group-items">
                        <div class="form--group-rel">
                            <form action="" method="post" id="searchForm">
                                <input id="searchInput"
                                       class="form--group-input full js--group-input--remove"
                                       name="searchInput"
                                       type="text"
                                       value=""
                                       placeholder="<?= lang('search_looking_for') ?>">
                                <button class="form--group-icon form--group-icon--remove js--group-icon--remove"
                                        type="button"><i class="fa-light fa-circle-xmark"></i></button>
                                <button class="form--group-icon form--group-icon--close-modal js--group-icon--close-modal d-lg-none"
                                        type="button"><i class="fa-light fa-circle-xmark"></i></button>
                                <button class="form--group-icon form--group-icon--search js--group-icon--search"
                                        type="submit"><i class="fa-light fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="searchResultsContent" class="bsapp--search-content with-bg"></div>
        </section>
    </div>
</div>
<!-- Bottom bar - only for mobile -->
<div id="bsappBar" class="bsapp--bar" data-animation="scaleInOut">
    <div class="modal-dialog"></div>
</div>

<?php include '../assets/office/develop/templates/partials/modals.php'; ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3BPF8F" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</body>
</html>

