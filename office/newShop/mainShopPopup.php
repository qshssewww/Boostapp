<?php
require_once dirname(__DIR__)."/Classes/ItemColor.php";
require_once dirname(__DIR__)."/Classes/Size.php";
require_once dirname(__DIR__)."/Classes/ItemSupplier.php";

$colorsObj = new ItemColor();
$colors = $colorsObj->getDefaultColors();

$sizesObj = new Size();
$sizes = $sizesObj->getDefaultSizes();
?>
<div class="popupWrapper" id="mainShopPopup">
    <div class="popupContainer smPopup scaleUp bsapp-max-w-700p">
        <!-- modal header start -->
        <div class="generalPopupHeader mt-3 mainShopHeaderFlex mx-0 pb-7">
            <div class="mainPopupHeaderFlex">
                <h5 class="generalPopupTitle mie-10 align-self-center" id="generalPopupTitleMaim">הקמת</h5>
                <div class="w-150p mie-20">
                <select name="select-type-secondary" id="select-type-secondary" class="selectPopUp" >
                    <option value="2"><?php echo lang('product') ?></option>
                    <option value="3"><?php echo lang('payment_pages') ?></option>
                </select>
                </div>
            </div>
            <a  href="javascript:;"  class="newCalendarPopupCloseTimes toggleClosePopup text-dark" data-target="mainShopPopup" style="font-size:1.5rem;">
                <i class="fal fa-times"></i>
            </a>
        </div>

        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container bsapp-card-scroll overflow-y-auto">
            <div class="popupSectionsContainer">
                <div class="hiddenPopupSection" style="display:none" data-id="1">
                    <?php
                    $type = 1;
                    include "mainPopupSections/membership.php"
                    ?>
                </div>
                <div class="hiddenPopupSection" style="display:none" data-id="2">
                    <?php
                    $type = 2;
                    include "mainPopupSections/product.php"
                    ?>
                </div>
                <div class="hiddenPopupSection" style="display:none" data-id="3">
<?php
$type = 3;
include "mainPopupSections/smartLink.php"
?>
                </div>
                <div class="hiddenPopupSection" style="display:none" data-id="4">
                    <?php
                    $type = 4;
                    include "mainPopupSections/membership.php"
                    ?>
                </div>
                <div class="hiddenPopupSection" style="display:none" data-id="5">
<?php $type = 5;
include "mainPopupSections/membership.php"
?>
                </div>
            </div>
        </div>
        <!-- modal content end -->
        <div class="generalPopupFooter">
            <button id="mainShopPopupButtonCancel" type="button" class="btn btn-light calendarPopupButton mt-10 generalPopupButtonCancel toggleClosePopup" data-target="mainShopPopup"><?php echo lang('action_cacnel') ?></button>
            <button id="mainShopPopupButtonSave" type="button" class="btn btn-primary calendarPopupButton mt-10 generalShopPopupButtonSave"><?php echo lang('save') ?></button>
        </div>
    </div>

</div>
<div class="popupWrapper" id="InventoryPopup">
    <div class="popupContainer scaleUp w-600p">
        <div class="generalPopupHeader mt-3 mb-5">
            <h4 class="generalPopupTitle">רשימת מלאים</h4>
            <button type="button" class="close newCalendarPopupCloseTimes toggleClosePopup" data-target="InventoryPopup" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
       <table id="InventoryTable">
           <thead>
           <tr>
               <th>כמות</th>
               <th>מידה</th>
               <th>צבע</th>
               <th>הסרה</th>
           </tr>
           </thead>
           <tbody>

           </tbody>
       </table>
       <div class="text-danger text-start px-15 js-inventoryTable-error d-none "></div>
        <div class="AddInventoryTable text-start font-weight-bold py-20 px-16 cursor-pointer">+ מלאי</div>
        <div class="generalPopupFooter">
            <button id="inventoryPopupButtonCancel" type="button" class="btn btn-light calendarPopupButton generalPopupButtonCancel toggleClosePopup" data-target="InventoryPopup">בטל</button>
            <button id="inventoryPopupButtonSave" type="button" class="btn calendarPopupButton generalShopPopupButtonSave">עדכן</button>
        </div>
    </div>
</div>

<script>
    var sizeArr = [];
    var colorArr = [];
    <?php
    foreach ($colors as $color){?>
    colorArr.push({id: <?php echo $color["id"]?>, hex: '<?php echo $color["hex"]?>'});
    <?php }
    foreach ($sizes as $size){?>
    sizeArr.push({id: <?php echo $size["id"]?>, name: '<?php echo $size["name"]?>'});
    <?php } ?>


    var DelInventoryTable =[];
</script>
