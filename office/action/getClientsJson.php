<?php
require_once '../../app/initcron.php';
if(Auth::check()) {

    $CompanyNum = Auth::user()->CompanyNum;
    $resArr = ["results" => []];
    if (isset($_GET['query'])) {
        $input = $_GET['query'];
        $input2 = (strpos($input, '0') === 0) ? substr($input, 1) : $input;


        $Clients = DB::table('client')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('isRandomClient', 0)
            ->where(function ($q) use ($input, $input2) {
                $q->where('CompanyName', 'like', '%' . $input . '%')
            ->Orwhere('FirstName','like', '%'.$input . '%')
            ->Orwhere('LastName','like', '%'.$input . '%')
            ->Orwhere('CompanyId','like', '%'.$input . '%')
            ->Orwhere('ContactMobile','LIKE', '%' . $input . '%')
            ->Orwhere('ContactMobile','LIKE', '%' . $input2 . '%')
            ->Orwhere('Email','like', '%'.$input . '%');
            })->limit(20)
            ->orderBy('Status', 'ASC')
            ->get();

        $ClientsSearch = array();
        foreach ($Clients as $Client) {
            if ($Client->Status == 0) {
                $status = 'bg-success';
            }
            elseif ($Client->Status == 1) {
                $status = 'bg-danger';
            }
            else {
                $status = 'bg-warning';
            }
            $img = !empty($Client->ProfileImage) ? $Client->ProfileImage : 'https://ui-avatars.com/api/?length=1&name=' . $Client->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
            $obj = [
                'name' => $Client->CompanyName,
                'url' => '/office/ClientProfile.php?u=' . $Client->id,
                'email' => $Client->Email,
                'img' => $img,
                'phone' => $Client->ContactMobile,
                'brand' => $Client->BrandName,
                'status' => $status,
                'id' => $Client->id,
                'gender' => $Client->Gender
            ];
            array_push($ClientsSearch, $obj);
        }
        $resArr["results"] = $ClientsSearch;
        echo json_encode($resArr, JSON_UNESCAPED_UNICODE);
    }
}

