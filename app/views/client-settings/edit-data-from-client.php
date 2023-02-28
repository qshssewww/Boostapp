<div class="d-none flex-column justify-content-between bg-white h-100    bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1-1" data-depth="2">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2  pb-17">
        <a  class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-page-1" ><i class="fal fa-angle-left mie-7 bsapp-fs-24"></i>חזור</a>
        <a href="javascript:;" onclick="ClientsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class=""  style="height:calc( 100% - 60px);">
        <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center justify-content-between font-weight-bold">
            <i class="fal fa-filter"></i><div style="width:calc(100% - 25px);"> הגדרת תהליכי מכירה/ צור חדש </div>
        </div>
        <div class="d-flex  mt-16 mb-14 align-items-center justify-content-between">
            <i class="fal fa-bookmark"></i>
            <input class="bg-light form-control border border-light bsapp-br-3" value="שם השדה" style="width:calc(100% - 25px);"/>
        </div>
        <div class="d-flex  mt-16 mb-14 align-items-center justify-content-between">
            <i class="fal fa-info-circle"></i>
            <div style="width:calc(100% - 25px);">
                <select class="js-select2">
                    <option id="id">בחירה יחידה מרשימת אפשרויות</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between  mt-14 mb-10">
            <div>אפשרויות בחירה</div>
        </div>
        <div id="js_sortable_container">
            <?php for ($i = 0; $i < 2; $i++): ?>
                <div class="bsapp-editable-field js-editable-item js-sortable-item">
                    <div class="js-part-view d-none justify-content-between mb-12 align-items-center " >
                        <div class="js-grip-handle" style="color:#c6c6c6;"><i class="fas fa-grip-vertical"></i></div>
                        <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                            <div  class="js-text-div" > אפשרות ראשונה </div>
                            <div class="bsapp-fs-14 bsapp-lh-17" ></div>
                            <div class="d-flex align-items-center">
                                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                                <div class="dropdown-menu  text-start">
                                    <a class="dropdown-item text-gray-700  px-8" onclick="ClientsSettings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                    <a class="dropdown-item  px-8 text-gray-700" onclick="ClientsSettings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-part-edit d-flex align-items-center ">
                        <div class="js-grip-handle mb-12" style="color:#c6c6c6;width:25px;"><i class="fas fa-grip-vertical"></i></div>
                        <div class=" h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success "  style="width:calc(100% - 25px);border-radius: 6px;">
                            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" value="אפשרות ראשונה"  />
                            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                <a class="mie-20 text-gray-700" onclick="ClientsSettings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                <a class="text-success" onclick="ClientsSettings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="mt-4 pb-20 mb-20 ">
            <a class="text-gray-700  text-decoration-none" href="javascript:;" onclick="ClientsSettings.addNewItem(this, event);" data-copy-container="#js_sortable_container" data-copy-item="js-design-editable-draggable-item">   + הוסף שלב </a>
        </div>
    </div>
    <div class="border-top border-light d-flex justify-content-between align-items-center pt-12">
        <a class="btn d-flex align-items-center justify-content-center btn-light bg-white flex-fill mie-16" href="javascript:;" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-page-1" style="border-color: #191919;border-radius: 8px;height:48px;">  ביטול</a>
        <a class="btn d-flex align-items-center justify-content-center  btn-success flex-fill " href="javascript:;" onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-page-1" style="border-radius: 8px;height:48px;"> שמור</a>
    </div>
</div>
