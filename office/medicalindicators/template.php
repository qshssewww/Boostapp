<div class="row pb-3">
    <!-- date, time & weather -->
    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <?php echo $DateTitleHeader; ?>
        </h3>
    </div>
    <!-- section title -->
    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
                <i class="fas fa-user-md"></i> מדדים רפואים      
            </div>
        </h3>
    </div>
</div>

<!-- breadcrumb -->
<nav aria-label="breadcrumb" dir="rtl">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/index.php" class="text-dark">ראשי</a>
        </li>
        <li class="breadcrumb-item"><a href="./index.php" class="text-dark">מדדים</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="./?page=<?php echo $page ?>"><?php echo $breadcrumb; ?></a></li>
    </ol>
</nav>

<div class="row"  dir="rtl">
    <!-- side nav -->
    <div class="col-md-2"><?php include("./sidenav.php.html"); ?></div>
    <!-- content -->
    <div class="col-md-10 text-right"><?php include($page); ?></div>
</div>