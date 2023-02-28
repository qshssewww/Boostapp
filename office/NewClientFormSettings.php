<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ClientFormFields.php';
require_once 'Classes/ClientForm.php';
require_once 'Classes/FormFields.php';

if (Auth::check()){
    $pageTitle = 'הגדרות הקמת לקוח';
    require_once '../app/views/headernew.php';
    if (Auth::userCan('31')){
    include_once('loader/loader.php');
    ?>
        <script src="/office/assets/js/clientForm/newClientFormSettings.js"></script>
        <link href="/office/assets/css/ClientForm/clientFormSetting.css" rel="stylesheet">
        <body>
        <div class="settings-form display-flex">
            <div class="header">
                <span>הגדרת טופס</span>
                <select id="form_type" class="color">
                    <option value="client" selected>לקוח חדש</option>
                    <option value="lead" >Lead</option>
                </select>
            </div>

            <div id ='form-new-cus' class ="show-form">
                <div id="new_client_form" class="formFields">
                    <!-- get data from ajax and append to form -->
                    <input style="display: none" name="form_id" id="form_id">
                    <input style="display: none" name="company_num" id="company_num" >
                    <input style="display: none" name="fromPage" value="form_settings">
                    <table id="fields-table">
                        <thead class="thead-dark">
                        <tr>
                            <th>שם השדה</th>
                            <th>מוצג</th>
                            <th>חובה</th>
                            <th>סוג השדה</th>
                        </tr>
                    </thead>
                    <tbody class="clientSettingForm" id="tbodyFormSettings">

                </tbody>
                    </table>
                    <div id="add_new_field" class="add-new-field color">
                        <i class="fas fa-plus"></i>
                        <span>הוספת שדה</span>
                    </div>
                    <input class="saveSettings" type="submit" value="Submit">
                </div>


            </div>
        </body>
        <?php
    }
}
?>



