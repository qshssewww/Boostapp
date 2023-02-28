<?php
    require_once('./wp/wp-load.php');
    require_once '../app/init.php'; 

    $wpUser = sprintf("team@%s.com", Auth::user()->CompanyNum);

    if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'newPost'){
        $user = get_user_by_email($wpUser);
        // Create post object
        $my_post = array(
            'post_title'    => wp_strip_all_tags( $_POST['title'] ),
            'post_status'   => $_POST['status'],
            'post_author'   => $user->ID,
            'post_type'     => 'page'
        );

        $post_ID = wp_insert_post( $my_post );
        echo $post_ID;
        exit;
    }

    if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'deletePage'){
        wp_delete_post($_REQUEST['id']);
        exit;
    } 


   
   
   global $wpdb;
    // a secure page
    if (Auth::guest() || !Auth::check()) {
        redirect_to(App::url());
        exit();
    }

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
    // autologin to wordpress
    if(is_user_logged_in() === FALSE){
        // user already exists in our system
        if( email_exists( $wpUser )) {
            $user_id = $wpdb->get_var($wpdb->prepare("SELECT * FROM ".$wpdb->users." WHERE user_email = %s", $wpUser ) );
            wp_set_auth_cookie( $user_id);
        }else{
            // Register those user whose mail id does not exists in database 
            $userdata = array(
                'user_login'  =>  $wpUser,
                'user_email'  =>  $wpUser, 
                'user_pass'   =>  Auth::user()->password,   // password will be username always
                'first_name'  =>  (!empty($SettingsInfo->AppName))?$SettingsInfo->AppName:Auth::user()->display_name,  // first name will be username
                'role'        =>  'editor'     //register the user with subscriber role only
            );

            $user_id = wp_insert_user( $userdata ) ; // adding user to the database

            if ( is_wp_error( $user_id ) ) {
                echo "We having a problom, Huston!";
                exit;
            }
            wp_set_auth_cookie( $user_id);
        }
    }
    $user = get_user_by_email($wpUser);

    // boostapp
    echo View::make('headernew')->render();
    $CompanyNum = Auth::user()->CompanyNum;
    $UserId = Auth::user()->id;

    
    if(!empty($SettingsInfo)){
        $BrandsMain = $SettingsInfo->BrandsMain;
        if ($SettingsInfo->BrandsMain!='0'){
            $BrandsNames = DB::table('brands')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->where('Status', '=', '0')->first(); 
        }
    }
    
    ?>
   <div class="col-md-12 col-sm-12">
        <div class="row pb-3">
            <div class="col-md-5 col-sm-12 order-md-1">
                <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
                <?php echo $DateTitleHeader; ?>
            </h3>
            </div>
            <div class="col-md-5 col-sm-12 order-md-3">
                <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
                <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                    <i class="fas fa-home fa-fw"></i> לוח בקרה //
                    <?php if(isset($SettingsInfo->AppName)) echo $SettingsInfo->AppName; ?>
                </div>
            </h3>
            </div>
            <div class="col-md-2 col-sm-12 order-md-2 pb-1"></div>
        </div>

        <nav aria-label="breadcrumb" dir="rtl">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Main ראשי</li>
                <?php if(!empty($SettingsInfo->BrandsMain)) {
            if ($SettingsInfo->BrandsMain!='0'){ ?>
                    <li style="float: left; left: 30px; position:absolute;" dir="ltr">
                        סניף
                        <?php if(isset($BrandsNames->BrandName)) echo $BrandsNames->BrandName; ?>
                    </li>
                    <?php }} ?>

            </ol>
        </nav>

        <div class="row" style="padding-top: 15px;" dir="rtl">
            <div class="col-md-2 text-right">
                <div class="wrapper">
                    <!-- Sidebar  -->
                    <div class="card spacebottom">
                        <a data-toggle="collapse" href="#CPAmenu" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
                            <div class="card-header text-right">
                                <strong><i class="fas fa-plus-square fa-fw"></i> דף נחיתה</strong>
                            </div>
                        </a>
  
                        <div class="collapse show" id="CPAmenu">
                            <div class="card-body">
                                <div class="nav nav-tabs flex-column nav-pills text-right" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link text-dark" href="#new" aria-selected="true">דף חדש</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 text-right">
                <table id="landingPageWP" class="display" style="width:100%">
                    <thead>
                        <th>שם דף נחיתה</th>
                        <th>סטאטוס</th>
                        <th>תאריך יצירה</th>
                        <th>פעולות</th>
                    </thead>
                <table>
            </div>
        </div>


    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/i18n/jquery.colorbox-he.js"></script>
    <link rel="stylesheet" href="//static.foxycart.com/scripts/colorbox/1.3.9/style1/colorbox.css">
    
    
    <div id="newPostForm" class="d-none">
        <form method="post" action="" name="new-post-form" dir="rtl" class="text-right">

            <label>Page title</label>
            <input type="text" id="title" class="form-control mb-4" name ="title" placeholder="הכנס כותרת כאן">
columns
            <label>סטאטוס</label>columns
            <select name="status" class="form-contrcolumnsol mb-4" id="status">
                <option value="publish">פומבי</option>
                <option value="draft">טיוטא</option>
            </select>

            <!-- Sign in button -->
            <input type="Submit" value="צור" class="btn btn-info btn-block my-4" name="sub">

        </form>  
    </div>
    

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script>
        var boostapp = boostapp || {}
            boostapp.wp = boostapp.wp || {}
            boostapp.wp.uid = <?php echo $user->ID; ?>;
            boostapp.wp.url = boostapp.wp.url || {}
            boostapp.wp.url.pages ={
                list: '<?php echo App::url(sprintf('office/wp/?rest_route=/wp/v2/pages&author=%d', $user->ID)); ?>',
            } 

        jQuery(document).ready(function(){

            var tbl = jQuery('#landingPageWP');


                tbl.DataTable({
                    ajax: function (data, callback, settings) {

                        jQuery.ajax({
                            method: 'get',
                            url: boostapp.wp.url.pages.list,
                            success: function(json){
                                callback({
                                    data: json.map(function(x){
                                        return {
                                            title: x.title.rendered,
                                            status: x.status,
                                            date: x.date,
                                            tools: '\
                                                <a title="ערוך דף" class="iframe" href="wp/wp-admin/post.php?post='+x.id+'&action=elementor"><i class="fa fa-edit"></i></a>\
                                                <a title="מחק דף" class="deletePage" data-id="'+x.id+'" data-name="'+x.title.rendered+'"><i class="fa fa-trash text-danger"></i></a>\
                                            '
                                        }
                                    })
                                })
                            }
                        })                        
                    },
                    language: {
                        "processing": "מעבד...",
                        "lengthMenu": "הצג _MENU_ פריטים",
                        "zeroRecords": "לא נמצאו רשומות מתאימות",
                        "emptyTable": "לא נמצאו רשומות מתאימות",
                        "info": "_START_ עד _END_ מתוך _TOTAL_ רשומות",
                        "infoEmpty": "0 עד 0 מתוך 0 רשומות",
                        "infoFiltered": "(מסונן מסך _MAX_  רשומות)",
                        "infoPostFix": "",
                        "search": "חפש:",
                        "url": "",
                        "paginate": {
                            "first": "ראשון",
                            "previous": "קודם",
                            "next": "הבא",
                            "last": "אחרון"
                        }
                    },
                    columns: [
                        {"data": "title"},
                        {"data": "status"},
                        {"data": "date"},
                        {"data": "tools"}
                    ]
                })


        $(document).on("click", ".deletePage", function(e){
            var id = jQuery(this).data('id');

            var r = confirm("אתה בטוח שאתה רוצה למחוק דף נחיתה "+jQuery(this).data('name')+"!");

            e.preventDefault();
            if (r == true) {
                jQuery.ajax({
                    url: '?action=deletePage',
                    method: 'POST',
                    data: {id: id},
                    crossDomain: false,


                    success: function(data) {
                        tbl.DataTable().ajax.reload();
                    }
                });  
            }
        
        })
        $(document).on("click", ".iframe", function(e){
            e.preventDefault();
                jQuery(this).colorbox({
                    iframe: true,
                    width: "80%",
                    height: "80%",
                    open: true
                });
        });






            var newPostForm = jQuery('#newPostForm');

                   
            newPostForm.find('form').on('submit', function(e){
                e.preventDefault();

                var postForm = jQuery(this);

               jQuery.ajax({
                    url: '?action=newPost',
                    method: 'POST',
                    data: postForm.serialize(),
                    crossDomain: false,


                    success: function(data) {
                        postForm[0].reset();
                        if(jQuery.colorbox && jQuery.colorbox.close) jQuery.colorbox.close();
                        tbl.DataTable().ajax.reload();
 
                        
                        console.log(data);

                    }
                });       
            })



            jQuery('a[href="#new"]').on('click', function(e){
                e.preventDefault();
                jQuery.colorbox({inline:true, href: newPostForm.removeClass('d-none'),  width: '50%', height: '50%', onClosed: function(){
                    newPostForm.addClass('d-none')
                }})
            })
        })
    </script>

    <?php
    require_once '../app/views/footernew.php';