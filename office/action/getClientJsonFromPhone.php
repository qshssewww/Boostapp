<?php
require_once '../../app/initcron.php';

if(Auth::check()) {
    $CompanyNum = Auth::user()->CompanyNum;
    $resArr = ["results" => []];
    if(isset($_GET['query'])) {
        $queryStr = $_GET['query'];
        $Clients = DB::table('client')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('parentClientId', '=', 0)
            ->where(function($q) use ($queryStr) {
                $q->where('ContactMobile','like', '%'.$queryStr.'%')->orWhere('ContactMobile','like', '%'.substr($queryStr,1,strlen($queryStr)).'%');
            })->limit(5)->get();

        $ClientsSearch = [];
        foreach ($Clients as $Client) {
            if($Client->Status == 0) {
                $status = 'bg-success';
            }
            elseif($Client->Status == 1) {
                $status = 'bg-danger';
            }
            else {
                $status = 'bg-warning';
            }
            $img = !empty($Client->ProfileImage) ? $Client->ProfileImage : 'https://ui-avatars.com/api/?length=1&name='.$Client->FirstName.'&background=f3f3f4&color=000&font-size=0.5';
            $obj = [
                'name' => $Client->CompanyName,
                'firstName' => $Client->FirstName,
                'lastName' => $Client->LastName,
                'email' => $Client->Email,
                'url' => '/office/ClientProfile.php?u=' . $Client->id,
                'img' => $img,
                'phone' => $Client->ContactMobile,
                'brand' => $Client->BrandName,
                'status' => $status,
                'id' => $Client->id,
                'parentId' => $Client->parentClientId
            ];
            $ClientsSearch[] = $obj;
        }

        $resArr["results"] = $ClientsSearch;
        echo json_encode($resArr, JSON_UNESCAPED_UNICODE);
    }
}

