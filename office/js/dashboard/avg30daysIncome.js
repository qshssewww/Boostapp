(function($){
    $(document).ready(function(){
        var dom = $('#avg30daysIncome');

        var displayData = $('span.number', dom);

        $.get('rest/', {type: 'report', method: 'avgIncome'}).done(function(data){
            try{
                data = JSON.parse(data);
                var income = parseFloat(data.items[0].avgIncome ? data.items[0].avgIncome  : '0' );
                var html = '<span dir="ltr" class="text-'+(income >= 0 ? 'success': 'danger')+'">₪ '+income.toFixed(2)+'</span>'
                displayData.html(html);
            }catch(e){}
        })
    })
})(jQuery)