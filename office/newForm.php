<?php
require_once '../app/init.php';

if (Auth::check()) : if (Auth::userCan('31')) :
    $pageTitle = 'טופס הקמת לקוח';
    require_once '../app/views/headernew.php';

    ?>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

        <script src="/office/assets/js/newForm/autoCompleteTags.js"></script>
        <script src="/office/assets/js/newForm/newForm.js"></script>
        <script src="/office/assets/js/newForm/sendForm.js"></script>

        <link href="/office/assets/css/newForm/newForm.css" rel="stylesheet">

        <body>
            <?php include_once('loader/loader.php') ?>
            <div class="container">
                <div class="btns-wrapper row mb-3 mt-3">
                    <div class="col-md-6">
                        <div class="btn client-btn current-btn">לקוח חדש</div>
                        <div class="btn lead-btn">ליד חדש</div>
                    </div>
                </div>
                <div id="fields">
                    <div class="row dynamic-wrapper mt-3 mb-3"></div>
                </div>
                <div class=" btns-wrapper row mb-3 mt-3">
                    <div class="col-md-6">
                        <div class="float-right btn cancel-btn">בטל</div>
                    </div>
                    <div class="col-md-6">
                        <div id="submitForm" class="float-left btn create-btn">שלח טופס</div>
                    </div>
                </div>
            </div>
        </body>

<?php endif;
endif; ?>