<?php
require_once '../app/init.php';
if(Auth::user()->role_id == 1) {
    $CompanyNum = 904346;
    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
    $activeClients = DB::table('client')->where('CompanyNum', $CompanyNum)->where('Status', 0)->get();
    $number = 1;

        foreach ($activeClients as $ClientInfo) {

            $isMinor = $ClientInfo->parentClientId != 0;
            if ($isMinor) {
                $ClientId = $ClientInfo->parentClientId;
                $parent = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $ClientId)->first();
                $CompanyName = htmlentities($parent->CompanyName);
                $FirstName = htmlentities($parent->FirstName);
                $LastName = htmlentities($parent->LastName);
                $ContactMobile = !empty($parent->ContactMobile) ? $parent->ContactMobile : '';
                $Email = trim($parent->Email);
            } else {
                $ClientId = $ClientInfo->id;
                $CompanyName = htmlentities($ClientInfo->CompanyName);
                $FirstName = htmlentities($ClientInfo->FirstName);
                $LastName = htmlentities($ClientInfo->LastName);
                $ContactMobile = !empty($ClientInfo->ContactMobile) ? $ClientInfo->ContactMobile : '';
                $Email = trim($ClientInfo->Email);
                $ClientId = $ClientInfo->id;
            }

            if (empty($ContactMobile)) {
                echo($number . " " . $ClientId . " " . lang('mobile_req_ajax') . "<br>");
                $number++;
                continue;
            }
            $ContactMobile = substr($ContactMobile, 0, 1) == '0' ? substr(
                $ContactMobile,
                1,
                strlen($ContactMobile)
            ) : $ContactMobile;
            $ContactMobile = substr($ContactMobile, 0, 4) == '+972' ? $ContactMobile : '+972' . $ContactMobile;

            $UserId = Auth::user()->id;
            $MakeRandomPass = mt_rand(100000, 999999);
            $password = Hash::make(trim($MakeRandomPass));
            $GetUsersId = '0';

//user for an adult
            $AppUsers = DB::table('boostapplogin.users')->where('newUsername', '=', $ContactMobile)->first();
            if (empty($AppUsers)) {
                $AppUserId = DB::table('boostapplogin.users')->insertGetId(
                    array(
                        'username' => $Email,
                        'email' => $Email,
                        'newUsername' => $ContactMobile,
                        'password' => $password,
                        'display_name' => $CompanyName,
                        'FirstName' => $FirstName,
                        'LastName' => $LastName,
                        'ContactMobile' => $ContactMobile,
                        'AppLoginId' => $ContactMobile,
                        'status' => '1'
                    )
                );
            } else {
                $AppUserId = $AppUsers->id;
                if (!$isMinor) {
                    DB::table('boostapplogin.users')
                        ->where('id', $AppUserId)
                        ->update(array('email' => trim($Email), 'password' => $password, 'PassAct' => 0));
                }
            }
            $status = 0;
//      minor user section
            if ($isMinor) {
                $minorUser = DB::table('boostapplogin.users')
                    ->where('newUsername', '=', '-1')
                    ->where('parentId', $AppUserId)
                    ->where('display_name', '=', $ClientInfo->CompanyName)
                    ->first();
                $minorStudio = DB::table('boostapplogin.studio')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('ClientId', $ClientInfo->id)
                    ->first();
                $status = 1;
                if (empty($minorUser)) {
                    $minorUserId = DB::table('boostapplogin.users')->insertGetId(
                        array(
                            'username' => '',
                            'email' => '',
                            'newUsername' => '-1',
                            'password' => $password,
                            'display_name' => htmlentities($ClientInfo->CompanyName),
                            'FirstName' => htmlentities($ClientInfo->FirstName),
                            'LastName' => htmlentities($ClientInfo->LastName),
                            'ContactMobile' => '',
                            'AppLoginId' => $ContactMobile,
                            'status' => '1',
                            'parentId' => $AppUserId
                        )
                    );
                } else {
                    $minorUserId = $minorUser->id;
                }

                if (empty($minorStudio)) {
                    $minorStudioId = DB::table('boostapplogin.studio')->insertGetId(
                        array(
                            'StudioUrl' => $SettingsInfo->StudioUrl,
                            'StudioName' => $SettingsInfo->AppName,
                            'CompanyNum' => $CompanyNum,
                            'UserId' => $minorUserId,
                            'ClientId' => $ClientInfo->id,
                            'LastDate' => date('Y-m-d'),
                            'LastTime' => date('H:i:s'),
                            'Memotag' => $SettingsInfo->Memotag,
                            'Folder' => $SettingsInfo->Folder,
                            'Takanon' => $ClientInfo->Takanon,
                            'Medical' => $ClientInfo->Medical
                        )
                    );
                } else {
                    $minorStudioId = $minorStudio->id;
                    $updateUserId = DB::table('boostapplogin.studio')
                        ->where('id', '=', $minorStudioId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(
                            array(
                                'UserId' => $minorUserId,
                                'LastDate' => date('Y-m-d'),
                                'LastTime' => date('H:i:s')
                            )
                        );
                }

                echo ($number." minor user ClientID: ".$ClientInfo->id.",minorUserId: ".$minorUserId.", minorStudioId:  ".$minorStudioId. "<br>");
                $number++;
            }
            // end minor user section
            $CheckAppUsers = DB::table('boostapplogin.studio')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('ClientId',$ClientId)
                ->first();

            if (empty($CheckAppUsers)) {
                $AppStudio = DB::table('boostapplogin.studio')->insertGetId(
                    array(
                        'StudioUrl' => $SettingsInfo->StudioUrl,
                        'StudioName' => $SettingsInfo->AppName,
                        'CompanyNum' => $CompanyNum,
                        'UserId' => $AppUserId,
                        'ClientId' => $ClientId,
                        'Status' => $status,
                        'LastDate' => date('Y-m-d'),
                        'LastTime' => date('H:i:s'),
                        'Memotag' => $SettingsInfo->Memotag,
                        'Folder' => $SettingsInfo->Folder,
                        'Takanon' => $ClientInfo->Takanon,
                        'Medical' => $ClientInfo->Medical
                    )
                );
            } else {
                $AppStudio = $CheckAppUsers->id;
                if (!empty($CheckAppUsers) && $CheckAppUsers->UserId != $AppUserId) {
                    DB::table('boostapplogin.studio')
                        ->where('id', $CheckAppUsers->id)
                        ->where('CompanyNum', $CompanyNum)
                        ->update(
                            array('UserId' => $AppUserId, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s'))
                        );
                }
            }
            echo ($number." adult user ClientID: ".$ClientId.",UserId: ".$AppUserId.", StudioId:  ".$AppStudio. "<br>");
            $number++;
        }

}
