<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

if (Auth::userCan('149')):

$report = new StdClass();
$report->name = lang('time_clock');
$pageTitle = lang('time_clock_report');
require_once '../../app/views/headernew.php';



?>
<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>

<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

<!--  not responsive make total time bug...-->
<!--<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">-->


<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
</script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js">
</script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
</script>
<script src="../js/datatable/dataTables.checkboxes.min.js">
</script>

<link href="../assets/css/fixstyle.css?<?php echo filemtime('../assets/css/fixstyle.css') ?>" rel="stylesheet">
<style>
    .bg-gray {
        background-color: #e9ecef;
    }

    .dataTables_scrollHead table {
        margin-bottom: 0px;
    }
</style>


<div class="row mx-0 px-0" >
    <div class="col-12 mx-0 px-0" >

        <div class="row">

            <?php require_once("../ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                    <?php echo $report->name ?>
                                </strong>
                            </div>
                            <div class="card-body">

                                <!-- page content -->
                                <hr>

                                <div style="padding-left:15px; padding-right:15px;" ng-app="signInClock" ng-controller="form as vm" ng-cloak>
                                    <div class="row" >
                                        <div class="col-4">
                                            <label><?php echo lang('employee_single') ?></label>
                                            <select ng-model="vm.filter.user" class="form-control">
                                                <option value=""><?php echo lang('select_employee') ?></option>
                                                <option ng-repeat="option in vm.users" ng-value="option">{{option.fullName}}</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label><?php echo lang('month') ?></label>
                                            <select ng-model="vm.filter.month" class="form-control">
                                                <option value=""><?php echo lang('choose_month') ?></option>
                                                <option ng-repeat="option in vm.months" ng-value="option">{{option.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label><?php echo lang('year_signinclock') ?></label>
                                            <select ng-model="vm.filter.year" class="form-control">
                                                <option value=""><?php echo lang('choose_year') ?></option>
                                                <option ng-repeat="option in vm.years" ng-value="option">{{option.year}}</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label><?php echo lang('tools') ?></label>
                                            <div id="toolbar">
                                                <a href="#" class="btn d-inline mie-6" ng-show="vm.filter.user" ng-class="{'btn-outline-info': !vm.$$edit, 'btn-outline-success': vm.$$edit}"
                                                    ng-click="vm.$$edit ? vm.save() : vm.$$edit = true">
                                                    <span ng-show="!vm.$$edit"><i class="far fa-edit"></i> <?php echo lang('edit') ?></span>
                                                    <span ng-show="vm.$$edit"><i class="fa fa-save"></i> <?php echo lang('save_action') ?></span>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- end filter -->
                                    <div id="printcontentTitle" hidden>
                                    <?php echo lang('employee_name') ?>: {{vm.filter.user.fullName}}
                                    </div>
                                    <table class="table" id="printcontent">
                                        <thead>
                                            <th><?php echo lang('date') ?></th>
                                            <th><?php echo lang('entrance') ?></th>
                                            <th><?php echo lang('exit_action') ?></th>
                                            <th><?php echo lang('total') ?></th>
                                        </thead>
                                        <tbody >
                                            <tr ng-repeat="day in vm.daysInMonth()" ng-class="{'text-danger': ['שישי', 'שבת'].indexOf(vm.getDayName(day)) != -1}">
                                                <td>{{vm.padLeft(day, 2, '0')}}/{{vm.padLeft(vm.filter.month.value, 2, '0')}}/{{vm.filter.year.year}}
                                                    - {{vm.getDayName(day)}}</td>
                                                <td>
                                                    <div ng-show="vm.$$edit" class="mb-1">
                                                        <button class="btn btn-outline-success btn-sm" type="button" ng-click="vm.insertTime(day, 0)">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div ng-repeat="data in vm.getEntrence(day)" ng-if="data.Act === '0' && data.Status != '0' ">
                                                        <div ng-show="!vm.$$edit && data.Status != '0'">{{data.jsDate | date:'HH:mm'}}</div>
                                                        <div ng-show="vm.$$edit" class="row">
                                                            <div class="col-8">
                                                                <input type="time" ng-model="data.jsDate" class="form-control" ng-disabled="data.Status == '0'" ng-model-option="{debounce: 1000}">
                                                            </div>
                                                            <div class="col-4">
                                                                <button ng-show="data.Status != '0'" class="btn btn-outline-danger" type="button" ng-click="vm.remove(vm.getEntrence(day), $index)">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                                <button ng-show="data.Status == '0'" class="btn btn-outline-info" type="button" ng-click="data.Status = '1'">
                                                                    <i class="fa fa-undo"></i>
                                                                </button>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div ng-show="vm.$$edit" class="mb-1">
                                                        <button class="btn btn-outline-success btn-sm" type="button" ng-click="vm.insertTime(day, 1)">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div ng-repeat="data in vm.getEntrence(day)" ng-if="data.Act === '1' && data.Status != '0'">
                                                        <div ng-show="!vm.$$edit && data.Status != '0'">
                                                            {{data.jsDate | date:'HH:mm'}}</div>
                                                        <div ng-show="vm.$$edit" class="row">
                                                            <div class="col-8">
                                                                <input type="time" ng-model="data.jsDate" class="form-control" ng-disabled="data.Status == '0'" ng-model-option="{debounce: 1000}">
                                                            </div>
                                                            <div class="col-4">
                                                                <button ng-show="data.Status != '0'" class="btn btn-outline-danger" type="button" ng-click="vm.remove(vm.getEntrence(day), $index)">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                                <button ng-show="data.Status == '0'" class="btn btn-outline-info" type="button" ng-click="data.Status = '1'">
                                                                    <i class="fa fa-undo"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{vm.getTotalTimeDay(day) || '0.00'}}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                        <th class="text-start"><?php echo lang('total').':' ?><span class="mis-7">{{vm.getMonthTotal() || '0.00'}}</span></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        </tfoot>
                                    </table>
                                    <div class="row" >
                                        <div class="col-5">
                                            <label><?php echo lang('employee_single') ?></label>
                                            <select ng-model="vm.filter.user" class="form-control">
                                                <option value=""><?php echo lang('select_employee') ?></option>
                                                <option ng-repeat="option in vm.users" ng-value="option">{{option.fullName}}</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label><?php echo lang('month') ?></label>
                                            <select ng-model="vm.filter.month" class="form-control">
                                                <option value=""><?php echo lang('choose_month') ?></option>
                                                <option ng-repeat="option in vm.months" ng-value="option">{{option.name}}</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label><?php echo lang('year_signinclock') ?></label>
                                            <select ng-model="vm.filter.year" class="form-control">
                                                <option value=""><?php echo lang('choose_year') ?></option>
                                                <option ng-repeat="option in vm.years" ng-value="option">{{option.year}}</option>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <label><?php echo lang('tools') ?></label>
                                            <div>
                                                <a href="#" class="btn d-inline mie-9" ng-show="vm.filter.user" ng-class="{'btn-outline-info': !vm.$$edit, 'btn-outline-success': vm.$$edit}"
                                                    ng-click="vm.$$edit ? vm.save() : vm.$$edit = true">
                                                    <span ng-show="!vm.$$edit"><i class="far fa-edit"></i> <?php echo lang('edit') ?></span>
                                                    <span ng-show="vm.$$edit"><i class="fa fa-save"></i> <?php echo lang('save_action') ?></span>
                                                </a>
                                                <a href="#" class="btn btn-outline-info d-inline mr-2" ng-click="vm.print()"><i class="fa fa-print"></i>
                                                <?php echo lang('print') ?></a>
                                                
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- end page content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            body * {
                visibility: hidden !important;
            }

            #printcontent * {
                visibility: visible !important;
            }

            #printcontentTitle {
                visibility: visible !important;
                position: absolute;
                top: -165px;
                right: 10px;
                display: initial !important;
            }

            #printcontent {
                position: absolute;
                top: -110px;
                left: 0px;
            }

            .table th,
            .table td {
                padding-top: 0px !important;
                padding-bottom: 0.3em !important;
            }

            .table th:first-child,
            .table td:first-child {
                width: 200px;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.2/angular.min.js">
    </script>
    <?php if(isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == "eng") { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/en.js"></script>
    <?php } else { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>
    <?php } ?>
    <script>
        angular.module("signInClock", []).controller('form', ['$http', '$scope', '$timeout', function($http, $scope,
            $timeout) {
            var vm = this;
            vm.filter = {} // container hold filter for user, month and year
            vm.filter.user = undefined;
            vm.users = []; // hold users of Company via AJAX populated
            vm.$$edit = false; // Boolean edit user clock time
            vm.clock = {}; // initilize a clock for user


            $http.get('../rest/?type=report&method=users').then(function(res) {
                // console.log("get1", res);
                vm.users = res.data.items || [];
                // test purpose
                vm.filter.user = vm.users[0];
            }, function() {});




            // populate users from db



            // print button to popup system dialog
            // print is setup via queria media of css
            vm.print = function() {
                vm.$$edit = false;
                vm.sortTime();
                $timeout(function() {
                    window.print();
                }, 0);

            }

            // months dict.
            vm.months = [{
                    name: '<?php echo lang('january') ?>',
                    value: 1
                },
                {
                    name: '<?php echo lang('february') ?>',
                    value: 2
                },
                {
                    name: '<?php echo lang('march') ?>',
                    value: 3
                },
                {
                    name: '<?php echo lang('april') ?>',
                    value: 4
                },
                {
                    name: '<?php echo lang('may') ?>',
                    value: 5
                },
                {
                    name: '<?php echo lang('june') ?>',
                    value: 6
                },
                {
                    name: '<?php echo lang('july') ?>',
                    value: 7
                },
                {
                    name: '<?php echo lang('august') ?>',
                    value: 8
                },
                {
                    name: '<?php echo lang('september') ?>',
                    value: 9
                },
                {
                    name: '<?php echo lang('october') ?>',
                    value: 10
                },
                {
                    name: '<?php echo lang('november') ?>',
                    value: 11
                },
                {
                    name: '<?php echo lang('december') ?>',
                    value: 12
                }
            ];

            // bind vurrunt month to filter
            var currMonth = ((new Date()).getMonth() + 1);
            vm.filter.month = (vm.months.filter(function(x) {
                return x.value == currMonth
            })[0]);

            vm.years = [];
            var currYear = ((new Date()).getFullYear());
            // make a list of years for user
            for (let index = currYear; index >= 2018; index--) {
                vm.years.push({
                    year: index
                })
            }
            // bind currunt year to filter
            vm.filter.year = vm.years.filter(function(x) {
                return x.year == currYear
            })[0];

            // a helper function to add 0's to dates
            vm.padLeft = function(nr, n, str) {
                return Array(n - String(nr).length + 1).join(str || '0') + nr;
            }



            // a helper to sort month by day times, help logic
            vm.sortTime = function sortTime() {
                for (var date in vm.clock) {
                    vm.clock[date] = vm.clock[date].sort(function(a, b) {
                        return a.jsDate - b.jsDate
                    })
                }
            }

            // save user clock back to server
            // using system time to upsert to db
            vm.save = function() {
                $timeout(function() {
                    var prepare = [];
                    for (var date in vm.clock) {
                        for (let index = 0; index < vm.clock[date].length; index++) {
                            prepare.push(vm.clock[date][index])
                        }
                    }

                    $http.post('../rest/?type=report&method=clock', {
                        dates: prepare,
                        userId: vm.filter.user.userId
                    }).then(function(res) {
                        vm.sortTime();
                        vm.$$edit = false;
                    }, function(res) {
                        alert('<?php echo lang('info_not_saved') ?>')
                    })
                }, 0)

            }

            // helper to figure out days in given month via the filter
            vm.daysInMonth = function() {
                var date = new Date(vm.filter.year.year, vm.filter.month.value - 1, 1);
                var days = moment(date).daysInMonth();
                var arr = [];
                for (let index = 0; index < days; index++) {
                    arr.push(index + 1);
                }
                return arr;
            }


            // helper convert date and return as string Y-m-d
            function dayToDate(day) {
                var day = vm.padLeft(day, 2, '0');
                var month = vm.padLeft(vm.filter.month.value, 2);
                var year = vm.filter.year.year;

                return (year + '-' + month + '-' + day).toString();
            }

            // insert time for given dat, Act 0=clockin 1=clockout
            vm.insertTime = function(day, Act) {
                if (!vm.filter.user) return;
                date = dayToDate(day);
                vm.clock = vm.clock || {};
                vm.clock[date] = vm.clock[date] || [];
                var now = new Date();
                vm.clock[date].push({
                    Act: Act.toString(),
                    jsDate: new Date(vm.filter.year.year, vm.filter.month.value - 1, day, now.getHours(),
                        now.getMinutes(), now.getSeconds()),
                    Status: "1"
                });
            }

            // remove a clock, if id exists just concert Status to 0
            vm.remove = function(arr, index) {
                var el = arr[index];
                if (el.id) return el.Status = "0";
                arr.splice(index, 1);
            }

            // get day clock via filters
            vm.getEntrence = function(day) {
                if (!vm.filter.user) return;
                date = dayToDate(day);
                if (!vm.clock || !vm.clock[date]) return;
                var data = vm.clock[date];
                return data;
            }

            // output day name using locale moment in Hebrew
            vm.getDayName = function(day) {
                date = dayToDate(day);
                return moment(date).format('dddd')
            }

            //todo fix make one function
            vm.getMonthTotal = function() {

                if (!vm.filter.user || !vm.clock || !Object.keys(vm.clock).length) return;

                var hours = [];
                var minutes = [];

                for (var date in vm.clock) {
                    var clockData = angular.copy(vm.clock[date]).sort(function(a, b) {
                        return a.jsDate - b.jsDate
                    });
                    // checking is amount of time is valid
                    let startCount = 0;
                    let endCount = 0 ;
                    clockData.forEach(( clock) => {
                        if ( clock.Status === '1') {
                            if ( clock.Act === '1' ){
                                endCount++
                            } else {
                                startCount++
                            }
                        }
                    });
                    if (startCount !== endCount)
                        return 'בדוק את הקלטים שהזנת'; //todo lang

                    // loop over all the times and find sequential times
                    for (let index = 0; index < clockData.length -1 ; index++) {
                        let entrenceTime
                        let exitTime;

                        // act start and status 1 (on)
                        if ( clockData[index].Act !== "0" || clockData[index].Status !== "1") {
                            continue;
                        }
                        entrenceTime = clockData[index].jsDate;
                        let findNext = index+1;

                        // act end and status 1 (on)
                        while (findNext != clockData.length) {
                            // valid data
                            if(clockData[findNext].Act === "1" && clockData[findNext].Status === "1"){
                                exitTime = clockData[findNext].jsDate;
                                break;
                            }
                            findNext++;
                        }
                        if (!exitTime) continue;
                        // calculate the diff time between start and end
                        let startTime = moment(entrenceTime.getHours() + ":" + entrenceTime.getMinutes(), 'hh:mm');
                        let endTime = moment(exitTime.getHours() + ":" + exitTime.getMinutes(), 'hh:mm');
                        let diff = moment.duration(endTime.diff(startTime));
                        hours.push(diff._data.hours);
                        minutes.push(diff._data.minutes);
                    }
                }
                minutes = minutes.reduce(function(total, num) {
                    return total + num
                });
                hours = hours.reduce(function(total, num) {
                    return total + num
                });

                if (minutes > 60) {
                    hours += Math.floor(minutes / 60);
                    minutes = minutes % 60;
                }
                return hours + '.' + minutes;

            }

            vm.getTotalTimeDay = function(day) {

                let date = dayToDate(day);
                // data not ready
                if (!vm.filter.user ||!vm.clock || !vm.clock[date]) return;

                let hours = [];
                let minutes = [];

                var clockData = angular.copy(vm.clock[date]).sort(function(a, b) {
                    return a.jsDate - b.jsDate
                });

                // checking is amount of time is valid
                let startCount = 0;
                let endCount = 0 ;
                clockData.forEach(( clock) => {
                    if ( clock.Status === '1') {
                        if ( clock.Act === '1' ){
                            endCount++
                        } else {
                            startCount++
                        }
                    }
                });
                if (startCount !== endCount)
                    return 'קלט לא תקין'; //todo lang

                // loop over all the times and find sequential times
                for (let index = 0; index < clockData.length -1 ; index++) {
                    let entrenceTime
                    let exitTime;

                    // act start and status 1 (on)
                    if ( clockData[index].Act !== "0" || clockData[index].Status !== "1") {
                        continue;
                    }
                    entrenceTime = clockData[index].jsDate;
                    let findNext = index+1;

                    // act end and status 1 (on)
                    while (findNext != clockData.length) {
                        // valid data
                        if(clockData[findNext].Act === "1" && clockData[findNext].Status === "1"){
                            exitTime = clockData[findNext].jsDate;
                            break;
                        }
                        findNext++;
                    }
                    if (!exitTime) continue;
                    // calculate the diff time between start and end
                    let startTime = moment(entrenceTime.getHours() + ":" + entrenceTime.getMinutes(), 'hh:mm');
                    let endTime = moment(exitTime.getHours() + ":" + exitTime.getMinutes(), 'hh:mm');
                    let diff = moment.duration(endTime.diff(startTime));
                    hours.push(diff._data.hours);
                    minutes.push(diff._data.minutes);
                }

                if (!hours.length && !minutes.length) return '';

                minutes = minutes.reduce(function(total, num) {
                    return total + num
                });
                hours = hours.reduce(function(total, num) {
                    return total + num
                });

                if (minutes > 60) {
                    hours += Math.floor(minutes / 60);
                    minutes = minutes % 60;
                }
                return hours + '.' + minutes;
            }

            // watch filters binding and load data from srv
            $scope.$watch('vm.filter', function(newVal, oldVal) {
                var month = newVal.month.value;
                var year = newVal.year.year;

                var userId = newVal.user && newVal.user.userId ? newVal.user.userId : undefined;
                if (!month || !year || !userId) return false;

                // reset clocks
                vm.clock = {};
                // console.log("get Times!!");
                $http.get('../rest/?type=report&method=clock&month=' + month + '&year=' + year +
                    '&userId=' + userId).then(function(res) {

                    var items = res.data.items || [];
                    // convert string type to date
                    for (var date in items) {
                        var dates = items[date] || [];
                        for (let index = 0; index < dates.length; index++) {
                            const date = dates[index];
                            if (date.jsDate) date.jsDate = new Date(parseInt(date.jsDate))
                        }
                    }
                    vm.clock = items;
                }, function(res) {})

            }, true)

        }])

    </script>

    <script>
        $(document).ready(function(){
            setTimeout(function() {
                $('#printcontent').DataTable( {
                    language: BeePOS.options.datatables,
                    responsive: true,
                    processing: true,
                    searching: false,
                    paging: false,
                    dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
                    buttons: [
                        <?php if (Auth::userCan('98')): ?>
                        {extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                            filename: '<?php echo lang('reports_time_clock') ?>',
                            className: 'btn d-inline btn-outline-info mr-2',exportOptions: {modifier: {
                                    page: 'current'
                                }}},
                        {extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_time_clock') ?>' , className: 'btn d-inline btn-outline-info mr-2',exportOptions: {}},
                        <?php endif ?>
                    ]
                })

                $('#toolbar').append($('.dt-buttons'))
                $('#printcontent_info').hide()
            }, 5000);

        })

    </script>

<?php 
require_once '../../app/views/footernew.php';

else: 
    ErrorPage (lang('permission_blocked'), lang('no_page_persmission'));
endif;
?>