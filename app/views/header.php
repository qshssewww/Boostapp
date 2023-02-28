<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="csrf-token" content="<?php echo csrf_token() ?>">
	<meta name="description" content="login.boostapp ממשק לניהול הסטודיו. Boostapp היא מערכת המוקדשת לשיפור חייהם של מאמנים, בעלי סטודיו וחדרי כושר על ידי מתן פיתרון הוליסטי לניהול העסק.">

	<title><?php echo (isset($pageTitle) ? $pageTitle .' | ' : '') . Config::get('app.name') ?></title>

	<link href="<?php echo asset_url('img/favicon.png') ?>" rel="icon">
	<link rel="stylesheet" type="text/css" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/dist/css/app.min.css' ?>">
	<link href="office/assets/css/fixstyle.css?<?php echo date('YmdHis') ?>" rel="stylesheet">
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-IIED/eyOkM6ihtOiQsX2zizxFBphgnv1zbe1bKA+njdFzkr6cDNy16jfIKWu4FNH" crossorigin="anonymous">

	<script src="<?php echo asset_url('office/js/vendor/jquery-1.11.1.min.js') ?>"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
	<script src="<?php echo asset_url('js/BeePOS.js') ?>"></script>
	<script src="<?php echo asset_url('js/main.js') ?>"></script>
	
		<script>
		BeePOS.options = {
            ajaxUrl:'<?php echo (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != "localhost:8000") ? 'https://'.$_SERVER["HTTP_HOST"]. '/ajax.php' : "http://localhost:8000/ajax.php"?>',
			lang: <?php echo json_encode(trans('main.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0 ?>,
		};
		
	</script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-P3BPF8F');</script>
    <!-- End Google Tag Manager -->

    <style>
        @import url(https://fonts.googleapis.com/css?family=Arimo:400,700&amp;subset=hebrew);

        html, body {
            height: 100%;
            font-family: 'Arimo' !important;
        }

        body.my-login-page {
            background-color: #f7f9fb;
            font-size: 14px;
        }

        .my-login-page .brand {
            width: 100%;
            overflow: hidden;
            margin: 0 auto;
        }

        .my-login-page .brand img {
            width: 50%;
            margin-bottom: 10px;
        }

        .my-login-page .card-wrapper {
            width: 400px;
            margin: 0 auto;
        }

        .my-login-page .card {
            border-color: transparent;
            box-shadow: 0 0 40px rgba(0, 0, 0, .05);
        }

        .my-login-page .card.fat {
            padding: 10px;
        }

        .my-login-page .card .card-title {
            margin-bottom: 30px;
        }

        .my-login-page .form-control {
            border-width: 2.3px;
        }

        .my-login-page .form-group label {
            width: 100%;
        }

        .my-login-page .btn.btn-block {
            padding: 12px 10px;
        }

        .my-login-page .margin-top20 {
            margin-top: 20px;
        }

        .my-login-page .no-margin {
            margin: 0;
        }

        .my-login-page .footer {
            margin: 40px 0;
            color: #888;
            text-align: center;
        }

        .mt-5r {
            margin-top: 3rem !important;
        }

        .fs-16 {
            font-size: 16px;
        }

        @media screen and (max-width: 425px) {
            .my-login-page .card-wrapper {
                width: 90%;
                margin: 0 auto;
            }
        }

        @media screen and (max-width: 320px) {
            .my-login-page .card.fat {
                padding: 0;
            }

            .my-login-page .card.fat .card-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body class="my-login-page" oncontextmenu="return false">
	<section class="h-100">
		<div class="container h-100">
			<div class="row justify-content-md-center h-200 mt-5r">
				<div class="card-wrapper">
					<div class="card fat">
						<div class="card-body text-right">
					<div class="brand">
						<a href="/index.php"><center><img src="assets/img/Logo.png"></center></a>
					</div>
<!--					<hr>-->