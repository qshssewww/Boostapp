<?php
$CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($client['clientId'], Auth::user()->CompanyNum);
?>
<tr id="conclusion-<?php echo $client['clientId'] ?>">
    <td>
        <div class="d-flex">
            <div class="d-flex align-items-center position-relative">
            <img src="<?php
                //display avatar.
                if (!empty($CheckUserApp->__get('UploadImage'))) {
                    echo get_appboostapp_domain().'/camera/uploads/large/'.$CheckUserApp->__get('UploadImage');
                } else {
                    echo 'https://ui-avatars.com/api/?length=1&name=' . $client['firstName'] . '&background=f3f3f4&color=000&font-size=0.5';
                }?>" class="w-40p h-40p rounded-circle mie-8">
                <div><?php echo $client['companyName']; ?></div>
                <a href="javascript:;"  class="stretched-link js-modal-user" data-client-id="<?php echo $client['clientId'] ?>" data-activity-id="<?php echo $client['activityId'] ?>" data-act-id="<?php echo $client['actId'] ?>" ></a>
            </div>
        </div>
    </td>
</tr>