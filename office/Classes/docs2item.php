<?php

require_once __DIR__."/Utils.php";
require_once __DIR__."/Client.php";
require_once __DIR__."/OrderItems.php";
require_once __DIR__."/MembershipType.php";
require_once __DIR__."/Brand.php";
require_once __DIR__."/DocsList.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $TrueCompanyNum
 * @property $Brands
 * @property $ClientId
 * @property $ItemId
 * @property $DocsId
 * @property $Amount
 * @property $Department
 * @property $MemberShip
 * @property $ItemName
 * @property $UserDate
 * @property $BusinessCompanyId
 * @property $BusinessType
 *
 * * *
 * Class docs2item
 * * */

class docs2item extends \Hazzard\Database\Model
{
    protected $table = 'docs2item';

    public function GetSales($dateFrom, $dateTo){
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = self::where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($dateFrom, $dateTo))->orderBy('UserDate', 'ASC')->get();

        $resArray = array("data" => array());
        $totalAmountAllPeriod = 0;
        foreach($OpenTables as $Task){
            $tempArr = array();

            $ClientObj = new Client($Task->ClientId);
            $ClientLink = '<a href="ClientProfile.php?u='.$ClientObj->__get('id').'/"><span class=\"text-dark\">'.$ClientObj->__get('CompanyName').'</span></a>';

            $membership = DB::table('membership')->where('id', $Task->Department)->first();

            if ($Task->MemberShip=='BA999'){
                $Type = lang('without_department');
            }
            else {
                $membership_type = new MembershipType();
                $membership_type = $membership_type->getRow($Task->MemberShip);
                $Type = $membership_type->Type ?? lang('no_membership_type');
            }

            if ($Task->Brands == 0){
                $Brands = lang('primary_branch');
            }
            elseif ($Task->Brands){
                $Brands = null;
                $brandsTypes = new Brand();
                $brandsTypes = $brandsTypes->getAllByCompanyNum($CompanyNum);
                foreach ($brandsTypes as $brandsType){
                    $Brands = $brandsType->id == $Task->Brands ? $brandsType->BrandName : $Brands;
                }
            }

            if ($Task->Amount >= '0.00'){
                $StatusClass = 'text-primary';
            }
            else {
                $StatusClass = 'text-danger';
            }

            $DocsInfo = DB::table('docs')->where('id', $Task->DocsId)->first();
            if(!$DocsInfo){
                continue;
            }
            $DocsPaymentsInfo = DB::table('docs_payment')->where('DocsId', $Task->DocsId)->groupBy('TypePayment')->get();

            foreach ($DocsPaymentsInfo as $DocsPaymentInfo){
                if ($DocsPaymentInfo->TypePayment=='1'){
                    $Payments = lang('cash');
                }
                elseif ($DocsPaymentInfo->TypePayment=='2'){
                    $Payments = lang('check');
                }
                elseif ($DocsPaymentInfo->TypePayment=='3'){
                    $Payments = lang('credit_card');
                }
                elseif ($DocsPaymentInfo->TypePayment=='4'){
                    $Payments = lang('bank_transfer');
                }
                else {
                    $Payments = lang('other');
                }
            }

            // default - management system
            $PaymentSource = lang('customer_card_my_profile_app');
            if (isset($DocsPaymentsInfo[0]) && str_contains($DocsPaymentsInfo[0]->CreditType, "האפליקציה")) {
                $PaymentSource = lang('application');
            } elseif (isset($DocsPaymentsInfo[0]) && (str_contains($DocsPaymentsInfo[0]->CreditType, "עסקה מגנטית") || str_contains($DocsPaymentsInfo[0]->CreditType, "דף סליקה"))) {
                $PaymentSource = lang('payment_page_shopping_cart');
            }

            $docsListItem = DocsList::where('DocsId', $DocsInfo->id)->first();

            $OrderItem = new OrderItems();
            if (!empty($DocsInfo->OrderId)) {
                $OrderItem->setDataByItemAndOrder($Task->ItemId, $DocsInfo->OrderId);
            }
            $totalAmount = $Task->Amount ?? $DocsInfo->Amount ?? $OrderItem->__get('TotalAmount');
            $totalAmountAllPeriod += $totalAmount;
            $tempArr[0] = with(date('d/m/Y', strtotime($Task->UserDate)));
            $tempArr[1] = $docsListItem->ItemName ?? $Task->ItemName;
            $tempArr[2] = $membership->MemberShip;
            $tempArr[3] = $Type;
            $tempArr[4] = '<span dir="ltr" class="'.$StatusClass.'"> '.$totalAmount.' </span> <input type="hidden" class="TotalAmounts"  name="Amounts" value="'.$totalAmount.'">';
            $tempArr[5] = $Brands;
            $tempArr[6] = $ClientLink;
            $tempArr[7] = $ClientObj->ContactMobile ? '<span dir="ltr">'.$ClientObj->ContactMobile.'</span>' : '--';
            $tempArr[8] = $Payments ?? lang('other');
            $tempArr[9] = '<a href="javascript:void(0);" onclick="TINY.box.show({iframe:\'PDF/Docs.php?DocType='.$DocsInfo->TypeDoc.'&DocId='.$DocsInfo->TypeNumber.'\',boxid:\'frameless\',width:750,height:470,fixed:false,maskid:\'bluemask\',maskopacity:40,closejs:function(){}})">'.lang('document_single').' '. $DocsInfo->TypeNumber.'</a>';
            $tempArr[10] = $PaymentSource;

            array_push($resArray["data"], $tempArr);
        }
        $resArray["totalAmountAllPeriod"] = $totalAmountAllPeriod;
        return $resArray;
    }

    /**
     * @param ClientActivities $ClientActivity
     */
    public function setPropertiesByClientActivity(ClientActivities $ClientActivity): void
    {
        $this->ItemId = $ClientActivity->ItemId;
        $this->Amount = $ClientActivity->ItemPrice;
        $this->Department = $ClientActivity->Department;
        $this->MemberShip = $ClientActivity->MemberShip;
        $this->ItemName = $ClientActivity->ItemText;
    }

    /**
     * @param Docs $Doc
     */
    public function setPropertiesByDocs(Docs $Doc): void
    {
        $this->CompanyNum = $Doc->CompanyNum;
        $this->TrueCompanyNum = $Doc->TrueCompanyNum;
        $this->Brands = $Doc->Brands;
        $this->ClientId = $Doc->ClientId;
        $this->DocsId = $Doc->id;
        $this->UserDate = $Doc->DocDate;
        $this->BusinessCompanyId = $Doc->BusinessCompanyId;
        $this->BusinessType = $Doc->BusinessType;
    }

    /**
     * @param int $docId
     * @param int $companyNum
     * @return bool
     */
    public static function existsForDocsId(int $docId, int $companyNum): bool
    {
        return (bool) self::where('CompanyNum', $companyNum)
            ->where('DocsId', $docId)
            ->first();
    }


    public static function getDocsItemsByDocId(int $companyNum, int $docId): array{
        return self::where('CompanyNum', $companyNum)->where('DocsId', $docId)->get();
    }

}
