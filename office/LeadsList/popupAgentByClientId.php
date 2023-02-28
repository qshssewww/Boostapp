<div class="ip-modal" id="AgentClientPush" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-hidden="true" >
		<div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title">שייך לנציג מטפל</h4>
                <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                
				</div>
				<div class="ip-modal-body" >
                <form action="AgentClientPushReport"  class="ajax-form clearfix" autocomplete="off">
                <input type="hidden" name="me" value="1">
                <input type="hidden" name="_token" id="csrf-token" value="<?php echo Session::token() ?>" />

                <input type="hidden" name="newclientsIds">
                <div id="newclientsNames"></div>
                 
                <div class="form-group" >
                <label>בחר נציג מטפל</label>
                <select class="form-control" name="Type" required>
                <option selected>בחר</option> 
                <option value="0">ללא נציג מטפל</option>     
                <?php 
                $Pipes = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->where('status', '=', '1')->get();
                foreach ($Pipes as $Pipe) {                    
                ?>
                <option value="<?php echo $Pipe->id; ?>"><?php echo $Pipe->display_name; ?></option>                                        
                <?php } ?>  
                </select>
                </div>   

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary text-white">שלח</button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>

<style>
    span.badge a.remove{cursor: pointer;}
    #AgentClientPush #newclientsNames #newclientList{max-height: 4.5em; overflow-y: scroll; overflow:auto;}
</style>
<script>
    (function($){

        // a hack to deselct all the checkboxes on ajax request callback
        // workaround fro boostapp
        $(document).on('xhr.dt', 'table', function(){
            var tbl = $(this);
            if(!tbl || !tbl.DataTable || !$(this).DataTable().column || !$(this).DataTable().column(0) || !$(this).DataTable().column(0).checkboxes) return;
            tbl.DataTable().column(0).checkboxes.deselectAll();
        });

        $(document).ready(function(){
            var SendClientPush =  $('#AgentClientPush');
            var clients = $('[name="newclientsIds"]', SendClientPush);
            SendClientPush.on('shown.bs.modal', function() {
                var el = $(this);
                // wysiwyg textarea editor


                var ids = clients.val().split(",");
                var clientsArea = $('#newclientsNames', el).html('');
                var html = '<div><label>לקוחות (<span id="clientCount">'+ids.length+'</span>)</label></div><div id="newclientList">';
                

                
                ids.map(function(x){
                    html += '<span class="badge badge-primary text-white mr-1"><span><i class="fas fa-spinner fa-spin"></i>&nbsp;&nbsp;</span><a data-id="'+x+'" class="text-white remove" title="הסר לקוח">x</a></span>'
                });

                
                $.get('<?php echo get_loginboostapp_domain() ?>/api/client/all?'+ids.map(function(x){return 'clientsId[]='+x}).join('&'), function(data){
                    var html = '<div><label>לקוחות (<span id="clientCount">'+ids.length+'</span>)</label></div><div id="newclientList">';
                    data.items.map(function(x){
                        html += '<span class="badge badge-primary text-white mr-1" title="'+x.clientPhone+'"><span>'+x.clientFullName+'&nbsp;&nbsp;</span><a data-id="'+x.clientId+'" class="text-white remove" title="הסר לקוח">x</a></span>'
                    });
                    clientsArea.html(html+"</div>");
                })
                clientsArea.html(html+"</div>");

            }).on('click', 'span.badge a.remove', function(){
                var id = $(this).data('id');
                var countsClient = clients.val().split(",").filter(function(x){return x.toString() != id.toString()});
                clients.val(countsClient.join(","));
                $('span#clientCount', SendClientPush).html(countsClient.length);

                $(this).parent().remove();
                if(!clients.val()){
                    SendClientPush.modal('toggle');
                    alert('נא בחר לקוח אחד לשיוך לנציג')
                }
            });


        })
    })(jQuery);


    
</script>   

<script>
	(function($){
		$.ajaxSetup({
			beforeSend: function(xhr, settings){
				if(settings && settings.url && settings.url.match(/api\.boostapp\.co\.il/)){
					for(var key in $.ajaxSettings.headers){
						xhr.setRequestHeader(key, null)
					}
					xhr.setRequestHeader('x-cookie', document.cookie)
					xhr.setRequestHeader('Content-Type', 'application/json')
				}
			}
		});
	})(jQuery);
</script>
