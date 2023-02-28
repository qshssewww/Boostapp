<?php
require_once '../../Classes/Client.php';
require_once '../../Classes/ClientAdditionalContacts.php';
require_once "../../Classes/Company.php";
require_once '../../../app/init.php';
require_once "../../services/ClientService.php";

header("Content-Type: application/json", true);
if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        $postdata = file_get_contents("php://input");
        $obj = json_decode($postdata);
        $phoneExists = DB::table('boostapp.client')->where("ContactMobile", "=", $obj->pphone)->get();
        if ($phoneExists) {
            echo json_encode(['alreadyExists' => true, 'data' => $phoneExists], JSON_UNESCAPED_UNICODE);
        } else {

            $city = DB::table('boostapp.cities')->where("City", "=", $obj->city)->first();
            $street = DB::table('boostapp.street')->where("City", "=", $obj->city)->where("Street", "=", $obj->street)->first();

            $ClientFormRowId = ClientService::addClient([
                'CompanyNum' => Company::getInstance()->__get("CompanyNum"),
                'FirstName' => $obj->fname,
                'LastName' => $obj->lname,
                'CompanyId' => $obj->id,
                'ContactMobile' => $obj->pphone,
                'Email' => $obj->pemail,
                'Dob' => $obj->birthday,
                'City' => $city ? $city->CityId : null,
                'Street' => $street ? $street->StreetId : null,
                'Flat' => $obj->apartment,
                'Number' => $obj->number,
                'PostCode' => $obj->zip,
                'POBox' => $obj->mailbox,
                //תיעוד לקוח
                'Remarks' => $obj->userDucomentation,
                'RemarkIcon' => $obj->addComment,
                //Gender 0 male 1 female 2 other
                'Gender' => $obj->gender,
                // כל השאר
                'additional_data' => json_encode($obj->additional_data, JSON_UNESCAPED_UNICODE),
                'GetSMS' => $obj->smsMailing,
                'GetEmail' => $obj->mailMailing
            ]);
            $ClientFormRowId = $ClientFormRowId['Message']['client_id'];

            $data[] = $ClientFormRowId;
            $additionalContacts = new ClientAdditionalContacts();
            foreach ($obj->additional_contacts as $contact) {
                $data[] = $additionalContacts->insert_into_table([
                    "client_id" => $ClientFormRowId,
                    "phone" => $contact->phone,
                    "email" => $contact->email,
                    "relation" => $contact->relative
                ]);
            }
            $data['alreadyExists'] = false;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
}
