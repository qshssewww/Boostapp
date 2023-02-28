
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<div class="mb-20">
    <a class="btn btn-outline-gray-300 text-black" href="javascript:;" onclick="Settings.openSettings();"><i class="fal fa-cog"></i></a>
    <div class="position-relative bsapp-drop-menu js-drop-menu">
        <div class=" shadow position-absolute  p-16  overflow-hidden bg-white rounded bsapp-z-999  w-100  text-start js-dropdown-inner bsapp-drop-menu-inner d-none"   >
            <!-- page home :: begin -->
            <div class="d-flex flex-column bg-white  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-home" data-depth="0" >
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
                    <span class="">הגדרות משימות</span>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="py-8 pt-10 ">
                    <h6 class="bsapp-fs-18 bsapp-lh-22 mb-6">
                        <i class="fal fa-tasks-alt"></i> סוגי משימות
                    </h6>
                    <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">
                        לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.
                    </div>
                    <a class="text-primary  text-decoration-none"   href="javascript:;"  onclick="Settings.goTo(this, event);" data-next="js-tabs-page-1"> נהל  <i class="fal fa-angle-right"></i></a>  
                </div>              
            </div>
            <!-- page home :: end -->
            <div class="d-none flex-column bg-white h-100  bsapp-settings-panel  bsapp-page-tab animated slideInStart" data-page-id="js-tabs-page-1" data-depth="1">
                <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2">
                    <a  class="text-black text-decoration-none font-weight-bold"  href="javascript:;" onclick="Settings.goTo(this, event);" data-next="js-tabs-home" ><i class="fal fa-angle-left"></i>    חזור</a>
                    <a href="javascript:;" onclick="Settings.closeSettings(this)" class="text-black  text-decoration-none">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class=""  style="height:calc( 100% - 60px);">
                    <div class="d-flex flex-column mt-20">
                        <h6 class="bsapp-fs-14 bsapp-lh-17 font-weight-bold"><i class="fal fa-tasks-alt"></i> סוגי משימות</h6>
                    </div>
                    <div class="d-flex px-8 mt-16 mb-10">
                        <span class="bsapp-fs-14 bsapp-lh-17">שם</span>
                    </div>
                    <div class="mb-16 js-fields">
                        <?php for ($i = 0; $i < 2; $i++): ?>
                            <div class="bsapp-editable-field js-editable-item">
                                <div class="js-part-view d-flex justify-content-between mb-12 align-items-center js-sortable-item" >
                                    <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8 w-100" style="border-color:#c6c6c6;border-radius: 6px;">
                                        <div class="js-text-div" > שם של פייפליין </div>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                                <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                            </a>
                                            <div class="dropdown-menu  text-start">
                                                <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);"  data-next="js-tabs-page-1-1" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                                                <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="js-part-edit h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success w-100"  style="border-radius: 6px;">
                                    <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"   placeholder="הקלד שם כאן"   />
                                    <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                        <a class="mie-20 text-gray-700" onclick="Settings.cancelEdit(this, event);"><i class="fal fa-minus-circle"></i></a>
                                        <a class="text-success" onclick="Settings.saveEdit(this, event);"><i class="fal fa-check-circle"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div>
                        <a href="javascript:;" class="text-gray text-decoration-none font-weight-bold" onclick="Settings.addNewItem(this, event);"  data-copy-item="js-design-editable-item" data-copy-container=".js-fields">+ הוסף סוג משימה</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="js-design-editable-item d-none">
    <div class="bsapp-editable-field js-editable-item">
        <div class="js-part-view d-flex justify-content-between mb-12 align-items-center js-sortable-item" >
            <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8 w-100" style="border-color:#c6c6c6;border-radius: 6px;">
                <div class="js-text-div" > שם של פייפליין </div>
                <div class="d-flex align-items-center">
                    <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                        <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                    </a>
                    <div class="dropdown-menu  text-start">
                        <a class="dropdown-item text-gray-700  px-8" onclick="Settings.showEdit(this, event);"  data-next="js-tabs-page-1-1" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                        <a class="dropdown-item  px-8 text-gray-700" onclick="Settings.deleteItem(this, event);" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-trash-alt"></i></span> <span> Delete</span></a>
                    </div> 
                </div>
            </div>
        </div>
        <div class="js-part-edit h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success w-100"  style="border-radius: 6px;">
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
        //Settings.initSortable();
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
