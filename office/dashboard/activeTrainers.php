<div class="card-header text-right">
	<strong class="text-secondary">
    <span><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span> מתאמנים פעילים</strong>
</div>

<span class="text-center font-weight-bold display-3" style="padding-top: 10px;" ng-app="count" ng-cloak ng-controller="controllercount"
 count-to="{{DataCount}}" value="0" duration="1">
</span>

<div class="card-body text-right DivScroll" style='min-height:273px; max-height:273px; overflow-y:scroll; overflow-x:hidden;'>
	<span class="text-right text-secondary">
		<small class="font-weight-bold">סוג מנויים</small>
	</span>
	<?php
$i = '1';      
$MemberShipTypes = DB::table('membership_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Count','DESC')->get();
foreach ($MemberShipTypes as $MemberShipType) {      
?>
	<p class="text-right">
		<?php echo $MemberShipType->Type ; ?>
		<strong class="float-left">
			<?php if ($i=='1'){ ?>
			<mark>
				<?php echo $MemberShipType->Count ; ?>
			</mark>
			<?php } else { echo $MemberShipType->Count ; } ?>
		</strong>
	</p>
	<?php ++ $i; } ?>
</div>

<span class="text-center text-secondary">
	<small class="font-weight-bold">
		<a href="Client.php?Act=0" class="text-secondary">ניהול לקוחות >></a>
	</small>
</span>