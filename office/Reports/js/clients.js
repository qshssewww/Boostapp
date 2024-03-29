(function ($, BeePOS, settings) {



    function get_boostapplogin_domain() {
        var queryString = 'devlogin.boostapp.co.il';
        var url = window.location.href;
        if (url.indexOf(queryString) != -1) {
            return 'https://devlogin.boostapp.co.il';
        }
        return 'https://login.boostapp.co.il'
    }



    /* $.ajax({
     
     url: get_boostapplogin_domain() + '/api/company/memberships',
     
     headers: { 'x-cookie': document.cookie },
     
     method: 'GET',
     
     }).done(function (data, textStatus, jqXHR) {
     
     $(document).ready(function () {
     
     var select = $('#membershipFilter');
     
     for (let index = 0; index < data.items.length; index++) {
     
     select.append($('<option>', { value: data.items[index].id, text: data.items[index].name }));
     
     }
     
     select.select2({ tags: true, placeholder: select.attr('placeholder') });
     
     })
     
     });*/

    $.ajax({

        url: get_boostapplogin_domain() + '/api/company/branches',

        headers: {'x-cookie': document.cookie},

        method: 'GET',

    }).done(function (data, textStatus, jqXHR) {

        $(document).ready(function () {

            var select = $('#branchFilter');

            for (let index = 0; index < data.items.length; index++) {

                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name}));

            }

            select.select2({tags: true, placeholder: select.attr('placeholder')});

        })

    });

    $.ajax({

        url: get_boostapplogin_domain() + '/api/company/levels',

        headers: {'x-cookie': document.cookie},

        method: 'GET',

    }).done(function (data, textStatus, jqXHR) {

        $(document).ready(function () {

            var select = $('#clientRanksFilter');

            for (let index = 0; index < data.items.length; index++) {

                select.append($('<option>', {value: data.items[index].id, text: data.items[index].name}));

            }

            select.select2({tags: true, placeholder: select.attr('placeholder')});

        })

    });















    $(document).ready(function () {





        var table = $(boostAppDataTable.id || '#dataTable');

        var searchFields = jQuery('thead tr:nth-child(2) [data-search]', table);

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

            dom: '<<"d-flex justify-content-start"><"d-flex justify-content-between w-100 mb-10" <lrf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

            ajax: {

                url: get_boostapplogin_domain() + '/api/company/clients',

                beforeSend: function (xhr, settings) {

                    if (boostAppDataTable && boostAppDataTable.skipAjax) {

                        var lastResponse = table.dataTable().api().ajax.json(); //get last server response

                        lastResponse.draw = 0; //change draw value to match draw value of current request



                        this.success(lastResponse); //call success function of current AJAX object (while passing fake data) which is used by DataTable on successful response from server



                        boostAppDataTable.skipAjax = false; //reset the flag



                        return false; //cancel current AJAX request                    

                    }

                },

                headers: {

                    'x-cookie': document.cookie

                },

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

            jQuery(api.column(1).footer()).html('סה"כ: ' + json.meta.rows);



            json.data = json.items.map(function (x) {

                var data = [];



                data.push(x.clientId)


                data.push('<a href="../ClientProfile.php?u=' + (x.clientId || 0) +
                        '">' + (x.clientFullName || '') + '</a>');



                var TreuclientStatus = '';

                if (x.clientStatus == '0') {
                    TreuclientStatus = 'לא פעיל';
                } else if (x.clientStatus == '2') {
                    TreuclientStatus = 'מתעניין';
                } else {
                    TreuclientStatus = 'פעיל';
                }

                data.push(TreuclientStatus)


                if (x.clientPhone && x.clientPhone.indexOf('0') == 0 && x.clientPhone.length > 9) {

                    data.push('<a href="tel:+' + x.clientPhone.replace('0', '972').replace(
                            /\D/g, '') + '">' + (x.clientPhone || '') + '</a>');

                } else {

                    data.push(x.clientPhone || '');

                }



                data.push(x.clientAge || '');

                data.push(x.clientRank || '');





                // data.push(x.memberShipType.map(function (member, index) {

                //     return '<span title="'+(x.itemName[index] || '')+' :: '+(x.departmentName[index] || '')+'">'+member+'</span>'

                // }).join("<br>"));





                data.push(x.clientBranchName || '')

                data.push('<i data-client-id="' + x.clientId + '" class="fal fa-copy copyToClipboard fa-lg"></i>');



                return data

            });

            // json.draw = 1;

            json.recordsTotal = parseInt(json.meta.pageRows);

            json.recordsFiltered = parseInt(json.meta.rows);

        });

        $(document).on('click', '.copyToClipboard', function (e) {

            let clientId = $(this).attr('data-client-id');
            let formUrl =  sendUrlToClientPush.find('.modal-header input').val();
            let studioUrl =  document.getElementsByClassName("js-studio-url")[0].value;
            let link = formUrl  + '&GetUrl=' + studioUrl + '&Id=' + clientId;

            document.addEventListener('copy', function(e) {
                e.clipboardData.setData('text/plain', link);
                e.preventDefault();
            }, true);

            document.execCommand('copy');

            $(this).attr('title', 'Copied!');
            $(this).tooltip('show');
            $(this).removeClass("fa-copy").addClass("fa-check text-success");
            // $('.js-remove-copy-link-elm').remove();
            setTimeout(() => {
                $(this).removeClass("fa-check text-success").addClass("fa-copy");
                $(this).tooltip('dispose');
                $(this).attr('title', '');
            }, 2000);
        });

    })

})(jQuery, BeePOS, boostAppDataTable)

