window.boostappFBpages = { data: [], pipelines: {}, pages: {}, branches: {} };
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {

        if(this.name.indexOf('[') != -1){
            var name = this.name.split('[');
            o[name[0]] = o[name[0]] || {}
            var key = o[name[0]];
            for (let index = 1; index < name.length; index++) {
                const element = name[index].replace(']', '');
                if(index !== name.length -1){
                    key = (key[element] = key[element] || {});
                }else{
                    key[element] = this.value || '';
                }                
            }
        }else{
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        }
    });
    return o;
};

function get_boostapplogin_domain(){
    var queryString = 'devlogin.boostapp.co.il';
    var url = window.location.href;
    if(url.indexOf(queryString) != -1){
        return 'https://devlogin.boostapp.co.il';
    }
    return 'https://login.boostapp.co.il'
}

function getCategoriesPipeLines(callback) {
    jQuery.ajax({
        url: get_boostapplogin_domain() + '/api/' + 'pipeline/config/categories',
        method: 'GET',
        headers: {
            // 'Access-Control-Allow-Origin': '*',
            'x-cookie': document.cookie
        }
    }).done(function (data) {
        $(document).ready(function () {
            window.boostappFBpages.pipelines = data;
            if (callback && typeof callback === 'function') callback(data);
        });
    });
}

function getPagesFromBoostapp(callback){
    jQuery.ajax({
        url: get_boostapplogin_domain() + '/api/' + 'facebook/pages',
        method: 'GET',
        headers: {
            // 'Access-Control-Allow-Origin': '*',
            'x-cookie': document.cookie
        }
    }).done(function (data) {
        $(document).ready(function () {
            window.boostappFBpages.pages = (data && data.items) ? data.items : [];
            if (callback && typeof callback === 'function') callback(data);
        });
    });   
}
function getBranchesFromBoostapp(callback){
    jQuery.ajax({
        url: get_boostapplogin_domain() + '/api/' + 'company/branches',
        method: 'GET',
        headers: {
            // 'Access-Control-Allow-Origin': '*',
            'x-cookie': document.cookie
        }
    }).done(function (data) {
        $(document).ready(function () {
            window.boostappFBpages.branches = (data && data.items) ? data.items : [];
            if (callback && typeof callback === 'function') callback(data);
        });
    });   
}

getCategoriesPipeLines();
getPagesFromBoostapp();
getBranchesFromBoostapp();


window.fbAsyncInit = function () {
    FB.init({
        appId: '1931387196922899',
        autoLogAppEvents: true,
        xfbml: true,
        version: 'v14.0',
        status: true
    });
    (function initFBCallback(FB, $) {
        var facebookLoginBtn = $('#facebookLoginbtnWrapper>a.btn-facebook');
        var FBuserDetails = $('#FBuserDetails');
        var FBalerts = $('#FBalerts');
        var FBalertsBody = $('.card-body', FBalerts);
        var facebookLogout = $('#facebookLogout');
        var facebookLogoutBtn = $('a', facebookLogout);
        facebookLogout.hide();

        var settingsModal = $('#fb-boostapp-settings')
        var settingsModalPageName = $('div.modal-header h5.modal-title span', settingsModal);
        var settingsModalBody = $('div.modal-body', settingsModal);

        settingsModal.on('hidden.bs.modal', function (e) {
            settingsModalPageName.html('');
            settingsModalBody.html('');
        });

        $('button[data-submit]', settingsModal).on('click', function (e) {
            e.preventDefault();
            settingsModalBody.find('form').submit();
        })


        facebookLogoutBtn.on('click', function () {
            FB.logout();
            FBalertsBody.html('');
            facebookLoginBtn.show();
            FBalerts.hide();
        })

        var options = {
            sources: [],
            states: [],
            pages: []
        }

        jQuery.ajax({
            url: get_boostapplogin_domain() + '/api/' + 'pipeline/config/sources?facebook=true',
            method: 'GET',
            headers: {
                // 'Access-Control-Allow-Origin': '*',
                'x-cookie': document.cookie
            }
        }).done(function (data) {
            options.sources = options.sources.concat(data);
        })
        var textFBLogIn = facebookLoginBtn.html();
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        var FBError = getParameterByName('error_message');
        if (FBError) {
            FBalertsBody.html(FBError);
            FBalerts.addClass('card-danger').show();
        }


        if (!FBError) facebookLoginBtn.html('<i class="fa fa-spin fa-spinner"></i> '+lang("loading_facebook"));
        jQuery.ajax({
            url: get_boostapplogin_domain() + '/api/' + 'pipeline/config/pages',
            method: 'GET',
            headers: {
                // 'Access-Control-Allow-Origin': '*',
                'x-cookie': document.cookie
            }
        }).done(function (data) {
            options.pages = options.pages.concat(data);
            if (options.pages && options.pages.length && !FBError) {
                // jQuery(document).ready(function(){
                doFacebookLogin(function () {
                    renderDomAfterLogIn();
                })
                // })
                return;
            }
            facebookLoginBtn.html(textFBLogIn);
        })

        jQuery.ajax({
            url: get_boostapplogin_domain() + '/api/' + 'pipeline/config/states',
            method: 'GET',
            headers: {
                // 'Access-Control-Allow-Origin': '*',
                'x-cookie': document.cookie
            }
        }).done(function (data) {
            options.states = options.states.concat(data);
        })

        var loginOnce = false;
        function doFacebookLogin(callback) {
            FB.getLoginStatus(function (response) {
                if (response && response.status && response.status == 'connected') {
                    return renderDomAfterLogIn();
                }

                return doFBlogin(function () {
                    renderDomAfterLogIn()
                })

            })
        }


        function getPages(callback) {
            FB.api('/me/accounts',{
                "fields": "id,name,is_published,is_webhooks_subscribed,members,page_token,access_token",
                "limit": "30000"
            }, function (res) {
                console.log(res);
                window.boostappFBpages.data = res.data;
                if(!res || !res.data || !res.data.length){
                    facebookLogout.show();
                    FBalerts.hide();
                    facebookLoginBtn.hide();
                    FBalertsBody.html(FBalertsBody.html() + '<BR>');
                    FBalertsBody.append('<div class="text-center" dir="rtl"><img src="' + get_boostapplogin_domain() + '/assets/img/Logo.png" title="BOOSTAPP V.2.1"><br>' +lang('failed_to_get_the_pages_notice')+'</div>');
                    FBalerts.addClass('card-success').show();
                    return;
                }
                var done = 0;
                for (let index = 0; index < res.data.length; index++) {
                    const page = res.data[index];
                    (function (p) {
                        FB.api('/' + p.id + '/leadgen_forms', { fields: 'id, name', access_token: p.access_token }, (response) => {
                            // error error: {message: "Expected 1 '.' in the input between the postcard and the payload", type: "OAuthException", code: 190, fbtrace_id: "GMnPbyfDyaK"}
                            p.forms = response;
                            done++;
                            if (done == res.data.length) callback(res);
                        });
                    })(page)

                }
            })
        }


        function renderDomAfterLogIn() {
            facebookLoginBtn.html(textFBLogIn);

            FBalerts.hide();
            facebookLoginBtn.hide();
            facebookLogout.show();
            getPages(function (res) {
                var pages = res.data;

                if(!pages || !pages.length){
                    FBalertsBody.html(FBalertsBody.html() + '<BR>');
                    FBalertsBody.append('<div class="text-center">'+lang("landing_pages_not_found")+'</div>');
                    FBalerts.addClass('card-success').show();
                    return;
                }

                function updateBoostapp(page, changeStatus) {
                    // no need to disturbd boostapp API if lead not trying to register
                    if (!changeStatus && !page.is_webhooks_subscribed) return false;
                    jQuery.ajax({
                        method: 'POST',
                        // url: get_boostapplogin_domain() + '/api/' + 'facebook/page/register',
                        data: {
                            pageId: page.id,
                            cookie: document.cookie,
                            page: page,
                            Status: changeStatus ? !page.is_webhooks_subscribed : page.is_webhooks_subscribed,
                            StatusId: page.boostappStatus,
                            SourceName: page.boostappSource,
                            type: 'facebook/page/register'
                        }
                    })
                        .done(function (data, textStatus, jqXHR) {
                            // console.log(data);
                        });
                }


                function subscribeApp(page, el) {
                    var method = page.is_webhooks_subscribed ? 'delete' : 'post';
                    FB.api(
                        '/' + page.id + '/subscribed_apps',
                        page.is_webhooks_subscribed ? 'delete' : 'post',
                        { 'access_token': page.access_token, subscribed_fields: 'leadgen' },
                        function (res) {
                            if (res.error) {
                                console.log(res);
                                //var check_if_permitted_notice = $('html').attr('dir') == 'rtl' ? 'יש לבדוק האם למשתמש פייסבוק שלך יש את ההרשאות הנכונות לביצוע פעולה זו.' : 'please check if your facebook user has permissions for this kind of action.';
                                //var facebook_err = $('html').attr('dir') == 'rtl' ? 'השגיאה שהתקבלה מפייסבוק: ' : 'error from facebook: ';
                                if(!res.error.message){
                                    alert(lang('check_if_permitted_notice'));
                                }else{
                                    alert(lang('check_if_permitted_notice') + res.error.message);
                                }
                                
                                return false;
                            }

                            updateBoostapp(page, true);

                            var tr = $(el).closest('tr');
                            page.is_webhooks_subscribed = !page.is_webhooks_subscribed;
                            jQuery('td:eq(1)', tr).html(page.is_webhooks_subscribed ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>')
                            jQuery('td:eq(2) a', tr).html((page.is_webhooks_subscribed) ? lang('disconnect_leads') : lang('attach_leads')).attr('href', '#' + ((page.is_webhooks_subscribed) ? 'remove' : 'add') + '/' + page.id)
                        }
                    );
                }

                function showLog(page, el) {
                    jQuery.ajax({
                        method: 'GET',
                        url: get_boostapplogin_domain() + '/api/' + 'facebook/leads/page/' + page.id,
                        headers: {
                            // 'Access-Control-Allow-Origin': '*',
                            'x-cookie': document.cookie
                        }
                    }).done(function (data, textStatus, jqXHR) {
                        console.log(data);
                    })
                }


                var table = document.createElement('table');
                table.setAttribute('class', 'table table-striped');
                table.setAttribute('dir', 'rtl');
                var thead = document.createElement('thead');
                table.appendChild(thead);

                // col 1
                var col = document.createElement('th');
                col.innerHTML = lang('page_name_star');
                thead.appendChild(col);
                // col 2
                var col = document.createElement('th');
                col.setAttribute('class', 'text-center');
                col.innerHTML = lang('status_table');
                thead.appendChild(col);
                // col 3
                var col = document.createElement('th');
                col.innerHTML = lang('connect_disconnect');
                thead.appendChild(col);
                // col 4
                var col = document.createElement('th');
                col.innerHTML = lang('tools');
                thead.appendChild(col);

                var tbody = document.createElement('tbody');
                table.appendChild(tbody);

                for (let index = 0; index < pages.length; index++) {
                    var page = pages[index];
                    var pagedb = (function (page, options) {
                        var lookup = options.pages.filter(function (x) { return x.PageId.toString() == page.id.toString() });
                        if (!lookup || !lookup.length) return {}
                        return lookup[0];
                    })(page, options);

                    var tr = document.createElement('tr');
                    tr.setAttribute('data-id', page.id)
                    // col 1
                    var td = document.createElement('td');
                    td.innerHTML = page.name;
                    tr.appendChild(td);
                    // col 2
                    var td = document.createElement('td');
                    td.setAttribute('class', 'text-center');
                    td.innerHTML = (page.is_webhooks_subscribed) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
                    tr.appendChild(td);
                    // col 3
                    var td = document.createElement('td');
                    var a = document.createElement('a');
                    a.onclick = subscribeApp.bind(this, page, a);
                    a.setAttribute('href', '#' + ((page.is_webhooks_subscribed) ? 'remove' : 'add') + '/' + page.id);
                    a.innerHTML = ((page.is_webhooks_subscribed) ? lang('disconnect_leads') : lang('attach_leads'));
                    td.appendChild(a);
                    tr.appendChild(td);
                    // col 4
                    var td = document.createElement('td');
                    var settings = document.createElement('span');
                    settings.className = 'btn btn-outline-success';
                    settings.setAttribute('boostapp-settings-page-fb', page.id);
                    settings.innerHTML = '<i class="fa fa-cog"></i>';
                    settings.addEventListener('click', function (e) {
                        var pageId = this.getAttribute('boostapp-settings-page-fb');
                        var data = boostappFBpages.data.filter(function (x) {
                            return x.id.toString() === pageId
                        })[0];
                        // console.log(data.name, settingsModalPageName);
                        settingsModalPageName.html(data.name);
                        var bapiplines = JSON.parse(JSON.stringify(window.boostappFBpages.pipelines));

                        var pageSettings = window.boostappFBpages.pages.filter(function(x){return x.id.toString() === pageId});
                        var hasPageSettings = pageSettings.length ? true : false;
                        

                        var html = '<form>';
                        html += '<input type="hidden" name="pageId" value="'+pageId+'">';
                        html += '<div class="card mb-1 closed">';
                        html += '<div class="card-header bg-dark text-light">'+ lang('default_pipeline_for_forms') + '<span class="float-left"><i class="fas fa-angle-left"></i></span></div>';
                            html += '<div class="card-body">';
                                // default branch select
                                html += '<div class="form-group">';
                                    html += '<label>' + lang('branch')+ '</label>';
                                    html += '<select data-branch class="form-control" name="forms[default][branch]">';
                                    
                                    for (let index = 0; index < window.boostappFBpages.branches.length; index++) {
                                        const branch = window.boostappFBpages.branches[index];
                                        if(!branch.status) continue;

                                        if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings['default'] && pageSettings[0].settings['default'].branch){
                                            html += '<option value="' + branch.id + '" ' + (pageSettings[0].settings['default'].branch.toString() === branch.id.toString() ? 'selected' : '') + '>' + branch.name + '</option>'
                                        }else{
                                            html += '<option value="' + branch.id + '" ' + (branch.id === 0 ? 'selected' : '') + '>' + branch.name + '</option>'
                                        }
                                    }
                                    html += '</select>';
                                html += '</div>';
                                // default category select
                                html += '<div class="form-group">';
                                    html += '<label>' + lang('category_single') + '</label>';
                                    html += '<select data-categories class="form-control" name="forms[default][category]">';
                                    
                                    for (let index = 0; index < bapiplines.items.length; index++) {
                                        const category = bapiplines.items[index];
                                        if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings['default']){
                                            html += '<option value="' + category.id + '" ' + (pageSettings[0].settings['default'].category === category.id.toString() ? 'selected' : '') + '>' + category.name + '</option>'
                                        }else{
                                            html += '<option value="' + category.id + '" ' + (category.default ? 'selected' : '') + '>' + category.name + '</option>'
                                        }
                                    }
                                    html += '</select>';
                                html += '</div>';
                                // default status select
                                html += '<div class="form-group">';
                                    html += '<label>' + lang('status') +'</label>';
                                    html += '<select data-pipeline class="form-control" name="forms[default][pipeline]">';

                                    var pipeline;
                                    if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings.default){
                                        var check = bapiplines.items.filter(function(x){return x.id.toString() === pageSettings[0].settings.default.category.toString()});
                                        if(check.length){
                                            pipeline = check[0].values;
                                        }else{
                                            pipeline = bapiplines.items[0].values;
                                        }
                                    }else{
                                        pipeline = bapiplines.items[0].values;
                                    }

                                    for (let index = 0; index < pipeline.length; index++) {
                                        if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings['default']){
                                            html += '<option value="' + pipeline[index].id + '" ' + (pageSettings[0].settings['default'].pipeline === pipeline[index].id.toString() ? 'selected' : '') + '>' + pipeline[index].name + '</option>';
                                        }else{
                                            html += '<option value="' + pipeline[index].id + '" ' + (pipeline[index].default ? 'selected' : '') + '>' + pipeline[index].name + '</option>';
                                        }
                                        
                                    }
                                    html += '</select>';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';

                        

                        for (let index = 0; index < data.forms.data.length; index++) {
                            const form = data.forms.data[index];
                            html += '<div class="card mb-1 closed">';
                                html += '<div class="card-header">'+form.name+'<span class="float-left"><i class="fas fa-angle-down"></i></span></div>';
                                    html += '<div class="card-body d-none">';
                                        // form branch select
                                        html += '<div class="form-group">';
                                            html += '<label>'+lang('branch')+'</label>';
                                            html += '<select data-branch class="form-control" name="forms['+form.id+'][branch]">';
                                            
                                            for (let index = 0; index < window.boostappFBpages.branches.length; index++) {
                                                const branch = window.boostappFBpages.branches[index];
                                                if(!branch.status) continue;

                                                if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[form.id] && pageSettings[0].settings[form.id].branch){
                                                    html += '<option value="' + branch.id + '" ' + (pageSettings[0].settings[form.id].branch.toString() === branch.id.toString() ? 'selected' : '') + '>' + branch.name + '</option>'
                                                }else{
                                                    html += '<option value="' + branch.id + '" ' + (branch.id === 0 ? 'selected' : '') + '>' + branch.name + '</option>'
                                                }
                                            }
                                            html += '</select>';
                                        html += '</div>';

                                        html += '<div class="form-group">'
                                            html += '<label>'+lang('category_single')+'</label>';
                                            html += '<select data-categories class="form-control" name="forms['+form.id+'][category]">';
                                            for (let index = 0; index < bapiplines.items.length; index++) {
                                                const category = bapiplines.items[index];
                                                if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[form.id]){
                                                    html += '<option value="' + category.id + '" ' + (pageSettings[0].settings[form.id].category === category.id.toString() ? 'selected' : '') + '>' + category.name + '</option>'
                                                }else{
                                                    html += '<option value="' + category.id + '" ' + (category.default ? 'selected' : '') + '>' + category.name + '</option>'
                                                }   
                                            }
                                            html += '</select>';
                                        html += '</div>';
                                        html += '<div class="form-group">';
                                        html += '<label>'+ lang('status')+'</label>';
                                        html += '<select data-pipeline class="form-control" name="forms['+form.id+'][pipeline]">';

                                        var pipeline;
                                        if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[form.id]){
                                            var check = bapiplines.items.filter(function(x){return x.id.toString() === pageSettings[0].settings[form.id].category.toString()});
                                            if(check.length){
                                                pipeline = check[0].values;
                                            }else{
                                                pipeline = bapiplines.items[0].values;
                                            }
                                        }else{
                                            pipeline = bapiplines.items[0].values;
                                        }
                                        
                                        for (let index = 0; index < pipeline.length; index++) {
                                            if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[form.id]){
                                                html += '<option value="' + pipeline[index].id + '" ' + (pageSettings[0].settings[form.id].pipeline === pipeline[index].id.toString() ? 'selected' : '') + '>' + pipeline[index].name + '</option>';
                                            }else{
                                                html += '<option value="' + pipeline[index].id + '" ' + (pipeline[index].default ? 'selected' : '') + '>' + pipeline[index].name + '</option>';
                                            }
                                            
                                        }
                                        html += '</select>';
                                    html += '</div>';                                
                                html += '</div>';
                            html += '</div>';                                 
                        }

                        html += '</form>';     


                        settingsModalBody.html(html);
                        settingsModal.modal('show');
                    })

                    td.appendChild(settings);
                    tr.appendChild(td);

                    tbody.appendChild(tr);
                }


                FBalertsBody.html(FBalertsBody.html() + '<BR>');
                FBalertsBody.append(table);
                FBalerts.addClass('card-succuess').show();

            })
        }

        settingsModalBody.on('submit', 'form', function(e){
            e.preventDefault();
            var form = $(this);
            var data = form.serializeObject();


            jQuery.ajax({
                method: 'PUT',
                url: get_boostapplogin_domain() + '/api/' + 'facebook/page/register/' + data.pageId,
                headers: {
                    // 'Access-Control-Allow-Origin': '*',
                    'x-cookie': document.cookie,
                    "Content-Type": "application/json"
                },
                dataType: 'json',
                data: JSON.stringify(data.forms)
            }).done(function (data, textStatus, jqXHR) {
                getPagesFromBoostapp();
                settingsModal.modal('hide');
            })
        })

        settingsModalBody.on('change', 'select[data-categories]', function(e){
            var el = $(this);
            var pipeline = el.parent('div').next('div').find('select[data-pipeline]');
            var data = window.boostappFBpages.pipelines.items.filter(function(x){return x.id.toString() === el.val()});
            if(!data || !data.length) return alert(lang('error_no_info'));

            var html = '';
            for (let index = 0; index < data[0].values.length; index++) {
                const pipeline = data[0].values[index];
                html += '<option value="' + pipeline.id + '" ' + (pipeline.default ? 'selected' : undefined) + '>' + pipeline.name + '</option>'
            }

            pipeline.html(html);
        });

        settingsModalBody.on('click', '.card .card-header', function(e){
            var el = $(this);
            var svg = el.find('span svg');
            console.log(svg);
            svg.hasClass('.fa-angle-left') ? 
                svg.attr('class', function(i, className){return (className.replace('fa-angle-left', '')) + ' fa-angle-down'}) :
                svg.attr('class', function(i, className){return (className.replace('fa-angle-down', '')) + ' fa-angle-left'});
            el.next('.card-body').toggleClass('d-none');
        })

        doFBlogin = function (callback) {
            var scopes = 'email,business_management,pages_manage_ads,pages_manage_metadata,pages_read_engagement,pages_read_user_content,leads_retrieval,ads_management,pages_show_list,public_profile';
            FB.login(function (response) {
                FBalerts.hide();
                if (!response && !response.authResponse) {
                    FBalertsBody.html(lang('failed_to_coonect_account'));
                    FBalerts.addClass('card-danger').show();
                    return;
                }
                facebookLoginBtn.hide();
                callback();
            }, { scope: scopes })
        }

        loginFB = function () {
            doFacebookLogin(function () {
                renderDomAfterLogIn();
            });
        }

        facebookLoginBtn.on('click', loginFB);
    })(FB, jQuery);
};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));