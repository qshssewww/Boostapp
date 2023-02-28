<?php
require_once '../../app/init.php';
?>





<?php
$TypeId = $_POST['TypeId'];
$DocId = $_POST['DocId'];

$CompanyNum = Auth::user()->CompanyNum;

$DocGet = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('TypeNumber','=',$DocId)->where('TypeDoc','=',$TypeId)->first();
$DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();

CreateLogMovement(
	'נכנס לדוח פתיחת מסמך'.$DocsTables->TypeTitleSingle.' מספר '.$DocGet->TypeNumber, //LogContent
	'0' //ClientId
);
?>
<span style="font-weight: bold;">
<?php echo $DocsTables->TypeTitleSingle; ?> מספר <span style="color: #48AD42;"><?php echo $DocGet->TypeNumber; ?></span>
<br>
תיעוד פתיחת מסמך ע"י הלקוח
</span>

 <br><br>


<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th class="text-right">#</th>
                <th class="text-right">תאריך</th>
			</tr>
		</thead>
		<tbody>
<?php 
$i = '1';
$DocLogs = DB::table('logopendoc')->where('CompanyNum' ,'=', $CompanyNum)->where('DocsId','=',$DocGet->id)->orderBy('Dates','DESC')->get();            
foreach ($DocLogs as $DocLog) {  

?>            
<tr>
<td><?php echo $i; ?></td> 
<td><?php echo with(new DateTime($DocLog->Dates))->format('d/m/Y H:i:s'); ?></td>     
</tr>
<?php ++$i; } ?>
    </tbody>
</table>


  <div class="form-group">
<button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal">סגור</button>      
  </div>

