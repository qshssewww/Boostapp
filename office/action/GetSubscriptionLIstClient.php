<?php
 require_once '../../app/initcron.php';

header('Content-Type: text/html; charset=utf-8');

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
//require_once '../../app/init.php';
$ClientId = $Id = $_REQUEST['Id'];

//$OpenTables = DB::table('client_activities')
//    ->where('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')->where("ClientId",'=',$ClientId)
//    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')->where("ClientId",'=',$ClientId)
//    ->Orwhere('TrueBalanceValue','<=', '0')->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')->where("ClientId",'=',$ClientId)
//    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=','2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')->where("ClientId",'=',$ClientId)
//    ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=','2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')->where("ClientId",'=',$ClientId)
//    ->get();
$OpenTables = DB::table('client_activities')
    ->join("membership_type", 'client_activities.MemberShip', "=", "membership_type.id")
    ->select("membership_type.Type", "client_activities.*")
    ->where('client_activities.TrueDate','<', date('Y-m-d'))->where('client_activities.Freez', '!=', 1)->where('client_activities.Department','=', '1')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueDate','<', date('Y-m-d'))->where('client_activities.Freez', '=', 1)->where('client_activities.EndFreez', '<=', date('Y-m-d'))->where('client_activities.Department','=', '1')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueBalanceValue','<=', '0')->where('client_activities.Department','=', '2')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueBalanceValue','<=', '0')->where('client_activities.Department','=', '3')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueDate','<', date('Y-m-d'))->where('client_activities.Freez', '!=', 1)->where('client_activities.Department','=','2')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueDate','<', date('Y-m-d'))->where('client_activities.Freez', '!=', 1)->where('client_activities.Department','=','3')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->Orwhere('client_activities.TrueDate','<', date('Y-m-d'))->where('client_activities.Freez', '=', 1)->where('client_activities.EndFreez', '<=', date('Y-m-d'))->where('client_activities.Department','=','2')->where('client_activities.CompanyNum','=', $CompanyNum)->where('client_activities.Status','=', '0')->where('client_activities.ClientStatus','=', '0')->where('client_activities.KevaAction','=', '0')->where("client_activities.ClientId",'=',$ClientId)
    ->get();

?>

<div class="SubsList">

   <h4 class="pb-9">בחר מתוך הרשימה</h4>
    <?php foreach ($OpenTables as $client) { ?>
       <?php if ($client->Department == '1') { ?>
            <div>
                <input class="check-subs mie-7" type="checkbox" data-client='<?php echo json_encode($client)  ?>' checked ><label><?php echo $client->Type." - " . $client->ItemText ?></label>
            </div>
        <?php } elseif($client->Department == '2') { ?>
            <div>
                <input class="check-subs mie-7" type="checkbox" data-client='<?php echo json_encode($client) ?>' checked><label><?php echo $client->Type." - " .$client->ItemText ?></label>
            </div>

        <?php } else if ($client->Department == '3') { ?>
            <div>
                <input class="check-subs mie-7" type="checkbox" data-client='<?php echo json_encode($client) ?>' checked><label><?php echo $client->Type." - " . $client->ItemText ?></label>
            </div>

        <?php } else if ($client->Department == '4') {?>
            <div>
                <input class="check-subs mie-7" type="checkbox" data-client='<?php echo json_encode($client) ?>' checked><label><?php echo $client->Type." - " . $client->ItemText ?></label>
            </div>

        <?php } ?>

    <?php }  ?>
    <div>
        <button id="compliteSub"  type="button" class="btn btn-dark text-whit">העבר להושלם</button>
    </div>
</div>
<script>

    $(document).ready(function () {
        $("#compliteSub").click(function () {
            var PleaseWaitGlobal = $.notify(
                {
                    icon: 'fas fa-spinner fa-spin',
                    message: 'מעבד נתונים, כמה רגעים...',
                },{
                    type: 'warning',
                    z_index: '99999999',
                });
            $(".check-subs").each(function () {

                if($(this).is(':checked')){
                    dataClient = JSON.parse($(this).attr("data-client"));
                    data = {
                        "ClientId": dataClient.ClientId,
                        "ActivityId": dataClient.id,
                        "ItemText": dataClient.ItemText,
                        "ItemPrice": dataClient.BalanceMoney,
                        "IncludeVat": 1,
                        "DiscountsType": dataClient.DiscountType,
                        "Discounts": dataClient.Discount,
                        "Status": 3,
                        "action": "AddDiscountActivity"
                    };
                    debugger;
                    $.ajax({
                        type: 'POST',
                        url:'/ajax.php',
                        data: data,
                        success: function(res){
                            $.notify(
                                {
                                    icon: 'fas fa-check-circle',
                                    message: 'הפעולה בוצעה בהצלחה!',

                                },{
                                    type: 'success',
                                    z_index: '99999999',
                                });
                            $("#ViewDeskInfo").modal('hide');
                            $('#categories').DataTable().ajax.reload(InitInvildMemberShipPost,false);
                        },
                        error: function(res){

                            $.notify(
                                {
                                    icon: 'fas fa-times-circle',
                                    message: 'אופס... התגלתה שגיאה!',
                                },{
                                    type: 'danger',
                                    z_index: '99999999',
                                });
                        }

                    })
                }
            })
        })


    })
</script>