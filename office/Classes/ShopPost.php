<?php

require_once "Company.php";
require_once "PaymentPage.php";
require_once "ItemCategory.php";
require_once "ItemRoles.php";
require_once "ItemLimit.php";
require_once "ItemDetails.php";
require_once "ClassCalendar.php";
require_once "Utils.php";
require_once "Item.php";
require_once "CompanyProductSettings.php";
require_once "MembershipType.php";
require_once "EncryptDecrypt.php";


class ShopPost extends Utils
{
    /**
     * @var Company $company
     */
    private $company;

    /**
     * @var $user
     */
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->company = Company::getInstance();
    }


    //+++++++++++++++++1+++++++++++++++++++++++++
    /**
     * @return mixed
     */
    public function getItemAndMembership()
    {
        $OpenTables = DB::table('items')->leftJoin('membership_type', function ($join) {
            $join->on('membership_type.id', '=', 'items.MemberShip')->on('membership_type.CompanyNum', '=', 'items.CompanyNum');
        })->join('appsettings', 'appsettings.CompanyNum', '=', 'items.CompanyNum')
            ->select('items.*', 'membership_type.Type as mType', 'appsettings.MembershipType')
            ->where('items.Department', "!=", 4)->where('items.CompanyNum', '=', $this->company->__get("CompanyNum"))->where("items.Status", "=", 0)->where('items.isPaymentForSingleClass',"=","0")
            ->orderBy('items.Department', 'ASC')->orderBy('items.Disabled', 'ASC')->get();
        return $OpenTables;
    }
    //+++++++++++++++++2+++++++++++++++++++++++++
    /**
     * @return mixed
     */
    public function getPhysicalItems()
    {
        $OpenTables = DB::table('items')->leftjoin('item_cat', 'item_cat.id', '=', 'items.ItemCat')
            ->select('items.*', "item_cat.Name as CategoryName")
            ->where('items.Department', "=", 4)->where('items.CompanyNum', '=', $this->company->__get("CompanyNum"))->where("items.Status", "=", 0)
            ->orderBy('item_cat.order', 'ASC')->orderBy('items.Disabled', 'ASC')->orderBy('items.order', 'ASC')
            ->get();
        return $OpenTables;
    }
    //+++++++++++++++++3+++++++++++++++++++++++++
    public function getPayment()
    {
        $dataTable = DB::table("payment_pages")
            ->leftJoin('docs', 'docs.pageId', '=', 'payment_pages.id')
            ->select("payment_pages.*", DB::raw('Count(docs.PageId) as CountSales'))
            ->where("payment_pages.CompanyNum", "=", $this->company->__get("CompanyNum"))->where("payment_pages.isNew","=",1)
            ->where("payment_pages.Status", "=", "0")->where("payment_pages.displayTable", "=", "1")
            ->groupBy('payment_pages.id')
            ->get();
        return $dataTable;
    }

    //+++++++++++++++++1.5+++++++++++++++++++++++++
    public function dtMembership($tableData)
    {
        $data = array(
            "data" => array()
        );
        $department = 1;
        foreach ($tableData as $key => $item) {
            $paymentObj = new PaymentPage();
            $payment = $paymentObj->getFirstPaymentPagesOfItem($item->id);
            $payType = $this->getPaymentType(0,1);
            if($item->Payment != null){
                $payType = $this->getPaymentType(0,$item->Payment);
            }
            else if ($payment->__get("id") != null) {
                $payType = $this->getPaymentType($payment->__get("PaymentType"), $item->Payment);
            }
            if ($item->Display == 1) {
                $statusCheck = "checked";
            } else {
                $statusCheck = "";
            }
            $Valid_Type = $this->getValidType($item->Vaild_Type);
            $Valid = $this->getValidParam($item->Vaild, $Valid_Type, $item->Department);

            $ItemTile = str_replace('"', "``", $item->ItemName);
            $ItemTile = str_replace("'", "`", $ItemTile);
            //todo  add vaild check;
            if(isset($item->ClubMembershipsId)) {
                $ItemTile .= ' -(new-' . $item->ClubMembershipsId . ')';
            }
            $brandString = $this->getBranch($item->Brands);
            $arrData = array();
            if ($key == 0 || $department != $item->Department) {
                $department = $item->Department;
                $arrData[0] = "<div class='shopRowSeparator shopRow' data-folder-id='" . $department . "'>" . $this->getDepartment($department) . "</div>";
                $arrData[1] = "";
                $arrData[2] = "";
                $arrData[3] = "";
                $arrData[4] = "";
                $arrData[5] = "";
                $arrData[6] = "";
                $arrData[7] = "";
                $arrData[8] = "<div style='text-align: center;'><i class='folderIcon fas fa-chevron-down'></i>";
                array_push($data["data"], $arrData);
            }
            $arrData[0] = '<div class="shopRowItem shopRow" data-folder-id="' . $department . '" > ' . htmlentities(addslashes($ItemTile)) . '</div>';
            $arrData[1] = $item->mType;
            $arrData[2] = ($item->Vaild != 0) ? $Valid : "--";
            $arrData[3] = ($item->BalanceClass != 0) ? $item->BalanceClass : "--";
            /*$arrData[4] =  '<div class="checkboxDiv checkboxDiv-table">
                                <label class="sliderCheckboxLabel sliderCheckboxLabel-dt">
                                    <input data-id ="' . $item->id . '" type="checkbox" ' . $statusCheck . ' class="sliderCheckbox sliderCheckbox-dt" data-id="' . $item->id . '"/>
                                    <span class="checkbox-slider checkbox-round checkbox-round-dt"></span>
                                </label>
                           </div>';*/
            $arrData[4] =  '<div class="custom-control custom-switch">
                                <input data-id ="' . $item->id . '" type="checkbox" ' . $statusCheck . ' class="custom-control-input sliderCheckbox" id="2customSwitch' . $item->id . '">
                                <label class="custom-control-label" for="2customSwitch' . $item->id . '"></label>
                            </div>';
            $arrData[5] = $brandString;
            $arrData[6] = "₪" . $item->ItemPrice;
            //            $arrData[7] = "₪" . $item->ItemPrice;
            $arrData[7] = ($payType != "") ? $payType : "--";
            $arrData[8] = '<div class="shop-dots-btn" data-id="' . $item->id . '">
                            <i class="fal fa-ellipsis-v"></i>
                            <div class="rowBox" id="rowBox_' . $item->id . '">
                                <div id="rowBoxEdit" class="rowBox-item">'.lang('edit').'</div>';
            //todo  add vaild check;
            if(isset($item->ClubMembershipsId)) {
                $arrData[8] .= '<div onclick="createClubMemberships.showEditClubMembershipsData(this)" data-id="'. $item->ClubMembershipsId .'" id="rowBoxEditNew" class="rowBox-item">'.lang('edit') . ' חדש! '. '</div>';
            }

            if ($item->Disabled == 0) {
                $arrData[8] .= '<div id="rowBoxPause" class="rowBox-item">'.lang('pause_store').'</div>';
                $arrData[8] .= '<div id="rowBoxStart" style="display: none" class="rowBox-item">'.lang('activate_store').'</div>';
            } else {
                $arrData[8] .= '<div id="rowBoxStart" class="rowBox-item disabledRow">'.lang('activate_store').'</div>';
                $arrData[8] .= '<div id="rowBoxPause" style="display: none" class="rowBox-item">'.lang('pause_store').'</div>';
            }
            if(Auth::user()->role_id == 1) {
                $arrData[8] .= '<div id="rowBoxDel" class="rowBox-item">'.lang('delete_store').'</div>';
            }
            $arrData[8] .= '</div>
                       </div>';


            array_push($data["data"], $arrData);
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    //+++++++++++++++++2.5+++++++++++++++++++++++++
    public function dtItems($items)
    {
        $data = array(
            "data" => array()
        );
        $catId = -1;
        foreach ($items as $key => $item) {
            $details = new ItemDetails();
            $itemDetails = $details->getItemDetails($item->id);
            if ($item->Display == 1) {
                $statusCheck = "checked";
            } else {
                $statusCheck = "";
            }
            $inv = "--";
            if (!empty($itemDetails) && is_array($itemDetails)) {
                $invPopup = '<div data-id="' . $item->id . '">';
                $inv = array_reduce($itemDetails, function ($carry, $item) use ($invPopup) {
                    if ($item->__get("inventory")) {
                        $inventory = $item->__get("inventory") - $item->__get("used");
                        if ($inventory < 0) {
                            $inventory = 0;
                        }

                        return $carry + $inventory;
                    }
                    return $carry;
                }, null);

                $invPopup .= '</div>';

                if ($inv === null) {
                    $inv = "--". $invPopup;
                } else {
                    $inv .= ' ' . $invPopup;
                }
            } else {
                $invPopup = '<div data-id="' . $item->id . '">';
                $invPopup .= '</div>';
                $inv = "--". $invPopup;
            }
            $ItemTile = str_replace('"', "``", $item->ItemName);
            $ItemTile = str_replace("'", "`", $ItemTile);
            $brandString = $this->getBranch($item->Brands);
            if ($key == 0 || $catId != $item->ItemCat) {
                $catId = $item->ItemCat;
                $catName = $item->CategoryName;
                if ($item->ItemCat == 0) {
                    $catName = lang('without_cat_app');
                }
                $arrData[0] = "<div class='shopRowSeparator shopRow' data-folder-id='" . $catId . "'>" . $catName . "</div>";
                $arrData[1] = "";
                $arrData[2] = "";
                $arrData[3] = "";
                $arrData[4] = "";
                $arrData[5] = "<div style='text-align: center;'><i class='folderIcon fas fa-chevron-down'></i></div>";
                array_push($data["data"], $arrData);
            }
            $arrData[0] = '<div class="shopRow shopRowItem" data-folder-id="' . $catId . '" >' . $ItemTile . '</div>';
            $arrData[1] = $inv;
            /*$arrData[2] =  '<div class="checkboxDiv checkboxDiv-table">
                                <label class="sliderCheckboxLabel sliderCheckboxLabel-dt">
                                    <input data-id ="' . $item->id . '" type="checkbox" ' . $statusCheck . ' class="sliderCheckbox sliderCheckbox-dt" data-id="' . $item->id . '"/>
                                    <span class="checkbox-slider checkbox-round checkbox-round-dt"></span>
                                </label>
                           </div>';*/
            $arrData[2] =  '<div class="custom-control custom-switch">
                                <input data-id ="' . $item->id . '" type="checkbox" ' . $statusCheck . ' class="custom-control-input sliderCheckbox" id="1customSwitch' . $item->id . '">
                                <label class="custom-control-label" for="1customSwitch' . $item->id . '"></label>
                            </div>';
            $arrData[3] = $brandString;
            $arrData[4] = "₪" . $item->ItemPrice;
            $arrData[5] = '<div class="shop-dots-btn" data-id="' . $item->id . '">
                            <i class="fal fa-ellipsis-v"></i>
                            <div class="rowBox" id="rowBox_' . $item->id . '">
                                <div id="rowBoxEdit" class="rowBox-item rowBoxEdit">'.lang('edit').'</div>';
            if ($item->Disabled == 0) {
                $arrData[5] .= '<div id="rowBoxPause" class="rowBox-item">'.lang('pause_store').'</div>';
                $arrData[5] .= '<div id="rowBoxStart" style="display: none" class="rowBox-item">'.lang('activate_store').'</div>';
            } else {
                $arrData[5] .= '<div id="rowBoxStart"  class="rowBox-item">'.lang('activate_store').'</div>';
                $arrData[5] .= '<div id="rowBoxPause" style="display: none" class="rowBox-item disabledRow">'.lang('pause_store').'</div>';
            }

            $arrData[5] .= '<div class="rowBox-item editInventoryItem">'.lang('inventory_management').'</div>';
            if(Auth::user()->role_id == 1) {
                $arrData[5] .= '<div id="rowBoxDel" class="rowBox-item">'.lang('delete_store').'</div>';
            }
            $arrData[5] .= '</div>
                       </div>';
            array_push($data["data"], $arrData);
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    //+++++++++++++++++3.5+++++++++++++++++++++++++
    /**
     * @param $tableData stdClass[]
     * @return string
     */
    public function dtPayment($tableData)
    {
        $encryptDecrypt = new EncryptDecrypt();

        $data = array(
            "data" => array()
        );
        $firstRow = true;
        foreach ($tableData as $dt) {
            if ($dt->Disabled == 0) {
                $statusCheck = "checked";
            } else {
                $statusCheck = "";
            }
            if ($dt->CountSales == '0' || $dt->Visit == '0') {
                $TotalFix = '0';
            } else {
                $TotalFix = $dt->CountSales / $dt->Visit * 100;
            }

            if ($dt->PaymentType == 0) {
                $payment = "הוראת קבע";
            } else if ($dt->PaymentType == 1) {
                $payment = "חשבונית / קבלה";
            } else if ($dt->PaymentType == 2) {
                $payment = "אישור ללא חיוב";
            } else if ($dt->PaymentType == 3) {
                $payment = "חשבונית / קבלה";
            } else {
                $payment = "";
            }
            $payment = $this->getPaymentType($dt->PaymentType);
            $dataArr = array();
            $dataArr[0] = htmlentities(addslashes($dt->Title));
            /*$dataArr[1] = '<div class="checkboxDiv checkboxDiv-table">
                                <label class="sliderCheckboxLabel sliderCheckboxLabel-dt">
                                    <input data-id ="' . $dt->id . '" type="checkbox" ' . $statusCheck . ' class="sliderCheckbox sliderCheckbox-dt" id="insStatus_' . $dt->id . '"/>
                                    <span class="checkbox-slider checkbox-round checkbox-round-dt"></span>
                                </label>
                           </div>';*/
            $dataArr[1] = '<div class="custom-control custom-switch">
                                <input data-id ="' . $dt->id . '" type="checkbox" ' . $statusCheck . ' class="custom-control-input sliderCheckbox" id="3customSwitch' . $dt->id . '">
                                <label class="custom-control-label" for="3customSwitch' . $dt->id . '"></label>
                            </div>';
            $dataArr[2] = htmlentities(addslashes($payment));
            $dataArr[3] = htmlentities(addslashes($dt->Visit));
            $dataArr[4] = htmlentities(addslashes($dt->CountSales));
            $dataArr[5] = number_format($TotalFix, 0) . "%";
            $dataArr[6] = '<i data-link="'.get_letts_domain().'/payment/'.$encryptDecrypt->encryption($dt->id).'" class="fal fa-paste payment-icon copyToClipboard"></i>';
            $dataArr[7] = htmlentities(addslashes("₪" . $dt->Amount));
            $dataArr[8] = '<div class="shop-dots-btn" data-id="' . $dt->id . '">
                           <i class="fal fa-ellipsis-v"></i>
                           <div class="rowBox';
            if ($firstRow) {
                $dataArr[8] .= '1';
                $firstRow = false;
            }
            $dataArr[8] .= '" id="rowBox_' . $dt->id . '">
                               <div id="rowBoxEdit" class="rowBox-item rowBoxEdit">'.lang('edit').'</div>';
            if ($dt->Disabled == 0) {
                $dataArr[8] .= '<div id="rowBoxPause" class="rowBox-item">'.lang('pause_store').'</div>';
                $dataArr[8] .= '<div id="rowBoxStart" style="display: none" class="rowBox-item">'.lang('activate_store').'</div>';
            } else {
                $dataArr[8] .= '<div id="rowBoxStart"  class="rowBox-item">'.lang('activate_store').'</div>';
                $dataArr[8] .= '<div id="rowBoxPause" style="display: none" class="rowBox-item disabledRow">'.lang('pause_store').'</div>';
            }
            if(Auth::user()->role_id == 1) {
                $dataArr[8] .= '<div id="rowBoxDel" class="rowBox-item">'.lang('delete_store').'</div>';
            }
            $dataArr[8] .= '</div>
                      </div>';

            array_push($data["data"], $dataArr);
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function newMembership($data)
    {
        $vat = 17;
        $hasTax = isset($data["hasTax"]) && $data["hasTax"] == "true" ? true : false; 
        $vatPrice = $this->calcVatItemPrice((int) $data["price"], $vat, $hasTax);
        $userId = $this->user->id;

        //Boostapp logic
        if (!empty($data["type"])) {
           switch ($data["type"]) {
               case '4':
                   $data["type"] = '2';
                   $data['priceOptions'] = "1";
                   break;
               case '5':
                   if ($data["numOfEntries"] > 5) {
                       echo "error";
                       return;
                   }
                   $data["type"] = '3';
                   $data['priceOptions'] = "1";
                   break;
            }
        }
        if($data['isMembershipTypeNew'] == "1"){
            $data["membershipName"] = $data["membershipType"];
            $data["membershipType"] = $this->createMembershipType($data["membershipType"]);
    
        }
        $valid = isset($data["membershipLength"]) ? (int) $data["membershipLength"] : 0;
        $validType = $data["membershipUnits"] ?? 0;
        if(isset($data['priceOptions']) && $data['priceOptions'] == 2) {
            $valid = 1;
            $validType = 3;
        }
        $name = trim(str_replace("\\","/",$data["name"]));
        $dataToInsert =  array(
            "CompanyNum" => $this->company->__get("CompanyNum"),
            "Department" => $data["type"],
            "Brands" => (isset($data["branch"]) && $data["branch"] != "-1") ?  $data["branch"] : "BA999",
            "ItemName" => $name,
            "ItemPrice" => $data["price"],
            "ItemPriceVat" => $vatPrice,
            "UserId" => $userId,
            "Dates" => date('Y-m-d H:i:s'),
            "Vat" => $hasTax ? $vat : 0,
            "Vaild" => $valid,
            "Vaild_Type" => $validType,
            "MemberShip" => $data["membershipType"] ?? "BA999",
            "NotificationDays" => (isset($data["membershipAlertSettingsNumber"]) && $data["membershipAlertSettingsNumber"] > '0') ? $this->getNotificationDays($data) : 0,
            "notificationAtEnd" => (isset($data["alertOnEnd"]) &&  $data["alertOnEnd"] == 'true') ? 1 : 0,
            "ItemCat" => null,
            "CostPrice" => 0.00,
            "Display" => isset($data['allowBuyFromApp']) && (bool) $data['allowBuyFromApp'] ? 1 : 0,
            'Payment' => $data['priceOptions'] ?? null,
            "membershipStartCount" => $data['membershipStartSelect'] ?? 1,
            "membershipStartDate" => $data['lateRegisterDateInputMembership']??null,
            "membershipAllowLateReg" => $data['allowLateRegisterMembership'] == "true" ? "1" : "0",
            "membershipAllowRelativeDiscount" => $data['allowRelativeCheckboxMembership'] == "true" ? 1 : 0,
            "membershipRelativeDiscount" => $data['membershipRelativeDiscount'] ?? 0,
            "Content" => $data["membershipContent"] ?? null,
            "BalanceClass" => $data["numOfEntries"] ?? 0,
            "Image" => !empty($data["pageImgPath"]) ? $data["pageImgPath"] : null,
            "isNew" => 1,
            "LimitType" => 1
        );

        // if ($dataToInsert["NotificationDays"] == 0) {
        //     $dataToInsert["NotificationDays"] = 3;
        // }

        $companyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($this->company->__get("CompanyNum"));
        
        if (!$companyProductSettings->manageMemberships) {
            $defaultMembership = MembershipType::getDefaultMembership();
            $dataToInsert["MemberShip"] = $defaultMembership->id;
            $dataToInsert["oldMemberShip"] = $defaultMembership->id;
        }


        $ItemsId = DB::table('items')->insertGetId(
            $dataToInsert
        );

        if (isset($ItemsId) && $ItemsId != null && $ItemsId != "" && $ItemsId != 0) {

            if (isset($data['classes'])) {
                $itemRole = new ItemRoles();
                $itemRole->insertItemRole($data, $ItemsId);
            } else {
                $itemRole = new ItemRoles();
                $arr = [
                    "classes" => "all",
                    "maximum" => null,
                    "days" => null,
                    "hours" => null,
                    "extraHours" => null,
                    "register" => null,
                    "string" => "כל השיעורים.",
                    "id" => strtotime("now")
                ];
                $data["classes"][] = $arr;
                $itemRole->insertItemRole($data, $ItemsId);
            }

            if (isset($ItemsId) && $ItemsId != null && $ItemsId != "" && $ItemsId != 0 && isset($data['purchaseLimits'])) {
                $itemLimit = new ItemLimit();
                $itemLimit->createObjFromShop($data["purchaseLimits"], $userId, $this->company->__get("CompanyNum"), $ItemsId);
                $itemLimit->insertItemLimit();
            }
        }
        if(isset($data['allowBuyFromApp']) && (bool) $data['allowBuyFromApp'] == 1) {

            $linkDataToInsert = array(
                "CompanyNum" => $this->company->__get("CompanyNum"),
                "ItemId" => $ItemsId,
                "Title" => $data["name"],
                "TitleUrl" => $data["name"],
                "Content" => isset($data["membershipContent"]) ? $data["membershipContent"] : null,
                "Amount" => $data["price"],
                "ItemDepartment" => $data["type"],
                "UserId" => $userId,
                "Dates" => date('Y-m-d H:i:s'),
                "Brands" => (isset($data["branch"]) && $data["branch"] != "-1") ? $data["branch"] : "BA999",
                "displayTable" => 0,
                "PaymentType" => ($data['priceOptions'] == 2) ? 4 : 1,
                "TypeKeva" => ($data['priceOptions'] == 2) ? 0 : 1,
                "ItemVaildType" => (isset($data['membershipStartSelect']) && $data['membershipStartSelect'] == 1) ? 0 : 5,
                "ImageLink" => !empty($data["pageImgPath"]) ? $data["pageImgPath"] : null,
                "RandomNumber" => uniqid(),
                "MaxPaymentRegular" => ($data['priceOptions'] == 2) ? 0 : 12,
                "MaxPaymentToken" => 999,
                "TypePage" => ($data['priceOptions'] == 2) ? 1 : 0,
                "pageImg" => !empty($data["pageImgPath"]) ? str_replace('files/items/', '', $_POST['pageImgPath'])  : null,
                "isNew" => 1
            );
            $ItemsId = DB::table('payment_pages')->insertGetId(
                $linkDataToInsert
            );
        }


        $res = ["data" => true];
        if ($data["isMembershipTypeNew"] == 1) {
            $res["membershipType"] = '<option value="'.$data['membershipType'].'">'.$data["membershipName"].'</option>';
        }

        return $res;
    }

    private function getNotificationDays($data)
    {
        $num = $data['membershipAlertSettingsNumber'] ?? 1;
        $num = (int) $num;

        switch ($data['membershipAlertSettingsUnitType']) {

            case '1': {
                    //days
                    return $num;
                }

            case '2': {
                    //weeks
                    return $num * 7;
                }

            case '3': {
                    //months
                    return $num * 30;
                }
        }
    }

    public function newItem($data)
    {
        if($data["vat"] == "true" ||$data["vat"] == true ) {
            $vat = 17;
        }
        else{
            $vat = 0;
        }
        $vatPrice = $this->calcVatItemPrice($data["price"], $vat, true);
        $userId = $this->user->id;
        $companyNum = $this->company->__get("CompanyNum");
        if ($data['categoryIsNew'] == "true") {
            $categoryClass = new ItemCategory();
            $cat = $categoryClass->setCompanyCategory($companyNum, $data['category']);
        } else {
            $cat = $data['category'];
        }
        $name = str_replace("\\","/",$data["name"]);
        $ItemsId = DB::table('items')->insertGetId(
            array(
                "CompanyNum" => $companyNum,
                "Department" => 4,
                "ItemName" => $name,
                "UserId" => $userId,
                "ItemPrice" => $data["price"],
                "ItemPriceVat" => $vatPrice,
                "CostPrice" => isset($data["costPrice"]) ? $data["costPrice"] : 0.00,
                "Vat" => $vat,
                "MemberShip" => "BA999",
                "ItemCat" => $cat,
                "Dates" => date('Y-m-d H:i:s'),
                "Content" => isset($data["content"]) ? $data["content"] : '',
                "Brands" => (isset($data['branch']) && $data['branch'] != "-1") ? $data['branch'] : "BA999",
                "Display" => $data["allowBuyInApp"] == "true" ? "1" : "0",
                "Image" => !empty($data["img"]) ? $data["img"] : null,
                "isNew" => 1,
                "Payment" => 1
            )
        );
        if (isset($ItemsId) && $ItemsId != null && $ItemsId != "" && $ItemsId != 0 && isset($data['purchaseLimits'])) {
            $itemLimit = new ItemLimit();
            $itemLimit->createObjFromShop($data["purchaseLimits"], $userId, $companyNum, $ItemsId);
            $itemLimit->insertItemLimit();
        }
        if (isset($ItemsId) && $ItemsId != null && $ItemsId != "" && $ItemsId != 0) {
            $itemDetails = new ItemDetails();
            $iDetails = $itemDetails->insertItemDetails($data, $ItemsId);
            $detailsId = isset($iDetails["id"]) ? $iDetails["id"] : null;
        } else {
            return false;
        }
        if (isset($detailsId) && $detailsId != null && $detailsId != "") {

            if($data["allowBuyInApp"] == "true") {
                $linkDataToInsert = array(
                    "CompanyNum" => $companyNum,
                    "ItemId" => $ItemsId,
                    "Title" => $data["name"],
                    "TitleUrl" => $data["name"],
                    "Content" => isset($data["content"]) ? $data["content"] : '',
                    "Amount" => $data["price"],
                    "ItemDepartment" => 4,
                    "UserId" => $userId,
                    "Dates" => date('Y-m-d H:i:s'),
                    "Brands" => (isset($data['branch']) && $data['branch'] != "-1") ? $data['branch'] : "BA999",
                    "displayTable" => 0,
                    "itemValidType" => 0,
                    "PaymentType" => 0,
                    "ImageLink" => !empty($data["img"]) ? $data["img"] : null,
                    "RandomNumber" => uniqid(),
                    "MaxPaymentRegular" => 12,
                    // "MaxPaymentToken" => 999,
                    "TypePage" => 0,
                    "pageImg" => !empty($data["img"]) ? str_replace('files/items/', '', $_POST['img'])  : null,
                    "isNew" => 1
                );

                $ItemsId = DB::table('payment_pages')->insertGetId(
                    $linkDataToInsert
                );
            }

            $res = ["Status" => true];

            if (isset($iDetails)) {
                if (isset($iDetails["newSize"])) {
                    $res["newSize"] = $iDetails["newSize"];
                }
            }

            if ($data["categoryIsNew"] == "true") {
                $res["category"] = '<option value="'.$cat.'">'.$data["category"].'</option>';
            }

            return $res;
        }
        return false;
    }
    /**
     * @param $Type
     * @return string
     */
    private function getValidType($Type)
    {
        $Valid_Type = '';
        if ($Type == '1') {
            $Valid_Type = lang('days');
        } else if ($Type == '2') {
            $Valid_Type = lang('weeks');
        } else if ($Type == '3') {
            $Valid_Type = lang('months');
        }
        return $Valid_Type;
    }

    /**
     * @param $clientValid
     * @param $Valid_Type
     * @param int $department
     * @return string
     */
    private function getValidParam($clientValid, $Valid_Type, $department = 1)
    {
        $Valid = '';
        if ($department == '1') {
            $Valid = $clientValid . ' ' . $Valid_Type;
        } else if ($department == '2') {
            $Valid = $clientValid . ' ' . $Valid_Type;
        } else if ($department == '3') {
            $Valid = $clientValid . ' ' . $Valid_Type;
        } else if ($department == '4') {
            if ($clientValid == '0') {
                $Valid = '';
            } else {
                $Valid = $clientValid;
            }
        }
        return $Valid;
    }

    /**
     * @param $branches
     * @return string
     */
    private function getBranch($branches)
    {
        $brandString = "";
        $itemBrand = explode(",", $branches);
        foreach ($this->company->getBrands() as $brand) {
            if (in_array($brand->__get("id"), $itemBrand)) {
                $brandString .= $brand->__get("BrandName") . ", ";
            }
        }
        if ($brandString == "") {
            $brandString = lang('all_branches_store');
        }
        if (substr($brandString, -2) == ", ") {
            $brandString = substr($brandString, 0, -2);
        }
        return $brandString;
    }

    private function getPaymentType($paymentType, $payment = null)
    {
        if($payment == 1){
            $payment = lang('regular_payment_items');
        }
        else if($paymentType == 4 || $payment == 2) {
            $payment = lang('standing_order');
        }
        else{
            $payment = lang('regular_payment_items');
        }
        return $payment;
    }
    /**
     * @param $price
     * @param $vat
     * @param bool $includeTax
     * @return string|float
     */
    private function calcVatItemPrice($price, $vat, $includeTax = true)
    {
        return $includeTax ? $price / ($vat / 100 + 1) : $price;
    }

    private function getDepartment($type)
    {
        if ($type == 1) {
            return lang('cycle_membership');
        } else if ($type == 2) {
            return lang('punch_cards_items');
        } else if ($type == 3) {
            return lang('a_trial');
        } else if ($type == 4) {
            return lang('related_items_store');
        } else {
            return null;
        }
    }

    public function toggleSuspendItem($id, $bool)
    {
        $display = 0;
        return DB::table('boostapp.items')
            ->where('id', $id)
            ->update([
                'Disabled' => $bool,
                'Display' => $display
            ]);
    }
    public function toggleSuspendLink($id, $bool)
    {
        $itemApp = 1;
        return DB::table('boostapp.payment_pages')
            ->where('id', $id)
            ->update([
                'Disabled' => $bool,
                'ItemApp' => $itemApp
            ]);
    }
    public function deleteItem($id)
    {
        DB::table('boostapp.payment_pages')
            ->where('ItemId', $id)
            ->update([
                'Status' => "1"
            ]);
        return DB::table('boostapp.items')
            ->where('id', $id)
            ->update([
                'Status' => "1"
            ]);
    }
    public function deleteLink($id)
    {
        return DB::table('boostapp.payment_pages')
            ->where('id', $id)
            ->update([
                'Status' => "1"
            ]);
    }
    public function toggleAppDisplayItems($id, $bool)
    {
        if($bool == 1){
            $res = DB::table('boostapp.items')
                ->where('id', $id)
                ->update([
                    'Display' => $bool,
                    'Disabled' => 0,
                ]);
            $upadtePayment = DB::table('boostapp.payment_pages')
                ->where('ItemId', $id)
                ->update([
                    'Status' => 0
                ]);

        }
        else {
            $res = DB::table('boostapp.items')
                ->where('id', $id)
                ->update([
                    'Display' => $bool
                ]);
            $upadtePayment = DB::table('boostapp.payment_pages')
                ->where('ItemId', $id)
                ->update([
                    'Status' => 1
                ]);
        }
        return $res;
        //רגיל

    }
    public function toggleAppDisplayLink($id, $bool)
    {
        $res = DB::table('boostapp.payment_pages')
            ->where('id', $id)
            ->update([
                'ItemApp' => $bool,
                'Disabled' => $bool,
            ]);
        return $res;
        //הפוך
    }

    public function addNewSmartLink($data)
    {
        $vat = 17;
        $userId = $this->user->id;
        $itemObj = new Item();
        $companyNum = $this->company->__get("CompanyNum");
        $itemId = '';
        $items = [];
        $dataToInsert = [];

        if (isset($data['itemType']) && $data['itemType']['membership']['id'] != "0") {
            $itemId = $data['itemType']['membership']['id'];
        } else if (isset($data['itemType']) && count($data['itemType']['generalItem']['items']) > 0) {
            $items = $data['itemType']['generalItem']['items'];
        }

        $itemObj->getItemById($itemId);
        $itemValidType = 0;
        $ValidType = 1;
        if(isset($data['itemType']['membership'])){
            $ValidType = $data['itemType']['membership']["date"];
            if($ValidType == 4){
                $itemValidType = 6;
                $validData = array(
                    "date" => $data['itemType']['membership']["chooseDate"],
                    "allowLateRegitration" => (isset($data['itemType']['membership']["allowLateRegitration"]) && $data['itemType']['membership']["allowLateRegitration"] == "on") ? 1 : 0,
                    "allowRelativeReduction" => (isset($data['itemType']['membership']["allowRelativeReduction"]) && $data['itemType']['membership']["allowRelativeReduction"] == "on") ? 1 : 0,
                    "relativeReductionPrice" => (isset($data['itemType']['membership']["relativeReductionPrice"])) ? $data['itemType']['membership']["relativeReductionPrice"] : 0,
                );
                $validData = json_encode($validData);
            }
            else if($ValidType == 1){
                $itemValidType = 0;
            }
            else if($ValidType){
                $itemValidType = 5;
            }
        }

        if (count($items) > 0) {
            foreach ($items as $item) {
                $useTax = $item['taxIncluded'] ?? (bool)$item['taxIncluded'];
                $vatPrice = $item["price"] ?? $item["price"];
                if($useTax == "on"){
                    $useTax = 0;
                }
                else{
                    $useTax = 1;
                }
                $itemObj->getItemById($item['itemId']);
                $dataToInsert = array(
                    "CompanyNum" => $companyNum,
                    "ItemId" => $item['itemId'],
                    "Title" => $data["pageTitle"] ?? '',
                    "TitleUrl" => $data["pageTitle"] ?? '',
                    "Content" => $data["description"] ?? '',
                    "Amount" => $vatPrice,
                    "UserId" => $userId,
                    "Vat" => $useTax,
                    "Dates" => date('Y-m-d H:i:s'),
                    "Brands" => $data['clientToBranch'] ?? "BA999",
                    "ThankYouPage" => $data['linkAfterPay'],
                    "dynamicForm" => isset($data['registerForm']) ? $this->uniqeIdsAsString($data['registerForm']) : '',
                    "medicalForm" => isset($data['medicalForm']) ? $this->uniqeIdsAsString($data['medicalForm']) : '',
                    "extraFees" => isset($data['registerInsurance']) ? $this->uniqeIdsAsString($data['registerInsurance']) : '',
                    "ItemVaildType" => $itemValidType,
                    "lateRegistration" => $validData ?? $ValidType,
                    "ImageLink" => isset($data["image"]) && !empty($data["image"]) ? $data["image"] : null,
                    "RandomNumber" => uniqid(),
                    "MaxPaymentRegular" => 12,
                    "ItemDepartment" => $itemObj->__get("Department") ?? 1,
                    "pageImg" => !empty($data["image"]) ? str_replace('files/items/', '', $_POST['image'])  : null,
                    "isNew" => 1
                );

                $ItemsId = DB::table('payment_pages')->insertGetId(
                    $dataToInsert
                );

            }
        } else {

            $type = $this->ticketsOrMembership($itemId);
            $useTax = $data['itemType']['membership']['taxIncluded'] ?? (bool) $data['itemType']['membership']['taxIncluded'];
//            if ($useTax && isset($data['itemType']['membership']['price'])) {
//                $vatPrice = $this->calcVatItemPrice($data['itemType']['membership']['price'], $vat, $useTax);
//            }
//            else {
                $vatPrice = $data['itemType']['membership']['price'] ?? $data['itemType']['membership']['price'];
//            }
            if($useTax == "on"){
                $useTax = 0;
            }
            else{
                $useTax = 1;
            }

            $dataToInsert =  array(
                "CompanyNum" => $companyNum,
                "ItemId" => $itemId,
                "Title" => $data["pageTitle"] ?? '',
                "TitleUrl" => $data["pageTitle"] ?? '',
                "Content" => $data["description"] ?? '',
                "Amount" => $vatPrice,
                "UserId" => $userId,
                "Vat" => $useTax,
                "Dates" => date('Y-m-d H:i:s'),
                "Brands" => $data['clientToBranch'] ?? "BA999",
                "ThankYouPage" => $data['linkAfterPay'],
                "dynamicForm" => isset($data['registerForm']) ? $this->uniqeIdsAsString($data['registerForm']) : '',
                "medicalForm" => isset($data['medicalForm']) ? $this->uniqeIdsAsString($data['medicalForm']) : '',
                "extraFees" => isset($data['registerInsurance']) ?  $this->uniqeIdsAsString($data['registerInsurance']) : '',
                "ItemVaildType" => $itemValidType,
                "lateRegistration" => isset($validData) ? $validData : $ValidType,
                "ImageLink" => !empty($data["image"]) ? $data["image"] : null,
                "RandomNumber" => uniqid(),
                "MaxPaymentRegular" => 12,
                "ItemDepartment" => $itemObj->__get("Department"),
                "pageImg" => !empty($data["image"]) ? str_replace('files/items/', '', $_POST['image'])  : null,
                "isNew" => 1
            );

            $ItemsId = DB::table('payment_pages')->insertGetId(
                $dataToInsert
            );

            if ($type == 'tickets' && !empty($ItemsId)) { //is a ticket
                $dates = $data['dateTickets'] ?? [];
                foreach ($dates as $date) :
                    $dataToInsert =  array(
                        "CompanyNum" => $companyNum,
                        "classId" => $date['class'],
                        "classDate" => date('Y-m-d H:i:s', strtotime($date['date'])),
                        "paymentId" => $ItemsId,
                        "date" => date('Y-m-d H:i:s')
                    );
                    DB::table('assignTickets')->insertGetId(
                        $dataToInsert
                    );
                endforeach;
            } elseif ($type == 'membership' && !empty($ItemsId)) {
                //is annual membership
                $days = $data['annualMembership'] ?? [];
                foreach ($days as $day) :
                    $dataToInsert =  array(
                        "CompanyNum" => $companyNum,
                        "classGroup" => $day['class'],
                        "day" => $day['day'],
                        "paymentId" => $ItemsId,
                        "date" => date('Y-m-d H:i:s')
                    );
                    DB::table('assignMembership')->insertGetId(
                        $dataToInsert
                    );

                endforeach;
            }
            // at insert to table assigntickets - date + classid כרטיסיות 
            //option1 after that insert to table assignMembership - day + classGroup מנוי שנתי מתחדש
        }
    }

    public function getSingleItem($id)
    {
        $item = DB::table("items")->leftjoin('item_cat', 'item_cat.id', '=', 'items.ItemCat')->select('items.*', "item_cat.Name as CategoryName", "item_cat.id as CategoryId")
            ->where('items.Department', "=", 4)->where('items.CompanyNum', '=', $this->company->__get("CompanyNum"))->where("items.Status", "=", 0)->where("items.id", "=", $id)->first();
        $limit = DB::table('items_limit')->where('itemId', "=", $id)->first();
        $itemDet = new ItemDetails();
        $details = $itemDet->getItemDetailsAsArr($id);
//        $OpenTables = DB::table('items')->leftjoin('item_details', function ($join) {
//            $join->on("item_details.itemId", "=", "items.id")->on('item_details.CompanyNum', '=', 'items.CompanyNum');
//        })->leftjoin('item_cat', 'item_cat.id', '=', 'items.ItemCat')
//            ->select('items.*', "item_cat.Name as CategoryName", "item_details.barcode as barcode", "item_details.sku as sku", "item_details.inventory as inventory", "item_details.colors as colors", "item_details.sizes as sizes")
//            ->where('items.Department', "=", 4)->where('items.CompanyNum', '=', $this->company->__get("CompanyNum"))->where("items.Status", "=", 0)->where("items.id", "=", $id)->first();
//        $limit = DB::table('items_limit')->where('itemId', "=", $id)->first();
        return array("item" => $item, "limit" => $limit, "details" => $details);
    }

    public function getSingleSmartLink($id)
    {
        $class_calendar=new ClassCalendar();
        $smartLink = DB::table("payment_pages")
            ->select("*")
            ->where("payment_pages.CompanyNum", "=", $this->company->__get("CompanyNum"))
            ->where("payment_pages.id", "=", $id)
            ->first();

        $type = $this->itemOrMembership($smartLink->ItemId);

        if ($type === 'membership') {
            $tickets = DB::table("assignTickets")
                ->select("*")
                ->where("assignTickets.paymentId", "=", $smartLink->id)
                ->get();

            $membership = DB::table("assignMembership")
                ->select("*")
                ->where("assignMembership.paymentId", "=", $smartLink->id)
                ->get();
        }

        $smartLink->type = $type;
        $smartLink->tickets = '';
        $smartLink->membership = '';

        if (!empty($tickets)) {
            $newTickets=[];
            foreach($tickets as $ticket){
                $thisSelectOptions=$class_calendar->getClassesByDate($ticket->classDate,$ticket->CompanyNum);
                if($thisSelectOptions != null) {
                    $ticket->options = $class_calendar->createArrayFromObjArr($thisSelectOptions);
                    $newTickets[] = $ticket;
                }
            }
            $smartLink->tickets = $newTickets;
        }

        if (!empty($membership)) {
            $newMembership=[];
            $itemId=$smartLink->ItemId;

            $item = DB::table("items")
            ->where("items.id", "=", $itemId)
            ->first();
            if($item){
                foreach($membership as $singleMembership){
//                    echo $singleMembership->day;
//                    echo $singleMembership->CompanyNum;
//                    echo $item->Vaild;
//                    echo $item->Vaild_Type;
//                    var_dump( $this->convertMembershipDurationToTime($item->Vaild,$item->Vaild_Type));
//                    echo "endddddddd";
                    $thisSelectOptions=$class_calendar->getGroupClassesByDay($singleMembership->day,$singleMembership->CompanyNum,$this->convertMembershipDurationToTime($item->Vaild,$item->Vaild_Type,$item->Payment));
                    if($thisSelectOptions) {
                        $singleMembership->options = $class_calendar->createArrayFromObjArr($thisSelectOptions);
                        $newMembership[] = $singleMembership;
                    }
                }
                $smartLink->membership = $newMembership;
            }

        }


        // $dataTable = DB::table("payment_pages")
        //     ->leftJoin('docs', 'docs.pageId', '=', 'payment_pages.id')
        //     ->select("payment_pages.*", DB::raw('Count(docs.PageId) as CountSales'))
        //     ->where("payment_pages.CompanyNum", "=", $this->company->__get("CompanyNum"))
        //     ->where("payment_pages.id", "=", $id)
        //     ->first();
        return $smartLink;
    }

    public function getSingleMembership($id)
    {
        $roles = new ItemRoles();
        $purchaseLimits = DB::table('items_limit')
            ->select()
            ->where('items_limit.itemId', "=", $id)
            ->first();
        $registerLimits = DB::table('items_roles')
            ->select()
            ->where('items_roles.ItemId', "=", $id)
            ->get();
        $sortedRegisterLimits = array();
        foreach ($registerLimits as $key => $item) {
            $sortedRegisterLimits[$item->GroupId][] = $item;
        }
        ksort($sortedRegisterLimits, SORT_NUMERIC);
        foreach ($sortedRegisterLimits as $key => $item) {
            $item_role_name = DB::table('items_roles_names')->where("GroupId", "=", $key)->first();
            $sortedRegisterLimits[$key][] = array(
                "Group" => "String",
                "Item" => "String",
                "Value" => $item_role_name ? $item_role_name->GeneratedString : $roles->generateRolesString($item)
            );
        }

        $sortedRegisterLimits = array_values($sortedRegisterLimits);

        $membership = DB::table('items')->leftJoin('membership_type', function ($join) {
            $join->on('membership_type.id', '=', 'items.MemberShip')->on('membership_type.CompanyNum', '=', 'items.CompanyNum');
        })->join('appsettings', 'appsettings.CompanyNum', '=', 'items.CompanyNum')
            ->select('items.*', 'membership_type.Type as mType', 'appsettings.MembershipType')
            ->where('items.Department', "!=", 4)
            ->where('items.CompanyNum', '=', $this->company->__get("CompanyNum"))->where("items.Status", "=", 0)
            ->where('items.id', "=", $id)
            ->first();

        $membership->purchaseLimits = $purchaseLimits;
        $membership->registerLimits = $sortedRegisterLimits;
        return $membership;
    }

    public function updateItem($data)
    {
        try {
            if($data["vat"] == "true" || $data["vat"] == true ) {
                $vat = 17;
            }
            else{
                $vat = 0;
            }
            $vatPrice = $this->calcVatItemPrice($data["price"], $vat, true);
            $userId = $this->user->id;
            $companyNum = $this->company->__get("CompanyNum");
            if ($data['categoryIsNew'] == "true") {
                $categoryClass = new ItemCategory();
                $cat = $categoryClass->setCompanyCategory($companyNum, $data['category']);
            } else {
                $cat = $data['category'];
            }
            $name = str_replace("\\","/",$data["name"]);
            DB::table('items')->where('id', "=", $data["id"])->update(
                array(
                    "CompanyNum" => $companyNum,
                    "Department" => 4,
                    "ItemName" => $name,
                    "UserId" => $userId,
                    "ItemPrice" => $data["price"],
                    "ItemPriceVat" => $vatPrice,
                    "CostPrice" => isset($data["costPrice"]) ? $data["costPrice"] : 0.00,
                    "Brands" => (isset($data["branch"]) && $data["branch"] != "-1") ? $data["branch"] : "BA999",
                    "Vat" => $vat,
                    "MemberShip" => "BA999",
                    "ItemCat" => $cat,
                    "Dates" => date('Y-m-d H:i:s'),
                    "Content" => isset($data["content"]) ? $data["content"] : '',
                    "Display" => $data["allowBuyInApp"] == "true" ? "1" : "0",
                    "Image" => !empty($data["img"]) ? $data["img"] : null
                )
            );

            $paymentObj = new PaymentPage();
            $payment = $paymentObj->getFirstPaymentPagesOfItem($data["id"]);

            if(isset($data['allowBuyInApp']) && (bool) $data['allowBuyInApp'] == 1) {
    
                $linkDataToInsert = array(
                    "CompanyNum" => $this->company->__get("CompanyNum"),
                    "Title" => $data["name"],
                    "TitleUrl" => $data["name"],
                    "Content" => isset($data["membershipContent"]) ? $data["membershipContent"] : null,
                    "Amount" => $data["price"],
                    "ItemDepartment" => 4,
                    "Status" => 0,
                    "UserId" => $userId,
                    "Dates" => date('Y-m-d H:i:s'),
                    "Brands" => (isset($data["branch"]) && $data["branch"] != "-1") ? $data["branch"] : "BA999",
                    "displayTable" => 0,
                    "ImageLink" => !empty($data["img"]) ? $data["img"] : null,
                    "pageImg" => !empty($data["img"]) ? str_replace('files/items/', '', $_POST['img'])  : null,
                    "isNew" => 1
                );
                if($payment->__get('id')) {
                    $updatePaymentPage = DB::table('payment_pages')->where('id', "=", $payment->__get('id'))->update($linkDataToInsert);
                } else {
                    ///// insert new payment page
                    $linkDataToInsert["ItemId"] = $data["id"];
                    $linkDataToInsert["RandomNumber"] = uniqid();
                    $linkDataToInsert["MaxPaymentRegular"] = 12;
                    $paymentId = DB::table('payment_pages')->insertGetId($linkDataToInsert);
                }
                
            } else if($payment->__get('id')) {
                $updatePaymentPage = DB::table('payment_pages')->where('id', "=", $payment->__get('id'))->update(array('Status' => 1));
            }

            $itemLimit = new ItemLimit();
            $itemLimit->deleteLimits($data['id']);
            if (isset($data['purchaseLimits'])) {

                $itemLimit->createObjFromShop($data["purchaseLimits"], $userId, $companyNum, $data['id']);
                $itemLimit->insertItemLimit();
            }
            $itemDetails = new ItemDetails();
            //$itemDetails->deleteDetails($data['id']);
            $iDetails = $itemDetails->updateItemDetails($data);
            $res = ["Status" => true];
            if (isset($iDetails)) {
                if (isset($iDetails["newSize"])) {
                    $res["newSize"] = $iDetails["newSize"];
                }
                if (isset($iDetails["newSupplier"])) {
                    $res["newSupplier"] = $iDetails["newSupplier"];
                }
            }

            if ($data["categoryIsNew"] == "true") {
                $res["category"] = '<option value="'.$cat.'">'.$data["category"].'</option>';
            }

            return $res;
        } catch (Exception $e) {
            return json_encode($e);
        }
    }

    private function createMembershipType($name) {
        return DB::table('boostapp.membership_type')->insertGetId(
            array(
                "CompanyNum" => $this->company->__get("CompanyNum"),
                "Type" =>  $name,
                "Status" =>"0",
                "Count" => "0",
                "ClassMemberType" => "BA999",
                "ViewClassAct" => "0",
                "ViewClass" => "3",
                "ViewClassDayNum" => "6",
                "OldId" => "0"

            )
        );
    }

    function updateMembership($data)
    {
        if($data['isMembershipTypeNew']=="1"){
            $data["membershipName"] = $data["membershipType"];
            $data["membershipType"] = $this->createMembershipType($data["membershipType"]);
    
        }

        $vat = 17;
        $hasTax = isset($data["hasTax"]) && $data["hasTax"] == "true" ? true : false;
        $vatPrice = $this->calcVatItemPrice((int) $data["price"], $vat, $hasTax);
        $userId = $this->user->id;
        $companyProductSettings= new CompanyProductSettings();
        $thisCompanySettings= $companyProductSettings->getSingleByCompanyNum($this->company->__get("CompanyNum"));
        if($thisCompanySettings->manageMemberships=="0"){
            $defaultMembership= DB::table('boostapp.membership_type')->where('CompanyNum','=',$this->company->__get("CompanyNum"))->where('mainMembership','=','1')->first();
            $data["membershipType"]=$defaultMembership->id;
        }
        //Boostapp logic
        if (!empty($data["type"])) {
            switch ($data["type"]) {
                case '4':
                    $data["type"] = '2';
                    break;
                case '5':
                    $data["type"] = '3';
                    break;
            }
        }
        $valid = isset($data["membershipLength"]) ? (int) $data["membershipLength"] : 0;
        $validType = isset($data["membershipUnits"]) ? $data["membershipUnits"] : 0;
        if(isset($data['priceOptions']) && $data['priceOptions'] == 2) {    /// periodic payment membership
            $valid = 1;
            $validType = 3;
        }
        $name = trim(str_replace("\\","/",$data["name"]));
        $dataToInsert =  array(
            "CompanyNum" => $this->company->__get("CompanyNum"),
            "Department" => $data["type"],
            "Brands" => (isset($data["branch"])) ?  $data["branch"] : "BA999",
            "ItemName" => $name,
            "ItemPrice" => $data["price"],
            "ItemPriceVat" => $vatPrice,
            "UserId" => $userId,
            "Dates" => date('Y-m-d H:i:s'),
            "Vat" => $hasTax ? $vat : 0,
            "Vaild" => $valid,
            "Vaild_Type" => $validType,
            "MemberShip" => (isset($data["membershipType"])) ? $data["membershipType"] : "BA999",
            "NotificationDays" => (isset($data["membershipAlertSettingsNumber"]) && $data["membershipAlertSettingsNumber"] > '0') ? $this->getNotificationDays($data) : 0,
            "notificationAtEnd" => (isset($data["alertOnEnd"]) && $data["alertOnEnd"] == 'true' ) ? "1" : "0",
            "ItemCat" => null,
            "CostPrice" => 0.00,
            "Display" => isset($data['allowBuyFromApp']) && (bool) $data['allowBuyFromApp'] ? 1 : 0,
            'Payment' => isset($data['priceOptions']) ? $data['priceOptions'] : null,
            "membershipStartCount" => isset($data['membershipStartSelect']) ? $data['membershipStartSelect'] : "1",
            "membershipStartDate" => $data['lateRegisterDateInputMembership'] ?? null,
            "membershipAllowLateReg" => $data['allowLateRegisterMembership'] == "true" ? "1" : "0",
            "membershipAllowRelativeDiscount" => $data['allowRelativeCheckboxMembership'] == "true" ? "1" : "0",
            "membershipRelativeDiscount" => isset($data['membershipRelativeDiscount']) ? $data['membershipRelativeDiscount'] : 0,
            "Content" => isset($data["membershipContent"]) ? $data["membershipContent"] : null,
            "BalanceClass" => isset($data["numOfEntries"]) ? $data["numOfEntries"] : 0,
            "Image" => !empty($data["pageImgPath"]) ? $data["pageImgPath"] : null,
            "LimitType" => 1
        );

        $update = DB::table('items')->where('id', "=", $data["id"])->update($dataToInsert);
        
        $paymentObj = new PaymentPage();
        $payment = $paymentObj->getFirstPaymentPagesOfItem($data["id"]);

        if(isset($data['allowBuyFromApp']) && (bool) $data['allowBuyFromApp'] == 1) {

            $linkDataToInsert = array(
                "CompanyNum" => $this->company->__get("CompanyNum"),
                "Title" => $data["name"],
                "TitleUrl" => $data["name"],
                "Content" => isset($data["membershipContent"]) ? $data["membershipContent"] : null,
                "Amount" => $data["price"],
                "ItemDepartment" => $data["type"],
                "Status" => 0,
                "UserId" => $userId,
                "Dates" => date('Y-m-d H:i:s'),
                "Brands" => (isset($data["branch"]) && $data["branch"] != "-1") ? $data["branch"] : "BA999",
                "displayTable" => 0,
                "PaymentType" => (isset($data['priceOptions']) && $data['priceOptions'] == 2) ? 4 : 1,
                "TypeKeva" => (isset($data['priceOptions']) && $data['priceOptions'] == 2) ? 0 : 1,
                "ItemVaildType" => (isset($data['membershipStartSelect']) && $data['membershipStartSelect'] == 1) ? 0 : 5,
                "ImageLink" => !empty($data["pageImgPath"]) ? $data["pageImgPath"] : null,
                "MaxPaymentRegular" => (isset($data['priceOptions']) && $data['priceOptions'] == 2) ? 0 : 12,
                "MaxPaymentToken" => 999,
                "TypePage" => (isset($data['priceOptions']) && $data['priceOptions'] == 2) ? 1 : 0,
                "pageImg" => !empty($data["pageImgPath"]) ? str_replace('files/items/', '', $_POST['pageImgPath'])  : null,
                "isNew" => 1
            );
            if($payment->__get('id')) {
                $updatePaymentPage = DB::table('payment_pages')->where('id', "=", $payment->__get('id'))->update($linkDataToInsert);
            } else {
                ///// insert new payment page
                $linkDataToInsert["ItemId"] = $data["id"];
                $linkDataToInsert["RandomNumber"] = uniqid();
                $paymentId = DB::table('payment_pages')->insertGetId($linkDataToInsert);
            }
            
        } else if($payment->__get('id')) {
            $updatePaymentPage = DB::table('payment_pages')->where('id', "=", $payment->__get('id'))->update(array('ItemApp' => 1));
        }

        if (isset($data["id"]) && $data["id"] != null && $data["id"] != "" && $data["id"] != 0) {

            $itemRole = new ItemRoles();
            if (isset($data['classes'])) {
                $itemRole->deleteItemRole($data["id"]);
                $itemRole->insertItemRole($data, $data["id"]);
            } else {
                $itemRole->deleteItemRole($data["id"]);
                $arr = [
                    "classes" => "all",
                    "maximum" => null,
                    "days" => null,
                    "hours" => null,
                    "extraHours" => null,
                    "register" => null,
                    "string" => "כל השיעורים.",
                    "id" => strtotime("now")
                ];
                $data["classes"][] = $arr;
                $itemRole->insertItemRole($data, $data["id"]);
            }

            if (isset($data["id"]) && $data["id"] != null && $data["id"] != "" && $data["id"] != 0 && isset($data['purchaseLimits'])) {
                $itemLimit = new ItemLimit();
                $itemLimit->deleteLimits($data["id"]);
                $itemLimit->createObjFromShop($data["purchaseLimits"], $userId, $this->company->__get("CompanyNum"), $data["id"]);
                $itemLimit->insertItemLimit();
            }
        }

        $res = ["data" => true];
        if ($data["isMembershipTypeNew"] == 1) {
            $res["membershipType"] = '<option value="'.$data['membershipType'].'">'.$data["membershipName"].'</option>';
        }

        return $res;
    }

    function updateSmartLink($data)
    {
        $vat = 17;
        $userId = $this->user->id;
        $companyNum = $this->company->__get("CompanyNum");
        $itemObj = new Item();
        $itemId = '';
        $items = [];
        $dataToInsert = [];

        if (isset($data['itemType']) && $data['itemType']['membership']['id'] != "0") {
            $itemId = $data['itemType']['membership']['id'];
        } else if (isset($data['itemType']) && count($data['itemType']['generalItem']['items']) > 0) {
            $items = $data['itemType']['generalItem']['items'];
        }
        $itemObj->getItemById($itemId);
        $itemValidType = 0;
        $ValidType = 1;
        if(isset($data['itemType']['membership'])){
            $ValidType = $data['itemType']['membership']["date"];
            if($ValidType == 4){
                $itemValidType = 6;
                $validData = array(
                    "date" => $data['itemType']['membership']["chooseDate"],
                    "allowLateRegitration" => (isset($data['itemType']['membership']["allowLateRegitration"]) && $data['itemType']['membership']["allowLateRegitration"] == "on") ? 1 : 0,
                    "allowRelativeReduction" => (isset($data['itemType']['membership']["allowRelativeReduction"]) && $data['itemType']['membership']["allowRelativeReduction"] == "on") ? 1 : 0,
                    "relativeReductionPrice" => (isset($data['itemType']['membership']["relativeReductionPrice"])) ? $data['itemType']['membership']["relativeReductionPrice"] : 0,
                );
                $validData = json_encode($validData);
            }
            else if($ValidType == 1){
                $itemValidType = 0;
            }
            else if($ValidType){
                $itemValidType = 5;
            }
        }

        if (count($items) > 0) {
            foreach ($items as $item) {
                $itemObj = new Item();
                $itemObj->getItemById($item['itemId']);
                $useTax = $item['taxIncluded'] ?? (bool)$item['taxIncluded'];
                if($useTax == "on"){
                    $useTax = 0;
                }
                else{
                    $useTax = 1;
                }
                $vatPrice = isset($item["price"]) ? $item["price"] : $itemObj->__get("ItemPrice");
                $dataToInsert = array(
                    "CompanyNum" => $companyNum,
                    "ItemId" => $item['itemId'],
                    "Title" => $data["pageTitle"] ?? '',
                    "TitleUrl" => $data["pageTitle"] ?? '',
                    "Content" => $data["description"] ?? '',
                    "Amount" => $vatPrice,
                    "UserId" => $userId,
                    "Vat" => $useTax,
                    "Dates" => date('Y-m-d H:i:s'),
                    "Brands" => $data['clientToBranch'] ?? "BA999",
                    "ThankYouPage" => $data['linkAfterPay'],
                    "dynamicForm" => isset($data['registerForm']) ? $this->uniqeIdsAsString($data['registerForm']) : '',
                    "medicalForm" => isset($data['medicalForm']) ? $this->uniqeIdsAsString($data['medicalForm']) : '',
                    "extraFees" => isset($data['registerInsurance']) ? $this->uniqeIdsAsString($data['registerInsurance']) : '',
                    "ItemVaildType" => $itemValidType,
                    "ItemDepartment" => $itemObj->__get("Department") ?? 1,
                    "lateRegistration" => isset($validData) ? $validData : $ValidType,
                    "ImageLink" => !empty($data["image"]) ? $data["image"] : null,
                    "pageImg" => !empty($data["image"]) ? str_replace('files/items/', '', $_POST['image'])  : null
                );
                DB::table('payment_pages')->where('id', "=", $data["id"])->update($dataToInsert);
            }
        } else {
            $item = new Item();
            $item->getItemById($itemId);
            $type = $this->ticketsOrMembership($itemId);
            $useTax = $data['itemType']['membership']['taxIncluded'] ?? (bool) $data['itemType']['membership']['taxIncluded'];
            if($useTax == "on"){
                $useTax = 0;
            }
            else{
                $useTax = 1;
            }
            $vatPrice = isset($data['itemType']['membership']['price']) ? $data['itemType']['membership']['price'] : $item->__get("ItemPrice");
            $dataToInsert =  array(
                "CompanyNum" => $companyNum,
                "ItemId" => $itemId,
                "Title" => $data["pageTitle"] ?? '',
                "TitleUrl" => $data["pageTitle"] ?? '',
                "Content" => $data["description"] ?? '',
                "Amount" => $vatPrice,
                "UserId" => $userId,
                "Vat" => $useTax,
                "Dates" => date('Y-m-d H:i:s'),
                "Brands" => $data['clientToBranch'] ?? "BA999",
                "ThankYouPage" => $data['linkAfterPay'],
                "dynamicForm" => isset($data['registerForm']) ? $this->uniqeIdsAsString($data['registerForm']) : '',
                "medicalForm" => isset($data['medicalForm']) ? $this->uniqeIdsAsString($data['medicalForm']) : '',
                "extraFees" => isset($data['registerInsurance']) ?  $this->uniqeIdsAsString($data['registerInsurance']) : '',
                "ItemVaildType" => $itemValidType,
                "ItemDepartment" => $itemObj->__get("Department") ?? 1,
                "lateRegistration" => isset($validData) ? $validData : $ValidType,
                "ImageLink" => !empty($data["image"]) ? $data["image"] : null,
                "pageImg" => !empty($data["image"]) ? str_replace('files/items/', '', $_POST['image'])  : null
            );

            DB::table('payment_pages')->where('id', "=", $data["id"])->update($dataToInsert);

            if ($type == 'tickets') { //is a ticket
                DB::table('assignTickets')->where('paymentId', '=', $data["id"])->delete();
                $dates = $data['dateTickets'] ?? [];
                foreach ($dates as $date) :
                    $dataToInsert =  array(
                        "CompanyNum" => $companyNum,
                        "classId" => $date['class'],
                        "classDate" => date('Y-m-d H:i:s', strtotime($date['date'])),
                        "paymentId" => $data["id"],
                        "date" => date('Y-m-d H:i:s')
                    );
                    DB::table('assignTickets')->insertGetId(
                        $dataToInsert
                    );
                endforeach;
            } elseif ($type == 'membership') {
                //is annual membership
                DB::table('assignMembership')->where('paymentId', '=', $data["id"])->delete();
                $days = $data['annualMembership'] ?? [];
                foreach ($days as $day) :
                    $dataToInsert =  array(
                        "CompanyNum" => $companyNum,
                        "classGroup" => $day['class'],
                        "day" => $day['day'],
                        "paymentId" => $data["id"],
                        "date" => date('Y-m-d H:i:s')
                    );
                    DB::table('assignMembership')->insertGetId(
                        $dataToInsert
                    );
                endforeach;
            }
            // at insert to table assigntickets - date + classid כרטיסיות 
            //option1 after that insert to table assignMembership - day + classGroup מנוי שנתי מתחדש
        }
    }

    function itemOrMembership($itemId)
    {
        $type = '';
        $item = DB::table("items")
            ->select("Department")
            ->where("items.id", "=", $itemId)
            ->first();
        $membershipArr = ['1', '2', '3'];
        if ($item && $item->Department && in_array($item->Department, $membershipArr)) {
            $type = 'membership';
        } else {
            $type = 'item';
        }
        return $type;
    }

    function ticketsOrMembership($id)
    {
        $type = '';
        $item = DB::table("items")
            ->select("Vaild","Department")
            ->where("items.id", "=", $id)
            ->first();

        if ($item && (($item->Department == 3) || ($item->Vaild == 0 && $item->Department == 2))) {
            $type = "tickets";
        } else {
            $type = "membership";
        }
        return $type;
    }

    private function uniqeIdsAsString($arr)
    {
        $uniqe = array_unique($arr);
        $returnString = implode(',', $uniqe);
        return $returnString;
    }
}
