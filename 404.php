<?php
ini_set("max_execution_time", 0);

require_once 'app/init.php';
require_once "office/Classes/Translations.php";

if (Auth::guest()) {
    redirect_to('index.php');
}

$isPinkTheme = Config::get('app.name') === 'PINKAPP' ?? false;
$_SESSION['lang'] = $_COOKIE['boostapp_lang'] ?? 'he';
$pageTitle = lang('not_found');

header("HTTP/1.0 404 Not Found");
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
	<style>
        * {
            box-sizing: border-box;
        }

        .container {
            max-width: 1085px;
            padding-inline: 18px;
            margin-inline: auto;
        }

        .theme--page-404 {
            background-color: #fff;
            color: #182434;
            font-family: Rubik, Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: 400;
            letter-spacing: 0;
            line-height: 1.2;
            text-align: start;
            margin: 0;
            padding: 0;
        }
        .theme--page-404 .page-logo,
        .theme--page-404 .logo {
            margin-inline: auto;
        }
        .theme--page-404 .logo {
            max-width: 240px;
            margin-bottom: 60px;
        }
        .theme--page-404 .logo.smaller {
            max-width: 145px;
        }
        @media (min-width: 576px) {
            .theme--page-404 .logo {
                margin-bottom: 121px;
            }
        }
        @media (min-width: 768px) {
            .theme--page-404 .logo {
                max-width: 360px;
                margin-bottom: 90px;
            }
            .theme--page-404 .logo.smaller {
                max-width: 236px;
            }
        }
        .theme--page-404 .page-logo {
            max-width: 528px;
            margin-bottom: 90px;
        }
        @media (max-width: 959px) {
            .theme--page-404 .page-logo {
                margin-bottom: 76px;
                padding-inline: 1rem;
            }
        }
        @media (min-width: 768px) and (max-height: 768px) {
            .theme--page-404 .page-logo {
                max-width: 428px;
            }
            .theme--page-404 .page-logo,
            .theme--page-404 .logo {
                margin-bottom: 2rem;
            }
        }
        .theme--page-404 h5 {
            font-size: 18px;
            margin-block: 0 1.5rem;
            font-weight: normal;
        }
        @media (min-width: 768px) {
            .theme--page-404 h5 {
                text-align: center;
                font-weight: 500;
            }
        }

        .image-wrapper img {
            width: 100%;
            object-fit: cover;
        }

        .theme--page__d-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .h--100 {
            min-height: 100vh;
            padding-block: 1rem;
        }
	</style>
</head>

<body class="theme--page-404">
	<div class="container">
		<div class="theme--page__d-flex h--100">
			<a href="/" class="logo image-wrapper <?php if ($isPinkTheme){ echo 'smaller';} ?>">
                <?php if ($isPinkTheme): ?>
					<img src="/assets/img/pinkapp-logo-dark-2x.png" alt="<?= Config::get('app.name') ?> logo">
                <?php else: ?>
					<img src="/assets/img/boostapp-logo-dark.png" alt="<?= Config::get('app.name') ?> logo">
                <?php endif ?>
			</a>
			<div class="page-logo image-wrapper">
                <?php if ($isPinkTheme): ?>
					<img src="/assets/img/page-404-pink.png" alt="<?= Config::get('app.name') ?> 404 page">
                <?php else: ?>
					<img src="/assets/img/page-404-boostapp.png" alt="<?= Config::get('app.name') ?> 404 page">
                <?php endif ?>
			</div>
			<h5><?= lang('page_404_description'); ?></h5>
		</div>
	</div>
</body>
</html>
