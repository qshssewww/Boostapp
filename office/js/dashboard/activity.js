(function ($, chartData) {
    $(document).ready(function(){
        var ctx = document.getElementById("myChart");

        var data = {
            labels: ["ניצול תופסה", "אי ניצול"],
            datasets: [{
                backgroundColor: [
                    '#00c736',
                    '#FF003B'
                ],
                hoverBackgroundColor: [
                    '#218838',
                    '#c82333'
                ],
                data: chartData.activity.data, // [<?php echo @$FixTotalClassRegister; ?>, <?php echo @$FixTotalClassMax; ?>]
            }]
        };
    
        var option = {
            legend: {
                display: true,
                position: 'right'
            },
            responsive: true,
        };
    
    
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: option
        });
    })
})(jQuery, chartData)