<?php
require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/Video.php";
require_once "Classes/Users.php";

header('Content-Type: text/html; charset=utf-8');
$pageTitle = lang('online_library_title');
require_once '../app/views/headernew.php';


if (Auth::check()) {
    if (Auth::userCan('31')) {
        include_once('loader/loader.php');
?>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

        <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

        <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" rel="stylesheet">

        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"> -->
        <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
        <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>


        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

        <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>
        <script src="//cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js"></script>

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

        <link href="/office/assets/css/onlineLibrary/library.css?<?= filemtime(__DIR__.'/assets/css/onlineLibrary/library.css') ?>" rel="stylesheet">
        <script src="/office/assets/js/onlineLibrary/onlineLibrary.js"></script>

        <link href="/office/assets/css/onlineLibrary/videoEdit.css" rel="stylesheet">
        <link href="/office/assets/css/onlineLibrary/checkbox.css" rel="stylesheet">
        <script src="/office/assets/js/onlineLibrary/editFolder.js"></script>
        <script src="/office/assets/js/onlineLibrary/confirm.js"></script>

        <link href="assets/css/fixstyle.css?<?= filemtime(__DIR__.'/assets/css/fixstyle.css') ?>" rel="stylesheet">

        <div class="col-md-12 col-sm-12 onlineLibraryPage" >
            <div class="row">
                <div class="library-page mb-20">
                    <div class="add-video bg-primary">
                        <i class="fal fa-plus fa-sm" aria-hidden="true"></i>
                    </div>
                        <div class="pis-15 pt-10 bsapp-fs-20 text-start">
                        <?php echo lang('online_library_title') ?>
                        </div>
                    <div class="dataTables_scrollBody pb-0">
                        <table class=" table table-hover dt-responsive text-start display wrap dataTable no-footer dtr-inline"  cellspacing="0" width="100%" role="grid" style="width: 100%;">
                            <tr role="row">
                                <td  class="text-start px-0" style="border:none !important">
                                    <input type="text" id="search-library" class="search text-start" placeholder="<?php echo lang('search_user_list') ?>" aria-controls="folders" /></td>
                                <td class="text-end px-0"  style="border:none !important" >
                                    <div class="table-cog" id='openVideoFolderEdit'><i class="fas fa-cog text-gray-400"></i></div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- <div class="library-top-bar row">
                        <div class="col-11 ">
                            <input type="text" id="search-library" class="search text-start" placeholder="חיפוש" aria-controls="folders" />
                        </div>
                        <div class="col-1 p-0">
                            <div id='openVideoFolderEdit'><i class="fas fa-cog"></i></div>
                        </div>
                    </div> -->

                    <table class="table table-hover dt-responsive text-start display wrap dataTable no-footer dtr-inline"  id="folders" cellspacing="0" width="100%" role="grid" aria-describedby="folders_info" style="width: 100%;">
                        <thead>
                            <tr role="row">
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders " colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                                <th class="text-start no-padding-start" tabindex="0" aria-controls="folders" colspan="1" rowspan="1"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <?php include_once('videoEditPopup.php'); ?>
            <?php include_once('videoEditFolderPopup.php'); ?>
            <?php include_once('../app/views/footernew.php'); ?>
        </div>

        <style>
            .no-padding-start{
                padding-inline-start : 0px !important; 
            }
        </style>

<?php
    }
}
