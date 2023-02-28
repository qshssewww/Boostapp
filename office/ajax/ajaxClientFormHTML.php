<?php

require_once '../../app/init.php';

// load client form classes
require_once '../Classes/ClientForm.php';
$clientForm = new ClientForm();

$type = $_POST['type'];


$formDetails = $clientForm->getCompanyForm(Auth::user()->CompanyNum, $type);
$fieldsDetails = $clientForm->getFormByCompanyNumAndType(Auth::user()->CompanyNum, $type);

require_once '../partials-views/client-form/cf-fields.php';
exit;