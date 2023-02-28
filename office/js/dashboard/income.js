(function($, Chart, moment){

    var optiontop = {
        legend: {
            display: false,
            position: 'bottom'
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        responsive: true,
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: false,
                    labelString: 'חודש'
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'סך החשבוניות שהונפקו'
                }
            }]
        }
    };
    
    var datatop = {
        labels: [
            moment().subtract(3, "month").format('MMMM'),
            moment().subtract(2, "month").format('MMMM'),
            moment().subtract(1, "month").format('MMMM'),
            moment().format('MMMM')
        ],
        datasets: [{
                label: 'שנה נוכחית',
                fill: false,
                backgroundColor: [
                    '#48AD42',
                    '#48AD42',
                    '#48AD42',
                    '#48AD42',
                ],
                borderColor: [
                    '#48AD42',
                    '#48AD42',
                    '#48AD42',
                    '#48AD42',
                ],
                data: [0, 0, 0, 0],
            }
        ]
    };


    $(document).ready(function(){
        var ctxtop = document.getElementById("myChartTop");
        var backgroundColor = '#48AD42';
        var borderColor = '#48AD42';

        $.ajax({
            url: 'REST',
            data: {type: 'report', method: 'quarter'},
            dataType: "json"
          }).done(function(data){

 
            datatop.labels = [];
            datatop.datasets[0].data = [];
            datatop.backgroundColor = []
            datatop.borderColor = []

            for(var i=0; i < 3;i++){
                var item = data.items[i] || {};
                datatop.labels[i] = moment(item.date || moment().subtract(i, "month").format('YYYY-MM'), 'YYYY-MM').format('MMMM');
                datatop.backgroundColor[i] = backgroundColor;
                datatop.borderColor[i] = borderColor;
                datatop.datasets[0].data[i] = parseFloat(item.total || 0)
            }

            myPieChartTop = new Chart(ctxtop, {
                type: 'bar',
                data: datatop,
                options: optiontop
            });
        })


    });
})(jQuery, Chart, moment)