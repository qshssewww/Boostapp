<!-- modal start -->
<div class="popupWrapper" id="zoomPopup">
    <div class="popupContainer smPopup scaleUp">
        <!-- modal header start -->
        <div class="generalPopupHeader mb-4 mt-3">
            <h4 class="generalPopupTitle" >
                שיעור
                zoom
                <i class="fas fa-video"></i>
            </h4>
            <button id="closeZoom" type="button"  class="close newCalendarPopupCloseTimes toggleClosePopup" data-target="createNewClassType" data-parent="mainPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container p-0">
            <div class="row d-flex p-0 m-0 ">
                <div class="col-12 d-flex align-items-center mb-3 p-0">
                    <input class='cute-input' type="text" name="zoomMeetingId" id="zoomMeetingId" style='width:100%;' placeholder="Meeting ID">
                </div>
                <div class="col-12 d-flex align-items-center mb-3 p-0">
                    <input class='cute-input' type="text" name="zoomMeetingPassword" id="zoomMeetingPassword" style='width:100%;' placeholder="Meeting Password">
                </div>
            </div>

            <!-- <div class="row d-flex p-0 m-0 mb-5">
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <b>מצלמה</b>
                </div>
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <div class="d-flex justify-content-between" style="width:100%;">
                        <p class="p-0 m-0">המאמן</p>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center p-0">
                    <div class="d-flex justify-content-between" style="width:100%;">
                        <p class="p-0 m-0">המתאמנים</p>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>


            <div class="row d-flex p-0 m-0 mb-5">
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <b>סאונד</b>
                </div>
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <div class="d-flex justify-content-between" style="width:100%;">
                        <p class="p-0 m-0">המאמן</p>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center p-0">
                    <div class="d-flex justify-content-between" style="width:100%;">
                        <p class="p-0 m-0">המתאמנים</p>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row d-flex p-0 m-0">
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <div class="d-flex justify-content-between" style="width:100%;">
                        <b>הקלט שידור</b>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center mb-1 p-0">
                    <select name="zoomSaveTo" id="zoomSaveTo" class='cute-input ml-2'>
                        <option value="1">ספריה לאחסון</option>
                    </select>
                </div>
            </div> -->
            <!-- modal body end -->
            <!-- modal footer start  -->
            <div class="generalPopupFooter d-flex justify-content-end">
                <button id="cancelZoom" class="subCancel generalPopupButtonCancel toggleClosePopup" data-parent="mainPopup" data-target="createNewClassType">בטל</button>
                <button  id="saveZoom" class="subSave generalPopupButtonSave toggleClosePopup"  data-parent="mainPopup" data-target="createNewClassType">שמור</button>
            </div>
            <!-- modal footer end -->
        </div>
        <!-- modal content end -->
    </div>
</div>