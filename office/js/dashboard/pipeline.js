(function($){
    $(document).ready(function(){
        var dom = $('#pipeline');
        var pipeLineToday = $('#pipeLineToday', dom);
        var pipeLineMonth = $('#pipeLineMonth', dom);
        var displayData = $('span.number', dom);

        var today = 0;
        var month = 0;

        pipeLineToday.on('click', function(e){
            displayData.html(today);
            pipeLineToday.addClass('btn-danger').removeClass('btn-light');
            pipeLineMonth.removeClass('btn-danger').addClass('btn-light');
        });

        pipeLineMonth.on('click', function(e){
            displayData.html(month);
            pipeLineMonth.addClass('btn-danger').removeClass('btn-light');
            pipeLineToday.removeClass('btn-danger').addClass('btn-light');
        });

        $.get('rest/', {type: 'report', method: 'pipeline'}).done(function(data){
            try{
                data = JSON.parse(data);
                today = data.items.filter(function(x){return (moment().format('YYYY-MM')).indexOf(x) != -1}).length;
                month = data.items.length;
                displayData.html(today);
            }catch(e){}
        })
    })
})(jQuery)