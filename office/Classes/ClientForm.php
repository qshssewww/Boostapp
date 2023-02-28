<?php
require_once "ClientFormFields.php";
require_once "FormFields.php";

class ClientForm
{

    /**
     * @var $form_id int
     */
    private $form_id;

    /**
     * @var $type int
     */
    private $type;

    /**
     * @var $company_num int
     */
    private $company_num;

    /**
     * @var $user_id int
     */
    private $user_id;

    /**
     * @var $last_update DateTime
     */
    private $last_update;

    /**
     * @var $fields ClientFormFields[]
     */
    private $fields;
    /**
     * @var string $table
     */
    private $table;

    public function __construct()
    {
        $this->table = "client_forms";
    }

    public function getCompanyForm($company_num, $type, $userId = null)
    {
        $form = DB::table($this->table)->where("company_num", "=", $company_num)->where("type", "=", $type)->get();
        if (sizeof($form) > 0) {
            foreach ($form[0] as $key => $value) {
                $this->__set($key, $value);
            }
        } else {
            $form = $this->insertclient_form($company_num, $type, $userId);
            $this->insertDefaultClientFormFields();
        }
        return $form;
    }

    public static function getFormByCompanyNumAndType($company_num, $type)
    {
        $form = DB::table('form_fields')
            ->select('*', 'client_form_fields.type')
            ->join(
                'client_form_fields',
                'form_fields.field_id',
                '=',
                'client_form_fields.field_id'
            )
            ->join(
                'client_forms',
                'client_form_fields.form_id',
                '=',
                'client_forms.form_id'
            )
            ->where(
                'client_forms.company_num',
                '=',
                $company_num
            )
            ->where(
                'client_forms.type',
                '=',
                $type
            )->where(
                "client_form_fields.status",
                '=',
                1
            )->orderBy("client_form_fields.order","asc")
            ->get();

        if (count($form) > 0) {
            return $form;
        }

        return null;
    }

    public function getCompanyFormAsArray()
    {
        $returnedArray = get_object_vars($this);
        $returnedArray["table"] = null;
        return $returnedArray;
    }

    /**
     * @param ClientForm $form
     */
    public function insertclient_form($company_num, $type, $userId)
    {
        $id = DB::table($this->table)->insertGetId(
            array(
                "company_num" => $company_num,
                "type" => $type,
                "user_id" => $userId,
                "last_update" => date('Y-m-d H:i:s')
            )
        );
        $form = DB::table($this->table)->where("form_id", "=", $id)->get();
        foreach ($form[0] as $key => $value) {
            $this->__set($key, $value);
        }
        return $form;
    }

    private function insertDefaultClientFormFields()
    {
        $FormFields = new FormFields();
        $fields = $FormFields->getDefaultFormFieldsByType($this->__get("type"));

        foreach ($fields as $key => $field) {
            $mandatory =  $field["default_id"] == 1 ? '1' : '0';

            $order = $key + 1;
            $clientFields = new ClientFormFields();
            $clientFields->__set("form_id", $this->__get("form_id"));
            $clientFields->__set("show", "1");
            $clientFields->__set("mandatory", $mandatory);
            $clientFields->__set("order", (string) $order);
            $clientFields->__set("type", $field["default_type"]);
            $clientFields->__set("options", $field["default_options"]);
            $clientFields->__set("field_id", $field["field_id"]);
            $clientFields->__set("status", 1);
            $clientFields->insertClientFormField();
        }
    }

    public function getOtherFormId($form_id, $company_num, $user_id) {
        $res = DB::table($this->table)->where('form_id', '!=', $form_id)->where('company_num', $company_num)->get();
        if (count($res) == 0) {
            $res = DB::table($this->table)->where('company_num', $company_num)->get();
            $type = $res[0]->type == 'client' ? 'lead' : 'client';
            $res = $this->getCompanyForm($company_num, $type, $user_id);
            return  $res[0]->form_id;
        }
        return $res[0]->form_id;
    }

    public function getFormById($id) {
        $res = DB::table($this->table)
            ->where("form_id", $id)->first();

        return $res;
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
}
