<?php
require_once __DIR__ . '/../app/initcron.php';
require_once __DIR__ . '/Classes/Users.php';
require_once __DIR__ . '/Classes/ClassStudioDate.php';

$CompanyNum = Auth::user()->CompanyNum;

$option = $_GET['option'] ?? 'new';
$editId = $_GET['id'] ?? null;
$MainDiv = 'js-block-event-popup';

// in case of edit => fill fields
/** @var ClassStudioDate $editBlock */
$editBlock = ClassStudioDate::find($editId);

$blockName = $editBlock->ClassName ?? '';
$TeamMemberSelectedId = $editBlock->GuideId ?? null;
$blockDate = $editBlock->StartDate ?? date('Y-m-d');
$startTime = $editBlock->StartTime ?? date('H:00', strtotime("+1 hour", time()));
$endTime = $editBlock->EndTime ?? date('H:00', strtotime("+2 hour", time()));
$startTime = substr($startTime, 0, 5);
$endTime = substr($endTime, 0, 5);

if ($endTime < $startTime) {
    $endTime = '23:55';
}

?>

<!-- new event block modal :: begin -->
<form class="modal-body d-flex flex-column justify-content-between p-0 h-100" id="blockEventPopupForm" method="post">
    <div class="js-subpage-home h-100">
        <!--    header    -->
        <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
            <div class="w-150p px-15 py-15">
                <span class="bsapp-fs-18 font-weight-bold"><?= lang('close_calendar_title') ?></span>
            </div>

            <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" data-dismiss="modal">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="bsapp-scroll overflow-auto bsapp-blockevent-middle-height">
            <!--    name line    -->
            <div class="d-flex px-15">
                <div class="form-group flex-fill mb-2">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('close_calendar_details') ?></label>
                    <div class="is-invalid-container">
                        <input name="BlockName" id="ClassName" maxlength="60"
                               placeholder="<?= lang('close_calendar_exmaple') . ': &quot;' . lang('close_calendar_placeholder') . '&quot;' ?>"
                               value="<?= $blockName ?>"
                               class="form-control border-light" type="text" autocomplete="off">
                    </div>
                </div>
            </div>
            <!--    guide line    -->
            <?php
            $TeamMembers = (new Users())->getCoachers($CompanyNum);
            ?>
            <div class="<?php echo(empty($TeamMembers) || sizeof($TeamMembers) == 1 ? "d-none" : "d-flex") ?> px-15">
                <div class="form-group flex-fill mb-2">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('instructor') ?></label>
                    <select class="form-control border-light" name="team-member" id="GuideId">
                        <?php
                        if (!empty($TeamMembers)) {
                            foreach ($TeamMembers as $TeamMember) { ?>
                                <option value="<?php echo $TeamMember->id; ?>" <?php if ($TeamMember->id == $TeamMemberSelectedId) echo 'selected'; ?>>
                                    <?php echo $TeamMember->display_name ?></option>
                                <?php
                            }
                        } ?>
                    </select>
                </div>
            </div>

            <!--    date line    -->
            <div class="d-flex px-15">
                <div class="form-group flex-fill mb-2">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('date_selection') ?></label>
                    <div class="is-invalid-container">
                        <input name="BlockDate" id="StartDate" value="<?= $blockDate ?>"
                               class="form-control border-light" type="date" autocomplete="off">
                    </div>
                </div>
            </div>

            <!--    time line    -->
            <div class="d-flex px-15">
                <div class="form-group flex-fill mb-2 mie-15">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('close_clandar_start') ?></label>
                    <div class="is-invalid-container">
                        <select class="js-select2-schedule js-schedule-select bg-light"
                                name="StartTime" id="StartTime">
                            <?php
                            for ($i = 0; $i <= 23; $i++) {
                                for ($j = 0; $j <= 55; $j += 5) {
                                    if ($i == 23 && $j == 55) break;
                                    $time = date('H:i', strtotime("$i:$j"));
                                    $selected = $time == $startTime ? 'selected' : '';
                                    echo "<option $selected value='$time'>$time</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group flex-fill mb-2">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('close_calendar_end') ?></label>
                    <div class="is-invalid-container">
                        <select class="js-select2-schedule js-schedule-select bg-light"
                                name="EndTime" id="EndTime">
                            <?php
                            for ($i = 0; $i <= 23; $i++) {
                                for ($j = 0; $j <= 55; $j += 5) {
                                    $time = date('H:i', strtotime("$i:$j"));
                                    $selected = $time == $endTime ? 'selected' : '';
                                    echo "<option $selected value='$time'>$time</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!--    footer    -->
        <div class="d-flex justify-content-around border-top border-light px-15 py-15">
            <?php if ($option == 'edit') : ?>
                <button type="button" class="d-inline-block btn btn-outline-secondary meeting--btn mie-15 px-40 w-100"
                        data-id="<?= $editId ?>" id="deleteBlockEvent"
                        style="min-width: unset"><?php echo lang('delete') ?></button>
            <?php endif; ?>
            <button type="submit" style="min-width: unset"
                    class="d-inline-block btn btn-dark meeting--btn px-40 w-100"><?php echo lang('save') ?></button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {

        <?php if (!empty($TeamMembers) && sizeof($TeamMembers) > 1): ?>
        $("select#GuideId").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        <?php endif; ?>

        $("select#StartTime").select2({
            theme: "bsapp-dropdown",
            width: '100%',
            minimumResultsForSearch: -1,
        }).select2('open').select2('close');

        $("select#EndTime").select2({
            theme: "bsapp-dropdown",
            width: '100%',
            minimumResultsForSearch: -1,
            matcher: function (params, data) {
                const valStart = $("select#StartTime").val();
                return (data.text > valStart ? data : null);
            },
        }).select2('open').select2('close');

        // end time logic
        $("select#StartTime").change(function () {
            let valEnd = $("select#EndTime").val();
            const valStart = $("select#StartTime").val();
            if (valEnd <= valStart) {
                const day = $("#StartDate").val()
                const date = new Date(day + " " + valStart);
                date.setHours(date.getHours() + 1);

                valEnd = String(date.getHours()).padStart(2, '0') + ":" + String(date.getMinutes()).padStart(2, '0');
                if (valEnd < valStart) {
                    valEnd = "23:55";
                }
                $("select#EndTime").val(valEnd)

                $("select#EndTime").select2({
                    theme: "bsapp-dropdown",
                    width: '100%',
                    minimumResultsForSearch: -1,
                    matcher: function (params, data) {
                        const valStart = $("select#StartTime").val();
                        return (data.text > valStart ? data : null);
                    },
                }).select2('open').select2('close');
            }
        });

        $("#deleteBlockEvent").on('click', function (event) {
            event.preventDefault();
            event.cancelBubble = true;
            event.stopPropagation();

            const id = $(this).data('id');

            const $parent = $('#<?php echo $MainDiv ?>')
            $parent.showModalLoader()

            $.ajax({
                url: 'ajax/SaveStudioDate.php',
                method: 'POST',
                data: {
                    action: 'deleteBlockEvent',
                    data: {
                        'id': id
                    },
                },
                success: function (res) {
                    if (res.status == 1) {
                        $parent.hideModalLoader();
                        $parent.modal('hide');

                        GetCalendarData();

                        $.notify({
                            icon: 'fas fa-times-circle',
                            message: res.message,
                        }, {
                            type: 'success',
                            z_index: '99999999',
                        });
                    } else {
                        $.notify({
                            icon: 'fas fa-times-circle',
                            message: lang('action_not_done'),
                        }, {
                            type: 'danger',
                            z_index: '99999999',
                        });
                    }
                },
                error: function (res) {
                    $parent.hideModalLoader();
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('action_not_done'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            })
        });

        // form submit action
        $("#blockEventPopupForm").on('submit', function (event) {
            event.preventDefault();
            event.cancelBubble = true;
            event.stopPropagation();

            const blockEventData = {};
            for (let i = 0; i < this.length; i++) {
                const field = this[i];
                const key = field.id;
                if (key == "") continue;
                switch (field.type) {
                    case "checkbox":
                        blockEventData[key] = field.checked;
                        break;
                    case "search":
                    case "button":
                        // skip
                        break;
                    default:
                        blockEventData[key] = field.value;
                }
            }

            // convert endTime to duration
            const diff = Math.abs(new Date(blockEventData['StartDate'] + " " + blockEventData['EndTime'])
                - new Date(blockEventData['StartDate'] + " " + blockEventData['StartTime']));
            blockEventData['duration'] = Math.floor((diff / 1000) / 60);

            // fix empty className
            if (!blockEventData['ClassName'] || blockEventData['ClassName'] == '') {
                blockEventData['ClassName'] = '<?= lang('close_calendar_placeholder') ?>';
                $('#ClassName').val(blockEventData['ClassName']);
            }

            <?php if ($option == 'edit') : ?>
            blockEventData['CalendarId'] = <?= $editId ?>;
            <?php endif; ?>

            // fill other required parameters
            blockEventData['Floor'] = $("#calendarFilters-location input").data("id");      // first space id
            blockEventData['ShowApp'] = 2;                                                  // don't show
            blockEventData['MaxClient'] = 0;
            blockEventData['color'] = '#E3E3E3';
            blockEventData['ClassNameType'] = 0;
            blockEventData['ClassRepeat'] = 0;

            const $parent = $('#<?php echo $MainDiv ?>')
            $parent.showModalLoader()

            $.ajax({
                url: 'ajax/SaveStudioDate.php',
                method: 'POST',
                data: {
                    action: 'saveClass',
                    data: blockEventData,
                },
                success: function (res) {
                    $parent.hideModalLoader();

                    if (res.status == 1) {
                        $parent.modal('hide');
                        GetCalendarData();

                        $.notify({
                            icon: 'fas fa-times-circle',
                            message: res.message,
                        }, {
                            type: 'success',
                            z_index: '99999999',
                        });
                    } else {
                        // show modal with conflicts
                        occupiedPopup.showBlockEvent(res);
                    }
                },
                error: function (res) {
                    $parent.hideModalLoader();
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('action_not_done'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            })
        });
    });

</script>
