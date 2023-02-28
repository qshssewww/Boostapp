<?php require_once '../../app/initcron.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('faq')->where('id', '=' , $ItemId)->first();

?>

                <div class="form-group" dir="rtl">
                <label>שאלה</label>
                <input type="text" name="Question" class="form-control" value="<?php echo htmlentities($Items->Question); ?>">
                </div>   


                <div class="form-group" dir="rtl">
                <label>תשובה</label>
                <textarea class="form-control summernote" name="Answer" rows="5"><?php echo $Items->Answer; ?></textarea>
                </div>   
               

                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>מוצג</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>  


<script>

$(document).ready(function() {
 $('.summernote').summernote({
        tabsize: 2,
        height: 100,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],      
    ['para', ['ul', 'ol']],
    ['color', ['color']], 
    ['insert', ['hr']]    
  ]
      });
});	

</script>