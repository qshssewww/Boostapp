<form class="list-group bsapp-draggable-fields js-draggable-fields" id="js_field_items"  >
    <div class="d-flex mb-10">
        <h6 class="pis-30" style="width:calc( 100% - 100px );"><?php echo lang('editin_fields')?></h6>
        <h6 class="bsapp-fs-14 mie-10">חובה</h6>
        <h6  class="bsapp-fs-14"><?php echo lang('displayed')?></h6>
    </div>
    <?php foreach ($fieldsDetails as $keyField => $singleField): ?>
        <?php
        if ($singleField->system_field == 0  && $singleField->for_minor == 0):
            $options = json_decode($singleField->options);

            $required = ($singleField->mandatory == 1) ? "checked" : " ";
            $visible = ($singleField->show == 1) ? "checked" : " ";
            ?>
            <div class="list-group-item w-100 js-item p-0 d-flex align-items-start  mb-12 border border-transparent rounded"  js-field-id="<?php echo $singleField->field_id; ?>">
                <div class="d-flex align-items-center text-gray-700  py-7 px-10 js-grip-handle">
                    <i class="fas fa-grip-vertical"></i>
                </div>

                <div class="d-flex flex-column w-100 js-item-view bsapp-item-view overflow-hidden rounded" >
                    <div class="d-flex">
                        <div class="d-flex justify-content-between align-items-center  bg-light py-7 px-10 border border-light js-item-text" style="width : calc( 100% - 100px ); ">
                            <span>
                                <?php echo $singleField->name; ?>
                            </span>
                            <?php if ($singleField->type == 'checkbox' || $singleField->type == 'multi' || $singleField->type == 'multiCheck'): ?>
                                <a data-toggle="collapse" class="mie-10 bsapp-fs-12" href="#js-collapse-<?php echo $singleField->field_id; ?>" >Options</a>
                            <?php endif; ?>
                        </div>                        
                        <div class="d-flex align-items-center w-100p bg-light py-7 px-10">
                            <div class="custom-control custom-checkbox  custom-control-inline mx-0 mie-10">
                                <input type="checkbox" id="js-form-<?php echo $singleField->form_id; ?>-req-field-<?php echo $singleField->field_id; ?>" name="js-checkbox-mandatory" class="custom-control-input js-checkbox-mandatory" <?php echo $required; ?>>
                                <label class="custom-control-label" for="js-form-<?php echo $singleField->form_id; ?>-req-field-<?php echo $singleField->field_id; ?>"></label>
                            </div>
                            <div class="custom-control custom-checkbox  custom-control-inline mx-0 mie-10">
                                <input type="checkbox" id="js-form-<?php echo $singleField->form_id; ?>-vis-field-<?php echo $singleField->field_id; ?>" name="js-checkbox-visible" class="custom-control-input js-checkbox-visible"   <?php echo $visible; ?>>
                                <label class="custom-control-label" for="js-form-<?php echo $singleField->form_id; ?>-vis-field-<?php echo $singleField->field_id; ?>"></label>
                            </div>
                            <a href="javascript:;" class="text-dark"></a>
                            <?php if ($singleField->customer_default_field == 0) { ?>
                                <a href="javascript:;" class="text-gray-700 dropdown-toggle" data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                            <?php } ?>

                            <div class="dropdown-menu  text-start">
                                <?php if ($singleField->type == 'multi' || $singleField->type == 'multiCheck'): ?>
                                    <a class="dropdown-item js-btn-field-edit-modal  text-gray-700  px-8"  data-field-options='<?php echo $singleField->options; ?>'  data-field-type="<?php echo $singleField->type; ?>" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span>ערוך</span></a>
                                <?php else: ?>
                                    <a class="dropdown-item  js-btn-field-edit text-gray-700  px-8" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span>ערוך</span></a>
                                <?php endif; ?>
                                <a class="dropdown-item js-btn-field-delete px-8 text-gray-700" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-user"></i></span> <span>מחק</span></a>
                            </div>
                        </div>
                    </div>
                    <?php if ($singleField->type == 'checkbox' || $singleField->type == 'multi' || $singleField->type == 'multiCheck'): ?>
                        <div id="js-collapse-<?php echo $singleField->field_id; ?>" class="collapse bg-light  py-7 px-10" >
                            <?php
                            if (!empty($options)):
                                foreach ($options as $key => $option):
                                    if ($singleField->type == 'multi'):
                                        ?>
                                        <div class ="custom-control custom-radio mb-10">
                                            <input type = "radio" id = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>" name = "js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>" class = "custom-control-input">
                                            <label class = "custom-control-label" for = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"><?php echo $option; ?></label>
                                        </div>
                                    <?php elseif($singleField->type == 'checkbox'): ?>
                                        <div class ="custom-control custom-radio mb-10">
                                            <label>
                                                <input type="radio" name="choice-radio">
                                                <?php echo lang('yes')?>
                                            </label>
                                            <label>
                                                <input type="radio" name="choice-radio">
                                                <?php echo lang('no')?>
                                            </label>
                                        </div>
                                    <?php break;
                                    else:
                                        ?>
                                        <div class ="custom-control custom-checkbox mb-10">
                                            <input type = "checkbox" id = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>" name = "js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>" class = "custom-control-input">
                                            <label class = "custom-control-label" for = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"><?php echo $option; ?></label>
                                        </div>   
                                    <?php
                                    endif;
                                endforeach;
                            elseif ($singleField->type == 'checkbox'): ?>
                                    <div class ="custom-control custom-radio mb-10">
                                        <label>
                                            <input type="radio" name="choice-radio">
                                            כן
                                        </label>
                                        <label>
                                            <input type="radio" name="choice-radio">
                                            לא
                                        </label>
                                    </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-group d-none w-100 border border-primary rounded js-item-edit overflow-hidden ">
                    <input type="text" class="outline-none border-0 shadow-none py-5 px-10 js-item-input flex-grow-1" id="js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>" name="" value="<?php echo $singleField->name; ?>"  style="width: calc( 100% - 100px );">
                    <div class="input-group-append d-flex justify-content-between">
                        <div class="js-item-field-save btn text-primary py-3 px-7 bsapp-fs-20">
                            <i class="fal fa-check-circle"></i>
                        </div>                                                        
                        <div class="js-item-field-cancel btn text-gray-700 py-3 px-7 bsapp-fs-20">
                            <i class="fal fa-minus-circle"></i>
                        </div>
                    </div>
                </div>                           
            </div> 
        <?php endif; ?>
    <?php endforeach; ?>
</form>
