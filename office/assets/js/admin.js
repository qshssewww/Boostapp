jQuery(function ($) {   
    function trans (m) { 
        return Beesoft.trans(m);
    }

    function profileUrl (id, name) { 
        return '<a href="'+Beesoft.options.baseUrl+'/profile.php?u='+id+'" target="_blank">'+name+'</a>';
    }

    var usersDataTable, messagesDataTable, commentsDataTable;

    // Users DataTable
    Beesoft.admin.usersDT = function () {
        
        // Apply the DataTable plugin for the #users table
        usersDataTable = $('#users').DataTable({
			serverSide: true,
    		ajax: {
    			url: Beesoft.options.ajaxUrl,
    			data: {action: 'getUsers'}
    		},
			language: Beesoft.options.datatables,
			order: [[4, 'desc']],
			columns: [
				{
                    orderable: false,
                    searchable: false,
                    render: function (d) {
                        // Render checkbox for selecting the user
                        return '<input type="checkbox" name="users[]" value="'+d+'" class="chb-select">';
                    }
                },
				{
                    render: function (d, t, r) { return profileUrl(r[0], d) }
                }, 
                null, null, null,
			    {
                    searchable: false, 
                    render: function (d) {
                        // Render the label for the user status
                        switch (d) {
                            case '1': return '<span class="label label-success">'+trans('activated')+'</span>'; break;
                            case '2': return '<span class="label label-danger">'+trans('suspended')+'</span>'; break;
                            default:  return '<span class="label label-warning">'+trans('unactivated')+'</span>';
                        }
                    }
                },
			    null,
			    {
                    searchable: false, 
                    orderable: false, 
                    render: function (d, t, r) {
                        // Render the actions buttons
                        return '<a href="?page=user-edit&id='+r[0]+'" title="'+trans('edit_user')+'"><span class="glyphicon glyphicon-edit"></span></a> '+
                        '<a href="?page=message-reply&id='+r[0]+'" title="'+trans('send_message')+'"><span class="glyphicon glyphicon-share-alt"></span></a> '+
                        '<a href="javascript:Beesoft.admin.composeEmail(\''+r[2]+'\')" title="'+trans('send_email')+'"><span class="glyphicon glyphicon-envelope"></span></a> '+
                        '<a href="javascript:Beesoft.admin.deleteUser('+r[0]+', \''+(r[1]||r[2])+'\')" title="'+trans('delete_user')+'"><span class="glyphicon glyphicon-trash"></span></a>';
                    }
                }
			]
		});

        /*
        // Search only if the user enters 3 or more characters
        $('#users_filter input').off().on('input', function (e) {
            var value = $.trim($(this).val());
            if (value.length >= 3) usersDataTable.search(value).draw();
            if (value == '')  usersDataTable.search('').draw();
        });
        */

        // Add "Delete" button for deleting multiple users
		$('#users_length').before('<button type="submit" class="btn btn-danger btn-sm delete-bulk" disabled>'+trans('delete')+'</button>');

        var $form = $('#users_form');

        // Delete users when clicking on the "Delete" button
        $form.on('submit', function (e) {
            e.preventDefault();
            
            var length = $(e.currentTarget).find('.chb-select:checked').length;
            
            if (!length) return;

            var modal = $('#deleteUsersModal');
            modal.find('input[name="users"]').val($form.serialize());
            modal.find('.users').text(length);
            modal.modal('show');

            // Register ajax form callback
            Beesoft.ajaxFormCb.deleteUsers = function () {
                usersDataTable.draw();
                modal.modal('hide');
            };
        });

        // Filter users by the role when clicking on a role
        $form.find('.role-filter li').on('click', 'a', function (e) {
            e.preventDefault();

            if ($(e.delegateTarget).hasClass('active')) return;

            usersDataTable.column(6).search( $(this).attr('data-role') ).draw();
            
            $(e.delegateTarget).parent().find('li.active').removeClass('active');
            $(e.delegateTarget).addClass('active');
        });
	};

	// Delete User
	Beesoft.admin.deleteUser = function (userId, username) {
		var modal = $('#deleteUserModal');
        modal.find('input[name="user_id"]').val(userId);
        modal.find('.user').text(username);
        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.deleteUser = function () {
            usersDataTable.draw();
            modal.modal('hide');
        };
	};


     // Messages DataTable
    Beesoft.admin.messagesDT = function () {
        
        // Apply the DataTable plugin for the #users table
        messagesDataTable = $('#messages').DataTable({
            serverSide: true,
            ajax: {
                url: Beesoft.options.ajaxUrl,
                data: {action: 'getMessages'}
            },
            language: Beesoft.options.datatables,
            searching: false,
            ordering: false, 
            order: [[3, 'desc']],
            columns: [
                {
                    render: function (d, t, r) {
                        return '<input type="checkbox" name="messages[]" value="'+r[5]+'" class="chb-select">';
                    }
                },
                {
                    render: function(d, t, r) {
                        return (r[6] ? '<span class="replied"></span>' : '') + '<a href="?page=message-reply&id='+r[5]+'" title="'+trans('reply')+'">'+d+'</a>';
                    }
                },
                {
                    render: function (d, t, r) { return profileUrl(r[5], d) }
                },
                null,
                {
                   render: function (d, t, r) {
                        return '<a href="?page=message-reply&id='+r[5]+'" title="'+trans('reply')+'"><span class="glyphicon glyphicon-share-alt"></span></a> '+
                        '<a href="javascript:Beesoft.admin.deleteConversation('+r[5]+', \''+r[2]+'\')" title="'+trans('delete_conversation')+'"><span class="glyphicon glyphicon-trash"></span></a>';
                    }
                }
            ],
            createdRow: function(r, d) {
                if (!d[6] && !d[4]) $(r).addClass('info');
            }
        });

        // Add "Delete" button for deleting multiple messages
        $('#messages_length').before('<button type="submit" class="btn btn-danger btn-sm delete-bulk" disabled>'+trans('delete')+'</button>');

        $('#messages_form').on('submit', function (e) {
            e.preventDefault();
            
            var length = $(e.currentTarget).find('.chb-select:checked').length;
            
            if (!length) return;

            var modal = $('#deleteConversationsModal');
            modal.find('input[name="conversations"]').val($(this).serialize());
            modal.find('.conversations').text(length);
            modal.modal('show');

            Beesoft.ajaxFormCb.deleteConversations = function () {
                messagesDataTable.draw();
                modal.modal('hide');
            };
        });
    };

    Beesoft.admin.deleteConversation = function (id, username) {
        var modal = $('#deleteConversationModal');
        modal.find('input[name="user_id"]').val(id);
        modal.find('.user').text(username);
        modal.modal('show');

        Beesoft.ajaxFormCb.deleteConversation = function () {
            messagesDataTable.draw();
            modal.modal('hide');
        };
    };

    // Compose E-mail
    Beesoft.admin.composeEmail = function (email) {
        var modal = $('#composeModal');

        if (email) modal.find('input[name="to"]').val(email);

        modal.modal('show');

        // Register ajax form callback
        Beesoft.ajaxFormCb.sendEmail = function () {
            modal.modal('hide');
        };
    };

    // Delete Field | Add New Field
    Beesoft.admin.fields = function() {
        $('.fields').on('click', '.delete-btn', function() {
            $(this).closest('.row').remove()
        });
        
        $('.fields').on('click', '.last input', function(e) {
            var clone = $(this).closest('.row').clone();
            $(this).closest('.row').removeClass('last');
            $(e.delegateTarget).append(clone);
        });
    };

    // Search contact
    Beesoft.admin.searchContact = function() {
        var div = $('.search-user'),
            list = div.find('.list-group');

        div.on('input', '.search', function (e) {
            var value = $.trim( $(e.currentTarget).val() );

            div.find('[name="user"]').val('');

            if (value.length < 2) return list.html('');
            if (value == $(e.currentTarget).data('last-value')) return;
            
            $.get(Beesoft.options.ajaxUrl, {action: 'searchContact', admin: true, user: value}, function (response) {
                $(e.currentTarget).data('last-value', value);

                list.html('');

                if (!response.success) return;

                for (var i = 0; i < response.message.length; i++) {
                    list.append( tmpl('userSearchTemplate', response.message[i]) );
                }
            }, 'json');
        });

        list.on('click', '.list-group-item', function (e) {
            e.preventDefault();
            div.find('[name="user"]').val( $(this).attr('data-conversation-id') );
            div.find('[name="to"]').val( $(this).find('.fullname').text() );
            list.html('');
        });
    };

    Beesoft.admin.commentsDT = function () {
        // Apply the DataTable plugin for the #comments table
        commentsDataTable = $('#comments').DataTable({
            serverSide: true,
            ajax: {
                url: Beesoft.options.ajaxUrl,
                data: {action: 'get_comments_admin'}
            },
            language: Beesoft.options.datatables,
            autoWidth: false,
            order: [[3, 'desc']],
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return '<input type="checkbox" name="comments[]" value="'+data+'" class="chb-select">';
                    }
                },
                {
                    orderable: false,
                    render: function (data, type, row) { 
                        return profileUrl(data, row[6]);
                    }
                },
                {
                    orderable: false,
                    render: function (data) {
                        return data.replace(/&amp;/g, '&');
                    }
                }, 
                {
                    searchable: false,
                    render: function (data, type, row) {
                        return row[5] ? '<a href="'+row[5]+'#comment-'+row[0]+'" target="_blank">'+data+'</a>' : data;
                    }
                },
                {
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return row[4] ? '<a href="#"><span class="badge">'+row[10]+'</span></a> '+
                        '<a href="'+row[5]+'" target="_blank" title="'+trans('view_page')+'">'+row[4]+'</a>' : '';
                    }
                },
                {   
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        var html = '',
                            id = row[0],
                            status = $('.status-filter .active a').attr('data-status');

                        if (row[9] === '0') {
                            html += '<a href="javascript:Beesoft.admin.commentAction('+id+', \'approve\')" title="'+trans('approve_comment')+'"><span class="glyphicon glyphicon-ok"></span></a> ';
                        }
                        if (row[9] === '1') {
                            html += '<a href="javascript:Beesoft.admin.commentAction('+id+', \'unapprove\')" title="'+trans('unapprove_comment')+'"><span class="glyphicon glyphicon-remove"></span></a> ';
                        }
                        if (status !=  '2') {
                            html += '<a href="javascript:Beesoft.admin.commentReply('+id+', \''+row[6]+'\')" title="'+trans('reply_comment')+'"><span class="glyphicon glyphicon-share-alt"></span></a> ';
                        }
                        if (status !=  '2') {
                            html += '<a href="?page=comment-edit&id='+id+'" title="'+trans('edit_comment')+'"><span class="glyphicon glyphicon-edit"></span></a> ';
                        }
                        if (status === '2') {
                            html += '<a href="javascript:Beesoft.admin.commentAction('+id+', \'approve\')" title="'+trans('restore_comment')+'"><span class="glyphicon glyphicon-ok"></span></a> ';
                        }
                        if (status !=  '2' ) {
                            html += '<a href="javascript:Beesoft.admin.commentAction('+id+', \'trash\')" title="'+trans('trash_comment')+'"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        if (status === '2') {
                            html += '<a href="javascript:Beesoft.admin.commentAction('+id+', \'delete\')" title="'+trans('delete_comment')+'"><span class="glyphicon glyphicon-trash"></span></a>';
                        }

                        return html;
                    }
                },
                {
                    orderable: false,
                    visible: false
                },
                {
                    orderable: false,
                    visible: false
                },
                {
                    orderable: false,
                    visible: false
                },
                {
                    orderable: false,
                    visible: false
                },
            ],
            createdRow: function (row, data) {
                if (data[9] === '0') {
                    $(row).addClass('warning');
                }
            }
        });

        commentsDataTable.on('draw', function () {
            var status = $('.status-filter .active a').attr('data-status');

            $('.bulk-action').remove();
            
            // Add "Bulk Actions" button
            $('#comments_length').before(
                '<div class="bulk-action"><select name="bulk_action" class="form-control input-sm">'+
                        '<option value="" selected="selected">'+trans('bulk_actions')+'</option>'+
                        (status !=  '0' && status != '2' ? '<option value="unapprove">'+trans('unapprove')+'</option>' : '')+
                        (status !=  '2' && status != '1' ? '<option value="approve">'+trans('approve')+'</option>' : '')+
                        (status !=  '2' ? '<option value="trash">'+trans('trash')+'</option>' : '')+
                        (status === '2' ? '<option value="approve">'+trans('restore')+'</option>' : '')+
                        (status === '2' ? '<option value="delete">'+trans('delete_permanently')+'</option>' : '')+
                '</select> <button type="submit" class="btn btn-default btn-sm">'+trans('apply')+'</button></div>'
            );
        });


        Beesoft.ajaxFormCb.comments_bulk_action = function (message) {
            commentsDataTable.draw();

            var status = $('.status-filter');
            status.find('[data-status="0"] .count').text('('+message.pending+')');
            status.find('[data-status="2"] .count').text('('+message.trash+')');

            $('.select-all').prop('checked', false);
        };

        Beesoft.admin.commentReply = function (id, user) {
            var modal = $('#commentReplyModal');
            modal.find('input[name="id"]').val(id);
            modal.find('.user').text(user);
            modal.modal('show');

            // Register ajax form callback
            Beesoft.ajaxFormCb.comment_reply = function () {
                commentsDataTable.draw();
                modal.modal('hide');
            };
        };

        // Filter comments by the status
        $('.status-filter li').on('click', 'a', function (e) {
            e.preventDefault();

            if ($(e.delegateTarget).hasClass('active')) return;

            commentsDataTable.column(9).search( $(this).attr('data-status') ).draw();
            
            $(e.delegateTarget).parent().find('li.active').removeClass('active');
            $(e.delegateTarget).addClass('active');
        });
    };

    Beesoft.admin.commentAction = function (id, action) {
        $.post(Beesoft.options.ajaxUrl, {
            action: 'comments_bulk_action', 
            bulk_action: action,
            comments: [id],
        }, function (response) {
            if (!response.success) return;

            commentsDataTable.draw();

            var status = $('.status-filter');
            status.find('[data-status="0"] .count').text('('+response.message.pending+')');
            status.find('[data-status="2"] .count').text('('+response.message.trash+')');
        }, 'json');
    };

    // Select all checkboxes from the table
    $('.table-dt').on('click', '.select-all', function (e) {
    	$(e.delegateTarget).find('.chb-select').prop('checked', this.checked);

    	$('.delete-bulk').prop('disabled', 
                            $('.chb-select:checked').length ? false : true);
    });
    
    // If when checking one checkbox at the time from the table
    // all the checboxes where checked, also check the select all checkbox.
    $('.table-dt').on('click', '.chb-select', function (e) {
    	var checked = $(e.delegateTarget).find('.chb-select:checked').length;
    	
        $(e.delegateTarget).find('.select-all').prop('checked', 
                $(e.delegateTarget).find('.chb-select').length == checked);
    	
        $('.delete-bulk').prop('disabled', checked ? false : true);
    });
});


// Bootstrap Hover Dropdown Plugin by Cameron Spear
// http://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin
(function(e,t,n){var r=e();e.fn.dropdownHover=function(n){if("ontouchstart"in document)return this;r=r.add(this.parent());return this.each(function(){function h(e){r.find(":focus").blur();l.instantlyCloseOthers===!0&&r.removeClass("open");t.clearTimeout(c);s.addClass("open");i.trigger(a)}var i=e(this),s=i.parent(),o={delay:100,instantlyCloseOthers:!0},u={delay:e(this).data("delay"),instantlyCloseOthers:e(this).data("close-others")},a="show.bs.dropdown",f="hide.bs.dropdown",l=e.extend(!0,{},o,n,u),c;s.hover(function(e){if(!s.hasClass("open")&&!i.is(e.target))return!0;h(e)},function(){c=t.setTimeout(function(){s.removeClass("open");i.trigger(f)},l.delay)});i.hover(function(e){if(!s.hasClass("open")&&!s.is(e.target))return!0;h(e)});s.find(".dropdown-submenu").each(function(){var n=e(this),r;n.hover(function(){t.clearTimeout(r);n.children(".dropdown-menu").show();n.siblings().children(".dropdown-menu").hide()},function(){var e=n.children(".dropdown-menu");r=t.setTimeout(function(){e.hide()},l.delay)})})})};e(document).ready(function(){e('[data-hover="dropdown"]').dropdownHover()})})(jQuery,this);