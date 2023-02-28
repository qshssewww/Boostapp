<?php

require_once __DIR__ . '/../../app/initcron.php';
require_once __DIR__ . '/../Classes/Utils.php';
require_once __DIR__ . '/../Classes/ClassStudioDate.php';

if (Auth::userCan('80') || Auth::userCan('65')):

    $CompanyNum = Auth::user()->CompanyNum;

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

    $ClassYear = $_REQUEST['ClassYear'];
    $ClassMonth = $_REQUEST['ClassMonth'];

    $ClassDateStart = $ClassYear . '-' . $ClassMonth . '-01';
    $ClassDateEnd = $ClassYear . '-' . $ClassMonth . '-31';
    $ClientId = $_REQUEST['ClientId'];

    ?>

    <div class="row" style="padding-right: 15px;padding-left: 15px;">
        <div class="col-md-2">
            <?php
            $starting_year = $SettingsInfo->StartYear;

            $yearQuarter = Utils::getCurrentAnnualQuarter(date('Y-m-d'));
            $ending_year = $yearQuarter == 4.0 ? date('Y', strtotime("+1 years")) : date('Y');

            for ($starting_year; $starting_year <= $ending_year; $starting_year++) {
                if ($starting_year == $ClassYear) {
                    $years[] = '<option value="' . $starting_year . '" selected>' . $starting_year . '</option>';
                } else {
                    $years[] = '<option value="' . $starting_year . '">' . $starting_year . '</option>';
                }
            }

            ?>

            <select name="HistoryYears" id="HistoryYears" data-placeholder="<?php echo lang('choose_year') ?>" class="form-control form-control-sm" style="width:100%;">
                <?php echo implode("\n\r", $years); ?>
            </select>

        </div>


        <div class="col-md-10">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <ul class="pagination pagination-sm">
                        <li class="page-item <?php if ($ClassMonth == '01') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="01">
                                <?php echo lang('january') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '02') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="02">
                                <?php echo lang('february') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '03') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="03">
                                <?php echo lang('march') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '04') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="04">
                                <?php echo lang('april') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '05') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="05">
                                <?php echo lang('may') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '06') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="06">
                                <?php echo lang('june') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '07') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="07">
                                <?php echo lang('july') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '08') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="08">
                                <?php echo lang('august') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '09') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="09">
                                <?php echo lang('september') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '10') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="10">
                                <?php echo lang('october') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '11') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="11">
                                <?php echo lang('november') ?>
                            </a>
                        </li>

                        <li class="page-item <?php if ($ClassMonth == '12') {
                            echo 'active';
                            $TextColor = '';
                        } else {
                            $TextColor = 'text-primary';
                        } ?>">
                            <a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="12">
                                <?php echo lang('december') ?>
                            </a>
                        </li>
                    </ul>
                </ul>
            </nav>
        </div>
    </div>

    <div class="alertb alert-light">
        <div class="row" style="padding-right: 15px;padding-left: 15px;">
            <div class="col-md-2 col-sm-12 order-md-1">
                <label><?php echo lang('membership') ?></label>
            </div>

            <div class="col-md col-sm-12 order-md-2">
                <label><?php echo lang('location') ?></label>
            </div>

            <div class="col-md-2 col-sm-12 order-md-3">
                <label><?php echo lang('customer_card_class_title') ?></label>
            </div>

            <div class="col-md col-sm-12 order-md-4">
                <label><?php echo lang('date') ?></label>
            </div>


            <div class="col-md col-sm-12 order-md-5">
                <label><?php echo lang('day') ?></label>
            </div>


            <div class="col-md col-sm-12 order-md-6">
                <label><?php echo lang('hour') ?></label>
            </div>


            <div class="col-md col-sm-12 order-md-7">
                <label><?php echo lang('instructor') ?></label>
            </div>

            <div class="col-md col-sm-12 order-md-8">
                <label><?php echo lang('status_table') ?></label>
            </div>


            <div class="col-md col-sm-12 order-md-9">
                <label><?php echo lang('management') ?></label>
            </div>
        </div>

    </div>

    <?php

    $ClassHistorys = DB::table('classstudio_act')
        ->where('CompanyNum', $CompanyNum)
        ->whereBetween('ClassDate', array($ClassDateStart, $ClassDateEnd))
        ->where('FixClientId', $ClientId)
        ->orderBy('ClassDate', 'ASC')
        ->orderBy('Status', 'ASC')
        ->get();

    foreach ($ClassHistorys as $ClassHistory) {
        $ClassHistoryDate = ClassStudioDate::find($ClassHistory->ClassId);
        $StatusInfoColor = DB::table('class_status')->where('id', '=', $ClassHistory->Status)->first();
        $MemberShipInfo = DB::table('client_activities')->where('id', '=', $ClassHistory->ClientActivitiesId)->where('CompanyNum', '=', $CompanyNum)->first();
        $FloorInfo = DB::table('sections')->where('id', '=', $ClassHistoryDate->Floor)->where('CompanyNum', '=', $CompanyNum)->first();
        $GuideInfo = DB::table('users')->where('id', '=', $ClassHistoryDate->GuideId)->where('CompanyNum', '=', $CompanyNum)->first();

        $ClassDay = transDbVal(trim($ClassHistoryDate->Day));
        $FloorName = $FloorInfo->Title ?? '';
        $GuideName = $GuideInfo->display_name ?? '';

        if ($ClassHistory->TrueClientId == '0') {
            $TrueClientIcon = '';
        } else {
            $TrueClientIcon = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i>';
        }

        ?>

        <div class="alertb alert-light">
            <div class="row" style="padding-right: 15px;padding-left: 15px;">
                <div class="col-md-2 col-sm-12 order-md-1">
                    <?php if ($ClassHistory->RegularClass == '1' && ($ClassHistory->Status == 9 || $ClassHistory->Status == 12)) { ?>
                        <?php if (Auth::userCan('124')): ?>
                            <a href='javascript:LogActivityRegular("<?php echo $ClassHistory->ClientActivitiesId; ?>");'><?php echo lang('will_be_updated') ?></a>
                        <?php else: ?>
                            <p><?php echo lang('will_be_updated') ?></p>
                        <?php endif ?>
                    <?php } else { ?>
                        <?php if (Auth::userCan('124')): ?>
                            <p>
                                <a href='javascript:LogActivity("<?php echo $ClassHistory->ClientActivitiesId; ?>");'><?php echo @$MemberShipInfo->ItemText; ?>
                                    (<?php echo @$MemberShipInfo->CardNumber; ?>) </a> <?php echo $TrueClientIcon; ?>
                            </p>
                        <?php else: ?>
                            <p><?php echo @$MemberShipInfo->ItemText; ?><?php echo $TrueClientIcon; ?></p>
                        <?php endif ?>
                    <?php } ?>
                </div>

                <div class="col-md col-sm-12 order-md-2">
                    <p><?php echo $FloorName; ?></p>
                </div>

                <div class="col-md-2 col-sm-12 order-md-3">
                    <p><?php echo $ClassHistoryDate->ClassName; ?></p>
                </div>

                <div class="col-md col-sm-12 order-md-4">
                    <p><?php echo with(new DateTime($ClassHistoryDate->StartDate))->format('d/m/Y'); ?></p>
                </div>

                <div class="col-md col-sm-12 order-md-5">
                    <p><?php echo $ClassDay; ?></p>
                </div>

                <div class="col-md col-sm-12 order-md-6">
                    <p><?php echo with(new DateTime($ClassHistoryDate->StartTime))->format('H:i'); ?></p>
                </div>

                <div class="col-md col-sm-12 order-md-7">
                    <p><?php echo $GuideName; ?></p>
                </div>

                <div class="col-md col-sm-12 order-md-8">
                <?php if (!$ClassHistoryDate || !$ClassHistoryDate->meetingTemplateId || in_array($StatusInfoColor->id,[8,3])) : ?>
                    <p <?php echo $StatusInfoColor->Color ?>><?php echo transDbVal(trim($StatusInfoColor->Title)) ?></p>
                <?php else: ?>
                    <p><?= MeetingStatus::name($ClassHistoryDate->meetingStatus) ?></p>
                <?php endif; ?>
                </div>

                <div class="col-md col-sm-12 order-md-9">
                <?php if (!$ClassHistoryDate || !$ClassHistoryDate->meetingTemplateId): ?>
                    <p>
                        <a href='javascript:ChangeActivity("<?php echo $ClassHistory->id; ?>","<?php echo $ClassHistory->ClientActivitiesId; ?>","<?php echo $ClassYear; ?>","<?php echo $ClassMonth; ?>","<?php echo $ClientId; ?>");'>
                            <i class="fas fa-retweet"></i> <?php echo lang('change_membership') ?>
                        </a>
                    </p>
                <?php endif; ?>
                </div>
            </div>
        </div>

        <hr>

    <?php } ?>

    <input type="hidden" id="HistoryMonth">

    <script>
        $(document).ready(function () {
            $('#HistoryMonth').val('<?php echo $ClassMonth; ?>');

            $('.HistoryItem').click(function () {
                var ClassYear = $('#HistoryYears').val();
                var ClassMonth = $(this).data('month');
                var ClientId = '<?php echo $ClientId; ?>';

                $('#HistoryMonth').val(ClassMonth);

                var url = 'action/ClassHistory.php?ClassYear=' + ClassYear + '&ClassMonth=' + ClassMonth + '&ClientId=' + ClientId;

                $('#DivClassHistory').empty();
                $('#DivClassHistory').load(url);
            });


            $('#HistoryYears').change(function () {
                var ClassYear = $('#HistoryYears').val();
                var ClassMonth = $('#HistoryMonth').val();
                var ClientId = '<?php echo $ClientId; ?>';

                var url = 'action/ClassHistory.php?ClassYear=' + ClassYear + '&ClassMonth=' + ClassMonth + '&ClientId=' + ClientId;

                $('#DivClassHistory').empty();
                $('#DivClassHistory').load(url);
            });
        });

    </script>
<?php endif ?>
