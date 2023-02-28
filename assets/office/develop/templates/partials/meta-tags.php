<?php
$siteName = 'login.boostapp.co.il';
$metaTitle = 'Boost';
$metaDescription = 'login.boostapp ממשק לניהול הסטודיו. Boostapp היא מערכת המוקדשת לשיפור חייהם של מאמנים, בעלי סטודיו וחדרי כושר על ידי מתן פיתרון הוליסטי לניהול העסק.';
$metaImage = asset_url('img/favicon2.png'); //JPG, PNG, WEBP and GIF images are all supported, and should be no larger than 5MB
$metaUrl = 'https://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'],'?');
?>

<meta name="description" content="<?= $metaDescription ?>">

<link href="<?= $metaImage ?>" rel="icon">
<link rel="canonical" href="<?= $metaUrl; ?>">

<!-- Open Graph Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@<?= $siteName ?>" />
<meta name="twitter:title" content="<?= $metaTitle ?>"/>
<meta name="twitter:description" content="<?= $metaDescription ?>"/>
<meta name="twitter:image" content="<?= $metaImage ?>"/>

<!-- Open Graph Facebook -->
<meta property="og:site_name" content="<?= $siteName ?>">
<meta property="og:title" content="<?= $metaTitle ?>" />
<meta property="og:description" content="<?= $metaDescription ?>" />
<meta property="og:url" content="<?= $metaUrl ?>" />
<meta property="og:type" content="website" />
<meta property="og:image" content="<?= $metaImage ?>" />
<meta property="og:image:width" content="1024">
<meta property="og:image:height" content="576">