<?php

require_once '../app/initcron.php';
header('Content-Type: text/html; charset=utf-8');
$CompanyNum = Auth::user()->CompanyNum;
$clients = DB::table('client')->where('CompanyNum','=',$CompanyNum)->where('status','=',0)->get();
$activity = false;
$clientCheckInvalid = [];
//$clientcheck = DB::table('client_activities')
//    ->where('TrueDate','<', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $companyNum)->where('Status','=', '0')
//    ->orWhere('TrueBalanceValue','<', '1')->whereNull('TrueDate')->where('Department','=', '2')->where('CompanyNum','=', $companyNum)->where('Status','=', '0')
//    ->orWhere('Department','=', '2')->where(function($query) {
//        $query->where('TrueDate','<', date('Y-m-d'))
//            ->orWhere('TrueBalanceValue','<', '1');
//    })->where('CompanyNum','=', $companyNum)->where('Status','=', '0')
//    ->groupBy('ClientId')->get();
foreach ($clients as $client){
    $clientcheck = DB::table('client_activities')->where('ClientId' ,'=',$client->id)->where("Status","=",0)->get();
    if(!empty($clientcheck)) {
        $num = 0;
        foreach ($clientcheck as $key => $check) {

            $date = date_create();
            $StartDate = new DateTime($check->StartDate);
            $ValidDate = new DateTime($check->TrueDate);
            switch ($check->Department) {
                case 1:
                    if ($date->getTimestamp() <= $ValidDate->getTimestamp()) {
                        $activity = true;
                    }
                    break;
                case 2 || 3:
                    if (($date->getTimestamp() <= $ValidDate->getTimestamp() && $check->TrueBalanceValue > 0) || ($check->TrueBalanceValue > 0 && $check->VaildDate == null)) {
                        $activity = true;
                    }
                    break;
            }
            if($activity){
                $num = $key;
                break;
            }
        }
        if(!$activity){
            $clientcheck[$num]->client = $client;
            array_push($clientCheckInvalid,$clientcheck[$num]);
        }
        else{
            $activity = false;
        }

    }
}


$TableCount = count($clientCheckInvalid);
?>
    {
    "data": [

<?php
$number = $TableCount;
$i=1;
foreach ($clientCheckInvalid as $Client){
    if(!empty($Client->client->id)){
        $ClientID = $Client->client->id;
        $ClientName= '<a href=\"/office/ClientProfile.php?u='.@$Client->client->id.'\" >'. $Client->client->FirstName .' '. $Client->client->LastName .'</a>';
        $CompanyId = $Client->client->CompanyId;
        $Phone = $Client->client->ContactMobile;
        $TrueBalanceValue = '';
        $balance = '';
        if ($Client->Status!='1'){
            if ($Client->Department=='1') {
                $Type = 'מנוי תקופתי';
            }
            else if ($Client->Department=='2'){
                $Type = 'כרטיסיה';
            }
            else if ($Client->Department=='3'){
                $Type = 'התנסות';
            }
            else if ($Client->Department=='4'){
                $Type = 'פריט לרכישה';
            }
            $Item = $Client->ItemText;
            if ($Client->TrueDate!=''){
                $date = date_create("$Client->TrueDate");

                $validityDate =  date_format($date,"d/m/Y");
            }
            else{
                $validityDate = '';
            }

            ?>
            [
            "<?php echo $ClientID; ?>",
            "<?php echo $ClientName; ?>",
            "<?php echo $CompanyId; ?>",
            "<?php echo $Phone ?>",
            "<?php echo $Type; ?>",
            "<?php echo $Item; ?>",
            "<?php echo $validityDate; ?>",
            "",
            ""
            ]

            <?php if ($i < $number) {echo ',';} ?>
        <?php }
    } $i++; $TableCount--;
}
?>
]}


