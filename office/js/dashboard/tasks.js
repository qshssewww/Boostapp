(function($){
    $(document).ready(function(){
        var dom = $('#tasks');
        var content = $('#taskContent', dom);
        var lateTask = $('#lateTask', dom);
        var taskToday = $('#taskToday', dom);
        
        var template = '<div class="alertb alert-{{status}} text-right">';
            template += '<small>{{user}}';
            template += '<a href="javascript:void(0);" onclick="NewCal(\'{{calanderId}}\',\'{{clientId}}\')" class="text-dark">{{title}}<a>';
            template += '</small><br>';
            template += '<small>עדיפות: {{level}}, מוגדר ל- {{time}}</small>';
            template += '</div>{{hr}}';

        var templateUser = '<a href="ClientProfile.php?u={{clientId}}" class="text-dark">{{fullName}}</a> :: ';


        var todo = [];
        var late = [];

        lateTask.on('click', function(){
            taskToday.removeClass('btn-success').addClass('btn-light');
            lateTask.addClass('btn-danger').removeClass('btn-light');
            content.html(generate(late));
        })

        taskToday.on('click', function(){
            lateTask.removeClass('btn-danger').addClass('btn-light');
            taskToday.addClass('btn-success').removeClass('btn-light');
            content.html(generate(todo));
        })

        function generate(items){
            var response = '';
            for(var i=0; i < items.length; i++){
                response += template
                            .replace('{{status}}', items[i].calStatus)
                            .replace('{{user}}', items[i].clientId?templateUser.replace('{{clientId}}', items[i].clientId).replace('{{fullName}}', items[i].fullName): '')
                            .replace('{{calanderId}}', items[i].calendarId)
                            .replace('{{clientId}}', items[i].clientId || '')
                            .replace('{{title}}', items[i].title)
                            .replace('{{level}}', items[i].calLevel)
                            .replace('{{time}}', items[i].deadLine)
                            .replace('{{hr}}', i+1 < items.length ? '<hr>': '')
            }
            return response;
        }

        $.get('rest/', {type: 'report', method: 'tasks'}).done(function(data){
            try{
                data = JSON.parse(data);
                // data.items = [data.items].concat(data.items).concat(data.items).concat(data.items).concat(data.items);
                todo = data.items.filter(function(x){return x.calStatus == 'success'});
                late = data.items.filter(function(x){return x.calStatus == 'danger'});
                
                taskToday.trigger('click');

            }catch(e){}
        })
    })
})(jQuery)