// datatable
(function ($, BeePOS, settings, document) {
    $(document).ready(function () {


        var create = function (tableId, kabalot) {
            var table = $(tableId);
            var searchFields = jQuery('thead tr:nth-child(2) [data-search]', table);
            var filters = jQuery('thead tr:nth-child(2) [data-filter]', table)

            if(settings.allowDebug){
                var startRow = table.find('thead tr').length
                table.find('tbody').on('click', 'tr', function(){
                    console.log(table.DataTable().ajax.json().items[this.rowIndex-startRow])
                })
            }


            function debounce(func, wait, immediate) {
                var timeout;
                return function () {
                    var context = this, args = arguments;
                    var later = function () {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            };

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
                        var start,end;
                        if(el.attr('data-date-start') != ''){
                            switch(el.attr('data-date-start')){
                                case 'month':  start = moment().startOf('month'); break;
                                case 'now':  start = moment(); break;
                                default: start = moment(); break;
                            }
                        }else{
                            start = moment()
                        }
    
                        if(el.attr('data-date-end') != ''){
                            switch(el.attr('data-date-end')){
                                case 'month':  end = moment().endOf('month'); break;
                                case 'now':  end = moment(); break;
                                default: end = moment(); break;
                            }
                        }else{
                            end = moment()
                        }

      
    
                        jQuery(el).daterangepicker({
                            startDate: start,
                            endDate: end, //.endOf('month'),
                            isRTL: true,
                            langauge: 'he',
                            locale: {
                                format: 'DD/M/YY',
                                "applyLabel": "אישור",
                                "cancelLabel": "ביטול",
                            }
                        }).on('apply.daterangepicker', function(){
                            table.DataTable().ajax.reload();
                        });
    
                        if(el.attr('data-search-start') === 'false'){
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

            if (settings.buttons.allowClientPush) buttons.push({
                text: 'שלח הודעה <i class="fas fa-comments"></i>',
                className: 'btn btn-dark',
                action: function (e, dt, node, config) {
                    // rows_selected = table.column(0).checkboxes.selected();
                    var clientsIds = dt.column(0).checkboxes.selected().toArray();
                    if (!clientsIds.length) return alert('אנא בחר לקוחות');

                    modalsClientIds.val(clientsIds.join(","));
                    modal.modal('show');

                }
            })

            if (settings.buttons.excel) buttons.push({
                extend: 'excelHtml5',
                text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                filename: 'דו״ח אי הרשמה',
                className: 'btn btn-dark',
                exportOptions: {}
            })
            if (settings.buttons.csv) buttons.push({
                extend: 'csvHtml5',
                text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                filename: 'דו״ח אי הרשמה',
                className: 'btn btn-dark',
                exportOptions: {}
            })




            var dataTableSettings = {
                bInfo: false,
                orderCellsTop: true, // sorting only on first raw in thead
                language: BeePOS.options.datatables,
                responsive: true,
                processing: true,
                paging: true,
                scrollY: "450px",
                scrollCollapse: true,
                dom: "Blfrtip",
                ajax: {
                    url: BeePOS.options.api+ 'client/sales',
                    headers: {
                        'x-cookie': document.cookie
                    },
                    method: 'GET',
                    data: function (d) {
                        var sortKey = JSON.parse(JSON.stringify(d.columns[d.order[0].column].name));
                        var sortDir = JSON.parse(JSON.stringify(d.order[0].dir));
                        var limit = JSON.parse(JSON.stringify(d.length));
                        var page = JSON.parse(JSON.stringify((d.start + d.length) / d.length));
                        for (key in d) delete d[key];
                        d.kabalot = kabalot;
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

            if (buttons.length) dataTableSettings.buttons = buttons;



            table.dataTable(dataTableSettings).on('xhr.dt', function (e, settings, json, xhr) {
                var api = table.dataTable().api();

                jQuery(api.column(0).footer()).html('סה"כ');
                jQuery(api.column(4).footer()).html((json.meta.totalAmount || 0).toFixed(2));

                json.data = json.items.map(function (x) {
                    var data = [];

                    data.push(x.clientId)


                    data.push(moment(x.date).format('DD/MM/YYYY'));
                    data.push(x.product);
                    data.push(x.productType);
                    data.push( (x.amount ? x.amount.toFixed(2) : '0.00'));
                    data.push(x.branchName);
                    data.push('<a href="../ClientProfile.php?u=' + (x.clientId || 0) +
                        '">' + (x.fullName || '') + '</a>');
                    data.push(x.memberType)
                    data.push(x.paymentMethod + ((x.paymentCard) ? ' ' + x.paymentCard : '') + ((x.paymentCardType) ? ' ' + x.paymentCardType : ''))

                    return data
                });
                // json.draw = 1;
                json.recordsTotal = parseInt(json.meta.pageRows);
                json.recordsFiltered = parseInt(json.meta.rows);
            });
        }

        create('#dataTable', 'true');
        //create('#dataTableCheshbonit', 'false');

    })
})(jQuery, BeePOS, boostAppDataTable, document);


// auto populate filter field for tags select
(function ($, document) {
    $.ajax({
        url: BeePOS.options.api + 'company/departments',
        headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var selects = $('select[data-id="departmentFilter"]');
            selects.each(function (i, select) {
                select = jQuery(select);
                for (let index = 0; index < data.items.length; index++) {
                    select.append($('<option>', { value: data.items[index].id, text: data.items[index].name }));
                }
                select.select2({ tags: true, placeholder: select.attr('placeholder') });
            })
        })
    });

    $.ajax({
        url: BeePOS.options.api + 'company/memberships',
        headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var selects = $('select[data-id="membershipFilter"]');
            selects.each(function (i, select) {
                select = jQuery(select);
                for (let index = 0; index < data.items.length; index++) {
                    select.append($('<option>', { value: data.items[index].id, text: data.items[index].name }));
                }
                select.select2({ tags: true, placeholder: select.attr('placeholder') });
            })
        })
    });

    $.ajax({
        url: BeePOS.options.api + 'company/products',
        headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var selects = $('select[data-id="productsFilter"]');
            selects.each(function (i, select) {
                select = jQuery(select);
                for (let index = 0; index < data.items.length; index++) {
                    select.append($('<option>', { value: data.items[index].id, text: data.items[index].name || 'אין שם למוצר ' + data.items[index].id }));
                }
                select.select2({ tags: true, placeholder: select.attr('placeholder') });
            })
        })
    });

    $.ajax({
        url: BeePOS.options.api + 'company/branches',
        headers: { 'x-cookie': document.cookie },
        method: 'GET',
    }).done(function (data, textStatus, jqXHR) {
        $(document).ready(function () {
            var selects = $('select[data-id="branchFilter"]');
            selects.each(function (i, select) {
                select = jQuery(select);
                for (let index = 0; index < data.items.length; index++) {
                    select.append($('<option>', { value: data.items[index].id, text: data.items[index].name || 'אין שם למוצר ' + data.items[index].id }));
                }
                select.select2({ tags: true, placeholder: select.attr('placeholder') });
            })
        })
    });


})(jQuery, document)