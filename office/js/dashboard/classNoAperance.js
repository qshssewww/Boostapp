(function($){
    $(document).ready(function(){
        var dom = $('#classNoAperance');
        $.get('rest/', {type: 'report', method: 'classNoAperance'}).done(function(data){
            try{
                data = JSON.parse(data);
                $('.number', dom).html(data.items.length)
            }catch(e){}
        })
    })
})(jQuery)