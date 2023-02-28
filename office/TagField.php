<?php

if (Auth::guest()) {
    redirect_to('index.php');
}
?>
<div class="text-start px-15 tagInfo d-none">
    <div class="form-group flex-fill mb-15">
        <label class="" for="js-select2-class"><?= lang('lesson_tag') ?></label>
        <div class="input-group">
            <input name="tag" class="form-control bg-light border-light" disabled style="border: 0; border-radius: 0" type="text" required autocomplete="off">
            <div class="input-group-prepend bg-light" style="border: 0; border-radius: 0">
                <a onclick="fieldEvents.showCategoryChoice()" href="javascript:;"
                   data-id="js-items-tab-1-stuff" style="color:blue; padding:10px"
                   class="bg-light border-light"> <?= lang('edit_two') ?> </a>
            </div>
        </div>
        <div class="js-tab-sub-preview bsapp-fs-9 text-muted d-flex" >בחרו תגית המתארת בצורה הקרובה ביותר את השיעור
            *בחירה זו לא תוצג ללקוחות שלכם
        </div>
    </div>
</div>

