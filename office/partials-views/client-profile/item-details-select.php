<?php
require_once "../../../app/init.php";
require_once "../../Classes/ItemDetails.php";
require_once "../../Classes/Size.php";

if (isset($_GET['itemId']))
    $itemId = $_GET['itemId'];
else
    return;

$itemDetailObj = new ItemDetails();
$itemsBySize = $itemDetailObj->getItemDetailsSorted($itemId);
?>

<?php if (count($itemsBySize) > 1): ?>
<div class="col-md-6">
    <div class="form-group">
        <label>מידה</label>
        <select class="js-select-size form-control" onchange="itemDetailsSelect.showColorSelect()" required="true">
            <?php
                    foreach($itemsBySize as $size => $item):
                        $sizeObj = new Size($size);
            ?>
                        <option value="<?php echo $sizeObj->__get('id') ?>">
                            <?php echo $sizeObj->__get('name') ?>
                        </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<?php
    foreach ($itemsBySize as $size => $item):
?>
        <div class="col-md-6 d-none">
            <label>צבע</label>
            <div class='colorGridSelect form-group' style="width: 70px;">
                <div class='colorCube selectedColor' data-id='0'></div>
                <span class='downArrow'>
                    <i class='fas fa-sort-down'></i>
                </span>
                <input type='hidden' name="itemDetailsId" class='selectedItemDetails'>
                <div class='colorGridContainer'>
                    <div id="select-color-<?php echo $size ?>" class="js-select-color form-control colorGrid" style="padding: 0px; border: none;">
                        <?php
                        foreach ($item as $itemDetails):
                            if ($itemDetails->colors):
                        ?>
                            <div id="<?php echo $itemDetails->__get('colors')->id ?>"
                                 class="colorCube <?php echo $itemDetails->__get('colors')->id == 2 ? 'border' : '' ?>"
                                 style="background-color: <?php echo $itemDetails->__get('colors')->hex ?>"
                                 data-item-details-id="<?php echo $itemDetails->__get('id') ?>"></div>
                            <?php else: ?>
                            <div id="0" class="colorCube" data-item-details-id="<?php echo $itemDetails->__get('id') ?>"></div>
                            <?php endif;
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    endforeach;
?>

<script>
    var itemDetailsSelect = {
        showColorSelect: function (){
            const sizeId = $('.js-select-size').find(':selected').val();
            $('.js-select-color').closest('.col-md-6').addClass('d-none');
            $('#select-color-'+sizeId).closest('.d-none').removeClass('d-none');
            const firstCube = $('#select-color-'+sizeId).find('.colorCube').first();
            itemDetailsSelect.chooseColor(firstCube);

        },
        chooseColor: function (elem){
            if (elem.attr('id') == 0)
                elem.closest('.col-md-6').addClass('d-none');
            $(".selectedColor").data('id', elem.attr('id'));
            $(".colorGridSelect .selectedColor").css("background-color", elem.css("background-color"));
            $(".selectedItemDetails").val(elem.data('itemDetailsId'));
        },
        //If there is no sizes showing color select
        initSizeSelect: function (){
            if (!$('.js-select-size').length){
                $('.js-select-size').attr('required', false);
                $('.js-select-color').closest('.d-none').removeClass('d-none');
                const firstCube = $('.js-select-color').find('.colorCube').first();
                itemDetailsSelect.chooseColor(firstCube);
            }
        }
    }

    $(document).ready(function() {
        itemDetailsSelect.showColorSelect();
        itemDetailsSelect.initSizeSelect();


        $(".colorGrid .colorCube").click(function (evt) {
            evt.stopPropagation();
            let color = $(this);
            itemDetailsSelect.chooseColor(color);
            $(".colorGridContainer").hide();
        });

        $(".colorGridSelect").click(function () {
            $(".colorGridContainer").toggle();
        });
        let mouse_is_inside;
        $(".colorGridSelect").hover(
            function () {
                mouse_is_inside = true;
            },
            function () {
                mouse_is_inside = false;
            }
        );

        $(".js-select-size").select2( {
            theme:"bsapp-dropdown", placeholder: "Select a State",
            minimumResultsForSearch: -1}
        );
    });
</script>