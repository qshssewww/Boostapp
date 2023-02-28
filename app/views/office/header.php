<!DOCTYPE html>
<html lang="he" dir="rtl">
    <head>
	<meta charset="utf-8">
    <base href="<?php echo Config::get('app.url'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="csrf-token" content="<?php echo csrf_token() ?>">
	<link href="<?php echo asset_url('img/favicon.png') ?>" rel="icon">
	<!-- <meta name="referrer" content="never">-->


	<title><?php echo (isset($pageTitle) ? $pageTitle .' | ' : '') . Config::get('app.name') ?></title>

    <?php 
        global $headerCss;
        $headerCss = array_merge(
            [
                // 'CDN/bootstrap/bootstrap.min.css',
                'CDN/bootstrap/custom-bootstrap4.css',
                'CDN/CheckBox/pretty-checkbox.min.css',
                'assets/office/css/main.css',
                'assets/office/css/imgpicker.css',
                'assets/office/css/animate.css',
                'assets/office/css/colors/'.Config::get('app.color_scheme').'.css',
                'office/tinybox2/style.css',
                'CDN/select2/select2.min.css',
                'CDN/select2/select2-bootstrap.css',
                ['href'=>'//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css']
            ], (is_array($headerCss))?$headerCss:[]
        );
        foreach($headerCss as $url):
            if(!is_array($url)):
                printf("<link href=\"%s?v=%d\" rel=\"stylesheet\">\n\t", app()->url($url), filemtime(app_path('../'.$url)));
            else:
                printf("<link href=\"%s\" rel=\"stylesheet\">\n\t", $url['href']);
            endif;
        endforeach;
    ?>
    <style>
        html, body{text-align: right!important;}
    </style>
</head>
<body dir="rtl">
	
<?php echo View::make('office/top-nav')->render(); ?>
<div class="container-fluid">
    <div class="main">
