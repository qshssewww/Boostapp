<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ClientFormFields.php';
require_once 'Classes/ClientForm.php';


echo View::make('sidebar')->render();

if (Auth::check()){
    if (Auth::userCan('31')){
        
        //GET ALL STREETS AND CITIES NAMES
        $cities = DB::table('cities')->get();
        $streets = DB::table('street')->get();
        
        // get company data
        $company = Company::getInstance();
        $company_num =  $company->__get("CompanyNum");
        
        //new client form fields data
        $clientForm = ClientForm::getFormByCompanyNumAndType($company_num,'client');

        //lead form fields data
        $leadForm = ClientForm::getFormByCompanyNumAndType($company_num,'lead');

        if ($clientForm("form_id") == null){
            echo 'please set form first. go to NEW CUSTOMER FORM SETTINGS';
        }
        else{
            $lead_display = 'hide-form';
            $new_customer_display = 'show-form';
            $NCselected='selected';
            $Lselected = '';
            $config_lead_color = '';
            $config_new_customer_color = 'color';
            $separation_line_dispay = 'show-form';

            if(isset($_GET['type'])){
                if($_GET['type'] == 'lead'){
                    $lead_display = 'show-form';
                    $new_customer_display = 'hide-form';
                    $NCselected='';
                    $Lselected = 'selected';
                    $config_lead_color = 'color';
                    $config_new_customer_color = '';
                }
            }
?>
<script src="/office/assets/js/clientForm/newClientForm.js"></script>
<link href="/office/assets/css/ClientForm/clientForm.css" rel="stylesheet">
<body>
    <div class="new-client-form">
        <div class="header">
            <span><?php echo lang('form_type_newform') ?></span>
            <div class="menu">
                <div class="select">
                    <div class="choice <?php echo $config_new_customer_color; ?>" id="new_customer" value="new_customer" <?php echo $NCselected ?>><span><?php echo lang('new_client') ?></span></div>
                    <div class="choice <?php echo $config_lead_color; ?>" id="lead" value="lead" <?php echo $Lselected ?>><span><?php echo lang('a_new_lead') ?></span></div>
                </div>
                <div class="option">
                    <div class="choice settings"><i class="fas fa-cog"></i></div>
                </div>
            </div>
        </div>

        <?php
        if(isset($_GET['message']) && $_GET['message'] == 'illegalFields'){
            ?>
            <h3>שגיאה - כמה מהשדות שמולאו אינם חוקיים. אנא מלא את הטופס בשנית (שירה צריכה לעצב)</h3>
            <?php
        }
        ?>



        <div id="form-new-cus" class="<?php echo $new_customer_display?>">
            <form id="new_client_form" action="/office/ajax/ajaxNewClientForm.php" method="post">
                <?php
    // FIELDS NAMES MUST BE THE SAME AS COLUMNS NAMES
                foreach ($form_newCustomer_fields as $field){
                    if($field->show == '1'){
                            if($field->name == 'phone'){
                                ?>
                                <div class="<?php echo $field->name?> field">
                                    <label>
                                        מס' נייד
                                        <?php if($field->mandatory == '1'){?>
                                            <span>*</span>
                                            <?php
                                        }?>
                                    </label><br>
                                    <div class="hold-2-details">
                                        <div class="details details1">
                                            <input type="number" class="<?php echo $field->name?> remove-border" value="" name="<?php echo $field->name?>[]">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="details details2">
                                            <select class="remove-border" name="<?php echo $field->name?>[]">
                                                <option value="+972">972+</option>
                                                <option value="050">050</option>
                                                <option value="054">054</option>
                                                <option value="077">077</option>
                                            </select>
                                        </div>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'gender'){
                                ?>
                                <div class="<?php echo $field->name?> field">
                                    <label>
                                        מין
                                        <?php if($field->mandatory == '1'){?>
                                            <span>*</span>
                                        <?php
                                        }?>
                                    </label><br>
                                    <div class="details">
                                        <select class="select-an-option remove-border" name="<?php echo $field->name?>">
                                            <option value="0">זכר</option>
                                            <option value="1">נקבה</option>
                                        </select>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'mail'){
                                ?>
                                <div class="<?php echo $field->name?> field">
                                    <label>
                                        מייל
                                        <?php if($field->mandatory == '1')
                                        {?>
                                            <span>*</span>
                                        <?php
                                        }
                                        ?>
                                    </label><br>
                                    <div class="details">
                                        <input type="email" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[]">
                                        <i class="far fa-envelope"></i>
                                    </div>
                                    <div class="keep-in-touch">
                                        <label class="replace">
                                            <input type="checkbox" value="" name="<?php echo $field->name?>[]">
                                            <span class="checkmark"></span>
                                        </label>
                                        <span>קבלת דיוור במייל</span>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'id number'){
                                ?>
                                <div class="<?php echo $field->name?> field">
                                    <label>
                                        ת''ז
                                        <?php if($field->mandatory == '1')
                                        {?>
                                            <span>*</span><?php
                                        }?>
                                    </label><br>
                                    <div class="details">
                                        <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>" pattern="[0-9]{9}">
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'date of birth'){
                                $max_date = date("Y/m/d")
                                ?>
                                <div class="<?php echo$field->name?> field">
                                    <label>
                                        תאריך לידה
                                        <?php if($field->mandatory == '1'){?>
                                            <span>*</span><?php
                                        }?>
                                    </label><br>
                                    <div class="details">
                                        <input type="date" class="<?php echo $field->name?> remove-border replace-between-arrow-and-input" value="" name="<?php echo $field->name?>" max="<?php echo $max_date?>">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'address'){
                                ?>
                                <div class="<?php echo $field->name?> field address full-block">
                                    <p>
                                        <i class="far fa-map"></i>
                                        כתובת מגורים
                                        <?php if($field->mandatory == '1'){?>
                                            <span>*</span>
                                        <?php }?>
                                    </p>
                                    <div class="line-one">
                                        <div class="city">
                                            <label>עיר</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border " placeholder="הקלד 3 תווים או יותר" name="<?php echo $field->name?>[City]" id="CitiesSelect">
                                            </div>
                                        </div>

                                        <div class="street">
                                            <label>רחוב</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" placeholder="הקלד 3 תווים או יותר" name="<?php echo $field->name?>[City]" id="StreetSelect">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="line-two">
                                        <div class="address-fields">
                                            <div class="house-num">
                                                <label>מספר בית</label><br>
                                                <div class="details">
                                                    <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[street_number]">
                                                </div>
                                            </div>
                                            <div class="house-num">
                                                <label>מספר דירה</label><br>
                                                <div class="details">
                                                    <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[house_number]">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="address-fields">
                                            <div class="house-num">
                                                <label>תא דואר</label><br>
                                                <div class="details">
                                                    <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[post_office_box]">
                                                </div>
                                            </div>
                                            <div class="house-num">
                                                <label>מיקוד</label><br>
                                                <div class="details">
                                                    <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[postal_code]">
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>

                                </div><br><br>
                                <?php
                                continue;
                            }
                            else if($field->name == 'payment'){
                                ?>
                                <div class="<?php echo $field->name?> field credit-card full-block">
                                    <p>
                                        <i class="far fa-credit-card"></i>
                                        אמצעי תשלום
                                        <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                    </p>
                                    <div class="line-one">
                                        <div class="delete"><div></div></div>
                                        <div>
                                            <label>מספר כרטיס</label><br>
                                            <div class="details">
                                                <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[card number]">
                                            </div>
                                        </div>
                                        <div class="validity">
                                            <label>תוקף</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[expire]">
                                            </div>
                                        </div>
                                        <div class="ccv">
                                            <label>CCV</label><br>
                                            <div class="details">
                                                <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[cvv]">
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                                else if($field->name == 'medical records' || $field->name == 'documentation' ){
                                ?>
                                <div class="<?php echo $field->name?> field full-block">
                                    <p>
                                        <i class="far fa-clipboard"></i>
                                        <?php
                                        if ($field->name == 'medical records'){
                                            echo "תיעוד רפואי";
                                            }
                                        if ($field->name == 'documentation') {
                                            echo "תיעוד לקוח";
                                        }?>

                                        <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                    </p>
                                    <div>
                                        <label>תוכן התיעוד</label><br>
                                        <div class="details">
                                            <?php if ($field->name == 'medical records'){?><input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[medical records]">
                                            <?php
                                            }?>
                                            <?php if ($field->name == 'documentation'){?><input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[documentation]">
                                            <?php }?>
                                        </div>
                                    </div>
                                    <div class="important">
                                        <label class="replace">
                                            <input type="checkbox" value="" name="<?php echo $field->name?>[]">
                                            <span class="checkmark"></span>
                                        </label>
                                        <?php if ($field->name == 'medical records'){?><span>הוסף הערה באייקון רפואי קבוע לשם הלקוח</span><?php }?>
                                        <?php if ($field->name == 'documentation'){?><span>הוסף הערה באייקון קבוע לשם הלקוח</span><?php }?>
                                    </div>
                                </div><br><br>
                                <?php
                                continue;
                            }
                            ?>
                            <div class="<?php echo $field->name?> field">
                                <label>
                                    <?php if ($field->name == 'first name'){?>שם פרטי<?php }?>
                                    <?php if ($field->name == 'last name'){?>שם משפחה<?php }?>
                                    <?php if ($field->name != 'last name' && $field->name != 'first name')
                                    {
                                        echo $field->name;
                                    }
                                    if($field->mandatory == '1'){?><span>*</span>
                                    <?php }?>
                                </label><br>
                                <div class="details">
                                    <input class="remove-border" type="text" value="" name="<?php echo $field->name?>" <?php if($field->mandatory == '1') echo 'required'?>>
                                    <?php if ($field->name == 'first name' || $field->name == 'last name'){?><i class="far fa-user"></i><?php }?>
                                </div>
                            </div><br><br>
                            <?php
                        }
                    }
                ?>
                <br><br>
                <input type="hidden" name="fromPage" value="form">
                <input type="hidden" name="form_type" value="formNewCus">
                <input type="button" onclick="Ajaxcall()" value="Submit">
            </form>
        </div>


    <div id="form-lead" class="<?php echo $lead_display?>">
        <form id="lead_form" action="/office/ajax/ajaxNewClientForm.php" method="post">
            <?php
             foreach ($form_lead_fields as $field){
                if($field->show == '1'){
                    if($field->name == 'representative'){
                        $AgentLoops = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('ActiveStatus', '=', '0')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                            <select name="<?php echo $field->name?>[]">
                                <?php
                                foreach ($AgentLoops as $agent){
                                    ?>
                                    <option value="<?php echo $agent->id?>"><?php echo $agent->display_name?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'status'){
                        $leadstatus = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('Act','=', '0')->where('Status','=', '0')->orderBy('PipeId', 'ASC')->orderBy('Sort', 'ASC')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                            <select name="<?php echo $field->name?>[]">
                                <?php
                                foreach ($leadstatus as $status){
                                    ?>
                                    <option value="<?php echo $status->id?>"><?php echo $status->Title?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'lead source'){
                        $leadsource = DB::table('leadsource')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Title', 'ASC')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                            <select name="<?php echo $field->name?>[]">
                                <?php
                                foreach ($leadsource as $source){
                                    ?>
                                    <option value="<?php echo $source->id?>"><?php echo $source->Title?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'branch'){
                        $branches = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                            <select name="<?php echo $field->name?>[]">
                                <?php
                                foreach ($branches as $branch){
                                    ?>
                                    <option value="<?php echo $branch->id?>"><?php echo $branch->BrandName?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'interested in class'){
                        $ClassTypes = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Type', 'ASC')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                            <select name="<?php echo $field->name?>[]">
                                <?php
                                foreach ($ClassTypes as $class_type){
                                    ?>
                                    <option value="<?php echo $class_type->id?>"><?php echo $class_type->Type?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'pipeline'){
                        $pipeline_categories = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();
                        ?>
                        <div class="<?php echo $field->name?>">
                            <label><?php echo $field->name?></label><br>
                             <select name="<?php echo $field->name?>[]">
                        <?php
                             foreach ($pipeline_categories as $category){
                                 ?>
                                 <option value="<?php echo $category->CompanyNum?>"><?php echo $category->Title?></option>
                                 <?php
                             }
                        ?>
                            </select>
                        </div><br><br>
                        <?php
                        continue;
                    }
                    if($field->name == 'phone'){
                        ?>
                        <div class="<?php echo $field->name?> field">
                            <label>
                                מס' נייד
                                <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                            </label><br>
                            <div class="hold-2-details">
                                <div class="details details1">
                                    <input type="number" class="<?php echo $field->name?> remove-border" value="" name="<?php echo $field->name?>[]">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="details details2">
                                    <select class="remove-border" name="<?php echo $field->name?>[]">
                                        <option value="+972">972+</option>
                                        <option value="050">050</option>
                                        <option value="054">054</option>
                                        <option value="077">077</option>
                                    </select>
                                </div>
                            </div>
                        </div><br><br>
                        <?php
                        continue;
                    }if($field->name == 'gender'){
                            ?>
                            <div class="<?php echo $field->name?> field">
                                <label>
                                    מין
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </label><br>
                                <div class="details">
                                    <select class="select-an-option remove-border" name="<?php echo $field->name?>">
                                        <option value="0">זכר</option>
                                        <option value="1">נקבה</option>
                                    </select>
                                </div>
                            </div><br><br>
                            <?php
                            continue;
                    }
                    if($field->name == 'mail'){
                            ?>
                            <div class="<?php echo $field->name?> field">
                                <label>
                                    מייל
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </label><br>
                                <div class="details">
                                    <input type="email" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[]">
                                    <i class="far fa-envelope"></i>
                                </div>
                                <div class="keep-in-touch">
                                    <label class="replace">
                                        <input type="checkbox" value="" name="<?php echo $field->name?>[]">
                                        <span class="checkmark"></span>
                                    </label>
                                    <span>קבלת דיוור במייל</span>
                                </div>
                            </div><br><br>
                            <?php
                            continue;
                    }if($field->name == 'id number'){
                            ?>
                            <div class="<?php echo $field->name?> field">
                                <label>
                                    ת''ז
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </label><br>
                                <div class="details">
                                    <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>" pattern="[0-9]{9}">
                                </div>
                            </div><br><br>
                            <?php
                            continue;
                    }if($field->name == 'date of birth'){
                            $max_date = date("Y/m/d")
                            ?>
                            <div class="<?php echo $field->name?> field">
                                <label>
                                    תאריך לידה
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </label><br>
                                <div class="details">
                                    <input type="date" class="<?php echo $field->name?> remove-border replace-between-arrow-and-input" value="" name="<?php echo $field->name?>" max="<?php echo $max_date?>">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div><br><br>
                            <?php
                            continue;
                    }if($field->name == 'address'){
                            ?>
                            <div class="<?php echo $field->name?> field address full-block">
                                <p>
                                    <i class="far fa-map"></i>
                                    כתובת מגורים
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </p>
                                <div class="line-one">
                                <div class="city">
                                        <label>עיר</label><br>
                                        <div class="details">
                                            <input type="text" value="" class="<?php echo $field->name?> remove-border" placeholder="הקלד 3 תווים או יותר" name="<?php echo $field->name?>[City]" id="CitiesSelect">
                                        </div>
                                    </div>

                                    <div class="street">
                                        <label>רחוב</label><br>
                                        <div class="details">
                                            <input type="text" value="" class="<?php echo $field->name?> remove-border" placeholder="הקלד 3 תווים או יותר" name="<?php echo $field->name?>[City]" id="StreetSelect">
                                        </div>
                                    </div>
                                </div>

                                <div class="line-two">
                                    <div class="address-fields">
                                        <div class="house-num">
                                            <label>מספר בית</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[street_number]">
                                            </div>
                                        </div>
                                        <div class="house-num">
                                            <label>מספר דירה</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[house_number]">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="address-fields">
                                        <div class="house-num">
                                            <label>תא דואר</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[post_office_box]">
                                            </div>
                                        </div>
                                        <div class="house-num">
                                            <label>מיקוד</label><br>
                                            <div class="details">
                                                <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[postal_code]">
                                            </div>
                                        </div>
                                    </div>
                                </div><br>

                            </div><br><br>
                            <?php
                            continue;
                    }if($field->name == 'contacts'){
                            ?>
                            <div class="<?php echo $field->name?> field contacts full-block">
                                <p>
                                    <i class="far fa-user"></i>
                                    אנשי קשר
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </p>
                                <div class="line-one">
                                <div>
                                        <label>שם מלא</label><br>
                                        <div class="details">
                                            <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[0][full_name]">
                                        </div>
                                    </div>

                                    <div>
                                        <label>קרבה</label><br>
                                        <div class="details">
                                            <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[0][relationship]">
                                        </div>
                                    </div>
                                </div>
                                <div class="line-one">
                                <div>
                                        <label>טלפון</label><br>
                                        <div class="hold-2-details">
                                            <div class="details details1">
                                                <input type="number" class="<?php echo $field->name?> remove-border" value="" name="<?php echo $field->name?>[0][phone][number]">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="details details2">
                                                <select class="remove-border" name="<?php echo $field->name?>[0][phone][area code]">
                                                    <option value="+972">972+</option>
                                                    <option value="050">050</option>
                                                    <option value="054">054</option>
                                                    <option value="077">077</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label>מייל</label><br>
                                        <div class="details">
                                            <input type="email" value="" class="<?php echo $field->name?> field remove-border" name="<?php echo $field->name?>[0][mail]"><br>
                                        </div>
                                    </div>
                                </div><br>

                                <button id="add_contact2" class="add-new">
                                    <i class="fas fa-plus"></i>
                                    <span>איש קשר נוסף</span>
                                </button>
                            </div><br><br>
                            <?php
                            continue;
                    }if($field->name == 'payment'){
                            ?>
                            <div class="<?php echo $field->name?> field credit-card full-block">
                                <p>
                                    <i class="far fa-credit-card"></i>
                                    אמצעי תשלום
                                    <?php if($field->mandatory == '1'){?><span>*</span><?php }?>
                                </p>
                                <div class="line-one">
                                    <div>
                                        <label>מספר כרטיס</label><br>
                                        <div class="details">
                                            <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[card number]">
                                        </div>
                                    </div>
                                    <div class="validity">
                                        <label>תוקף</label><br>
                                        <div class="details">
                                            <input type="text" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[expire]">
                                        </div>
                                    </div>
                                    <div class="ccv">
                                        <label>CCV</label><br>
                                        <div class="details">
                                            <input type="number" value="" class="<?php echo $field->name?> remove-border" name="<?php echo $field->name?>[cvv]">
                                        </div>
                                    </div>
                                </div>
                            </div><br><br>

                            <?php
                            continue;
                        }
                        ?>
                    <div class="<?php echo $field->name?> field">
                            <label>
                                <?php
                                if ($field->name == 'first name'){
                                    echo "שם פרטי";
                                }
                                if ($field->name == 'last name'){
                                    echo "שם משפחה";
                                }
                                if ($field->name != 'last name' && $field->name != 'first name'){
                                    echo $field->name;
                                }
                                if($field->mandatory == '1'){
                                    ?><span>*</span>
                                <?php }?>
                            </label><br>
                            <div class="details">
                                <input class="remove-border" type="text" value="" name="<?php echo $field->name?>" <?php if($field->mandatory == '1') echo 'required'?>>
                                <?php if ($field->name == 'first name' || $field->name == 'last name'){?><i class="far fa-user"></i><?php }?>
                            </div>
                    </div><br><br>
                        <?php
                    }
                }
                ?>
                <br><br>
                <input type="hidden" name="fromPage" value="form">
                <input type="hidden" name="form_type" value="formLead">
                <input type="button" onclick="Ajaxcall()" value="Submit">
            </form>
        </div>

    </div>

</body>

<?php
        }
    }
}