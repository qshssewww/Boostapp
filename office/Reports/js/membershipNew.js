(function ($, BeePOS, settings) {

    function get_boostapplogin_domain() {
        var queryString = 'devlogin.boostapp.co.il';
        var url = window.location.href;
        if (url.indexOf(queryString) != -1) {
            return 'https://devlogin.boostapp.co.il';
        }
        return 'https://login.boostapp.co.il'
    }

    $.ajax({
        url: get_boostapplogin_domain() + '/api/company/departments',
        // headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var select = $('#departmentFilter');
            for (let index = 0; index < data.items.length; index++) {
                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name}));
            }
            select.select2({tags: true, placeholder: select.attr('placeholder')});
        })
    });

    $.ajax({
        url: get_boostapplogin_domain() + '/api/company/branches',
        // headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var select = $('#branchesFilter');
            // if( data.items.length <= 1) return;
            for (let index = 0; index < data.items.length; index++) {
                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name}));
            }
            select.select2({tags: true, placeholder: select.attr('placeholder')});
        })
    });



    $.ajax({
        url: get_boostapplogin_domain() + '/api/company/memberships',
        // headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var select = $('#membershipFilter');
            for (let index = 0; index < data.items.length; index++) {
                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name}));
            }
            select.select2({tags: true, placeholder: select.attr('placeholder')});
        })
    });

    $.ajax({
        url: get_boostapplogin_domain() + '/api/company/products',
        // headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var select = $('#productsFilter');
            for (let index = 0; index < data.items.length; index++) {
                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name || 'אין שם למוצר ' + data.items[index].id}));
            }
            select.select2({tags: true, placeholder: select.attr('placeholder')});
        })
    });

    $(document).ready(function () {


        var table = $('#dataTable');
        var searchFields = jQuery('thead tr:nth-child(2) [data-search]', table);
        searchFields.push($('#branchesFilter')[0]);
        var filters = jQuery('thead tr:nth-child(2) [data-filter]', table)



        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate)
                        func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow)
                    func.apply(context, args);
            };
        }
        ;

        jQuery.merge(filters, searchFields).each(function (i, el) {
            switch (el.tagName.toLowerCase()) {
                case "select":
                    jQuery(el).on('change', function () {
                        table.DataTable().ajax.reload();
                    });
                    break;
                case "input":
                    jQuery(el).on('keyup', debounce(function () {
                        table.DataTable().ajax.reload();
                    }, 500));
                    break;
            }

            if (el.getAttribute('data-search-type') && (el.getAttribute('data-search-type')).toLowerCase() == 'daterange') {
                try {
                    var el = jQuery(el);
                    var start, end;
                    if (el.attr('data-date-start') != '') {
                        switch (el.attr('data-date-start')) {
                            case 'month':
                                start = moment().startOf('month');
                                break;
                            case 'now':
                                start = moment();
                                break;
                            default:
                                start = moment();
                                break;
                        }
                    } else {
                        start = moment()
                    }

                    if (el.attr('data-date-end') != '') {
                        switch (el.attr('data-date-end')) {
                            case 'month':
                                end = moment().endOf('month');
                                break;
                            case 'now':
                                end = moment();
                                break;
                            default:
                                end = moment();
                                break;
                        }
                    } else {
                        end = moment()
                    }

                    var direction = false;

                    if (jQuery("html").attr("dir") == 'rtl') {
                        direction = true;
                    }

                    jQuery(el).daterangepicker({
                        startDate: start,
                        endDate: end, //.endOf('month'),
                        isRTL: direction,
                        langauge: 'he',
                        locale: {
                            format: 'DD/M/YY',
                            "applyLabel": "אישור",
                            "cancelLabel": "ביטול",
                        }
                    }).on('apply.daterangepicker', function () {
                        table.DataTable().ajax.reload();
                    });

                    if (el.attr('data-search-start') === 'false') {
                        jQuery(el).val('');
                    }

                } catch (e) {
                    console.log(e);
                }
            }
        })



        var modal = $('#SendClientPush');
        var modalsClientIds = $('input[name="clientsIds"]', modal);

        var buttons = []

        var send_messages = $('html').attr('dir') == 'rtl' ? 'שלח הודעה' : 'Send message';
        var choose_clients = $('html').attr('dir') == 'rtl' ? 'אנא בחר לקוחות' : 'please select clients';

        if (settings.buttons.allowClientPush)
            buttons.push({
                text: send_messages + ' <i class="fas fa-comments"></i>',
                className: 'btn btn-dark',
                action: function (e, dt, node, config) {
                    // rows_selected = table.column(0).checkboxes.selected();
                    var clientsIds = dt.column(0).checkboxes.selected().toArray();
                    if (!clientsIds.length)
                        return alert(choose_clients);

                    modalsClientIds.val(clientsIds.join(","));
                    modal.modal('show');

                }
            })

        if (settings.buttons.excel)
            buttons.push({
                extend: 'excelHtml5',
                text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                filename: 'non registers report',
                className: 'btn btn-dark',
                exportOptions: {}
            })
        if (settings.buttons.csv)
            buttons.push({
                extend: 'csvHtml5',
                text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                filename: 'non registers report',
                className: 'btn btn-dark',
                exportOptions: {}
            });

        jQuery('input[name="exp_start_or_smaller_then_today"]').on("change", function () {
            table.DataTable().ajax.reload();
        });




        var dataTableSettings = {
            bInfo: false,
            orderCellsTop: true, // sorting only on first raw in thead
            language: BeePOS.options.datatables,
            responsive: true,
            processing: true,
            paging: true,
            scrollX: true,
            scrollY: "450px",
            scrollCollapse: true,
            dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
            ajax: {
                url: get_boostapplogin_domain() + '/api/client/activityNew',
                // headers: {
                //     'x-cookie': document.cookie
                // },
                method: 'GET',
                data: function (d) {
                    var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                    var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                    var limit = JSON.parse(JSON.stringify(d.length));
                    var page = JSON.parse(JSON.stringify((d.start + d.length) / d.length));
                    for (key in d)
                        delete d[key];
                    d.report = 'true';
                    d.sort = sortKey;
                    d.dir = sortDir;
                    d.page = page;
                    d.limit = limit;



                    searchFields.each(function (i, el) {
                        el = jQuery(el);
                        if (el.attr('data-search-type') === 'dateRange' && el.val() != '') {
                            d[el.attr('data-search') + '_start'] = moment(el.data('daterangepicker').startDate._d).format('YYYY-MM-DD');
                            d[el.attr('data-search') + '_end'] = moment(el.data('daterangepicker').endDate._d).format('YYYY-MM-DD');
                            return;
                        }


                        if (el.attr('data-filter-type') && el.val() && el.val() != '') {
                            d[el.attr('data-search') + '_filter'] = jQuery('thead tr:nth-child(2) ' + el.attr('data-filter-type') + '[data-filter="' + el.attr('data-search') + '"]:first').val();
                            d[el.attr('data-search')] = el.val();
                            return; // go to next iteration
                        }

                        if (el.val() && el.val() != '') {
                            d[el.attr('data-search')] = el.val();
                            return; // go to next iteration
                        }
                    });

                    d.exp_start_or_smaller_then_today = jQuery('input[name="exp_start_or_smaller_then_today"]').is(':checked').toString()
                },
                processing: true,
            },
            serverSide: true,
            bFilter: false, // hide search field
            bSort: true,
            pageLength: 100,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
            columnDefs: [{
                'targets': [0],
                'checkboxes': {
                    'selectRow': true
                },
                bSortable: false,
                ordering: false
            }],
            select: {
                style: 'multi'
            },
            order: [
                [1, 'asc']
            ]
        }

        $('thead tr:first th', table).each(function (i, el) {
            dataTableSettings.columns = dataTableSettings.columns || [];
            dataTableSettings.columns.push({
                name: el.getAttribute('data-name'),
                bSortable: el.getAttribute('data-bSortable') ? JSON.parse(el.getAttribute('data-bSortable')) : true
            })
        })

        if (buttons.length)
            dataTableSettings.buttons = buttons;

        var reportConatainer = $('#membershipReport');

        table.dataTable(dataTableSettings).on('xhr.dt', function (e, settings, json, xhr) {
            var api = table.dataTable().api();

            table.DataTable().column(0).checkboxes.deselectAll();

            jQuery(api.column(0).footer()).html('סה"כ');
            jQuery(api.column(1).footer()).html(json.total.clients);
            jQuery(api.column(3).footer()).html(json.total.membership);
            jQuery(api.column(4).footer()).html((function (obj) {
                if (!Object.keys(obj).length)
                    return '';
                return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#totalMembershipType">סיכום סוג מנוי</button>'
            })(json.total.membershipTypes));
            jQuery(api.column(5).footer()).html((function (obj) {
                if (!Object.keys(obj).length)
                    return '';
                return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#totalProductType">סיכום מנויים למוצר</button>'
            })(json.total.products));


            var data = '';
            if (Object.keys(json.total.membershipTypes).length) {

                data += (function (obj) {
                    var rows = '<table class="table">';
                    for (var title in obj) {
                        rows += '<tr><td>' + title + ':</td><td> ' + obj[title] + '</td></tr>'
                    }
                    rows += "</table>"

                    return `
                    <div class="modal fade" id="totalMembershipType" dir="rtl" tabindex="-1" role="dialog" aria-labelledby="totalMembershipTypeLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="totalMembershipTypeLabel">סה"כ סוגי מנויים</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: -1rem 0rem -1rem 0;">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            ${rows}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close') ?></button>
                          </div>
                        </div>
                      </div>
                    </div>
                    `;
                })(json.total.membershipTypes)
            }

            if (Object.keys(json.total.products).length) {
                data += (function (obj) {


                    var rows = '<table class="table">';
                    for (var title in obj) {
                        rows += '<tr><td>' + title + ':</td><td> ' + obj[title] + '</td></tr>'
                    }
                    rows += "</table>"
                    return `
                    <div class="modal fade" id="totalProductType" dir="rtl" tabindex="-1" role="dialog" aria-labelledby="totalProductTypeLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="totalProductTypeLabel">סה"כ סוגי מנויים</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: -1rem 0rem -1rem 0;">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            ${rows}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close') ?></button>
                          </div>
                        </div>
                      </div>
                    </div>
                    `;
                })(json.total.products)
            }

            reportConatainer.html(data);




            json.data = json.items.map(function (x) {
                var data = [];

                data.push(x[0].clientId)
                data.push('<a href="../ClientProfile.php?u=' + (x[0].clientId || 0) +
                    '">' + (x[0].clientFullName || '') + '</a>');

                if (x[0].clientPhone && x[0].clientPhone.indexOf('0') == 0 && x[0].clientPhone.length > 9) {
                    data.push('<a href="tel:+' + x[0].clientPhone.replace('0', '972').replace(
                        /\D/g, '') + '">' + (x[0].clientPhone || '') + '</a>');
                } else {
                    data.push(x[0].clientPhone || '');
                }

                data.push(x.map(function (member) {
                    return member.departmentName
                }).join("<br>"));

                data.push(x.map(function (member) {
                    return member.memberShipType
                }).join("<br>"));

                data.push(x.map(function (member) {
                    return member.itemName
                }).join("<br>"));

                data.push(x.map(function (member) {
                    if (member.memberShipStart && member.memberShipExp)
                        return '<span class="text-dark">' + moment(member.memberShipStart).format('DD/MM/YYYY') + '</span> - <span class="text-dark">' + moment(member.memberShipExp).format('DD/MM/YYYY') + '</span>';
                    return member.memberShipExp ? moment(member.memberShipExp).format('DD/MM/YYYY') : undefined;
                }).join("<br>"));

                data.push(x.map(function (member) {
                    return member && member.memberShipTicket !== null && typeof member.memberShipTicket != 'undefined' ? '<span dir="ltr">' + member.memberShipTicketLeft + '/' + member.memberShipTicket + '</span>' : undefined;
                }).join("<br>"));


                return data
            });
            // json.draw = 1;
            json.recordsTotal = parseInt(json.meta.pageRows);
            json.recordsFiltered = parseInt(json.total.clients);
        });

    })
})(jQuery, BeePOS, boostAppDataTable)