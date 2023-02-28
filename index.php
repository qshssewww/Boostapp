<?php
ini_set("max_execution_time", 0);

require_once 'app/init.php';

if (Auth::check()) {
    redirect_to('/office/');
}

$theme_prefix = 'bsapp';
$_SESSION['lang'] = $_COOKIE['boostapp_lang'] ?? 'he';
$pageTitle = lang('login_admin');
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_COOKIE['boostapp_lang']) ? ($_COOKIE['boostapp_lang'] == 'eng' ? 'en' : $_COOKIE['boostapp_lang']) : 'he'; ?>"
      dir="<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_dir'] : 'rtl'; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">

    <title><?php echo (isset($pageTitle) ? $pageTitle .' | ' : '') . Config::get('app.name') ?></title>
    <meta name="description" content="login.boostapp ממשק לניהול הסטודיו. Boostapp היא מערכת המוקדשת לשיפור חייהם של מאמנים, בעלי סטודיו וחדרי כושר על ידי מתן פיתרון הוליסטי לניהול העסק.">

    <link href="<?php echo asset_url('img/favicon2.png') ?>" rel="icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="/CDN/fontawesome-pro-5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/dist/css/app.min.css?' . filemtime(__DIR__. '/office/dist/css/app.min.css'); ?>">

	<script src="/assets/office/dist/index.bundle.js?<?= filemtime(__DIR__.'/assets/office/dist/index.bundle.js') ?>" defer></script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-P3BPF8F');</script>
    <!-- End Google Tag Manager -->
</head>

<body class="<?php echo $theme_prefix; ?>__login-page">

<?php if (Auth::guest()): ?>
    <section class="<?php echo $theme_prefix; ?>__section">
        <div class="<?php echo $theme_prefix; ?>__svg-bg d-none d-lg-block">
            <div class="svg svg--small">
                <svg xmlns="http://www.w3.org/2000/svg" width="2155.668" height="2155.668" viewBox="0 0 2155.668 2155.668"><g id="Group_11" data-name="Group 11" transform="translate(890.132 1177.618) rotate(-43)"><ellipse id="Ellipse_2" data-name="Ellipse 2" cx="622.505" cy="622.506" rx="622.505" ry="622.506" transform="matrix(-0.966, -0.259, 0.259, -0.966, 659.581, 801.068)" fill="#6e7580" opacity="0.24"/><ellipse class="svg--shadow" id="Ellipse_3" data-name="Ellipse 3" cx="622.505" cy="622.506" rx="622.505" ry="622.506" transform="matrix(-0.966, -0.259, 0.259, -0.966, 626.282, 794.094)" fill="#808790" opacity="0.5"/></g></svg>
            </div>
            <div class="svg svg--big">
                <svg xmlns="http://www.w3.org/2000/svg" width="1971.139" height="1971.142" viewBox="0 0 1971.139 1971.142"><g data-name="Group 10" transform="translate(-56.777 416.752)"><ellipse class="svg--shadow" data-name="Ellipse 3" cx="854.22" cy="854.222" rx="854.22" ry="854.222" transform="translate(185.462 -287.752)" fill="#fff"/></g></svg>
            </div>
        </div>
        <div class="container">
            <div class="row flex-lg-row-reverse justify-content-lg-between">
                <div class="col-lg-7">
                    <div class="<?php echo $theme_prefix; ?>__section-bg"></div>
                    <div class="<?php echo $theme_prefix; ?>__section-center <?php echo $theme_prefix; ?>__section-logo d-flex align-items-start align-items-lg-center justify-content-center justify-content-lg-end">
                        <div class="logo-image">
                            <img src="/assets/img/login-logo-white.PNG" alt="<?php echo Config::get('app.name') ?>">
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 <?php echo $theme_prefix; ?>__section-content">
                    <div class="<?php echo $theme_prefix; ?>__svg-bg d-lg-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1026" height="1026" viewBox="0 0 1026 1026"><g data-name="Group 10" transform="translate(43 43)"><ellipse class="svg--shadow-small" data-name="Ellipse 3" cx="470" cy="470" rx="470" ry="470" fill="#fff"/></g></svg>
                    </div>

                    <div class="<?php echo $theme_prefix; ?>__section-center d-lg-flex align-items-end align-items-lg-center">
                        <div class="d-lg-block d-flex align-items-center w-100">
                            <div id="step--phone" class="<?php echo $theme_prefix; ?>__step js--step">
                                <h1 class="title"><?php echo lang('welcome_app'); ?></h1>
                                <h3 class="subtitle"><?php echo lang('login_welcome_msg'); ?></h3>
                                <form action="/office/ajax/login/otp.php"
                                      method="post"
                                      class="<?php echo $theme_prefix; ?>__login-form login-form"
                                      name="loginWithPhone">
                                    <div class="form-group d-flex flex-row-reverse form-group_ltr">
                                        <input required
                                               autofocus
                                               value=""
                                               type="tel"
                                               name="phone"
                                               class="form-control js--input-phone"
                                               id="phone"
                                               maxlength="10"
                                               pattern="0?[5]{1}[0-9]{8}"
                                               placeholder="<?php echo lang('mobile_number'); ?>">
                                        <label class="label-pseudo" for="phone">+972</label>
                                    </div>

                                    <input type="hidden" name="action" value="sendOtp">

                                    <div class="form-group form-group_btn">
                                        <button type="submit" class="btn btn-block" data-to="step--code">
                                            <span class="btn--text-send"><?php echo lang('send_code'); ?></span>
                                            <span class="btn--text-blocked"><?php echo lang('login_phone_blocked'); ?> <b>00:30</b></span>
                                        </button>
                                    </div>
                                    <div class="form-group_message text-center js--form-group_message"></div>
                                </form>

                                <p class="paragr-big text-center">
                                    <?php echo lang('login_username_text'); ?>
                                    <span class="<?php echo $theme_prefix; ?>__link js--link-to" data-to="step--login"><?php echo lang('login_username'); ?></span>
                                </p>
                                <!--								<p class="text-center">-->
                                <!--                                    --><?php //echo lang('login_register_text'); ?>
                                <!--									<a href="https://devget.boostapp.co.il/" class="--><?php //echo $theme_prefix; ?><!--__link">--><?php //echo lang('login_register_link'); ?><!--</a>-->
                                <!--								</p>-->
                                <div class="text-center">
                                    <a class="open-modal js--open-modal" href="#indexModal" data-toggle="modal" data-open="modal"><?php echo lang('login_terms_link'); ?></a>
                                </div>
                            </div>

                            <div id="step--code" class="<?php echo $theme_prefix; ?>__step js--step">
                                <h2 class="title js--link-to" data-to="step--phone">
                                    <i class="fas fa-long-arrow-alt-<?php echo isset($_COOKIE['boostapp_dir']) && $_COOKIE['boostapp_dir'] == 'ltr' ? 'left' : 'right'; ?>"></i>
                                    <span><?php echo lang('auth_admin'); ?></span>
                                </h2>
                                <h3 class="subtitle js--put-phone"><?php echo lang('login_code_subtitle'); ?></h3>
                                <form action="/office/ajax/login/login.php"
                                      method="post"
                                      class="<?php echo $theme_prefix; ?>__login-form"
                                      name="loginWithCode">
                                    <div class="form-group d-flex form-group_ltr">
                                        <input required class="input-code js--input-code" id="digit1" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                        <input required class="input-code js--input-code" id="digit2" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                        <input required class="input-code js--input-code" id="digit3" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                        <input required class="input-code js--input-code" id="digit4" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                        <input required class="input-code js--input-code" id="digit5" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                        <input required class="input-code js--input-code" id="digit6" type="tel" maxlength="1" pattern="[0-9]{1}" placeholder="0">
                                    </div>

                                    <input required name="otp" type="hidden" maxlength="6" pattern="[0-9]{6}" value="" autocomplete="one-time-code">

                                    <input type="hidden" name="action" value="loginByPhone">

                                    <div class="form-group form-group_btn">
                                        <button type="submit" class="btn btn-block"><span><?php echo lang('login_check'); ?></span></button>
                                    </div>
                                    <div class="form-group_message text-center js--form-group_message"></div>
                                </form>

                                <p class="paragr-big text-center"><?php echo lang('send_again_text'); ?>
                                    <span id="send-phone-again" class="<?php echo $theme_prefix; ?>__link" data-to="step--phone"><?php echo lang('send_again_link'); ?></span>
                                </p>
                            </div>

                            <div id="step--login" class="<?php echo $theme_prefix; ?>__step js--step">
                                <h2 class="title js--link-to" data-to="step--phone">
                                    <i class="fas fa-long-arrow-alt-<?php echo isset($_COOKIE['boostapp_dir']) && $_COOKIE['boostapp_dir'] == 'ltr' ? 'left' : 'right'; ?>"></i>
                                    <span><?php echo lang('back_new_add_credit'); ?></span>
                                </h2>
                                <h3 class="subtitle"><?php echo lang('user_password_subtitle'); ?></h3>
                                <form action="/office/ajax/login/login.php"
                                      method="post"
                                      class="<?php echo $theme_prefix; ?>__login-form"
                                      name="loginWithUsername">

                                    <div class="form-group form-group_small">
                                        <label for="username" hidden aria-hidden="true"><?php echo lang('username_single'); ?></label>
                                        <input id="username" type="text" class="form-control" name="username" value="" placeholder="<?php echo lang('username_single'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" hidden aria-hidden="true"><?php echo lang('password'); ?></label>
                                        <input id="password" type="password" class="form-control" name="password" placeholder="<?php echo lang('password'); ?>" required>
                                    </div>

                                    <input type="hidden" name="action" value="loginByEmail">

                                    <div class="form-group form-group_btn">
                                        <button type="submit" class="btn btn-block"><span><?php echo lang('login_admin'); ?></span></button>
                                    </div>
                                    <div class="form-group_message text-center js--form-group_message"></div>
                                </form>

                                <p class="text-center">
                                    <?php echo lang('login_forgot_password'); ?>
                                    <span class="<?php echo $theme_prefix; ?>__link js--link-to" data-to="step--reminder"><?php echo lang('login_forgot_link'); ?></span>
                                </p>
                                <div class="text-center">
                                    <a class="open-modal js--open-modal" href="#indexModal" data-open="modal"><?php echo lang('login_terms_link'); ?></a>
                                </div>
                            </div>

                            <div id="step--reminder" class="<?php echo $theme_prefix; ?>__step js--step">
                                <h2 class="title js--link-to" data-to="step--login">
                                    <i class="fas fa-long-arrow-alt-<?php echo isset($_COOKIE['boostapp_dir']) && $_COOKIE['boostapp_dir'] == 'ltr' ? 'left' : 'right'; ?>"></i>
                                    <span><?php echo lang('back_new_add_credit'); ?></span>
                                </h2>
                                <h3 class="subtitle"><?php echo lang('login_reminder_subtitle'); ?></h3>

                                <form action="/office/ajax/login/login.php"
                                      method="post"
                                      class="<?php echo $theme_prefix; ?>__login-form"
                                      name="loginWithReminder">

                                    <div class="form-group form-group_small">
                                        <label for="emailReminder" hidden aria-hidden="true"><?php echo lang('enter_username_login'); ?></label>
                                        <input id="emailReminder" type="text" class="form-control" name="username" value="" placeholder="<?php echo lang('enter_username_login'); ?>" required>
                                    </div>
                                    <input type="hidden" name="action" value="remindPassword">
                                    <div class="form-group form-group_btn">
                                        <button type="submit" class="btn btn-block"><span><?php echo lang('continue_indexnew'); ?></span></button>
                                    </div>
                                    <div class="form-group_message text-center js--form-group_message"></div>
                                </form>

                                <div class="text-center text-with-modal">
                                    <a class="open-modal js--open-modal" href="#indexModal" data-toggle="modal" data-open="modal"><?php echo lang('login_terms_link'); ?></a>
                                </div>
                            </div>

                            <div id="step--lock" class="<?php echo $theme_prefix; ?>__step js--step">
                                <div class="d-flex align-items-center justify-content-center text-center h-100">
                                    <div>
                                        <div class="error-image">
                                            <img src="/assets/img/error-animation.gif" alt="<?php echo Config::get('app.name') ?> | <?php echo lang('login_blocked_text'); ?>">
                                        </div>
                                        <h4 class="error-text">
                                            <?php echo lang('login_blocked_text'); ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <div class="<?php echo $theme_prefix; ?>__login-modal <?php echo $theme_prefix; ?>__modal fade" id="indexModal" role="dialog" aria-labelledby="indexModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo lang('login_terms_link'); ?></h5>
                    <button type="button" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body--scroll">
                        <?php
                        if (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] !== 'he') {
                            require_once(__DIR__ . '/index-terms-en.php');
                        } else {
                            require_once(__DIR__ . '/index-terms-he.php');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        window.theme = window.theme || {};
        theme.prefix = "<?php echo $theme_prefix; ?>";
        theme.translation = {
            loginCodeSent: "<?php echo lang('login_code_subtitle'); ?>",
            loginCodeError: "<?php echo lang('login_code_error'); ?>",
            loginUsernameError: "<?php echo lang('login_username_error'); ?>"
        };
    </script>
<?php endif; ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3BPF8F" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
</body>
</html>
