<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

}else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
/*
    return user with gender and years of age
*/
$q = DB::table('client as c')
->select(
    'c.id as clientId', 
    'c.CompanyName as fullName',
    'c.Email as email',
    'c.ContactMobile as phone',
    DB::raw("IF(c.Gender = 0, NULL, IF(c.Gender = 1, 'male', 'female')) as gender"),
    DB::raw("IF(c.Gender = 0, NULL, IF(c.Gender = 1, 'זכר', 'נקבה')) as genderHebrew"),
    DB::raw("IF(c.Dob='0000-00-00', NULL, floor(datediff (now(), c.Dob)/365)) as age"),
    DB::raw("IF(c.Dob='0000-00-00', NULL, c.Dob) as bday"),
    DB::raw("IF(c.Brands=0,NULL, c.BrandName) as branch")
)
->where('c.CompanyNum', '=', $rest->CompanyNum)
->where('c.Status', '=', '0')
->leftJoin('brands as b', function($join){
    $join->on('b.CompanyNum', '=', 'c.CompanyNum')
        ->on('b.id', '=', 'c.Brands');
})
->orderBy('c.CompanyName')
->limit(20);

if(!empty($_GET['clientId']) && (int) $_GET['clientId']) $q->where('c.id', '=', (int) $_GET['clientId']);
if(!empty($_GET['q'])) $q->where('c.CompanyName', 'LIKE', $_GET['q'].'%');

$rest->answer->items = $q->get();
}
