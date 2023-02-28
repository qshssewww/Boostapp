<?php
require_once __DIR__.'/../app/init.php';
$url = 'https://login.boostapp.co.il/office/assets/img/email-template/';
//$password = Session::get('password') ?? '';
if(!isset($password)) {
    $password = $_POST['password'] ?? '';
}
if(!isset($name)) {
    $name = $_POST['name'] ?? '';
}
$subject = 'הצטרפות למערכת';
$system_notice = lang('system_notice') ;
$date_notice = date('d/m/Y') ;
$show_top_part = false;
$lessons_url = 'https://' . $_SERVER['HTTP_HOST'] . '/office/DeskPlanNew.php';
$fb_url = 'https://www.facebook.com/boostapp.co.il';
$insta_url = 'https://www.instagram.com/boostapp_insta/';
$videoLink = 'https://www.youtube.com/watch?v=35A4JIyiLWg';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head> </head>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="x-apple-disable-message-reformatting" />
        <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!--<![endif]-->
        <style type="text/css">
            * {
                text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                -moz-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }

            html {
                height: 100%;
                width: 100%;
            }

            body {
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                mso-line-height-rule: exactly;
            }

            div[style*="margin: 16px 0"] {
                margin: 0 !important;
            }

            table,
            td {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }

            img {
                border: 0;
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
            }

            .ReadMsgBody,
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }
        </style>
        <!--[if gte mso 9]>
          <style type="text/css">
          li { text-indent: -1em; }
          table td { border-collapse: collapse; }
          </style>
          <![endif]-->
        <title> </title>
        <style>
            @media only screen and (max-device-width:480px) {
                .a[href^=sms],
                .a[href^=tel] {
                    pointer-events: none;
                    cursor: default;
                    color: #000000;
                    text-decoration: none;
                }
                .a[href^=sms] .a__text,
                .a[href^=tel] .a__text {
                    color: #000000;
                    text-decoration: none;
                }
                .mobile_link .a[href^=sms],
                .mobile_link .a[href^=tel] {
                    pointer-events: auto;
                    cursor: default;
                    color: #FFA500!important;
                    text-decoration: default;
                }
                .mobile_link .a[href^=sms] .a__text,
                .mobile_link .a[href^=tel] .a__text {
                    color: #FFA500!important;
                    text-decoration: default;
                }
            }

            @media only screen and (min-device-width:768px) and (max-device-width:1024px) {
                .a[href^=sms],
                .a[href^=tel] {
                    pointer-events: none;
                    cursor: default;
                    color: #0000FF;
                    text-decoration: none;
                }
                .a[href^=sms] .a__text,
                .a[href^=tel] .a__text {
                    color: #0000FF;
                    text-decoration: none;
                }
                .mobile_link .a[href^=sms],
                .mobile_link .a[href^=tel] {
                    pointer-events: auto;
                    cursor: default;
                    color: #FFA500!important;
                    text-decoration: default;
                }
                .mobile_link .a[href^=sms] .a__text,
                .mobile_link .a[href^=tel] .a__text {
                    color: #FFA500!important;
                    text-decoration: default;
                }
            }

            @media screen and (min-width:650px) {
                .bsmail-body,
                .bsmail-body-1 {
                    width: 693px!important;
                }
                .part {
                    padding-left: 60px!important;
                    padding-right: 60px!important;
                }
                .bsmail-header-item-2 {
                    color: #000000;
                    font-size: 20px;
                    font-weight: 300;
                    letter-spacing: 0;
                    line-height: 24px;
                    opacity: 1;
                    text-align: center;
                }
                .bsmail-header {
                    margin-bottom: 0!important;
                }
                .bsmail-header .h3.header {
                    opacity: 1!important;
                    color: #000000!important;
                    font-size: 30px!important;
                    font-weight: 400!important;
                    line-height: 36px!important;
                    margin-top: 0!important;
                    text-align: center!important;
                }
                .bsmail-part-1 {
                    background-image: url("<?php echo $url; ?>lady_-_mobile_amms8b.png");
                    background-position: 60px!important;
                    background-repeat: no-repeat!important;
                    background-size: 143px 235px!important;
                    padding-bottom: 35px;
                    padding-top: 30px;
                }
                .bsmail-part-1 .p.text {
                    display: block;
                    text-align: right;
                }
                .bsmail-part-1 .item-1 {
                    margin-top: 100px;
                }
                .bsmail-part-1 .item-2 {
                    font-size: 16px;
                    font-weight: 600;
                    line-height: 19px;
                    margin-bottom: 20px!important;
                    text-align: right!important;
                    width: 362px!important;
                }
                .bsmail-part-1 .item-3 {
                    margin-bottom: 30px!important;
                    margin-top: 14px!important;
                    text-align: right!important;
                }
                .bsmail-part-1 .item-3 .a {
                    vertical-align: middle;
                    text-align: center;
                    padding: 11px 26px 10px!important;
                    opacity: 1;
                    min-width: 220px;
                    line-height: 19px;
                    letter-spacing: 1.43px;
                    height: 40px;
                    font-size: 16px;
                    box-sizing: border-box;
                    border-radius: 4px;
                    border: 1px solid #00C736;
                    background-clip: padding-box;
                    background-repeat: no-repeat;
                    background-position: 0 0;
                    background-color: #00C736;
                    padding-right: 26px;
                    padding-left: 26px;
                    height: 40px!important;
                    border-radius: 4px!important;
                    color: #FFFFFF;
                    display: inline-block;
                    text-decoration: none;
                }
                .bsmail-part-1 .item-3 .a .a__text {
                    color: #FFFFFF;
                    text-decoration: none;
                }
                .bsmail-part-1 .item-7 {
                    color: #000000;
                    font-size: 14px;
                    letter-spacing: 0;
                    line-height: 17px;
                    opacity: .78;
                    text-align: right!important;
                    width: 360px!important;
                }
                .bsmail-part-4 .h3.header,
                .bsmail-part-5 .h3.header,
                .bsmail-part-6 .h3.header,
                .bsmail-part-7 .h3.header {
                    opacity: 1;
                    color: #000000;
                    font-size: 20px!important;
                    font-weight: medium!important;
                    letter-spacing: 0;
                    line-height: 24px!important;
                }
                .bsmail-part-4 .item-3 .a,
                .bsmail-part-5 .item-3 .a,
                .bsmail-part-6 .item-3 .a,
                .bsmail-part-7 .item-3 .a {
                    line-height: 19px!important;
                    font-size: 16px!important;
                    text-decoration: none!important;
                }
                .bsmail-part-4 .item-3 .a .a__text,
                .bsmail-part-5 .item-3 .a .a__text,
                .bsmail-part-6 .item-3 .a .a__text,
                .bsmail-part-7 .item-3 .a .a__text {
                    text-decoration: none!important;
                }
                .bsmail-footer .item-7,
                .bsmail-footer .item-8 {
                    font-size: 16px!important;
                    line-height: 19px!important;
                }
                .bsmail-part-4 .item,
                .bsmail-part-6 .item {
                    text-align: right;
                    width: 350px!important;
                }
                .bsmail-part-5 .item,
                .bsmail-part-7 .item {
                    margin-right: auto!important;
                    text-align: left;
                    width: 350px!important;
                }
                .bsmail-part-3 .item-1 {
                    font-size: 22px!important;
                    line-height: 27px!important;
                    margin: 0 auto;
                    width: 286px!important;
                }
                .bsmail-part-2 .h3.header {
                    font-size: 24px!important;
                    line-height: 28px!important;
                }
                .bsmail-part-2 .item-2 {
                    font-size: 16px!important;
                    line-height: 19px!important;
                }
                .bsmail-part-1 .item-3 .a {
                    width: 184px!important;
                }
                .bsmail-part-4 {
                    background-image: url("<?php echo $url; ?>schedule-web_b7dcpf.png")!important;
                }
                .bsmail-part-4,
                .bsmail-part-6 {
                    background-position: 60px!important;
                    background-repeat: no-repeat!important;
                    padding-top: 68px!important;
                }
                .bsmail-part-6 {
                    background-image: url("<?php echo $url; ?>team-spirit-web_pnenrv.png")!important;
                }
                .bsmail-part-5 {
                    background-image: url("<?php echo $url; ?>account-web_xv7a0m.png")!important;
                }
                .bsmail-part-5,
                .bsmail-part-7 {
                    background-position: right 60px center!important;
                    background-repeat: no-repeat!important;
                    padding-top: 68px!important;
                }
                .bsmail-part-7 {
                    background-image: url("<?php echo $url; ?>mail-sent-web_f61pzs.png")!important;
                }
                .bsmail-part-4,
                .bsmail-part-6 {
                    text-align: right!important;
                }
                .bsmail-part-4,
                .bsmail-part-6,
                .bsmail-part-5,
                .bsmail-part-7 {
                    background-size: unset!important;
                    height: 313px!important;
                    padding-top: 68px!important;
                }
                .bsmail-part-5,
                .bsmail-part-7 {
                    margin-right: auto!important;
                    text-align: left!important;
                }
                .bsmail-part-5 .item,
                .bsmail-part-7 .item {
                    margin-left: unset!important;
                    margin-right: auto;
                    text-align: left!important;
                }
                .bsmail-part-2 {
                    background-image: url("<?php echo $url; ?>background-web_lkfbep.png")!important;
                }
                .bsmail-part-1 {
                    background-image: url("<?php echo $url; ?>lady-web_urhzqu.png")!important;
                    background-position: 60px;
                    background-repeat: no-repeat;
                    background-size: 143px 235px;
                    direction: rtl;
                    padding-bottom: 21px!important;
                    padding-top: 92px!important;
                    text-align: right;
                }
                .bsmail-footer {
                    background-color: #EEF3F6!important;
                }
            }
        </style>
        <!-- content -->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Your Message Subject or Title</title>
        <!-- Targeting Windows Mobile -->
        <!--[if IEMobile 7]>
            <style type="text/css">
            </style>
            <![endif]-->
        <!-- ***********************************************
            ****************************************************
            END MOBILE TARGETING
            ****************************************************
            ************************************************ -->
        <!--[if gte mso 9]>
            <style>
                    /* Target Outlook 2007 and 2010 */
            </style>
            <![endif]-->
        <!--[if gte mso 9]><xml>
           <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
           </o:OfficeDocumentSettings>
          </xml><![endif]-->
    </head>
    <body class="body" style="background-color: #EEF3F6; margin: 0; width: 100%;">
        <table class="bodyTable" role="presentation" width="100%" align="left" border="0" cellpadding="0" cellspacing="0" style="background-color: #EEF3F6; margin: 0; width: 100%;" bgcolor="#EEF3F6">
            <tr>
                <td class="body__content" align="center" width="100%" valign="top" style="color: #000000; font-size: 16px; line-height: 20px; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: Arial; text-align: center;">
                    <div class="container" style="margin: 0 auto; max-width: 600px; width: 100%;"> <!--[if mso | IE]>
                      <table class="container__table__ie" role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-right: auto; margin-left: auto;width: 600px" width="600" align="center">
                        <tr>
                          <td> <![endif]-->
                        <table class="container__table" role="presentation" border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
                            <tr class="container__row">
                                <td class="container__cell" width="100%" align="left" valign="top">
                                    <div class="bsmail-body" style="//padding-left: 60px; //padding-right: 60px; background-color: #FFFFFF; border-radius: 4px; margin: 0 auto; width: 100%; font-family: Arial;">
                                        <div class="bsmail-body-1" style="box-shadow: -1px -1px 25px rgba(0, 0, 0, .16); direction: rtl; width: 100%; background: #ffffff; border-radius: 4px;">
                                            <?php if ($show_top_part): ?>
                                                <div class="bsmail-top part" style="vertical-align:middle;direction: rtl; padding-left: 16px; padding-right: 16px; margin-bottom: 0px; padding-top: 60px; padding-bottom: 60px; border-bottom: 1px solid #ccc;">
                                                    <div style="display:inline-block;width:50%;text-align:right;"> <img src="<?php echo $logo; ?>" border="0" alt="" class="img__block" style="vertical-align:middle;text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; width: 180px;"
                                                                                                                        width="180" /> </div>
                                                    <div style="display:inline-block;width:49.5%;width:calc( 50% - 5px );height:70px;text-align:left;">
                                                        <div><?php echo $system_notice; ?></div>
                                                        <div><?php echo $date_notice; ?></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="bsmail-header part" style="margin-bottom: 62px; direction: rtl; padding-left: 16px; padding-right: 16px; min-height: 300px;">
                                                <div class="bsmail-header-item-1" style="text-align: center; width: 100%;">
                                                    <img class="img__block" src="<?php echo $url; ?>tada_ufarwl.gif" border="0" alt="" style="text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; width: 170px; margin: 0 auto; height: 170px;"
                                                         width="170" height="170" /> </div>
                                                <h3 class="header h3" style="margin: 20px 0; font-family: Arial; opacity: 1; font-size: 24px; font-weight: 400; line-height: 28px; margin-bottom: 18px; margin-top: 0; text-align: center; color: #000000;">איזה יופי שהצטרפת אלינו!</h3>
                                                <div class="bsmail-header-item-2" style="color: #000000; font-size: 16px; font-weight: 300; letter-spacing: 0; line-height: 19px; opacity: 1; text-align: center;"> היי <?= $name ?? '' ?>, אנחנו שמחים שהצטרפת למשפחת Boostapp! מערכת ניהול העוצמתית שלנו תעזור לך לנהל את לוחות הזמנים שלך, ליצור קשר עם הלקוחות שלך וכמובן תעזור להגדיל מכירות בזכות כלים נהדרים שפיתחנו  </div>
                                            </div>
                                            <div class="part bsmail-part-1" style="background-image: url('<?php echo $url; ?>mlday_t2t5a8.png'); background-position: top; background-repeat: no-repeat; background-size: 114px 238px; direction: rtl; padding-bottom: 35px; padding-top: 238px; text-align: center; padding-left: 16px; padding-right: 16px;">
                                                <h3 class="item item-2 header h3" style="margin: 20px 0; font-family: Arial; font-size: 16px; font-weight: 600; line-height: 19px; margin-bottom: 20px; text-align: center; color: #000000;"> התחברו עכשיו דרך אימות הטלפוני, והתחילו לנהל את העסק שלכם בקלות ובמהירות </h3>
                                                <p class="item item-3 text p" style="color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; display: block; text-align: center; margin-bottom: 22px; margin-top: 12px;">
                                                    <a class="a" href="<?php echo $lessons_url; ?>" style="font-weight: medium; width: 100%; vertical-align: middle; text-align: center; padding: 16px 26px 15px; opacity: 1; min-width: 220px; line-height: 19px; letter-spacing: 1.43px; height: 48px; font-size: 16px; box-sizing: border-box; border-radius: 8px; border: 1px solid #00C736; background-clip: padding-box; background-repeat: no-repeat; background-position: 0 0; background-color: #00C736; color: #FFFFFF; display: inline-block; text-decoration: none;"><span class="a__text" style="color: #FFFFFF; text-decoration: none;">אני רוצה להתחיל</span></a>                                </p>
                                                <p class="item item-7 text p" style="margin: 0; font-family: Arial; color: #000000; font-size: 14px; letter-spacing: 0; line-height: 17px; opacity: .78; display: block; text-align: center;"> ניתן להתחבר גם עם שם משתמש וסיסמה, שם המשתמש שלכם הוא כתובת המייל שהזנתם, הסיסמה היא: <?php echo $password; ?></p>
                                            </div>
                                            <div class="part bsmail-part-2" style="direction: rtl; background-image: url('<?php echo $url; ?>background-mobile_xlz7if.png'); background-position: 50%; background-repeat: no-repeat; background-size: 593px 234px; margin-bottom: 20px; min-height: 234px; padding-top: 36px; text-align: center; padding-left: 16px; padding-right: 16px;">
                                                <h3 class="item item-1 header h3" style="margin: 20px 0; font-family: Arial; text-align: center; font-size: 22px; font-weight: 600; line-height: 27px; opacity: 1; letter-spacing: 0; margin-bottom: 8px; color: #00C736;">התחברות ראשונית קלה ומהירה </h3>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; letter-spacing: 0; margin-bottom: 8px; opacity: 1; color: #000000; font-size: 18px; line-height: 22px; text-align: center;">
                                                    הכנו סרטון הדרכה קצר שיהפוך את ההתחברות הראשונית שלכם לחלקה יותר </p>
                                                <p class="item item-3 text p" style="display: block; margin: 0; font-family: Arial; color: #000000; font-size: 18px; letter-spacing: 0; line-height: 22px; margin-bottom: 51px; opacity: 1; text-align: center;">
                                                    לסרטון <a href="<?= $videoLink ?>" class="a" style="text-decoration: none; opacity: 1; line-height: 19px; letter-spacing: 1.43px; font-weight: medium; font-size: 16px; color: #2680EB;"><span class="a__text" style="text-decoration: none; color: #2680EB;"> לחצו כאן</span></a>                                </p>
                                                <p class="item item-4 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; text-align: center;"> <a class="item-instagram a" href="<?php echo $insta_url;?>" style="margin-right: 20px; text-decoration: none; opacity: 1; line-height: 19px; letter-spacing: 1.43px; font-weight: medium; font-size: 16px; display: inline-block; color: #2680EB;"><span class="a__text" style="text-decoration: none; color: #2680EB;"><img src="<?php echo $url; ?>instagram_pmp7et.png" border="0" alt="" class="img__block" style="text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; border: none;"/></span></a>                                <a href="<?php echo $fb_url;?>" class="a" style="text-decoration: none; opacity: 1; line-height: 19px; letter-spacing: 1.43px; font-weight: medium; font-size: 16px; display: inline-block; color: #2680EB;"><span class="a__text" style="text-decoration: none; color: #2680EB;"><img src="<?php echo $url; ?>facebook_ljt9pn.png" border="0" alt="" class="img__block" style="text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; border: none;"/></span></a>                                </p>
                                            </div>
                                            <div class="part bsmail-part-3" style="direction: rtl; text-align: center; padding-left: 16px; padding-right: 16px;">
                                                <p class="item item-1 text p" style="display: block; font-family: Arial; text-align: center; color: #000000; font-size: 20px; font-weight: 600; letter-spacing: 0; line-height: 24px; margin: 0 auto; opacity: 1;"> ההצלחה שלכם זו ההצלחה שלנו </p>
                                                <div class="item item-1-2" style="background-color: #00C736; height: 2px; margin: 3px auto 6px; width: 286px;"> </div>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; text-align: center; color: #000000; font-size: 16px; font-weight: 400; letter-spacing: 0; line-height: 19px; margin-bottom: 13px; opacity: 1;">
                                                    חשוב לנו שתצליחו, לכן יצרנו במיוחד עבורכם ארגז כלים שיעזור לכם בתחילת הדרך </p>
                                            </div>
                                            <div class="part bsmail-part-4" style="direction: rtl; background-color: #959595; background-color: rgba(149,149,149,.04); box-sizing: border-box; text-align: right; background-image: url('<?php echo $url; ?>mcalendar_eyg2zi.png'); background-position: center top 63px; background-size: 243px 187px; padding-top: 231px; background-repeat: no-repeat; padding-bottom: 26px; padding-left: 16px; padding-right: 16px;">
                                                <h1 class="item item-0-1 header h1" style="margin: 20px 0; font-family: Arial; font-size: 28px; line-height: 33px; margin-left: auto; text-align: right; color: #000000; margin-bottom: 0;"> </h1>
                                                <h3 class="item item-1 header h3" style="margin: 20px 0; font-family: Arial; margin-left: auto; text-align: right; opacity: 1; font-size: 18px; font-weight: 600; letter-spacing: 0; line-height: 22px; margin-bottom: 12px; margin-top: 0; color: #000000;">
                                                    תזמנו את השיעורים שלכם </h3>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; margin-left: auto; text-align: right; color: #000000; font-size: 16px; font-weight: 400; letter-spacing: 0; line-height: 19px; margin-bottom: 12px; opacity: 1;">
                                                     הכנו עבורכם שיעורים לדוגמא ביומן, היעזרו במדריך המצולם על מנת לשבץ את השיעורים שלכם ביומן  </p>
                                                <p class="item item-3 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; margin-left: auto; text-align: right;">
                                                    <a href="https://intercom.help/boostapp-site/he/articles/5937255-%D7%94%D7%A7%D7%9E%D7%AA-%D7%A9%D7%99%D7%A2%D7%95%D7%A8-%D7%97%D7%93%D7%A9" class="a" style="opacity: 1; letter-spacing: 1.43px; color: #00C736; line-height: 17px; font-weight: 600; font-size: 14px; text-decoration: none;"><span class="a__text" style="color: #00C736; text-decoration: none;">
                                                             למידע נוסף
                                                        ></span></a> </p>
                                            </div>
                                            <div class="part bsmail-part-5" style="direction: rtl; box-sizing: border-box; text-align: left; background-image: url('<?php echo $url; ?>mteam_rzi1hq.png'); background-position: center top 53px; background-size: 225px 197px; padding-top: 253px; background-repeat: no-repeat; padding-bottom: 26px; padding-left: 16px; padding-right: 16px;">
                                                <h1 class="item item-0-1 header h1" style="margin: 20px 0; font-family: Arial; font-size: 28px; line-height: 33px; margin-left: auto; text-align: right; color: #000000; margin-bottom: 0;"> </h1>
                                                <h3 class="item item-1 header h3" style="margin: 20px 0; font-family: Arial; margin-left: auto; text-align: right; opacity: 1; font-size: 18px; font-weight: 600; letter-spacing: 0; line-height: 22px; margin-bottom: 12px; margin-top: 0; color: #000000;">
                                                    הקמת מנויים </h3>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; margin-left: auto; text-align: right; color: #000000; font-size: 16px; font-weight: 400; letter-spacing: 0; line-height: 19px; margin-bottom: 12px; opacity: 1;">
                                                    אפשרו ללקוחות שלכם לקבוע שיעורים על ידי יצירת כרטיסיית ניקובים או מנוי תקופתי שניתן להקצות ללקוחות. הלקוחות יכולים לרכוש את המנויים דרך אפליקציית המתאמנים </p>
                                                <p class="item item-3 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; margin-left: auto; text-align: right;">
                                                    <a href="https://intercom.help/boostapp-site/he/articles/5937261-%D7%94%D7%A7%D7%9E%D7%AA-%D7%9E%D7%A0%D7%95%D7%99-%D7%9B%D7%A8%D7%98%D7%99%D7%A1%D7%99%D7%94" class="a" style="opacity: 1; letter-spacing: 1.43px; color: #00C736; line-height: 17px; font-weight: 600; font-size: 14px; text-decoration: none;"><span class="a__text" style="color: #00C736; text-decoration: none;">
                                                            <    למידע נוסף
                                                        </span></a> </p>
                                            </div>
                                            <div class="part bsmail-part-6" style="direction: rtl; background-color: #959595; background-color: rgba(149,149,149,.04); box-sizing: border-box; text-align: right; background-repeat: no-repeat; padding-bottom: 26px; background-image: url('<?php echo $url; ?>maccount_fsgk1y.png'); background-position: center top 52px; background-size: 259px 250px; padding-top: 305px; padding-left: 16px; padding-right: 16px;">
                                                <h1 class="item item-0-1 header h1" style="margin: 20px 0; font-family: Arial; font-size: 28px; line-height: 33px; margin-left: auto; text-align: right; color: #000000; margin-bottom: 0;"> </h1>
                                                <h3 class="item item-1 header h3" style="margin: 20px 0; font-family: Arial; margin-left: auto; text-align: right; opacity: 1; font-size: 18px; font-weight: 600; letter-spacing: 0; line-height: 22px; margin-bottom: 12px; margin-top: 0; color: #000000;">
                                                    הוסף את הצוות שלך </h3>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; margin-left: auto; text-align: right; color: #000000; font-size: 16px; font-weight: 400; letter-spacing: 0; line-height: 19px; margin-bottom: 12px; opacity: 1;">
                                                    שלחו לצוות שלכם הזמנה והתחילו לשתף פעולה במשימות היומיומיות, בטיפול בלקוחות, בשיעורים ובמכירות. </p>
                                                <p class="item item-3 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; margin-left: auto; text-align: right;">
                                                    <a href="https://intercom.help/boostapp-site/he" class="a" style="opacity: 1; letter-spacing: 1.43px; color: #00C736; line-height: 17px; font-weight: 600; font-size: 14px; text-decoration: none;"><span class="a__text" style="color: #00C736; text-decoration: none;">
                                                             למידע נוסף
                                                        ></span></a> </p>
                                            </div>
                                            <div class="part bsmail-part-7" style="direction: rtl; box-sizing: border-box; text-align: left; background-repeat: no-repeat; padding-bottom: 26px; background-image: url('<?php echo $url; ?>mmail_katupt.png'); background-position: center top 28px; background-size: 255px 283px; padding-top: 284px; padding-left: 16px; padding-right: 16px;">
                                                <h1 class="item item-0-1 header h1" style="margin: 20px 0; font-family: Arial; font-size: 28px; line-height: 33px; margin-left: auto; text-align: right; color: #000000; margin-bottom: 0;"> </h1>
                                                <h3 class="item item-1 header h3" style="margin: 20px 0; font-family: Arial; margin-left: auto; text-align: right; opacity: 1; font-size: 18px; font-weight: 600; letter-spacing: 0; line-height: 22px; margin-bottom: 12px; margin-top: 0; color: #000000;">
                                                    תן לעולם לדעת </h3>
                                                <p class="item item-2 text p" style="display: block; margin: 0; font-family: Arial; margin-left: auto; text-align: right; color: #000000; font-size: 16px; font-weight: 400; letter-spacing: 0; line-height: 19px; margin-bottom: 12px; opacity: 1;">
                                                    שלחו ללקוחות שלכם הזמנה להצטרף למועדון שלך על ידי מילוי טופס מקוון, אם כבר יש לך את נתוני הלקוחות שלך תוכל להעלות אותם למערכת שלנו בקלות. ברגע שלקוח ייכנס למערכת הוא יוכל להשתמש באפליקציה ולהזמין שיעורים. </p>
                                                <p class="item item-3 text p"
                                                   style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; margin-left: auto; text-align: right;"> <a href="https://intercom.help/boostapp-site/he" class="a" style="opacity: 1; letter-spacing: 1.43px; color: #00C736; line-height: 17px; font-weight: 600; font-size: 14px; text-decoration: none;"><span class="a__text" style="color: #00C736; text-decoration: none;">
                                                          <  למידע נוסף
                                                        </span></a> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bsmail-footer" style="direction: rtl; background-color: #F5F5F5; padding-bottom: 96px; padding-left: 16px; padding-right: 16px;">
                                        <p class="item item-1 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; padding-bottom: 25px; padding-top: 25px; text-align: center;"> <img src="<?php echo $url; ?>boostapp-logo_q5q6d2.png" border="0" alt="" class="img__block" style="text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; font-family: Arial; width: 182px; margin: 0 auto;"
                                                                                                                                                                                                        width="182" /> </p>
                                        <p class="item item-2 text p" style="display: block; font-family: Arial; text-align: center; color: #000000; font-size: 20px; letter-spacing: 0; line-height: 24px; margin: 0 auto; opacity: 1; width: 268px;">יש לך שאלות? אנחנו פה בשבילך</p>
                                        <div class="item item-2-3" style="font-family: Arial; background-color: #00C736; height: 2px; margin: 7px auto 15px; width: 218px;"> </div>
                                        <p class="item item-3 text p" style="display: block; margin: 0; font-family: Arial; text-align: center; margin-bottom: 10px; color: #000000; font-size: 16px; letter-spacing: 0; line-height: 19px; opacity: 1;">ניתן ליצור איתנו קשר בWhatsApp</p>
                                        <p class="item item-4 text p" style="display: block; margin: 0; font-family: Arial; text-align: center; color: #000000; font-size: 16px; letter-spacing: 0; line-height: 19px; opacity: 1;">
                                            בימים א' - ה': 08:30 - 17:30</p>
                                        <p class="item item-5 text p" style="display: block; color: #000000; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; text-align: center; margin-bottom: 28px; margin-top: 20px;">
                                            <a href="https://wa.me/972542134991" target="_blank">
                                                <img src="<?php echo $url; ?>whatsapp_wqadzd.png" border="0" alt="" class="img__block" style="text-decoration: none; outline: none; -ms-interpolation-mode: bicubic; display: block; max-width: 100%; font-family: Arial; width: 25px; margin: 0 auto; height: 25px;"
                                                     width="25" height="25" />
                                            </a>
                                             </p>
                                        <p class="item item-6 text p" style="display: block; font-size: 16px; line-height: 20px; margin: 0; font-family: Arial; text-align: center; margin-bottom: 28px; text-decoration: underline; color: #000000; letter-spacing: 0; opacity: 1;"><a href="https://intercom.help/boostapp-site/he">מרכז התמיכה והמידע</a></p>
                                        <p class="item item-7 text p" style="display: block; margin: 0; font-family: Arial; text-align: center; color: #000000; letter-spacing: 0; opacity: 1; margin-bottom: 4px; font-size: 14px; line-height: 17px;"> אתם מקבלים מייל זה לאחר שנרשמתם למערכת <span>BOOSTAPP</span></p>
                                        <p class="item item-8 text p" style="display: block; margin: 0; font-family: Arial; text-align: center; color: #000000; letter-spacing: 0; opacity: 1; font-size: 14px; line-height: 17px;">
                                            א.א.ר בוסטאפ בע"מ © 2022 כל הזכויות שמורות </p>
<!--                                        --><?php //echo $unsubscribe; ?>
                                    </div>
                                </td>
                            </tr>
                        </table> <!--[if mso | IE]> </td>
                    </tr>
                  </table> <![endif]--> </div>
                </td>
            </tr>
        </table>
        <div style="display:none; white-space:nowrap; font-size:15px; line-height:0;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
    </body>
</html>