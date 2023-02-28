<?php
require_once '../../app/init.php';
// secure page
if (!Auth::check())
    redirect_to('../../index.php');

echo View::make('headernew')->render();

$report = new StdClass();
$report->name = 'דוח מכירות - קבלות';
?>
<link
    href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">


<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js">
</script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js">
</script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js">
</script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
</script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js">
</script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
</script>
<script src="../js/datatable/dataTables.checkboxes.min.js">
</script>

<link href="../assets/css/fixstyle.css" rel="stylesheet">
<style>
    .bg-gray {
        background-color: #e9ecef;
    }

    .dataTables_scrollHead table {
        margin-bottom: 0px;
    }
    div.dataTables_wrapper div.dataTables_processing {
        position: fixed;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        z-index: 999;
        background: #38ab4b;
        color: #fff;
    }
    th.appendInputs {white-space: nowrap;}
    th.appendInputs input{display: inline-block; max-width: 60px}
    th.appendInputs select{display: inline-block; max-width: 20px}
    .select2-container .select2-search__field {
        width: 100% !important;
    }
</style>

<div class="row pb-3">
    <div class="col-md-6 col-sm-12">
        <h3 class="page-header headertitlemain"  style="height:54px;">
<?php echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12">
        <h3 class="page-header headertitlemain"  style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px;">
                <i class="fas fa-user-plus"></i>
<?php echo $report->name ?>
            </div>
        </h3>
    </div>
</div>

<div class="row px-0 mx-0"  >
    <div class="col-12 mx-0 px-0" >


        <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
<?php echo $report->name ?>
                </li>
            </ol>
        </nav>

        <div class="row">

<?php include("../ReportsInc/SideMenu.php"); ?>

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
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div class="row px-15">

                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th data-name="select" data-bSortable="false"></th>
                                                <th data-name="date">תאריך</th>
                                                <th data-name="product">מוצר</th>
                                                <th data-name="productType">מחלקה</th>
                                                <th data-name="amount">מחיר</th>
                                                <th data-name="branchName">סניף</th>
                                                <th data-name="fullName">שם לקוח</th>
                                                <th data-name="memberType">סוג מנוי</th>
                                                <th data-name="paymentMethod">פרטי תשלום</th>
                                            </tr>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="date">
                                                    <input type="text" data-search="date" data-search-type="dateRange" data-search-start="true" data-date-start="month" data-date-end="now" class="form-control" placeholder="לפי תאריכים">
                                                </th>
                                                <th data-name="product">
                                                    <select data-search="productIds" multiple="multiple" data-id="productsFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מוצר"></select>
                                                </th>
                                                <th data-name="productType">
                                                    <select data-search="departmentIds" multiple="multiple" data-id="departmentFilter" class="form-control" size="1" placeholder="חפש מחלקה"></select>
                                                </th>
                                                <th data-name="amount">
                                                    <input data-search="amount" type="text" name="amount" data-id="amountFilter" class="form-control" placeholder="חפש סכום"> 
                                                </th>
                                                <th data-name="branchName">
                                                    <select data-search="branchIds" multiple="multiple" data-id="branchFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש סניף"></select>
                                                </th>
                                                <th data-name="fullName">
                                                    <input data-search="clientName" type="text" name="clientName" data-id="clientNameFilter" class="form-control" placeholder="חפש לקוח">        
                                                </th>
                                                <th data-name="memberType">
                                                    <select data-search="membershipIds" multiple="multiple" data-id="membershipFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מנוי"></select>
                                                </th>
                                                <th data-name="paymentMethod"></th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="date"></th>
                                                <th data-name="product"></th>
                                                <th data-name="productType"></th>
                                                <th data-name="amount"></th>
                                                <th data-name="branchName"></th>
                                                <th data-name="fullName"></th>
                                                <th data-name="memberType"></th>
                                                <th data-name="paymentMethod"></th>
                                            </tr>
                                        </tfoot>
                                    </table>






                                </div>

                                <div class="row px-15"  >
                                    <div id="membershipReport" style="width: 100%"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- חשבוניות  
                <div class="tab-content">
                         <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                             <div class="card spacebottom">
                                 <div class="card-header text-start">
                                     <i class="fas fa-user-plus"></i>
                                     <strong>
                                     דוח מכירות - חשבוניות
                                     </strong>
                                 </div>
                                 <div class="card-body">
     
                                     
                                     <hr>
     
                                     <div class="row"  style="padding-left:15px; padding-right:15px;">
     
                                         <table class="table table-hover dt-responsive text-start display wrap" id="dataTableCheshbonit"  cellspacing="0" width="100%">
                                             <thead>
                                                 <tr class="bg-dark text-white">
                                                     <th data-name="select" data-bSortable="false"></th>
                                                     <th data-name="date">תאריך</th>
                                                     <th data-name="product">מוצר</th>
                                                     <th data-name="productType">מחלקה</th>
                                                     <th data-name="amount">מחיר</th>
                                                     <th data-name="branchName">סניף</th>
                                                     <th data-name="fullName">שם לקוח</th>
                                                     <th data-name="memberType">סוג מנוי</th>
                                                     <th data-name="paymentMethod">פרטי תשלום</th>
                                                 </tr>
                                                 <tr>
                                                     <th data-name="select"></th>
                                                     <th data-name="date">
                                                         <input type="text" data-search="date" data-search-type="dateRange" data-search-start="true" data-date-start="month" data-date-end="now" class="form-control" placeholder="לפי תאריכים">
                                                     </th>
                                                     <th data-name="product">
                                                         <select data-search="productIds" multiple="multiple" data-id="productsFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מוצר"></select>
                                                     </th>
                                                     <th data-name="productType">
                                                         <select data-search="departmentIds" multiple="multiple" data-id="departmentFilter" class="form-control" size="1" placeholder="חפש מחלקה"></select>
                                                     </th>
                                                     <th data-name="amount">
                                                         <input data-search="amount" type="text" name="amount" data-id="amountFilter" class="form-control" placeholder="חפש סכום"> 
                                                     </th>
                                                     <th data-name="branchName">
                                                         <select data-search="branchIds" multiple="multiple" data-id="branchFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש סניף"></select>
                                                     </th>
                                                     <th data-name="fullName">
                                                         <input data-search="clientName" type="text" name="clientName" data-id="clientNameFilter" class="form-control" placeholder="חפש לקוח">        
                                                     </th>
                                                     <th data-name="memberType">
                                                         <select data-search="membershipIds" multiple="multiple" data-id="membershipFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מנוי"></select>
                                                     </th>
                                                     <th data-name="paymentMethod"></th>
                                                 </tr>
                                             </thead>
     
                                             <tbody>
     
                                             </tbody>
                                             <tfoot>
                                                 <tr>
                                                     <th data-name="select"></th>
                                                     <th data-name="date"></th>
                                                     <th data-name="product"></th>
                                                     <th data-name="productType"></th>
                                                     <th data-name="amount"></th>
                                                     <th data-name="branchName"></th>
                                                     <th data-name="fullName"></th>
                                                     <th data-name="memberType"></th>
                                                     <th data-name="paymentMethod"></th>
                                                 </tr>
                                             </tfoot>
                                         </table>
     
                                         
     
                                         
     
     
                                     </div>
     
                                     <div class="row"  style="padding-left:15px; padding-right:15px;">
                                     <div id="membershipReport" style="width: 100%"></div>
                                     </div>
     
                                 </div>
                             </div>
                         </div>
                     </div>
                
                 </div>
             </div> -->
            </div>

<!--            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
            <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
            <style>
                .datepicker-dropdown {
                    max-width: 300px;
                }



                .datepicker.dropdown-menu {
                    right: auto
                }
            </style>
            <script>
                var BeePOS = BeePOS || {};
                BeePOS.options = BeePOS.options || {};
                BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
                BeePOS.options.datatables.processing = '<i class="fas fa-spinner fa-spin"></i> ' + BeePOS.options.datatables.processing
                var boostAppDataTable = {
                    allowDebug: <?php echo Auth::user()->role_id == '1' ? 'true' : 'false'; ?>,
                    buttons: {
                        allowClientPush: true,
                        excel: <?php echo Auth::userCan('98') ? true : false; ?>,
                        csv: <?php echo Auth::userCan('98') ? true : false; ?>
                    }
                };
            </script>
            <script src="./js/sales.js"></script>


            <!-- popupSendByClientId -->
<?php include('./popupSendByClientId.php'); ?>



<?php
require_once '../../app/views/footernew.php';
?>