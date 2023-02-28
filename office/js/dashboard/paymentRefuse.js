(function($){
    $(document).ready(function(){
        var dom = $('#paymentRefuse');
        $.get('rest/', {type: 'report', method: 'paymentRefuse'}).done(function(data){
            try{
                data = JSON.parse(data);
                $('.number', dom).html(data.items.length)
            }catch(e){}
        })
    })
})(jQuery)