<div class="col-md-2 col-sm-12 order-md-2" dir="rtl">
      

   <div class="card spacebottom">
  <a data-toggle="collapse" href="#CPAmenu" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
  <div class="card-header text-right">
    <strong><i class="fas fa-plus-square fa-fw"></i> דוחות סטודיו</strong>
  </div>
  </a>
  
  <div class="collapse show" id="CPAmenu">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-right" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    
 <a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Register.php') !== false) {echo "active";} ?>" href="Register.php" aria-selected="true">הצטרפות</a> 
    
<a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'BDay.php') !== false) {echo "active";} ?>" href="BDay.php" aria-selected="true">ימי הולדת</a>     
    
 <a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Bookings.php') !== false) {echo "active";} ?>" href="Bookings.php" aria-selected="true">נוכחות</a> 
 <a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'NoneShow.php') !== false) {echo "active";} ?>" href="NoneShow.php" aria-selected="true">אי נוכחות</a>
 <a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'NoneBooking.php') !== false) {echo "active";} ?>" href="NoneBooking.php" aria-selected="true">אי הרשמה</a>   
    
<a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassAdd.php') !== false) {echo "active";} ?>" href="ClassAdd.php" aria-selected="true">דוח רישום לשיעור</a>   
    
    
<a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassHours.php') !== false) {echo "active";} ?>" href="ClassHours.php" aria-selected="true">דוח שעות לפי מדריך</a>   
<a class="nav-link text-info <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassClient.php') !== false) {echo "active";} ?>" href="ClassClient.php" aria-selected="true">דוח מתאמנים לפי מדריך</a>       
    

</div>      
       
  </div>
	</div></div>
    



    
</div>





<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#CPAmenu, #HokMenu, #System, #Leads').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>




<script>

$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});		

</script>