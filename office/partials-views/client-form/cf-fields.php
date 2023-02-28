<?php
/**
 * This file contains template for add field popup HTML
 * Field naming structure followed is as below
 * Input name attribute for  the field ->   "js_fm_<FORM_ID HERE>_fd_<FIELD_ID>"
 * Input id attribute structure for the field -> "js-fm-<FORM_ID HERE>-fd-<FIELD_ID>-ok-<KEY_ID_IN_CASE_OF_MULTI_OPTIONS>"
 */
foreach ($fieldsDetails as $keyField => $singleField):
    $required = ($singleField->mandatory == 1) ? "required" : "";
    $show = $singleField->show;

    if ($show == 1):
        // minor fields:
        if ($singleField->default_id == 11): ?>
                <div class="form-group d-none align-items-center mb-10" data-context="js-for-minor">
                    <label class="fal fa-child mie-16 w-20p my-auto">
                    </label>
                    <div class="flex-fill d-flex">
                        <input type="text" name="first_name" class="form-control  js-minor-first-name mie-10" placeholder="<?php echo  lang('first_name')?>" <?php echo $required; ?>>
                        <input type="text" name="last_name" class="form-control js-minor-last-name"  placeholder="<?php echo  lang('last_name')?>" <?php echo $required; ?>>
                    </div>
                </div>
        <?php endif; ?>

        <!--minor_phone-->
        <?php if ($singleField->default_id == 13): ?>
            <div class="form-group d-none align-items-center mb-10" data-context="js-for-minor" >
                <label class="fal fa-phone mie-10  w-20p  my-auto">
                </label>
                <div class="d-flex  align-items-center  position-relative w-100 pie-10">
                    <input placeholder="מספר נייד" name="phone" type="tel" class="form-control" id="contactMobile" pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="<?php echo lang('incorrect_mobile') ?>" <?php echo $required; ?>>

                </div>
                <div class="w-150p">
                    <select name="phone_zone" class="js-select2 form-control" <?php echo $required; ?> >
                        <option>+972</option>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <!--minor_relationship-->
        <?php if ($singleField->default_id == 1045550): ?>
            <div class="form-group d-none align-items-center mb-10" data-context="js-for-minor" >
                <label class="fal fa-user-circle mie-10 w-20p  my-auto"></label>
                <div class="d-flex  align-items-center  position-relative w-100">
                    <select name="relationship" class="js-select2 js-minor-relationship form-control" placeholder="<?php echo lang('minor_relationship')?>" <?php echo $required; ?>>
                        <option value="1"><?php echo lang('father') ?></option>
                        <option value="2"><?php echo lang('mother') ?></option>
                        <option value="3"><?php echo lang('brother_or_sister') ?></option>
                        <option value="4"><?php echo lang('relative') ?></option>
                        <option value="5"><?php echo lang('other') ?></option>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <!--minor fields done-->

        <!--more details-->

        <!-- date birthday-->
        <?php if ($singleField->default_id == 8):?>
            <div class="form-group d-flex align-items-center mb-10" >
                <label class="fal fa-birthday-cake mie-10 w-20p  my-auto">
                </label>
                <input class="form-control" type="date" name="date_of_birth" placeholder="<?php echo lang('date_birthday')?>" <?php echo $required; ?>/>
            </div>
        <?php endif; ?>

        <!-- gender-->
        <?php if ($singleField->default_id == 7): ?>
            <div class="form-group d-flex align-items-center mb-10">
                <label class="fal fa-venus-mars mie-10 w-20p my-auto" for="gender">
                </label>
                <div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="js-gender-male" name="gender" value="1" class="custom-control-input parsley-validated" <?php echo $required; ?> >
                        <label class="custom-control-label" for="js-gender-male"><?php echo lang('male')?> </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="js-gender-female" value="2" name="gender" class="custom-control-input">
                        <label class="custom-control-label" for="js-gender-female"><?php echo lang('female')?></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="js-gender-unicorn" value="0" name="gender" class="custom-control-input">
                        <label class="custom-control-label" for="js-gender-unicorn"><?php echo lang('other')?></label>
                    </div>
                </div>
            </div>
         <?php endif; ?>

        <!-- email-->
        <?php if ($singleField->default_id == 4): ?>
            <div class="form-group d-flex align-items-center mb-10" >
                <label class="fal fa-envelope mie-10 w-20p my-auto">
                </label>
                <input type="email" name="email" class="form-control js-summary-email" data-parsley-type="email" placeholder="<?php echo lang('email')?>" <?php echo $required; ?>>
            </div>
        <?php endif; ?>

        <!--id-->
        <?php if ($singleField->default_id == 5): ?>
        <div class="form-group d-flex align-items-center mb-10">
            <label class="fal fa-fingerprint mie-10 w-20p my-auto"></label>
            <input type="number" data-parsley-type="number" name="id_card" class="form-control"  placeholder="<?php echo lang('id_card')?>" <?php echo $required; ?>>
        </div>
        <?php endif; ?>

        <!--city-->
        <?php if ($singleField->default_id == 9): ?>
            <div class="form-group d-flex align-items-center mb-10">
                <label class="fal fa-location-arrow mie-10 w-20p my-auto">
                </label>
                <select class="cities-select form-control" name="city" id="cities-select" <?php echo $required; ?>>
                </select>
                    <!--                <input type="text" name="city" class="form-control"  placeholder="--><?php //echo lang('city')?><!--" >-->
            </div>
        <?php endif; ?>

        <?php if ($singleField->default_id == 6000): ?>
            <div class="form-group d-flex align-items-center mb-10">
                <label class="fal fa-city mie-10 w-20p my-auto">
                </label>
                <input type="text" name="street" class="form-control"  placeholder="<?php echo lang('street')?>" <?php echo $required; ?>>
            </div>
        <?php endif; ?>
        <!--more details done-->

        <!--Manually defined fields-->
        <?php if ($singleField->default_id == 0): ?>
            <?php if (in_array($singleField->type, ['text', 'tel', 'number', 'date'])) : ?>
                <div class="form-group d-flex align-items-center mb-10">
                    <label class="fal fa-thumbtack mie-10 w-20p my-auto">
                    </label>
                    <input type="<?php echo $singleField->type; ?>" name="form_id-<?php echo $singleField->form_id; ?>,field_id-<?php echo $singleField->field_id; ?>" class="form-control"  placeholder="<?php echo $singleField->name; ?>" <?php echo $required; ?>>
                </div>


            <?php elseif ($singleField->type == 'multiCheck'): ?>
                <div class="form-group d-flex align-items-center mb-10 mr-20">
                    <?php echo $singleField->name; ?>
                </div>
                <div class="form-group d-flex flex-column">
                    <?php $options = json_decode($singleField->options);
                    if (!empty($options)):
                        foreach ($options as $key => $option):?>
                            <div class ="custom-control custom-checkbox mb-5 mr-10"">
                                <input type = "checkbox" value="<?php echo $option; ?>" id = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"
                                       name = "form_id-<?php echo $singleField->form_id; ?>,field_id-<?php echo $singleField->field_id; ?>"
                                       class = "custom-control-input" <?php echo ($key===0 && $required) ? 'data-parsley-mincheck="2"': ""?> >
                                <label class = "custom-control-label" for = "js-view-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"><?php echo $option; ?></label>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>

            <?php elseif ($singleField->type == 'multi'): ?>
                <div class="form-group d-flex align-items-center mb-10 mr-20">
                    <?php echo $singleField->name; ?>
                </div>
                <div class=" form-group d-flex flex-column">
                    <?php $options = json_decode($singleField->options);
                    if (!empty($options)):
                        foreach ($options as $key => $option):?>
                            <div class = "custom-control custom-radio mb-5 mr-10">
                                <input type = "radio" value="<?php echo $option; ?>" id = "js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"
                                       name = "form_id-<?php echo $singleField->form_id; ?>,field_id-<?php echo $singleField->field_id; ?>" class = "custom-control-input" <?php echo $required; ?> <?php echo ($key==0)? $required: ""?>>
                                <label class = "custom-control-label" for = "js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"><?php echo $option; ?></label>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>

            <?php elseif ($singleField->type == 'checkbox'):
                $singleField->options = '[{"name": "כן", "value":1}, {"name": "לא", "value":0}]'; ?>
                <div class="form-group d-flex align-items-center mr-20">
                    <?php echo $singleField->name; ?>
                </div>
                <div class="form-group d-flex mr-10">
                    <?php $options = json_decode($singleField->options);
                    if (!empty($options)):
                        foreach ($options as $key => $option):?>
                            <div class="custom-control custom-radio custom-control-inline mb-5">
                                <input type="radio" value="<?php echo $option->value; ?>" id="js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"
                                       name="form_id-<?php echo $singleField->form_id; ?>,field_id-<?php echo $singleField->field_id; ?>" class="custom-control-input" <?php echo ($key==0)? $required: ""?>>
                                <label class="custom-control-label" for="js-fm-<?php echo $singleField->form_id; ?>-fd-<?php echo $singleField->field_id; ?>-ok-<?php echo $key; ?>"><?php echo $option->name; ?></label>
                            </div>
                            <?php
                        endforeach;
                    endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>

<div class="form-group align-items-center mb-10 mr-30">
    <a class="mie-2 " id="js-open-textarea-medical" href="javascript:;" onclick=""><i class="fas fa-plus mie-4 w-20p my-auto"></i></a>
    <span><?php echo lang("customer_card_medical_records")?></span>
    <div class="lex-column js-textarea-edit-mode px-15 py-20 border-bottom border-top border-light" id="add-medical-note" style="display:none;">
        <div class="mb-10">
            <textarea class="form-control js-form-control-textarea" id="textarea-medical-note" name="medical-note" style="margin-top: 0px; margin-bottom: 0px; height: 60px;"></textarea>
        </div>
        <div class="d-flex justify-content-between align-items-start">
            <a class="btn btn-danger mie-8" id="js-textarea-medical-delete" href="javascript:;" onclick=""><?php echo lang("delete")?></a>
        </div>
    </div>
</div>

<div class="form-group align-items-center mb-10 mr-30">
    <a class="mie-2" id="js-open-textarea-crm" href="javascript:;" onclick=""><i class="fas fa-plus mie-4 w-20p my-auto"></i></a>
    <span>הערה</span>
    <div class="lex-column js-textarea-edit-mode px-15 py-20 border-bottom border-top border-light" id="add-crm-note" style="display: none;">
        <div class="mb-10">
            <textarea class="form-control js-form-control-textarea" id="textarea-crm-note" name="crm-note" style="margin-top: 0px; margin-bottom: 0px; height: 60px;"></textarea>
        </div>
        <div class="d-flex justify-content-between align-items-start">
            <a class="btn btn-danger mie-8" id= "js-textarea-cdn-delete" href="javascript:;" onclick=""><?php echo lang("delete")?></a>
        </div>
    </div>
</div>

