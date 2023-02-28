<?php
require_once  __DIR__.'/../app/init.php';
require_once __DIR__ .'/../office/Classes/Utils.php';
require_once __DIR__ .'/../office/Classes/Item.php';
require_once __DIR__ .'/../office/services/LoggerService.php';
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}
/////////////// change
//SELECT * FROM boostapp.items_limit where startAge < 0    >> 0
//SELECT * FROM boostapp.items_limit where startAge > 120  >> 120
//SELECT * FROM boostapp.items_limit where endAge > 120  >> 120
//SELECT * FROM boostapp.items_limit where endAge < startAge  >>  ?


/////////////// ask!
//SELECT * FROM boostapp.items where ItemPriceVat < 0 >> fix
//SELECT * FROM boostapp.items where Vat != 17 >> 17
//SELECT * FROM boostapp.items where membershipAllowRelativeDiscount = 1  to  >> 0


$filename = basename(__FILE__, '.php');
try {
    //***** change items *******//
    $companyNums = [];
//    $companyNumsNotValid = [193027,274125,819079,704885,720051,461799,461799,237107,872870,274125,408487,298419,819079,384595,384595,384595,384595,384595,152220,152220,152220,152220,152220,986986,239647,298419,298419,298419,378185,378185,142364,298419,152220,298419,408487,408487,408487,408487,298419,408487,614353,281832,666389,238733,384595,980914,904346,608322];

    foreach ($companyNums as $companyNum) {
//        if(in_array($companyNum,$companyNumsNotValid)) {
//            echo '<br>' . 'not valid -' . $companyNum .'<br>';
//            continue;
//        }


        $items = DB::table('boostapp.items')
            ->where('CompanyNum', $companyNum)
            ->where('Department', '!=', 4)
            ->where('isPaymentForSingleClass', 0)
            ->whereNull('ClubMembershipsId')
            ->orderBy('CompanyNum')
            ->get();

        /** @var Item $item */
        foreach ($items as $itemStd) {
            //create item modal
            $item = new Item($itemStd->id);

            //********************* Create clubMemberships *********************//
            $data = [
                'Status' => $item->Status == 0  ? 1 : 0,
                'CompanyNum' => $item->CompanyNum,
                'ClubMemberShipName' => $item->ItemName,
            ];
            if($item->Status != 1 && $item->Disabled == 1 ) {
                $data['Status'] = 2;
            }

            //MemberShip
            if (is_numeric($item->MemberShip) && $item->MemberShip > 0) {
                $data['MemberShipTypeId'] = $item->MemberShip;
            }
            //Brand
            if (is_numeric($item->Brands) && $item->Brands > 0) {
                $data['BrandsId'] = $item->Brands;
            }
            $clubMembershipsId = DB::table('boostapp.club_memberships')
                ->insertGetId($data);
            if ($clubMembershipsId == 0) {
                LoggerService::info($data, LoggerService::CATEGORY_CLUB_MEMBERSHIPS . 'script');
                echo $item->id;
                continue;
            }
            //********************* Update items *********************//
            $newItemName = $item->getNewItemName($item->ItemName);
            //fix vat
            $itemPrice = $item->ItemPrice ?? 0;
            $itemData = ['ClubMembershipsId' => $clubMembershipsId,
                'Vat' => 17,
                'ItemPriceVat' => $itemPrice - ($itemPrice * 0.17)
            ];
            //from html to string
            if ((bool)($item->Content)) {
                $oldText = $item->Content;
                $search = '/' . preg_quote("<li>", '/') . '/';
                $oldText = preg_replace($search, "• ", $oldText, 1);
                $textAddPoint = str_replace("<li>", "\n" . "• ", $oldText);
                $textWithOutTags = strip_tags($textAddPoint);
                $itemData['Content'] = str_replace('&nbsp;', ' ', $textWithOutTags);
            }
            $itemData['ItemName'] = $newItemName; // create item name

            DB::table('boostapp.items')
                ->where('id', $item->id)
                ->update($itemData);

            $clubMembershipsIdArray = ['ClubMembershipsId' => $clubMembershipsId];

            $itemsRoles = DB::table('boostapp.items_roles')
                ->where('itemId', $item->id)
                ->update($clubMembershipsIdArray);

            $itemsLimits = DB::table('boostapp.items_limit')
                ->where('itemId', $item->id)
                ->update($clubMembershipsIdArray);
        }
        echo $companyNum .'<br>';
    }
}catch (Exception $e) {
    echo $e->getMessage();
}