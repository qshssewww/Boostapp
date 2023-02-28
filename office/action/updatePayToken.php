<?php
require_once '../../app/initcron.php';
require_once '../Classes/Item.php';

if(Auth::guest()) {
    exit;
}
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$TokenId = @$_REQUEST['TokenId'];

$Tokens = DB::table('paytoken')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $TokenId)->first();


$TokenInfo = DB::table('token')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$Tokens->TokenId)->first();  

if (@$Tokens->Status=='0'){
$TokensStatus = '<span class="text-success">פעיל</span>';    
}
else {
$TokensStatus = '<span class="text-danger">בוטל</span>';     
}


if (@$Tokens->TypeKeva=='0'){
$TypeToken = 'מתחדש';    
}
else {
$TypeToken = 'מוגבל בחזרות';     
}

$UsersName = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$Tokens->UserId)->first();



$CountSuccessKeva = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=' , $TokenId)->where('Status', '=' , '1')->count();
$CountFailsKeva = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=' , $TokenId)->where('Status', '=' , '2')->count();
$CountFailsTotalKeva = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=' , $TokenId)->where('Status', '=' , '4')->count();


?>
       <style>
       
           .DivScroll::-webkit-scrollbar {
             width: 5px;
             padding-left: 0px;
             margin-left: 0px;
           } 
           
             .DivScroll::-webkit-scrollbar-thumb {
             background-color: darkgrey;
             outline: 1px solid slategray;
            padding-left: 0px;
             margin-left: 0px;     
           }    
           
       
</style>   

<div class="row">

<div class="col-md-3"><?php echo lang('item_single') ?>:
    <br>
    <?php
    if (!$Tokens->MultiItems):
        echo $Tokens->Text;
    else:
        $itemIdArr = explode(',', $Tokens->MultiItems);
        $uniqueItemIdArr = array_count_values($itemIdArr); ?>
        <a tabindex="0" data-toggle="popover" data-trigger="hover" title="<?php echo lang('items') ?>" data-html="true"
             data-content=
             "<?php
             foreach ($uniqueItemIdArr as $itemId => $occurrences){
//                 $itemObj = new Item($itemId);
                 $itemObj = Item::find($itemId);
                 echo $itemObj->__get('ItemName') . '<br>';
             }
             ?>">
            <?php echo count($itemIdArr). ' ' .lang('items')?>
        </a>
    <?php endif; ?>
</div>
<div class="col-md-3">סוג ה.קבע: <?php echo $TypeToken; ?></div>
<div class="col-md-3">הוגדר: <?php if (@$UsersName->display_name=='') { echo 'אוטומטי'; } else { echo $UsersName->display_name; } ?></div>     
<div class="col-md-3">סטטוס: <?php echo $TokensStatus; ?></div>    

</div>


<div class="row">

<div class="col-md-3"></div>
<div class="col-md-3"><a href="javascript:void(0);" class="SuccessKeva" data-kevaid="<?php echo $TokenId; ?>">חיובים מוצלחים (<?php echo @$CountSuccessKeva; ?>)</a></div>     
<div class="col-md-3"><a href="javascript:void(0);" class="FailsKeva text-danger" data-kevaid="<?php echo $TokenId; ?>">חיובים נכשלים (<?php echo @$CountFailsKeva; ?>)</a></div> 
<div class="col-md-3"><a href="javascript:void(0);" class="FailsTotalKeva text-warning" data-kevaid="<?php echo $TokenId; ?>">חוב אבוד (<?php echo @$CountFailsTotalKeva; ?>)</a></div>     

</div>

<hr>

 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:420px; overflow-y:scroll; overflow-x:hidden;'> 
<table class="table table-hover text-right" style="font-size:12px; font-weight:bold;" dir="rtl" id="Token">
  <thead >
          <tr style="background-color:#bce8f1;">
            <th align="right" style="text-align:right;" width="10%">מס' חיוב</th>
            <th align="right" style="text-align:right;">פריט</th>  
            <th align="right" style="text-align:right;">סכום החיוב</th>
            <th align="right" style="text-align:right;">חיוב הבא</th> 
            <th align="right" style="text-align:right;">טוקן</th>   
            <th align="right" style="text-align:right;">פעולות</th>
          </tr>
        </thead>
<tbody>
  
<?php
    
$GetKevaInfos = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=' , '0')->where('KevaId', '=' , $TokenId)->orderBy('Date', 'ASC')->get();
foreach ($GetKevaInfos as $GetKevaInfo) {
     
  
if ($GetKevaInfo->ActStatus=='1') {
    
    
}    
     if ($GetKevaInfo->ActStatus=='1'){
    $ColorClass = 'class="text-secondary"';
    $TrueBalanceValueColor = 'text-secondary'; 
    $TrueDateColor = 'text-secondary';
    $ColorTextClass = 'class="text-secondary"';
    $LineThrough = 'style="text-decoration: line-through;"';    
    }
    else{
    $ColorClass = '';
    $TrueBalanceValueColor = ''; 
    $TrueDateColor = '';
    $ColorTextClass = '';
    $LineThrough = '';    
    }     

    
              $CardTokef = @$TokenInfo->Tokef; 
                  
              if (@$TokenInfo->Type=='0') {      
              $L4digit = substr($TokenInfo->Token, -4);
              $Month =  mb_substr($CardTokef, 2);	
              $Year = '20'.mb_substr($CardTokef, 0, 2);	
              }
              else {
              $L4digit = @$TokenInfo->L4digit;      
              $Month =  mb_substr($CardTokef, 0, 2);	
              $Year = '20'.mb_substr($CardTokef, 2);	    
              }      
    
?>    
    
    <tr <?php echo $ColorClass; ?>>
    <td <?php echo $LineThrough; ?> ><?php echo $GetKevaInfo->NumPayment; ?></td>    
    <td <?php echo $LineThrough; ?>><?php echo $Tokens->Text; ?></td>
    <td <?php echo $LineThrough; ?>>₪<?php echo $GetKevaInfo->Amount; ?></td>
    <td <?php echo $LineThrough; ?>><?php echo with(new DateTime(@$GetKevaInfo->Date))->format('d/m/Y'); ?></td>
    <td <?php echo $LineThrough; ?>><?php echo $L4digit.'****'; ?></td>    
    <td><a href="javascript:void(0);" class="EditKeva" data-rowid="<?php echo $GetKevaInfo->id; ?>" data-kevaid="<?php echo $TokenId; ?>" >נהל חיוב</a></td>    
    
    </tr>
    
 <?php } ?>   

    </tbody>

    </table>
</div>

<script>

$(function () {
    $('[data-toggle="popover"]').popover({
        container: 'body',
        trigger: 'hover',
    })
})

$(".EditKeva").click(function(){

    var TokenId = $(this).data("kevaid");
    var RowId =  $(this).data("rowid");
        
   document.getElementById("resultPayToken").innerHTML=" ";
   $('#resultPayToken').load('/office/action/updatePayTokenEdit.php?TokenId='+TokenId+'&RowId='+RowId);
   $('#ShowSaveKeva').show();    
    
 });


$(".SuccessKeva").click(function(){

    var TokenId = $(this).data("kevaid");
        
   document.getElementById("resultPayToken").innerHTML=" ";
   $('#resultPayToken').load('/office/action/SuccessKeva.php?TokenId='+TokenId);
   $('#ShowSaveKeva').hide();    
    
 }); 
    
$(".FailsKeva").click(function(){

    var TokenId = $(this).data("kevaid");
        
   document.getElementById("resultPayToken").innerHTML=" ";
   $('#resultPayToken').load('/office/action/FailsKeva.php?TokenId='+TokenId);
   $('#ShowSaveKeva').hide();    
    
 });     

  $(".FailsTotalKeva").click(function(){

    var TokenId = $(this).data("kevaid");
        
   document.getElementById("resultPayToken").innerHTML=" ";
   $('#resultPayToken').load('/office/action/FailsTotalKeva.php?TokenId='+TokenId);
   $('#ShowSaveKeva').hide();    
    
 });     
  
    
    
</script>


