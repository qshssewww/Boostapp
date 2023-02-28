(function ($) {
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            if (settings && settings.url && settings.url.match(/api\.boostapp\.co\.il/)) {
                for (var key in $.ajaxSettings.headers) {
                    xhr.setRequestHeader(key, null)
                }
                xhr.setRequestHeader('x-cookie', document.cookie)
                xhr.setRequestHeader('Content-Type', 'application/json')
            }
        }
    });

})(jQuery);

(function ($, api, site) {
/*
    $(document).ready(function () {
        var wrapper = $('#newPageFromTemplate');
        var ajaxheaders = $.ajaxSettings.headers;
        $.ajaxSettings.headers = {};


        $.ajax({
            url: api + '&action=templates',
            method: 'get',
            success: function (data) {
                var html = "<div class=''>";
                for (let index = 0; index < data.items.length; index++) {
                    var t = data.items[index];



                    html += '<div class="card col-md-6 m-2" data-id="' + t.id + '" duplicateNewTemplate>';
                    html += '<img class="card-img-top" src="https://wp.boostapp.co.il/' + t.thumb + '" alt="' + t.title + '">';
                    html += '<div class="card-body">';
                    html += '<h5 class="card-title">' + t.title + '</h5>';
                    html += '</div>';
                    html += '</div>'


                }
                html += "</div>";
                wrapper.html(html);

            }
        });
        $.ajaxSettings.headers = ajaxheaders;
        $(document).on('click', '[duplicateNewTemplate]', function () {
            var el = $(this)
            var ajaxheaders = $.ajaxSettings.headers;
            $.ajaxSettings.headers = {};
            $.ajax({
                url: api + '&action=duplicate&id=' + el.attr('data-id'),
                method: 'get',
                success: function (d) {
                    console.log(d);
                    var win = window.open(d.items.url, '_blank');
                    win.focus();

                }
            });
            $.ajaxSettings.headers = ajaxheaders;
        })
    })

    */

    // $.ajaxSettings.headers = {};

    $(document).ready(function () {
        var iframe = ($('iframe'));
        var iframeUrl = iframe[0].src;
        var table = $('#landingPages');

        var createPageModal = $('#createPageModal');
        var createPageModalTitle = $('.modal-title', createPageModal);
        var createPageModalFooter = $('.modal-footer', createPageModal);
        var createPageModalFooterHTML = createPageModalFooter.html();
        var createPageModalTitleText = createPageModalTitle.html();
        var createPageModalForm = $('form', createPageModal);
        var form = $('#newPage', createPageModal);

        $('#newPageThanksUrl').find('select').on('change', function () {
            var el = $(this);
            if (el.val() != '' && el.val().match(/^http/g) !== null) {
                $('input[name="thanksPageUrl"]', createPageModal).val(el.val());
            } else {
                $('input[name="thanksPageUrl"]', createPageModal).val('');
            }
        }).on("select2:opening", function () {
            createPageModal.removeAttr("tabindex", "-1");
        }).on("select2:close", function () {
            createPageModal.attr("tabindex", "-1");
        })

        createPageModal.on('hidden.bs.modal', function (e) {
            // reset modal to default no matter what
            createPageModalForm.data('id', null).removeAttr('data-id');
            createPageModalForm[0].reset();
            createPageModalTitle.html(createPageModalTitleText);
            createPageModalFooter.html(createPageModalFooterHTML);
        })

        setInterval(function () {
            iframe[0].src = iframeUrl + '&_=' + (+new Date())
        }, 60 * 1000 * 10); // refresh evry 10 minutes relogin

        table.on('dblclick', 'tbody td', function (e) {
            var el = $(this);
            switch (el.index()) {
                case 0:
                    el.html(`
                        <div class="input-group">
                        <input type="text" class="form-control" value="${el.html().replace(/"/g, "&quot;").replace(/'/, "&apos;")}" />
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-success" type="button" data-update>`+lang('confirm')+`</button>
                        </div>
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-danger" type="button" data-oldval="${el.html().replace(/"/g, "&quot;").replace(/'/, "&apos;")}" data-cancel>`+lang('cancel')+`</button>
                        </div>
                            
                        </div>
                    `
                    )
                    break;
                case 1:
                    var selected = el.html();
                    // console.log(el, selected);
                    el.html(`
                    <div class="input-group">
                    <select name="status" class="form-control">
                        <option value="publish" ${(selected === lang('published')) ? 'selected' : ''}>`+ lang('published') +` </option>
                        <option value="pending" ${(selected === lang('pending')) ? 'selected' : ''}>`+ lang('pending')+`</option>
                        <option value="trash" ${(selected === lang('recycle_bin')) ? 'selected' : ''}>`+lang('recycle_bin')+`</option>
                        <option value="draft" ${(selected === lang('draft')) ? 'selected' : ''}>`+lang('draft')+`</option>
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-success" type="button" data-update>`+lang('confirm')+`</button>
                    </div>
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-danger" type="button" data-oldval="${el.html().replace(/"/g, "&quot;").replace(/'/, "&apos;")}" data-cancel>`+lang('cancel')+`</button>
                    </div>
                        
                    </div>
                `
                    )
                    break;
                default: break;
            }
        });

        // update data
        table.on('click', 'tbody td button[data-update]', function (e) {
            var el = $(this);
            var cog = '<i class="fa fa-spin fa-cog"></i>';
            var cancel = el.prev('div').find('button[data-cancel]');
            cancel.attr('data-disabled', true);
            if (el.html() === cog) return; // prevent multiple submit

            el.html(cog)
            var id = table.DataTable().data()[el.closest('tr').index()].id;
            switch (el.closest('td').index()) {
                case 0:
                    var input = el.parent('div').prev('input');
                    var value = encodeURIComponent(input.val());
                    input.attr('disabled', 'true');

                    var ajaxheaders = $.ajaxSettings.headers;
                    $.ajaxSettings.headers = {};
                    $.get('proxy.php?action=update&id=' + id + '&title=' + value, function () {
                        el.closest('td').html(input.val());
                    }).fail(function (data) {
                        // console.log(data);
                        el.closest('td').html(cancel.data('oldval'));
                    }).always(function () {
                        $.ajaxSettings.headers = ajaxheaders;
                    })
                    break;
                case 1:
                    var input = el.parent('div').prev('select');
                    var value = input.val();
                    input.attr('disabled', 'true');
                    var ajaxheaders = $.ajaxSettings.headers;
                    $.ajaxSettings.headers = {};
                    $.get('proxy.php?action=update&id=' + id + '&status=' + value, function () {
                        // el.closest('td').html(input.find('[value="'+input.val()+'"]').html());
                        table.DataTable().ajax.reload();
                    }).fail(function (data) {
                        // console.log(data);
                        el.closest('td').html(cancel.data('oldval'));

                    }).always(function () {
                        $.ajaxSettings.headers = ajaxheaders;
                    })
                    break;
            }

        });

        // cancel button
        table.on('click', 'tbody td button[data-cancel]', function (e) {
            var el = $(this);
            if (el.data('disabled')) return alert(lang('cant_cancel_notice'));
            el.closest('td').html(el.data('oldval'));
        });

        var showDeleted;
        dTable = table.DataTable({
            dom: 'Bfrtip',
            language: {
                "processing": lang('loading_your_pages') + " <i class=\"fas fa-spinner fa-spin\"></i>",
                "lengthMenu": "הצג _MENU_ פריטים",
                "zeroRecords": lang('Pages_not_found'),
                "emptyTable": lang('Pages_not_found'),
                "info": "_START_ עד _END_ מתוך _TOTAL_ רשומות",
                "infoEmpty": "0 עד 0 מתוך 0 רשומות",
                "infoFiltered": "(מסונן מסך _MAX_  רשומות)",
                "infoPostFix": "",
                "search": lang('search'),
                "url": "",
                "paginate": {
                    "first": lang('first'),
                    "previous": lang('previous'),
                    "next": lang('next_client_profile'),
                    "last": lang('last_client_profile')
                }
            },
            buttons: {
                dom: {
                    button: {
                        tag: 'button',
                        className: ''
                    },
                    container: {
                        className: 'dt-buttons float-left'
                    }
                },
                buttons: [
                    {
                        text: '<i class="fa fa-trash small"></i> '+lang('archive'),
                        className: 'btn btn-success btn-danger mie-7 btn-sm',
                        action: function (w, dt, node, config) {
                            showDeleted = !showDeleted;
                            showDeleted ? node.removeClass('btn-danger') : node.addClass('btn-danger');
                            node.html(showDeleted ? '<i class="fa fa-file small"></i> '+lang('actives') : '<i class="fa fa-trash small"></i> '+lang('archive'))
                            dt.ajax.reload();
                        }
                    },
                    {
                        text: '<i class="fa fa-plus small"></i> '+lang('create_page'),
                        className: 'btn btn-success btn-sm',
                        action: function () {
                            createPageModal.modal('toggle');
                        }
                    }
                ]
            },
            ajax: function (data, callback, settings) {

                // $.ajaxSettings.headers = {};
                var ajaxheaders = $.ajaxSettings.headers;
                $.ajaxSettings.headers = {};


                $.ajax({
                    url: 'proxy.php?action=list',
                    method: 'get',
                    success: function (data) {
                        console.log(data);
                        table.find('thead').show();
                        // console.log(data);
                        var data = data || { items: [] }

                        var select = $('#newPageThanksUrl').find('select').html('<option>בחר דף תודה</option>');
                        for (let index = 0; index < data.items.length; index++) {
                            select.append($('<option/>').html(data.items[index].post_title).val(data.items[index].guid))
                        }
                        select.select2();

                        if (showDeleted) {
                            data.items = data.items.filter(function (x) { return ['trash'].indexOf(x.post_status) !== -1 })
                        } else {
                            data.items = data.items.filter(function (x) { return ['trash'].indexOf(x.post_status) === -1 })
                        }
                        var items = data.items.map(function (x) {
                            // var data = data.items.map(function(x){
                            var tools = '';

                            if (x.post_status === 'trash') {
                                tools += '<a class="btn btn-sm btn-outline-info" href="#settings-' + x.ID + '" data-settings="' + x.ID + '" class=""><i class="fa fa-cog text-dark"></i></a> ';
                            } else {
                                tools += '<a class="btn btn-sm btn-outline-success" title="'+lang('edit_page')+'" target="_blank" href="' + site + 'wp-admin/post.php?post=' + x.ID + '&action=elementor"><i class="fa fa-edit"></i></a> ';
                                tools += '<a class="btn btn-sm btn-outline-danger" href="#delete-' + x.ID + '" data-delete="' + x.ID + '" class=""><i class="fa fa-trash text-danger"></i></a> ';
                                tools += '<a class="btn btn-sm btn-outline-info" href="#link-' + x.ID + '" data-link="' + ((x.meta && x.meta['1baco']) ? 'https://1ba.co/' + x.meta['1baco'] : x.guid) + '" class=""><i class="fa fa-link text-dark"></i></a> ';
                                tools += '<a class="btn btn-sm btn-outline-info" href="#settings-' + x.ID + '" data-settings="' + x.ID + '" class=""><i class="fa fa-cog text-dark"></i></a> ';
                            }

                            var pageType = {
                                "page": "עמוד",
                                "thankyou": lang('thank_you_page')
                            }

                            switch (x.post_status) {
                                case "publish": x.post_status_name = lang('published'); break;
                                case "inherit": x.post_status_name = lang('inherit'); break;
                                case "pending": x.post_status_name = lang('pending'); break;
                                case "private": x.post_status_name = lang('private'); break;
                                case "future": x.post_status_name = lang('future_single'); break;
                                case "draft": x.post_status_name = lang('draft'); break;
                                case "trash": x.post_status_name = lang('recycle_bin'); break;
                                default: x.post_status_name = x.post_status;
                            }
                            return {
                                id: x.ID,
                                name: x.post_title,
                                status: x.post_status_name,
                                pageType: pageType[x.meta.boostapp_pageType && x.meta.boostapp_pageType.length ? x.meta.boostapp_pageType[0]: "page"],
                                statusRaw: x.post_status,
                                create: x.post_date_gmt,
                                tools: tools,
                                meta: x.meta
                            }
                        });
                        callback({ data: items })
                    }
                });
                $.ajaxSettings.headers = ajaxheaders;
            },
            columns: [
                { "data": "name" },
                { "data": "status" },
                { "data": "pageType" },
                { "data": "create" },
                { "data": "tools" },
            ],
            fnDrawCallback: function (oSettings) {
                if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                    $(oSettings.nTableWrapper).find('.dataTables_info').hide();
                }
            },
            columnDefs: [
                { targets: 'no-sort', orderable: false }
            ],
            processing: true,
            serverSide: true,
            bPaginate: false
        });


        table.on('click', '[data-link]', function (e) {
            e.preventDefault();
            var el = $(this);
            var url = el.attr('data-link');
            if (url.match(/1ba.co/)) return sendShortLinkToClient(url, el);

            var ajaxheaders = $.ajaxSettings.headers;
            $.ajaxSettings.headers = {};
            var ajax = $.ajax({
                url: 'https://1ba.co/',
                method: 'post',
                data: { url: url }
            });
            ajax.done(function (data) {
                var ajaxheaders2 = $.ajaxSettings.headers;
                $.ajaxSettings.headers = {};
                var save = $.ajax({
                    method: 'get',
                    url: api + '&action=update&id=' + el.attr('href').replace('#link-', '') + '&1baco=' + data.id,
                });
                save.done(function () {
                    el.attr('data-link', 'https://1ba.co/' + data.id);
                    return sendShortLinkToClient('https://1ba.co/' + data.id, el);
                }).always(function () {
                    $.ajaxSettings.headers = ajaxheaders2;
                });
            }).fail(function (data) {
                console.log(data)
            }).always(function () {
                $.ajaxSettings.headers = ajaxheaders;
            });
        });


        function copyToClipboard(value) {

            // Create a "hidden" input
            var aux = document.createElement("input");

            // Assign it the value of the specified element
            aux.setAttribute("value", value);

            // Append it to the body
            document.body.appendChild(aux);

            // Highlight its content
            aux.select();

            // Copy the highlighted text
            document.execCommand("copy");

            // Remove it from the body
            document.body.removeChild(aux);

        }

        var sendUrlToClientPush = $('#sendUrlToClientPush').on('shown.bs.modal', function (e) {
            boostAppDataTable.skipAjax = true;
            $('#dataTableClients').dataTable().fnAdjustColumnSizing();
        });

        var sendUrlToClientPush = $('#sendUrlToClientPush');

        var popupClientPushFORM = $('form[action="SendClientPushReport"]');
        var clientPopUpClientPushFROMHTML = $('div:first', popupClientPushFORM);
        clientPopUpClientPushFROMHTML.html(clientPopUpClientPushFROMHTML.html() + '<br><strong>[[קישור]]</strong> '+lang('replace_link_dynamicforms'));
        popupClientPushFORM.append('<input type="hidden" name="landingUrl" value="">');

        var landingUrl = $('[name="landingUrl"]', popupClientPushFORM);

        function sendShortLinkToClient(url, el) {

            copyToClipboard(url);
            landingUrl.val(encodeURI(url));
            var data = table.DataTable().data()[el.closest('tr').index()]
            sendUrlToClientPush
                .find('.modal-header h5>span').html(url).end()
                // .find('.modal-body').html(JSON.stringify(data)).end()
                .modal('toggle')
        }

        table.on('click', '[data-settings]', function (e) {
            e.preventDefault();
            var el = $(this);
            var index = el.closest('tr').index();
            var data = table.DataTable().data()[index];

            createPageModalForm.attr('data-id', data.id);
            createPageModalTitle.html(lang('edit_page'));
            $('input[name="title"]', createPageModalForm).val(data.name);
            $('select[name="status"]', createPageModalForm).val(data.statusRaw);
            createPageModalFooter.html('<input type="submit" class="btn btn-success" value="'+lang('save_changes_button')+'" data-action="modify">');

            if (data.meta && data.meta.boostapp_thanks) {
                $('select[name="thanksPage"]', createPageModalForm).val(data.meta.boostapp_thanks);
                $('input[name="thanksPageUrl"]', createPageModalForm).val(data.meta.boostapp_thanks);
            }

            createPageModal.modal('toggle');
        })

        table.on('click', '[data-delete]', function (e) {
            e.preventDefault();
            var el = $(this);
            var id = el.data('delete');
            var name = el.parents('tr').find('td:first').html();

            var r = confirm(lang('delete_page_q') + " " + name + "!");

            if (r === true) {
                var ajaxheaders = $.ajaxSettings.headers;
                $.ajaxSettings.headers = {};
                $.ajax({
                    url: 'proxy.php?action=delete&id=' + id,
                    method: 'get',
                    success: function (json) {
                        // console.log(json);
                        dTable
                            .row(el.parents('tr'))
                            .remove()
                            .draw();
                    },
                    error: function (jXhr) {
                        alert(jXhr.responseJSON.message)
                    }
                });
                $.ajaxSettings.headers = ajaxheaders;

            }
        });



        // a helper to know where to go after creating a page
        $('input[type="submit"][data-action]', form).each(function (i, el) {
            $(el).on('click', function (e) {
                form.attr('data-action', $(this).data('action'))
            })
        })

        form.on('submit', function (e) {
            e.preventDefault();


            var ajaxheaders = $.ajaxSettings.headers;
            $.ajaxSettings.headers = {};

            var data = $('input, select[name!=thanksPage]', form).serialize()
            var id = form.data('id');
            if (id && !isNaN(id)) {
                data = data.replace('action=insert', 'action=update&id=' + id)
            }
            $.ajax({
                type: 'GET',
                url: 'proxy.php',
                data: data,
                success: function (json) {


                    switch (form.data('action')) {
                        case "edit":
                            return window.location = json.editUrl;
                            break;
                        default:
                            form[0].reset();
                            if (dTable && dTable.ajax && dTable.ajax.reload) dTable.ajax.reload();
                            if (createPageModal) createPageModal.modal('toggle')
                            break;
                    }
                },
                error: function (jXhr) {
                    alert(jXhr.responseJSON.message)
                }
            });
            $.ajaxSettings.headers = ajaxheaders;


        })
    }); // end ready
})(jQuery, api, site)
