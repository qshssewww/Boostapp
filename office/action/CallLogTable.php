<?php require_once '../../app/init.php'; ?>

<?php

$server_output = @$_POST['Json'];
$result = $server_output;
$json = json_decode($result, true);

if ($server_output!='') {

?>

<table class="table table-bordered table-hover dt-responsive text-right wrap LogCall" dir="rtl"  cellspacing="0" width="100%" id="AccountsTable">
		<thead class="thead-dark">
			<tr>
				<th style="text-align:right;">#</th>
				<th style="text-align:right;">תאריך</th>
				<th style="text-align:right;">שעה</th>
				<th style="text-align:right;">סוג</th>
                <th style="text-align:right;">משך שיחה</th>
                <th style="text-align:right;">הקלטה</th>
                <th style="text-align:right;">נציג</th>
                <th style="text-align:right;">סטטוס</th>
			</tr>
		</thead>
		<tbody>

<?php
$i = '1';            
foreach($json['CDR_LIST'] as $key => $customer) {                       
?> 
<tr>
<td><?php echo $i; ?></td> 
<td><?php echo with(new DateTime(@$customer['Date']))->format('d/m/Y'); ?></td> 
<td><?php echo with(new DateTime(@$customer['Date']))->format('H:i'); ?></td> 
<td><?php echo @$customer['Type']; ?></td> 
<td><?php echo @$customer['Duration']; ?> שניות</td> 
<td><?php if (@$customer['RecordURL']!='' && @$customer['Duration']>'0'){ ?>
<audio controls>
<source src="<?php echo @$customer['RecordURL']; ?>" type="audio/mpeg">
הדפדפן שלך לא תומך בתוסף.    
</audio>   
<?php } ?>    
</td> 
<td><?php echo @$customer['RepresentativeName']; ?></td>  
<td><?php echo @$customer['DialStatus']; ?></td>      
</tr>                    
<?php ++$i; } ?>             
            
		</tbody>
	</table> 


<script>
    $('.LogCall').DataTable({
		responsive: true,
		"language": {
			"processing":   "מעבד...",
			"lengthMenu":   "הצג _MENU_ פריטים",
			"zeroRecords":  "לא נמצאו רשומות מתאימות",
			"emptyTable":   "לא נמצאו רשומות מתאימות",
			"info": "_START_ עד _END_ מתוך _TOTAL_ רשומות" ,
			"infoEmpty":    "0 עד 0 מתוך 0 רשומות",
			"infoFiltered": "(מסונן מסך _MAX_  רשומות)",
			"infoPostFix":  "",
			"search":       "חיפוש: ",
			"url":          "",
			"paginate": {
				"first":    "ראשון",
				"previous": "קודם",
				"next":     "הבא",
				"last":     "אחרון"
    					}
		},
        pageLength: 100,
		dom: "Bfrtip",
		buttons: [
        <?php if (Auth::userCan('98')): ?>     
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'לוג מרכזייה', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'לוג מרכזייה' , className: 'btn btn-dark'},
        <?php endif ?>    
        ],
	});
</script>


<?php } else { ?>

<table class="table table-bordered table-hover dt-responsive text-right wrap Log" dir="rtl"  cellspacing="0" width="100%" id="AccountsTable">
		<thead class="thead-dark">
			<tr>
				<th style="text-align:right;">#</th>
				<th style="text-align:right;">תאריך</th>
				<th style="text-align:right;">שעה</th>
				<th style="text-align:right;">סוג</th>
                <th style="text-align:right;">זמן שיחה</th>
                <th style="text-align:right;">הקלטת שיחה</th>
                <th style="text-align:right;">נציג</th>
			</tr>
		</thead>
		<tbody>

     
		</tbody>
	</table> 


<?php } ?>
