<?php

require_once "Company.php";

class ShopRender
{
    /**
     * @var $company Company
     */
    private $company;

    private $isMobile;
    /**
     * ShopRender constructor.
     */
    public function __construct()
    {
        $this->company = Company::getInstance();
        $this->isMobile = isMobileDevice();
    }

    /**
     * @param $AppSettings
     * @param $headline string
     * @param $page string
     * @return string
     */
    public function renderFirstBlock($AppSettings, $headline, $page = "membership"){

        $htmlString = '<label class="ItemBlockLabel" for="mainItemBlock">'.$headline.'</label>
                       <div class="mainItemBlock ItemBlock">
                            <div class="itemBlockDiv">
                                <div class="form-group">
                                    <input id="membershipName" required class="blockInput form-control"/>
                                    <label class="shopLabel form-control-placeholder" for="membershipName">'.lang('subscribtion_name_membership').'</label>                                 
                                </div>';
        if($AppSettings->MembershipType == 1 && count($this->company->getMembershipTypes()) > 0 && $page == "membership") {
                $htmlString .= '<div class="form-group">                                   
                                    <select id="membershipType" required class="blockInput form-control shopSelect">
                                        <option value="" selected></option>';
                foreach ($this->company->getMembershipTypes() as $member) {
                        $htmlString .= '<option value="' . $member->__get("id") . '">' . $member->__get("Type") . '</option>';
                }
                    $htmlString .= '</select>
                                    <label class="shopLabel form-control-placeholder" for="membershipType" >'.lang('membership_type_single').'</label>
                                </div>';
        }
            $htmlString .= '</div>
                            <div class="itemBlockDiv padding-bottom-row">
                                <div style="height: 63px;">
                                    <div class="form-group itemBlockDiv-row">
                                        <input id="shopItemPrice" required type="number" class="blockInput form-control" style="width: 70%"/>
                                        <label class="shopLabel form-control-placeholder" for="shopItemPrice">'.lang('price').'</label>
                                        <label class="CheckboxLabel">
                                            <input type="checkbox" class="shopCheckbox" id="taxInclude" name="taxInclude" checked="checked"/>
                                            <span class="shopCheckmark"></span>
                                        </label>
                                        <label class="taxIncludeLabel" for="taxInclude">'.lang('include_vat').'</label>
                                    </div>
                                </div>';
        if(count($this->company->getBrands()) > 0){

                $htmlString .= '<div class="form-group">  
                                <select id="shopCmpBranch" required class="blockInput shopSelect form-control">
                                    <option value="" selected></option>
                                    <option value="-1">'.lang('all_branch').'</option>';
            foreach ($this->company->getBrands() as $brand) {
                    $htmlString .= '<option value="'.$brand->__get("id") .'">'. $brand->__get("BrandName") .'</option>';
            }
                $htmlString .= '</select>
                                <label class="shopLabel form-control-placeholder" for="shopCmpBranch" >'.lang('branch').'</label>
                                </div>';
        }
            $htmlString .= '</div>';
        $htmlString .= '</div>';
        return $htmlString;
    }

    /**
     * @param  $headline string
     * @return string
     */
    public function renderSecondBlock($headline){
        $htmlString = '<div class="itemBlockLabelDiv">
            <label class="ItemBlockLabel" for="SecondItemBlock">'. $headline .'</label>
            <div class="checkboxDiv">
                <label class="sliderCheckboxLabel">
                    <input type="checkbox" class="sliderCheckbox" id="time-Validation-checkbox">
                    <span class="checkbox-slider checkbox-round"></span>
                </label>';
                if (!$this->isMobile) {
                    $htmlString .= '<label class="sliderCheckboxName" for="time-Validation-checkbox">'.lang('subscription_limit_shop_render').'</label>';
                }
            $htmlString .= '</div>
        </div>
        <div class="SecondItemBlock ItemBlock ItemBlockCol" id="SecondItemBlock">';
            if($this->isMobile){
                $htmlString .= '<div class="itemBlockDiv itemBlockDiv-row">
                        <label class="shopLabel shopLabelBold" id="timePeriodLabel" for="timePeriod">'.lang('subscription_period_shop_render').'</label>
                        </div>';
            }
           $htmlString .= '<div class="itemBlockDiv itemBlockDiv-row">';
        if(!$this->isMobile) {
            $htmlString .= '<label class="shopLabel" id="timePeriodLabel" for="timePeriod">'.lang('subscription_period_shop_render').'</label>';
        }
        $htmlString .= '<input id="timePeriod" type="number" class="blockInput" />
                <select class="blockInput" id="timePeriodDdl">
                    <option value="days">'.lang('days').'</option>
                    <option value="weeks">'.lang('weeks').'</option>
                    <option value="months">'.lang('months').'</option>
                    <option value="years">'.lang('years').'</option>
                </select>
            </div>';
                if(!$this->isMobile) {
                    $htmlString .= '<div class="itemBlockDiv itemBlockDiv-row no-padding-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="renew-membership" name="renew-membership" value="1" checked="checked"/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="renewLabel" for="renew-membership">'.lang('send_notification_shop_render').'</label>
                    </div>
                    <div class="itemBlockDiv itemBlockDiv-row no-padding-row padding-bottom-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="timePeriodRenewCheck" name="timePeriodRenewCheck" value="1" checked="checked"/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="renewLabel" for="timePeriodRenewCheck">'.lang('send_notification_before_shop_render').'</label>
                        <input id="timePeriodRenew" type="number" class="blockInput" />
                        <select class="blockInput" id="timePeriodRenewDdl">
                            <option value="days">'.lang('days').'</option>
                            <option value="weeks">'.lang('weeks').'</option>
                            <option value="months">'.lang('months').'</option>
                            <option value="years">'.lang('years').'</option>
                        </select>
                        <label class="renewLabel mr-2">'.lang('before_membership_ends').'</label>
                    </div>';
                }
                else{
                    $htmlString .= '<div class="itemBlockDiv itemBlockDiv-row no-padding-row">
                        <label class="shopLabelBold">'.lang('send_renew_notification_shop_render').'</label>
                        </div>
                        <div class="itemBlockDiv itemBlockDiv-row no-padding-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="renew-membership" name="renew-membership" value="1" checked="checked"/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="renewLabel" for="renew-membership">'.lang('period_end_shop_render').'</label>
                    </div>
                    <div class="itemBlockDiv itemBlockDiv-row no-padding-row padding-bottom-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="timePeriodRenewCheck" name="timePeriodRenewCheck" value="1" checked="checked"/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="renewLabel"">'.lang('before_membership_ends').'</label>
                    </div>
                    <div class="itemBlockDiv itemBlockDiv-row no-padding-row padding-bottom-row">
                        <input id="timePeriodRenew" type="number" class="blockInput" />
                        <select class="blockInput" id="timePeriodRenewDdl">
                            <option value="days">'.lang('days').'</option>
                            <option value="weeks">'.lang('weeks').'</option>
                            <option value="months">'.lang('months').'</option>
                            <option value="years">'.lang('years').'</option>
                        </select>
                    </div>';
                }
        $htmlString .= '</div>';
                return $htmlString;
    }

    /**
     * @param  $headline string
     * @return string
     */
    public function renderThirdBlock($headline){
        $htmlString = '<div class="itemBlockLabelDiv">
             <label class="ItemBlockLabel" for="thirdItemBlock">'. $headline .'</label>
             <div class="checkboxDiv">
                 <label class="sliderCheckboxLabel">
                     <input type="checkbox" class="sliderCheckbox" id="limits-checkbox">
                     <span class="checkbox-slider checkbox-round"></span>
                 </label>';
        if(!$this->isMobile) {
            $htmlString .= '<label class="sliderCheckboxName" for="limits-checkbox">'.lang('mark_no_limit_shop_render').'</label>';
        }
        $htmlString .= '</div>
         </div>
         <div class="thirdItemBlock ItemBlock ItemBlockCol">
             <div class="itemBlockDiv itemBlockDiv-row">
                 <i class="fad fa-info-circle"></i>
                 <label class="shopLabel label-black" for="selectClassType">'.lang('bookable_class_shop_render').'</label>
             </div>
             <div class="itemBlockDiv no-padding-row select2block" style="width: 100%">
                 <select class="js-example-basic-single select2multipleDesk text-right" name="ClassType[]" id="selectClassType" dir="rtl"   multiple="multiple" data-select2order="true" style="width: 100%;">';

                 foreach ($this->company->getClassTypes() as $class){
                     if($class->EventType == 0 ) {
                         $htmlString .= '<option value="' . $class->__get("id") . '">' . $class->__get("Type") . '</option>';
                     }
                  }
                $htmlString .= '</select>
<!--                <input type="hidden" id="CheckselectClassType" value="-1">-->
             </div>
             <div class="itemBlockDiv itemBlockDiv-row no-padding-row">
                 <i class="fad fa-info-circle"></i>
                 <label class="shopLabel label-black">'.lang('booking_limits_shop_render').'</label>
             </div>
             <div class="itemBlockDiv no-padding-row" style="width: 100%">
                 <button class="itemBlockBtn" id="itemLimitsBtn"> '.lang('add_limit_shop_render').'</button>
             </div>
             <div class="itemBlockDiv itemBlockDiv-row no-padding-row padding-bottom-row">
                 <label class="CheckboxLabel check-black">
                     <input type="checkbox" class="shopCheckbox" id="freeReg"/>
                     <span class="shopCheckmark"></span>
                 </label>
                 <label class="shopLabel">'.lang('book_by_avaiable_space_shop_render').'</label>               
             </div>
             <div class="itemBlockDiv itemBlockDiv-row no-padding-row freeSpaceReg" style="display: none">
                <label class="shopLabel timePeriodLabel" for="regNumber">'.lang('number_of_bookable_class').'</label>
                <input id="regNumber" type="number" class="blockInput" />
                <label class="shopLabel timePeriodLabel" for="regTime">'.lang('in_period_shop_render').'</label>
                <select class="blockInput" id="regTime">
                    <option value="1">'.lang('days').'</option>
                    <option value="2">'.lang('weeks').'</option>
                    <option value="3">'.lang('months').'</option>
                    <option value="4">'.lang('years').'</option>
                </select>
                <label class="shopLabel timePeriodLabel" for="regBeforeClass">'.lang('can_book_shop_render').'</label>
                <input id="regBeforeClass" type="number" class="blockInput" />
                <select class="blockInput" id="regBeforeClassTime">
                    <option value="2">'.lang('hours').'</option>
                    <option value="1">'.lang('minutes').'</option>
                </select>
                <label class="shopLabel timePeriodLabel" for="regBeforeClassTime">'.lang('before_class_shop_render').'</label>
            </div>
             
         </div>
         <button id="limitsBtn">'.lang('book_other_class_shop_render').'</button>';

        return $htmlString;
    }

    /**
     * @param $headline string
     * @param $page string
     * @return string
     */
    public function renderFourthBlock($headline, $page = "membership"){
        $dealType= "";
        $dealValue = "";
        if($page == "membership") {
            $dealType .= lang('recurring_billing_membership');
            $dealValue = 2;
        }
        else if($page == "items"){
            $dealType = lang('regular_one_time_shop_render');
            $dealValue = 1;
        }
        $htmlString =  '<div class="itemBlockLabelDiv">
             <label class="ItemBlockLabel" for="fourthItemBlock">'. $headline .'</label>
             <div class="checkboxDiv">
                 <label class="sliderCheckboxLabel">
                     <input type="checkbox" class="sliderCheckbox" id="application-checkbox" checked>
                     <span class="checkbox-slider checkbox-round"></span>
                 </label>';
        if(!$this->isMobile) {
            $htmlString .= '<label class="sliderCheckboxName" for="application-checkbox">'.lang('shop_web_purchase_shop_render').'</label>';
        }
        $htmlString .= '</div>
         </div>
         <div class="fourthItemBlock ItemBlock ItemBlockCol">';
        if(!$this->isMobile) {
            $htmlString .= '<div class="itemBlockDiv-row" >';
        }
        else{
            $htmlString .= '<div class="itemBlockDiv" >';
        }
        $htmlString .= '
                 <div class="itemBlockDiv">
                    <div class="form-group">
                         <input id="ApplicationName" required class="blockInput form-control"/>
                         <label class="shopLabel form-control-placeholder" for="ApplicationName" >'.lang('app_view_name_shop_render').'</label>                     
                     </div>
                 </div>';
                if($this->isMobile) {
                    $htmlString .= '<div class="itemBlockDiv-row" >';
                }
                $htmlString .= '<div class="itemBlockDiv">
                     <div class="midDiv">
                         <div class="form-group">
                             <input type="number" id="shopItemAppPrice" required class="blockInput form-control"/>
                             <label class="shopLabel form-control-placeholder" for="shopItemAppPrice">'.lang('price_in_app_shop_render').'</label>                             
                         </div>
                     </div>
                 </div>
                 <div class="itemBlockDiv">
                     <div class="midDiv">
                        <label class="shopLabel dealTypeMob" style="width: auto">'.lang('transaction_type_shop_render').'</label>  
                        <i class="fad fa-info-circle"></i>
                        <label class="shopLabel" id="dealType" data-id="'. $dealValue .'">'.$dealType.'</label>
                     </div>
                 </div>';
                if($this->isMobile) {
                    $htmlString .= '</div>';
                }
            $htmlString .= '</div>
             <div class="itemBlockDiv no-padding-row" style="width: 100%">
                 <div style="height: 56px;">
                     <div class="form-group">
                        <input id="shopItemAppDesc" required class="blockInput form-control" style="width: 100%"/>
                        <label class="shopLabel form-control-placeholder" for="shopItemAppDesc">'.lang('description_app_shop_render').'</label>                        
                     </div>
                 </div>
             </div>';
            if($page == "membership") {
                $htmlString .= '<div id="shopPayment">';
            }
            else if($page == "items"){
                $htmlString .= '<div id="shopPayment" style="display: block">';
            }
            $htmlString .= '<div class="itemBlockDiv  no-padding-row">
                     <div class="itemBlockDiv-row">
                        <label class="shopLabel PaymentNum" for="PaymentNum">'.lang('max_payments').'</label>
                        <input id="PaymentNum" type="number" value="1" class="blockInput" />
                    </div>
                     <div class="itemBlockDiv-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="paymentFrame" value="1" disabled/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="shopLabel" for="paymentFrame">'.lang('allow_transactions_shop_render').'</label>
                    </div>
                </div>
                <div class="itemBlockDiv  no-padding-row">
                     <div class="itemBlockDiv-row">
                        <label class="CheckboxLabel">
                            <input type="checkbox" class="shopCheckbox" id="credit-payment" value="1" disabled/>
                            <span class="shopCheckmark"></span>
                        </label>
                        <label class="shopLabel" for="credit-payment">'.lang('allow_credit_shop_render').'</label>
                    </div>
                </div>
            </div>
             <div class="itemBlockDiv" style="width: 100%">
                 <label class="itemBlockBtn filesToUploadLabel" for="appPicture">'.lang('add_image_shop_render').'</label>
                 <label class="uploadFileName shopLabel" hidden></label>
                 <input hidden type="file" class="itemBlockBtn filesToUpload" id="appPicture" />
             </div>
             <div class="itemBlockDiv itemBlockDiv-row" style="width: 100%">
                 <i class="fad fa-info-circle"></i>
                 <label class="shopLabel">'.lang('purchase_limit_shop_render').'</label>
             </div>
             <div class="itemBlockDiv no-padding-row padding-bottom-row" style="width: 100%">
                 <button class="itemBlockBtn" id="itemLimitsAppBtn"> '.lang('add_limit_shop_render').'</button>
             </div>
         </div>';
         return $htmlString;
    }

    /**
     * @param $headline
     * @param $itemCategory
     * @return string
     */
    public function renderItemMainBlock($headline, $itemCategory){
        $htmlString = '<div class="itemBlockLabelDiv">
             <label class="ItemBlockLabel" for="fifthItemBlock">'. $headline .'</label>
             </div>
             <div class="itemMainBlock ItemBlock ItemBlockCol">
                 <div class="itemBlockDiv-row">
                     <div class="itemBlockDiv">
                        <div class="form-group">
                            <div class="midDiv">
                                <input id="additemName" required class="blockInput form-control"/>
                                <label class="shopLabel form-control-placeholder" for="additemName">'.lang('product_name').'</label>                               
                            </div>
                        </div>
                     </div>
                     <div class="itemBlockDiv">
                        <div style="height: 56px;">
                            <div class="form-group itemBlockDiv-row">
                                <input id="shopItemPrice" type="number" required class="blockInput form-control" style="width: 70%"/>
                                <label class="shopLabel form-control-placeholder" for="shopItemPrice">'.lang('price_shop_render').'</label>
                                <label class="CheckboxLabel">
                                    <input type="checkbox" class="shopCheckbox" id="taxInclude" name="taxInclude" value="1" checked="checked"/>
                                    <span class="shopCheckmark"></span>
                                </label>
                                <label class="taxIncludeLabel" for="taxInclude">'.lang('include_vat').'</label>
                            </div>
                        </div>
                     </div>
                 </div>
                 <div class="itemBlockDiv-row">
                     <div class="itemBlockDiv  no-padding-row">
                         <div class="form-group">
                             <div class="midDiv">
                                 <input id="addItemPrice" type="number" required class="blockInput form-control"/>
                                 <label class="shopLabel form-control-placeholder" for="addItemPrice">'.lang('cost_price_product').'</label>                               
                             </div>
                         </div>
                     </div>
                     <div class="itemBlockDiv no-padding-row">
                        <label class="shopLabel" for="departmentDdl">'.lang('class').'</label>
                        <select id="departmentDdl" required name="Categories[]" class="blockInput js-example-basic-single select2multipleDesk text-right itemsMultiSelect" multiple="multiple" data-select2order="true" style="width: 100%;">';
                            foreach ($itemCategory as $cat){
                                $htmlString .= '<option value="'. $cat->__get("id") .'">'. $cat->__get("Name") .'</option>';
                            }
        $htmlString .= '</select>
                     </div>
                 </div>
             </div>
             ';
        return $htmlString;
    }

    /**
     * @param $headline
     * @param $colors
     * @param $sizes
     * @return string
     */
    public function renderSixthBlock($headline,$colors,$sizes){
        $htmlString = '<div class="itemBlockLabelDiv">
             <label class="ItemBlockLabel" for="sixthItemBlock">'. $headline .'</label>
             </div>
             <div class="sixthItemBlock ItemBlock ItemBlockCol">
                 <div class="itemBlockDiv-row" >
                     <div class="itemBlockDiv">
                         <div class="midDiv">
                             <div class="form-group">
                                 <input id="itemBarcode" required class="blockInput form-control"/>
                                 <label class="shopLabel form-control-placeholder" for="itemBarcode">'.lang('bardcode_product').'</label>
                             </div>
                         </div>
                     </div>
                     <div class="itemBlockDiv">
                         <div class="form-group">
                            <input id="itemSku"  required class="blockInput form-control"/>
                            <label class="shopLabel form-control-placeholder" for="itemSku">'.lang('sku_product').'</label>
                        </div>
                     </div>
                 </div>
                 <div class="itemBlockDiv-row">
                     <div class="itemBlockDiv no-padding-row">
                         <div class="midDiv">
                             <div class="form-group">
                                 <label class="shopLabel multiSelectLabel" for="itemSize">'.lang('size_shop_render').'</label>
                                 <select id="itemSize" required name="Sizes[]" class="blockInput js-example-basic-single select2multipleDesk text-right itemsMultiSelect" multiple="multiple" data-select2order="true" style="width: 60%;">';
                                 foreach ($sizes as $size){
                                     $htmlString .= '<option value="'. $size->__get("id") .'">'. $size->__get("name") .'</option>';
                                 }
                 $htmlString .=' </select>
                                 
                             </div>
                         </div>
                     </div>
                     <div class="itemBlockDiv no-padding-row">
                         <div class="form-group">
                                <label class="shopLabel multiSelectLabel" for="itemColor">'.lang('color_shop_render').'</label>
                                <select id="itemColor" required name="Colors[]" class="blockInput js-example-basic-single select2multipleDesk text-right itemsMultiSelect" multiple="multiple" data-select2order="true" style="width: 60%;">';
                                foreach ($colors as $color){
                                    $htmlString .= '<option value="'. $color->__get("id") .'">'. $color->__get("name") .'</option>';
                                }
             $htmlString .='</select>
                        </div>
                     </div>
                 </div>
                 <div class="itemBlockDiv-row" >
                     <div class="itemBlockDiv">
                         <div class="midDiv">
                             <div class="form-group">
                                 <input id="itemStock" required class="blockInput form-control"/>
                                 <label class="shopLabel form-control-placeholder" for="itemStock">'.lang('inventory_shop_render').'</label>
                             </div>
                         </div>
                     </div>
                     <div class="itemBlockDiv">
                         <div class="form-group">
                            <input id="itemSupplier" required class="blockInput form-control"/>
                            <label class="shopLabel form-control-placeholder" for="itemSupplier">'.lang('supplier_shop_render').'</label>
                        </div>
                     </div>
                 </div>
              </div>';
             return $htmlString;
    }

    /**
     * @param $headline string
     * @return string
     */
    public function renderSeventhBlock($headline){
        $htmlString =  '<div class="itemBlockLabelDiv">
            <label class="ItemBlockLabel" for="SeventhItemBlock">'. $headline .'</label>
            </div>
            <div class="SeventhItemBlock ItemBlock ItemBlockCol">
                <div class="itemBlockDiv-row">
                    <div class="itemBlockDiv">
                        <div class="form-group">
                            <input id="linkHeadline" required class="blockInput form-control"/>
                            <label class="shopLabel form-control-placeholder" for="linkHeadline">'.lang('url_title_shop_render').'</label>                                 
                        </div>
                    </div>';
            if(count($this->company->getBrands()) > 0){
                $htmlString .= '<div class="itemBlockDiv">
                                    <div class="form-group">  
                                        <select id="shopCmpBranch" required class="blockInput shopSelect form-control">
                                            <option value="" selected></option>
                                            <option value="-1">'.lang('all_branch').'</option>';
                    foreach ($this->company->getBrands() as $brand) {
                        $htmlString .= '<option value="'.$brand->__get("id") .'">'. $brand->__get("BrandName") .'</option>';
                    }
                    $htmlString .= '</select>
                                        <label class="shopLabel form-control-placeholder" for="shopCmpBranch" >'.lang('branch').'</label>
                                </div>
                            </div>
                        </div>';
        }
            $htmlString .='<div class="itemBlockDiv" style="width: 100%">
                        <div class="form-group">
                            <input id="linkContent" required class="blockInput form-control"/>
                            <label class="shopLabel form-control-placeholder" for="linkContent">'.lang('add_description_url').'</label>                                 
                        </div>
                    </div>
                    <div class="itemBlockDiv  no-padding-row" style="width: 100%">
                        <label class="itemBlockBtn filesToUploadLabel" for="linkPicture">'.lang('add_image_shop_render').'</label>
                        <label class="uploadFileName shopLabel" hidden></label>
                        <input hidden type="file" class="itemBlockBtn filesToUpload" id="linkPicture" />
                    </div>
                    <div class="itemBlockDiv" style="width: 100%">
                        <div class="form-group">
                            <input id="linkRedirect" required class="blockInput form-control"/>
                            <label class="shopLabel form-control-placeholder" for="linkRedirect">'.lang('refer_link_shop_render').'</label>                                 
                        </div>
                    </div>';
        $htmlString .= '</div>';
        return $htmlString;
    }

    /**
     * @param $headline string
     * @return string
     */
    public function renderPayItemBlock($headline){
        $htmlString =  '<div class="itemBlockLabelDiv">
                            <label class="ItemBlockLabel" for="EighthItemBlock">'. $headline .'</label>
                        </div>';
        $htmlString .= '
                        <div class="EighthItemBlock ItemBlock ItemBlockCol">
                            <div class="itemBlockDiv"  style="width: 100%">
                                <div class="form-group">  
                                    <select id="saleType" required class="blockInput shopSelect form-control">
                                        <option value="1" selected>'.lang('club_membership_shop_render').'</option>
                                        <option value="2">'.lang('items').'</option>
                                    </select>
                                    <label class="shopLabel form-control-placeholder" for="saleType" >'.lang('choose_type').'</label>
                                </div>
                            </div>
                            <div class="no-padding-row membershipSelection" style="width: 100%">
                                <div class="itemBlockDiv-row">
                                   <div class="itemBlockDiv">
                                      <div class="form-group">
                                            <select id="membershipNameDDl" required class="blockInput form-control shopSelect">
                                                <option value="" selected></option>';
                                    foreach ($this->company->setGetItems() as $item) {
                                        if($item->Department == 1 || $item->Department == 2 || $item->Department == 3) {
                                            $htmlString .= '<option value="' . $item->id . '" data-price="' . $item->ItemPrice . '" data-dep="' . $item->Department . '">' . $item->ItemName . '</option>';
                                        }
                                    }
                            $htmlString .= '</select>
                                            <label class="shopLabel form-control-placeholder" for="membershipNameDDl" >'.lang('membership_type_single').'</label>
                                      </div>
                                   </div>
                                   <div class="itemBlockDiv itemBlockDiv-row" style="width: 50%">
                                      <div class="form-group">
                                          <div class="midDiv">
                                              <input id="membershipPrice" type=number value="" required class="blockInput form-control"/>
                                              <label class="shopLabel form-control-placeholder" for="membershipPrice">'.lang('price').'</label>                               
                                          </div>
                                      </div>
                                      <div class="itemBlockDiv no-padding-row" style="padding-left: 0">
                                          <div class="midDiv">
                                              <label class="shopLabel" style="width: auto">'.lang('transaction_type_shop_render').'</label>  
                                              <i class="fad fa-info-circle"></i>
                                              <label class="shopLabel" style="width: 100%" >'.lang('transaction_regular_shop_render').'</label>
                                         </div>
                                     </div>
                                  </div>
                              </div>
                            </div>
                            <div class="no-padding-row padding-bottom-row itemsSelection" style="width: 100%">
                               <div  class="itemBlockDiv-row">
                                   <div class="itemBlockDiv no-padding-row" style="width: 25%">
                                      <label class="shopLabel" >'.lang('item_name_shop_render').'</label>
                                   </div>
                                   <div class="itemBlockDiv no-padding-row" style="width: 25%">
                                      <label class="shopLabel" >'.lang('price').'</label>
                                   </div>
                               </div>
                               <div class="itemBlockDiv no-padding-row"  style="width: 100%">
                                   <button class="itemBlockBtn" id="addItemBtn"> '.lang('add_item_shop_render').'</button>
                               </div>
                            </div>
                        </div>';
        return $htmlString;
    }

    /**
     * @param $headline string
     * @return string
     */
    public function renderAddOnBlock($headline){
        $htmlString =  '<div class="itemBlockLabelDiv">
                            <label class="ItemBlockLabel" for="renderAddOnBlock">'. $headline .'</label>
                        </div>';
        $htmlString .= '<div class="renderAddOnBlock ItemBlock ItemBlockCol">
                            <div class="itemBlockDiv">
                                <label class="shopLabel" >'.lang('attach_forms_shop_render').'</label>
                            </div>
                            <div class="itemBlockDiv no-padding-row"  style="width: 100%">
                                <label class="itemBlockBtn filesToUploadLabel" for="addDocBtn">'.lang('add_a_form_shop_render').'</label>
                                <label class="uploadFileName shopLabel" hidden></label>
                                <input hidden type="file" class="itemBlockBtn filesToUpload" id="addDocBtn" />
                            </div>
                            <div class="itemBlockDiv  no-padding-row">
                                <label class="shopLabel" >'.lang('add_to_payment_shop_render').'</label>
                            </div>
                            <div class="itemBlockDiv itemBlockDiv-row">
                                <label class="CheckboxLabel">
                                    <input type="checkbox" class="shopCheckbox" id="regFeeCheck" name="regFeeCheck" value="1" checked="checked"/>
                                    <span class="shopCheckmark"></span>
                                </label>
                                <label class="CheckLabel" for="regFeeCheck">'.lang('registration_fee_shop_render').'</label>
                                <label class="CheckboxLabel">
                                    <input type="checkbox" class="shopCheckbox" id="insuranceCheck" name="insuranceCheck" value="1" checked="checked"/>
                                    <span class="shopCheckmark"></span>
                                </label>
                                <label class="CheckLabel" for="insuranceCheck">'.lang('insurance_shop_render').'</label>
                            </div>
                            <div class="itemBlockDiv itemBlockDiv-row no-padding-row">
                                 <label class="CheckboxLabel">
                                    <input type="checkbox" class="shopCheckbox" id="AppEndCheck" name="AppEndCheck"/>
                                    <span class="shopCheckmark"></span>
                                </label>
                                <label class="CheckLabel" for="AppEndCheck">'.lang('on_end_send_login').'</label>
                            </div>
                        </div>';
        return $htmlString;
    }

    /**
     * @param string $headline
     * @param string $page
     * @return string
     */
    public function renderCalcMembershipBlock($headline, $page = "payment"){
        $htmlString =  '<div class="itemBlockLabelDiv CalcMembershipBlockDiv">
                            <label class="ItemBlockLabel" for="CalcMembershipBlock">'. $headline .'</label>
                        </div>';
        $htmlString .= '<div class="CalcMembershipBlock ItemBlock ItemBlockCol">
                            <div class="itemBlockDiv calcRadioDiv">
                                <label>
                                    <input type="radio" class="rdo-input" checked name="memCalc" value="1">
                                    <span class="rdo"></span><span>'.lang('normal_start_date_shop_render').'</span>
                                </label>
                                <div class="itemBlockDiv no-padding-row" id="regularMemCalc">
                                    <label class="regularMemCalcLabel">
                                        <input type="radio" class="rdo-input" checked name="memCalcRegular" value="1">
                                        <span class="rdo"></span><span>'.lang('immediate_start_date_shop_render').'</span>
                                    </label>
                                    <label class="regularMemCalcLabel">
                                        <input type="radio" class="rdo-input" name="memCalcRegular" value="2">
                                        <span class="rdo"></span><span>'.lang('according_first_lesson_shop_render').'</span>
                                    </label>
                                    <label class="regularMemCalcLabel">
                                        <input type="radio" class="rdo-input" name="memCalcRegular" value="3">
                                        <span class="rdo"></span><span>'.lang('by_end_subsciption_shop_render').'</span>
                                    </label>  
                                </div>';
        if($page == "payment") {
            $htmlString .= '<label>
                                    <input type="radio" class="rdo-input" name="memCalc" value="2">
                                    <span class="rdo"></span><span>'.lang('set_date_shop_render').'</span>
                                </label>
                                <div class="itemBlockDiv no-padding-row" id="definedMemCalc">
                                    <div class="form-group">
                                        <input placeholder="" id="startDateMem" required class="blockInput form-control dateInput" type="date">
                                        <label for="startDateMem" class="shopLabel form-control-placeholder">'.lang('subscription_start_shop_render').'</label>
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="" id="endDateMem" required class="blockInput form-control dateInput" type="date">
                                        <label for="endDateMem" class="shopLabel form-control-placeholder">'.lang('subscription_end_date_shop_render').'</label>
                                    </div>
                                    <div class="itemBlockDiv-row no-padding-row">
                                         <label class="CheckboxLabel">
                                            <input type="checkbox" class="shopCheckbox" id="latePurchase" name="latePurchase"/>
                                            <span class="shopCheckmark"></span>
                                        </label>
                                        <label class="CheckLabel" for="latePurchase">'.lang('allow_late_purchase_shop_render').'</label>
                                    </div>
                                    <div class="itemBlockDiv untilDiv  no-padding-row">
                                        <div class="form-group">
                                            <input placeholder="" id="untilDateMem" required class="blockInput form-control dateInput" type="date">
                                            <label for="untilDateMem" class="shopLabel form-control-placeholder">'.lang('until_date').'</label>
                                        </div>
                                        <div class="itemBlockDiv-row no-padding-row">
                                            <label class="CheckboxLabel">
                                                <input type="checkbox" class="shopCheckbox" id="priceCut" name="priceCut"/>
                                                <span class="shopCheckmark"></span>
                                            </label>
                                            <label class="CheckLabel" for="priceCut">'.lang('relative_offset_shop_render').'</label>
                                            <div class="form-group">  
                                                <select id="priceCutDDl" required class="blockInput shopSelect form-control no-padding-row">
                                                    <option value="1" selected>'.lang('days').'</option>
                                                    <option value="2">'.lang('classes').'</option>
                                                </select>
                                            </div>
                                            <label class="CheckLabel" style="padding-right: 5px"> '.lang('remaining_month_shop_render').'</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="itemBlockDiv calcTicketDiv">
                                <label class="shopLabel labelBlock" >'.lang('normal_start_date_shop_render').'</label>
                                <div class="itemBlockDiv no-padding-row no-padding-right">
                                    <label>
                                        <input type="radio" class="rdo-input" checked name="ticketRadio" value="1">
                                        <span class="rdo"></span><span>'.lang('immediate_start_date_shop_render').'</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="rdo-input" name="ticketRadio" value="2">
                                        <span class="rdo"></span><span>'.lang('every_one_month_shop_render').'</span>
                                    </label>
                                </div>
                                <div class="itemBlockDiv-row paddingTop">
                                    <label class="CheckboxLabel">
                                        <input type="checkbox" class="shopCheckbox" id="relativeTicketPrice" name="relativeTicketPrice"/>
                                        <span class="shopCheckmark"></span>
                                    </label>                              
                                    <label class="CheckLabel" for="relativeTicketPrice"> '.lang('allow_first_payment_relative').'</label>                                 
                                </div>
                                <label class="CheckLabel" style="padding-right: 35px;">'.lang('relative_cost_shop_render').'</label>';
        }
            $htmlString .='</div>
                        </div>';

        return $htmlString;

    }

    /**
     * @param $headline
     * @return string
     */
    public function renderPayClassesBlock($headline){
        $htmlString =  '<div class="itemBlockLabelDiv PayClassesBlockDiv">
                            <label class="ItemBlockLabel" for="PayClassesBlock">'. $headline .'</label>
                        </div>';
        $htmlString .= '<div class="PayClassesBlock ItemBlock ItemBlockCol">
                               <div class="itemBlockDiv itemBlockDiv-row">
                                    <select id="membershipNameDDl" required class="blockInput shopSelect">
                                        <option value="-1" selected>בחר שיעור</option>';
                            foreach ($this->company->getClassTypes() as $class) {
                                if($class->EventType == 0 ) {
                                    $htmlString .= '<option value="' . $class->__get("id") . '">' . $class->__get("Type") . '</option>';
                                }
                            }
                    $htmlString .= '</select>
                                    <input id="ClassDay" value="חמישי" required class="blockInput blockInputClasses"/>
                                    <input id="ClassHour" value="18:00" required class="blockInput blockInputClasses"/>
                               </div>                                              
                               <div class="itemBlockDiv no-padding-row"  style="width: 100%">
                                   <button class="itemBlockBtn" id="addClassBtn"> '.lang('add_class_shop_render').'</button>
                               </div>
                               <div class="itemBlockDiv no-padding-row">
                                    <label class="shopLabel labelBlock" >'.lang('limit_note_shop_render').'</label>
                                    <label>
                                        <input type="radio" class="rdo-input" name="LimitedClass" checked value="1">
                                        <span class="rdo"></span><span>'.lang('dont_allow_book_shop_render').'</span>
                                    </label>
                                    <label>
                                        <input type="radio" class="rdo-input" name="LimitedClass" value="2">
                                        <span class="rdo"></span><span>'.lang('book_register_shop_render').'</span>
                                    </label>  
                               </div>
                        </div>';
        return $htmlString;
    }

}