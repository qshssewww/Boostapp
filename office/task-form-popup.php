<?php
require_once __DIR__ . '/../app/initcron.php';
require_once __DIR__ . '/Classes/CalType.php';
require_once __DIR__ . '/Classes/TaskStatus.php';
require_once __DIR__ . '/Classes/calendar.php';
require_once __DIR__ . '/Classes/Client.php';

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$BrandsMain = $SettingsInfo->BrandsMain;

$id = empty($_GET['id']) ? null : $_GET['id'];
$task = (new calendar())->getTaskById($CompanyNum, $id);
$clientId = $task->ClientId ?? (empty($_GET['clientId']) ? null : $_GET['clientId']);
if (!$task) $id = null;
/** @var Client $client */
$client = Client::find($clientId);

$MainDiv = 'js-task-popup';
$typesTasks = CalType::getAllActiveByCompanyNum($CompanyNum);

$taskName = $task->Title ?? '';
$taskType = $task->Type ?? null;
$taskPriority = $task->Level ?? null;
$taskAgentId = $task->AgentId ?? null;
$taskGroupPermissions = explode(',', $task->GroupPermission ?? '');
$taskStatus = $task->Status ?? '';
$SetDate = $task->StartDate ?? date('Y-m-d');
$startTime = $task->StartTime ?? date('H:00', strtotime("+1 hour", time()));
$endTime = $task->EndTime ?? date('H:00', strtotime("+2 hour", time()));
$startTime = substr($startTime, 0, 5);
$endTime = substr($endTime, 0, 5);
$taskRemarks = $task->Content ?? '';

$rolesList = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->orderBy('id', 'ASC')->get();
$taskStatuses = TaskStatus::getAllActiveByCompanyNum($CompanyNum);
?>

<!-- new task modal :: begin -->
<form class="modal-body d-flex flex-column justify-content-between p-0 h-100" id="new-task" method="post">
    <div class="js-subpage-home h-100">
        <!--    header    -->
        <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
            <div class="w-100 px-15 py-15">
                <span class="bsapp-fs-18 font-weight-bold"><?= lang('task_window_title') ?></span>
            </div>

            <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" data-dismiss="modal">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="bsapp-scroll overflow-y-auto bsapp-newclient-middle-height" style="overflow-x:hidden ">
            <!--    name line    -->
            <div class="d-flex px-15 mt-10">
                <div style="position: relative" class="form-group flex-fill mb-10 d-flex flex-column ">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang("custome_selection") ?></label>
                    <input name="customer" id="task-customer" maxlength="60"
                           placeholder="<?= lang("search_by_name_or_phone") ?>"
                           name="name" value="<?= $client ? htmlspecialchars($client->CompanyName) : '' ?>"
                           class="form-control border-light bg-light border-light text-start" type="text"
                           autocomplete="off" style="direction:inherit"
                           oninvalid="this.setCustomValidity('<?= lang('first_name_req_field') ?>')"
                           oninput="this.setCustomValidity('')">
                    <i class="fal fa-times-circle delete-btn"></i>
                    </input>
                </div>
            </div>

            <!--    task title   -->
            <div class="d-flex px-15 align-items-end">
                <div class="form-group flex-fill ">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('task_title') ?></label>
                    <input type="text" class="form-control text-start border-light" id="task-title" required
                           oninvalid="this.setCustomValidity('<?= lang('type_title_ajax') ?>')"
                           oninput="this.setCustomValidity('')"
                           value="<?= $taskName ?>" />
                </div>
            </div>
            <div class="form-row px-15">
                <div class=" col">
                    <div class="form-group flex-fill mb-0">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang("task_type") ?></label>
                        <select class="form-control border-light" name="Type" id="task-type">
                            <?php
                            /** @var CalType $type */
                            foreach ($typesTasks as $type) { ?>
                                <option value="<?= $type->id ?>" <?= $type->id == $taskType ? 'selected' : '' ?>>
                                    <?= $type->Type ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class=" col">
                    <div class="form-group flex-fill mb-0">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang("priority") ?></label>
                        <select class="form-control border-light" name="Level" id="task-priority">
                            <option value="0" <?= $taskPriority == 0 ? 'selected' : '' ?>><?= lang('low_priority_cal') ?></option>
                            <option value="1" <?= $taskPriority == 1 ? 'selected' : '' ?>><?= lang('medium_priority_cal') ?></option>
                            <option value="2" <?= $taskPriority == 2 ? 'selected' : '' ?>><?= lang('high_priority_cal') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <!--    date    -->
            <div class="d-flex px-15 align-items-end ">
                <div class="form-group flex-fill mb-10 w-100">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('date') ?></label>
                    <input name="SetDate" id="task-SetDate" type="date" value="<?= $SetDate ?>"
                           class="form-control border-light" placeholder="<?= lang('set_reminder_cal') ?>"/>
                </div>
            </div>

            <div class="form-row px-15">
                <div class="col">
                    <label class="custom-select-sm mb-0 font-weight-bold" custom-select-sm mb-0
                           font-weight-bold><?= lang('a_from_hour') ?></label>
                    <select name="SetTime" id="task-fromTime" class="form-control"
                            placeholder="<?= lang('set_reminder_cal') ?>">
                        <?php for ($i = 0; $i <= 23; $i++) {
                            for ($j = 0; $j <= 55; $j += 5) {
                                if ($i == 23 && $j == 55) break;
                                $time = date('H:i', strtotime("$i:$j"));
                                $selected = $time == $startTime ? 'selected' : '';
                                echo "<option $selected value='$time'>$time</option>";
                            }
                        } ?>
                    </select>
                </div>
                <div class="col">
                    <label class="custom-select-sm mb-0 font-weight-bold" custom-select-sm mb-0
                           font-weight-bold> <?= lang('end_hour') ?></label>
                    <select name="SetToTime" id="task-tillTime" class="form-control"
                            placeholder="<?= lang('set_reminder_cal') ?>">
                        <?php for ($i = 0; $i <= 23; $i++) {
                            for ($j = 0; $j <= 55; $j += 5) {
                                $time = date('H:i', strtotime("$i:$j"));
                                $selected = $time == $endTime ? 'selected' : '';
                                echo "<option $selected value='$time'>$time</option>";
                            }
                        } ?>
                    </select>
                </div>
            </div>

            <hr>
            <div class="form-row px-15">
                <div class="col">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('taking_care_representative') ?></label>
                    <select class="form-control js-example-basic-single select2" id="task-AgentForTask" name="AgentId"
                            data-placeholder="<?= lang('choose_taking_care_representative') ?>"
                            style="width: 100%">
                        <?php
                        if ($BrandsMain == '0') {
                            $UserInfos = DB::table('users')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('role_id', '!=', '1')
                                ->where('status', '=', '1')
                                ->orderBy('display_name', 'ASC')
                                ->get();
                        } else {
                            $UserInfos = DB::table('users')
                                ->where('BrandsMain', '=', $BrandsMain)
                                ->where('role_id', '!=', '1')
                                ->where('status', '=', '1')
                                ->orderBy('display_name', 'ASC')
                                ->get();
                        }
                        foreach ($UserInfos as $UserInfo) { ?>
                            <option value="<?= $UserInfo->id; ?>" <?= $taskAgentId == $UserInfo->id ? 'selected' : '' ?>>
                                <?= $UserInfo->display_name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col ">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang("task_auth_group") ?></label>
                    <select id="task-permissions" multiple
                            class="bsappMultiSelect form-control js-example-basic-single select2" name="Class">
                        <?php foreach ($rolesList as $role) { ?>
                            <option value="<?= $role->id ?>" <?= in_array($role->id, $taskGroupPermissions) ? 'selected' : '' ?>>
                                <?= $role->Title ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group w-100 px-15">
                <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('task_content') ?></label>
                <textarea name="Remarks" id="task-CalRemarks" class="form-control border-light " rows="3"><?= $taskRemarks ?></textarea>
            </div>

        </div>
        <div class="p-15 flex-row d-flex justify-content-between">
            <select id="task-status" class="border-radius-3r">
                <option value="0" <?= $taskStatus == 0 ? "selected" : "" ?>><?= lang('open_task') ?></option>
                <option value="1" <?= $taskStatus == 1 ? "selected" : "" ?>><?= lang('completed_task') ?></option>
                <option value="2" <?= $taskStatus == 2 ? "selected" : "" ?>><?= lang('canceled_task') ?></option>
                <?php
                /** @var TaskStatus $status */
                foreach ($taskStatuses as $status) { ?>
                    <option value="<?= $status->id ?>" <?= $taskStatus == $status->id ? "selected" : "" ?>>
                        <?= $status->Name ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-dark  " style="width:<?= $id ? "48%!important" : "100%" ?>">
                <?= $id != null ? lang('save_changes_button') : lang("save") ?>
            </button>
        </div>
    </div>
    <!--    footer    -->
</form>

<script>
    $(document).ready(function () {
        let clientId = <?=$client->id ?? '""' ?>;
        let taskId = <?=$id ?? '""'?>;
        const customerInput = document.getElementById('task-customer');

        $('.delete-btn').on('click', (e) => {
            clientId = '';
            customerInput.value = '';
            customerInput.removeAttribute("disabled")
        })

        // select2 type
        $('#task-type').select2({
            theme: "bsapp-dropdown",
            width: '100%',

        });

        $('#task-priority').select2({
            theme: "bsapp-dropdown",
            width: "100%"
        });

        $('#task-AgentForTask').select2({
            theme: "bsapp-dropdown",

        });

        $('#task-permisionGroup').select2({
            theme: "bsapp-dropdown",

        });

        $('#task-status').select2({
            theme: "bsapp-dropdown status-task-select ",
            minimumResultsForSearch: -1,
        }).select2('open').select2('close')

        const taskStatusContainer = document.querySelector('.status-task-select');
        if (taskId) {
            taskStatusContainer.classList.remove('d-none')
        } else {
            taskStatusContainer.classList.add('d-none');
        }

        $("select#task-fromTime").select2({
            theme: "bsapp-dropdown",
            width: '100%',
            minimumResultsForSearch: -1,
        }).select2('open').select2('close');

        $("select#task-tillTime").select2({
            theme: "bsapp-dropdown",
            width: '100%',
            minimumResultsForSearch: -1,
            matcher: function (params, data) {
                const valStart = $("select#task-fromTime").val();
                return (data.text > valStart ? data : null);
            },
        }).select2('open').select2('close');

        // end time logic
        $("select#task-fromTime").change(function () {
            let valEnd = $("select#task-tillTime").val();
            const valStart = $("select#task-fromTime").val();
            if (valEnd <= valStart) {
                const day = $("#task-SetDate").val()
                const date = new Date(day + " " + valStart);
                date.setHours(date.getHours() + 1);

                valEnd = String(date.getHours()).padStart(2, '0') + ":" + String(date.getMinutes()).padStart(2, '0');
                if (valEnd < valStart) {
                    valEnd = "23:55";
                }
                $("select#task-tillTime").val(valEnd)

                $("select#task-tillTime").select2({
                    theme: "bsapp-dropdown",
                    width: '100%',
                    minimumResultsForSearch: -1,
                    matcher: function (params, data) {
                        const valStart = $("select#task-fromTime").val();
                        return (data.text > valStart ? data : null);
                    },
                }).select2('open').select2('close');
            }
        });

        //mobile client search :: begin
        const mobile_users_data = new Bloodhound({
            datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                wildcard: '%QUERY',
                url: '/office/action/getClientsJson.php?query=%QUERY',
                // Map the remote source JSON array to a JavaScript object array
                transform: response => $.map(response.results, user => ({
                    name: user.name,
                    url: user.url,
                    email: user.email,
                    img: user.img,
                    phone: user.phone,
                    brand: user.brand,
                    status: user.status,
                    id: user.id
                }))
            }
        });

        function clientSuggestionsSMWithDefaults(q, sync, async) {
            if (q === '') { // if query is empty, show default suggestions
                sync([]);
            } else {
                /* countries_suggestions is the bloodhound instance
                   as we used in the previous example */
                mobile_users_data.search(q, sync, async);
            }
        }

        $('#task-customer').typeahead({
            hint: true,
            highlight: true,
            minLength: 0,
            showHintOnFocus: true,
        }, {
            display: 'name',
            name: 'remote-data',
            source: clientSuggestionsSMWithDefaults,
            limit: Infinity,
            templates: {
                empty: [
                    '<div class="empty-message px-15 py-15">',
                    'לא נמצא לקוח',
                    '</div>'
                ].join('\n'),
                //suggestion: Handlebars.compile('<div class="text-start position-relative">{{name}}<a class="stretched-link" href="{{url}}"></a></div></div>')
                suggestion: Handlebars.compile('<div data="{{id}}"  class="data d-flex text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6>{{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div></div>')
            }
        }, {
            display: "name",
            name: "default-data",
            limit: 5,
            source: function (query, callback) {
                if (query == "") {
                    saved_searches = JSON.parse(localStorage.getItem('task_search_history'));
                } else {
                    saved_searches = [];
                }
                callback(saved_searches);
            },
            templates: {
                empty: [
                    ''
                ].join('\n'),
                suggestion: Handlebars.compile('<div data="{{id}}"  class="data d-flex text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6>{{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div></div>')
            }
        }).on('typeahead:selected', function (e, datum) {
            // TO SAVE LOCAL STORAGE
            return
            if (window.localStorage.getItem("task_search_history") == null) {
                var data_array = new Array();
                data_array.unshift(datum);
                window.localStorage.setItem("task_search_history", JSON.stringify(data_array));
            } else {
                var saved_searches = JSON.parse(localStorage.getItem('js_selected_history'));
                saved_searches.unshift(datum);
                saved_searches.length = 5;
                window.localStorage.setItem("task_search_history", JSON.stringify(saved_searches));
            }
        });

        $('#task-permissions').bsappMultiSelect({
            groupSizeLimit: 1,
        });

        window.addEventListener('click', (e) => {
            e.stopPropagation()
            const el = e.target.closest('.data');
            if (el) {
                const id = el.getAttribute('data');
                clientId = id;
                customerInput.setAttribute("disabled", true)
            }
            if (!clientId) {
                customerInput.value = ''
            }
        })

        $("#new-task").on('submit', (e) => {
            e.preventDefault();
            event.cancelBubble = true;
            event.stopPropagation();
            $('#js-task-popup').showModalLoader()

            const title = $('#task-title').val();
            const date = $("#task-SetDate").val();
            const fromTime = $("#task-fromTime").val()
            const tillTime = $("#task-tillTime").val();
            const agent = $("#task-AgentForTask").val();
            const per = $('#task-permissions').val()
            const taskType = $('#task-type').val();
            const priority = $('#task-priority').val();
            const text = $('#task-CalRemarks').val();
            const status = $('#task-status').val()

            const values = {
                ClientId: clientId,
                TaskTitle: title,
                SetDate: date,
                SetTime: fromTime,
                SetToTime: tillTime,
                AgentId: agent,
                SendStudioOption: per,
                TypeOption: taskType,
                Level: priority,
                Remarks: text,
                action: "AddCalendarClient",
                TaskStatus: status,
                <?= $id ? 'CalendarId: ' . $id . ',' : ''?>
            }
            $.ajax({
                url: '../ajax.php',
                method: 'POST',
                data: values,
                success: function (res) {
                    $('#js-task-popup').find('.modal-content').find(".js-loader").remove();
                    if (res.success) {
                        if (typeof GetCalendarData === 'function') {
                            GetCalendarData();
                        } else {
                            location.reload();
                        }
                        $('#<?= $MainDiv ?>').modal('hide');
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
                            message: res.message,
                        }, {
                            type: 'danger',
                            z_index: '99999999',
                        });
                    }
                },
                error: function (res) {
                    $('#js-task-popup').find('.modal-content').find(".js-loader").remove();
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('action_not_done'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            })
        })
    });
</script>
