<div class="d-none flex-column justify-content-between bg-white h-100    bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1" data-depth="1">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
        <a  class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left mie-7 bsapp-fs-24"></i>חזור</a>
        <a href="javascript:;" onclick="ClientsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class=""  style="height:calc( 100% - 60px);">
        <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center justify-content-between font-weight-bold">
            <i class="fal fa-address-card"></i><div style="width:calc(100% - 25px);"> מידע מלקוחות </div>
        </div>

        <div class="d-flex justify-content-between  mt-14 mb-10">
            <div>#</div>
            <div class="d-flex" style="width:calc(100% - 25px);">
                <span class="bsapp-fs-14 bsapp-lh-17" style="width:188px;">שם השדה</span>
                <span class="bsapp-fs-14 bsapp-lh-17" style="width:85px;">אפשרות</span>
                <span class="bsapp-fs-14 bsapp-lh-17" >תצוגה</span>
            </div>
        </div>
        <div id="js_sortable_container_1">
            <?php for ($i = 1; $i < 4; $i++): ?>
                <div class="bsapp-editable-field js-editable-item js-sortable-item">
                    <div class="js-part-view d-flex justify-content-between mb-12 align-items-center " >
                        <div><?php echo $i; ?></div>
                        <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                            <div class="js-grip-handle" style="color:#c6c6c6;"><i class="fas fa-grip-vertical"></i></div>
                            <div  class="js-text-div" style="width:150px;"> שם של פייפליין </div>
                            <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">חובה</div>
                            <div class="">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"  id="customSwitch2">
                                    <label class="custom-control-label" for="customSwitch2"></label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                                <div class="dropdown-menu  text-start">
                                    <a class="dropdown-item text-gray-700  px-8" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-page-1-1"  href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                    <a class="dropdown-item  px-8 text-gray-700" onclick="ClientsSettings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-part-edit h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success "  style="margin-inline-start:25px;width:calc(100% - 25px);border-radius: 6px;">
                        <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן"   />
                        <div class="bsapp-fs-22 d-flex" style="width:70px;">
                            <a class="mie-20 text-gray-700" onclick="ClientsSettings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                            <a class="text-success" onclick="ClientsSettings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <div class="">
        <a class="btn btn-light btn-block " href="javascript:;" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-page-1-1" style="background: #f5f5f5;border-color: #f5f5f5;border-radius: 4px;">  + צור חדש </a>
    </div>
</div>