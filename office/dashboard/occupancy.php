<div class="card-header text-right">
    <strong class="text-secondary">
        <i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i>
        <span>אחוז תפוסת שיעורים בסטודיו</span>
    </strong>
</div>

<div class="card-body text-center" style="min-height:290px; max-height:290px;">
    <canvas id="occupancyChart"></canvas>
    <!-- progress bar -->
    <div class="row align-items-center mt-4 pt-2">
        <div class="col-sm order-md-1 text-right">
            <span class="text-center text-secondary">
                <small class="font-weight-bold">ר.המתנה</small>
            </span>
        </div>
        <div class="col-sm order-md-2">
            <div class="progress">
                <div class="progress-bar bg-warning watingbar" role="progressbar" style="width: 25%" aria-valuenow="50" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
        </div>

        <div class="col-sm order-md-3">
            <span class="text-center text-secondary">
                <small class="font-weight-bold progressBarText">0%</small>
            </span>
        </div>
    </div>

</div>


<div class="card-body text-center">

    <a href="javascript:void(0);" class="btn btn-success btn-sm" id="occupancyWeek">שבוע</a>
    <a href="javascript:void(0);" class="btn btn-light btn-sm" id="occupancyMonth">חודש</a>
    <a href="javascript:void(0);" class="btn btn-light btn-sm" id="occupancy3Months">3 חודשים</a>

</div>



<span class="text-center text-secondary">
    <small class="font-weight-bold">
        <a href="reports/occupancy.php" class="text-secondary">צפייה בדוח &gt;&gt;</a>
    </small>

</span>