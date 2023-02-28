  $(document).ready(function () {
       //for hiding top bar on mobile while scroll :: begin
          var prevScrollpos = window.pageYOffset;
          if (screen.width < 768) {
            window.onscroll = function() {
            var currentScrollPos = window.pageYOffset;
            let h = document.documentElement.scrollTop + window.innerHeight;
              if (prevScrollpos > currentScrollPos && h + 20 < document.body.scrollHeight || window.pageYOffset <= 0) {
                // scroll up
                if( $(".js-header-primary-menu").hasClass("slideInDown") == false){ 
                  $(".js-header-primary-menu").removeClass("light-animated slideOutUp").addClass("light-animated slideInDown");
                }  
              } else {
                // scroll down
                if( $(".js-header-primary-menu").hasClass("slideOutUp") == false){ 
                  $(".js-header-primary-menu").removeClass("light-animated slideInDown").addClass("light-animated slideOutUp");
                }
              }
              prevScrollpos = currentScrollPos;
            }
          }  
       //for hiding top bar on mobile while scroll :: end 
       var sidebar_tooltip = 'right';
       if( $("html").attr("dir")=='rtl'){
          sidebar_tooltip = 'left';
        }

              
       $('[data-toggle="tooltip"]').tooltip()
       $(".js-md-menu .js-toggle-visibility").show();
          
       if($(".js-toggle-visibility").hasClass("bsapp-shown") == false ){
          $(".bsapp-menu-item a").tooltip({
              placement : sidebar_tooltip,
              trigger : "hover",
              boundary: 'window' 
          })
       }
       $(".js-md-menu-show").on("click", function () {
           if ($(this).hasClass("is-active") == false) {
               $(this).addClass("is-active");
               $(".js-md-menu").css("width", "250px");
               $(".js-md-menu .js-toggle-visibility").addClass("bsapp-shown");
               $(".js-main-section").addClass("bsapp-shrink");
               $(".js-header-primary-menu").addClass("bsapp-shrink");
               setTimeout(function(){
                  $(".js-md-menu .js-toggle-visibility").show();
               },500)
               
               
                $(".bsapp-menu-item a").tooltip('dispose');
              //save user sidebar preference in localstorage :: begin
                //window.localStorage.setItem("js_sidebar_opened", "yes");
                $.cookie('js_sidebar_opened', 'yes',{ path: '/' });
              //save user sidebar preference in localstorage :: end
               
             
           } else {
               $(this).removeClass("is-active");
               $(".js-md-menu").css("width", "60px");
               $(".js-header-primary-menu").removeClass("bsapp-shrink");
               $(".js-main-section").removeClass("bsapp-shrink");
               $(".js-md-menu .js-toggle-visibility").removeClass("bsapp-shown").hide();
               //save user sidebar preference in localstorage :: begin
                //window.localStorage.setItem("js_sidebar_opened", "no");
                $.cookie('js_sidebar_opened', 'no',{ path: '/' });
              //save user sidebar preference in localstorage :: end
              
              $(".bsapp-menu-item a").tooltip({
                  placement : sidebar_tooltip,
                  trigger : "hover",
                  boundary: 'window' 
              })

           }

          
          
          //resize the scheduler  example :: /office/Cal.php :: begin
          if ( typeof(scheduler) == "object" ){
             console.log("view-updated")
             setTimeout(function(){
                scheduler.setCurrentView();
               },500)
            
          }
          //resize the scheduler  example :: /office/Cal.php :: end

          $(".js-slim-scroll").removeClass("bsapp-scroll-temp-css");
          
       })

        //trigger user sidebar preference saved in localstorage :: begin
       /*if( window.localStorage.getItem("js_sidebar_opened") == "no"){
           $(".js-md-menu-show").trigger("click")
       }*/
       
        //trigger user sidebar preference saved in localstorage :: begin

       $(".js-close-sm-menu").on("click", function () {
           $("body").removeClass("overflow-hidden");
           $(".js-sm-menu").css("width","0px");
   
       });
       $(".js-sm-menu-show").on("click", function () { 
               $(".js-sm-menu").css("width", "100%"); 
               $("body").addClass("overflow-hidden");
       })
   
   
       $("body").on("click", ".js-close-collapse", function () {
           $("#" + $(this).parents(".collapse").attr("id")).collapse("hide");
       })
   
       $("body").on("click", ".js-hide-search", function () {
            /* $(".js-search-minified").removeClass("bsapp-search-collapsed");*/
             $("body").removeClass("overflow-hidden");
            $(".js-sm-search-screen").css("width", "0"); 
   
       })
       $("body").on("click", ".js-show-search", function () {
           // $(".js-search-minified").addClass("bsapp-search-collapsed");
           $(".js-sm-search-screen").css("width", "100%"); 
           $("body").addClass("overflow-hidden");
           $(".js-search-scroll").find(".tt-input").focus();
           $(".js-search-scroll").find(".tt-input").click();
       })

      
      $(".js-slim-scroll").slimScroll({
            height: '100%' 
      });

       $("#home-boost").on('click', function() {
        location.href = '/office/';
      });
      $(".js-translation").on('click', function() {
          let code = $(this).attr("data-code");
          let dir = $(this).attr("data-rtl");
          $.cookie('boostapp_lang', code,{ path: '/' });
          $.cookie('boostapp_dir', dir,{ path: '/' });
          translate(code);
      });
      // $("#trans-eng").on('click', function() {
      //   let dir = $("html").attr("dir");
      //   if (dir == 'rtl') {
      //     $.cookie('boostapp_lang', 'eng',{ path: '/' });
      //     translate("he");
      //     // location.reload();
      //   }
      // });
      //
      // $("#trans-heb").on('click', function() {
      //   let dir = $("html").attr("dir");
      //   if (dir == '' || dir == null) {
      //     $.cookie('boostapp_lang', 'heb',{ path: '/' });
      //     translate("en");
      //     // location.reload();
      //   }
      // });

      // $('#js-translate').on('click', function() {
      //   $.post({
      //     url: '/lang.php'
      //   });
      //   location.reload();
      // });

      function translate(lang) {
        let data = {
          lang: lang
        };
        $.post({
          url: '/lang.php',
          data: data,
          type: 'POST'
        }).done(function() {
          location.reload();
        });
      }
     
  


        //code for search bar :: begin 

        /* content of demo json is of the form 
          {"results":[{"name":"aaaaaasssa","url":"http://google.com"},{"name":"Abcdeg","url":"http://google.com"}]}
        */

        //for {{img}} please pass a dummy image instead of null

        const users_data = new Bloodhound({
            datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
              wildcard: '%QUERY',
              url: '/office/action/getClientsJson.php?query=%QUERY',
              // Map the remote source JSON array to a JavaScript object array
              transform: response => $.map(response.results, user => ({
                name: user.name,
                url : user.url,
                email: user.email,
                img: user.img,
                phone: user.phone,
                brand: user.brand,
                status : user.status
              }))
            }
          });


        function clientSuggestionsWithDefaults(q, sync, async) {
            if (q === '') {                   // if query is empty, show default suggestions
                sync([]);
            } else {
                /* countries_suggestions is the bloodhound instance
                   as we used in the previous example */
                users_data.search(q, sync, async);
            }
        }

          // {hint: true, highlight: true, minLength: 1}
          // Instantiate the Typeahead UI
          $('#js-clientsSearch .typeahead').typeahead({
            hint: true, 
            highlight: true,
            minLength: 0, 
            showHintOnFocus: true
          },
            {
            display: 'name',
            name: 'remote-data',
            source: clientSuggestionsWithDefaults,
            limit: Infinity,
             templates: {
              empty: [
                '<div class="empty-message px-15 py-15">',
                  'לא נמצא לקוח',
                '</div>'
              ].join('\n'),
              //suggestion: Handlebars.compile('<div class="text-start position-relative">{{name}}<a class="stretched-link" href="{{url}}"></a></div></div>')
              suggestion: Handlebars.compile('<div class="d-flex  text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6> {{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div><a class="stretched-link" href="{{url}}"></a></div>')
            }
          }, { 
              display : "name",
              name : "default-data",
              limit :5,
               source: function(query, callback) {
                 var saved_searches ;
                 if( query == ""){
                     saved_searches = JSON.parse(localStorage.getItem('js_selected_history'));
                    }else{
                      saved_searches = [];
                    }  
                          callback(saved_searches);

                      },
                      templates: {
                  empty: [
                    ''
                  ].join('\n'),
                  suggestion: Handlebars.compile('<div class="d-flex  text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6> {{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div><a class="stretched-link" href="{{url}}"></a></div>')
                }
            }).on('typeahead:selected', function (e, datum){
                if( window.localStorage.getItem("js_selected_history") == null ){
                  var data_array = new Array();
                  data_array.unshift(datum);
                  window.localStorage.setItem("js_selected_history" , JSON.stringify(data_array));
                }else{
                  var saved_searches = JSON.parse(localStorage.getItem('js_selected_history')); 
                  saved_searches.unshift(datum);
                  saved_searches.length = 5;
                  window.localStorage.setItem("js_selected_history" , JSON.stringify(saved_searches));
                } 
                window.location.replace(datum.url);
            });

            $("body").on("focus","#js-clientsSearch  .typeahead",function(){
                $(this).parent(".twitter-typeahead").width("250px");
            })

             $("body").on("focusout","#js-clientsSearch  .typeahead",function(){
                $(this).parent(".twitter-typeahead").width("200px");
            })

          //code for search bar :: end 

          const company_data = new Bloodhound({
            datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.name),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
              wildcard: '%QUERY',
              url: '/office/action/CompanyNumSelect.php?q=%QUERY',
              // Map the remote source JSON array to a JavaScript object array
              transform: response => $.map(response.results, company => ({
                id: company.id,
                name : company.name,
                logo: company.logo
              }))
            }
          });

          // Instantiate the Typeahead UI
          $('#js-studiosSearch .typeahead').typeahead({hint: true, highlight: true, minLength: 2,menu : $(".js-studio-searches")}, {
            display: 'name',
            name: 'name',
            source: company_data,
            limit: Infinity,
             templates: {
              empty: [
                '<div class="empty-message px-15">',
                  'לא נמצא סטודיו',
                '</div>'
              ].join('\n'),
              //suggestion: Handlebars.compile('<div class="text-start position-relative">{{name}} :: {{id}}<a class="stretched-link" href="{{id}}"></a></div></div>')
              suggestion: Handlebars.compile('<a href="javascript:;" class="list-group-item d-flex align-items-center py-10"><img class="w-30p h-30p rounded-circle mie-8" src="{{logo}}"/> <div class="d-flex flex-column"><span>{{name}}</span><span>{{id}}</span></div></a>')
            }
          }).on('typeahead:selected', function (e, datum) {
            data = {
              'CompanyNum': datum.id,
              'action': 'SupportChangeCompanyNum' 
            }
            $.ajax({
              url: BeePOS.options.ajaxUrl,
              type: 'POST',
              data: data
            }).done( function(response) {
              location.reload();
            });
        });  


          //mobile client search :: begin 
            const mobile_users_data = new Bloodhound({
              datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
              queryTokenizer: Bloodhound.tokenizers.whitespace,
              remote: {
                wildcard: '%QUERY',
                url: '/office/action/getClientsJson.php?query=%QUERY',
                // Map the remote source JSON array to a JavaScript object array
                transform: response => $.map(response.results, user => ({
                  name: user.name,
                  url : user.url,
                  email: user.email,
                  img: user.img,
                  phone: user.phone,
                  brand: user.brand,
                  status : user.status
                }))
              }
            });

             function clientSuggestionsSMWithDefaults(q, sync, async) {
                  if (q === '') {                   // if query is empty, show default suggestions
                      sync([]);
                  } else {
                      /* countries_suggestions is the bloodhound instance
                         as we used in the previous example */
                      mobile_users_data.search(q, sync, async);
                  }
              }

            // {hint: true, highlight: true, minLength: 1}
            // Instantiate the Typeahead UI
            $('.js-sm-typeahead-client').typeahead({
               hint: true,
               highlight: true,
               minLength: 0 ,
               showHintOnFocus: true,
               menu : $(".js-sm-client-searches")}, 
               {
              display: 'name',
              name: 'remote-data',
              source: clientSuggestionsSMWithDefaults,
              limit: Infinity,
               templates: {
                empty: [
                  '<div class="empty-message px-15 py-15">',
                    'לא נמצא לקוח',
                  '</div>'
                ].join('\n'),
                //suggestion: Handlebars.compile('<div class="text-start position-relative">{{name}}<a class="stretched-link" href="{{url}}"></a></div></div>')
                suggestion: Handlebars.compile('<div class="d-flex  text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6> {{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div><a class="stretched-link" href="{{url}}"></a></div>')
              }
            },{ 
              display : "name",
              name : "default-data",
              limit :5,
               source: function(query, callback) {
                     if( query == ""){
                     saved_searches = JSON.parse(localStorage.getItem('js_selected_history'));
                    }else{
                      saved_searches = [];
                    } 
                          callback(saved_searches);
                      },
                      templates: {
                  empty: [
                    ''
                  ].join('\n'),
                  suggestion: Handlebars.compile('<div class="d-flex  text-start position-relative px-6 rounded border-bottom border-light py-10" > <div class="position-relative"><img src="{{img}}" class="bsapp-image"> <div class="bsapp-status-icon {{status}}"></div> </div><div class="d-flex flex-column pis-10"> <h6>{{name}}</h6> {{#if phone}}<div><i class="fal fa-phone"></i> {{phone}}</div>{{/if}}  {{#if email}} <div><i class="fal fa-envelope"></i> {{email}}</div>{{/if}}  {{#if brand}}<div><i class="fal fa-location-circle"></i> {{brand}}</div>{{/if}}</div><a class="stretched-link" href="{{url}}"></a></div>')
                }
            }

            ).on('typeahead:selected', function (e, datum) {
                if( window.localStorage.getItem("js_selected_history") == null ){
                  var data_array = new Array();
                  data_array.unshift(datum);
                  window.localStorage.setItem("js_selected_history" , JSON.stringify(data_array));
                }else{
                  var saved_searches = JSON.parse(localStorage.getItem('js_selected_history')); 
                  saved_searches.unshift(datum);
                  saved_searches.length = 5;
                  window.localStorage.setItem("js_selected_history" , JSON.stringify(saved_searches));
                } 
                 
                 window.location.replace(datum.url);
            });

          //mobile client search :: end

          //mobile studio search :: begin
           const mobile_company_data = new Bloodhound({
            datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.name),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
              wildcard: '%QUERY',
              url: '/office/action/CompanyNumSelect.php?q=%QUERY',
              // Map the remote source JSON array to a JavaScript object array
              transform: response => $.map(response.results, company => ({
                id: company.id,
                name : company.name,
              }))
            }
          });

          // Instantiate the Typeahead UI
          $('.js-sm-studio-search').typeahead({hint: true, highlight: true, minLength: 2,menu : $(".js-sm-studio-searches")}, {
            display: 'name',
            name: 'name',
            source: mobile_company_data,
            limit: Infinity,
             templates: {
              empty: [
                '<div class="empty-message px-15">',
                  'לא נמצא סטודיו',
                '</div>'
              ].join('\n'),
              //suggestion: Handlebars.compile('<div class="text-start position-relative">{{name}} :: {{id}}<a class="stretched-link" href="{{id}}"></a></div></div>')
              suggestion: Handlebars.compile('<a href="javascript:;" class="list-group-item d-flex align-items-center py-10"><img class="w-30p h-30p rounded-circle mie-8" src="{{logo}}"/> <div class="d-flex flex-column"><span>{{name}}</span><span>{{id}}</span></div></a>')
            }
          }).on('typeahead:selected', function (e, datum) {
            data = {
              'CompanyNum': datum.id,
              'action': 'SupportChangeCompanyNum' 
            }
            $.ajax({
              url: BeePOS.options.ajaxUrl,
              type: 'POST',
              data: data
            }).done( function(response) {
              location.reload();
            });
          });

      //mobile studio search :: end

      $("body").on("click", ".js-close-collapse", function () {
          //$("." + $(this).attr("data-attr")).html("")
          $("." + $(this).attr("data-search")).val("")
      })
      $("body").on("click", ".js-hide-search", function () {
          //$("." + $(this).attr("data-attr")).html("")
          $("." + $(this).attr("data-search")).val("")
      });
      //code for search bar :: end

      $(".js-switch-brand").on('click', function () {
          var brand_id = $(this).attr("data-id");
          data = {
              BrandId: brand_id
          }
          $.ajax({
              url: '/office/action/UpdateBrandSelected.php',
              type: 'POST',
              data: data,
              success: function (response) {
                  if (response == "brand not found") {
                      alert("לא נמצא סניף");
                  } else {
                      location.reload();
                  }
              },
              error: function (response) {

              }
          })
      });

      $(".js-switch-multiuser").click(function () {
          const multiUserId = $(this).attr("data-id");
          const data = {
              action: 'switchMultiUser',
              multiUserId: multiUserId
          };

          $.ajax({
              url: '/office/ajax/login/login.php',
              type: 'POST',
              data: data,
              success: function (response) {
                  if (response.status !== 200) {
                      alert("לא נמצא סניף");
                  } else {
                      location.reload();
                  }
              },
              error: function (response) {

              }
          })
      });

    
   });  //document.ready closes here 

  function hashCode(str) { // java String#hashCode
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
       hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    return hash;
  }

