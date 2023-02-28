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

window.fbAsyncInit = function () {
    FB.init({
        appId: '1931387196922899',
        autoLogAppEvents: true,
        xfbml: true,
        version: 'v14.0',
        status: true
    });

};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

const dropdown_disconnect = '<i class="fal  fa-link fa-fw mx-5"></i> <span>'+lang('disconnect_leads')+'</span>',
    dropdown_connect = '<i class="fal fa-link fa-fw mx-5"></i> <span>'+ lang('attach_leads') +'</span>';

const FBFunction = {
    goToFBConnect: function () {

        var facebookLoginBtn = $('.js-facebook-disconnected #facebookLoginbtnWrapper>a.btn-facebook');
        const FBListPage = $('.js-facebook-connected .facebook-pages-list');
        var facebookLogout = $('#facebookLogout');
        var facebookLogoutBtn = $('a', facebookLogout);
        FBFunction.showLogin();

        facebookLogoutBtn.on('click', function () {
            FB.logout();

            // reset data
            FBListPage.find('li:not(.item-example)').remove();
            FBListPage.find('.item-loading').removeClass('d-none');
            $('.js-facebook-connected .js-item-title-page').addClass('d-none');

            FBFunction.showLogin();
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
            FBFunction.showError(FBError);
        }


        if (!FBError) facebookLoginBtn.html(`<i class="fab fa-facebook-f"></i> ${lang("loading_facebook")} <div class="spinner-border spinner-border-md" role="status"></div>`);
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
                    FBFunction.showError(lang('failed_to_get_the_pages_notice'));
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
            FBFunction.showBody();
            getPages(function (res) {
                var pages = res.data;

                if(!pages || !pages.length){
                    FBFunction.showError(lang("landing_pages_not_found"));
                    return;
                }

                function updateBoostapp(page, changeStatus) {
                    // no need to disturbd boostapp API if lead not trying to register
                    if (!changeStatus && !page.is_webhooks_subscribed) return false;
                    jQuery.ajax({
                        method: 'POST',
                        url: get_boostapplogin_domain() + '/office/ajax/ManageLeadsSettings.php',
                        data: {
                            pageId: page.id,
                            cookie: document.cookie,
                            page: page,
                            Status: changeStatus ? !page.is_webhooks_subscribed : page.is_webhooks_subscribed,
                            StatusId: page.boostappStatus,
                            SourceName: page.boostappSource,
                            fun: 'facebook/page/register'
                        }
                    })
                        .done(function (data, textStatus, jqXHR) {
                            // console.log(data);
                        });
                }


                function subscribeApp(el, fromOnePage = false) {
                    const pageId = el.data('id') ?? '';
                    const page = window.boostappFBpages.data.filter(function (x) {
                        return x.id.toString() === pageId.toString()
                    })[0];
                    let inputInOnePage;
                    if(fromOnePage){
                        inputInOnePage = el;
                        el = $(`.js-facebook-connected .facebook-pages-list .js-fields[data-id=${pageId}]`);
                    }
                    const textStatusElement = el.find(".js-item-text-status:first");
                    const inputStatusElement = el.find(".js-item-status input:first");
                    if(inputStatusElement.is(":disabled")){
                        return
                    } else {
                        inputStatusElement.prop("disabled", true);
                        textStatusElement.prop("disabled", true);
                        if(fromOnePage) inputInOnePage.prop("disabled", true);
                    }

                    let newStatus = !page.is_webhooks_subscribed ? dropdown_disconnect : dropdown_connect;
                    inputStatusElement.prop("checked", !page.is_webhooks_subscribed);
                    textStatusElement.empty();
                    textStatusElement.prepend(newStatus);
                    if(fromOnePage) inputInOnePage.prop("checked", !page.is_webhooks_subscribed);

                    if(!!page) {
                        FB.api(
                            '/' + page.id + '/subscribed_apps',
                            page.is_webhooks_subscribed ? 'delete' : 'post',
                            { 'access_token': page.access_token, subscribed_fields: 'leadgen' },
                            function (res) {
                                if (res.error) {
                                    console.log(res);

                                    newStatus = page.is_webhooks_subscribed ? dropdown_disconnect : dropdown_connect;
                                    inputStatusElement.prop("checked", page.is_webhooks_subscribed);
                                    textStatusElement.empty();
                                    textStatusElement.prepend(newStatus);
                                    if(fromOnePage) inputInOnePage.prop("checked", page.is_webhooks_subscribed);
                                    inputStatusElement.prop("disabled", false);
                                    textStatusElement.prop("disabled", false);
                                    if(fromOnePage) inputInOnePage.prop("disabled", false);

                                    if(!res.error.message){
                                        FBFunction.showAlertError(lang('check_if_permitted_notice'));
                                    }else{
                                        FBFunction.showAlertError(lang('check_if_permitted_notice') + res.error.message);
                                    }

                                    return false;
                                }

                                updateBoostapp(page, true);

                                page.is_webhooks_subscribed = !page.is_webhooks_subscribed;

                                inputStatusElement.prop("disabled", false);
                                textStatusElement.prop("disabled", false);
                                if(fromOnePage) inputInOnePage.prop("disabled", false);
                            }
                        );
                    }
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


                FBListPage.find('.item-loading').addClass('d-none');
                $('.js-facebook-connected .js-item-title-page').removeClass('d-none');
                $('.js-facebook-connected  .facebook-pages-list .js-fields:not(.item-example)').remove();
                for (let index = 0; index < pages.length; index++) {
                    var page = pages[index];
                    const switchId = Math.random();
                    const hide_btn = page.is_webhooks_subscribed ? dropdown_disconnect : dropdown_connect;
                    const elementClone = $('.js-facebook-connected  .facebook-pages-list .js-fields.item-example').clone();
                    elementClone.removeClass("d-none item-example").attr("data-id", page.id);
                    elementClone.find(".js-item-name:first").text(page.name);

                    elementClone.find(".js-item-status input:first").attr("id", `js-switch-id-${page.name}-${switchId}`).prop("checked", page.is_webhooks_subscribed);
                    elementClone.find(".js-item-status label:first").attr("for", `js-switch-id-${page.name}-${switchId}`);
                    elementClone.find(".js-item-text-status:first").prepend(hide_btn);

                    elementClone.find(".js-item-text-status:first").on('click', function (){
                        subscribeApp($(this).closest('.js-fields'));
                    })
                    elementClone.find(".js-item-status input:first").on('click', function (){
                        subscribeApp($(this).closest('.js-fields'));
                    })

                    elementClone.find('.js-item-go-one-page:first').on('click', function (event) {
                        $(".js-facebook-details-page .js-item-status-page input:first").attr("data-id", elementClone.data("id"));
                        $(".js-facebook-details-page .js-item-status-page input:first").on('click', function (){
                            subscribeApp($(this), true);
                        })
                        FBFunction.goPageFB(this, event);
                    })

                    FBListPage.append(elementClone);
                }
            })
        }

        doFBlogin = function (callback) {
            var scopes = 'email,business_management,pages_manage_ads,pages_manage_metadata,pages_read_engagement,pages_read_user_content,leads_retrieval,ads_management,pages_show_list,public_profile';
            FB.login(function (response) {
                if (!response || !response.authResponse) {
                    facebookLoginBtn.html(textFBLogIn);
                    return;
                }
                callback();
            }, { scope: scopes })
        }

        loginFB = function () {
            doFacebookLogin(function () {
                renderDomAfterLogIn();
            });
        }

        facebookLoginBtn.on('click', loginFB);
},
    openCard:function (elem) {
        const parent = $(elem).parent('div');
        const open = parent.find('.js-card-data-page:first');
        if(open.hasClass('d-none')) open.removeClass('d-none');
        else open.addClass('d-none');
    },
    goPageFB: function (elem) {
        const pageId = `${$(elem).closest('.js-fields').data('id')}`;
        const pageSettings =  window.boostappFBpages.pages.filter(function(x){return x.id.toString() === pageId});
        const hasPageSettings = pageSettings.length ? true : false;
        const data = boostappFBpages.data.filter(function (x) {
            return x.id.toString() === pageId
        })[0];
        const bapiplines = JSON.parse(JSON.stringify(window.boostappFBpages.pipelines));

        const switchId = Math.random();
        $('.js-facebook-details-page .js-item-page-id').val(pageId);
        $(".js-facebook-details-page .js-item-title-page:first").text(data.name);
        $(".js-facebook-details-page .js-item-status-page input:first").attr("id", `js-switch-id-${data.name}-${switchId}`).prop("checked", data.is_webhooks_subscribed);
        $(".js-facebook-details-page .js-item-status-page label:first").attr("for", `js-switch-id-${data.name}-${switchId}`);

        // enter data to default routing
        const firstElement = $('.js-facebook-details-page .js-default-routing-fb');
        const selectBranch = firstElement.find('.js-item-branch select:first');
        selectBranch.find('option').remove();
        this.addOptionFB(selectBranch, window.boostappFBpages.branches, 'branch', 'default', pageSettings);

        const selectPipeLine = firstElement.find('.js-item-name-pipe-line select:first');
        selectPipeLine.find('option').remove();
        this.addOptionFB(selectPipeLine, bapiplines.items, 'category', 'default', pageSettings);

        let pipeline;
        if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings.default){
            const check = bapiplines.items.filter(function(x){return x.id.toString() === pageSettings[0].settings.default.category.toString()});
            if(check.length){
                pipeline = check[0].values;
            }else{
                pipeline = bapiplines.items.find(p=> p.status == STATUS_ON).values;
            }
        }else{
            pipeline = bapiplines.items.find(p=> p.status == STATUS_ON).values;
        }
        const selectLeadStatus = firstElement.find('.js-item-lead-status select:first');
        selectLeadStatus.find('option').remove();
        this.addOptionFB(selectLeadStatus, pipeline, 'pipeline', 'default', pageSettings);

        // enter data to all forms
        $(".js-facebook-details-page .js-one-form-fb:not(.js-item-example)").remove();
        for (let index = 0; index < data.forms.data.length; index++) {
            const form = data.forms.data[index];
            const elementClone = $('.js-facebook-details-page .js-one-form-fb.js-item-example').clone();
            elementClone.removeClass('js-item-example d-none');
            elementClone.find('.js-name-form-fb span:first').text(form.name);
            elementClone.find('.js-card-data-page:first').attr("data-id", form.id);
            elementClone.find('.js-card-data-page select').addClass('js-select2');

            const selectBranch = elementClone.find('.js-item-branch select:first');
            this.addOptionFB(selectBranch, window.boostappFBpages.branches, 'branch', form.id, pageSettings);

            const selectPipeLine = elementClone.find('.js-item-name-pipe-line select:first');
            this.addOptionFB(selectPipeLine, bapiplines.items, 'category', form.id, pageSettings);

            let pipeline;
            if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[form.id]){
                var check = bapiplines.items.filter(function(x){return x.id.toString() === pageSettings[0].settings[form.id].category.toString()});
                if(check.length){
                    pipeline = check[0].values;
                }else{
                    pipeline = bapiplines.items.find(p=> p.status == STATUS_ON).values;
                }
            }else{
                pipeline = bapiplines.items.find(p=> p.status == STATUS_ON).values;
            }
            const selectLeadStatus = elementClone.find('.js-item-lead-status select:first');
            this.addOptionFB(selectLeadStatus, pipeline, 'pipeline', form.id, pageSettings);

            $('.js-facebook-details-page .js-list-forms').append(elementClone);
        }
        $(".js-facebook-details-page .js-one-form-fb select.js-select2").select2({theme: 'bsapp-dropdown'});
        $(".js-facebook-details-page select.js-select2[name='branch']").select2({
            theme: 'bsapp-dropdown',
            placeholder: "סניף ראשי",
            allowClear: true
        });
        LeadsSettings.goTo(elem);

        // update scrollTop
        setTimeout(function () {
            if($('.js-facebook-details-page .scrollable').scrollTop() != '0')
                $('.js-facebook-details-page .scrollable').animate({
                    scrollTop: 0
                }, 1000);
        }, 300);
    },
    addOptionFB:function (currSelect, arrayData, type, formId, pageSettings){
        const hasPageSettings = pageSettings.length ? true : false;
        let html = '', selected, valueSelected;
        for (let index = 0; index < arrayData.length; index++) {
            selected = false;
            const item = arrayData[index];
            if(item.status == STATUS_ON) {
                html += '<option value="' + item.id + '" >' + item.name + '</option>'
            }

            // check if selected, save valueSelected
            if(hasPageSettings && pageSettings[0] && pageSettings[0].settings && pageSettings[0].settings[formId] && pageSettings[0].settings[formId][type]){
                selected = pageSettings[0].settings[formId][type].toString() === item.id.toString();
                if(selected && item.status == STATUS_OFF){
                    html += '<option value="' + item.id + '">' + item.name + '</option>'
                }
            }else if(item.status == STATUS_ON){
                selected = type == 'branch' ? item.id === 0 : item.default;
            }
            if(selected)
                valueSelected = item.id;
        }
        if(type == 'branch' && !valueSelected && valueSelected != 0){
            html = '<option></option>' + html;
        }else if(!valueSelected){
            valueSelected = arrayData.find(a=> a.status == STATUS_ON).id;
        }
        currSelect.attr('name', type);
        currSelect.append(html);
        if(valueSelected) {
            currSelect.val(`${valueSelected}`);
            currSelect.trigger('change');
        }
    },
    changePipeLineFB: function(elem){
        let elementAddOptions = $(elem).closest('.js-card-data-page');
        if(elementAddOptions.length == 0 ) elementAddOptions = $(elem).closest('.js-default-routing-fb');

        const selectLeadStatus = elementAddOptions.find('.js-item-lead-status select:first');
        if(selectLeadStatus.find('option').length == 0) return

        const data = window.boostappFBpages.pipelines.items.filter(function(x){return x.id.toString() === $(elem).val()});
        if(!data || !data.length) return this.showAlertError(lang('error_no_info'));
        selectLeadStatus.find('option').remove();
        const formId = elementAddOptions.data('id') ?? 'default';
        this.addOptionFB(selectLeadStatus, data[0].values, 'pipeline', formId, []);
    },
    afterGetDataFromBoostapp: function (data) {
        $(document).ready(function () {
            window.boostappFBpages.pipelines = data.response.pipelines;
            window.boostappFBpages.pages = data.response.pages;
            window.boostappFBpages.branches = data.response.branches;
        });
        FBFunction.goToFBConnect();
    },
    getPagesFromBoostapp:function (){
        const apiProps = {
            fun: "getPagesFacebookFromBoostapp",
        }
        postApi('manageLeadsSettings', apiProps, 'FBFunction.afterGetPagesFromBoostapp', true);
    },
    afterGetPagesFromBoostapp: function (data) {
            window.boostappFBpages.pages = data.response;
    },
    saveFBPage: function (elem, event) {
        let forms = {}
        $('.js-facebook-details-page .js-one-form-fb:not(.js-item-example) .js-card-data-page').each(function () {
            const formId = $(this).data('id');
            let currData = $(this).find(':input').serializeObject();
            if(currData.branch == '') currData.branch = '0';
            forms[`${formId}`] = currData;
        });
        forms['default'] = $('.js-facebook-details-page .js-default-routing-fb :input').serializeObject()
        if(forms['default'].branch == '') forms['default'].branch = '0'
        const pageId = $('.js-facebook-details-page form :input.js-item-page-id').val();
        const data = { pageId, forms }

        console.log("data: ", data);
        event.preventDefault();

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
            FBFunction.getPagesFromBoostapp();
        })

        LeadsSettings.goTo(elem);
    },
    showAlertError: function (message){
        $.notify({
            message: message ?? lang('error_oops_something_went_wrong')
        }, {
            type: 'danger',
            z_index: 2000,
        });
    },
    showError: function (message) {
        $('.js-facebook-error').text(message);
        $('.js-facebook-error').removeClass('d-none').addClass('d-flex');
        $('.js-facebook-connected').addClass('d-none');
        $('.js-facebook-loading').addClass('d-none').removeClass('d-flex');
    },
    showLogin: function () {
        $('.js-facebook-error').addClass('d-none').removeClass('d-flex');
        $('.js-facebook-loading').addClass('d-none').removeClass('d-flex');
        $('.js-facebook-disconnected #facebookLoginbtnWrapper>a.btn-facebook').show();
        $('.js-facebook-disconnected').removeClass('d-none').addClass('d-flex');
        $('.js-facebook-connected').addClass('d-none');
        $('#facebookLogout').hide();
    },
    showBody: function () {
        $('.js-facebook-loading').addClass('d-none').removeClass('d-flex');
        $('.js-facebook-disconnected #facebookLoginbtnWrapper>a.btn-facebook').hide();
        $('.js-facebook-disconnected').addClass('d-none').removeClass('d-flex');
        $('.js-facebook-connected').removeClass('d-none');
        $('#facebookLogout').show();
    },
    showLoading: function (){
        $('.js-facebook-loading').removeClass('d-none');
        $('.js-facebook-disconnected #facebookLoginbtnWrapper>a.btn-facebook').hide();
        $('.js-facebook-disconnected').addClass('d-none').removeClass('d-flex');
        $('.js-facebook-connected').addClass('d-none');
        $('#facebookLogout').hide();
    }
}