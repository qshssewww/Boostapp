
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<div>
    <a class="btn btn-outline-gray-300 text-black" href="javascript:;" onclick="Settings.openSettings();"><i class="fal fa-cog"></i></a>
    <div class="position-relative bsapp-drop-menu js-drop-menu">
        <div class=" shadow position-absolute  p-16  overflow-hidden bg-white rounded bsapp-z-99  w-100  text-start js-dropdown-inner bsapp-drop-menu-inner d-none"   >
            <!-- page home :: begin -->
            <div class="d-flex flex-column bg-white    bsapp-page-tab" data-page-id="js-tabs-home" data-depth="0" >
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
                    <span class="" style="font-weight:500;">הגדרות לקוח</span>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="py-8 pt-10 border-bottom border-light">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-address-card"></i> מידע מלקוחות
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1"> נהל  <i class="fal fa-angle-right"></i></a>  
                </div>
                <div class="py-8 pt-10 border-bottom border-light">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-tag"></i> תגיות לקוח
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-2"> נהל  <i class="fal fa-angle-right"></i></a>  
                </div>
                <div class="py-8 pt-10">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-user-minus"></i> סיבות עזיבה/ אי הצטרפות
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3"> נהל  <i class="fal fa-angle-right"></i></a>
                </div>
            </div>
            <!-- page home :: end -->
            <div class="d-none flex-column justify-content-between bg-white h-100    bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1" data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
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
                                                <a class="dropdown-item text-gray-700  px-8" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1-1"  href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                                <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="js-part-edit h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success "  style="margin-inline-start:25px;width:calc(100% - 25px);border-radius: 6px;">
                                    <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן"   />
                                    <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                        <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                        <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>                                                  
                </div>
                <div class="">
                    <a class="btn btn-light btn-block " href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1-1" style="background: #f5f5f5;border-color: #f5f5f5;border-radius: 4px;">  + צור חדש </a>
                </div>
            </div>
            <div class="d-none flex-column justify-content-between bg-white h-100    bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1-1" data-depth="2">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2  pb-17">
                    <a  class="text-black text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
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
                                                <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                                <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="js-part-edit d-flex align-items-center ">
                                    <div class="js-grip-handle mb-12" style="color:#c6c6c6;width:25px;"><i class="fas fa-grip-vertical"></i></div>
                                    <div class=" h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success "  style="width:calc(100% - 25px);border-radius: 6px;">
                                        <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" value="אפשרות ראשונה"  />
                                        <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                            <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                            <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="mt-4 pb-20 mb-20 ">
                        <a class="text-gray-700  text-decoration-none" href="javascript:;" onclick="Settings.addNewItem(this, event);" data-copy-container="#js_sortable_container" data-copy-item="js-design-editable-draggable-item">   + הוסף שלב </a>
                    </div>                                   
                </div>
                <div class="border-top border-light d-flex justify-content-between align-items-center pt-12">
                    <a class="btn d-flex align-items-center justify-content-center btn-light bg-white flex-fill mie-16" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1" style="border-color: #191919;border-radius: 8px;height:48px;">  ביטול</a>
                    <a class="btn d-flex align-items-center justify-content-center  btn-success flex-fill " href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1" style="border-radius: 8px;height:48px;"> שמור</a>
                </div>
            </div>
            <div class="d-none flex-column    bsapp-page-tab" data-page-id="js-tabs-page-2"  data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="mb-16">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold ">
                        <div class="mie-7"><i class="fas fa-tag text-gray-500"></i></div>
                        <span> תגיות לקוח </span>
                    </div>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
                    <div class="mb-10 d-flex text-gray-700">
                        <div style="width:calc(70% - 8px)  ;">שם</div>
                        <div>מס׳ לקוחות</div>
                    </div>
                    <div class="js-fields">
                        <div class="bsapp-editable-field js-editable-item">
                            <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex  align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
                                <div style="width:70%;" class="js-text-div" > שם של פייפליין </div>
                                <div class="bsapp-fs-14 bsapp-lh-17 flex-fill" >56</div>
                                <div class="">

                                </div>
                                <div class="d-flex align-items-center ">                               
                                    <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                        <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                    </a>
                                    <div class="dropdown-menu  text-start">
                                        <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                        <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success"  style="border-radius: 6px;">
                                <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" />
                                <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                    <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                    <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <a class="text-gray-700  text-decoration-none font-weight-bold" href="javascript:;" data-copy-container=".js-fields" data-copy-item="js-design-editable-item" onclick="Settings.addNewItem(this, event);"> + הוסף תגית </a>
                    </div>
                </div>
            </div>

            <div class="d-none flex-column    bsapp-page-tab" data-page-id="js-tabs-page-3"  data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="mb-16">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
                        <div class="mie-7"><i class="fal fa-user-minus text-gray-500"></i></div>
                        <span> סיבות עזיבה/ אי הצטרפות</span>
                    </div>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
                    <div class="mb-10 d-flex text-gray-700">
                        <div style="width:calc(70% - 8px)  ;">שם</div>
                        <div>ID</div>
                    </div>
                    <div class="js-fields">
                        <div class="bsapp-editable-field js-editable-item">
                            <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex  align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
                                <div style="width:70%;" class="js-text-div" > שם של פייפליין </div>
                                <div class="bsapp-fs-14 bsapp-lh-17 flex-fill" >56</div>
                                <div class="">

                                </div>
                                <div class="d-flex align-items-center ">                               
                                    <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                        <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                    </a>
                                    <div class="dropdown-menu  text-start">
                                        <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                        <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success"  style="border-radius: 6px;">
                                <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" />
                                <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                    <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                    <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <a class="text-gray-700  text-decoration-none font-weight-bold" href="javascript:;" data-copy-container=".js-fields" data-copy-item="js-design-editable-item" onclick="Settings.addNewItem(this, event);"> + הוסף תגית </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="js-design-editable-item d-none">
    <div class="bsapp-editable-field js-editable-item">
        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-none  align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
            <div style="width:70%;" class="js-text-div" > שם של פייפליין </div>
            <div class="bsapp-fs-14 bsapp-lh-17 flex-fill" >56</div>
            <div class="">

            </div>
            <div class="d-flex align-items-center ">                               
                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                </a>
                <div class="dropdown-menu  text-start">
                    <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                    <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                </div>
            </div>
        </div>
        <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success"  style="border-radius: 6px;">
            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" />
            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="js-design-editable-draggable-item d-none">
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
                        <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                        <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                    </div> 
                </div>
            </div>
        </div>
        <div class="js-part-edit d-flex align-items-center ">
            <div class="js-grip-handle mb-12" style="color:#c6c6c6;width:25px;"><i class="fas fa-grip-vertical"></i></div>
            <div class=" h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success "  style="width:calc(100% - 25px);border-radius: 6px;">
                <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן" value="אפשרות ראשונה"  />
                <div class="bsapp-fs-22 d-flex" style="width:70px;">
                    <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                    <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        Settings.initSelect2();
        Settings.initSortable();
    });

    var Settings = {
        initSelect2: function () {
            $(".js-select2:not(.select2-hidden-accessible)").select2({
                theme: 'bsapp-dropdown'
            });
        },
        initSortable: function () {
            Sortable.create(js_sortable_container_1, {
                animation: 100,
                group: 'list-1',
                draggable: '.js-sortable-item',
                handle: '.js-grip-handle',
                sort: true,
                filter: '.sortable-disabled',
                chosenClass: 'active'
            });
            Sortable.create(js_sortable_container, {
                animation: 100,
                group: 'list-1',
                draggable: '.js-sortable-item',
                handle: '.js-grip-handle',
                sort: true,
                filter: '.sortable-disabled',
                chosenClass: 'active'
            });
        },
        openSettings: function () {
            $(".js-drop-menu").addClass("bsapp-js-show");
            $(".js-dropdown-inner").removeClass("d-none").addClass("d-block");
        },
        closeSettings: function (elem) {
            var $elem = $(elem);
            var $parent = $elem.parents("[data-page-id]");
            $parent.removeClass("d-flex").addClass("d-none");
            $("[data-page-id='js-tabs-home']").removeClass("d-none").addClass("d-flex");
            $(".js-dropdown-inner").removeClass("d-block").addClass("d-none");
            $(".js-drop-menu").removeClass("bsapp-js-show");
        },
        goTo: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents("[data-page-id]");
            var $target = $('[data-page-id="' + $elem.attr("data-next") + '"]');
            //$parent.removeClass("d-flex").addClass("d-none")
            // $target.removeClass("d-none").addClass("d-flex animated slideInStart animated");

            curr_depth = $parent.data('depth'),
                    target_depth = $target.data('depth');

            if (target_depth < curr_depth) { // backwards
                $target.removeClass('d-none slideInStart animated')
                        .addClass('d-flex');
                $parent.removeClass('slideInStart animated')
                        .addClass('slideOutStart animated');
                setTimeout(function () {
                    +
                            $parent.removeClass('d-flex slideOutStart animated')
                            .addClass('d-none slideInStart animated');
                }, 300)
            } else { // forward
                $target.addClass('slideInStart animated d-flex')
                        .removeClass('d-none');
                setTimeout(function () {
                    $parent.removeClass('d-flex slideInStart animated')
                            .addClass('d-none');
                }, 300);
            }
        },
        addNewItem: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents("[data-page-id]");
            var js_copy = $('.' + $elem.attr("data-copy-item")).html();
            $parent.find($elem.attr("data-copy-container")).append(js_copy);
        },
        saveEdit: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents(".js-editable-item");
            $parent.find(".js-text-div").html($parent.find(".js-input-div").val());
            $parent.find(".js-part-edit").removeClass("d-flex").addClass("d-none");
            $parent.find(".js-part-view").removeClass("d-none").addClass("d-flex");
        },
        cancelEdit: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents(".js-editable-item");
            $parent.find(".js-part-edit").removeClass("d-flex").addClass("d-none");
            $parent.find(".js-part-view").removeClass("d-none").addClass("d-flex");
        },
        showEdit: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents(".js-editable-item");
            $parent.find(".js-input-div").val($parent.find(".js-text-div").html());
            $parent.find(".js-part-edit").removeClass("d-none").addClass("d-flex");
            $parent.find(".js-part-view").removeClass("d-flex").addClass("d-none");
        },
        deleteItem: function (elem, event) {
            var $elem = $(elem);
            var $parent = $elem.parents(".js-editable-item");
            $parent.remove();
        }
    };
</script>
