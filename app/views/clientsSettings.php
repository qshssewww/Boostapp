<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<div class="mb-15 text-end">
    <a class="btn btn-outline-gray-300 text-black" href="javascript:;" onclick="ClientsSettings.openSettings();"><i class="fal fa-cog"></i></a>
    <div class="position-relative bsapp-drop-menu js-drop-menu">
        <div class=" shadow position-absolute  p-16  overflow-hidden bg-white rounded bsapp-z-99  w-100  text-start js-dropdown-inner bsapp-drop-menu-inner d-none"   >
            <?php require_once 'client-settings/main.php'; ?>
            <?php require_once 'client-settings/tags-client.php'; ?>
            <?php require_once 'client-settings/reasons-leave.php'; ?>
            <?php require_once 'client-settings/data-from-client.php'; ?>
            <?php require_once 'client-settings/edit-data-from-client.php'; ?>
        </div>
    </div>
</div>
