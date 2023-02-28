<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('caltype')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();

?>

                <div class="form-group" >
                <label><?php echo lang('task_type') ?></label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Type); ?>" required>
                </div> 


 				<div class="form-group">
                <label><?php echo lang('background_color') ?></label>
                <div id="SetDocBackPreviews" style="background-color: <?php echo @$Items->Color; ?>;width:50px;height:10px;display:inline-block;"></div>
                <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColors" onchange="dsfsds()">
                	<option value="#e10025" <?php if ($Items->Color == '#e10025') {echo "selected";} ?>><?php echo lang('red_color') ?></option>
                	<option value="#bd1a2f" <?php if ($Items->Color == '#bd1a2f') {echo "selected";} ?>><?php echo lang('dark_red') ?></option>
                	<option value="#f19218" <?php if ($Items->Color == '#f19218') {echo "selected";} ?>><?php echo lang('orange_color') ?></option>
                	<option value="#f8b43d" <?php if ($Items->Color == '#f8b43d') {echo "selected";} ?>><?php echo lang('yellow_color') ?></option>
                	<option value="#48AD42" <?php if ($Items->Color == '#48AD42') {echo "selected";} ?>><?php echo lang('green_color') ?></option>
                	<option value="#648426" <?php if ($Items->Color == '#648426') {echo "selected";} ?>><?php echo lang('dark_green_color') ?></option>
                	<option value="#17a2b8" <?php if ($Items->Color == '#17a2b8') {echo "selected";} ?>><?php echo lang('turquoise_color') ?></option>
                	<option value="#2b71b9" <?php if ($Items->Color == '#2b71b9') {echo "selected";} ?>><?php echo lang('blue_color') ?></option>
                	<option value="#2B619D" <?php if ($Items->Color == '#2B619D') {echo "selected";} ?>><?php echo lang('dark_blue_color') ?></option>
                	<option value="#e83e8c" <?php if ($Items->Color == '#e83e8c') {echo "selected";} ?>><?php echo lang('pink_color') ?></option>
                	<option value="#b79bf7" <?php if ($Items->Color == '#b79bf7') {echo "selected";} ?>><?php echo lang('purple_color') ?></option>
                	<option value="#6610f2" <?php if ($Items->Color == '#6610f2') {echo "selected";} ?>><?php echo lang('dark_purple_color') ?></option>
                    <option value="#DDAA33" <?php if ($Items->Color == '#DDAA33') {echo "selected";} ?>><?php echo lang('ochre_color') ?></option>
                    <option value="#4B0082" <?php if ($Items->Color == '#4B0082') {echo "selected";} ?>><?php echo lang('indigo_color') ?></option>
                    <option value="#7F003F" <?php if ($Items->Color == '#7F003F') {echo "selected";} ?>><?php echo lang('a_purple_color') ?></option>
                    <option value="#C3B091" <?php if ($Items->Color == '#C3B091') {echo "selected";} ?>><?php echo lang('khaki_color') ?></option>
                    <option value="#7F3F00" <?php if ($Items->Color == '#7F3F00') {echo "selected";} ?>><?php echo lang('brown_color') ?></option>
                    <option value="#FF00FF" <?php if ($Items->Color == '#FF00FF') {echo "selected";} ?>><?php echo lang('magenta_color') ?></option>
                    <option value="#00FFFF" <?php if ($Items->Color == '#00FFFF') {echo "selected";} ?>><?php echo lang('cyan_color') ?></option>
                    <option value="#C41E3A" <?php if ($Items->Color == '#C41E3A') {echo "selected";} ?>><?php echo lang('cardinal_color') ?></option>
                    <option value="#7F0000" <?php if ($Items->Color == '#7F0000') {echo "selected";} ?>><?php echo lang('kermes_color') ?></option>
                    <option value="#007FFF" <?php if ($Items->Color == '#007FFF') {echo "selected";} ?>><?php echo lang('azure_color') ?></option>
                    <option value="#FFDF00" <?php if ($Items->Color == '#FFDF00') {echo "selected";} ?>><?php echo lang('gold_color') ?></option>
                </select>
                </div>

                <hr>

                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>><?php echo lang('active') ?></option>
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>><?php echo lang('hidden') ?></option>
                </select>
                </div>  

<script>
function dsfsds() {
    var x = document.getElementById("DocsBackgroundColors").value;
    document.getElementById("SetDocBackPreviews").style.backgroundColor = x;
}    
</script> 