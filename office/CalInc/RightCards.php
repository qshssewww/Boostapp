<div class="col-md-2 col-sm-12 order-md-2" dir="rtl">
      
    
    <div class="card spacebottom">
  <a data-toggle="collapse" href="#MenuItems" aria-expanded="true" aria-controls="MenuItems" style="color: black;">
  <div class="card-header text-right">
    <strong><i class="fas fa-cog fa-fw"></i> תפריט</strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuItems">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-right" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    		    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ManageClass.php') !== false) {echo "active";} ?>" href="ManageClass.php" aria-selected="true"><i class="fas fa-university
 fa-fw"></i> ניהול שיעורים</a>  
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DeskPlanNew.php') !== false) {echo "active";} ?>" href="DeskPlanNew.php" aria-selected="true"><i class="fas fa-calendar-check fa-fw"></i> יומן שיעורים</a>
    
 			<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Cal.php') !== false) {echo "active";} ?>" href="Cal.php" aria-selected="true"><i class="fas fa-calendar-alt fa-fw"></i> יומן פעילויות</a>
 			
	        <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ManageClassHistory.php') !== false) {echo "active";} ?>" href="ManageClassHistory.php" aria-selected="true"><i class="fas fa-archive fa-fw"></i> ארכיון שיעורים</a>

</div>      
      
  </div>
	</div></div>
      
    
    
    
    
</div>






<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuItems').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>
