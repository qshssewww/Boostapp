<?php

require_once __DIR__ . "/../../app/init.php";
require_once __DIR__ . "/../Classes/Client.php";
require_once __DIR__ . "/../Classes/City.php";
require_once __DIR__ . "/../Classes/ClientActivities.php";
require_once __DIR__ . "/../Classes/Clientcrm.php";
require_once __DIR__ . "/../Classes/ClientMedical.php";
require_once __DIR__ . "/../Classes/Company.php";
require_once __DIR__ . "/../Classes/Item.php";
require_once __DIR__ . "/../Classes/LeadStatus.php";
require_once __DIR__ . "/../Classes/LeadSource.php";
require_once __DIR__ . "/../Classes/OrderLogin.php";
require_once __DIR__ . "/../Classes/PaymentType.php";
require_once __DIR__ . "/../Classes/Pipeline.php";
require_once __DIR__ . "/../Classes/PipelineCategory.php";
require_once __DIR__ . "/../Classes/RegistrationFees.php";
require_once __DIR__ . "/../Classes/Settings.php";
require_once __DIR__ . "/../Classes/Street.php";
require_once __DIR__ . "/../Classes/StudioBoostappLogin.php";
require_once __DIR__ . "/../Classes/TempReceiptPaymentClient.php";
require_once __DIR__ . "/../Classes/UserBoostappLogin.php";
require_once __DIR__ . "/../Classes/Token.php";
require_once __DIR__ . "/../services/LoggerService.php";
require_once __DIR__ . "/../services/OrderService.php";
require_once __DIR__ . "/../services/PaymentService.php";
require_once __DIR__ . "/../services/payment/Meshulam.php";
require_once __DIR__ . "/../services/payment/Yaad.php";
require_once __DIR__ . "/../services/receipt/ReceiptService.php";
require_once __DIR__ . "/../services/ClientService.php";

/**
 *
 */
const mobileRegex = "/^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}(-){0,1}[0-9]{7}$/";


$userId = Auth::user()->id;

$company = Company::getInstance();
$companyNum = $company->__get("CompanyNum");

$transition = [
    "id_card" => "CompanyId",
    "apartment" => "Flat",
    "house_number" => "Number",
    "zip_code" => "PostalCode",
    "po_box" => "POBox",
    "gender" => "Gender"
];

header('Content-Type: application/json');

if (Auth::guest() || (!Auth::userCan('51') && !Auth::userCan('50'))) exit;

/**
 *  addNewUser - add user to UserBoostappLogin
 *
 * @param $phone
 * @param $email
 * @param $firstName
 * @param $lastName
 * @param $userPassword
 * @param string $adultPhone
 * @param int $parentId
 * @return int
 */
function addNewUser($phone, $email, $firstName, $lastName, $userPassword, $adultPhone="", $parentId=0): int {
    $user = UserBoostappLogin::find_by_phone($phone);
    if (!$user) {
        //Create new user
        $loginId = UserBoostappLogin::insert_into_table([
            "username" => trim($phone),
            "email" => isset($email) ? $email : "",
            "display_name" => trim($firstName.' '.$lastName),
            "FirstName" => trim($firstName),
            "LastName" => trim($lastName),
            "ContactMobile" => !empty($phone) ? $phone : "",
            "newUsername" => !empty($phone) ? $phone : "-1",
            "password" => $userPassword,
            "AppLoginId" => !empty($phone) ? $phone : $adultPhone,
            "parentId" => $parentId,
            "status" => '1'
        ]);
        //if error
        if ($loginId == 0) {
            return 0;
        }
    } else {
        $loginId = $user->id;
    }
    return $loginId;
}


/**
 * @param $clientId
 * @return mixed
 */
function addMedicalNote($clientId) {
    $clientMedical = new ClientMedical();
    $content = $_POST['medical-note'];
    $tillDate = !empty($_POST["tillDate"]) ? $_POST["tillDate"] : null;
    $date = date('Y-m-d G:i:s');
    $userId = Auth::user()->id;
    $companyNum = Auth::user()->CompanyNum;
    return $clientMedical->addClientMedicalRecord($companyNum, $clientId,$content, $tillDate, $date, $userId);
}

/**
 * @param $clientId
 * @param $medicalId
 * @return int
 */
function editMedicalNote($clientId, $medicalId) {
    $clientMedical = new ClientMedical();
    $content = $_POST['medical-note'];
    $tillDate = !empty($_POST["tillDate"]) ? $_POST["tillDate"] : null;
    $clientMedicalRow = $clientMedical->editClientMedicalContent($content, $clientId, $medicalId, $tillDate);
    return empty($clientMedicalRow) ? 0 : $clientMedicalRow->id;
}

/**
 * @param $clientId
 * @param $medicalId
 * @return mixed
 */
function removeMedicalNote($clientId, $medicalId) {
    $clientMedical = new ClientMedical();
    return $clientMedical->editMedicalStatus($clientId, $medicalId, 1);
}

/**
 * @param $clientId
 * @return mixed
 */
function addCrmNote($clientId) {
    $clientCrm = new Clientcrm();
    $userId = Auth::user()->id;
    $remarks = nl2br($_POST['crm-note']);
    $tillDate = !empty($_POST["tillDate"]) ? $_POST["tillDate"] : null;
    $clientCrmRow = $clientCrm->addClientCrm($clientId, $userId, $remarks, $tillDate);
    return $clientCrmRow[0]->id;
}

/**
 * @param $clientId
 * @param $crmId
 * @return int
 */
function editCrmNote($clientId, $crmId) {
    $clientCrm = new Clientcrm();
    $remarks = nl2br($_POST['crm-note']);
    $tillDate = !empty($_POST["tillDate"]) ? $_POST["tillDate"] : null;
    $clientCrmRow = $clientCrm->editClientCrmRemark($remarks, $clientId, $crmId, $tillDate);
    return empty($clientCrmRow) ? 0 : $clientCrmRow->id;
}

/**
 * @param $clientId
 * @param $crmId
 * @return mixed
 */
function removeCrmNote($clientId, $crmId) {
    $clientCrm = new Clientcrm();
    return $clientCrm->editClientCrm($crmId, $clientId, 1);
}

function divideToPayments($totalAmount, $numOfPayments) {
        $Amount = $totalAmount / $numOfPayments;
        $roundedAmount = ceil(round($Amount, 2));
        $restOfPayments = $roundedAmount * ($numOfPayments - 1);
        $firstPayment = $totalAmount - $restOfPayments;
        $firstPayment = number_format((float)$firstPayment, 2, '.', '');
        $secondPayment = number_format($roundedAmount, 2, '.', '');
        return ["firstPayment" => $firstPayment, "secondPayment" => $secondPayment];
    }

if (!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {

        /** client function  **/
        case "addClient":
            $hasMinor = isset($_POST['is_minor']) && ($_POST['is_minor'] == 'true' || $_POST['is_minor'] == 'on');
            $adultClientId = empty($_POST["parent_id"]) || $_POST["parent_id"] == -1 ? 0 : $_POST["parent_id"];
            $isLead = isset($_POST['PipeLine']);

            if ($isLead) {
                $SourceId = $_POST['Source'] ?? 0;
                $SourceInfo = DB::table('leadsource')->where('CompanyNum', '=', $companyNum)->where('id', '=', $SourceId)->first();

                if (!empty($SourceInfo)) {
                    $Source = $SourceInfo->Title;
                } else {
                    $Source = lang('without');
                }

                $PipelineParams = [
                    'PipeLine' => $_POST['PipeLine'] ?? 0,
                    'Source' => $Source,
                    'SourceId' => $SourceId,
                    'Status' => $_POST['Status'] ?? 0,
                    'Agents' => $_POST['Agents'] ?? 0,
                ];
            }

            if ($adultClientId == 0) {
                // adult
                $adultClientId = ClientService::addClient(array_merge([
                    'FirstName' => $_POST["adult_first_name"] ?? $_POST['first_name'],
                    'LastName' => $_POST["adult_last_name"] ?? $_POST['last_name'],
                    'CompanyId' => trim($_POST['CompanyId'] ?? '000000000'),
                    'areaCode' => $_POST["adult_phone_zone"] ?? $_POST['phone_zone'] ?? 0,
                    'ContactMobile' => trim($_POST["adult_phone"] ?? $_POST["phone"]),
                    'Email' => isset($_POST['email']) ? trim($_POST['email']) : null,
                    'Brands' => $_POST['brands'] ?? 0,
                    'Dob' => $_POST['date_of_birth'] ?? '0000-00-00',
                    'City' => $_POST["city"] ?? 0,
                    'Street' => $_POST["street"] ?? 0,
                    'Number' => $_POST['house_number'] ?? null,
                    'PostCode' => $_POST['zip_code'] ?? null,
                    'POBox' => $_POST['po_box'] ?? null,
                    'Flat' => $_POST['apartment'] ?? null,
                    'Gender' => $_POST['gender'] ?? 0,
                    'AppLoginId' => trim($_POST["adult_phone"] ?? $_POST["phone"]),
                    'ClientRanks' => $_POST['ClientRanks'] ?? '',
                    'Remarks' => $_POST['Remarks'] ?? null,
                ], $hasMinor ? [] : [
                    'Membership' => $_POST['select_membership'] ?? -1,
                    'MembershipPrice' => $_POST['membership_price'] ?? null,
                ], $hasMinor || !$isLead ? [] : $PipelineParams),
                    $hasMinor ? ClientService::CLIENT_STATUS_ARCHIVE : ($isLead ? ClientService::CLIENT_STATUS_LEAD : ClientService::CLIENT_STATUS_ACTIVE));

                if ($adultClientId['Status'] != 'Success') {
                    echo json_encode(["Message" => $adultClientId['Message'], "Status" => "Error"]);
                    break;
                }
                $UserId = $adultClientId['Message']['user_id'];
                $StudioId = $adultClientId['Message']['studio_id'];
                $adultClientId = $adultClientId['Message']['client_id'];
            }

            if ($hasMinor) {
                $addMinorClient = ClientService::addClient(array_merge([
                    'FirstName' => $_POST['first_name'],
                    'LastName' => $_POST['last_name'],
                    'CompanyId' => trim($_POST['minor_CompanyId'] ?? '000000000'),
                    'areaCode' => $_POST["minor_phone_zone"] ?? null,
                    'ContactMobile' => $_POST['id_card'] ?? $_POST["minor_phone"] ?? null,
                    'Brands' => $_POST['brands'] ?? 0,
                    'Dob' => $_POST['minor_date_of_birth'] ?? $_POST['date_of_birth'] ?? '0000-00-00',
                    'City' => $_POST["city"] ?? 0,
                    'Street' => $_POST["street"] ?? 0,
                    'Number' => $_POST['house_number'] ?? null,
                    'PostCode' => $_POST['zip_code'] ?? null,
                    'POBox' => $_POST['po_box'] ?? null,
                    'Flat' => $_POST['apartment'] ?? null,
                    'Gender' => $_POST['minor_gender'] ?? $_POST['gender'] ?? 0,
                    'AppLoginId' => trim($_POST["adult_phone"] ?? $_POST['phone']),
                    'parentClientId' => $adultClientId,
                    'relationship' => $_POST['relationship'] ?? 0,
                    'Membership' => $_POST['select_membership'] ?? -1,
                    'MembershipPrice' => $_POST['membership_price'] ?? null,
                ], ($isLead ? $PipelineParams : [])),
                $isLead ? ClientService::CLIENT_STATUS_LEAD : ClientService::CLIENT_STATUS_ACTIVE);

                if ($addMinorClient['Status'] != 'Success') {
                    echo json_encode(["Message" => $addMinorClient['Message'], "Status" => "Error"]);
                    break;
                }
                $UserMinorId = $addMinorClient['Message']['user_id'];
                $StudioMinorId = $addMinorClient['Message']['studio_id'];
                $addMinorClient = $addMinorClient['Message']['client_id'];
            }

            $medicalNoteId = "";
            $crmNoteId = "";

            if (!empty($_POST['medical-note'])) {
                // medicalNoteId for future use if necessary
                $medicalNoteId = addMedicalNote($addMinorClient ?? $adultClientId);
            }
            if (!empty($_POST['crm-note'])) {
                // $crmNoteId for future use if necessary
                $crmNoteId = addCrmNote($addMinorClient ?? $adultClientId);
            }

            echo json_encode([
                "Message" => [
                    "adult_client_id" => $adultClientId,
                    "client_id" => $addMinorClient ?? $adultClientId,
                    "user_id" => $UserMinorId ?? $UserId,
                    "studio_id" => $StudioMinorId ?? $StudioId,
                    "crm_note_id" => $crmNoteId,
                    "medical_note_id" => $medicalNoteId,
                ],
                "Status" => "Success",
                "Notify" => "נוצר משתמש חדש בהצלחה",
            ]);

            break;

        case "updateClient":
            $isMinor = ($_POST["is_minor"]  === "true");
            if (!isset($_POST["id"])) {
                echo json_encode(["Message" => "id is required", "Status" => "Error"]);
            } elseif (!isset($_POST["user_id"])) {
                echo json_encode(["Message" => "user id is required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["user_id"])) {
                echo json_encode(["Message" => "id must be numeric", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(["Message" => "id must be numeric", "Status" => "Error"]);
            } elseif (!empty($_POST["adult_client_id"]) && !is_numeric($_POST["adult_client_id"])) {
                echo json_encode(["Message" => "adult client id must be numeric", "Status" => "Error"]);
            } elseif (!empty($_POST["parent_id"]) && !is_numeric($_POST["parent_id"])) {
                echo json_encode(["Message" => "adult client id must be numeric", "Status" => "Error"]);
            } elseif (empty($_POST["first_name"]) || ($isMinor && empty($_POST["adult_first_name"]))) {
                echo json_encode(["Message" => "first_name is required", "Status" => "Error"]);
            } elseif (empty($_POST["last_name"]) || ($isMinor && empty($_POST["adult_last_name"]))) {
                echo json_encode(["Message" => "last_name is required", "Status" => "Error"]);
            } elseif (empty($_POST["phone"]) && (empty($_POST["adult_phone"]) && $_POST["is_minor"])) {
                echo json_encode(["Message" => "phone is required", "Status" => "Error"]);
            } elseif (!empty($_POST["phone"]) && empty($_POST["phone_zone"])) {
                echo json_encode(["Message" => "phone_zone is required", "Status" => "Error"]);
            } elseif (!empty($_POST["adult_phone"]) && empty($_POST["adult_phone_zone"])) {
                echo json_encode(["Message" => "phone_zone is required", "Status" => "Error"]);
            } elseif (!empty($_POST["phone"]) && (!preg_match(mobileRegex, $_POST["phone"]))) {
                echo json_encode(["Message" => "טלפון תקין", "Status" => "Error"]);
            } elseif (!empty($_POST["adult_phone"]) && (!preg_match(mobileRegex, $_POST["adult_phone"]))) {
                echo json_encode(["Message" => "טלפון אב לא תקין", "Status" => "Error"]);
            } elseif (!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(["Message" => "email is not valid", "Status" => "Error"]);
            } elseif (!empty($_POST["date_of_birth"]) && !DateTime::createFromFormat('Y-m-d', $_POST["date_of_birth"])) {
                echo json_encode(["Message" => "date_of_birth is not valid", "Status" => "Error"]);
            } elseif (!empty($_POST["gender"]) && ($_POST["gender"] != 0 && $_POST["gender"] != 1 && $_POST["gender"] != 2)) {
                echo json_encode(["Message" => "gender must be 0, 1 or 2", "Status" => "Error"]);
            } else {

                $previousAdultClientId = !empty($_POST["adult_client_id"]) ? $_POST["adult_client_id"] : 0;

                $adultClientId = empty($_POST["parent_id"]) || $_POST["parent_id"] == -1 ? 0 : $_POST["parent_id"];

                $is_adult_new = !empty($_POST["is_adult_new"]);
                $appPassword = mt_rand(100000, 999999);
                $userPassword = mt_rand(100000, 999999);
                $password = Hash::make($userPassword);
                $settingsInfo = new Settings($companyNum);
                $adultLoginId = null;
                $adultCompanyName = null;


                $client = new Client($_POST["id"]);
                if ($client->__get("CompanyNum") != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }

                /** Test and cleaning before entering tables */
                //phone and email
                $email = (!empty($_POST["email"]) && (!$isMinor)) ? strtolower(trim($_POST["email"])) : null;
                $phone = !empty($_POST["phone"]) ? trim($_POST["phone_zone"]) . ltrim($_POST["phone"], '0') : null;
                if ($client->isDuplicatePhoneEmail($companyNum, $phone, $email)) {
                    echo json_encode(["Message" => "A client with the same phone or email exists", "Status" => "Error"]);
                    break;
                }

                $companyNameClient = trim($_POST["first_name"]) . ' ' . trim($_POST["last_name"]);

                //todo check valid +972 ..
                $adultPhone = !empty($_POST["adult_phone"]) ? trim($_POST["adult_phone_zone"]) . ltrim($_POST["adult_phone"], '0') : null;


                if ($isMinor) {

                    //from not minor
                    if ($adultClientId > 0) {
                        $adultClient = new Client($adultClientId);

                        if ($adultClient->isDuplicatePhoneEmail($companyNum, $adultPhone, $email)) {
                            echo json_encode(["Message" => "A client with the same phone or email exists", "Status" => "Error"]);
                            break;
                        }
                        if ($adultClient->__get("CompanyNum") != $companyNum || $adultClient->__get("Status") > 1) {
                            echo json_encode(["Message" => "Invalid parent_id", "Status" => "Error"]);
                            break;
                        }

                        $adultCompanyName = $adultClient->__get("CompanyName");
                        $adultUser = UserBoostappLogin::find_by_phone($adultClient->__get("ContactMobile"), $_POST["adult_phone"]);
                        if (!$adultUser) {
                            echo json_encode(["Message" => "Invalid parent user", "Status" => "Error"]);
                            break;
                        }
                        $adultLoginId = $adultUser->id;
                        $appPassword = $adultClient->__get("AppPassword");


                    } else {
                        if ($_POST["parent_id"] == -1 && $is_adult_new) {
                            $adultClient = new Client($previousAdultClientId);
                            if ($adultClient->isDuplicatePhoneEmail($companyNum, $adultPhone, $email)) {
                                echo json_encode(["Message" => "A client with the same phone or email exists", "Status" => "Error"]);
                                break;
                            }
                            if ($adultClient->__get("CompanyNum") != $companyNum || $adultClient->__get("Status") > 1) {
                                echo json_encode(["Message" => "Invalid parent_id", "Status" => "Error"]);
                                break;
                            }
                            $adultFirstName = trim($_POST["adult_first_name"]);
                            $adultLastName = trim($_POST["adult_last_name"]);
                            $adultCompanyName = trim($adultFirstName . ' ' . $adultLastName);

                            /** edit user */
                            $adultUser = UserBoostappLogin::find_by_phone($adultClient->__get("ContactMobile"));
                            if (!$adultUser) {
                                echo json_encode(["Message" => "Invalid parent user", "Status" => "Error"]);
                                break;
                            }
                            $adultUser->__set("FirstName", $adultFirstName);
                            $adultUser->__set("LastName", $adultLastName);
                            $adultUser->__set("display_name", $adultCompanyName);
                            $adultUser->__set("ContactMobile", $adultPhone);
                            $adultUser->__set("AppLoginId", $adultPhone);
                            $adultUser->__set("password", $password);
                            $adultUser->__set("newUsername", $adultPhone);
                            $adultUser->__set("username", $email);
                            $adultUser->__set("email", $email);
                            $adultUser->__set("email", $email);

                            $adultLoginId = $adultUser->id;

                            /** edit client */
                            $adultClient->__set("FirstName", $adultFirstName);
                            $adultClient->__set("LastName", $adultLastName);
                            $adultClient->__set("CompanyName", $adultCompanyName);
                            $adultClient->__set("Email", $email);
                            $adultClient->__set("ContactMobile", $adultPhone);
                            $adultClient->__set("AppLoginId", $adultPhone);
                            $adultClient->__set("AppPassword", $appPassword);

                            $adultUser->update();
                            $adultClient->save();


                        } else {
                            $adultClientId = ClientService::addClient([
                                'parentClientId' => $adultClientId,
                                'PayClientId' => $adultClientId,
                                'CompanyNum' => $companyNum,
                                'Company' => "",
                                'FirstName' => trim($_POST["adult_first_name"]),
                                'LastName' => trim($_POST["adult_last_name"]),
                                'ContactMobile' => $adultPhone,
                                'Email' => $email,
                                'City' => 0,
                                'Gender' => 0,
                                'relationship' => 0,
                                'AppPassword' => $appPassword,
                                'AppLoginId' => $adultPhone,
                            ], ClientService::CLIENT_STATUS_ARCHIVE);

                            if ($adultClientId['Status'] != 'Success') {
                                echo json_encode(["Message" => $adultClientId['Message'], "Status" => "Error"]);
                                break;
                            }
                            $adultLoginId = $adultClientId['Message']['user_id'];
                            $studioId = $adultClientId['Message']['studio_id'];
                            $adultClientId = $adultClientId['Message']['client_id'];
                        }
                    }

                    // now not minor and was minor
                } else {
                    $client->__set("ContactMobile", $phone);
                    $client->__set("Company", "");
                }


                $dob = null;
                $city = null;
                $street = null;
                $user = null;
                $pipeline = null;
                if ($client->isClient()) {
                    // get user if exists
                    if ($_POST["user_id"]) {
                        $user = UserBoostappLogin::find_by_id($_POST["user_id"]);
                        if (!$user) {
                            echo json_encode(["Message" => "No user found, so it can not be edited", "Status" => "Error"]);
                            break;
                        }
                    }
                    // new date of birth (in case the user entered)
                    if (!empty($_POST["date_of_birth"])) {
                        $dob = DateTime::createFromFormat('Y-m-d', $_POST["date_of_birth"]);
                        $tz = new DateTimeZone('Asia/Jerusalem');
                        $age = $dob->diff(new DateTime('now', $tz))->y;
                    }
                    //new city ((in case the user entered)
                    if (!empty($_POST["city"])) {
                        $cityInstance = new City();
                        $city = $cityInstance->getFirstCityByName($_POST["city"]);
                    }
                    if (!empty($_POST["street"]) && $city) {
                        $streetInstance = new Street();
                        $street = $streetInstance->getFirstStreetByName($_POST["street"], $city->CityId);
                    }
                } else {
                    echo json_encode(["Message" => "Client is frozen", "Status" => "Error"]);
                    break;
                }

                // Update client data
                unset($_POST["fun"]);
                $medicalNoteId = "";
                $crmNoteId = "";

                foreach ($_POST as $key => $value) {
                    switch ($key) {
                        case "first_name":
                            $companyName = null;
                            if (isset($_POST["last_name"])) {
                                $companyName = trim($value) . " " . trim($_POST["last_name"]);
                            } else {
                                $companyName = trim($value) . " " . trim($client->__get("LastName"));
                            }
                            $client->__set("CompanyName", $companyName);
                            $client->__set("FirstName", trim($value));
                            if ($client->isClient() && $user) {
                                $user->__set("FirstName", trim($value));
                                $user->__set("display_name", $companyName);
                            } else {
                                $pipeline->__set("FirstName", trim($value));
                                $pipeline->__set("CompanyName", $companyName);
                            }
                            break;
                        case "last_name":
                            $client->__set("LastName", trim($value));
                            if ($client->isClient() && $user) {
                                $user->__set("LastName", trim($value));
                            } else {
                                $pipeline->__set("LastName", trim($value));
                            }
                            break;
                        case "phone":
                            $client->__set("ContactMobile", !empty($phone) ? $phone : "");
                            break;
                        case "date_of_birth":
                            if (!empty($value)) {
                                if (!$client->isClient()) break;
                                $client->__set("Dob", $dob->format('Y-m-d'));
                                $client->__set("Age", $age);
                            }
                            break;
                        case "city":
                            if (!empty($value)) {
                                if (!$client->isClient()) break;
                                $client->__set("City", $city->CityId);
                            }
                            break;
                        case "street":
                            if (!empty($value)) {
                                if (!$client->isClient()) break;
                                $client->__set("StreetH", $street->Street);
                                $client->__set("Street", $street->id);
                            }
                            break;
                        case "email":
                            if (!empty($value)) {
                                if (!$isMinor) {
                                    $client->__set("Email", trim(strtolower($value)));
                                }
                                if ($client->isClient() && $user) {
                                    $user->__set("email", $isMinor ? '' : trim(strtolower($value)));
                                }
                            }
                            break;
                        case "additional_data":
                            $client->__set("additional_data", isset($_POST["additional_data"]) ? json_encode($_POST["additional_data"]) : null);
                            break;
                        case "medical_note_id":
                            // create new medical note
                            if (empty($value) && !empty($_POST["medical-note"])) {
                                $medicalNoteId = addMedicalNote($_POST["id"]);
                            } elseif (!empty($value)) {
                                if (!empty($_POST["medical-note"])) {
                                    $medicalNoteId = editMedicalNote($_POST["id"], $value);
                                } else {
                                    removeMedicalNote($_POST["id"], $value);
                                    $medicalNoteId = "";
                                }
                            }
                            break;
                        case "crm_note_id":
                            // create new medical note
                            if (empty($value) && !empty($_POST["crm-note"])) {
                                $crmNoteId = addCrmNote($_POST["id"]);
                            } elseif (!empty($value)) {
                                if (!empty($_POST["crm-note"])) {
                                    $crmNoteId = editCrmNote($_POST["id"], $value);
                                } else {
                                    removeCrmNote($_POST["id"], $value);
                                    $crmNoteId = "";
                                }
                            }
                            break;
                        default:
                            if (isset($transition[$key])) {
                                $client->__set($transition[$key], trim($value));
                            }
                            break;
                    }
                }


                if ($client->isClient() && $user) {
                    $user->__set("ContactMobile", !empty($phone) ? $phone : "");
                    $user->__set("username", $isMinor ? '' : $phone);
                    $user->__set("newUsername", $isMinor ? '-1' : $phone);
                    $user->__set("AppLoginId", $isMinor ? $adultPhone : $phone);
                    $user->__set("parentId", $isMinor ? $adultLoginId : 0);
                    $user->__set("status", 1);

                    if (!$isMinor) {
                        $user->__set("password", $password);
                        //todo check if current
                        $updateStudio = DB::table('boostapplogin.studio')->where('ClientId', '=', $_POST["id"])->where('CompanyNum', '=', $companyNum)
                            ->update(array('UserId' => $user->id, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s'), 'Status' => 0));
                    }
                    $client->__set("parentClientId", $isMinor ? $adultClientId : 0);
                    $client->__set("Company", $isMinor ? $adultCompanyName : null);
                    $client->__set("UserId", Auth::user()->id);
                    $client->__set("AppLoginId", $isMinor ? $adultPhone : $phone);
                    $client->__set("AppPassword", $appPassword);
                    $client->__set("status", 0);
                    // relationship
                    $relationship = $isMinor ? 1 : 0;
                    $relationship = !empty($_POST["relationship"]) ? $_POST["relationship"] : $relationship;
                    $client->__set("relationship", $relationship);
                } else {
                    $pipeline->__set("ContactInfo", $phone);
                }


                $res = 0;
                if ($client->isClient()) {
                    $res += $user->update();
                } else {
                    $res += $pipeline->update();
                }
                $res += $client->save();


                echo json_encode(["Message" => ["client_id" => $client->id,
                    "adult_client_id" => $adultClientId,
                    "user_id" => $user->id,
                    "crm_note_id" => $crmNoteId,
                    "medical_note_id" => $medicalNoteId],
                    "Status" => "Success",
                    "Notify" => "המשתמש עודכן בהצלחה"]);
            }
            break;
        case "getPipelineCategories":
            $res = PipelineCategory::getPipelineCategories($companyNum);
            echo json_encode(["Message" => ["pipeline_categories" => $res], "Status" => "Success"]);
            break;

        case "getLeadStatuses":
            if (!isset($_POST["pipeId"])) {
                echo json_encode(["Message" => "pipeId is required", "Status" => "Error"]);
            } elseif(!is_numeric($_POST["pipeId"])) {
                echo json_encode(["Message" => "id must be numeric", "Status" => "Error"]);
            } else {
                $res = LeadStatus::getLeadStatuses($companyNum, $_POST["pipeId"],0);
                echo json_encode(["Message" => ["leadStatuses" => $res], "Status" => "Success"]);
            }
            break;

        case "getLeadSources":
            $sources = (new LeadSource())->getLeadSources($companyNum);
            echo json_encode(["Message" => ["LeadSources" => $sources], "Status" => "Success"]);
            break;

//        case "getPipelineCategories":
//            $res = PipelineCategory::get_pipleine_categories($companyNum);
//            echo json_encode(["Message" => ["pipeline_categories" => $res], "Status" => "Success"]);
//            break;
//        case "getLeadStatuses":
//            if (!isset($_POST["pipeId"])) {
//                echo json_encode(["Message" => "pipeId is required", "Status" => "Error"]);
//            } elseif(!is_numeric($_POST["pipeId"])) {
//                echo json_encode(["Message" => "id must be numeric", "Status" => "Error"]);
//            } else {
//                $res = LeadStatus::get_lead_statuses($companyNum, $_POST["pipeId"]);
//                echo json_encode(["Message" => ["leadStatuses" => $res], "Status" => "Success"]);
//            }
//            break;
//        case "getLeadSources":
//            $sources = LeadSource::get_lead_sources($companyNum);
//            echo json_encode(["Message" => ["LeadSources" => $sources], "Status" => "Success"]);
//            break;
        /** Membership function  **/

        case "assignMembership":
            if (!isset($_POST["clientId"])) {
                echo json_encode(["Message" => "clientId required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["clientId"])) {
                echo json_encode(["Message" => "clientId must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["itemId"])) {
                echo json_encode(["Message" => "itemId required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["itemId"])) {
                echo json_encode(["Message" => "itemId must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["startDate"])) {
                echo json_encode(["Message" => "startDate required", "Status" => "Error"]);
            } elseif (!DateTime::createFromFormat('Y-m-d', $_POST["startDate"])) {
                echo json_encode(["Message" => "startDate is not valid", "Status" => "Error"]);
            } elseif (!isset($_POST["end_date_calculation_type"])) {
                echo json_encode(["Message" => "end_date_calculation_type required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["end_date_calculation_type"])) {
                echo json_encode(["Message" => "end_date_calculation_type must be numeric", "Status" => "Error"]);
            } elseif ($_POST["end_date_calculation_type"] == 4 && !isset($_POST["endDate"])) {
                echo json_encode(["Message" => "endDate is required", "Status" => "Error"]);
            } elseif (isset($_POST["endDate"]) && !DateTime::createFromFormat('Y-m-d', $_POST["endDate"])) {
                echo json_encode(["Message" => "endDate is not valid", "Status" => "Error"]);
            } elseif (isset($_POST["sales_id"]) && !is_numeric($_POST["sales_id"])) {
                echo json_encode(["Message" => "sales_id must be numeric", "Status" => "Error"]);
            } elseif (isset($_POST["itemPrice"]) && !is_numeric($_POST["itemPrice"])) {
                echo json_encode(["Message" => "itemPrice must be numeric", "Status" => "Error"]);
            } else {
                if(!empty($_POST["client_activity_id"])) {
                    $clientActivity = new ClientActivities($_POST["client_activity_id"]);
                    $clientActivity->deleteClientActivityById($clientActivity->__get("id"));
                }
                $clientActivity = new ClientActivities();
                $cid = ClientActivities::assignMembership($_POST);
                if (!$cid["Status"]) {
                    echo json_encode(["Message" => $cid["error"], "Status" => "Error"]);
                    break;
                }

                $clientActivity = new ClientActivities($cid["ClientActivityId"]);
                echo json_encode(["Message" => ["ClientActivityId" => $cid["ClientActivityId"]], "Status" => "Success"]);
            }
            break;

        /** payment function  **/

        case "deletePayment":
             if (!isset($_POST["clientId"])) {
                echo json_encode(["Message" => "client id is required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["clientId"])) {
                echo json_encode(["Message" => "client id must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["tempListsId"])) {
                echo json_encode(["Message" => "temp list id mustis required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["tempListsId"])) {
                echo json_encode(["Message" => "temp list id must be numeric", "Status" => "Error"]);
            } elseif (!empty($_POST["groupNumber"]) && !is_numeric($_POST["groupNumber"])) {
                 echo json_encode(["Message" => "groupNumber must be numeric", "Status" => "Error"]);
            } elseif (!empty($_POST["trueFinalInvoiceNum"]) && !is_numeric($_POST["trueFinalInvoiceNum"])) {
                 echo json_encode(["Message" => "trueFinalInvoiceNum must be numeric", "Status" => "Error"]);
            } else {
                 $statusNew = '1';
                 $statusPay = '';
                 $groupNumber = $_POST['groupNumber'];
                 $tempId = $_POST['clientId'];
                 $tempListsId = $_POST['tempListsId'];
                 $trueFinalInvoiceNum = $_POST['trueFinalInvoiceNum'];

                 $tempPaymentInfo = TempReceiptPaymentClient::find($tempListsId);

                 if ($tempPaymentInfo->TypePayment != '3') {
                     $tempPaymentInfo->delete();
                     $statusPay = lang('docs_receipt_0');
                 } // Payment on credit card
                 else if ($tempPaymentInfo->TypePayment == '3') {
                     echo json_encode(["Message" => "לא ניתן למחוק תשלום בכרטיס אשראי", "Status" => "Error"]);
                     die();
                 } else{
                     echo json_encode(["Message" => "מחיקה מסוג שונה מהמוגדר", "Status" => "Error"]);
                     die();
                 }
                 $res = array(
                     'tempId' => $tempId,
                     'typeDoc' => $tempPaymentInfo->TypeDoc,
                     'companyNum' => $companyNum,
                     'trueFinalInvoiceNum' => $trueFinalInvoiceNum);
                 echo json_encode(["Message" => $res, "Status" => "Success"]);
             }
            break;

        case "fixedPayment":
            $regFees = new RegistrationFees();
            $res = $regFees->getFixedPaymentOfItem($companyNum,$_POST["membership"]);
            echo json_encode(["Message" => $res, "Status" => "Success"]);
            break;
        case "addPayment":
            if (!isset($_POST["trueFinalInvoiceNum"])) {
                echo json_encode(["Message" => "trueFinalInvoiceNum required", "Status" => "Error"]);
            } elseif (!isset($_POST['tempId'])) {
                echo json_encode(["Message" => "tempId required", "Status" => "Error"]);
            } elseif (!isset($_POST['typeDoc'])) {
                echo json_encode(["Message" => "typeDoc required", "Status" => "Error"]);
            }  elseif (!isset($_POST['act']))  {
                echo json_encode(["Message" => "act required", "Status" => "Error"]);
            }  elseif (!isset($_POST['clientActivityId']))  {
                echo json_encode(["Message" => "clientActivityId required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["act"])) {
                echo json_encode(["Message" => "act must be numeric", "Status" => "Error"]);
            } else {
                $statusreditCard = array(
                    0 => "עסקה מאושרת",
                    1 => "חסום החרם כרטיס",
                    2 => "גנוב החרם כרטיס",
                    3 => "התקשר לחברת האשראי",
                    4 => "סירוב",
                    5 => "מזויף החרם כרטיס",
                    6 => "ת.ז. או CVV שגויים",
                    7 => "חובה להתקשר לחברת האשראי",
                    19 => "נסה שנית, העבר כרטיס אשראי",
                    33 => "כרטיס לא תקין",
                    34 => "כרטיס לא רשאי לבצע במסוף זה או אין אישור לעסקה כזאת",
                    35 => "כרטיס לא רשאי לבצע עסקה עם סוג אשראי זה",
                    36 => "פג תוקף",
                    37 => "שגיאה בתשלומים - סכום העסקה צריך להיות שווה תשלום ראשון + תשלום קבוע כפול מספר התשלומים",
                    38 => "לא ניתן לבצע עסקה מעל התקרה לכרטיס לאשרי חיוב מיידי",
                    39 => "ספרת ביקורת לא תקינה",
                    57 => "לא הוקלד מספר תעודת זהות",
                    58 => "לא הוקלד CVV2",
                    69 => "אורך הפס המגנטי קצר מידי",
                    101 => "אין אישור מחברה אשראי לעבודה",
                    106 => "למסוף אין אישור לביצוע שאילתא לאשראי חיוב מיידי",
                    107 => "סכום העסקה גדול מידי - חלק למספר עסקאות",
                    110 => "למסוף אין אישור לכרטיס חיוב מיידי",
                    111 => "למסוף אין אישור לעסקה בתשלומים",
                    112 => "למסוף אין אישור לעסקה טלפון/ חתימה בלבד בתשלומים",
                    113 => "למסוף אין אישור לעסקה טלפונית",
                    114 => "למסוף אין אישור לעסקה חתימה בלבד",
                    118 => "למסוף אין אישור לאשראי ישראקרדיט",
                    119 => "למסוף אין אישור לאשראי אמקס קרדיט",
                    124 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס ישראכרט",
                    125 => "למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס אמקס",
                    127 => "למסוף אין אישור לעסקת חיוב מיידי פרט לכרטיסי חיוב מיידי",
                    129 => "למסוף אין אישור לבצע עסקת זכות מעל תקרה",
                    133 => "כרטיס לא תקף על פי רשימת כרטיסים תקפים של ישראכרט",
                    138 => "כרטיס לא רשאי לבצע עסקאות בתשלומים על פי רשימת כרטיסים תקפים של ישראכרט",
                    146 => "לכרטיס חיוב מיידי אסור לבצע עסקה זכות",
                    150 => "אשראי לא מאושר לכרטיסי חיוב מיידי",
                    151 => "אשראי לא מאושר לכרטיסי חול",
                    156 => "מספר תשלומים לעסקת קרדיט לא תקין",
                    160 => "תקרה 0 לסוג כרטיס זה בעסקה טלפונית",
                    161 => "תקרה 0 לסוג כרטיס זה בעסקת זכות",
                    162 => "תקרה 0 לסוג כרטיס זה בעסקת תשלומים",
                    163 => "כרטיס אמריקן אקספרס אשר הנופק בחול לא רשאי לבצע עסקאות תשלומים",
                    164 => "כרטיסי JCB רשאי לבצע עסקאות באשראי רגיל",
                    169 => "לא ניתן לבצע עסקת זכות עם אשראי שונה מהרגיל",
                    171 => "לא ניתן לבצע עסקה מאולצת לכרטיס/אשראי חיוב מיידי",
                    172 => "לא ניתן לבטל עסקה קודמת (עסקת זכות או מספר כרטיס אינו זהה)",
                    173 => "עסקה כפולה",
                    200 => "שגיאה יישומית",
                    251 => "נסה שנית, העבר כרטיס אשראי",
                    260 => "שגיאה כללית בחברת האשראי. נסה שנית מאוחר יותר.",
                    280 => "שגיאה כללית בחברת האשראי, נסה שנית מאוחר יותר.",
                    349 => 'אין הרשאה למסוף לאישור J5 ללא חיוב, התקשר לתמיכה.',
                    447 => 'מספר כרטיס שגוי',
                    901 => "שגיאה במסוף. התקשר לתמיכה BOOSTAPP",
                    902 => "שגיאת תקשורת. התקשר לתמיכה BOOSTAPP",
                    920 => "לא ניתן לביטול / לא נמצאה העסקה / העסקה בוטלה בעבר",
                    997 => "טוקן לא תקין, נא להצפין מחדש את כרטיס האשראי",
                    998 => "עסקה בוטלה - BOOSTAPP",
                    999 => "שגיאת תקשורת - BOOSTAPP"
                );
                $settingsInfo = Company::getInstance($companyNum);
                $dates = date('Y-m-d H:i:s');
                $userDate = date('Y-m-d');
                $checkRefresh = !empty($_POST["checkRefresh"]) ? $_POST["checkRefresh"] : 0;
                $creditStatus = '0';
                $fixTrueYaadNumber = '';
                $tempId = $_POST['tempId'];
                $typeDoc = $_POST['typeDoc'];
                $trueFinalInvoiceNum = $_POST['trueFinalInvoiceNum'];
                $clientActivityId = $_POST['clientActivityId'];

                $act = $checkRefresh == '2' ? '999' : $_POST['act'];
                $response = [
                    'tempId' => $tempId,
                    'typeDoc' => $typeDoc,
                    'companyNum' => $companyNum,
                    'trueFinalInvoiceNum' => $trueFinalInvoiceNum,
                    'clientActivityId' => $clientActivityId,
                ];

                switch ($act) {
                    case 1:
                        $cashValue = $_POST['paymentValue'];
                        $tempReceiptPaymentClient = new TempReceiptPaymentClient([
                            'CompanyNum' => $companyNum,
                            'TypeDoc' => $typeDoc,
                            'TempId' => $tempId,
                            'TypePayment' => '1',
                            'Amount' => $cashValue,
                            'CheckDate' => $userDate,
                            'Dates' => $dates,
                            'UserId' => $userId,
                            'Excess' => '0',
                            'UserDate' => $userDate,
                            'ClientActivityId' => $clientActivityId
                        ]);
                        $tempReceiptPaymentClient->save();
                        break;
                    case 2:
                        if (empty($_POST["checkDate"])) {
                            echo json_encode(["Message" => "תאריך פרעון נדרש", "Status" => "Error"]);
                        } elseif (empty($_POST["checkSnif"])) {
                            echo json_encode(["Message" => "מספר סניף נדרש", "Status" => "Error"]);
                        } elseif (empty($_POST["checkNumber"])) {
                            echo json_encode(["Message" => "מספר צ'ק נדרש", "Status" => "Error"]);
                        } elseif (!is_numeric($_POST["checkNumber"])) {
                            echo json_encode(["Message" => "מספר צ'ק חייב להיות מספרי", "Status" => "Error"]);
                        } elseif (empty($_POST["checkBank"])) {
                            echo json_encode(["Message" => "בנק נדרש", "Status" => "Error"]);
                        } elseif (empty($_POST["checkAccount"])) {
                            echo json_encode(["Message" => "שם הבנק נדרש", "Status" => "Error"]);
                        } else {
                            $checkValue = $_POST['paymentValue'];
                            $checkDate = $_POST['checkDate'];
                            $checkSnif = $_POST['checkSnif'];
                            $checkAccount = $_POST['checkAccount'];
                            $checkBank = $_POST['checkBank'];
                            $checkNumber = $_POST['checkNumber'];
                            $tempReceiptPaymentClient = new TempReceiptPaymentClient([
                                'CompanyNum' => $companyNum,
                                'TypeDoc' => $typeDoc,
                                'TempId' => $tempId,
                                'TypePayment' => '2',
                                'Amount' => $checkValue,
                                'CheckBank' => $checkAccount,
                                'CheckBankSnif' => $checkSnif,
                                'CheckBankCode' => $checkBank,
                                'CheckNumber' => $checkNumber,
                                'CheckDate' => $checkDate,
                                'Dates' => $dates,
                                'UserId' => $userId,
                                'UserDate' => $userDate,
                                'ClientActivityId' => $clientActivityId
                            ]);
                            $tempReceiptPaymentClient->save();
                        }
                        break;
                    case 3:
                        if (empty($_POST["paymentType"])) {
                            echo json_encode(["Message" => "סוג התשלום נדרש", "Status" => "Error"]);
                        } elseif (!is_numeric($_POST["paymentType"])) {
                            echo json_encode(["Message" => "סוג התשלום חייב להיות מספר", "Status" => "Error"]);
                        } elseif (!isset($_POST['paymentNumber'])) {
                            echo json_encode(["Message" => "מספר התשלומים נדרש", "Status" => "Error"]);
                        } elseif (!is_numeric($_POST["paymentNumber"])) {
                            echo json_encode(["Message" => "מספר תשלומים חייב להיות מספרי", "Status" => "Error"]);
                        } elseif (!isset($_POST['clientId'])) {
                            echo json_encode(["Message" => "מספר משתמש נדרש", "Status" => "Error"]);
                        } elseif (!isset($_POST['membershipId'])) {
                            echo json_encode(["Message" => "מספר מנוי נדרש", "Status" => "Error"]);
                        } else {
                            $client = new Client($_POST['clientId']);
                            $token = isset($_POST['token']) ? $_POST['token'] : null;
                            $method = $settingsInfo->TypeShva;
                            $paymentNumber = isset($_POST['paymentNumber']) ? (int)$_POST['paymentNumber'] : 1;
                            $paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : 0;
                            $paymentNumber = $paymentType == 1 ? 1 : $paymentNumber;
                            $paymentValue = $_POST['paymentValue'];
                            $membershipId = $_POST['membershipId'];

                            if ($paymentValue == 0) {
                                echo json_encode(["Message" => "סכום לתשלום צריך להיות גדול מ0", "Status" => "Error"]);
                                die();
                            }

                            switch ($method) {
                                case '0':
                                    $paymentSystemMethod = PaymentService::PAYMENT_YAAD;
                                    break;
                                case '1':
                                    $paymentSystemMethod = PaymentService::PAYMENT_MESHULAM;
                                    break;
                                default:
                                    echo json_encode(["Message" => "שם מערכת התשלום שנבחרה לא תואמת את האפשרויות הנתמכות", "Status" => "Error"]);
                                    die();
                            }

                            $paymentSystem = PaymentService::getPaymentSystemByMethod($paymentSystemMethod);

                            $tokenModel = null;
                            $l4Digits = $yaadCode = '';

                            $dataPayment = [
                                'client' => $client,
                                'companyNum' => $companyNum,
                                'totalAmount' => $paymentValue,
                                'info' => " הזמנת מנוי מס' " . $membershipId,
                            ];

                            $order = OrderService::createOrder($client, $paymentValue, $paymentNumber, OrderLogin::TYPE_NEW_CLIENT);

                            $order->PaymentMethod = PaymentService::getPaymentMethodByType($paymentSystemMethod);
                            $order->save();

                            //payment with token
                            if (!empty($token)) {
                                $tokenModel = Token::getByToken($token);
                                if (!$tokenModel) {
                                    echo json_encode(["Message" => "הטוקן שנבחר אינו תקין", "Status" => "Error"]);
                                    die();
                                }
                                $l4Digits = $tokenModel->L4digit;
                                $yaadCode = $tokenModel->YaadCode;

                                try {
                                    $paymentResult = $paymentSystem->makePaymentWithToken($order, $tokenModel, $paymentType, $paymentNumber);
                                    $newTempReceiptPaymentClient = [
                                        'CompanyNum' => $companyNum,
                                        'TypeDoc' => $typeDoc,
                                        'TempId' => $tempId,
                                        'Payments' => $paymentNumber,
                                        'TypePayment' => '3',
                                        'PaymentType' => $paymentType,
                                        'TokenId' => $tokenModel->id,
                                        'Amount' => $paymentValue,
                                        'L4digit' => $paymentResult['L4digit'],
                                        'YaadCode' => $paymentResult['YaadCode'],
                                        'CCode' => $paymentResult['CCode'],
                                        'ACode' => $paymentResult['ACode'],
                                        'Bank' => $paymentResult['Bank'],
                                        'Brand' => $paymentResult['Brand'],
                                        'Issuer' => $paymentResult['Issuer'],
                                        'BrandName' => $paymentResult['BrandName'],
                                        'Dates' => $dates,
                                        'UserId' => $userId,
                                        'tashType' =>  $paymentResult['tashTypeDB'],
                                        'UserDate' => $userDate,
                                        'ClientActivityId' => $clientActivityId
                                    ];
                                    if (!empty($paymentResult['PayToken'])) {
                                        $newTempReceiptPaymentClient['PayToken'] = $paymentResult['PayToken'];
                                    }
                                    if (!empty($paymentResult['TransactionId'])) {
                                        $newTempReceiptPaymentClient['TransactionId'] = $paymentResult['TransactionId'];
                                    }

                                    $tempReceiptPaymentClient = new TempReceiptPaymentClient($newTempReceiptPaymentClient);
                                    $tempReceiptPaymentClient->save();

                                    $transaction = new Transaction();
                                    $transaction->CompanyNum = $companyNum;
                                    $transaction->ClientId = $client->id;
                                    $transaction->UpdateTransactionDetails = serialize($paymentResult);
                                    $transaction->UserId = 0;
                                    $transaction->save();

                                    $order->TempReceiptId = $tempReceiptPaymentClient->id;
                                    $order->TransactionId = $transaction->id;
                                    $order->save();

                                    ReceiptService::saveReceiptAfterPayWithCard($order);
                                } catch (\Throwable $e) {
                                    echo json_encode(["Message" => $e->getMessage(), "Status" => "Error"]);
                                    die();
                                }

                            } else {
                                try {
                                    $tempReceiptPaymentClient = new TempReceiptPaymentClient([
                                        'CompanyNum' => $companyNum,
                                        'TypeDoc' => $typeDoc,
                                        'TempId' => $tempId,
                                        'Payments' => $paymentNumber,
                                        'TypePayment' => '3',
                                        'PaymentType' => $paymentType,
                                        'Amount' => $paymentValue,
                                        'Dates' => $dates,
                                        'UserId' => $userId,
                                        'UserDate' => $userDate,
                                        'PaymentConfirmed' => 0,
                                        'ClientActivityId' => $clientActivityId
                                    ]);
                                    $tempReceiptPaymentClient->save();
                                    $tempPaymentId = $tempReceiptPaymentClient->id;

                                    $order->TempReceiptId = $tempReceiptPaymentClient->id;
                                    $order->save();

                                    $url = $paymentSystem->makeFirstPayment($order, $paymentType, $paymentNumber);
                                    $response['iframe'] = true;
                                    $response['url'] = $url;
                                    $response['tempPaymentId'] = $tempPaymentId;

                                } catch (\Throwable $e) {
                                    echo json_encode(["Message" => $e->getMessage(), "Status" => "Error"]);
                                    die();
                                }
                            }

                        }
                        break;
                    case 4:
                        if (empty($_POST["bankDate"])) {
                            echo json_encode(["Message" => "תאריך העברה בנקאית נדרש", "Status" => "Error"]);
                        } elseif (empty($_POST["bankNumber"])) {
                            echo json_encode(["Message" => "מספר הסמכתה נדרש", "Status" => "Error"]);
                        } elseif (!is_numeric($_POST["bankNumber"])) {
                            echo json_encode(["Message" => "מספר הסמכתה חייב להיות מספר", "Status" => "Error"]);
                        } else {
                            $bankValue = $_POST['paymentValue'];
                            $bankDate = $_POST['bankDate'];
                            $bankNumber = $_POST['bankNumber'];

                            $tempReceiptPaymentClient = new TempReceiptPaymentClient([
                                'CompanyNum' => $companyNum,
                                'TypeDoc' => $typeDoc,
                                'TempId' => $tempId,
                                'TypePayment' => '4',
                                'Amount' => $bankValue,
                                'CheckDate' => $bankDate,
                                'BankNumber' => $bankNumber,
                                'Dates' => $dates,
                                'UserId' => $userId,
                                'UserDate' => $userDate,
                                'ClientActivityId' => $clientActivityId
                                ]
                            );
                            $tempReceiptPaymentClient->save();
                        }
                        break;
                    case 99:
                        break;
                }
                echo json_encode(["Message" => $response,
                    "Status" => "Success",
                    "Notify" => "תשלום זמני נוצר בהצלחה"]);
            }
            break;

        case 'saveReceipt':
             if (!isset($_POST["clientId"])) {
                echo json_encode(["Message" => "client id is required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["clientId"])) {
                echo json_encode(["Message" => "client id must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["isCreditCard"])) {
                echo json_encode(["Message" => "isCreditCard is required", "Status" => "Error"]);
            } elseif (!isset($_POST["isFullyPayment"])) {
                echo json_encode(["Message" => "isFullyPayment is required", "Status" => "Error"]);
            } elseif (!isset($_POST["finalInvoiceId"])) {
                echo json_encode(["Message" => "final invoice id is required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["finalInvoiceId"])) {
                echo json_encode(["Message" => "final invoice id must be numeric", "Status" => "Error"]);
             }  elseif (!isset($_POST['clientActivityId']))  {
                 echo json_encode(["Message" => "clientActivityId required", "Status" => "Error"]);
            } elseif (!empty($_POST["groupNumber"]) && !is_numeric($_POST["groupNumber"])) {
                 echo json_encode(["Message" => "groupNumber must be numeric", "Status" => "Error"]);
            } else {
                 try{
                     ReceiptService::saveReceipts($_POST["clientId"], $_POST["groupNumber"], $companyNum, $_POST['clientActivityId']);
                     echo json_encode(["Message" => 'redirect', "Status" => "Success"]);
                 } catch (\Throwable $e) {
                     echo json_encode(["Message" => $e->getMessage(), "Status" => "Error"]);

                 }
             }
             break;

        default:
        echo json_encode(["Message" => "Function not found", "Status" => "Error"]);
        break;
    }
} else {
    echo json_encode(["Message" => "No function", "Status" => "Error"]);
}
