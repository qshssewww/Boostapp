<!-- Store Settings Module :: Begin -->
<div id="calendarSettings" class="bsapp-settings-dialog position-relative dropdown d-flex">

    <button  id="calendarViewSettings" type="button" class="dropdown-toggle btn shadow-none p-0 position-absolute  d-none ">
        <i class="fal fa-cog fa-fw"></i>
    </button>

    <!-- Calendar Settings Module :: Dropdown Content Begin -->
    <div class="dropdown-menu position-absolute w-100 border-0 m-0 rounded-lg shadow overflow-hidden p-0 animated fadeIn bsapp-z-0">

        <button type="button" class="dropdown-toggle btn position-absolute shadow-none p-0 bsapp-fs-24 bsapp-lh-24 bsapp-z-9 js-close-calendar-settings">
            <i class="fal fa-times"></i>
        </button>

        <?php require_once 'calendar-settings/main.php'; ?>
        <?php require_once 'calendar-settings/general-settings.php'; ?>
        <?php require_once 'calendar-settings/tasks-settings.php'; ?>
        <?php require_once 'calendar-settings/tasks-settings-types.php'; ?>
        <?php require_once 'calendar-settings/tasks-settings-statuses.php'; ?>
        <?php require_once 'calendar-settings/meetings-cancellation-policy.php'; ?>
        <?php require_once 'calendar-settings/meetings-cancellation-policy-add-option.php'; ?>
        <?php require_once 'calendar-settings/meetings-category.php'; ?>
        <?php require_once 'calendar-settings/meetings-category-remove.php'; ?>
        <?php require_once 'calendar-settings/display-options.php'; ?>
        <?php require_once 'calendar-settings/calendars-and-classes.php'; ?>
        <?php require_once 'calendar-settings/calendars-and-classes_calendars.php'; ?>
        <?php require_once 'calendar-settings/calendars-and-classes_calendars--new.php'; ?>
        <?php require_once 'calendar-settings/calendars-and-classes_classes.php'; ?>
        <?php require_once 'calendar-settings/meetings-staff-add-coach-availability.php'; ?>
        <?php require_once 'calendar-settings/edit-periodic-availability.php'; ?>
        <?php require_once 'calendar-settings/meetings-staff-delete-coach-availability.php'; ?>
        <?php require_once 'calendar-settings/calendars-and-classes_classes--new.php'; ?>
        <?php require_once 'calendar-settings/meetings-staff-availability.php'; ?>
        <?php require_once 'calendar-settings/meetings-staff-coach-weekly-availability.php'; ?>
        <?php require_once 'calendar-settings/meetings-navigation.php'; ?>
        <?php require_once 'calendar-settings/meetings-all-templates.php'; ?>
        <?php require_once 'calendar-settings/meetings-general-settings.php'; ?>
        <?php require_once 'calendar-settings/meetings-templates-new.php'; ?>
        <?php require_once 'calendar-settings/meetings-template-class-type-remove.php'; ?>
        <?php require_once 'calendar-settings/meetings-templates-advanced-settings.php'; ?>
        <?php require_once 'calendar-settings/permanent-registration.php'; ?>
        <?php require_once 'calendar-settings/device-selection-management.php'; ?>
        <?php require_once 'calendar-settings/device-selection-management--new.php'; ?>
        <?php require_once 'calendar-settings/delete-item.php'; ?>

    </div> <!-- Dropdown Menu End -->

</div>

<!-- External Plugins -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.css">
<link rel="stylesheet" href="../../office/dist/css/select-pure-master.css">
<link rel="stylesheet" href="../../office/dist/css/calendarSettings.css">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.5.1/main.min.js"></script>

<!--script src="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.js"></script-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>
<script src="../../office/js/bundle.min.js"></script>
<script>

</script>
<script type="text/javascript">
    var $companyNo;
    $(document).ready(function () {
        $companyNo = <?php echo $CompanyNum ?>
    });
</script>