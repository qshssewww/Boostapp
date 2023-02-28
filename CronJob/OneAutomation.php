<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/ItemRoles.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/CompanyProductSettings.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/ClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

        	$Vaild_TypeOption = array(
			1 => "day",
			2 => "week",
			3 => "month",
            4 => "year"    
	        );	

//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////

try {

    $Clients = DB::table('client')->where('AutoInsert', '=', '1')->whereIn('Status', array(0, 2))->get();

    foreach ($Clients as $Client) {
        if ($Client->Brands == '1049') {
            $AutomationInfo = DB::table('boostapp.automation')->where('CompanyNum', '=', $Client->CompanyNum)->where('Category', '=', '1')->where('Type', '=', '1')->where('Status', '=', '0')->first();
        } else {
            $AutomationInfo = DB::table('boostapp.automation')->where('CompanyNum', '=', $Client->CompanyNum)->where('Category', '=', '2')->where('Type', '=', '1')->where('Status', '=', '0')->first();
        }
        $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $Client->CompanyNum)->where('Status', '=', '0')->first();
        if (@$CheckSettings->id != '' && @$AutomationInfo->Value != '') {


            $ClientId = $Client->id;
            $ValueId = $AutomationInfo->Value;
            $CompanyNum = $Client->CompanyNum;
            $FirstDate = '0';
            $FirstDateStatus = '0';

            $CompanyNum = $CompanyNum;
            $SettingsInfo = DB::table('boostapp.settings')->where('CompanyNum', '=', $CompanyNum)->first();
            $AppSettings = DB::table('boostapp.appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
            $BrandsMain = $SettingsInfo->BrandsMain;
            $MembershipType = @$AppSettings->MembershipType;
            $Vat = $SettingsInfo->Vat;
            $ClientId = $ClientId;
            $Items = $ValueId;
            $StartDates = @$_POST['ClassDate'];
            $Vaild_LastCalss = @$AutomationInfo->VaildType;

            $ItemNamep = trim(@$_POST['ItemNamep']);
            $ItemPricep = trim(@$_POST['ItemPricep']);
            $ClassDateEnd = @$_POST['ClassDateEnd'];

            if (@$Items == '') {
                json_message(lang('select_membership_ajax'), false);
                exit;
            }

            if (@$StartDates == '') {
                $Today = date('Y-m-d');
                $StartDate = date('Y-m-d');
            } else {
                $Today = $StartDates;
                $StartDate = $StartDates;
            }

            /// קליטת פרטי פעילות

            $ItemsInfo = DB::table('boostapp.items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Items)->first();


            $ItemText = $ItemsInfo->ItemName;
            if ($ItemNamep != '') {
                $ItemText = $ItemNamep;
            }


            $ItemPrice = $ItemsInfo->ItemPrice;
            $ItemPriceVat = $ItemsInfo->ItemPriceVat;


            $Department = $ItemsInfo->Department; // חוק מנוי
            $MemberShip = $ItemsInfo->MemberShip; // סוג מנוי

            $Vaild = $ItemsInfo->Vaild; // חישוב תוקף
            $Vaild_Type = $ItemsInfo->Vaild_Type; // סוג חישוב
            $LimitClass = $ItemsInfo->LimitClass; // הגבלת שיעורים

            $CompanyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum);
            $NotificationDays = $CompanyProductSettings->NotificationDays ?? 0; // התראה לפני סוף מנוי

            $BalanceClass = $ItemsInfo->BalanceClass; // כמות שיעורים
            $MinusCards = $CompanyProductSettings->offsetMemberships ?? 1; // קיזוז מכרטיסיה קודמת
            $StartTime = $ItemsInfo->StartTime; // הגבלת הזמנת שיעורים
            $EndTime = $ItemsInfo->EndTime; // הגבלת הזמנת שיעורים
            $CancelLImit = $ItemsInfo->CancelLImit; // ביטול הגבלה
            $ClassSameDay = $ItemsInfo->ClassSameDay; // הזמנת שיעור באותו היום
            $FreezMemberShip = $ItemsInfo->FreezMemberShip; // ניתן להקפאה?
            $FreezMemberShipDays = $ItemsInfo->FreezMemberShipDays; // מספר ימים מקסימלי להקפאה
            $FreezMemberShipCount = $ItemsInfo->FreezMemberShipCount; // מספר פעמים שניתן להקפיא מנוי  


            $LimitClassMorning = $ItemsInfo->LimitClassMorning;
            $LimitClassEvening = $ItemsInfo->LimitClassEvening;
            $LimitClassMonth = $ItemsInfo->LimitClassMonth;

            $TrueBalanceClass = $BalanceClass;
            $BalanceValueLog = NULL;


            $MemberShipRule = '';
            $MemberShipRule .= '{"data": [';
            $MemberShipRule .= '{"LimitClass": "' . $LimitClass . '", "NotificationDays": "' . $NotificationDays . '", "StartTime": "' . $StartTime . '", "EndTime": "' . $EndTime . '", "CancelLImit": "' . $CancelLImit . '", "ClassSameDay": "' . $ClassSameDay . '", "FreezMemberShip": "' . $FreezMemberShip . '", "FreezMemberShipDays": "' . $FreezMemberShipDays . '", "FreezMemberShipCount": "' . $FreezMemberShipCount . '", "LimitClassMorning": "' . $LimitClassMorning . '", "LimitClassEvening": "' . $LimitClassEvening . '", "LimitClassMonth": "' . $LimitClassMonth . '"}';
            $MemberShipRule .= ']}';


            // מנוי תקופתי   
            if ($Department == '1') {

                /// חישוב תוקף מהשיעור האחרון במידה וקיים
                if ($Vaild_LastCalss == '2') {
                    /// חישוב תוקף מהמנוי האחרון במידה וקיים
                    if ($MembershipType == '0') {
                        $LastClass = DB::table('boostapp.client_activities')
                            ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('MemberShip', '=', $MemberShip)->orderBy('id', 'DESC')->first();
                    } else {
                        $LastClass = DB::table('boostapp.client_activities')
                            ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->orderBy('id', 'DESC')->first();
                    }

                    if (@$LastClass->TrueDate != '') {
                        $StartDate = $LastClass->TrueDate;
                    } else {
                        $StartDate = $StartDate;
                    }

                } else if ($Vaild_LastCalss == '3') {
                    /// חישוב תוקף מהשיעור האחרון במידה וקיים
                    if ($MembershipType == '0') {
                        $LastClass = DB::table('boostapp.classstudio_act')
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->where('FixClientId', '=', $ClientId)
                            ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                            ->where('MemberShip', '=', $MemberShip)
                            ->orderBy('ClassDate', 'DESC')
                            ->first();
                    } else {
                        $LastClass = DB::table('boostapp.classstudio_act')
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->where('FixClientId', '=', $ClientId)
                            ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                            ->orderBy('ClassDate', 'DESC')
                            ->first();
                    }

                    if (@$LastClass->ClassDate != '') {
                        $StartDate = $LastClass->ClassDate;
                    } else {
                        $StartDate = $StartDate;
                    }

                } else if ($Vaild_LastCalss == '5') {
                    $StartDate = $StartDate;
                    $FirstDate = '1';
                    $FirstDateStatus = '1';
                }

                $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                $time = strtotime($StartDate);
                $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));


                if ($Vaild_LastCalss == '5') {
                    $StartDate = $StartDate;
                    $FirstDate = '1';
                    $FirstDateStatus = '1';
                }

            } // כרטיסיה
            else if ($Department == '2') {

                $ClassDate = NULL;

                /// חישוב תוקף
                if ($Vaild != '0') {


                    /// חישוב תוקף מהשיעור האחרון במידה וקיים
                    if ($Vaild_LastCalss == '2') {
                        /// חישוב תוקף מהמנוי האחרון במידה וקיים
                        if ($MembershipType == '0') {
                            $LastClass = DB::table('boostapp.client_activities')
                                ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('MemberShip', '=', $MemberShip)->orderBy('id', 'DESC')->first();
                        } else {
                            $LastClass = DB::table('boostapp.client_activities')
                                ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->orderBy('id', 'DESC')->first();
                        }

                        if (@$LastClass->TrueDate != '') {
                            $StartDate = $LastClass->TrueDate;
                        } else {
                            $StartDate = $StartDate;
                        }

                    } else if ($Vaild_LastCalss == '3') {
                        /// חישוב תוקף מהשיעור האחרון במידה וקיים
                        if ($MembershipType == '0') {
                            $LastClass = DB::table('boostapp.classstudio_act')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('FixClientId', '=', $ClientId)
                                ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                                ->where('MemberShip', '=', $MemberShip)
                                ->orderBy('ClassDate', 'DESC')
                                ->first();
                        } else {
                            $LastClass = DB::table('boostapp.classstudio_act')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('FixClientId', '=', $ClientId)
                                ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                                ->orderBy('ClassDate', 'DESC')
                                ->first();
                        }

                        if (@$LastClass->ClassDate != '') {
                            $StartDate = $LastClass->ClassDate;
                        } else {
                            $StartDate = $StartDate;
                        }

                    } else if ($Vaild_LastCalss == '5') {
                        $StartDate = $StartDate;
                        $FirstDate = '1';
                        $FirstDateStatus = '1';
                    }


                    $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                    $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                    $time = strtotime($StartDate);
                    $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));

                    if ($Vaild_LastCalss == '5') {
                        $StartDate = $StartDate;
                        $FirstDate = '1';
                        $FirstDateStatus = '1';
                    }

                }

            } // התנסות
            else if ($Department == '3') {

                $ClassDate = NULL;

                /// חישוב תוקף
                if ($Vaild != '0') {


                    /// חישוב תוקף מהשיעור האחרון במידה וקיים
                    if ($Vaild_LastCalss == '2') {
                        /// חישוב תוקף מהמנוי האחרון במידה וקיים
                        if ($MembershipType == '0') {
                            $LastClass = DB::table('client_activities')
                                ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('MemberShip', '=', $MemberShip)->orderBy('id', 'DESC')->first();
                        } else {
                            $LastClass = DB::table('client_activities')
                                ->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->orderBy('id', 'DESC')->first();
                        }

                        if (@$LastClass->TrueDate != '') {
                            $StartDate = $LastClass->TrueDate;
                        } else {
                            $StartDate = $StartDate;
                        }

                    } else if ($Vaild_LastCalss == '3') {
                        /// חישוב תוקף מהשיעור האחרון במידה וקיים
                        if ($MembershipType == '0') {
                            $LastClass = DB::table('classstudio_act')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('FixClientId', '=', $ClientId)
                                ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                                ->where('MemberShip', '=', $MemberShip)
                                ->orderBy('ClassDate', 'DESC')
                                ->first();
                        } else {
                            $LastClass = DB::table('classstudio_act')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('FixClientId', '=', $ClientId)
                                ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23])
                                ->orderBy('ClassDate', 'DESC')
                                ->first();
                        }

                        if (@$LastClass->ClassDate != '') {
                            $StartDate = $LastClass->ClassDate;
                        } else {
                            $StartDate = $StartDate;
                        }

                    } else if ($Vaild_LastCalss == '5') {
                        $StartDate = $StartDate;
                        $FirstDate = '1';
                        $FirstDateStatus = '1';
                    }


                    $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                    $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                    $time = strtotime($StartDate);
                    $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));

                    if ($Vaild_LastCalss == '5') {
                        $StartDate = $StartDate;
                        $FirstDate = '1';
                        $FirstDateStatus = '1';
                    }


                }


                $MemberShipRule = NULL;
                $LimitClass = '999';


            } // פריט כללי
            else if ($Department == '4') {
                $ClassDate = NULL;
                $MemberShipRule = NULL;
                $LimitClass = '0';
                $BalanceClass = '0';
            }

            // מספור מספר המנויים שהלקוח רכש   
            $CardNum = DB::table('boostapp.client_activities')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->count();
            $CardNumber = $CardNum + 1;

            if ($ClassDateEnd != '' && $Department == '1' || $ClassDateEnd != '' && $Department == '2') {
                $ClassDate = $ClassDateEnd;
            }
            /// הכנסת נתונים ועדכון טבלאות   

            $UserId = '0';
            $Dates = date('Y-m-d G:i:s');


            $Vaild_TypeOptions = @$Vaild_TypeOption['1'];
            $ItemsTime = '-' . $NotificationDays . ' ' . $Vaild_TypeOptions;

            $time = strtotime($ClassDate);
            $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

            if ($NotificationDays == '0' || $NotificationDays == '' || $Department == '4' || $Department == '3' || $Vaild_LastCalss == '5') {
                $NotificationDate = NULL;
            }


            if ($ItemPricep != '') {

                $ItemPrice = $ItemPricep;
                $CompanyVat = $SettingsInfo->CompanyVat;

                $Vat = $_POST['Vat'];

                if ($CompanyVat == '0') {

                    if ($Vat == '0') {

                        $Vat = $SettingsInfo->Vat;
                        $Vats = '1.' . $Vat;
                        $Vat = $Vat;

                        $TotalVatItemPrice = $ItemPrice / $Vats;
                        $TotalVatItemPrice = $TotalVatItemPrice;
                        $TotalVatItemPrice = round($ItemPrice - $TotalVatItemPrice, 2);

                        $ItemPriceVat = round($ItemPrice - $TotalVatItemPrice, 2);
                        $ItemPriceVat = $ItemPriceVat;
                        $ItemPrice = $ItemPrice;

                    } else {

                        $ItemPrice = $ItemPrice;
                        $ItemPriceVat = $ItemPrice;
                        $Vat = $SettingsInfo->Vat;
                        $Vat = $Vat;
                        $TotalVatItemPrice = $ItemPrice * $Vat / 100;
                        $TotalVatItemPrice = round($TotalVatItemPrice, 2);
                        $ItemPrice = $ItemPrice + $TotalVatItemPrice;

                    }


                } else {
                    $ItemPrice = $ItemPrice;
                    $ItemPriceVat = $ItemPrice;
                }


            }

            $VatAmount = $ItemPrice - $ItemPriceVat;


            $AddClientActivity = DB::table('boostapp.client_activities')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'CardNumber' => $CardNumber, 'ClientId' => $ClientId, 'Department' => $Department, 'MemberShip' => $MemberShip, 'ItemId' => $Items, 'ItemText' => $ItemText, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $ItemPriceVat, 'Vat' => $Vat, 'VatAmount' => $VatAmount, 'StartDate' => $StartDate, 'VaildDate' => $ClassDate, 'TrueDate' => $ClassDate, 'BalanceValue' => $BalanceClass, 'TrueBalanceValue' => $TrueBalanceClass, 'ActBalanceValue' => $TrueBalanceClass, 'LimitClass' => $LimitClass, 'Dates' => $Dates, 'UserId' => $UserId, 'BalanceMoney' => $ItemPrice, 'MemberShipRule' => $MemberShipRule, 'NotificationDays' => $NotificationDate, 'BalanceValueLog' => $BalanceValueLog, 'FirstDate' => $FirstDate, 'FirstDateStatus' => $FirstDateStatus));

            ///// מעבר ניקובים+שיעורים ממנוי ישן לחדש

            $MembershipType = $AppSettings->MembershipType ?? 1;
            $CheckItemsRoleTwo = DB::table('boostapp.items_roles')->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $Items)->first();
            $TrueClasessFinal = $CheckItemsRoleTwo->GroupId ?? '';

            $data = [
                "CompanyNum" => $CompanyNum,
                "ClientId" => $ClientId,
                "ActivityId" => $AddClientActivity,
                "MemberShip" => $MemberShip,
                "MembershipType" => $MembershipType,
                "MinusCards" => $MinusCards,
                "Department" => $Department,
                "TrueClasessFinal" => $TrueClasessFinal,
                "BalanceClass" => $BalanceClass,
                "StartDate" => $StartDate
            ];
            (new ClientActivities())->moveClassesToNewActivity($data);


            //// עדכון חוב ללקוח

            $MemberShipText = '';
            $MemberShipText .= '{"data": [';
            $Taski = '1';
            $GetTasks = DB::table('boostapp.client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->orderBy('CardNumber', 'ASC')->get();
            $TaskCount = count($GetTasks);

            foreach ($GetTasks as $GetTask) {

                if ($Taski < $TaskCount) {
                    $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"},';
                } else {
                    $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"}';
                }


                ++$Taski;
            }
            $MemberShipText .= ']}';


            //// בדיקת כרטיסית אב

            $CheckCleintPayment = DB::table('boostapp.client')->where('id', '=', $ClientId)->where('CompanyNum', $CompanyNum)->first();
            $BalanceAmount = '0.00';


            if (@$CheckCleintPayment->PayClientId != '0') {
                $PayClientId = $CheckCleintPayment->PayClientId;

                $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId', '=', $PayClientId)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->where('isDisplayed',  1)->sum('BalanceMoney');

                DB::table('boostapp.client')
                    ->where('id', $ClientId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('BalanceAmount' => '0.00'));

                DB::table('boostapp.client_activities')
                    ->where('ClientId', $ClientId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('PayClientId' => $PayClientId));

            } else {
                $PayClientId = $ClientId;

                $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId', '=', $ClientId)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->where('isDisplayed',  1)->sum('BalanceMoney');

                DB::table('boostapp.client_activities')
                    ->where('ClientId', $ClientId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('PayClientId' => '0'));

            }

            $CheckClientInfoer = DB::table('boostapp.client')->where('CompanyNum', $CompanyNum)->where('PayClientId', $PayClientId)->get();
            if (!empty($CheckClientInfoer)) {
                foreach ($CheckClientInfoer as $CheckClientInfo) {
                    if (@$CheckClientInfo->id != '') {
                        $BalanceAmount += DB::table('boostapp.client_activities')->where('ClientId', '=', $CheckClientInfo->id)->where('CompanyNum', $CompanyNum)->where('CancelStatus', '=', '0')->where('isDisplayed',  1)->where('isDisplayed',  1)->sum('BalanceMoney');
                    }
                }
            }


            DB::table('boostapp.client')
                ->where('id', $PayClientId)
                ->where('CompanyNum', $CompanyNum)
                ->update(array('BalanceAmount' => $BalanceAmount, 'MemberShipText' => $MemberShipText));


            //// סגירת מנוי קודם

            DB::table('boostapp.client_activities')
                ->where('ClientId', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->where('Department', '=', '1')
                ->where('Status', '=', '0')
                ->where('TrueDate', '<=', date('Y-m-d'))
                ->update(array('Status' => '3'));


            DB::table('boostapp.client_activities')
                ->where('ClientId', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->where('Department', '=', '2')
                ->where('Status', '=', '0')
                ->where('TrueBalanceValue', '<=', '0')
                ->update(array('Status' => '3'));

            DB::table('boostapp.client_activities')
                ->where('ClientId', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->where('Department', '=', '2')
                ->where('Status', '=', '0')
                ->where('TrueDate', '<=', date('Y-m-d'))
                ->update(array('Status' => '3'));


            ///// סגירת מנוי היכרות/התנסות

            DB::table('boostapp.client_activities')
                ->where('ClientId', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->where('Department', '=', '3')
                ->where('Status', '=', '0')
                ->where('TrueBalanceValue', '<=', '0')
                ->update(array('Status' => '3'));

            DB::table('boostapp.client_activities')
                ->where('ClientId', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->where('Department', '=', '3')
                ->where('Status', '=', '0')
                ->where('TrueDate', '<=', date('Y-m-d'))
                ->update(array('Status' => '3'));


            if (($Department == '1' && $Vaild_LastCalss != '5') || ($Department == '2' && $Vaild_LastCalss != '5')) {

                $GetClasess = DB::table('boostapp.classstudio_act')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->where('ClassDate', '>=', $StartDate)->whereIn('Status', array(12, 9))->get();
                foreach ($GetClasess as $GetClases) {


                    $TrueClasess = '';
                    $TrueClasessFinal = '';
                    $ClassInfo = DB::table('boostapp.classstudio_date')->where('id', '=', $GetClases->ClassId)->where('Status', '=', '0')->where('CompanyNum', '=', $CompanyNum)->first();
                    $CheckItemsRole = $ClassInfo ? ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $Items, $ClassInfo->ClassNameType) : null;
                    if ($CheckItemsRole) {
                        $GroupId = $CheckItemsRole->GroupId;
                        $TrueClasessFinal = $CheckItemsRole->GroupId;
                        $TrueClasess = $CheckItemsRole->Class;
                    } else {
                        $CheckItemsRoleTwo = DB::table('boostapp.items_roles')->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $Items)->first();
                        $TrueClasessFinal = @$CheckItemsRoleTwo->GroupId;
                    }

                    if ($TrueClasessFinal != '') {
                        DB::table('boostapp.classstudio_act')
                            ->where('id', $GetClases->id)
                            ->where('CompanyNum', $CompanyNum)
                            ->update(array('ClientActivitiesId' => $AddClientActivity, 'TrueClasess' => $TrueClasessFinal, 'MemberShip' => $MemberShip));


                        //// עדכון מנוי שיבוץ קבוע

                        $CheckClientRegular = DB::table('boostapp.classstudio_dateregular')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->first();

                        if (@$CheckClientRegular->id != '') {

                            $ClientActivitiesId = $CheckClientRegular->ClientActivitiesId;
                            $CheckClientActivites = DB::table('boostapp.client_activities')->where('CompanyNum', $CompanyNum)->where('id', $ClientActivitiesId)->first();

                            if (@$CheckClientActivites->Status != '0') {

                                DB::table('boostapp.classstudio_dateregular')
                                    ->where('ClientId', $ClientId)
                                    ->where('CompanyNum', $CompanyNum)
                                    ->update(array('ClientActivitiesId' => $AddClientActivity));

                            }


                        }


                    }

                }


            }


            /// עדכון ספירה לסוג המנוי
            if ($Department == '1' || $Department == '2' || $Department == '3') {


                if ($Department == '1') {

                    $GetActivityCount = DB::table('boostapp.client_activities')->where('TrueDate', '>=', date('Y-m-d'))->where('MemberShip', '=', $MemberShip)->where('Department', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->count();

                } else if ($Department == '2') {

                    $GetActivityCount = DB::table('boostapp.client_activities')->where('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')
                        ->where('MemberShip', '=', $MemberShip)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->Orwhere('TrueBalanceValue', '>=', '1')
                        ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('MemberShip', '=', $MemberShip)->where('CompanyNum', '=', $CompanyNum)
                        ->where('Status', '=', '0')->count();

                } else if ($Department == '3') {

                    $GetActivityCount = DB::table('boostapp.client_activities')->where('CompanyNum', $CompanyNum)->where('Department', '3')->where('MemberShip', $MemberShip)->where('TrueBalanceValue', '>=', '1')->where('Status', '=', '0')->count();

                }


                DB::table('boostapp.membership_type')
                    ->where('id', $MemberShip)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('Count' => $GetActivityCount));

            }


        }


        DB::table('client')
            ->where('id', $Client->id)
            ->where('CompanyNum', $Client->CompanyNum)
            ->update(array('AutoInsert' => '0'));


    }

    $ThisDate = date('Y-m-d');
    $ThisDay = date('l');
    $ThisTime = date('H:i:s');

//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($Client)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($Client),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
?>
