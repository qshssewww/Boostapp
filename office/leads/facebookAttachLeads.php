<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

 echo View::make('headernew')->render();

 $report = new StdClass();
 $report->name = 'ניהול לידים';


?>

<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script src="../js/datatable/dataTables.checkboxes.mihttp://localhost/boostapp/.js"></script>

<link href="../assets/css/fixstyle.css" rel="styleshehttp://localhost/boostapp/t">
<style>
    .bg-gray {background-color: #e9ecef;}
    .dataTables_scrollHead table{margin-bottom: 0px;}http://localhost/boostapp/
</style>

<div class="row pb-3">
    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <?php echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
                <?php echo  $report->name ?>
            </div>
        </h3>
    </div>
</div>

<div class="row" dir="rtl" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
    <div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


        <nav aria-label="breadcrumb" dir="rtl">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo  $report->name ?>
                </li>
            </ol>
        </nav>

        <div class="row">
        <!-- <div class="col-md-2 col-sm-12 order-md-1"> -->
            <?php include("../SettingsInc/RightCards.php"); ?>
        <!-- </div> -->


            <div class="col-md-10 col-sm-12 order-md-2">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-right">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                            </div>
                            <div class="card-body" dir="ltr" style="padding-left:15px; padding-right:15px;">

                                <!-- page content -->
                                <div class="card" id="FBalerts" style="display: none;">
                                    <div class="card-header">מידע מפייסבוק</div>
                                    <div class="card-body"></div>
                                </div>
                                <div class="text-center" id="facebookLoginbtnWrapper">
                                    <a class="btn btn-lg btn-social btn-facebook">
                                        <i class="fab fa-facebook-f"></i> חבר את הלידים שלך לבוסטאפ
                                    </a>
                                </div>
                                <div id="FBuserDetails"></div>
                                
                                <!--end page content -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<style>
.btn-social {
    position: relative;
    padding-right: 44px;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-social:hover {
    color: #eee;
}

.btn-social :first-child {
    position: absolute;
    top: 8px;
    right: 0px;
    bottom: 0;
    width: 40px;
    padding: 7px;
    font-size: 1.6em;
    text-align: center;
    border-left: 1px solid rgba(0,0,0,0.2);
}
.btn-facebook {
    color: #fff!important;
    background-color: #3b5998;
    border-color: rgba(0,0,0,0.2);
}

</style>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '1931387196922899',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v14.0'
    });
    (function initFBCallback(FB, $){
        var facebookLoginBtn = $('#facebookLoginbtnWrapper>a.btn-facebook');
        var FBuserDetails = $('#FBuserDetails');
        var FBalerts = $('#FBalerts');
        var FBalertsBody = $('.card-body', FBalerts);

        var options = {
            sources: [],
            states: [],
            pages: []
        }
        
        jQuery.ajax({
            url: '<?php echo get_loginboostapp_domain() ?>/api/'+'pipeline/config/sources?facebook=true',
            method: 'GET',
            headers: {
                'x-cookie': document.cookie
            }
        }).done(function(data){
            options.sources = options.sources.concat(data);
        })  

        jQuery.ajax({
            url: '<?php echo get_loginboostapp_domain() ?>/api/'+'pipeline/config/pages',
            method: 'GET',
            headers: {
                'x-cookie': document.cookie
            }
        }).done(function(data){
            options.pages = options.pages.concat(data);
            if(options.pages && options.pages.length) facebookLoginBtn.click();
        })

        jQuery.ajax({
            url: '<?php echo get_loginboostapp_domain() ?>/api/'+'pipeline/config/states',
            method: 'GET',
            headers: {
                'x-cookie': document.cookie
            }
        }).done(function(data){
            options.states = options.states.concat(data);
        })

        loginFB = function(){
            var scopes = 'email,manage_pages,leads_retrieval,ads_management,pages_show_list,public_profile';
            // var scopes = 'email,manage_pages,leads_retrieval,pages_show_list,public_profile';
            FB.login(function(response) {
                // console.log(response);
                FBalerts.hide();
                if (!response && !response.authResponse) {
                    FBalertsBody.html('חיבור החשבון לאפליקצייה נכשל');
                    FBalerts.addClass('card-danger').show();
                    return;
                }
                facebookLoginBtn.hide();
                // FBalerts.addClass('card-succuess').show();
                // FBalertsBody.html('ברוכים הבאים, קבלנו אישור לחבר אותך לאפליקצייה שלנו');

                //FB.api('/me', function(response) {
                    //var fullName = response.name;
                    //var accountId = response.id;
                    // FBalertsBody.html(FBalertsBody.html() + '<BR>' + 'שלום '+fullName+' מזהה פייס: '+accountId);

                    // list pages asociated with account
                    FB.api('/me/accounts', { 
                        "fields": "id,name,is_published,is_webhooks_subscribed,members,page_token,access_token",
                        "limit": "30000"
                    },function(res){
                        console.log(res);
                        var pages = res.data;

                        function updateBoostapp(page, changeStatus){
                            // no need to disturbd boostapp API if lead not trying to register
                            if(!changeStatus && !page.is_webhooks_subscribed) return false;
                            jQuery.ajax({
                                        method: 'POST',
                                        url: '<?php echo get_loginboostapp_domain() ?>/api/'+'facebook/page/register',
                                        data:{
                                            pageId: page.id,
                                            cookie: document.cookie,
                                            page: page,
                                            Status: changeStatus ? !page.is_webhooks_subscribed : page.is_webhooks_subscribed,
                                            StatusId: page.boostappStatus,
                                            SourceName: page.boostappSource
                                        }
                                    })
                                    .done(function(data, textStatus, jqXHR){
                                        // console.log(data);
                                    });
                        }


                        function subscribeApp(page, el){
                            console.log(page);
                            var method = page.is_webhooks_subscribed ? 'delete' :'post';
                            FB.api(
                                '/'+page.id+'/subscribed_apps', 
                                page.is_webhooks_subscribed ? 'delete' :'post', 
                                {'access_token': page.access_token, subscribed_fields: 'leadgen'},
                                function(res){
                                    if(res.error){
                                        console.log(res);
                                        alert('check the developer log');
                                        return false;
                                    }

                                    updateBoostapp(page, true);
                                    debugger;
                                    var tr = $(el).closest('tr');
                                    page.is_webhooks_subscribed = !page.is_webhooks_subscribed;
                                    jQuery('td:eq(1)', tr).html(page.is_webhooks_subscribed?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>')
                                    jQuery('td:eq(2) a', tr).html((page.is_webhooks_subscribed) ? lang('disconnect_leads') : lang('attach_leads')).attr('href', '#'+ ((page.is_webhooks_subscribed) ? 'remove' : 'add')+'/'+page.id)
                                }
                            );
                        }

                        function showLog(page, el){
                            jQuery.ajax({
                                method: 'GET',
                                url: '<?php echo get_loginboostapp_domain() ?>/api/'+'facebook/leads/page/'+page.id,
                                headers: {
                                    'x-cookie': document.cookie
                                }
                            }).done(function(data, textStatus, jqXHR){
                                console.log(data);
                            })
                        }


                        var table = document.createElement('table');
                        table.setAttribute('class', 'table table-striped');
                        table.setAttribute('dir', 'rtl');
                        var thead = document.createElement('thead');
                        table.appendChild(thead);

                        // col 1
                        var col = document.createElement('th');
                        col.innerHTML = 'שם עמוד';
                        thead.appendChild(col);
                        // col 2
                        var col = document.createElement('th');
                        col.setAttribute('class', 'text-center');
                        col.innerHTML = 'סטאטוס';
                        thead.appendChild(col);
                        // col 3
                        var col = document.createElement('th');
                        col.innerHTML = 'חיבור/ניתוק';
                        thead.appendChild(col);
                        // col 4
                        var col = document.createElement('th');
                        col.innerHTML = 'לוג';
                        thead.appendChild(col);
                        // col 5
                        var col = document.createElement('th');
                        col.innerHTML = 'שם מקור ל-pipeline';
                        thead.appendChild(col);   
                        // col 6
                        var col = document.createElement('th');
                        col.innerHTML = 'סטאטוס ראשוני';
                        thead.appendChild(col);                        

                        var tbody = document.createElement('tbody');
                        table.appendChild(tbody);

                        for (let index = 0; index < pages.length; index++) {
                            var page = pages[index];
                            var pagedb = (function(page, options){
                                var lookup = options.pages.filter(function(x){return x.PageId.toString() == page.id.toString()});                               
                                if(!lookup || !lookup.length) return {}
                                return lookup[0];
                            })(page, options)

                            var tr = document.createElement('tr');
                            tr.setAttribute('data-id', page.id)
                            // col 1
                            var td = document.createElement('td');
                            td.innerHTML = page.name;
                            tr.appendChild(td);
                            // col 2
                            var td = document.createElement('td');
                            td.setAttribute('class', 'text-center');
                            td.innerHTML = (page.is_webhooks_subscribed)?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>';
                            tr.appendChild(td);  
                            // col 3
                            var td = document.createElement('td');
                            var a = document.createElement('a');
                            a.onclick = subscribeApp.bind(this, page, a); 
                            a.setAttribute('href', '#'+ ((page.is_webhooks_subscribed) ? 'remove' : 'add')+'/'+page.id); 
                            a.innerHTML =  ((page.is_webhooks_subscribed) ? lang('disconnect_leads') : lang('attach_leads'));
                            td.appendChild(a);
                            tr.appendChild(td); 
                            //col 4
                            var td = document.createElement('td');
                            var a = document.createElement('a');
                            a.onclick = showLog.bind(this, page, a); 
                            a.innerHTML = 'הצג';
                            td.appendChild(a);
                            tr.appendChild(td); 
                            //col 5
                            var td = document.createElement('td');
                            var select = document.createElement('select');
                            select.className = 'form-control';
                            select.name = 'sourceName';
                            select.setAttribute('data-id', page.id)

                            for(var i=0; i<options.sources.length;i++){
                                var option = document.createElement('option');
                                option.value = options.sources[i].name;
                                option.text = options.sources[i].name;
                                option.selected = (options.sources[i].name == (pagedb.SourceName || 'פייסבוק'));
                                select.appendChild(option);
                            }
                            page.boostappSource = select.options[select.selectedIndex].value;
                            select.onchange = function(){
                                var pageId = this.getAttribute('data-id');
                                page = pages.filter(function(x){return x.id == pageId});
                                if(!page || !page.length) return;
                                page = page[0];
                                page.boostappSource = this.options[this.selectedIndex].value;
                                updateBoostapp(page);
                            }
                            td.appendChild(select);
                            tr.appendChild(td);
                            // col 6
                            var td = document.createElement('td');
                            var select = document.createElement('select');
                            select.className = 'form-control';
                            select.name = 'leadStatus';
                            select.setAttribute('data-id', page.id)

                            for(var i=0; i<options.states.length;i++){
                                var option = document.createElement('option');
                                option.value = options.states[i].id;
                                option.text = options.states[i].name;
                                option.selected = parseInt(options.states[i].id) == parseInt(pagedb.StatusId);
                                select.appendChild(option);
                            }
                            page.boostappStatus = select.options[select.selectedIndex].value;
                            select.onchange = function(){
                                var pageId = this.getAttribute('data-id');
                                page = pages.filter(function(x){return x.id == pageId});
                                if(!page || !page.length) return;
                                page = page[0];
                                page.boostappStatus = this.options[this.selectedIndex].value;
                                updateBoostapp(page);
                            }
                            td.appendChild(select);
                            tr.appendChild(td);

                            tbody.appendChild(tr);   
                        }


                        FBalertsBody.html(FBalertsBody.html() + '<BR>');
                        FBalertsBody.append(table);
                        FBalerts.addClass('card-succuess').show();

                    })
                //})

            }, {scope: scopes})
        }

        facebookLoginBtn.on('click', loginFB);
        // loginFB(); // autostart

    })(FB, jQuery);
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));

</script>

    <?php 
        require_once '../../app/views/footernew.php';
    ?>