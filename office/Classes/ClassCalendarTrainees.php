<?php
require_once '../../app/init.php';
require_once "ClassStudioDate.php";
require_once "ItemRoles.php";
require_once "../services/GoogleCalendarService.php";

Class ClassCalendarTrainees {
    public function setNewCalendarTrainee($data) {
        if (Auth::guest()) exit;
        $CompanyNum = Auth::user()->CompanyNum;
        $UserId = Auth::user()->id;
            if($data['Trainee']->isNew=="1"){
                $newUser= DB::table('boostapp.client')->insertGetId(array(
                    'CompanyNum'=>  $CompanyNum,
                    'CompanyName'=>$data['Trainee']->name,
                    'ContactMobile'=>$data['Trainee']->phone,
                    'ContactPhone'=>$data['Trainee']->phone,
                  ));
                $ClientId = $newUser;
  
            }else{
                $ClientId = $data['Trainee']->id;
            }
            if(!$data['Trainee']->chargeMembership){
                 $client=DB::table('boostapp.client')->where('id',"=",$ClientId)->first();
                 $clientAmount=(float)$client->BalanceAmount;
                 $newClassAmount= $data['Trainee']->dontCharge?(float)0:(float)$data['Trainee']->chargeAmount;
                 $newClientAmount= $clientAmount+$newClassAmount;
                 DB::table('boostapp.client')->where('id',"=",$ClientId)->update(array(
                     'BalanceAmount'=>$newClientAmount
                 ));
                $defaultItem= DB::table('boostapp.items')->where('CompanyNum','=',$CompanyNum)->where('isPaymentForSingleClass',"=","1")->first();
                if(!$defaultItem){
                    $newId=DB::table('boostapp.items')->insertGetId(array(
                      'isPaymentForSingleClass'=>1,
                      'CompanyNum'=>  $CompanyNum,
                      'Department'=>2,
                      "MemberShip"=>'BA999',
                      'ItemName'=>"שיבוץ שיעור",
                      'Status'=>"0",
                      'BalanceClass'=>"1"
                    ));
                    DB::table('boostapp.items_roles')->insertGetId(array(
                        'CompanyNum'=>  $CompanyNum,
                        'ItemId'=>$newId,
                        'Class'=>"BA999",
                        'Group'=>'Class',
                        'Item'=>'Class',
                        'Value'=>'',
                        'UserId'=>$UserId,
                        'GroupId'=> $CompanyNum . $newId . "-1"

                    ));
                    $newDefaultItem = DB::table('boostapp.items')->where('CompanyNum','=',$CompanyNum)->where('isPaymentForSingleClass',"=","1")->first();
                }
                $itemPrice=$data['Trainee']->dontCharge?(float)0:(float)$data['Trainee']->chargeAmount;
                $newActivityId= DB::table('boostapp.client_activities')->insertGetId(array(
                    'Department'=>2,
                    'ClientId'=>$ClientId,
                    'CompanyNum'=>  $CompanyNum,
                    'ItemId'=>$defaultItem?$defaultItem->id:$newDefaultItem->id,
                    'ItemText'=>"שיבוץ שיעור",
                    'ItemPrice'=>$data['Trainee']->dontCharge?(float)0:(float)$data['Trainee']->chargeAmount,
                    'Vat'=>'17',
                    'ItemPriceVat'=>($itemPrice - ($itemPrice*0.17)),
                    'ItemPriceVatDiscount'=>($itemPrice - ($itemPrice*0.17)),
                    'BalanceValue'=>"1",
                    'ActBalanceValue'=>"1",
                    'TrueBalanceValue'=>"1",
                    "MemberShip"=>'BA999',
                    'isPaymentForSingleClass'=>"1"
                  ));
                $ActivityId = $newActivityId;
            }else{
                $ActivityId = $data['Trainee']->chargeMembership;
            }
            $ClassId = $data['ClassId'];
            $WatingListSort = '0';
            $DeviceId = '0';
            $Remarks = '';
            $WatingListSort = '0';
            $TestClass = '1';
            if (!$ActivityId || $ActivityId == '') {
                json_message('יש לבחור מנוי אחד על מנת לשבץ את הלקוח לשיעור', false);
                exit;
            }else{
                $Dates = date('Y-m-d H:i:s');
                $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
                $ClassInfo = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->first();
                $ActivityInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityId)->first();
                $TrueClientId = $ActivityInfo->TrueClientId;
                $FixClientId = $ActivityInfo->ClientId;
                $ItemId = $ActivityInfo->ItemId;

                $CheckItemsRole = ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $ItemId, $ClassInfo->ClassNameType);
                if ($CheckItemsRole) {
                    $TrueClasessFinal = $CheckItemsRole->GroupId;
                } else {
                    json_message('במנוי זה לא הוגדר הזמנת שיעור זה', false);
                    exit;
                }
                //// בדיקת לקוח קיים בשיעור
                // if ($TrueClientId == '0' || $ClientId == $FixClientId) {
                //     $CheckClientClass = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('TrueClientId', '=', '0')->first();
                // } else {
                //     $CheckClientClass = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('TrueClientId', '=', $ClientId)->first();
                // }

                //// בדיקת הגדרות שיעור
                $FloorInfo = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassInfo->Floor)->first();
                $ReminderStatus = $ClassInfo->SendReminder;
                $TypeReminder = $ClassInfo->TypeReminder;
                $TimeReminder = $ClassInfo->TimeReminder;
                $CancelLaw = $ClassInfo->CancelLaw;
                $CancelDay = $ClassInfo->CancelDay;
                $CancelDayMinus = $ClassInfo->CancelDayMinus;
                $CancelDayName = $ClassInfo->CancelDayName;
                $CancelTillTime = $ClassInfo->CancelTillTime;
                $ClassName = $ClassInfo->ClassName;
                $ClassNameType = $ClassInfo->ClassNameType;
                $ClassDate = $ClassInfo->StartDate;
                $ClassStartTime = $ClassInfo->StartTime;
                $ClassEndTime = $ClassInfo->EndTime;
                if ($ReminderStatus == '1') {
                    $ReminderStatus = '2';
                }
                if ($CancelLaw == '1') {
                    $CancelDate = $ClassDate;
                    $CancelDay = '';
                    $CancelTime = $CancelTillTime;
                } else if ($CancelLaw == '2') {
                    $CancelDate = date("Y-m-d", strtotime('-1 day', strtotime($ClassDate)));
                    $CancelDay = '';
                    $CancelTime = $CancelTillTime;
                } else if ($CancelLaw == '3') {
                    $CancelDayNum = '-' . $CancelDayMinus . ' day';
                    $CancelDate = date("Y-m-d", strtotime($CancelDayNum, strtotime($ClassDate)));
                    $CancelDay = $CancelDayName;
                    $CancelTime = $CancelTillTime;
                } else if ($CancelLaw == '4') {
                    $CancelDate = '';
                    $CancelDay = '';
                    $CancelTime = '';
                } else if ($CancelLaw == '5') {
                    $CancelDate = '';
                    $CancelDay = '';
                    $CancelTime = '';
                }
                $CancelJson = '';
                $CancelJson.= '{"data": [';
                $CancelJson.= '{"CancelDate": "' . $CancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
                $CancelJson.= ']}';
                if ($TypeReminder == '1') {
                    $ReminderDate = $ClassDate;
                } else {
                    $ReminderDate = date("Y-m-d", strtotime('-1 day', strtotime($ClassDate)));
                }
                /// בדיקת הגדרות אפליקציה
                $DifrentTime = $AppSettings->DifrentTime; /// לאפשר החלפת שיעור באותו היום? 1 כן
                $DifrentTimeMin = $AppSettings->DifrentTimeMin; /// זמן בדקות
                $Watinglist = $AppSettings->Watinglist; /// בדיקת שיבוץ אוטומטי 2 לא
                $WatinglistMin = $AppSettings->WatinglistMin; // זמן תגובה ללא שיבוץ אוטומטי
                $SendSMSWeb = $AppSettings->SendSMS;
                if ($Watinglist == '2') {
                    $TimeAutoWatinglist = null;
                    $StatusTimeAutoWatinglist = '1';
                } else {
                    $TimeAutoWatinglist = null;
                    $StatusTimeAutoWatinglist = '0';
                }
                if ($DifrentTime == '1') {
                    $ItemsMin = '-' . $DifrentTimeMin . ' minutes';
                    $time = strtotime($ClassStartTime);
                    $ChangeClassTime = date("H:i", strtotime($ItemsMin, $time));
                    $ChangeClassStatus = '0';
                } else {
                    $ChangeClassTime = null;
                    $ChangeClassStatus = '1';
                }
                /// נתוני המנוי
                $Department = $ActivityInfo->Department;
                $MemberShip = $ActivityInfo->MemberShip;
                $ItemText = $ActivityInfo->ItemText;
                $LimitClass = $ActivityInfo->LimitClass;
                /// נתוני מנוי פנימי
                $MemberInfo = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ActivityInfo->ItemId)->first();
                $StartTime = $MemberInfo->StartTime;
                $EndTime = $MemberInfo->EndTime;
                $CancelLImit = $MemberInfo->CancelLImit;
                $ClassSameDay = $MemberInfo->ClassSameDay;
                $BalanceClass = $MemberInfo->BalanceClass;
                $LimitClassMorning = $MemberInfo->LimitClassMorning;
                $LimitClassEvening = $MemberInfo->LimitClassEvening;
                $LimitClassMonth = $MemberInfo->LimitClassMonth;
                $TrueBalanceClass = $ActivityInfo->TrueBalanceValue;
                /// בדיקת מצב שיעור וקביעת סטטוס ראשוני
                $ClassCount = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
                if ($ClassCount >= $ClassInfo->MaxClient) {
                    $Status = '9'; /// ממתין ברשימת המתנה
                    $StatusCount = '1';
                    /// עדכון רשימת המתנה
                    $WatingListSorts = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('Status', '=', '9')->where('CompanyNum', $CompanyNum)->where('WatingListSort', '!=', '0')->orderBy('WatingListSort', 'DESC')->first();
                    if ($WatingListSorts->WatingListSort != '') {
                        $WatingListSort = $WatingListSorts->WatingListSort + 1;
                    }
                } else {
                    $Status = '1'; /// שובץ פעיל/מומש
                    $StatusCount = '0';
                    if ($Department == '2') {
                        ////  ניקוב כרטיסיה
                        $TrueBalanceValue = $ActivityInfo->TrueBalanceValue - 1;
                        DB::table('client_activities')->where('id', $ActivityInfo->id)->where('CompanyNum', $CompanyNum)->update(array('TrueBalanceValue' => $TrueBalanceValue));
                    } else if ($Department == '3') {
                        $Status = '11'; /// שיעור ניסיון
                        $StatusCount = '0';
                        $TestClass = '2';
                        ////  ניקוב כרטיסיה
                        $TrueBalanceValue = $ActivityInfo->TrueBalanceValue - 1;
                        DB::table('client_activities')->where('id', $ActivityInfo->id)->where('CompanyNum', $CompanyNum)->update(array('TrueBalanceValue' => $TrueBalanceValue));
                    }
                }
                $CheckNewStatus = DB::table('class_status')->where('id', '=', $Status)->first();
                $StatusTitle = $CheckNewStatus->Title;
                $UserName = Auth::user()->display_name;
                $StatusJson = '';
                $StatusJson.= '{"data": [';
                $StatusJson.= '{"Dates": "' . $Dates . '", "UserId": "' . $UserId . '", "Status": "' . $Status . '", "StatusTitle": "' . $StatusTitle . '", "UserName": "' . $UserName . '"}';
                $StatusJson.= ']}';
                $WeekNumber = date("Wo", strtotime("+1 day", strtotime($ClassInfo->StartDate)));
                $ChangeClassDate = null;
                if ($DifrentTime == '1') {
                    $ClassDateDifrent = $ClassDate . ' ' . $ClassStartTime;
                    $CancelDayNum = '-' . $DifrentTimeMin . ' minutes';
                    $ChangeClassDate = date("Y-m-d H:i:s", strtotime($CancelDayNum, strtotime($ClassDateDifrent)));
                }
                //// שמירת נתונים בטבלה
                if ($TrueClientId == '0' || $ClientId == $FixClientId) {
                    $AddClassDesk = DB::table('classstudio_act')->insertGetId(array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'TrueClientId' => '0', 'ClassId' => $ClassId, 'ClassNameType' => $ClassNameType, 'ClassName' => $ClassName, 'ClassDate' => $ClassDate, 'ClassStartTime' => $ClassStartTime, 'ClassEndTime' => $ClassEndTime, 'ClientActivitiesId' => $ActivityId, 'Department' => $Department, 'MemberShip' => $MemberShip, 'ItemText' => $ItemText, 'WeekNumber' => $WeekNumber, 'DeviceId' => $DeviceId, 'Remarks' => $Remarks, 'StatusCount' => $StatusCount, 'Status' => $Status, 'Dates' => $Dates, 'UserId' => $UserId, 'CancelJson' => $CancelJson, 'StatusJson' => $StatusJson, 'ReminderStatus' => $ReminderStatus, 'ReminderDate' => $ReminderDate, 'ReminderTime' => $TimeReminder, 'WatinglistMin' => $WatinglistMin, 'TimeAutoWatinglist' => $TimeAutoWatinglist, 'StatusTimeAutoWatinglist' => $StatusTimeAutoWatinglist, 'SendSMSWeb' => $SendSMSWeb, 'ChangeClassStatus' => $ChangeClassStatus, 'GuideId' => $ClassInfo->GuideId, 'FloorId' => $ClassInfo->Floor, 'WatingListSort' => $WatingListSort, 'GroupNumber' => $ClassInfo->GroupNumber, 'TestClass' => $TestClass, 'DayNum' => $ClassInfo->DayNum, 'Day' => $ClassInfo->Day, 'TrueClasess' => $TrueClasessFinal, 'ChangeClassDate' => $ChangeClassDate, 'FixClientId' => $ClientId));
                } else {
                    $AddClassDesk = DB::table('classstudio_act')->insertGetId(array('CompanyNum' => $CompanyNum, 'ClientId' => $FixClientId, 'TrueClientId' => $ClientId, 'ClassId' => $ClassId, 'ClassNameType' => $ClassNameType, 'ClassName' => $ClassName, 'ClassDate' => $ClassDate, 'ClassStartTime' => $ClassStartTime, 'ClassEndTime' => $ClassEndTime, 'ClientActivitiesId' => $ActivityId, 'Department' => $Department, 'MemberShip' => $MemberShip, 'ItemText' => $ItemText, 'WeekNumber' => $WeekNumber, 'DeviceId' => $DeviceId, 'Remarks' => $Remarks, 'StatusCount' => $StatusCount, 'Status' => $Status, 'Dates' => $Dates, 'UserId' => $UserId, 'CancelJson' => $CancelJson, 'StatusJson' => $StatusJson, 'ReminderStatus' => $ReminderStatus, 'ReminderDate' => $ReminderDate, 'ReminderTime' => $TimeReminder, 'WatinglistMin' => $WatinglistMin, 'TimeAutoWatinglist' => $TimeAutoWatinglist, 'StatusTimeAutoWatinglist' => $StatusTimeAutoWatinglist, 'SendSMSWeb' => $SendSMSWeb, 'ChangeClassStatus' => $ChangeClassStatus, 'GuideId' => $ClassInfo->GuideId, 'FloorId' => $ClassInfo->Floor, 'WatingListSort' => $WatingListSort, 'GroupNumber' => $ClassInfo->GroupNumber, 'TestClass' => $TestClass, 'DayNum' => $ClassInfo->DayNum, 'Day' => $ClassInfo->Day, 'TrueClasess' => $TrueClasessFinal, 'ChangeClassDate' => $ChangeClassDate, 'FixClientId' => $ClientId));
                }
                GoogleCalendarService::checkChangedAndSync($AddClassDesk, [], true);
                //// עדכון שיעור ברשימת משתתפים
                if ($ClassInfo) {
                    $update = ClassStudioDate::updateClassRegistersCount($ClassInfo);
                }
//                $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '0')->count();
//                $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $ClassId)->where('CompanyNum', '=', $CompanyNum)->where('StatusCount', '=', '1')->count();
//                $ClientRegisterRegular1 = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->where('RegularClassType', '=', '1')->where('StatusType', '=', '12')->count();
//                $ClientRegisterRegularWating1 = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->where('RegularClassType', '=', '1')->where('StatusType', '=', '9')->count();
//                $ClientRegisterRegular2 = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->where('RegularClassType', '=', '2')->where('EndDate', '>=', $ClassInfo->StartDate)->where('StatusType', '=', '12')->count();
//                $ClientRegisterRegularWating2 = DB::table('classstudio_dateregular')->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $ClassInfo->GroupNumber)->where('DayNum', '=', $ClassInfo->DayNum)->where('ClassTime', '=', $ClassInfo->StartTime)->where('Floor', '=', $ClassInfo->Floor)->where('RegularClassType', '=', '2')->where('EndDate', '>=', $ClassInfo->StartDate)->where('StatusType', '=', '9')->count();
//                $ClientRegisterRegular = $ClientRegisterRegular1 + $ClientRegisterRegular2;
//                $ClientRegisterRegularWating = $ClientRegisterRegularWating1 + $ClientRegisterRegularWating2;
//                DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList, 'ClientRegisterRegular' => $ClientRegisterRegular, 'ClientRegisterRegularWating' => $ClientRegisterRegularWating));
                ///// Class Log
                DB::table('classlog')->insertGetId(array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassId, 'ClientId' => $ClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => $UserId, 'numOfClients' => $ClientRegister));
                /////////////////////////////////////////
            }
    }
}
