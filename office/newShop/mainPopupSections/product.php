<?php
require_once dirname(dirname(__DIR__)) . "/Classes/ItemColor.php";
require_once dirname(dirname(__DIR__)) . "/Classes/Size.php";
require_once dirname(dirname(__DIR__)) . "/Classes/ItemSupplier.php";
$colorsObj = new ItemColor();
$colors = $colorsObj->getDefaultColors();

$sizesObj = new Size();
$sizes = $sizesObj->getDefaultSizes();

$categories = new ItemCategory();
$CompanyNum = Auth::user()->CompanyNum;
$itemCats = $categories->getCompanyItemsCategories($CompanyNum);
$suppObj = new ItemSupplier();
$Suppliers = $suppObj->getCompanySuppliers($CompanyNum);
?>

<div class="row mt-20 ">

    <div class="col-md-6 mb-10">
        <input type="hidden" value="" id="hiddenIdInput<?php echo $type ?>">
        <label for="productName"><?php echo lang('product_name') ?></label>
        <input class="form-control bg-light border-light" placeholder="<?php echo lang('product_name') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('product_name') ?>'" name="productName" id="productName" style="width: 100%" />
    </div>
    <div class="col-md-6 mb-10 js-category-containers">
        <input type="hidden" id="isProductCategoryNew" value="0">
        <label for="productCategory"><?php echo lang('category_single') ?></label>
        <div class="icon-container bsapp-z-1">
            <span class="newLabel"><?php echo lang('new') ?></span>
            <!-- <i class="fas fa-bolt"></i> -->

        </div>
        <select name="productCategory" id="productCategory" class="form-control js-select2-shop" style="width:100%">
            <?php foreach ($itemCats as $cat) : ?>
                <option value="<?php echo $cat->__get('id'); ?>"><?php echo $cat->__get('Name'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if (Count($company->getBrands()) > 1) { ?>
        <div class="col-md-12 mb-10">
            <label class="shopLabel" for="productBranch"><?php echo lang('branch') ?></label>
            <select id="productBranch" class="form-control js-select2-shop">
                <option value="-1"><?php echo lang('all_branch') ?></option>
                <?php foreach ($company->getBrands() as $brand) { ?>
                    <option value="<?php echo $brand->__get("id") ?>"> <?php echo $brand->__get("BrandName") ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-10">
        </div>
    <?php } ?>
    <div class="col-md-12 d-flex align-items-center justidy-content-between mb-10" id="Price">
        <div class="col-md-6 px-0">
            <label for="productPrice" class="mb-6"><?php echo lang('price') ?></label>
            <!-- <input placeholder="מחיר" class="form-control bg-light border-light" onfocus="this.placeholder = ''" onblur="this.placeholder = 'מחיר'" type="number" name="productPrice" id="productPrice" style="width: 100%" /> -->
            <div class="d-flex align-items-center">
                <input placeholder="<?php echo lang('price') ?>" class="form-control bg-light border-light w-85p mie-7" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('price') ?>'" type="number" name="productPrice" id="productPrice" style="width: 100%" />
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="taxInclude2" name="taxInclude" checked="checked">
                    <label class="custom-control-label" for="taxInclude2"><?php echo lang('include_vat') ?></label>
                </div>
            </div>
        </div>
        <div class="col-md-6 pl-0">
            <div class="shouldBeHidden" style="display:none;">
                <!-- <div class="icon-container bsapp-left-5p">
                    <i class="fal fa-usd-circle"></i>
                </div> -->
                <label for="costPrice"><?php echo lang('cost_price_product') ?></label>
                <input class="form-control cute-input" placeholder="<?php echo lang('cost_price_product') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('cost_price_product') ?>'" type="number" name="costPrice" id="costPrice" />
            </div>
        </div>

    </div>
    <div id="InventoryList" class="col-md-12 mb-10">
    </div>
    <div class="text-danger px-15 js-inventory-error d-none"></div>
<!--    --><?php //if(Auth::user()->role_id == 1) { ?>
    <div class="col-md-6 mb-10">
        <div>
            <div class="AddInventory text-black"><?php echo lang('inventory_product') ?></div>
        </div>
    </div>
<!--    --><?php //} ?>
    <div class="col-md-6 mb-10 extraOption" >

    </div>
    <div class="col-md-6 mb-10 extraOption" style="display:none;">
        <label for="makat"><?php echo lang('sku_product') ?></label>
        <input class="form-control bg-light border-light" placeholder='<?php echo lang('sku_product') ?>' onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('sku_product') ?>'" name="makat" id="makat" style="width: 100%" />
    </div>

    <!-- <div class="col-md-6 mb-10 extraOption" style="display:none;">

    </div> -->
    <div class="col-md-6 mb-10 extraOption js-supplier-category" style="display:none;">

        <input type="hidden" id="isProductSupplierNew" value="0">
        <label for="productSupplier"><?php echo lang('suppliers_product') ?></label>
        <div class="icon-container bsapp-z-1">
            <span class="newLabel Supplier"><?php echo lang('new') ?></span>
            <!-- <i class="fas fa-bolt"></i> -->

        </div>
        <select class="form-control js-select2-shop" name="productSuppliers" id="productSuppliers" style="width:100%">
            <?php foreach ($Suppliers as $Supplier) : ?>
                <option value="<?php echo $Supplier['id']; ?>"><?php echo $Supplier['name']; ?></option>
            <?php endforeach; ?>
        </select>

    </div>
    <!-- <div class="col-md-6 mb-10 extraOption" style="display:none;">

    </div> -->
    <div class="col-md-6 mb-10 extraOption" style="display:none;">
        <label for="barcode"><?php echo lang('bardcode_product') ?></label>
        <input class="form-control border-light bg-light" placeholder="<?php echo lang('bardcode_product') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('bardcode_product') ?>'" name="barcode" id="barcode" style="width: 100%" />
    </div>
    <!-- <div class="col-md-6 mb-10 extraOption" style="display:none;">


    </div> -->

</div>
<div class="more-options mt-4">
    <div class="more-options-btn" id="openMoreOptions">
    <?php echo lang('view_more_product') ?>
    </div>
</div>

<div style="margin:0.5rem 0rem;width:100%;display:none;" class="shouldBeHidden">
    <div class="mt-4 d-flex justify-content-start align-items-center">
        <div class="rowIconContainer" style="display:inline-block;">
            <i class="fal fa-mobile"></i>
        </div>
        <select class="cute-input form-control js-select2-shop" name="productInApp" id="productInApp" style="width: fit-content;display:inline-block;border:none !important;padding:0 !important;">
            <option value="1"><?php echo lang('app_disable_purschase') ?></option>
                <option value="2"><?php echo lang('app_enable_purschase') ?></option>
        </select>
    </div>
</div>
<div class=" inAppOptions" style="display:none;width:100%;"> 
    <div class="rowInput" style="margin:0.5rem 0rem;">
        <div class="edit-avatar classImg" id="imgPlus<?php echo $type ?>" data-ip-modal="#itemModal" title="<?php echo lang('edit_image') ?>" style="display: flex">
            <div class="rowIconContainer">
                <i class="far fa-image"></i>
            </div>
            <div class="plus ImgEmpty">
                +
                <?php echo lang('add_image_membership') ?>
            </div>
            <div class="hidden hiddenImg d-flex align-items-center">
                <div class="ImgName" id="ImgName<?php echo $type ?>">
                </div>
            </div>
        </div>
        <!-- <div class="stop removeImg" id="removeImg" style="display: none"></div> -->
        <div class="text-danger mis-9 removeImg" id="removeImg" style="display: none"><i class="fas fa-do-not-enter"></i></div>
        <input type="hidden" id="pageImgPath<?php echo $type ?>" name="pageImgPath" value="" />
    </div>
    <div class="rowInput " style="margin:0.5rem 0rem;" id="openProductTextarea">
        <div class="rowIconContainer">
            <i class="far fa-comment-alt"></i>
        </div>
        <div class="plus"><?php echo lang('add_description_membership') ?></div>
        <div style="width:100%;" class="hidden hiddenTextarea-product d-flex align-items-center">
            <textarea id="productContent"></textarea>
            <!-- <div class="stop mr-2" id="closeProductTextarea"></div> -->
            <div class="text-danger mis-9" id="closeProductTextarea"><i class="fas fa-do-not-enter"></i></div>
        </div>
    </div>
    <div id="purchaseLimitPopupProduct2" data-type="2" class="fitContent rowInput openPurchaseLimitPopup" style="margin:0.5rem 0rem;">
        <div class="rowIconContainer">
            <i class="far fa-eye-slash"></i>
        </div>
        <div><?php echo lang('purchase_limit_membership') ?></div>
    </div>

    <div id="purchaseLimitPopupProductHidden2" style="display:none;margin:0.5rem 0rem;" class="rowInput">
        <div class="rowIconContainer">
            <i class="far fa-eye-slash"></i>
        </div>
        <input type="hidden" class="hiddenPurchaseInput" id="purchaseLimitPopupProductHiddenInput2" />
        <div class="popupLineText" id="purchaseLimitPopupProductHiddenText2"></div>
        <div class="mr-2 editPurchaseLine"><i class="fas fa-pencil-alt"></i></div>
        <!-- <div class="stop mr-2" id="purchaseLimitPopupProductHiddenClose"></div> -->
        <div class="text-danger mis-9" id="purchaseLimitPopupProductHiddenClose"><i class="fas fa-do-not-enter"></i></div>
    </div>
</div>
