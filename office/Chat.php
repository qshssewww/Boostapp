<?php require_once '../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php
$pageTitle = lang("chat_header");
require_once '../app/views/headernew.php'; 
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('112')): ?>

<?php $CompanyNum = Auth::user()->CompanyNum; ?>


<link href="assets/css/chat.css" rel="stylesheet">
<?php

function time_elapsed_string( $time_ago ) {
    $time_ago = strtotime( $time_ago );
    $cur_time = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds = $time_elapsed;
    $minutes = round( $time_elapsed / 60 );
    $hours = round( $time_elapsed / 3600 );
    $days = round( $time_elapsed / 86400 );
    $weeks = round( $time_elapsed / 604800 );
    $months = round( $time_elapsed / 2600640 );
    $years = round( $time_elapsed / 31207680 );
    // Seconds
    if ( $seconds <= 60 ) {
        return lang('sent_now_chat');
    }
    //Minutes
    else if ( $minutes <= 60 ) {
        if ( $minutes == 1 ) {
            return lang('one_minute_chat');
        } else {
            return lang('before_single'). $minutes. lang('minutes');
        }
    }
    //Hours
    else if ( $hours <= 24 ) {
        if ( $hours == 1 ) {
            return lang('one_hour_chat');
        } else {
            return lang('before_single'). $hours. lang('hours');
        }
    }
    //Days
    else if ( $days <= 7 ) {
        if ( $days == 1 ) {
            return lang('yesterday');
        } else {
            return lang('before_single'). $days. lang('days');
        }
    }
    //Weeks
    else if ( $weeks <= 4.3 ) {
        if ( $weeks == 1 ) {
            return lang('one_week_chat');
        } else {
            return lang('before_single'). $weeks. lang('weeks');
        }
    }
    //Months
    else if ( $months <= 12 ) {
        if ( $months == 1 ) {
            return lang('one_month_chat');
        } else {
            return lang('before_single'). $months. lang('months');
        }
    }
    //Years
    else {
        if ( $years == 1 ) {
            return lang('year_ago_chat');
        } else {
            return lang('before_single'). $years. lang('years');
        }
    }
}
?>


<?php

$Users = DB::table('client')->where( 'CompanyNum', '=', $CompanyNum )->orderBy( 'CompanyName', 'ASC' )->get();
$CurrentUserId = @$_REQUEST[ 'U' ];
?>
<link href="assets/css/fixstyle.css" rel="stylesheet">

<div class="loading" id="ReloadSpinner">Loading&#8230;</div>
<!-- <div class="col-md-12 col-sm-12">
    <div class="row">



        <div class="col-md-5 col-sm-12 order-md-1">
            <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
                <?php //echo $DateTitleHeader; ?>
            </h3>
        </div>

        <div class="col-md-5 col-sm-12 order-md-3">
            <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
                <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                    <i class="fas fa-comments"></i> צ׳אט </span>
                </div>
            </h3>
        </div>

        <div class="col-md-2 col-sm-12 order-md-2 pb-1">

        </div>


    </div>

    <nav aria-label="breadcrumb" dir="rtl">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a>
            </li>
            <li class="breadcrumb-item active">צ׳אט</li>
        </ol>
    </nav> -->

    <div class="row">


        <div class="col-md-4 col-sm-12">


            <div class="card spacebottom">
                <a class="text-black" data-toggle="collapse" href="#MenuChat" aria-expanded="true" aria-controls="MenuChat">
                    <div class="card-header text-start">
                        <i class="fas fa-th"></i> <b><?php echo lang('menu_chat') ?></b>
                    </div>
                </a>
                <div class="collapse show" id="MenuChat">

                    <div class="card-body" style="">
                        <div class="d-flex">
                            <input style='position: relative;' class="mie-7" type="text" id="myInput" onkeyup="myFunction()" placeholder="<?php echo lang('search_button') ?>" >
                            <a href="javascript:void(0)" class="align-self-center"><i class="fal fa-plus-circle fa-2x text-primary"></i></a>
                        </div>
                        <?php if (Auth::userCan('113')): ?>
                        <div id="AddChatUser" style="display: none;margin-bottom:10px;">
                            <select name="AddUserToMyChat" id="AddUserToMyChat" data-placeholder="<?php echo lang('') ?>" class="form-control select2 AddUserToMyChat"></select>
                        </div>
                        <?php endif ?>




                        <div id="ChatUsers">
                            <center><img src="../assets/office/img/spinner.gif">
                            </center>
                        </div>





                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-8 col-sm-12">
            <div class="ChatBoxAjax" id="ChatBoxAjax">
                <center><img src="../assets/office/img/spinner.gif">
                </center>
            </div>
        </div>

    </div>

</div>



<script>
    $( '#SendChatButton' ).on( 'click', function () {
        $( '#SendTrueChat' ).submit();
    } );




    function myFunction() {
        // Declare variables
        var input, filter, ul, li, a, i;
        input = document.getElementById( 'myInput' );
        filter = input.value.toUpperCase();
        ul = document.getElementById( "myUL" );
        li = ul.getElementsByTagName( 'li' );

        // Loop through all list items, and hide those who don't match the search query
        for ( i = 0; i < li.length; i++ ) {
            a = li[ i ].getElementsByTagName( "a" )[ 0 ];
            if ( a.innerHTML.toUpperCase().indexOf( filter ) > -1 ) {
                li[ i ].style.display = "";
            } else {
                li[ i ].style.display = "none";
            }
        }
    }
</script>


<script>
    $( '.AddUserToMyChat' ).select2( {
        theme: "bootstrap",
        placeholder: "<?php echo lang('select_customer_to_chat') ?>",
        language: "he",
        allowClear: true,
        width: '100%',
        ajax: {
            url: 'action/ChatClientSelect.php',
            dataType: 'json'
        },
        minimumInputLength: 3,
    } );
    $( document ).ready( function () {
        $( '.AddUserToMyChat' ).on( 'select2:select', function ( e ) {
            ChooseUserToChat( $( this ).val() );
            $( '.AddUserToMyChat' ).val( null ).trigger( 'change' );
            $( '#AddChatUser' ).hide();
        } );
    } );
</script>



<script>
    $( 'a[href="javascript:void(0)"]' ).click( function () {
        if ( $( "#AddChatUser" ).css( 'display' ) == 'none' ) {
            $( "#AddChatUser" ).show();
        } else {
            $( "#AddChatUser" ).hide();
        }
    } );



    function ChooseUserToChat( UserId ) {



        var TrueFixUserId = null;
        var url = 'action/ChatBox.php?U=' + UserId;
        $( '#ChatBoxAjax' ).load( url, function ( e ) {
            $( '#ChatBoxAjax .ajax-form' ).on( 'submit', BeePOS.ajaxForm );
            $( '#SendTrueChat' ).focus();
            return false;
        } );


        $.ajax( {
            url: 'action/ChatUsers.php?U=' + UserId,
            type: 'POST',
            data: '',
            success: function ( data ) {
                $( '#ChatUsers' ).html( data );
                myFunction();

            }
        } );






    }







    var url = 'action/ChatBox.php?U=<?php echo @$CurrentUserId; ?>';
    $( '#ChatBoxAjax' ).load( url, function ( e ) {
        $( '#ChatBoxAjax .ajax-form' ).on( 'submit', BeePOS.ajaxForm );
        $( '#SendTrueChat' ).focus();
        return false;
    } );



    <?php if  (@$CurrentUserId!='') { ?>

    $( document ).ready( function () {

        var ChatCheckNewMessagesVar;

        function ChatCheckNewMessages() {

            var url = 'action/ChatBox.php?U=<?php echo @$CurrentUserId; ?>';
            $( '#ChatBoxAjax' ).load( url, function ( e ) {
                $( '#ChatBoxAjax .ajax-form' ).on( 'submit', BeePOS.ajaxForm );
                $( '#SendTrueChat' ).focus();
                return false;
            } );

        }


        ChatCheckNewMessagesVar = setInterval( ChatCheckNewMessages, 10000 );

    } );

    <?php } ?>


    $ . ajax( {
        url: 'action/ChatUsers.php?U=<?php echo @$CurrentUserId; ?>',
        type: 'POST',
        data: '',
        success: function ( data ) {
            $( '#ChatUsers' ) . html( data );
            myFunction();
        }
    } );




    $( document ).ready( function () {
        var windowWidth = $( window ).width();
        if ( windowWidth <= 1024 ) //for iPad & smaller devices
            $( '#MenuChat' ).removeClass( 'show' );
    } );
</script>
<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage (lang('permission_blocked'), lang('no_page_persmission')); ?>
<?php endif ?>


<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>
