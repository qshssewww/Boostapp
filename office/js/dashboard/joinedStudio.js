(function($){
    $(document).ready(function(){
        var dom = $('#joinedStudio');
        $.get('rest/', {type: 'report', method: 'joinStudio', length: 1}).done(function(data){

            try{
                data = JSON.parse(data);
                var joined = 0, joinedMonth;

                if(data.items && data.items.length){
                    totalClients = parseInt(data.items[0].totalJoined);
                    joinedMonth = parseInt(data.items[0].joinedClientLast30days);
                }
                


                $.get('rest/', {type: 'report', method: 'leftStudio'}).done(function(data){
                    try{
                        data = JSON.parse(data);
                        var html = 'הצטרפו החודש: '+joinedMonth;
                            html += '<br>עזבו החודש: '+data.items.length;
                            html+= '<br> הצטרפו כללי: '+joined;

                        var el = $('.card-body .number', dom);
                        // el.html(html);


                        var c = $('<canvas />');

                        var ctx = c[0].getContext('2d');

                        var left = data.items.length;
                        var leftPercent = Math.round( 100-(((totalClients - left)/totalClients )*100) );
                        var newClientsPercent = Math.round(100-(((totalClients-joinedMonth)/totalClients)*100));

                        var barChartData = {
                            // labels: ['הצטרפו החודש', 'הצטרפו כללי', 'עזבו החודש'],
                            datasets: [
                                {
                                    label: 'הצטרפו כללי',
                                    backgroundColor: 'lightgreen',
                                    data: [100]
                                },{
                                label: 'הצטרפו החודש',
                                backgroundColor: 'lime',
                                data: [newClientsPercent]
                            },{
                                label: 'עזבו החודש',
                                backgroundColor: 'pink',
                                data: [leftPercent]
                            }]
                        }
                        var config = {
                            type: 'bar',
                            data: barChartData,
                            options: {
                                responsive: true,
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false,
                                    text: 'Chart.js Bar Chart'
                                },
                                tooltips:{
                                    callbacks: {
                                        label: function(tooltipItem, d) {
                                            var el = d.datasets[tooltipItem.datasetIndex];
                                            var label = el.label || '';

                                            var percent = el.data[0];
                                            var num = 0;
                                            switch(tooltipItem.datasetIndex){
                                                case 0: num = totalClients; break;
                                                case 1: num = joinedMonth; break;
                                                case 2: num = data.items.length; break;
                                            }

                                            label += ': ' + num + ' לקוחות ('+percent+'%)';

                                            return label
                                        }
                                    }
                                }
                            }
                            
                        }


                        new Chart(ctx, config);
                       

                        el.after(c);




                    }catch(e){
                        console.log(e);
                    }
                })


                
            }catch(e){}


            



        })
    })
})(jQuery)