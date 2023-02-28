<?php require_once '../../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

if(Auth::user()->role_id !== '1') {
    echo "You ar not authorized to see this";
    die();
}

$sql = DB::table('fbtocompany');


if($_SERVER['REQUEST_METHOD'] === 'POST'){

    switch($_POST['action']){
        case "checktoken":
            $token = $sql->where('id', '=', (int) $_POST['id'])->select('active_token')->limit(1)->get()[0];

            $url = 'https://graph.facebook.com/debug_token?input_token='.$token->active_token.'&access_token=1931387196922899|5trqAb6HSxjsGhkEuDT80xRLtdE';

            $response = array('err'=>false, 'message'=> 'facebook debug', 'item'=> json_decode(file_get_contents($url)));
        break;
        case "update":
            $update = array();
            $update[$_POST['field']] = $_POST['value'];
            $o = $sql->where('id', '=', (int) $_POST['id'])->update($update);
            $response = array('err'=>false, 'message'=> 'Updated', "q"=>$o);
        break;

        default:
            $response = array('err'=>true, 'message'=> 'Unknown action');
    }

    header('Content-type: application/json');
    http_response_code($response['err']? 500 : 200);
    echo json_encode( $response );


    exit;
}

$clients = $sql->get();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ניהול דפי פייסבוק לקוחות בוסטאפפ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
    
    <style>
        body{
            text-align: right
        }
    </style>
</head>
<body dir="rtl">
    <div class="container">
        <table class="table table-stripped">
            <thead>
                <th data-title="מספר חברה">מספר חברה</th>
                <th data-title="שם דף">שם דף</th>
                <th data-title="סטאטוס חיבור לקוח">סטאטוס חיבור לקוח</th>
                <th data-title="נעילת בוסטאפ">נעילת בוסטאפ</th>
                <th data-title="כלים">כלים</th>
            </thead>
            <tbody>
                <?php foreach($clients as $client) : ?>
                <tr>
                    <th data-title="מספר חברה"><?php echo $client->CompanyNum; ?></th>
                    <th data-title="שם דף"><a href="https://www.facebook.com/<?php echo $client->PageId; ?>" target="_blank"><?php echo $client->name; ?></a></th>
                    <th data-title="סטאטוס חיבור לקוח">
                        <input type="checkbox" data-id="<?php echo $client->id; ?>" data-name="Status" value="1" <?php echo $client->Status == 1? 'checked' : '' ?>>
                    </th>
                    <th data-title="נעילת בוסטאפ">
                        <input type="checkbox"  data-id="<?php echo $client->id; ?>" data-name="AdminDisabled" value="1" <?php echo $client->AdminDisabled == 1? 'checked' : '' ?>>
                    </th>
                    <th data-title="כלים">
                        <a data-checktoken="<?php echo $client->id; ?>" class="btn btn-sm btn-primary text-white">בדיקת טוקן</a>
                        <div data-checktoken-data></div>
                    </th>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<!--    <script src="https://code.jquery.com/jquery-3.3.1.min.js"  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script>-->
    <script src="/assets/js/vendor/jquery-3.5.1.min.js"></script>
    <script>
        jQuery(document).ready(function(){
            jQuery('a[data-checktoken]').on('click', function(){
                var el = jQuery(this);
                var id = el.attr('data-checktoken');

                $.post('', {id: id, action: 'checktoken'}, function(data){
                    if(!data || !data.item || !data.item.data){
                        console.log(data);
                        alert('check console for error');
                        return;
                    }
                    el.next().html('<pre dir="ltr" class="text-left" style="max-width: 350px">'+JSON.stringify(data.item.data, null, 4)+'</pre>');

                })
            })

            jQuery('table input[type="checkbox"]').on('change', function(){
                var el = $(this);
                var checked = el.is(':checked');
                var id = el.attr('data-id');
                var field = el.attr('data-name');

                $( ' <i class="fa fa-spinner fa-spin"></i>').insertAfter( el );
                el.prop('disabled', true);

                $.post('', {action: 'update', field: field, value: (checked? '1': '0'), id: id }, function(){
                    el.prop('disabled', false).next().remove();
                })

                
            })
        });
    </script>
</body>
</html>