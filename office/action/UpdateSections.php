<?php require_once '../../app/initcron.php';

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('sections')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();
?>

                <div class="form-group" >
                <label>כותרת</label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Title); ?>" required>
                </div>   

                <div class="form-group" >
                <label>סניף</label>
                <select class="form-control text-start" name="Brands" id="BrandsTypeClass" >
				<?php
                $b = '1';    
				$ClassTypes = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   
                if (!empty($ClassTypes)){     
				foreach ($ClassTypes as $ClassType) { ?> 	
				<option value="<?php echo $ClassType->id; ?>" <?php if ($Items->Brands==$ClassType->id){ echo 'selected';} else {} ?>><?php echo $ClassType->BrandName ?></option>
				<?php ++$b; } } else { ?>
                <option value="0">סניף ראשי</option>        
                <?php } ?>    
				</select>
                </div>	  

                <div class="form-group" >
                <label>חדר לאימונים אישיים?</label>
                <select class="form-control text-start" name="Private" id="PrivateSection2" > 	
				<option value="0" <?php echo ($Items->Private == 0) ? 'selected' : ''; ?>>לא</option>
                <option value="1" <?php echo ($Items->Private == 1) ? 'selected' : ''; ?>>כן</option>
				</select>
                </div>	
    
                <div id="DivPrivateSection2" style="display: <?php if ('1'==$Items->Private){ echo 'block';} else { echo 'none'; } ?>;">
                <div class="form-group" >
                <label>מקסימום מתאמנים באימון אישי</label>
                <input type="number" name="MaxClient" id="MaxClient" min="1" class="form-control" value="<?php echo htmlentities($Items->MaxClient); ?>">
                </div>   
                </div>

                <div class="form-group" >
                    <label><?= lang('section_location') ?></label>
                    <select class="form-control text-start" name="outdoor" id="outdoor_edit" >
                        <option value="0" <?php echo !$Items->outdoor ? 'selected' : '' ?>><?= lang('indoor') ?></option>
                        <option value="1" <?php echo $Items->outdoor ? 'selected' : '' ?>><?= lang('outdoor') ?></option>
                    </select>
                </div>

                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>  


<script>
$("#PrivateSection2").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivPrivateSection2.style.display = "block";     
  } 
  else {
  DivPrivateSection2.style.display = "none";       
  }    
});	 

</script>

                