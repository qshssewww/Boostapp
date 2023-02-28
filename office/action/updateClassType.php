<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('class_type')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();

?>

                <div class="form-group" >
                <label>כותרת השיעור</label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Type); ?>" required>
                </div>   

                <div class="form-group">
                <label>תיאור השיעור</label>
                <textarea class="form-control summernote" name="ClassNotes" rows="5"><?php echo htmlentities($Items->ClassContent); ?></textarea>
                </div>  

 				<div class="form-group">
                <label>צבע רקע</label>
                <div id="SetDocBackPreviews" style="background-color: <?php echo @$Items->Color; ?>;width:50px;height:10px;display:inline-block;"></div>
                <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColors" onchange="dsfsds()">
                	<option value="#e10025" <?php if ($Items->Color == '#e10025') {echo "selected";} ?>>אדום</option>
                	<option value="#bd1a2f" <?php if ($Items->Color == '#bd1a2f') {echo "selected";} ?>>אדום כהה</option>
                	<option value="#f19218" <?php if ($Items->Color == '#f19218') {echo "selected";} ?>>כתום</option>
                	<option value="#f8b43d" <?php if ($Items->Color == '#f8b43d') {echo "selected";} ?>>צהוב</option>
                	<option value="#48AD42" <?php if ($Items->Color == '#48AD42') {echo "selected";} ?>>ירוק</option>
                	<option value="#648426" <?php if ($Items->Color == '#648426') {echo "selected";} ?>>ירוק כהה</option>
                	<option value="#17a2b8" <?php if ($Items->Color == '#17a2b8') {echo "selected";} ?>>טורקיז</option>
                	<option value="#2b71b9" <?php if ($Items->Color == '#2b71b9') {echo "selected";} ?>>כחול</option>
                	<option value="#2B619D" <?php if ($Items->Color == '#2B619D') {echo "selected";} ?>>כחול כהה</option>
                	<option value="#e83e8c" <?php if ($Items->Color == '#e83e8c') {echo "selected";} ?>>ורוד</option>
                	<option value="#b79bf7" <?php if ($Items->Color == '#b79bf7') {echo "selected";} ?>>סגול</option>
                	<option value="#6610f2" <?php if ($Items->Color == '#6610f2') {echo "selected";} ?>>סגול כהה</option>
                    <option value="#DDAA33" <?php if ($Items->Color == '#DDAA33') {echo "selected";} ?>>אוכרה</option>
                    <option value="#4B0082" <?php if ($Items->Color == '#4B0082') {echo "selected";} ?>>אינדיגו</option>
                    <option value="#7F003F" <?php if ($Items->Color == '#7F003F') {echo "selected";} ?>>ארגמן</option>
                    <option value="#C3B091" <?php if ($Items->Color == '#C3B091') {echo "selected";} ?>>חאקי</option>
                    <option value="#7F3F00" <?php if ($Items->Color == '#7F3F00') {echo "selected";} ?>>חום</option>
                    <option value="#FF00FF" <?php if ($Items->Color == '#FF00FF') {echo "selected";} ?>>מג'נטה</option>
                    <option value="#00FFFF" <?php if ($Items->Color == '#00FFFF') {echo "selected";} ?>>ציאן</option>
                    <option value="#C41E3A" <?php if ($Items->Color == '#C41E3A') {echo "selected";} ?>>קרדינל</option>
                    <option value="#7F0000" <?php if ($Items->Color == '#7F0000') {echo "selected";} ?>>שני</option>
                    <option value="#007FFF" <?php if ($Items->Color == '#007FFF') {echo "selected";} ?>>תכלת</option>
                    <option value="#FFDF00" <?php if ($Items->Color == '#FFDF00') {echo "selected";} ?>>זהב</option>
                </select>
                </div>

                <?php if ($CompanyNum=='569121'){ ?>    
                <div class="form-group">
                <label>צבע רקע לאפליקציה</label>
                <select class="form-control" name="Color2">
                	<option value="#E30613" <?php if ($Items->Color2 == '#E30613') {echo "selected";} ?> >AEROBIC</option>
                	<option value="#0083E1" <?php if ($Items->Color2 == '#0083E1') {echo "selected";} ?> >CORE</option>
                	<option value="#AEAEAE" <?php if ($Items->Color2 == '#AEAEAE') {echo "selected";} ?> >NUTRITION</option>
                	<option value="#000000" <?php if ($Items->Color2 == '#000000') {echo "selected";} ?> >ANAEROBIC</option>
                </select>
                </div>     
                <?php } else { ?>    
                <input type="hidden" name="Color2" value="">    
                <?php } ?>  


                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>   

<script>
$(document).ready(function() {
 $('.summernote').summernote({
     //   placeholder: 'הקלד תיאור לשיעור',
        tabsize: 2,
        height: 100,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol']]
  ]
      });
});	
    
function dsfsds() {
    var x = document.getElementById("DocsBackgroundColors").value;
    document.getElementById("SetDocBackPreviews").style.backgroundColor = x;
}    
</script>

