(function($){
    $(document).ready(function(){
        var dom = $('#overBookReport');

        var tblData = {
            today: [],
            tommorow: []
        }

        $.get('rest/', {type: 'report', method: 'overbooked'}).done(function(data){
            try{


                
                data = JSON.parse(data);

                var today = data.today;
                var tommorow = data.tommorow;

                data.items = data.items || [];
                for (let index = 0; index < data.items.length; index++) {
                    const el = data.items[index];
                    
                    if(el.date == today){
                        tblData.today.push(el);
                        continue;
                    }
                    
                    if(el.date == tommorow){
                        tblData.tommorow.push(el);
                        continue;
                    }
                    
                }

                var todayBtn =  $('.menuBtnDays .todayBtn', dom);
                var tommorowBtn =  $('.menuBtnDays .tommorowBtn', dom);

                todayBtn
                    .on("click", function(){
                        $('.DivScroll', dom).html(generateTable('today', today));
                        todayBtn.attr('class', 'btn btn-primary btn-sm text-white todayBtn');
                        tommorowBtn.attr('class', 'btn btn-light btn-sm tommorowBtn');
                    })
                    .click()
                    .find('span')
                    .html(" "+tblData.today.length+"");
                    tommorowBtn
                    .on("click", function(){
                        $('.DivScroll', dom).html(generateTable('tommorow', tommorow));
                        todayBtn.attr('class', 'btn btn-light btn-sm todayBtn');
                        tommorowBtn.attr('class', 'btn btn-primary btn-sm text-white tommorowBtn');
                    })
                    .find('span')
                    .html(" "+tblData.tommorow.length+"")
                // $('.DivScroll', dom).html(generateTable('today'));

            }catch(e){
                console.log(e);
            }
        });

        function generateTable(day, date){
            
            if(!tblData || !tblData[day] || !tblData[day].length){
                return '<div class="text-center">אין שיעורים עם רשימת המתנה</div>';
            }

            var html  = "<table class='table border-0 table-sm table-responsive-sm text-right'>";
            for(var i =0; i < tblData[day].length; i++){
                html += "<tr>";
                    html += "<td class=\"border-0\"><a href=\"reports/overbooked.php?classId="+tblData[day][i].classDateId+"\">" 
                    +(tblData[day][i].overBooked)+"<a/></td>";
                    html += "<td class=\"border-0\" title=\"שם שיעור\">"+(tblData[day][i].className)+"</td>";
                    html += "<td class=\"border-0\" title=\"מיקום שיעור\">"+(tblData[day][i].classRoomName)+"</td>";
                    html += "<td class=\"border-0\" title=\"שעת שיעור\">"+(tblData[day][i].classTime)+"</td>";
                    html += "<td class=\"border-0\" title=\"מדריך\">"+((tblData[day][i].guideName))+"</td>";
                html += "</tr>";
            }

            html += "</table>";
            return html;        
        }
    })
})(jQuery)