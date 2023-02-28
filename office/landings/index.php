<?php
    /*if(!empty($_GET['nonce'])){
        echo file_get_contents('https://wp.boostapp.co.il/boostapp/index.php?cookie='.$_COOKIE['247SOFT_session']);
        exit;
    }*/

    require_once '../../app/init.php'; 
    $getVideo = '15';
    $pageTitle = lang('landing_pages');
    require_once '../../app/views/headernew.php';

    $CompanyNum = Auth::user()->CompanyNum;
    $UserId = Auth::user()->id;

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
    if(!empty($SettingsInfo)){
        $BrandsMain = $SettingsInfo->BrandsMain;
        if ($SettingsInfo->BrandsMain!='0'){
            $BrandsNames = DB::table('brands')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->where('Status', '=', '0')->first(); 
        }
    }


if (Auth::userCan('148')):

?>

<link href="../assets/css/fixstyle.css" rel="stylesheet">


<style>
 .dataTables_paginate>a {margin: 0 0.5em}
 .dataTables_paginate span a.current {
        padding: 6px 9px !important;
        background: #00c736 !important;
        border-color: #00c736 !important;
        color: #fff;
    }
 .dataTables_paginate span a {
        padding: 6px 9px !important;
        background: transparent !important;
        border-color: #00c736 !important;
    }

    #landingPages .btn-outline-danger:hover>*, #landingPages .btn-outline-secondary:hover>*, #landingPages .btn-outline-success:hover>*{color: #fff!important;}
    div.dataTables_wrapper div.dataTables_processing {
        background: #25ac1c;
    color: #ffff;
    border-radius: 5px;
    z-index: 1;
    opacity: 0.75;
    }
    @media screen and (max-width: 768px) {
        .bsapp-content {
            overflow-x: scroll;
        }
    }

</style>

   
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
<!--    <script src="js/translation/translate.js"></script>-->
    <style>
   .modal-open .select2-container--open { z-index: 999999 !important; width:100% !important; }
    </style>
    <div class="modal fade" id="createPageModal" tabindex="-1" role="dialog" aria-labelledby="createPageModalLabel" aria-hidden="true">
        <div class="modal-dialog text-right" role="document">
            <div class="modal-content">



            <div class="modal-header text-start">
                <h5 class="modal-title" id="createPageModalLabel"><?php echo lang('create_new_page') ?></h5>
                <button type="button" class="close float-left ml-0" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <ul class="nav nav-tabs pr-0" id="newPageTab" role="tablist" hidden>
                <!-- <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#newPageFromTemplate" role="tab" aria-controls="newPageFromTemplate" aria-selected="true">מתבנית</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#newPageBlank" role="tab" aria-controls="newPageBlank" aria-selected="false"><?php echo lang('new') ?></a>
                </li>
            </ul>

            <div class="tab-content" id="newPageTabContent">
                <!-- <div class="tab-pane fade show active" id="newPageFromTemplate" role="tabpanel" aria-labelledby="newPageFromTemplate-tab">newPageFromTemplate</div> -->
                <div class="tab-pane fade show active" id="newPageBlank" role="tabpanel" aria-labelledby="newPageBlank-tab">
                
                
                
    
                    <form id="newPage" class="mt-4">
                        <div class="modal-body text-start">
                            <div class="form-group">
                                <label><?php echo lang('page_name') ?></label>
                                <input type="text" name="title" placeholder="<?php echo lang('new_name_to_page') ?>" class="form-control">
                            </div>
                            <!-- <div class="form-group">
                                <label>דף תודה</label>
                                <div id="newPageThanksUrl">
                                    <select name="thanksPage" class="form-control"  style="width: 100%"></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>קישור תודה</label>
                                <input type="text" name="thanksPageUrl" class="form-control" placeholder="הקלד קישור או בחר מהרשימה למעלה">
                            </div> -->
                            <div class="form-group">
                                <label><?php echo lang('page_type') ?></label>
                                <select name="pageType" class="form-control">
                                    <option value="page" selected><?php echo lang('regular_page') ?></option>
                                    <option value="thankyou"><?php echo lang('thank_you_page') ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo lang('publish_status') ?></label>
                                <select name="status" class="form-control">
                                    <option value="publish"><?php echo lang('published') ?></option>
                                    <option value="pending"><?php echo lang('pending') ?></option>
                                    <option value="draft" selected><?php echo lang('draft') ?></option>
                                    <option value="trash"><?php echo lang('recycle_bin') ?></option>
                                </select>
                            </div>
                                <input type="hidden" name="type" value="page">
                                <input type="hidden" name="action" value="insert">
                                <input type="hidden" name="boostapp" value="<?php echo lang('landing') ?>">
                        </div>
                        <div class="modal-footer">
                            <!-- <button type="button" class="btn btn-secondary float-left" data-dismiss="modal">ביטול</button> -->
                            <input type="submit" class="btn btn-success" value="<?php echo lang('create_page') ?>" data-action="create">
                            <input type="submit" class="btn btn-success" value="<?php echo lang('edit') ?>" data-action="edit" style="display: none;">
                        </div>
                    </form>               
                
                
                
                
                
                </div>
            </div>



            </div>
        </div>
    </div>


 
    <iframe src="//wp.boostapp.co.il/boostapp/index.php?login=true&cookie=<?php echo $_COOKIE['247SOFT_session'];?>" class="d-none"></iframe>


    <!-- <div class="alert alert-danger text-center" role="alert">
       אנו מתנצלים, אך עקב תקלה תכנית יש בעייה עם הצגת דפי הנחיתה, אל דאגה כל דפי הנחיתה שייצרתם עדיין קיימים, בשעות הקרובות היום (10/02/2019) הכל יחזור לעבוד חלק, בוסטאפ
    </div> -->

    <div class="col-md-12 col-sm-12">
        <table id="landingPages" class="table table-striped text-start" style="width:100%">
            <thead  style="display: none;">
                <tr class="bg-dark text-white">
                    <th data-name="name"><?php echo lang('page_name_star') ?></th>
                    <th data-name="status"><?php echo lang('status_star') ?></th>
                    <th data-name="pageType"><?php echo lang('page_type') ?></th>
                    <th data-name="create"><?php echo lang('created_date') ?></th>
                    <th data-name="tools" class="no-sort"><?php echo lang('tools') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th data-name="name"></th>
                    <th data-name="status"></th>
                    <th data-name="pageType"></th>
                    <th data-name="create"></th>
                    <th data-name="tools"></th>
                </tr>
            </tfoot>
        </table>
        <div class="text-start small">
        <?php echo lang('double_tap_edit') ?>
        </div>
    </div>


<!-- Modal -->
<div class="modal fade text-start" id="sendUrlToClientPush" tabindex="-1" role="dialog" aria-labelledby="sendUrlToClientPushLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendUrlToClientPushLabel"><?php echo lang('send_link_to_clients') ?> <span></span></h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">



      <table class="table table-hover dt-responsive display wrap text-start" id="dataTableClients" cellspacing="0" width="100%">
        <thead>
            <tr class="bg-dark text-white">
                <th data-name="select" data-bSortable="false"></th>
                <th data-name="fullName"><?php echo lang('client_name') ?></th>
                <th data-name="status"><?php echo lang('status') ?></th>
                <th data-name="phone"><?php echo lang('telephone') ?></th>
                <th data-name="age"><?php echo lang('age') ?></th>
                <th data-name="rank"><?php echo lang('rank') ?></th>
                <!-- <th data-name="membership" data-bSortable="false">סוג מנוי</th> -->
                <th data-name="branch" data-bSortable="false"><?php echo lang('branch') ?></th>
                
            </tr>
            <tr>
                <th class="text-start"></th>
                <th class="text-start">
                    <input data-search="clientName" type="text" name="clientName" id="clientNameFilter" class="form-control" placeholder="<?php echo lang('search_client') ?>">
                </th>
                <th>
                <select data-search="clientStatus" id="clientStatusFilter" class="form-control">
                    <option value="0"><?php echo lang('active') ?></option>
					<option value="2"><?php echo lang('interested_single') ?></option>
                    <option value="1"><?php echo lang('archive') ?></option>
                    <option value=""><?php echo lang('all') ?></option>
                </select>
                </th>
                <th class="text-start">
                    <input data-search="clientPhone" type="text" name="clientPhone" id="clientPhoneFilter" class="form-control" placeholder="<?php echo lang('search_phone') ?>">
                </th>
                <th class="text-start">
                    <input data-search="clientAge" type="text" name="clientAge" id="clientAgeFilter" class="form-control" placeholder="<?php echo lang('search_age') ?>">
                </th>
                <th class="text-start">
                    <select data-search="clientRanks" multiple="multiple" id="clientRanksFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_membership') ?>"></select>
                </th>
                <!-- <th style="text-align:right;">
                    <select data-search="productIds" multiple="multiple" id="productsFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מוצר"></select>
                </th> -->
                <th class="text-start">
                    <select data-search="branchIds" multiple="multiple" id="branchFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_by_branch') ?>"></select>
                </th>
                
            </tr>
        </thead>

        <tbody></tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <!-- <th></th> -->
                <th></th>
            </tr>
        </tfoot>
        </table>

      </div>
        
<!--
      <div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="button" name="submit" class="btn btn-primary text-white">שמור שינויים</button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal">סגור</button>
                
				</div> 
-->
        
        
        
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal"><?php echo lang('search_by_branch') ?></button>
      </div>
    </div>
  </div>
</div>

<?php 
$Subject = "";
$Content = lang('url_ajax');
include('../Reports/popupSendByClientId.php'); 
?>

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap4.js"></script>
    <script>
    var site = '<?php try{echo file_get_contents('https://wp.boostapp.co.il/boostapp/index.php?cookie='.$_COOKIE['247SOFT_session'], false, stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false))));}catch(Exception $e){} ?>';
    var api = 'https://login.boostapp.co.il/api/';
    </script>
    <script src="<?php echo app()->url('office/landings/main.js') ?>?v=<?php echo filemtime(app_path('../office/landings/main.js'))?>"></script>
    <script>
        
        var categoriesDataTable;
	    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        
        boostAppDataTable = {
            id: '#dataTableClients',
            language: BeePOS.options.datatables,
            responsive: true,
            scrollX: true,
            scrollY: true,
            buttons: {
                allowClientPush: true,
                excel: false,
                csv: false
            }
        }
        

        
    </script>
    <script src="<?php echo app()->url('office/Reports/js/clients.js') ?>?v=<?php echo filemtime(app_path('../office/Reports/js/clients.js'))?>"></script>
    <script src="<?php echo app()->url('office/js/datatable/dataTables.checkboxes.min.js') ?>?v=<?php echo filemtime(app_path('../office/js/datatable/dataTables.checkboxes.min.js'))?>"></script>

        
<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>    
        
<?php
    require_once '../../app/views/footernew.php';