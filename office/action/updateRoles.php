<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('roles')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();
$myArray = explode(',', $Items->permissions);
?>

                <div class="form-group" dir="rtl">
                <label>כותרת הרשאה</label>
                <input type="text" name="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>" required>
                </div>   
                <hr>




  <div id="Eaccordion">
                    
                 <?php 
                $RolesCategories = DB::table('rolescategory')->where('Status', 0)->orderBy('id', 'ASC')->get();
                foreach ($RolesCategories as $RolesCategorie) {
                ?>  
                    
  <div class="card">
    <div class="card-header" id="EheadingOne<?php echo $RolesCategorie->id; ?>"  data-toggle="collapse" data-target="#EcollapseOne<?php echo $RolesCategorie->id; ?>" aria-expanded="false" aria-controls="EcollapseOne<?php echo $RolesCategorie->id; ?>">
      <h5 class="mb-0">
        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#EcollapseOne<?php echo $RolesCategorie->id; ?>" aria-expanded="false" aria-controls="EcollapseOne<?php echo $RolesCategorie->id; ?>">
          <?php echo $RolesCategorie->Title; ?>
        </button>
      </h5>
    </div>

    <div id="EcollapseOne<?php echo $RolesCategorie->id; ?>" class="collapse" aria-labelledby="EheadingOne<?php echo $RolesCategorie->id; ?>" data-parent="#Eaccordion">
      <div class="card-body">
                <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="ECheckAll<?php echo $RolesCategorie->id; ?>">
                <label class="custom-control-label" for="ECheckAll<?php echo $RolesCategorie->id; ?>">בחר הכל</label>
                </div>
  
  <table class="table">
  <thead>
    <tr>
      <th width="5%">#</th>
      <th>כותרת</th>
      <th width="10%">צפייה</th>
      <th width="10%">עריכה</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $i = '1';                    
  $Roles1 = DB::table('roleslist')->where('Category', '=', $RolesCategorie->id)->where('Status',0)->groupBy('Group')->orderBy('id', 'ASC')->get();
  foreach ($Roles1 as $Role1) {
  ?>    
   <tr>
   <td><?php echo $i; ?></td>
   <td><?php echo $Role1->Action; ?></td>  
  <?php      
  $RoleGroups = DB::table('roleslist')->where('Category', '=', $RolesCategorie->id)->where('Status',0)->where('Group', '=', $Role1->Group)->orderBy('View', 'ASC')->orderBy('id', 'ASC')->get();
  foreach ($RoleGroups as $RoleGroup) {
  $checked = (in_array($RoleGroup->id, $myArray)) ? ' checked="checked"' : '';
  ?>  
        
   <?php if ($RoleGroup->Single=='1' && $RoleGroup->View=='0') { ?>       
   <td align="center">
   <div class="pretty p-icon p-toggle p-plain">
        <input type="checkbox" class="ECheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" <?php echo $checked;  ?>  />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-eye"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-eye-slash"></i>
            <label></label>
        </div>
    </div>
   </td>   
   <td></td>       
   <?php } else if ($RoleGroup->Single=='1' && $RoleGroup->View=='1'){   ?> 
   <td></td>       
   <td align="center">
   <div class="pretty p-icon p-toggle p-plain">
        <input type="checkbox" class="ECheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" <?php echo $checked;  ?> />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-edit"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-times"></i>
            <label></label>
        </div>
    </div>       
   </td>       
   <?php } else { ?>       
   <td align="center">
       
     <?php if ($RoleGroup->View=='0'){ ?>
       
    <div class="pretty p-icon p-toggle p-plain">
        <input type="checkbox" class="ECheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" <?php echo $checked;  ?> />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-eye"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-eye-slash"></i>
            <label></label>
        </div>
    </div>     
       
    <?php } else  {  ?>  
     <div class="pretty p-icon p-toggle p-plain">
        <input type="checkbox" class="ECheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>"  <?php echo $checked;  ?> />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-edit"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-times"></i>
            <label></label>
        </div>
    </div>   
    <?php } ?>   
       
       
       
  </td>
  <?php  } } ?>     
   </tr>      
   <?php ++$i; } ?>     
      
      
 </tbody>
      
          </table>
      

          
          
      </div>
    </div>
  </div>
                    
        <?php } ?>              

</div> 

<script> 
    
 <?php 
$RolesCategories = DB::table('rolescategory')->orderBy('id', 'ASC')->get();
foreach ($RolesCategories as $RolesCategorie) {
?>      
    
$('#ECheckAll<?php echo $RolesCategorie->id ?>').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $('.ECheckAll<?php echo $RolesCategorie->id ?>').each(function() {
            this.checked = true;                        
        });
    } else {
        $('.ECheckAll<?php echo $RolesCategorie->id ?>').each(function() {
            this.checked = false;                       
        });
    }
});      
    
<?php } ?>   
    
</script>    