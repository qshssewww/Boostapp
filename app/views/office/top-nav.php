<?php
$MenuCompanyBG = 'bg-dark';             

if (Auth::check()): 
                
    $CompanyNum = !empty(Auth::user()->CompanyNum) ? Auth::user()->CompanyNum : FALSE;
    $roleId = !empty(Auth::user()->role_id) ? Auth::user()->role_id : FALSE;
                
    if ($roleId == '1' && $CompanyNum != '100'):
        printf("<style>nav.navbar.fixed-top.boostapp-admin{background-color: firebrick}</style>");
        // $MenuCompany = 'style="background-color: firebrick;"';
        $MenuCompanyBG = 'boostapp-admin';    
    endif;
                
endif;             
?>
                
 <nav class="navbar navbar-expand-lg fixed-top navbar-dark <?php echo $MenuCompanyBG; ?> text-white pull-right text-right" dir="rtl">
    <div class="container-fluid" style="margin: 0px;padding: 0px;">	
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
            
        <div class="collapse navbar-collapse text-right" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto text-right" style="margin-right: 0px;padding-right: 0px;">
                                 
            <?php if (Auth::check()): ?>           
                <li class="nav-item dropdown text-right">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo @Auth::user()->display_name ?>
                        <b class="caret"></b></a>
                        <ul class="dropdown-menu text-right dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="./office/index.php"><?php echo lang('path_main') ?></a></li>
                            <li><a class="dropdown-item" href="./logout.php"><?php echo lang('logout') ?></a></li>
                        </ul>
                </li>           
            <?php endif; ?>

                <li class="nav-item"><a class="nav-link" href="/office/ReportsDash.php"><i class="fas fa-chart-pie" aria-hidden="true"></i> <?php echo lang('reports') ?> </a></li>


            </ul>
                  
            <?php if (Auth::check()): ?>   

                <?php if (Auth::userCan('44')): // חיפוש לקוחות?>		  
                <div style="max-width: 300px;width: 100%;">
                    <div class="Select2OY select2opacity">
                        <select name="ClientSearchTop" id="ClientSearchTop" data-placeholder="חפש לקוח" class="form-control select2 ClientSearchTop"></select>
                    </div>
                </div>
                <?php endif ?>
                    
                    
                <?php if (Auth::userCan('100')):  ///// הגדרת סניפים
                    
                        $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
                    
                        if ($CompanySettingsDash->BrandsMain!='0' && Auth::user()->role_id != '1'):
                        
                            $ItemDetailsHeader = DB::table('brands')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->where('Status', '=', '0')->first(); 
                        ?>            
                    <div style="margin-right: 10px;" class="ChoodeItemOY select2opacity"><button data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('branch_select_footernew') ?> :: <?php echo @$ItemDetailsHeader->BrandName; ?>" type="button" class="btn btn-light ProductSearchToptooltip" data-ip-modal='#ChooseItem'><i class="fas fa-sitemap"></i></button></div>
                        <?php endif; ?>
                <?php endif ?>
                    
                <?php if (Auth::user()->role_id == '1') : // החלפת חברה ?>
                    <?php $CompanyNumDetailsHeader = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); ?>
                    <div style="margin-right: 10px;" class="ChoodeCompanyNumOY select2opacity"><button data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('select_company_footernew') ?> :: <?php echo @$CompanyNumDetailsHeader->CompanyName; ?>" type="button" class="btn btn-light ProductSearchToptooltip" data-ip-modal='#ChooseCompanyNum'><i class="fas fa-building fa-fw"></i></button></div>
                    <div style="margin-right: 10px;" class="ChoodeSystemOY select2opacity"><a href="/office/SystemSettings.php" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('system_settings_headerneew') ?>" class="btn btn-light ProductSearchToptooltip"><i class="fas fa-lock fa-fw"></i></a></div>
                <?php endif; ?>      
                  
                  
            <?php endif; ?>
                  
            <ul  class="navbar-nav" style="margin: 0;padding: 0;"> 
                <li class="nav-item" style="margin: 0;padding: 0;">
                    <a href="/office/" class="nav-link" style="margin: 0;padding: 0;padding-right: 10px;padding-left: 5px;"><img  src="<?php echo App::url("/assets/img/MainLogo.png") ?>" title="BOOSTAPP V.2.1"></a>
                </li> 
            </ul>
                  
        </div>
    </div>
</nav>