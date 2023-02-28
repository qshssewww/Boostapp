<?php
redirect_to(__DIR__.'/manage-leads.php');
exit;

header('Access-Control-Allow-Origin: *');


if(!empty($_POST['type'])){

    switch($_POST['type']){
        case "facebook/page/register":
            unset($_POST['type']);

            $ch = curl_init('https://api.boostapp.co.il/facebook/page/register');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_VERBOSE, true);
            
            // // execute!
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // // close the connection, release resources used
            // curl_close($ch);
            if ($response === FALSE) {
                
                printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
                       htmlspecialchars(curl_error($handle)));
                       http_response_code(500);
                exit;
            }

            http_response_code($http_code);
            // header('Access-Control-Allow-Origin: *');
            header('Content-type: application/json');
            echo $response;

        exit;
        break;
    }
}


 require_once '../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../index.php');

 $report = new StdClass();
 $report->name = lang('lead_manage_facebook');
 $pageTitle = $report->name;
 require_once '../app/views/headernew.php';


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

<!-- <script src="js/translation/translate.js"></script> -->
<!-- <script src="../js/datatable/dataTables.checkboxes.min.js"></script> -->

<link href="assets/css/fixstyle.css" rel="stylesheet">
<style>
    .bg-gray { background-color: #e9ecef; }
    .dataTables_scrollHead table{ margin-bottom: 0px; }
</style>

<!-- <div class="row pb-3">
    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <?php //echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-plus"></i>
                <?php //echo  $report->name ?>
            </div>
        </h3>
    </div>
</div> -->

<div class="row mx-0 px-0"  >
    <div class="col-12 mx-0 px-0" >

        <div class="row">
            <?php include("SettingsInc/RightCards.php"); ?>


            <div class="col-md-10 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                            </div>
                            <div class="card-body px-15"  >

                                <!-- page content -->
                                <div class="card" id="FBalerts" style="display: none;">
                                    <div class="card-header d-flex justify-content-between">
                                        <div>
                                        <?php echo lang('facebook_info') ?>
                                        </div>  
                                        <div  id="facebookLogout">
                                            <a class="btn btn-sm btn-facebook">
                                                <i class="fab fa-facebook-f"></i> <?php echo lang('disconnect_facebook') ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body"></div>
                                </div>

                                <div class="text-center" id="facebookLoginbtnWrapper">
                                    <a class="btn btn-lg btn-social btn-facebook">
                                        <i class="fab fa-facebook-f"></i> <?php echo lang('connect_leads_to_facebook') ?>
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
    padding: 5px;
    font-size: 1.4em;
    text-align: center;
    /* border-left: 1px solid rgba(0,0,0,0.2); */
}
.btn-facebook {
    color: #fff!important;
    background-color: #3b5998;
    border-color: rgba(0,0,0,0.2);
}

body .modal{
  text-align: right;
}

.modal-open {
  overflow: hidden;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 1050;
  display: none;
  overflow: hidden;
  outline: 0;
}

.modal-open .modal {
  overflow-x: hidden;
  overflow-y: auto;
}

.modal-dialog {
  position: relative;
  width: auto;
  margin: 0.5rem;
  pointer-events: none;
}

.modal.fade .modal-dialog {
  transition: -webkit-transform 0.3s ease-out;
  transition: transform 0.3s ease-out;
  transition: transform 0.3s ease-out, -webkit-transform 0.3s ease-out;
  -webkit-transform: translate(0, -25%);
  transform: translate(0, -25%);
}

.modal.show .modal-dialog {
  -webkit-transform: translate(0, 0);
  transform: translate(0, 0);
}

.modal-dialog-centered {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  min-height: calc(100% - (0.5rem * 2));
}

.modal-content {
  position: relative;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.3rem;
  outline: 0;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 1040;
  background-color: #000;
}

.modal-backdrop.fade {
  opacity: 0;
}

.modal-backdrop.show {
  opacity: 0.5;
}

.modal-header {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  border-top-right-radius: 0.3rem;
  border-top-left-radius: 0.3rem;
}

.modal-header .close {
  padding: 1rem;
  margin: -1rem auto -1rem -1rem;
}

.modal-title {
  margin-bottom: 0;
  line-height: 1.5;
}

.modal-body {
  position: relative;
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding: 1rem;
}

.modal-footer {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: end;
  -ms-flex-pack: end;
  justify-content: flex-end;
  padding: 1rem;
  border-top: 1px solid #e9ecef;
}

.modal-footer > :not(:first-child) {
  margin-right: .25rem;
}

.modal-footer > :not(:last-child) {
  margin-left: .25rem;
}

.modal-scrollbar-measure {
  position: absolute;
  top: -9999px;
  width: 50px;
  height: 50px;
  overflow: scroll;
}

@media (min-width: 576px) {
  .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
  }
  .modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
  }
  .modal-sm {
    max-width: 300px;
  }
}

@media (min-width: 992px) {
  .modal-lg {
    max-width: 800px;
  }
}

</style>

<!-- Modal -->
<div class="modal fade" id="fb-boostapp-settings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <h5 class="modal-title"><?php echo lang('page_settings') ?> <span></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-primary" data-submit><?php echo lang('save') ?></button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close') ?></button>
      </div>
    </div>
  </div>
</div>


<script src="./facebookAttachLeads.js?v=<?php echo filemtime(__DIR__.'/facebookAttachLeads.js'); ?>"></script>

    <?php 
        require_once '../app/views/footernew.php';
    ?>