<?php
require_once  __DIR__.'/../app/init.php';
require_once __DIR__ .'/../office/Classes/Utils.php';
require_once __DIR__ .'/../office/Classes/Item.php';
require_once __DIR__ .'/../office/services/LoggerService.php';
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}

//SELECT * FROM boostapp.items where Vaild_Type = 1 and Vaild > 10    >> fix type if can : הרצתי פתרון לקים (1)

//SELECT items.Vaild, items.* FROM boostapp.items where Vaild_Type = 1 and Vaild > 10 group by Vaild   --> פתרון להגבלה ימים ?
//SELECT items.Vaild, items.* FROM boostapp.items where Vaild_Type = 1 and Vaild > 10 group by Vaild --> פתרון משבועות?
//SELECT items.Vaild, items.* FROM boostapp.items where Vaild_Type = 3 and Vaild > 12 and Vaild not in (18,24) group by Vaild --> פתרון להגבלת חודשים ?

//SELECT * FROM boostapp.items where Vaild <0 --> יש 10 פעילים מה עוןשים איתם?
//SELECT * FROM boostapp.items where Vaild <1 and Department = 1  --> יש 122 מנויים שערך לא תקין

//SELECT items.BalanceClass , items.* FROM boostapp.items where BalanceClass > 144 group by BalanceClass --?


//SELECT * FROM boostapp.items where ItemPrice < 0   ?
//SELECT * FROM boostapp.items where ItemPriceVat < 0 ?
//SELECT * FROM boostapp.items where Vat != 17 >> 17 ?
//SELECT * FROM boostapp.items where membershipAllowRelativeDiscount = 1  to  >> 0




//SELECT * FROM boostapp.items_limit where startAge < 0    >> 0
//SELECT * FROM boostapp.items_limit where startAge > 120  >> 120
//SELECT * FROM boostapp.items_limit where endAge > 120  >> 120
//SELECT * FROM boostapp.items_limit where endAge < startAge  >>  ?
//SELECT * FROM boostapp.items_limit where maxPurchase > 20 >> 20


//SELECT *
//FROM   boostapp.items
//LEFT OUTER JOIN boostapp.items_roles
//  ON (items.id = items_roles.ItemId)
//  WHERE items_roles.ItemId IS NULL
//  and Department < 4 and Status = 0
//--> משה לראות מה עושים




$filename = basename(__FILE__, '.php');
try {
    //***** change items *******//


    //***** change items Vaild_type = day to week *******//
//        $items = DB::table('boostapp.items')
//            ->where('Vaild_Type', 1)//day
//            ->where('Vaild','>', 10)
//            ->where('CompanyNum', 100)
//            ->get();
//        /** @var Item $item */
//        foreach ($items as $item) {
//            if($item->Vaild % 7 === 0 && $item->Vaild < 71  && $item->Vaild > 6) {
//                $newValid = $item->Vaild / 7 ;
//                DB::table('boostapp.items')
//                    ->where('id', $item->id)
//                    ->update(['Vaild' =>  $newValid, 'Vaild_Type' => 2]);
//            }
//        }
    //***** done change items Vaild_type = day to week *******//

    //***** change items Vaild_type = day to week *******//
    $items = DB::table('boostapp.items')
        ->where('Vaild_Type', 3)//month
        ->whereIn('Vaild', [1,2,3,4,5,6,7,8,9,10,11,12,18,24])
        ->where('CompanyNum', 100)
        ->get();
    /** @var Item $item */
    foreach ($items as $item) {
        if($item->Vaild % 7 === 0 && $item->Vaild < 71  && $item->Vaild > 6) {
            $newValid = $item->Vaild / 7 ;
//            DB::table('boostapp.items')
//                ->where('id', $item->id)
//                ->update(['Vaild' =>  $newValid, 'Vaild_Type' => 2]);
        }
    }
    //***** done change items Vaild_type = day to week *******//






}catch (Exception $e) {
    echo $e->getMessage();
}