(function($){
    $(document).ready(function(){
        var dom = $('#skipClass');

        var tblData = {
            today: [],
            yestarday: []
        }

        $.get('rest/', {type: 'report', method: 'classNoAperance'}).done(function(data){
            try{


                
                data = JSON.parse(data);

                var today = data.today;
                var yestarday = data.yestarday;

                data.items = data.items || [];
                for (let index = 0; index < data.items.length; index++) {
                    const el = data.items[index];
                    
                    if(el.date == today){
                        tblData.today.push(el);
                        continue;
                    }
                    
                    if(el.date == yestarday){
                        tblData.yestarday.push(el);
                        continue;
                    }
                    
                }

                var todayBtn =  $('.menuBtnDays .todayBtn', dom);
                var yestardayBtn =  $('.menuBtnDays .yestardayBtn', dom);

                todayBtn
                    .on("click", function(){
                        $('.DivScroll', dom).html(generateTable('today', today));
                        todayBtn.attr('class', 'btn btn-primary btn-sm text-white todayBtn');
                        yestardayBtn.attr('class', 'btn btn-light btn-sm tommorowBtn');
                    })
                    .click()
                    .find('span')
                    .html(" "+tblData.today.length+"");

                yestardayBtn
                    .on("click", function(){
                        $('.DivScroll', dom).html(generateTable('yestarday', yestarday));
                        todayBtn.attr('class', 'btn btn-light btn-sm todayBtn');
                        yestardayBtn.attr('class', 'btn btn-primary btn-sm text-white tommorowBtn');
                    })
                    .find('span')
                    .html(" "+tblData.yestarday.length+"")
                // $('.DivScroll', dom).html(generateTable('today'));

            }catch(e){
                console.log(e);
            }
        });

        function generateTable(day, date){
            
            if(!tblData || !tblData[day] || !tblData[day].length){
                return '<div class="text-center">אין אי הגעות לשיעור</div>';
            }



            var html = '';
            /*
            // group by class id to sum total no show up to class
            var groupByClassId = {};
            for(var i =0; i < tblData[day].length; i++){
                groupByClassId[tblData[day][i].classDateId] = groupByClassId[tblData[day][i].classDateId] || {count: 0, className: tblData[day][i].className, classRoomName: tblData[day][i].classRoomName,classTime: tblData[day][i].classTime, guideName:tblData[day][i].guideName, data: [] }
                groupByClassId[tblData[day][i].classDateId].count++;
                groupByClassId[tblData[day][i].classDateId].data.push(tblData[day][i]);
            }

            html  = "<table class='table border-0 table-sm table-responsive-sm'>";
            for(var i in groupByClassId){
                html += "<tr>";
                    // onclick toggle hidden attr for boostrap 4
                    html += "<td class=\"border-0\"><i class=\"fa fa-eye\" onclick=\"jQuery(this).closest('tr').next().attr('hidden', !jQuery(this).closest('tr').next().attr('hidden'));\"></i></td>";
                    html += "<td class=\"border-0\">"+(groupByClassId[i].count)+"</td>";
                    html += "<td class=\"border-0\">"+(groupByClassId[i].className)+"</td>";
                    html += "<td class=\"border-0\">"+(groupByClassId[i].classRoomName)+"</td>";
                    html += "<td class=\"border-0\">"+(groupByClassId[i].classTime)+"</td>";
                    html += "<td class=\"border-0\">"+((groupByClassId[i].guideName))+"</td>";
                html += "</tr>";
                html += "<tr hidden>";
                html += "<td colspan=\"6\" class=\"\">";
                html += "<table class='table border-0 table-sm table-responsive-sm'>";
                for (let index = 0; index < groupByClassId[i].data.length; index++) {
                    const client = groupByClassId[i].data[index];
                    html += "<tr>";
                        html += "<td class=\"border-0\">"+(client.fullName)+"</td>";  
                        html += "<td class=\"border-0\">"+(client.email)+"</td>";  
                        html += "<td class=\"border-0\">"+'<a href="tel:+'+client.phone.replace('0', '972').replace(/\D/g,'')+'">'+client.phone+'</a>'+"</td>";  
                    html += "</tr>";
                }
                html += "</table>";
                html += "</td>";
            }
            html += "</table>";
            */
            
            // tbl to show all no show up
            html  += "<table class='table border-0 table-sm table-responsive-sm text-right'>";
            for(var i =0; i < tblData[day].length; i++){
                html += "<tr>";
                    // html += "<td class=\"border-0\">"+(tblData[day][i].classDateId)+"</td>";
                    html += "<td class=\"border-0\" title=\"שם\">"+((tblData[day][i].fullName))+"</td>";
                    html += "<td class=\"border-0\" title=\"נייד\">"+'<a href="tel:+'+tblData[day][i].phone.replace('0', '972').replace(/\D/g,'')+'">'+tblData[day][i].phone+'</a>'+"</td>";
                    html += "<td class=\"border-0\" title=\"מייל\">"+((tblData[day][i].email))+"</td>";
                    html += "<td class=\"border-0\" title=\"שם שיעור\">"+(tblData[day][i].className)+"</td>";
                    html += "<td class=\"border-0\" title=\"מיקום שיעור\">"+(tblData[day][i].classRoomName)+"</td>";
                    html += "<td class=\"border-0\" title=\"שעת שיעור\">"+(tblData[day][i].classTime)+"</td>";
                    html += "<td class=\"border-0\" title=\"שם מדריך\">"+((tblData[day][i].guideName))+"</td>";
                            
                html += "</tr>";
            }

            html += "</table>";
            return html;        
        }
    })
})(jQuery)