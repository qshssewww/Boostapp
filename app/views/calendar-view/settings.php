<!-- Calendar View Settings Module :: Begin -->

<div id="calendarViewSettings" class="bsapp-settings-dialog position-absolute dropdown d-flex">

    <button type="button" class="dropdown-toggle btn shadow-none p-0" data-toggle="dropdown">
        <i class="fal fa-cog fa-fw"></i>
    </button>
    <!-- Calendar View Settings Module :: Dropdown Content Begin -->
    <form class="dropdown-menu  w-100 border-0 m-0 rounded-lg shadow overflow-hidden p-0 animated fadeIn bsapp-max-h-500p"> 

        <button type="button" class="dropdown-toggle btn position-absolute shadow-none p-0 bsapp-fs-24 bsapp-lh-24 bsapp-z-9">
            <i class="fal fa-times"></i>
        </button>

        <!-- Calendar View Settings Module :: Panel begin -->
        <div class="bsapp-settings-panel main-settings-panel d-flex flex-column position-absolute h-100 w-100 bg-white p-15 overflow-hidden animated fadeIn" data-depth="0">
            <h5 class="d-flex text-black font-weight-bolder mb-15 mie-30 p-0">View Settings</h5>
            <!-- Start of Scrollable Area -->
            <div class="scrollable">
                <div>
                    <ul class="list-unstyled p-0">
                        <li class="mb-20">
                            <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-5">
                                <span class="flex-grow-1">Calendar View</span>
                                <div class="custom-control custom-switch mie-5">
                                    <input type="checkbox" class="custom-control-input" id="calendar-view-switch" checked>
                                    <label class="custom-control-label" for="calendar-view-switch" role="button"></label>
                                </div>
                            </h6>
                            <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">Text paragraph goes here.</p>
                        </li>
                        <li class="mb-20">
                            <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-5">
                                <span class="flex-grow-1">Event Owner or Event Location</span>
                                <div class="custom-control custom-switch mie-5">
                                    <input type="checkbox" class="custom-control-input" id="list-view-switch">
                                    <label class="custom-control-label" for="list-view-switch" role="button"></label>
                                </div>
                            </h6>
                            <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">Text paragraph goes here.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form> <!-- Dropdown Content End -->

</div>
