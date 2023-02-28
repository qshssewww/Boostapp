<?php

require_once '../app/initcron.php';
if(Auth::check()) {
    $CompanyNum = Auth::user()->CompanyNum;

    $answer = [];

    if (isset($_POST['q']) && (!empty($_POST['q']))) {
        $q = '%' . $_POST['q'] . '%';
        $postText = $_POST['q'] ?? '';

        $Items = DB::table('items')
            ->where('CompanyNum', $CompanyNum)
            ->where('Status', 0)
            ->where('Disabled', 0)
            ->where(function($query) use ($q,$postText) {
                $query->where('ItemName', 'LIKE', $q)->orWhere('id', $postText);
            })->get();

        $ItemCount = count($Items);

        foreach ($Items as $Item) {
            if ($Item->MemberShip != 'BA999') {
                $MemberShipInfo = DB::table('membership_type')
                    ->where('id', '=', $Item->MemberShip)
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->first();

                if (!$MemberShipInfo) {
                    continue;
                }
                $MemberShip = $MemberShipInfo->Type;
            } else {
                $MemberShip = lang('no_membership_type');
            }

            $answer[] = ['id' => $Item->id, 'text' => $Item->ItemName . ' :: ' . $MemberShip . ' :: ₪' . $Item->ItemPrice];
        }
    } else {
        $answer[] = ['id' => 0, 'text' => lang('table_no_data')];
    }

} else {
    $answer[] = ['id' => 0, 'text' => lang('table_no_data')];
}
echo '{"results": ' . json_encode($answer) . '}';


