<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
<div class="my-20 text-end">
    <a class="btn btn-outline-gray-300 text-black" href="javascript:;" onclick="LeadsSettings.openSettings();"><i class="fal fa-cog"></i></a>
    <div class="position-relative bsapp-drop-menu js-drop-menu">
        <div class=" shadow position-absolute  p-16  overflow-hidden bg-white rounded bsapp-z-999  w-100  text-start js-dropdown-inner bsapp-drop-menu-inner d-none">
            <?php require_once 'manage-leads-settings/main.php'; ?>
            <?php require_once 'manage-leads-settings/pipe-line-categories.php'; ?>
            <?php require_once 'manage-leads-settings/lead-source.php'; ?>
            <?php require_once 'manage-leads-settings/pipe-line-category-page.php'; ?>
            <?php require_once 'manage-leads-settings/facebook-connect.php'; ?>
            <?php require_once 'manage-leads-settings/facebook-details-page.php'; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<script>
    $(document).ready(function () {
        LeadsSettings.initSelect2();
        LeadsSettings.initSortable();
    });
</script>