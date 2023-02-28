<!-- Store Settings Module :: Begin -->
<div id="storeSettings" class="bsapp-settings-dialog position-relative dropdown d-flex">

    <a class="btn btn-outline-gray-300 text-black dropdown-toggle" href="javascript:;"><i class="fal fa-cog"></i></a>

    <!-- Store Settings Module :: Dropdown Content Begin -->
    <div class="dropdown-menu position-absolute w-100 border-0 m-0 rounded-lg shadow overflow-hidden p-0 animated fadeIn bsapp-z-0 ">

        <button type="button"
                class="dropdown-toggle btn position-absolute shadow-none p-0 bsapp-fs-24 bsapp-lh-24 bsapp-z-9">
            <i class="fal fa-times"></i>
        </button>

        <?php require_once 'store-settings/main.php'; ?>
        <?php require_once 'store-settings/manage-settings.php'; ?>
        <?php require_once 'store-settings/manage-items.php'; ?>
        <?php require_once 'store-settings/remove-item.php'; ?>
        <?php require_once 'store-settings/coupons.php'; ?>
        <?php require_once 'store-settings/coupons-new.php'; ?>
        <?php require_once 'store-settings/order-products.php'; ?>
        <?php require_once 'store-settings/fixed-payments.php'; ?>
        <?php require_once 'store-settings/fixed-payments-new.php'; ?>
        <?php require_once 'store-settings/payment-and-billing.php'; ?>
        <?php require_once 'store-settings/spread-payments.php'; ?>
        <?php require_once 'store-settings/direct-debit.php'; ?>

    </div> <!-- Dropdown Menu End -->

</div>

<script type="text/javascript">
    var $companyNo;
    $(document).ready(function () {
        $companyNo = <?php echo $CompanyNum ?>
    });
</script>