<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<div class="mb-20">
    <a class="btn btn-outline-gray-300 text-black" href="javascript:;" onclick="Settings.openSettings();"><i class="fal fa-cog"></i></a>
    <div class="position-relative bsapp-drop-menu js-drop-menu">
        <div class=" shadow position-absolute  p-16  overflow-hidden bg-white rounded bsapp-z-999  w-100  text-start js-dropdown-inner bsapp-drop-menu-inner d-none"   >
            <!-- page home :: begin -->
            <div class="d-flex flex-column bg-white  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-home" data-depth="0" >
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
                    <span class="">הגדרות מתעניינים</span>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="py-8 pt-10 ">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-filter"></i> הגדרת תהליכי מכירה
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1"> נהל  <i class="fal fa-angle-right"></i></a>  
                </div>
                <div class="py-8 pt-10">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-sparkles"></i> מקורות לידים
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-2"> נהל  <i class="fal fa-angle-right"></i></a>  
                </div>
                <div class="py-8 pt-10">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fab fa-facebook-square"></i> חיבור פייסבוק
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.ונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3"> נהל  <i class="fal fa-angle-right"></i></a>
                </div>
            </div>
            <!-- page home :: end -->
            <div class="d-none flex-column justify-content-between bg-white h-100  bsapp-settings-panel  bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1" data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2">
                    <a  class="text-black text-decoration-none font-weight-bold"  href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class=""  style="height:calc( 100% - 60px);">
                    <div class="d-flex flex-column mt-20">
                        <h6 class="bsapp-fs-14 bsapp-lh-17 font-weight-bold"><i class="fal fa-filter"></i> הגדרת תהליכי מכירה</h6>
                    </div>
                    <div class="d-flex px-8 mt-16 mb-10">
                        <span class="bsapp-fs-14 bsapp-lh-17" style="width:155px;">שם</span>
                        <span class="bsapp-fs-14 bsapp-lh-17">ID</span>
                    </div>
                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <div class="bsapp-editable-field js-editable-item">
                            <div class="js-part-view d-flex justify-content-between mb-12 align-items-center js-sortable-item" >
                                <div>1</div>
                                <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                                    <div class="js-text-div" style="width:155px;"> שם של פייפליין </div>
                                    <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
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
                                            <a class="dropdown-item text-gray-700  px-8" onclick="Settings.goTo(this, event);"  data-next="js-tabs-page-1-1" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
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
                <div class="">
                    <a class="btn btn-light btn-block " href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1-1" style="background: #f5f5f5;border-color: #f5f5f5;border-radius: 4px;">  + צור חדש </a>
                </div>
            </div>
            <div class="d-none flex-column  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-page-2"  data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="mb-16">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
                        <div class="mie-7"><i class="fas fa-sparkles text-gray-500"></i></div>
                        <span> מקורות לידים </span>
                    </div>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
                    <h6 class="mb-10">שם העמוד</h6>
                    <div class="js-fields">
                        <div class="bsapp-editable-field js-editable-item">
                            <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
                                <div style="width:155px;" class="js-text-div" > שם של פייפליין </div>
                                <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
                                <div class="">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"  id="js-switch-1">
                                        <label class="custom-control-label" for="js-switch-1"></label>
                                    </div>
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
                        <a class="text-gray-700  text-decoration-none" href="javascript:;" data-copy-container=".js-fields" data-copy-item="js-design-editable-item" onclick="Settings.addNewItem(this, event);"> + מקור לידים</a>
                    </div>
                </div>
            </div>
            <div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab"   data-page-id="js-tabs-page-1-1"  data-depth="2">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;"   onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1"><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc(100% - 60px );">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center justify-content-between font-weight-bold">
                        <i class="fal fa-filter"></i><div style="width:calc(100% - 25px);"> הגדרת תהליכי מכירה/ צור חדש </div>
                    </div>
                    <div class="d-flex  mt-16 mb-14 align-items-center justify-content-between">
                        <i class="fal fa-bookmark"></i>
                        <input class="bg-light form-control border border-light bsapp-br-3" value="שם ראשי של פייפליין פה" style="width:calc(100% - 25px);"/>
                    </div>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray">
                        פה יהיה מלל הסבר על כל עיניין הסטטוסים והפייפליין שיבינו במה מדובר פחות או יותר
                    </div>
                    <div class="d-flex justify-content-between  mt-14 mb-10">
                        <div>#</div>
                        <div class="d-flex" style="width:calc(100% - 25px);">
                            <span class="bsapp-fs-14 bsapp-lh-17" style="width:155px;">שם</span>
                            <span class="bsapp-fs-14 bsapp-lh-17">ID</span>
                        </div>
                    </div>
                    <div id="js_sortable_container">
                        <?php for ($i = 0; $i < 2; $i++): ?>
                            <div class="bsapp-editable-field js-editable-item js-sortable-item">
                                <div class="js-part-view d-none justify-content-between mb-12 align-items-center " >
                                    <div>1</div>
                                    <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                                        <div class="js-grip-handle" style="color:#c6c6c6;"><i class="fas fa-grip-vertical"></i></div>
                                        <div  class="js-text-div" style="width:150px;"> שם של פייפליין </div>
                                        <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
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
                                                <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                                <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="js-part-edit h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success "  style="margin-inline-start:25px;width:calc(100% - 25px);border-radius: 6px;">
                                    <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן"   />
                                    <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                        <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                        <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="mt-4 pb-20 mb-20 border-bottom border-light">
                        <a class="text-gray-700  text-decoration-none" href="javascript:;" onclick="Settings.addNewItem(this, event);" data-copy-container="#js_sortable_container" data-copy-item="js-design-editable-draggable-item">   + הוסף שלב </a>
                    </div>
                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <div class="d-flex justify-content-between mb-12 align-items-center" >
                            <div>-</div>
                            <div class="h-40p text-gray-700  d-flex align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                                <div style="width:150px;"> הצלחה </div>
                                <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

            </div>
            <div class="d-none flex-column  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-page-3"  data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;"  onclick="Settings.goTo(this, event);"  data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
                        <div class="mie-7"><i class="fab fa-facebook-square text-gray-500"></i></div>
                        <span> חיבור פייסבוק </span>
                    </div>
                </div>
                <div class="d-flex flex-column justify-content-center h-400p">
                    <h5 class="bsapp-fs-18 bsapp-lh-22  mb-20 mx-auto text-center" style="width:185px;">
                        המשתמש שלך בפייסבוק אינו מחובר
                    </h5>
                    <a class="btn btn-black d-flex align-items-center justify-content-center mx-auto" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3-1" style="height:48px;width : 164px;background: #15202E;border-color: #15202E;border-radius:8px;"> חיבור לפייסבוק </a>
                </div>
            </div>
            <div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-page-3-1"  data-depth="2">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="mb-16">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
                        <div class="mie-7"><i class="fab fa-facebook-square text-gray-500"></i></div>
                        <span > חיבור פייסבוק </span>
                    </div>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc(100% - 60px );">
                    <h6 class="mb-10">שם העמוד</h6>
                    <div class="bsapp-editable-field js-editable-item">
                        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
                            <div  class="js-text-div" style="width:265px;">קרוספיט שוש</div>
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
                                    <a class="dropdown-item text-gray-700  px-8" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3-1-1" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
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
                    <a class="btn btn-light btn-block " href="" style="background: #f5f5f5;border-color: #f5f5f5;border-radius: 4px;color: #FF0015;">  + צור חדש </a>
                </div>
            </div>
            <div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-page-3-1-1"  data-depth="3">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
                    <a  class="text-black  text-decoration-none font-weight-bold" href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3-1" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="mb-16">
                    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
                        <div class="mie-7"><i class="fab fa-facebook-square text-gray-500"></i></div>
                        <span > חיבור פייסבוק </span>
                    </div>
                </div>
                <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc(100% - 60px);">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <span class="bsapp-fs-18 bsapp-lh-22">שם העמוד</span>
                        <div class="">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"  id="js-facebook-checks">
                                <label class="custom-control-label" for="js-facebook-checks"></label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-20 border-light border-bottom">
                        <h6>ניתוב הלידים מפייסבוק</h6>
                        <div>
                            סניף
                        </div>
                        <div class="mb-15">
                            <select class="js-select2">
                                <option>ראשי</option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div class="mie-16 flex-fill">
                                <div>פייפליין</div>
                                <select class="js-select2">
                                    <option>ראשי</option>
                                </select>
                            </div>
                            <div class="flex-fill">
                                <div>שלב בתהליך</div>
                                <select class="js-select2">
                                    <option>ראשי</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom border-bottom mb-20 border-light py-20">
                        <h6>ניתוב לפי הטפסים שלך</h6>
                        <div>
                            סניף
                        </div>
                        <div class="mb-10">
                            <select class="js-select2">
                                <option>שם של טופס</option>
                            </select>
                        </div>
                        <div class="mb-10">
                            <select class="js-select2">
                                <option>שם של טופס</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <a class="btn btn-white flex-fill mie-16 d-flex align-items-center justify-content-center"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3-1"  href="javascript:;" style="height:48px;border-radius: 4px;border-color: #191919;"> ביטול </a>
                    <a class="btn btn-success  flex-fill d-flex align-items-center justify-content-center" onclick="Settings.goTo(this, event);" data-next="js-tabs-page-3-1"  href="javascript:;" style="height:48px;border-radius: 4px;"> שמור </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="js-design-editable-item d-none">
    <div class="bsapp-editable-field js-editable-item">
        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8" style="border-color:#c6c6c6;border-radius: 6px;">
            <div style="width:155px;" class="js-text-div" > שם של פייפליין </div>
            <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
            <div class="">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input"  id="js-switch-1">
                    <label class="custom-control-label" for="js-switch-1"></label>
                </div>
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
    <div class="bsapp-editable-field js-editable-item  js-sortable-item">
        <div class="js-part-view d-none justify-content-between mb-12 align-items-center" >
            <div>1</div>
            <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                <div class="js-grip-handle" style="color:#c6c6c6;"><i class="fas fa-grip-vertical"></i></div>
                <div style="width:150px;"  class="js-text-div"> שם של פייפליין </div>
                <div class="bsapp-fs-14 bsapp-lh-17" style="width:70px;">564984</div>
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
                        <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                        <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                    </div> 
                </div>
            </div>
        </div>
        <div class="js-part-edit h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8 border border-success "  style="margin-inline-start:25px;width:calc(100% - 25px);border-radius: 6px;">
            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן"   />
            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
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
