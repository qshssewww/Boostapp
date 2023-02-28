<?php
require_once __DIR__.'/../../Classes/PipelineCategory.php';
require_once __DIR__.'/../../Classes/LeadStatus.php';
require_once __DIR__.'/../../Classes/Users.php';

$class_leadStaus = new LeadStatus();

$Supplier = $Supplier ?? Client::find($_GET['u']);
$CompanyNum = $CompanyNum ?? $Supplier->CompanyNum;
$PipeNow = $PipeNow ?? (new Pipeline())->checkPipeId($Supplier->id, $CompanyNum);

$MainPipeLine = new PipelineCategory($PipeNow->MainPipeId);
$PipeTitles = $class_leadStaus->getPipeTitles($CompanyNum, $MainPipeLine->__get('id'));

$AgentForThisLead = Users::find($PipeNow->AgentId) ?? 0;

$GetSuccess = $GetSuccess ?? $class_leadStaus->getLeadStatusByPipeIdByAct($CompanyNum, $PipeNow->MainPipeId, 1);
$GetFails = $GetFails ?? $class_leadStaus->getLeadStatusByPipeIdByAct($CompanyNum, $PipeNow->MainPipeId, 2);
?>

<div class="tab-pane fade" role="tabpanel" id="user-lead">
    <div class="card spacebottom">
        <div class="card-header text-start d-flex justify-content-between justify-content-center">
            <div class="mt-10">
                <i class="fas fa-at fa-fw">
                </i>
                <strong><?php echo lang('lead_info') ?>
                </strong>
            </div>

            <?php if ($Supplier->Status == 1) { ?>
                <button class="btn btn-dark" onclick="clientProfile_tabLeadInfo.reLead(this, <?= $PipeNow->id; ?>, <?= ($PipeTitles[0])->id; ?>)"><?= lang('clientprofile_leadtab_reloadbtn'); ?></button>
            <?php } ?>
        </div>
        <div class="card-body">
            <div class="row px-15"  >
                <?php
                $i = '1';
                foreach ($PipeTitles as $PipeTitle) {
                    ?>
                    <div class="col-md col-sm-12" style="padding: 0px;margin: 0px;" >
                        <ul class="list-group list-special " style="padding: 0px;margin: 0px;">
                            <?php if ($Supplier->Status == 2) {?>
                            <a onclick="clientStatusChange(this,0)" href="javascript:void(0)" class="text-dark" style="text-decoration: none;">
                                <?php } else { ?>
                                <a class="text-dark" style="text-decoration: none;">
                                    <?php } ?>
                                    <li class="list-group-item text-start padding-0 <?php if($PipeNow->PipeId == $PipeTitle->id) {echo "bg-info text-white";} else {echo "bg-light text-dark";} ?> SetPipe" style="padding:15px; <?php if($i != count($PipeTitles)) {echo "border-left:0px;";} ?>" id="SetPipe<?php echo $PipeTitle->id; ?>" >
                                        <i class="fas fa-angle-double-left  text-end fa-lg" style="color: lightgray;">
                                        </i>
                                        <strong style="font-size: 12px;">
                                            <?php echo $PipeTitle->Title; ?>
                                        </strong>
                                    </li>
                                </a>
                        </ul>
                    </div>
                    <?php $i++;} ?>
            </div>
            <br>
            <table class="table table-bordered table-hover text-start wrap"   cellspacing="0" width="100%" id="LeadsTable">
                <tbody>
                <tr>
                    <td style="text-align:start;width:20%;" class="bg-light"><?php echo lang('date') ?>
                    </td>
                    <td  style="text-align: start;">
                        <?php echo with(new DateTime($PipeNow->Dates))->format('d/m/Y H:i:s'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:start;width:20%;" class="bg-light"><?php echo lang('last_status') ?>
                    </td>
                    <td  style="text-align: start;">
                        <div style="display:inline-block;text-align: center;align-content: center; max-width: 30%;" class="text-white wonlosedivbg99" id="<?php echo $GetFails; ?>">
                            <?php if($Supplier->Status == 2) { ?>
                            <ul onclick="clientStatusChange(this,0)" style="list-style-type: none; cursor: pointer;" class="list-group list-special">
                                <?php } else { ?>
                                <ul style="list-style-type: none;" class="list-group list-special">
                                    <?php } ?>
                                    <li class="bg-danger SetPipeV <?php if($PipeNow->PipeId == $GetFails) {echo "hover";} ?>" id="SetPipeV<?php echo $GetFails; ?>" style="padding: 5px;width: 300px; max-width: 100%;" >
                                        <i class="fas fa-trash-alt fa-fw">
                                        </i> <?php echo lang('failure') ?>
                                    </li>
                                </ul>
                        </div>
                        <div style="display:inline-block;text-align: center;align-content: center; max-width: 30%;" class="text-white wonlosedivbg98" id="<?php echo $GetSuccess; ?>">
                            <?php if($Supplier->Status == 2) { ?>
                            <ul onclick="clientStatusChange(this,0)" style="list-style-type: none; cursor: pointer;" class="list-group list-special">
                                <?php } else { ?>
                                <ul style="list-style-type: none;" class="list-group list-special">
                                    <?php } ?>
                                    <li class="bg-success SetPipeV <?php if($PipeNow->PipeId == $GetSuccess) {echo "hover";} ?>" id="SetPipeV<?php echo $GetSuccess; ?>" style=" padding: 5px;width: 300px; max-width: 100%;" >
                                        <i class="fas fa-trophy fa-fw">
                                        </i> <?php echo lang('success') ?>
                                    </li>
                                </ul>
                        </div>
                    </td>
                </tr>
                <?php if ($PipeNow->Source != '') { ?>
                    <tr>
                        <td style="text-align:start;width:20%;" class="bg-light"><?php echo lang('lead_source') ?>
                        </td>
                        <td  style="text-align: start;">
                            <?php echo @$PipeNow->Source; ?>
                        </td>
                    </tr>
                    <?php if(isset($MainPipeLine)) {?>
                        <tr>
                            <!--Change to lang-->
                            <td style="text-align:start;width:20%;" class="bg-light">Pipeline
                            </td>
                            <td  style="text-align: start;">
                                <?php echo $MainPipeLine->Title; ?>
                            </td>
                        </tr>
                    <?php }
                }?>
                <?php
                if (!empty($PipeNow->Info)) {
                    $Loops =  json_decode($PipeNow->Info,true);
                    foreach($Loops['data'] as $key){
                        foreach($key as $key2=>$val){
                            ?>
                            <tr>
                                <td style="text-align:start;width:20%;" class="bg-light">
                                    <?php echo $key2; ?>
                                </td>
                                <td>
                                    <?php
                                    if (is_array($val)) {
                                        echo $val[0];
                                    } else {
                                        echo $val;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php	}	}	}	?>
                <tr>
                    <td style="text-align:start;width:20%;" class="bg-light"><?php echo lang('taking_care_representative') ?>
                    </td>
                    <td id="TakeLeadTD">
                        <?php
                        if (Auth::userCan('141')) {
                            ?>
                            <select name="Agents" class="form-control js-example-basic-single text-start AgentLoop ChangeLeadAgent"  style="width: 100%" data-placeholder="<?php echo lang('choose_representative') ?>">
                                <option value="0"><?php echo lang('without_representative') ?>
                                </option>
                                <?php
                                $AgentLoops = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '1')->get();
                                foreach ($AgentLoops as $AgentLoop) {
                                    if ($PipeNow->AgentId == $AgentLoop->id) {$DoSelected = 'selected';} else {$DoSelected = '';}
                                    echo '<option value="'.$AgentLoop->id.'" '.$DoSelected.'>'.$AgentLoop->display_name.'</option>';
                                }
                                ?>
                            </select>
                            <?php
                        }
                        else {
                            echo $AgentForThisLead->display_name ?? '';
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="clientProfile/tabLeadInfo/tabLeadInfo.js"></script>
