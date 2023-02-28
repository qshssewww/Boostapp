<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
require_once '../app/views/headernew.php';
$assets_url = 'http://localhost/boost\office\assets\img\get-started';
?>
<link rel="stylesheet" type="text/css" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/dist/css/paper-bootstrap-wizard.css?' . date('YmdHis'); ?>">
<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/js/jquery.bootstrap.wizard.js?' . date('YmdHis'); ?>"></script>

<!-- new meeting modal :: begin -->
<div class="modal px-0 px-sm-auto  text-gray-700  text-start overflow-hidden" tabindex="-1" role="dialog" id="js-getting-started" data-backdrop="static" >
    <div class="modal-dialog modal-lg modal-dialog-centered m-0 m-sm-auto bsapp-max-w-600p" style="height : calc( 100vh - 120px );">
        <div class="modal-content  h-100 rounded ">
            <div class="modal-body d-flex flex-column justify-content-between p-0 h-100">
                <div class="d-flex flex-column justify-content-between p-0 h-100 " data-context='js-started-window-1'>
                    <div class="d-flex  px-15 py-15">
                        <a href="javascript:;" class="text-dark bsapp-fs-18" data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </a>
                        <h4 class="mx-auto mt-20">Your business info</h4>
                    </div> 
                    <div class=""  style="height : calc( 100% - 160px ); ">
                        <div class="bsapp-scroll overflow-auto pt-15 h-100">
                            <div class="px-15 px-sm-75 px-md-100 text-start">
                                <div class="form-group mb-20 bsapp-max-w-400p">
                                    <label>Enter Your Name</label>
                                    <input class="form-control bg-light border-light" type='text' onkeyup='getStarted.showNextInputs(this);' />
                                </div>
                                <div class="form-group mb-20 bsapp-max-w-400p d-none">
                                    <label>Business type</label>
                                    <div class="d-flex flex-wrap">
                                        <?php for ($i = 0; $i < 8; $i++): ?>
                                            <label class="bsapp-checkbox-buttons mie-10 mb-10">
                                                <input class="d-none js-started-business-types" type="checkbox" nam="business[type]"  onclick='getStarted.showNextInputs(this);' id="js-business-type-<?php echo $i; ?>" />
                                                <div class="btn  btn-rounded">
                                                    Business <?php echo $i; ?>
                                                </div>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="form-group mb-30 bsapp-max-w-400p d-none">
                                    <label>Number of Employees</label>
                                    <div class="">
                                        <select class="js-select2">
                                            <?php for ($i = 0; $i < 4; $i++): ?>
                                                <option value="<?php ?>">Option <?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-20 bsapp-max-w-400p d-none">
                                    <a class="btn btn-primary btn-block" href="javascript:;" onclick="getStarted.change(this, 2)">Continue</a>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="d-flex justify-content-end   px-15 px-sm-75 px-md-100 py-15">                   
                        <div class="w-100 bsapp-max-w-200p">
                            <span class="text-secondary mb-8">75% Complete</span>                                
                            <div class="progress w-100" style="height:4px;">
                                <div class="progress-bar bg-gray-800" role="progressbar" style="width:75%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-none flex-column justify-content-between p-0 h-100"  data-context='js-started-window-2'>
                    <div class="d-flex  px-15 py-15">
                        <a href="javascript:;" class="text-dark bsapp-fs-18" data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </a>
                        <h4 class="mx-auto mt-20">Getting Started</h4>
                    </div>
                    <div class=""  style="height : calc( 100% - 130px ); ">
                        <div class="bsapp-scroll overflow-auto pt-15 h-100 d-flex flex-column">
                            <div class="px-15 px-sm-30 px-md-60 text-center">
                                We have provided you with a quick interface
                                that will help you get started working on the system by 
                                completing a number of easy steps, so when you feel ready… let’s get started!
                            </div>
                            <div class="d-flex w-100 justify-content-center  flex-fill">
                                <img src="<?php echo $assets_url ?>/rocket.svg" class="w-50 my-30" />
                            </div>
                        </div> 
                    </div>
                    <div class="d-flex justify-content-center  px-15 pt-15 pb-75">                   
                        <a href="javascript:;" class="btn btn-light px-30" onclick="getStarted.change(this, 3)">Let's Go</a>
                    </div>
                </div>
                <div class="d-none flex-column justify-content-between p-0 h-100" data-context='js-started-window-3'>
                    <div class="d-flex  px-15 py-15">
                        <a href="javascript:;" class="text-dark bsapp-fs-18" data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </a>
                        <h4 class="mx-auto mt-20">Getting Started</h4>
                    </div>
                    <div class=""  style="height : calc( 100% - 175px ); ">
                        <div class="pt-15 h-100">
                            <div class="px-15 px-sm-30 px-md-60 h-100 bsapp-wizard bsapp-get-started">
                                <div class="wizard-card border-0    h-100" data-color="red" id="wizard">
                                    <form class="h-100" action="" method="">
                                        <div class="h-100">
                                            <div class="wizard-navigation position-relative w-300p mx-auto  bsapp-z-99">                                                
                                                <ul>
                                                    <li>
                                                        <a href="#js-step-1" data-toggle="tab">
                                                            <div class="icon-circle">
                                                            </div>
                                                            1
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#js-step-2" data-toggle="tab">
                                                            <div class="icon-circle">
                                                            </div>
                                                            2
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#js-step-3" data-toggle="tab">
                                                            <div class="icon-circle">
                                                            </div>
                                                            3
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#js-step-4" data-toggle="tab">
                                                            <div class="icon-circle">

                                                            </div>
                                                            4
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>                                            
                                            <div class="tab-content p-0 pt-75  h-100 " >
                                                <div class="tab-pane bsapp-scroll bsapp-overflow-y-auto " id="js-step-1" style="height : calc( 100% - 15px );">
                                                    <div class="text-center">
                                                        <h5 class="mb-15">Schedule your classes</h5>
                                                        <div class="px-15 px-sm-30 px-md-60 text-center ">
                                                            In the first step you need to set up your classes in the calendar, click on the button and create a new class. You can set an appointments scheduling in the calendar setting area as well.
                                                        </div>
                                                        <div class="row my-30 d-flex align-items-center">
                                                            <div class="col-md-6 order-md-2">
                                                                <img src="<?php echo $assets_url ?>/calendar.svg" class="w-100 my-30" />
                                                            </div>
                                                            <div class="col-md-6  order-md-1">
                                                                <a class="btn btn-primary btn-block mb-15" href="javascript:;">Start Creating</a>
                                                                <a class="btn btn-light btn-block mb-15" href="javascript:;">Watch the video</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane bsapp-scroll bsapp-overflow-y-auto " id="js-step-2" style="height : calc( 100% - 15px );">
                                                    <div class="text-center">
                                                        <h5 class="mb-15">Setting up a membership/ sission punch card</h5>
                                                        <div class="px-15 px-sm-30 px-md-60 text-center ">
                                                            Allow your customers schedule classes by creating a sission punch card or a membership that can be assigned to customers. you can also allow them to purchase independently in the app or with external link.                                                        </div>
                                                        <div class="row my-30 d-flex align-items-center">
                                                            <div class="col-md-6 order-md-2">
                                                                <img src="<?php echo $assets_url ?>/account.svg" class="w-100 my-30" />
                                                            </div>
                                                            <div class="col-md-6  order-md-1">
                                                                <a class="btn btn-primary btn-block mb-15" href="javascript:;">Start Creating</a>
                                                                <a class="btn btn-light btn-block mb-15" href="javascript:;">Watch the video</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane bsapp-scroll bsapp-overflow-y-auto " id="js-step-3" style="height : calc( 100% - 15px );">
                                                    <div class="">
                                                        <h5 class="text-center mb-15">Add your team/ stuff</h5>
                                                        <div class="px-15 px-sm-30 px-md-60 text-center ">
                                                            Send your team an invitation and start collaborating on the day-to-day tasks, customer care, classes and sales.        

                                                        </div>
                                                        <div class="row my-40">    
                                                            <div class="col-md-5 order-md-2">
                                                                <img src="<?php echo $assets_url ?>/team.svg" class="w-100 my-30" />
                                                            </div>
                                                            <div class="col-md-7 d-flex flex-column order-md-1 js-invite-box">
                                                                <h6 class="mb-10">Send Invitation</h6>
                                                                <div class="js-invite-elements">
                                                                    <div class="js-invite-add d-flex justify-content-start align-items-center mb-15">
                                                                        <div class="mie-10 w-20p">
                                                                            <i class="fal fa-user-circle"></i>
                                                                        </div>
                                                                        <div class="mie-10 w-150p">
                                                                            <input type="text" class="form-control"/>
                                                                        </div>
                                                                        <div class="w-150p mie-10">
                                                                            <select class="js-select2 w-100">
                                                                                <option value="">Option 1</option>
                                                                                <option>Option 2</option>
                                                                                <option>Option 3</option>
                                                                            </select>
                                                                        </div>
                                                                        <div >
                                                                            <a class="text-danger" href="javascript:;" onclick="getStarted.remove(this);"><i class="fas fa-minus-circle"></i></a>                                                              
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <a class="btn btn-light mb-15" href="javascript:;" onclick="getStarted.addInvite();"><i class="fal fa-plus"></i></a>
                                                                    <a class="btn btn-primary btn-block mb-15" href="javascript:;">Send</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane bsapp-scroll bsapp-overflow-y-auto " id="js-step-4" style="height : calc( 100% - 15px );">
                                                    <div class="text-center">
                                                        <h5 class="mb-20">Let the world know</h5>
                                                        <div class="px-15 px-sm-30 px-md-60 text-center">
                                                            Send your customers an invitation to join your club by filling out an online form, if you already have your customers’ data you can upload them to our system easily. Once a customer is logged into the system he will be able to use the app and book classes.
                                                            <div class="row my-30 d-flex align-items-center">
                                                                <div class="col-md-6 order-md-2">
                                                                    <img src="<?php echo $assets_url ?>/mail.svg" class="w-100 my-30" />
                                                                </div>
                                                                <div class="col-md-6  order-md-1">
                                                                    <a class="btn btn-primary btn-block mb-15" href="javascript:;">Copy joining form link</a>
                                                                    <a class="btn btn-info btn-block mb-15" href="javascript:;">Upload data (CSV file)</a>
                                                                    <a class="btn btn-light btn-block mb-15" href="javascript:;">Watch the video</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                                
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="d-flex justify-content-between  px-15 py-15 bsapp-z-99 bg-white">                   
                        <a href="javascript:;" class="btn btn-outline-gray-400 px-40 js-started-back" onclick="getStarted.$bsWizard.bootstrapWizard('previous');"> <i class="fal fa-angle-left"></i> Back</a>
                        <a href="javascript:;" class="btn btn-outline-gray-400 px-40 js-started-next" onclick="getStarted.$bsWizard.bootstrapWizard('next');">Skip  <i class="fal fa-angle-right"></i></a>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>
<!-- new meeting modal :: end -->
<div class="d-none js-invite-add-html">
    <div class="js-invite-add d-flex justify-content-start align-items-center mb-15">
        <div class="mie-10 w-20p">
            <i class="fal fa-user-circle"></i>
        </div>
        <div class="mie-10 w-150p">
            <input type="text" class="form-control"/>
        </div>
        <div class="w-150p mie-10">
            <select class="w-100">
                <option value="">Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
            </select>
        </div>
        <div>
            <a class="text-danger" href="javascript:;" onclick="getStarted.remove(this);"><i class="fas fa-minus-circle"></i></a>
        </div>
    </div>
</div>
<?php
require_once '../app/views/footernew.php';
?>
<script>
    var getStarted = {
        $bsWizard: null,
        init: function () {
            $("#js-getting-started").modal("show");
            $(".js-select2").select2({
                theme: "bsapp-dropdown",
                minimumResultsForSearch: -1,
                dropdownParent: $("#js-getting-started")

            });
            //step 1 show wizard 
            this.$bsWizard = $('.wizard-card').bootstrapWizard({
                'tabClass': 'nav nav-pills',
                'nextSelector': '.js-started-next',
                'previousSelector': '.js-started-back',

                onNext: function (tab, navigation, index) {

                },
                onPrevious: function (tab, navigation, index) {

                },
                onInit: function (tab, navigation, index) {

                    //check number of tabs and fill the entire row
                    var $total = navigation.find('li').length;
                    $width = 100 / $total;

                    navigation.find('li').css('width', $width + '%');

                },

                onTabClick: function (tab, navigation, index) {



                },

                onTabShow: function (tab, navigation, index) {
                    var $total = navigation.find('li').length;
                    var $current = index + 1;

                    var $wizard = navigation.closest('.wizard-card');

                    //update progress
                    var move_distance = 100 / $total;
                    move_distance = move_distance * (index) + move_distance / 2;

                    $wizard.find($('.progress-bar')).css({width: move_distance + '%'});
                    //e.relatedTarget // previous tab

                    $('.wizard-card .nav-pills li a.active').parents("li").prevAll().addClass("bsapp-step-done");
                    $('.wizard-card .nav-pills li a.active').parents("li").removeClass("bsapp-step-done").nextAll().removeClass("bsapp-step-done");
                }
            });
        },
        addInvite: function () {
            var $addHTML = jQuery(".js-invite-add-html");
            jQuery(".js-invite-box .js-invite-elements").after($addHTML.html());
            jQuery(".js-invite-box").find("select:not(.select2-hidden-accessible)").select2({theme: "bsapp-dropdown", dropdownParent: $("#js-getting-started"), minimumResultsForSearch: -1})
        },
        remove: function (elem) {
            $(elem).parents(".js-invite-add").fadeOut().remove();
        },
        change: function (elem, go_to) {
            var $parent = $(elem).parents('[data-context]').removeClass("d-flex").addClass("d-none");
            $('[data-context="js-started-window-' + go_to + '"]').removeClass("d-none").addClass("d-flex");
        },
        showNextInputs: function (elem) {
            var $elem = $(elem);
            var type = $elem.attr("type");
            if (type == "text") {
                if ($elem.val().trim() != "") {
                    $elem.parents(".form-group").next().removeClass("d-none");
                } else {
                    $elem.parents(".form-group").next().addClass("d-none");
                }
            }
            if (type == "checkbox") {
                if ($(".js-started-business-types:checked").length > 0) {
                    $elem.parents(".form-group").nextAll().removeClass("d-none");
                } else {
                    $elem.parents(".form-group").nextAll().addClass("d-none");
                }
            }
        }
    };
    $(document).ready(function () {
        getStarted.init();
    });
</script>

