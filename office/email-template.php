
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Your Message Subject or Title</title>
        <style type="text/css">

            /***********
            Originally based on The MailChimp Reset from Fabio Carneiro, MailChimp User Experience Design
            More info and templates on Github: https://github.com/mailchimp/Email-Blueprints
            http://www.mailchimp.com &amp; http://www.fabio-carneiro.com
            INLINE: No.
            ***********/
            /* Client-specific Styles */
            #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
            body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
            /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
            .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
            #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
            /* End reset */

            /* Some sensible defaults for images
            1. "-ms-interpolation-mode: bicubic" works to help ie properly resize images in IE. (if you are resizing them using the width and height attributes)
            2. "border:none" removes border when linking images.
            3. Updated the common Gmail/Hotmail image display fix: Gmail and Hotmail unwantedly adds in an extra space below images when using non IE browsers. You may not always want all of your images to be block elements. Apply the "image_fix" class to any image you need to fix.
            Bring inline: Yes.
            */
            img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
            a img {border:none;}
            .image_fix {display:block;}

            /** Yahoo paragraph fix: removes the proper spacing or the paragraph (p) tag. To correct we set the top/bottom margin to 1em in the head of the document. Simple fix with little effect on other styling. NOTE: It is also common to use two breaks instead of the paragraph tag but I think this way is cleaner and more semantic. NOTE: This example recommends 1em. More info on setting web defaults: http://www.w3.org/TR/CSS21/sample.html or http://meiert.com/en/blog/20070922/user-agent-style-sheets/
            Bring inline: Yes.
            **/
            p {margin: 0 0;}

            /** Hotmail header color reset: Hotmail replaces your header color styles with a green color on H2, H3, H4, H5, and H6 tags. In this example, the color is reset to black for a non-linked header, blue for a linked header, red for an active header (limited support), and purple for a visited header (limited support).  Replace with your choice of color. The !important is really what is overriding Hotmail's styling. Hotmail also sets the H1 and H2 tags to the same size.
            Bring inline: Yes.
            **/
            h1, h2, h3, h4, h5, h6 {color: black !important;}

            h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

            h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
                color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
            }

            h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
                color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
            }

            /** Outlook 07, 10 Padding issue: These "newer" versions of Outlook add some padding around table cells potentially throwing off your perfectly pixeled table.  The issue can cause added space and also throw off borders completely.  Use this fix in your header or inline to safely fix your table woes.
            More info: http://www.ianhoar.com/2008/04/29/outlook-2007-borders-and-1px-padding-on-table-cells/
            http://www.campaignmonitor.com/blog/post/3392/1px-borders-padding-on-table-cells-in-outlook-07/
            H/T @edmelly
            Bring inline: No.
            **/
            table td {border-collapse: collapse;}

            /** Remove spacing around Outlook 07, 10 tables
            More info : http://www.campaignmonitor.com/blog/post/3694/removing-spacing-from-around-tables-in-outlook-2007-and-2010/
            Bring inline: Yes
            **/
            table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

            /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email, bring your styles inline.  Your link colors will be uniform across clients when brought inline.
            Bring inline: Yes. */
            a {color: orange;}

            /* Or to go the gold star route...
            a:link { color: orange; }
            a:visited { color: blue; }
            a:hover { color: green; }
            */

            /***************************************************
            ****************************************************
            MOBILE TARGETING
            Use @media queries with care.  You should not bring these styles inline -- so it's recommended to apply them AFTER you bring the other stlying inline.
            Note: test carefully with Yahoo.
            Note 2: Don't bring anything below this line inline.
            ****************************************************
            ***************************************************/

            /* NOTE: To properly use @media queries and play nice with yahoo mail, use attribute selectors in place of class, id declarations.
            table[class=classname]
            Read more: http://www.campaignmonitor.com/blog/post/3457/media-query-issues-in-yahoo-mail-mobile-email/
            */
            @media only screen and (max-device-width: 480px) {

                /* A nice and clean way to target phone numbers you want clickable and avoid a mobile phone from linking other numbers that look like, but are not phone numbers.  Use these two blocks of code to "unstyle" any numbers that may be linked.  The second block gives you a class to apply with a span tag to the numbers you would like linked and styled.
                Inspired by Campaign Monitor's article on using phone numbers in email: http://www.campaignmonitor.com/blog/post/3571/using-phone-numbers-in-html-email/.
                Step 1 (Step 2: line 224)
                */
                a[href^="tel"], a[href^="sms"] {
                    text-decoration: none;
                    color: black; /* or whatever your want */
                    pointer-events: none;
                    cursor: default;
                }

                .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                    text-decoration: default;
                    color: orange !important; /* or whatever your want */
                    pointer-events: auto;
                    cursor: default;
                }
            }

            /* More Specific Targeting */

            @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
                /* You guessed it, ipad (tablets, smaller screens, etc) */

                /* Step 1a: Repeating for the iPad */
                a[href^="tel"], a[href^="sms"] {
                    text-decoration: none;
                    color: blue; /* or whatever your want */
                    pointer-events: none;
                    cursor: default;
                }

                .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                    text-decoration: default;
                    color: orange !important;
                    pointer-events: auto;
                    cursor: default;
                }
            }

            @media only screen and (-webkit-min-device-pixel-ratio: 2) {
                /* Put your iPhone 4g styles in here */
            }

            /* Following Android targeting from:
            http://developer.android.com/guide/webapps/targeting.html
            http://pugetworks.com/2011/04/css-media-queries-for-targeting-different-mobile-devices/  */
            @media only screen and (-webkit-device-pixel-ratio:.75){
                /* Put CSS for low density (ldpi) Android layouts in here */
            }
            @media only screen and (-webkit-device-pixel-ratio:1){
                /* Put CSS for medium density (mdpi) Android layouts in here */
            }
            @media only screen and (-webkit-device-pixel-ratio:1.5){
                /* Put CSS for high density (hdpi) Android layouts in here */
            }
            /* end Android targeting */
        </style>

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
        <style>
            body { 
                background: #EEF3F6; 
                font-family : Arial;
                text-align: center;
            }
            .bsmail-body{
                background:#ffffff;
                border-radius:4px;
                //padding-left:60px;
                //padding-right:60px;
                margin :auto;
                width : 100%;
                border-radius : 4px;
            }
            .bsmail-body-1{
             direction: rtl;
                width : 100%  ;
                box-shadow: -1px -1px 25px rgba(0, 0, 0, 0.16);
            }

            .bsmail-body{
                font-family: Arial;

            }
            .bsmail-footer *{
                font-family: Arial;
              
            }

  .bsmail-footer{
     direction: rtl;
  }

            .bsmail-header{
                margin-bottom : 62px;
                direction: rtl;
            }
            .bsmail-header-item-1{
                width : 100%;
                text-align:center;
            }

            .bsmail-header h3{
                font-size:24px;
                margin-top:0px;
                margin-bottom : 18px;
                line-height : 28px;
                text-align: center;
                color: #000000;
                opacity: 1;
                font-weight : 400;
            }
            
            .part{
              direction: rtl;
            }
            
            .bsmail-header-item-1 img{
                width: 170px;
                height: 170px;
                margin:auto;
            }
            .bsmail-header-item-2{
                text-align: center;
                font-weight : 300;
                font-size: 16px;
                line-height : 19px;
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
            }
            .bsmail-part-1{
                padding-top:238px;
                padding-bottom:35px;
                direction : rtl;
                text-align:center;
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1642329139/boostapp/mlday_t2t5a8.png');
                background-position :center top ;
                background-repeat : no-repeat;
                background-size : 114px 238px;
            }
            .bsmail-part-1 p{
                text-align:center;
                display : block;

            }

            .bsmail-part-1 .item-2{
                font-size : 16px;
                line-height : 19px;
                font-weight : 600;
                text-align:center;
                margin-bottom : 20px;
            }

            .bsmail-part-1 .item-3{
                margin-top:12px !important;
                margin-bottom:22px  !important;
            }

            .bsmail-part-1 .item-3 a{
                box-sizing:border-box;
                padding-left : 26px;
                padding-right:26px;
                padding-top:16px;
                padding-bottom : 15px;
                text-align:center ;
                display : inline-block;
                font-size : 16px;
                line-height : 19px;
                background : #00C736 0% 0% no-repeat padding-box;
                border : 1px solid #00C736;
                border-radius : 8px ;
                height : 48px;
                min-width : 220px;
                color : white;
                vertical-align:middle;
                letter-spacing: 1.43px;
                opacity: 1;
                width : 100%;
                text-decoration: none;
            }

            .bsmail-part-1 .item-7{
                letter-spacing: 0px;
                color: #000000;
                opacity: 0.78;
                font-size : 14px;
                line-height: 17px;
                text-align:center;
            }
            .bsmail-part-2{
                text-align:center;
                min-height : 234px;
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641302027/boostapp/background-mobile_xlz7if.png');
                background-position :center;
                background-size : 593px 234px;
                background-repeat : no-repeat;
                padding-top:36px;
                margin-bottom:20px;
            }
            .bsmail-part-2  h3{
                letter-spacing: 0px;
                color: #00C736 !important;
                opacity: 1;
                font-size : 22px;
                line-height : 27px;
                font-weight:600;
                margin-bottom: 8px;
            }
            .bsmail-part-2 .item-2{
                text-align: center;
                font-size : 18px;
                line-height : 22px;
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                margin-bottom: 8px;
            }

            .bsmail-part-2 .item-4 a{
                display : inline-block;

            }
            .item-instagram{
                margin-right : 20px;
            }
            .bsmail-part-2 .item-3{
                text-align: center;
                font-size : 18px;
                line-height : 22px;
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                margin-bottom : 51px;
            }
            .bsmail-part-2 a{
                color : #2680EB !important;
                text-decoration : none;
            }
            .bsmail-part-3{
                text-align:center;
            }
            .bsmail-part-3 .item-1{
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 20px;
                line-height : 24px;
                font-weight : 600;
                margin:auto;
            }
            .bsmail-part-3 .item-1-2{
                width : 286px;
                height : 2px;
                background-color : #00C736;
                margin-left : auto;
                margin-right : auto;
                margin-top : 3px;
                margin-bottom : 6px;
            }
            .bsmail-part-3 .item-2{
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 16px;
                line-height : 19px;
                font-weight : normal;
                margin-bottom : 13px;
            }

            .bsmail-part-4 .item-0-1,
            .bsmail-part-5 .item-0-1,
            .bsmail-part-6 .item-0-1,
            .bsmail-part-7 .item-0-1{
                font-size : 28px;
                line-height : 33px;
                margin-bottom :0px !important;
            }
            .bsmail-part-4,.bsmail-part-6{
                text-align:right;
                background : rgba(149, 149, 149, 0.04) ;
                box-sizing:border-box;
            }
            .bsmail-part-4 {
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1642329139/boostapp/mcalendar_eyg2zi.png');
                background-position :center top 63px ;
                background-repeat : no-repeat;
                background-size : 243px 187px;
                padding-top:231px;
                padding-bottom : 26px;
            }
            .bsmail-part-6 {
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1642329139/boostapp/maccount_fsgk1y.png'); 
                background-position :center top 52px ;
                background-repeat : no-repeat;
                background-size : 259px 250px;
                padding-top:305px;
                padding-bottom : 26px;
            }

            .bsmail-part-4 .item,.bsmail-part-6 .item{
                text-align : right; 
                margin-left : auto;
            }
            .bsmail-part-5,.bsmail-part-7{
                text-align:left;
                box-sizing:border-box;
            }
            .bsmail-part-5 {
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1642329139/boostapp/mteam_rzi1hq.png');
                background-position : center top 53px ;
                background-size : 225px 197px;
                background-repeat : no-repeat;
                padding-top:253px;
                padding-bottom : 26px;
            }
            .bsmail-part-7 {
                background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1642329139/boostapp/mmail_katupt.png'); 
                background-position :center top 28px  ;
                background-repeat : no-repeat;
                background-size : 255px 283px;
                padding-top : 284px;
                padding-bottom : 26px;

            }
            .item a{
                letter-spacing: 1.43px;
                color: #00C736;
                opacity: 1;
                font-weight : medium;
                font-size : 16px;
                line-height : 19px;
            }

            .bsmail-part-5  .item,.bsmail-part-7 .item{
                text-align : right;
                margin-left : auto;
            }
            .bsmail-part-4 h3,
            .bsmail-part-5 h3,
            .bsmail-part-6 h3,
            .bsmail-part-7 h3
            {
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 18px;
                line-height : 22px;
                font-weight : 600;
                margin-top:0px;
                margin-bottom: 12px;
            }
            .bsmail-part-4 .item-2,
            .bsmail-part-5  .item-2,
            .bsmail-part-6  .item-2,
            .bsmail-part-7  .item-2
            {
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 16px;
                line-height : 19px;
                font-weight : normal;
                margin-bottom : 12px;
            }
            .part{
                padding-left : 16px;
                padding-right : 16px;
            }
            .bsmail-part-4 .item-3 a,
            .bsmail-part-5  .item-3 a,
            .bsmail-part-6  .item-3 a,
            .bsmail-part-7  .item-3 a{
                text-decoration : none;
                font-size : 14px;
                line-height : 17px;
                font-weight : 600;
            }
            .bsmail-part-8{
                padding-top:84px;
                padding-bottom : 86px;
                text-align:right;
            }
            .bsmail-part-8 .item-1,
            .bsmail-part-8 .item-2{
                text-align : right;
                font-size : 18px; 
                line-height : 19px;
                margin : 0px;
            }
            .bsmail-part-8 .item-2 span{
                font-weight : 600;
            }
            .bsmail-footer{
                padding-bottom : 96px;   
                padding-left : 16px;
                padding-right : 16px;
                background-color : #F5F5F5 ;
            }
            .bsmail-footer .item-1{
                text-align : center;
                padding-top:25px;
                padding-bottom : 25px;
            }
            .bsmail-footer .item-1 img{
                width : 182px;
                margin:auto;
            }
            .bsmail-footer .item-2{
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 20px;
                line-height : 24px;

                width : 268px;
                margin:auto;

            }
            .bsmail-footer .item-2-3{
                width : 218px;
                height : 2px;
                background-color : #00C736;
                margin-left : auto;
                margin-right : auto;
                margin-top:7px;
                margin-bottom : 15px;
            }
            .bsmail-footer .item-3{
                margin-bottom: 10px;
            }
            .bsmail-footer .item-3,.bsmail-footer .item-4{
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
                font-size : 16px;
                line-height : 19px;
            }
            .bsmail-footer .item-5 img{
                width : 25px;
                height : 25px;
                margin:auto;
            }
            .bsmail-footer .item-5{
                margin-top:20px;
                margin-bottom : 28px;
            }
            .bsmail-footer .item-6{
                text-decoration : underline;
                margin-bottom: 28px;
            }
            .bsmail-footer .item-6,
            .bsmail-footer .item-7,
            .bsmail-footer .item-8
            {
                letter-spacing: 0px;
                color: #000000;
                opacity: 1;
            }
            .bsmail-footer .item-7{
                margin-bottom : 4px;
            }
            .bsmail-footer .item-7,
            .bsmail-footer .item-8
            {
                font-size : 14px;
                line-height : 17px;
            }
            @media screen and ( min-width : 650px ){
                .bsmail-body{
                    width : 693px !important;
                }
                .bsmail-body-1{
                    width : 693px  !important;
                }
                .bsmail-body-1{
                    width : 693px !important;
                }
                .part{
                    padding-left : 60px !important;
                    padding-right : 60px !important;
                }
                .bsmail-header-item-2{
                    text-align: center;
                    font-weight : 300;
                    font-size: 20px;
                    line-height : 24px;
                    letter-spacing: 0px;
                    color: #000000;
                    opacity: 1;
                }
                .bsmail-header{
                    margin-bottom : 0px !important;
                }
                .bsmail-header h3{
                    font-size:30px !important;
                    margin-top:0px !important;
                    line-height : 36px !important;
                    text-align: center !important;
                    color: #000000 !important;
                    opacity: 1 !important;
                    font-weight : 400 !important;
                }
                .bsmail-part-1{
                    padding-top:30px;
                    padding-bottom:35px;
                    direction : rtl;
                    text-align:right;
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128249/boostapp/lady_-_mobile_amms8b.png');
                    background-position :60px center !important ;
                    background-size : 143px 235px !important;
                    background-repeat : no-repeat !important;
                }
                .bsmail-part-1 p{
                    text-align:right;
                    display : block;
                }
                .bsmail-part-1 .item-1{
                    margin-top:100px;
                }
                .bsmail-part-1 .item-2{
                    font-size : 16px;
                    line-height : 19px;
                    font-weight : 600;
                    text-align:right !important;
                    width : 362px !important;
                    margin-bottom : 20px !important;
                }

                .bsmail-part-1 .item-3{
                    margin-top:14px !important;
                    margin-bottom:30px  !important;
                    text-align : right !important;
                }

                .bsmail-part-1 .item-3 a{
                    box-sizing:border-box;
                    padding-left : 26px;
                    padding-right:26px;
                    padding-top:11px  !important;
                    padding-bottom : 10px  !important;
                    text-align:center ;
                    display : inline-block;
                    font-size : 16px;
                    line-height : 19px;
                    background : #00C736 0% 0% no-repeat padding-box;
                    border : 1px solid #00C736;
                    border-radius : 4px  !important;
                    height : 40px  !important;
                    min-width : 220px;
                    color : white;
                    vertical-align:middle;
                    letter-spacing: 1.43px;
                    opacity: 1;
                    width : 184px !important;
                    text-decoration: none;
                }

                .bsmail-part-1 .item-3 a{
                    box-sizing:border-box;
                    padding-left : 26px !important;
                    padding-right:26px !important;
                    padding-top:11px !important;
                    padding-bottom : 10px !important;
                    text-align:center ;
                    display : inline-block;
                    font-size : 16px;
                    line-height : 19px;
                    background : #00C736 0% 0% no-repeat padding-box;
                    border : 1px solid #00C736;
                    border-radius : 4px;
                    height : 40px;
                    min-width : 220px;
                    color : white;
                    vertical-align:middle;
                    letter-spacing: 1.43px;
                    opacity: 1;
                    text-decoration: none;
                }

                .bsmail-part-1 .item-7{
                    letter-spacing: 0px;
                    color: #000000;
                    opacity: 0.78;
                    font-size : 14px;
                    line-height: 17px ;
                    text-align:right !important;
                    width : 360px !important;
                }
                .bsmail-part-4 h3,
                .bsmail-part-5 h3,
                .bsmail-part-6 h3,
                .bsmail-part-7 h3
                {
                    letter-spacing: 0px;
                    color: #000000;
                    opacity: 1;
                    font-size : 20px !important;
                    line-height : 24px !important;
                    font-weight : medium !important;
                }
                .bsmail-part-4 .item-3 a,
                .bsmail-part-5  .item-3 a,
                .bsmail-part-6  .item-3 a,
                .bsmail-part-7  .item-3 a{
                    text-decoration : none !important;
                    font-size : 16px !important;
                    line-height : 19px !important;
                }
                .bsmail-footer .item-7,
                .bsmail-footer .item-8
                {
                    font-size : 16px !important;
                    line-height : 19px !important;
                }
                .bsmail-part-4 .item,.bsmail-part-6 .item{
                    text-align : right; 
                    width : 350px !important;                  
                }
                .bsmail-part-5 .item,.bsmail-part-7 .item{
                    text-align : left; 
                    width : 350px !important;
                    margin-right: auto !important;
                }


                .bsmail-part-3 .item-1{
                    font-size : 22px !important;
                    line-height : 27px !important;
                    width : 286px !important;
                    margin:auto !important;
                }
                .bsmail-part-2  h3{
                    font-size : 24px !important;
                    line-height : 28px !important;
                }
                .bsmail-part-2 .item-2{
                    font-size : 16px !important;
                    line-height : 19px !important;
                }
                .bsmail-part-1 .item-3 a{
                    width : 184px !important;
                }
                .bsmail-part-4 {
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/schedule-web_b7dcpf.png') !important; 
                    background-position :60px center !important; 
                    background-repeat : no-repeat !important; 
                    padding-top : 68px !important; 
                }
                .bsmail-part-6 {
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/team-spirit-web_pnenrv.png') !important; 
                    background-position :60px center !important; 
                    background-repeat : no-repeat !important; 
                    padding-top : 68px !important; 
                }
                .bsmail-part-5 {
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/account-web_xv7a0m.png') !important; 
                    background-position : right 60px center !important; 
                    background-repeat : no-repeat !important; 
                    padding-top : 68px !important; 
                }
                .bsmail-part-7 {
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/mail-sent-web_f61pzs.png') !important;  
                    background-position : right 60px center !important; 
                    background-repeat : no-repeat !important; 
                    padding-top : 68px !important; 
                }

                .bsmail-part-4,.bsmail-part-6{
                    text-align:right !important;
                    height : 313px !important;
                    padding-top:68px !important;
                    background-size : unset !important;
                }

                .bsmail-part-5,.bsmail-part-7{
                    text-align:left !important;
                    height : 313px !important;
                    padding-top:68px !important;
                    background-size : unset !important;
                    margin-right : auto !important;
                }
                .bsmail-part-5 .item,.bsmail-part-7 .item{
                    text-align : left !important;
                    margin-right : auto;
                    margin-left : unset !important;
                }


                .bsmail-part-2{
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/background-web_lkfbep.png') !important;
                }
                .bsmail-part-1{

                    padding-top:92px !important;
                    padding-bottom:21px !important;
                    direction : rtl;
                    text-align:right;
                    background-image : url('https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/lady-web_urhzqu.png') !important;
                    background-position :60px center ;
                    background-size: 143px 235px;
                    background-repeat : no-repeat;
                }
                .bsmail-footer{
                    background-color :  #EEF3F6 !important; 
                }
            }
        </style>
    </head>
    <body>
        <container>
            <div class="bsmail-body">
                <div class="bsmail-body-1" style="background :#ffffff;border-radius : 4px;">
                    <div  class="bsmail-header part" style="min-height:300px;">
                        <div class="bsmail-header-item-1">
                            <!--img class="" src="https://cdn-icons.flaticon.com/png/512/3146/premium/3146600.png?token=exp=1640768437~hmac=bd94f194723c53fae18be012ccc2d678" /-->
                            <img class="" src="https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/tada_ufarwl.gif" />
                        </div>
                        <h3>איזה יופי שהצטרפת אלינו!</h3>
                        <div class="bsmail-header-item-2">
                            היי דניאל ליפר, אנחנו שמחים שהצטרפת למשפחת בוסטאפ! מערכת ניהול העוצמתית שלנו תעזור לך לנהל את לוחות הזמנים שלך, ליצור קשר עם הלקוחות שלך וכמובן תעזור להגדיל מכירות בזכות כלים נהדרים שפיתחנו במיוחד
                        </div>
                    </div>
                    <div class="part bsmail-part-1">
                        <h3  class="item item-2">
                            התחברו עכשיו דרך אימות הטלפוני, והתחילו לנהל את העסק שלכם בקלות ובמהירות
                        </h3>
                        <p class="item item-3">
                            <a class="">אני רוצה להתחיל</a>
                        </p>

                        <p class="item item-7">
                            ניתן להתחבר גם עם שם משתמש וסיסמה, שם המשתמש שלכם הוא כתובת המייל שהזנתם, הסיסמה היא: ds345a
                        </p>          
                    </div>
                    <div class="part bsmail-part-2">
                        <h3 class="item item-1">התחברות ראשונית קלה ומהירה
                        </h3>
                        <p class="item item-2">
                            הכנו סרטון הדרכה קצר שיהפוך את ההתחברות הראשונית שלך לחלקה יותר
                        </p>
                        <p class="item item-3">
                           
                            לסרטון לחצו כאן   
                             <a href=""> לחצו כאן</a>
                        </p>
                        <p class="item item-4">
                            <a class="item-instagram"><img src="https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/instagram_pmp7et.png"></a>

                            <a><img src="https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/facebook_ljt9pn.png"></a>
                        </p>
                    </div>
                    <div class="part bsmail-part-3">
                        <p class="item item-1"> 
                            הצלחה שלך זאת ההצלחה שלנו
                        </p>
                        <div class="item item-1-2"></div>
                        <p class="item item-2">
                            חשוב לנו שתצליחו, לכן יצרנו במיוחד עבורכם ארגז כלים שיעזור לכם בתחילת הדרך
                        </p>
                    </div>
                    <div class="part bsmail-part-4">
                        <h1 class="item item-0-1">1</h1>
                        <h3 class="item item-1">
                            תזמן את השיעורים שלך
                        </h3>
                        <p class="item item-2">
                            בשלב הראשון עליכם להגדיר את השיעורים שלכם בלוח השנה, ללחוץ על הכפתור וליצור כיתה חדשה. ניתן להגדיר תזמון פגישות גם באזור הגדרת היומן.
                        </p>
                        <p class="item item-3">
                            <a>
                                < למידע נוסף
                            </a>
                        </p>
                    </div>
                    <div class="part bsmail-part-5">
                        <h1 class="item item-0-1">2</h1>
                        <h3 class="item item-1">
                            הקמת חברותך
                        </h3>
                        <p class="item item-2">
                            אפשר ללקוחות שלך לקבוע שיעורים על ידי יצירת כרטיס ניקוב של סיסיון או חברות שניתן להקצות ללקוחות. אתה יכול גם לאפשר להם לרכוש באופן עצמאי באפליקציה או עם קישור חיצוני.
                        </p>
                        <p class="item item-3">
                            <a>
                                למידע נוסף
                            </a>
                        </p>
                    </div>
                    <div class="part bsmail-part-6">
                        <h1 class="item item-0-1">3</h1>
                        <h3 class="item item-1">
                            הוסף את הצוות שלך
                        </h3>
                        <p class="item item-2">
                            שלח לצוות שלך הזמנה והתחיל לשתף פעולה במשימות היומיומיות, בטיפול בלקוחות, בשיעורים ובמכירות.
                        </p>
                        <p class="item item-3">
                            <a>
                                < למידע נוסף
                            </a>
                        </p> 
                    </div>
                    <div class="part bsmail-part-7"> 
                        <h1 class="item item-0-1">4</h1>
                        <h3 class="item item-1">
                            תן לעולם לדעת
                        </h3>
                        <p class="item item-2">
                            שלח ללקוחות שלך הזמנה להצטרף למועדון שלך על ידי מילוי טופס מקוון, אם כבר יש לך את נתוני הלקוחות שלך תוכל להעלות אותם למערכת שלנו בקלות. ברגע שלקוח ייכנס למערכת הוא יוכל להשתמש באפליקציה ולהזמין שיעורים.
                        </p>
                        <p class="item item-3">
                            <a>
                                למידע נוסף
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bsmail-footer">
                <p class="item item-1">
                    <img src="https://res.cloudinary.com/dg2ej4eid/image/upload/v1641214357/boostapp-logo_q5q6d2.png" />
                </p>
                <p class="item item-2">יש לך שאלות? אנחנו פה בשבילך</p>
                <div class="item item-2-3"></div>
                <p class="item item-3">ניתן ליצור איתנו קשר בוואטסאפ, בימי</p>
                <p class="item item-4"> א' - ה': 17:00 - 09:00 </p>
                <p class="item item-5">
                    <img src="https://res.cloudinary.com/dg2ej4eid/image/upload/v1641128248/boostapp/whatsapp_wqadzd.png" />
                </p>
                <p  class="item item-6">מרכז התמיכה והמידע</p>
                <p  class="item item-7">BOOSTAPP אתם מקבלים מייל זה לאחר שנרשמתם למערכת </p>
                <p class="item item-8"> א.א.ר בוסטאפ בע"מ © 2018 - 2022 כל הזכויות שמורות 
                </p>
            </div>
        </container>
    </body>
</html>
