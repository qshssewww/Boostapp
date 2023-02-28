(function($){
    $(document).ready(function(){
        var dom = $('#registeredVisitors');
        $.get('rest/', {type: 'report', method: 'registeredVisitors'}).done(function(data){
            try{
                data = JSON.parse(data);
                $('.number', dom).html(data.items.length)
            }catch(e){}
        })
    })
})(jQuery)