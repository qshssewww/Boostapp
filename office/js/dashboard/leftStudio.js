(function($){
    $(document).ready(function(){
        var dom = $('#leftStudio');
        $.get('rest/', {type: 'report', method: 'leftStudio'}).done(function(data){
            try{
                data = JSON.parse(data);
                $('.card-body .number', dom).html(data.items.length)
            }catch(e){}
        })
    })
})(jQuery)