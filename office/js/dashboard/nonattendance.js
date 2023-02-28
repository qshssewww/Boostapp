(function ($) {
    $(document).ready(function () {
        var dom = $('#nonattendance');
        var body = $('.card-body', dom);

        var tblData = {
            week: {
                hasFutureClass: [],
                noFutureClass: [],
                total: 0
            },
            month: {
                hasFutureClass: [],
                noFutureClass: [],
                total: 0
            }
        }

        // $.get('rest/', {type: 'report', method: 'nonattendance', filter: {futureClasses: false}, dashboard: 'true' }).done(function(data){
        $.get('rest/', { type: 'report', method: 'nonattendance', dashboard: 'true' }).done(function (data) {
            try {



                data = JSON.parse(data);

                var week = data.last7Days || [];
                var month = data.last30Days || [];
                data.items = data.items || [];


                for (let index = 0; index < data.items.length; index++) {
                    const el = data.items[index];

                    if (week.indexOf(el.rawLastClassDate) != -1) {
                        tblData.week[(el.futureClasses == '0') ? 'noFutureClass' : 'hasFutureClass'].push(el);
                    }

                    if (month.indexOf(el.rawLastClassDate) != -1) {
                        tblData.month[(el.futureClasses == '0') ? 'noFutureClass' : 'hasFutureClass'].push(el);
                    }

                }

                tblData.week.total = ([].concat(tblData.week.hasFutureClass, tblData.week.noFutureClass)).length
                tblData.month.total = ([].concat(tblData.month.hasFutureClass, tblData.month.noFutureClass)).length







                var weekBtn = $('.weekBtn', dom);
                var monthBtn = $('.monthBtn', dom);



                weekBtn
                    .on("click", function(){
                        body.html(generateData('week'));
                        weekBtn.attr('class', 'btn btn-primary btn-sm text-white weekBtn');
                        monthBtn.attr('class', 'btn btn-light btn-sm monthBtn');
                    })
                    .click().find('span.badge').html(tblData.week.total)

                monthBtn
                    .on("click", function(){
                        body.html(generateData('month'));
                        weekBtn.attr('class', 'btn btn-light btn-sm weekBtn');
                        monthBtn.attr('class', 'btn btn-primary btn-sm text-white monthBtn');
                    })
                    .find('span.badge').html(tblData.month.total)




            } catch (e) {
                console.log(e);
            }
        });

        function generateData(btnDate) {
            var html = '';

            html += '<div class="row">';
            html += '<div class="col-12 display-3">' + tblData[btnDate].total + '</div>';
            html += '<div class="col-12">';
            html += '<label>ללא הרשמה:</label> <span class="text-center text-danger">' + tblData[btnDate].noFutureClass.length + '</span> ';
            html += '<label>יש הרשמה:</label> <span class="text-center text-success">' + tblData[btnDate].hasFutureClass.length + '</span>';
            html += '</div>';
            html += '</div>';
            return html;
        }
    })
})(jQuery)