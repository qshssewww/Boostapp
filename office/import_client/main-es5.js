function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["main"], {
  /***/
  "./src/$$_lazy_route_resource lazy recursive":
  /*!**********************************************************!*\
    !*** ./src/$$_lazy_route_resource lazy namespace object ***!
    \**********************************************************/

  /*! no static exports found */

  /***/
  function src$$_lazy_route_resourceLazyRecursive(module, exports) {
    function webpackEmptyAsyncContext(req) {
      // Here Promise.resolve().then() is used instead of new Promise() to prevent
      // uncaught exception popping up in devtools
      return Promise.resolve().then(function () {
        var e = new Error("Cannot find module '" + req + "'");
        e.code = 'MODULE_NOT_FOUND';
        throw e;
      });
    }

    webpackEmptyAsyncContext.keys = function () {
      return [];
    };

    webpackEmptyAsyncContext.resolve = webpackEmptyAsyncContext;
    module.exports = webpackEmptyAsyncContext;
    webpackEmptyAsyncContext.id = "./src/$$_lazy_route_resource lazy recursive";
    /***/
  },

  /***/
  "./src/app/Service/api-service.service.ts":
  /*!************************************************!*\
    !*** ./src/app/Service/api-service.service.ts ***!
    \************************************************/

  /*! exports provided: ApiServiceService */

  /***/
  function srcAppServiceApiServiceServiceTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ApiServiceService", function () {
      return ApiServiceService;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_common_http__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/common/http */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/http.js");
    /* harmony import */


    var _environments_environment__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ../../environments/environment */
    "./src/environments/environment.ts");

    var ApiServiceService =
    /*#__PURE__*/
    function () {
      function ApiServiceService(http) {
        _classCallCheck(this, ApiServiceService);

        this.http = http;
        this.BASE_URL = _environments_environment__WEBPACK_IMPORTED_MODULE_2__["environment"].BASE_URL;
      }

      _createClass(ApiServiceService, [{
        key: "addNewColumn",
        value: function addNewColumn(body) {
          return this.http.post(this.BASE_URL + 'office/rest/addClientColumn.php', body);
        }
      }, {
        key: "getColumns",
        value: function getColumns() {
          return this.http.get(this.BASE_URL + 'office/rest/getClientColumns.php');
        }
      }, {
        key: "insertActive",
        value: function insertActive(body) {
          return this.http.post(this.BASE_URL + 'office/rest/insertActiveClients.php', body);
        }
      }, {
        key: "insertArchive",
        value: function insertArchive(body) {
          return this.http.post(this.BASE_URL + 'office/rest/insertArchiveClient.php', body);
        }
      }, {
        key: "insertLead",
        value: function insertLead(body) {
          return this.http.post(this.BASE_URL + 'office/rest/insertLeadsClients.php', body);
        }
      }]);

      return ApiServiceService;
    }();

    ApiServiceService.ɵfac = function ApiServiceService_Factory(t) {
      return new (t || ApiServiceService)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵinject"](_angular_common_http__WEBPACK_IMPORTED_MODULE_1__["HttpClient"]));
    };

    ApiServiceService.ɵprov = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineInjectable"]({
      token: ApiServiceService,
      factory: ApiServiceService.ɵfac,
      providedIn: 'root'
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](ApiServiceService, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Injectable"],
        args: [{
          providedIn: 'root'
        }]
      }], function () {
        return [{
          type: _angular_common_http__WEBPACK_IMPORTED_MODULE_1__["HttpClient"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/Service/updatedata.service.ts":
  /*!***********************************************!*\
    !*** ./src/app/Service/updatedata.service.ts ***!
    \***********************************************/

  /*! exports provided: UpdatedataService */

  /***/
  function srcAppServiceUpdatedataServiceTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "UpdatedataService", function () {
      return UpdatedataService;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var rxjs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! rxjs */
    "./node_modules/rxjs/_esm2015/index.js");

    var UpdatedataService =
    /*#__PURE__*/
    function () {
      function UpdatedataService() {
        _classCallCheck(this, UpdatedataService);

        this.columnData = new rxjs__WEBPACK_IMPORTED_MODULE_1__["BehaviorSubject"](null);
        this.genderData = new rxjs__WEBPACK_IMPORTED_MODULE_1__["BehaviorSubject"](null);
        this.getEmailData = new rxjs__WEBPACK_IMPORTED_MODULE_1__["BehaviorSubject"](null);
        this.getSmsData = new rxjs__WEBPACK_IMPORTED_MODULE_1__["BehaviorSubject"](null);
        this.headersList = new rxjs__WEBPACK_IMPORTED_MODULE_1__["BehaviorSubject"](null);
      }

      _createClass(UpdatedataService, [{
        key: "updateSelectColumnData",
        value: function updateSelectColumnData(data) {
          this.columnData.next(data);
        }
      }, {
        key: "updateSelectGenderData",
        value: function updateSelectGenderData(data) {
          this.genderData.next(data);
        }
      }, {
        key: "updateHeadersList",
        value: function updateHeadersList(data) {
          this.headersList.next(data);
        }
      }, {
        key: "updateGetEmailList",
        value: function updateGetEmailList(data) {
          this.getEmailData.next(data);
        }
      }, {
        key: "updateGetSmsList",
        value: function updateGetSmsList(data) {
          this.getSmsData.next(data);
        }
      }]);

      return UpdatedataService;
    }();

    UpdatedataService.ɵfac = function UpdatedataService_Factory(t) {
      return new (t || UpdatedataService)();
    };

    UpdatedataService.ɵprov = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineInjectable"]({
      token: UpdatedataService,
      factory: UpdatedataService.ɵfac,
      providedIn: 'root'
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](UpdatedataService, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Injectable"],
        args: [{
          providedIn: 'root'
        }]
      }], function () {
        return [];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/app.component.ts":
  /*!**********************************!*\
    !*** ./src/app/app.component.ts ***!
    \**********************************/

  /*! exports provided: AppComponent */

  /***/
  function srcAppAppComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AppComponent", function () {
      return AppComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _shared_spinner_component__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ./shared/spinner.component */
    "./src/app/shared/spinner.component.ts");

    var AppComponent = function AppComponent() {
      _classCallCheck(this, AppComponent);
    };

    AppComponent.ɵfac = function AppComponent_Factory(t) {
      return new (t || AppComponent)();
    };

    AppComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: AppComponent,
      selectors: [["app-root"]],
      decls: 2,
      vars: 0,
      template: function AppComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "router-outlet");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](1, "app-spinner");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
        }
      },
      directives: [_angular_router__WEBPACK_IMPORTED_MODULE_1__["RouterOutlet"], _shared_spinner_component__WEBPACK_IMPORTED_MODULE_2__["SpinnerComponent"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL2FwcC5jb21wb25lbnQuY3NzIn0= */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AppComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-root',
          templateUrl: './app.component.html',
          styleUrls: ['./app.component.css']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/app.module.ts":
  /*!*******************************!*\
    !*** ./src/app/app.module.ts ***!
    \*******************************/

  /*! exports provided: AppModule */

  /***/
  function srcAppAppModuleTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AppModule", function () {
      return AppModule;
    });
    /* harmony import */


    var _angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/platform-browser */
    "./node_modules/@angular/platform-browser/__ivy_ngcc__/fesm2015/platform-browser.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _angular_forms__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_common_http__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! @angular/common/http */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/http.js");
    /* harmony import */


    var _app_routing__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! ./app.routing */
    "./src/app/app.routing.ts");
    /* harmony import */


    var _app_component__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! ./app.component */
    "./src/app/app.component.ts");
    /* harmony import */


    var _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(
    /*! @angular/flex-layout */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex-layout.js");
    /* harmony import */


    var _layouts_full_full_component__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(
    /*! ./layouts/full/full.component */
    "./src/app/layouts/full/full.component.ts");
    /* harmony import */


    var _layouts_full_header_header_component__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(
    /*! ./layouts/full/header/header.component */
    "./src/app/layouts/full/header/header.component.ts");
    /* harmony import */


    var _layouts_full_sidebar_sidebar_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(
    /*! ./layouts/full/sidebar/sidebar.component */
    "./src/app/layouts/full/sidebar/sidebar.component.ts");
    /* harmony import */


    var _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(
    /*! @angular/platform-browser/animations */
    "./node_modules/@angular/platform-browser/__ivy_ngcc__/fesm2015/animations.js");
    /* harmony import */


    var _demo_material_module__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(
    /*! ./demo-material-module */
    "./src/app/demo-material-module.ts");
    /* harmony import */


    var ngx_loading__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(
    /*! ngx-loading */
    "./node_modules/ngx-loading/__ivy_ngcc__/fesm2015/ngx-loading.js");
    /* harmony import */


    var _fileupload_fileupload_component__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(
    /*! ./fileupload/fileupload.component */
    "./src/app/fileupload/fileupload.component.ts");
    /* harmony import */


    var _shared_shared_module__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(
    /*! ./shared/shared.module */
    "./src/app/shared/shared.module.ts");
    /* harmony import */


    var _shared_spinner_component__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(
    /*! ./shared/spinner.component */
    "./src/app/shared/spinner.component.ts");

    var AppModule = function AppModule() {
      _classCallCheck(this, AppModule);
    };

    AppModule.ɵmod = _angular_core__WEBPACK_IMPORTED_MODULE_2__["ɵɵdefineNgModule"]({
      type: AppModule,
      bootstrap: [_app_component__WEBPACK_IMPORTED_MODULE_7__["AppComponent"]]
    });
    AppModule.ɵinj = _angular_core__WEBPACK_IMPORTED_MODULE_2__["ɵɵdefineInjector"]({
      factory: function AppModule_Factory(t) {
        return new (t || AppModule)();
      },
      providers: [{
        provide: _angular_common__WEBPACK_IMPORTED_MODULE_1__["LocationStrategy"],
        useClass: _angular_common__WEBPACK_IMPORTED_MODULE_1__["PathLocationStrategy"]
      }],
      imports: [[_angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__["BrowserModule"], _angular_common__WEBPACK_IMPORTED_MODULE_1__["CommonModule"], _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_12__["BrowserAnimationsModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_13__["DemoMaterialModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["FormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_5__["HttpClientModule"], _shared_shared_module__WEBPACK_IMPORTED_MODULE_16__["SharedModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["ReactiveFormsModule"], ngx_loading__WEBPACK_IMPORTED_MODULE_14__["NgxLoadingModule"].forRoot({}), _angular_router__WEBPACK_IMPORTED_MODULE_3__["RouterModule"].forRoot(_app_routing__WEBPACK_IMPORTED_MODULE_6__["AppRoutes"])]]
    });

    (function () {
      (typeof ngJitMode === "undefined" || ngJitMode) && _angular_core__WEBPACK_IMPORTED_MODULE_2__["ɵɵsetNgModuleScope"](AppModule, {
        declarations: [_app_component__WEBPACK_IMPORTED_MODULE_7__["AppComponent"], _layouts_full_full_component__WEBPACK_IMPORTED_MODULE_9__["FullComponent"], _layouts_full_header_header_component__WEBPACK_IMPORTED_MODULE_10__["AppHeaderComponent"], _shared_spinner_component__WEBPACK_IMPORTED_MODULE_17__["SpinnerComponent"], _layouts_full_sidebar_sidebar_component__WEBPACK_IMPORTED_MODULE_11__["AppSidebarComponent"], _fileupload_fileupload_component__WEBPACK_IMPORTED_MODULE_15__["FileuploadComponent"], _fileupload_fileupload_component__WEBPACK_IMPORTED_MODULE_15__["DialogOverviewExampleDialog"]],
        imports: [_angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__["BrowserModule"], _angular_common__WEBPACK_IMPORTED_MODULE_1__["CommonModule"], _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_12__["BrowserAnimationsModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_13__["DemoMaterialModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["FormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_5__["HttpClientModule"], _shared_shared_module__WEBPACK_IMPORTED_MODULE_16__["SharedModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["ReactiveFormsModule"], ngx_loading__WEBPACK_IMPORTED_MODULE_14__["NgxLoadingModule"], _angular_router__WEBPACK_IMPORTED_MODULE_3__["RouterModule"]]
      });
    })();
    /*@__PURE__*/


    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_2__["ɵsetClassMetadata"](AppModule, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_2__["NgModule"],
        args: [{
          declarations: [_app_component__WEBPACK_IMPORTED_MODULE_7__["AppComponent"], _layouts_full_full_component__WEBPACK_IMPORTED_MODULE_9__["FullComponent"], _layouts_full_header_header_component__WEBPACK_IMPORTED_MODULE_10__["AppHeaderComponent"], _shared_spinner_component__WEBPACK_IMPORTED_MODULE_17__["SpinnerComponent"], _layouts_full_sidebar_sidebar_component__WEBPACK_IMPORTED_MODULE_11__["AppSidebarComponent"], _fileupload_fileupload_component__WEBPACK_IMPORTED_MODULE_15__["FileuploadComponent"], _fileupload_fileupload_component__WEBPACK_IMPORTED_MODULE_15__["DialogOverviewExampleDialog"]],
          imports: [_angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__["BrowserModule"], _angular_common__WEBPACK_IMPORTED_MODULE_1__["CommonModule"], _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_12__["BrowserAnimationsModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_13__["DemoMaterialModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["FormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_5__["HttpClientModule"], _shared_shared_module__WEBPACK_IMPORTED_MODULE_16__["SharedModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_4__["ReactiveFormsModule"], ngx_loading__WEBPACK_IMPORTED_MODULE_14__["NgxLoadingModule"].forRoot({}), _angular_router__WEBPACK_IMPORTED_MODULE_3__["RouterModule"].forRoot(_app_routing__WEBPACK_IMPORTED_MODULE_6__["AppRoutes"])],
          providers: [{
            provide: _angular_common__WEBPACK_IMPORTED_MODULE_1__["LocationStrategy"],
            useClass: _angular_common__WEBPACK_IMPORTED_MODULE_1__["PathLocationStrategy"]
          }],
          bootstrap: [_app_component__WEBPACK_IMPORTED_MODULE_7__["AppComponent"]]
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/app.routing.ts":
  /*!********************************!*\
    !*** ./src/app/app.routing.ts ***!
    \********************************/

  /*! exports provided: AppRoutes */

  /***/
  function srcAppAppRoutingTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AppRoutes", function () {
      return AppRoutes;
    });
    /* harmony import */


    var _layouts_full_full_component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! ./layouts/full/full.component */
    "./src/app/layouts/full/full.component.ts");

    var AppRoutes = [{
      path: '',
      component: _layouts_full_full_component__WEBPACK_IMPORTED_MODULE_0__["FullComponent"],
      children: [{
        path: '',
        redirectTo: '/fileupload',
        pathMatch: 'full'
      }, {
        path: '',
        loadChildren: function loadChildren() {
          return __webpack_require__.e(
          /*! import() | material-component-material-module */
          "material-component-material-module").then(__webpack_require__.bind(null,
          /*! ./material-component/material.module */
          "./src/app/material-component/material.module.ts")).then(function (m) {
            return m.MaterialComponentsModule;
          });
        }
      }, {
        path: 'dashboard',
        loadChildren: function loadChildren() {
          return Promise.all(
          /*! import() | dashboard-dashboard-module */
          [__webpack_require__.e("default~dashboard-dashboard-module~fileupload-fileupload-module"), __webpack_require__.e("dashboard-dashboard-module")]).then(__webpack_require__.bind(null,
          /*! ./dashboard/dashboard.module */
          "./src/app/dashboard/dashboard.module.ts")).then(function (m) {
            return m.DashboardModule;
          });
        }
      }, {
        path: 'fileupload',
        loadChildren: function loadChildren() {
          return Promise.all(
          /*! import() | fileupload-fileupload-module */
          [__webpack_require__.e("default~dashboard-dashboard-module~fileupload-fileupload-module"), __webpack_require__.e("fileupload-fileupload-module")]).then(__webpack_require__.bind(null,
          /*! ./fileupload/fileupload.module */
          "./src/app/fileupload/fileupload.module.ts")).then(function (m) {
            return m.FileuploadModule;
          });
        }
      }, {
        path: '**',
        redirectTo: '/dashboard'
      }]
    }];
    /***/
  },

  /***/
  "./src/app/demo-material-module.ts":
  /*!*****************************************!*\
    !*** ./src/app/demo-material-module.ts ***!
    \*****************************************/

  /*! exports provided: DemoMaterialModule */

  /***/
  function srcAppDemoMaterialModuleTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DemoMaterialModule", function () {
      return DemoMaterialModule;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");
    /* harmony import */


    var _angular_cdk_table__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/cdk/table */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/table.js");
    /* harmony import */


    var _angular_cdk_accordion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/cdk/accordion */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/accordion.js");
    /* harmony import */


    var _angular_cdk_a11y__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/cdk/a11y */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/a11y.js");
    /* harmony import */


    var _angular_cdk_bidi__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! @angular/cdk/bidi */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/bidi.js");
    /* harmony import */


    var _angular_cdk_overlay__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! @angular/cdk/overlay */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/overlay.js");
    /* harmony import */


    var _angular_cdk_platform__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! @angular/cdk/platform */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/platform.js");
    /* harmony import */


    var _angular_cdk_observers__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(
    /*! @angular/cdk/observers */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/observers.js");
    /* harmony import */


    var _angular_cdk_portal__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(
    /*! @angular/cdk/portal */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/portal.js");
    /**
     * @license
     * Copyright Google LLC All Rights Reserved.
     *
     * Use of this source code is governed by an MIT-style license that can be
     * found in the LICENSE file at https://angular.io/license
     */

    /**
     * NgModule that includes all Material modules that are required to serve the demo-app.
     */


    var DemoMaterialModule = function DemoMaterialModule() {
      _classCallCheck(this, DemoMaterialModule);
    };

    DemoMaterialModule.ɵmod = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineNgModule"]({
      type: DemoMaterialModule
    });
    DemoMaterialModule.ɵinj = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineInjector"]({
      factory: function DemoMaterialModule_Factory(t) {
        return new (t || DemoMaterialModule)();
      },
      imports: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatAutocompleteModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCheckboxModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatChipsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTableModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDatepickerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormFieldModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatGridListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIconModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInputModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatMenuModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatPaginatorModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressSpinnerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRadioModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRippleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSelectModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSidenavModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSlideToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSliderModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSnackBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSortModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatStepperModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTabsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatToolbarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTooltipModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatNativeDateModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_2__["CdkTableModule"], _angular_cdk_a11y__WEBPACK_IMPORTED_MODULE_4__["A11yModule"], _angular_cdk_bidi__WEBPACK_IMPORTED_MODULE_5__["BidiModule"], _angular_cdk_accordion__WEBPACK_IMPORTED_MODULE_3__["CdkAccordionModule"], _angular_cdk_observers__WEBPACK_IMPORTED_MODULE_8__["ObserversModule"], _angular_cdk_overlay__WEBPACK_IMPORTED_MODULE_6__["OverlayModule"], _angular_cdk_platform__WEBPACK_IMPORTED_MODULE_7__["PlatformModule"], _angular_cdk_portal__WEBPACK_IMPORTED_MODULE_9__["PortalModule"]]
    });

    (function () {
      (typeof ngJitMode === "undefined" || ngJitMode) && _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵsetNgModuleScope"](DemoMaterialModule, {
        exports: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatAutocompleteModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCheckboxModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatChipsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTableModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDatepickerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormFieldModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatGridListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIconModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInputModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatMenuModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatPaginatorModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressSpinnerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRadioModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRippleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSelectModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSidenavModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSlideToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSliderModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSnackBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSortModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatStepperModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTabsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatToolbarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTooltipModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatNativeDateModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_2__["CdkTableModule"], _angular_cdk_a11y__WEBPACK_IMPORTED_MODULE_4__["A11yModule"], _angular_cdk_bidi__WEBPACK_IMPORTED_MODULE_5__["BidiModule"], _angular_cdk_accordion__WEBPACK_IMPORTED_MODULE_3__["CdkAccordionModule"], _angular_cdk_observers__WEBPACK_IMPORTED_MODULE_8__["ObserversModule"], _angular_cdk_overlay__WEBPACK_IMPORTED_MODULE_6__["OverlayModule"], _angular_cdk_platform__WEBPACK_IMPORTED_MODULE_7__["PlatformModule"], _angular_cdk_portal__WEBPACK_IMPORTED_MODULE_9__["PortalModule"]]
      });
    })();
    /*@__PURE__*/


    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](DemoMaterialModule, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["NgModule"],
        args: [{
          exports: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatAutocompleteModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCheckboxModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatChipsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTableModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDatepickerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormFieldModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatGridListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIconModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInputModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatListModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatMenuModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatPaginatorModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatProgressSpinnerModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRadioModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatRippleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSelectModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSidenavModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSlideToggleModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSliderModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSnackBarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSortModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatStepperModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTabsModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatToolbarModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatTooltipModule"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatNativeDateModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_2__["CdkTableModule"], _angular_cdk_a11y__WEBPACK_IMPORTED_MODULE_4__["A11yModule"], _angular_cdk_bidi__WEBPACK_IMPORTED_MODULE_5__["BidiModule"], _angular_cdk_accordion__WEBPACK_IMPORTED_MODULE_3__["CdkAccordionModule"], _angular_cdk_observers__WEBPACK_IMPORTED_MODULE_8__["ObserversModule"], _angular_cdk_overlay__WEBPACK_IMPORTED_MODULE_6__["OverlayModule"], _angular_cdk_platform__WEBPACK_IMPORTED_MODULE_7__["PlatformModule"], _angular_cdk_portal__WEBPACK_IMPORTED_MODULE_9__["PortalModule"]]
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/fileupload/fileupload.component.ts":
  /*!****************************************************!*\
    !*** ./src/app/fileupload/fileupload.component.ts ***!
    \****************************************************/

  /*! exports provided: FileuploadComponent, DialogOverviewExampleDialog */

  /***/
  function srcAppFileuploadFileuploadComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "FileuploadComponent", function () {
      return FileuploadComponent;
    });
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DialogOverviewExampleDialog", function () {
      return DialogOverviewExampleDialog;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var ngx_papaparse__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ngx-papaparse */
    "./node_modules/ngx-papaparse/__ivy_ngcc__/fesm2015/ngx-papaparse.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/material/dialog */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/dialog.js");
    /* harmony import */


    var export_to_csv__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! export-to-csv */
    "./node_modules/export-to-csv/build/index.js");
    /* harmony import */


    var export_to_csv__WEBPACK_IMPORTED_MODULE_4___default =
    /*#__PURE__*/
    __webpack_require__.n(export_to_csv__WEBPACK_IMPORTED_MODULE_4__);
    /* harmony import */


    var _angular_forms__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _Service_api_service_service__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! ../Service/api-service.service */
    "./src/app/Service/api-service.service.ts");
    /* harmony import */


    var _Service_updatedata_service__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! ../Service/updatedata.service */
    "./src/app/Service/updatedata.service.ts");
    /* harmony import */


    var sweetalert2__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(
    /*! sweetalert2 */
    "./node_modules/sweetalert2/dist/sweetalert2.all.js");
    /* harmony import */


    var sweetalert2__WEBPACK_IMPORTED_MODULE_8___default =
    /*#__PURE__*/
    __webpack_require__.n(sweetalert2__WEBPACK_IMPORTED_MODULE_8__);
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");
    /* harmony import */


    var _angular_flex_layout_extended__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(
    /*! @angular/flex-layout/extended */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/extended.js");
    /* harmony import */


    var ngx_loading__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(
    /*! ngx-loading */
    "./node_modules/ngx-loading/__ivy_ngcc__/fesm2015/ngx-loading.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");
    /* harmony import */


    var _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(
    /*! @angular/flex-layout/flex */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex.js");
    /* harmony import */


    var _angular_material_core__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(
    /*! @angular/material/core */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/core.js");

    var __awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
      function adopt(value) {
        return value instanceof P ? value : new P(function (resolve) {
          resolve(value);
        });
      }

      return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) {
          try {
            step(generator.next(value));
          } catch (e) {
            reject(e);
          }
        }

        function rejected(value) {
          try {
            step(generator["throw"](value));
          } catch (e) {
            reject(e);
          }
        }

        function step(result) {
          result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
        }

        step((generator = generator.apply(thisArg, _arguments || [])).next());
      });
    };

    function FileuploadComponent_div_4_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 24);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "h5");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "1");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4, "Upload CSV");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "p");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, "Please click on 'Import CSV' button from given options (Active/Lead/Archive) to proceed further");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_5_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 24);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "h5");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "2");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4, "Map Attributes");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "p");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, "Please MAP CSV columns, If your CSV includes Gender/GetEmail/GetSms columns please choose the values");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_6_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 24);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "h5");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "3");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4, "Summary");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "p");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, "Import Report, if you find any data in rejected section that means data already exists in our systems or there is some issues of CSV, to know the issues and fix them you can download the CSV and reimport");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_7_button_1_Template(rf, ctx) {
      if (rf & 1) {
        var _r111 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "button", 28);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_7_button_1_Template_button_click_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r111);

          var ctx_r110 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r110.submitInsertArchiveApi();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r107 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r107.loading ? "Loading.." : "Insert");
      }
    }

    function FileuploadComponent_div_7_button_2_Template(rf, ctx) {
      if (rf & 1) {
        var _r113 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "button", 28);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_7_button_2_Template_button_click_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r113);

          var ctx_r112 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r112.submitInsertActiveApi();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r108 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r108.loading ? "Loading.." : "Insert Active Clients");
      }
    }

    function FileuploadComponent_div_7_button_3_Template(rf, ctx) {
      if (rf & 1) {
        var _r115 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "button", 28);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_7_button_3_Template_button_click_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r115);

          var ctx_r114 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r114.submitInsertLeadApi();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r109 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r109.loading ? "Loading.." : "Insert Leads");
      }
    }

    function FileuploadComponent_div_7_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 26);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, FileuploadComponent_div_7_button_1_Template, 2, 1, "button", 27);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, FileuploadComponent_div_7_button_2_Template, 2, 1, "button", 27);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](3, FileuploadComponent_div_7_button_3_Template, 2, 1, "button", 27);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r92 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r92.insertCsvFile === "archive");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r92.insertCsvFile === "active");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r92.insertCsvFile === "lead");
      }
    }

    function FileuploadComponent_i_17_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](0, "i", 29);
      }
    }

    function FileuploadComponent_ng_template_18_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span", 30);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, "1");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_i_23_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](0, "i", 29);
      }
    }

    function FileuploadComponent_ng_template_24_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span", 30);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, "2");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_i_29_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](0, "i", 29);
      }
    }

    function FileuploadComponent_ng_template_30_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span", 30);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, "3");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_34_Template(rf, ctx) {
      if (rf & 1) {
        var _r117 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 31);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "mat-card");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](2, "i", 32);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "h4");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4, "Active Clients");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "p", 33);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, " Customers of your studio, You are able to charge these customers and add any subscription type, send them any type of communication and retain all of their information. The cost of the service is based on the number of active customers. ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](7, "div", 34);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "input", 35);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("change", function FileuploadComponent_div_34_Template_input_change_8_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r116 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r116.changeListener($event);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](9, "button", 36);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_34_Template_button_click_9_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r118 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r118.selectFile("active");
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](10, "Import CSV");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](11, "mat-card");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](12, "i", 37);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](13, "h4");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](14, "Lead");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](15, "p", 33);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](16, " List of interested contacts, this list is imported into the pipeline, You are able to add trial subscription and send them marketing communication and retain all of their data. The cost of the service is not affected by the number of Leads in the system. ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](17, "div", 34);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](18, "input", 38);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("change", function FileuploadComponent_div_34_Template_input_change_18_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r119 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r119.changeListener($event);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](19, "button", 36);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_34_Template_button_click_19_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r120 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r120.selectFile("lead");
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](20, "Import CSV");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](21, "mat-card");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](22, "i", 39);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](23, "h4");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](24, "Archive");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](25, "p", 33);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](26, " Customers that have been removed from your Active contacts and Leads lists,You won\u2019t be able to add any subscription type to archived customers, You are able to send marketing communication and retain all of their data. The cost of the service is not affected by the number of archived contacts. ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](27, "div", 34);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](28, "input", 40);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("change", function FileuploadComponent_div_34_Template_input_change_28_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r121 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r121.changeListener($event);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](29, "button", 36);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_34_Template_button_click_29_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r117);

          var ctx_r122 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r122.selectFile("archive");
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](30, "Import CSV");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_37_div_1_span_10_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span", 54);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, "done");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_37_div_1_div_20_li_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "p", 56);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r133 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        var ik_r129 = ctx_r133.index;
        var headData_r128 = ctx_r133.$implicit;

        var i_r125 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]().index;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", ik_r129 + 1, " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", headData_r128[i_r125], "");
      }
    }

    function FileuploadComponent_div_37_div_1_div_20_span_2_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "strong");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ik_r129 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]().index;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("+", ik_r129 - 4, "");
      }
    }

    function FileuploadComponent_div_37_div_1_div_20_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, FileuploadComponent_div_37_div_1_div_20_li_1_Template, 5, 2, "li", 55);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, FileuploadComponent_div_37_div_1_div_20_span_2_Template, 3, 1, "span", 55);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ik_r129 = ctx.index;
        var isLast_r130 = ctx.last;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ik_r129 < 5);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", isLast_r130 && ik_r129 >= 5);
      }
    }

    function FileuploadComponent_div_37_div_1_Template(rf, ctx) {
      if (rf & 1) {
        var _r137 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 43);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "div", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "div", 44);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "div", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "ul", 45);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "button", 46);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_37_div_1_Template_button_click_6_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r137);

          var headersVal_r124 = ctx.$implicit;
          var i_r125 = ctx.index;

          var ctx_r136 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r136.openDialog(headersVal_r124, i_r125);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "mat-icon");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](9, "arrow_drop_down");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](10, FileuploadComponent_div_37_div_1_span_10_Template, 2, 0, "span", 47);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](11, "div", 48);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](12, "button", 49);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_37_div_1_Template_button_click_12_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r137);

          var headersVal_r124 = ctx.$implicit;

          var ctx_r138 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r138.clearColumn(headersVal_r124);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](13, "span", 50);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](14, "Clear");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](15, "span", 51);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](16, "backspace");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](17, "div", 52);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](18, "h4");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](19);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](20, FileuploadComponent_div_37_div_1_div_20_Template, 3, 2, "div", 53);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var headersVal_r124 = ctx.$implicit;
        var i_r125 = ctx.index;

        var ctx_r123 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngClass", ctx_r123.ifHeaderSet(headersVal_r124));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngClass", ctx_r123.getUnique(headersVal_r124));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", ctx_r123.showSelectedHeader(headersVal_r124, i_r125 + 1), " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r123.checkMapingValue(headersVal_r124));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", headersVal_r124, ":");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r123.csvData);
      }
    }

    function FileuploadComponent_div_37_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 41);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, FileuploadComponent_div_37_div_1_Template, 21, 6, "div", 42);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r103 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r103.headersList);
      }
    }

    function FileuploadComponent_div_38_div_1_div_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 61);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r141 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("Total Inserted Rows: ", ctx_r141.insertedData.length, "");
      }
    }

    function FileuploadComponent_div_38_div_1_div_2_button_2_Template(rf, ctx) {
      if (rf & 1) {
        var _r145 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "button", 64);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_div_38_div_1_div_2_button_2_Template_button_click_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r145);

          var ctx_r144 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](4);

          return ctx_r144.rejectedDownload();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](1, "i", 65);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, " Download Rejected CSV ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_38_div_1_div_2_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 62);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, FileuploadComponent_div_38_div_1_div_2_button_2_Template, 3, 0, "button", 63);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r142 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", ctx_r142.rejectedData.length, " rows rejected ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r142.rejectedData.length);
      }
    }

    function FileuploadComponent_div_38_div_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 16);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, FileuploadComponent_div_38_div_1_div_1_Template, 2, 1, "div", 59);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, FileuploadComponent_div_38_div_1_div_2_Template, 3, 2, "div", 60);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r139 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r139.insertedData.length);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r139.rejectedData.length);
      }
    }

    function FileuploadComponent_div_38_div_2_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 16);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "div", 66);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, "Error Importing data / CSV Not valid");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function FileuploadComponent_div_38_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 57);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, FileuploadComponent_div_38_div_1_Template, 3, 2, "div", 58);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, FileuploadComponent_div_38_div_2_Template, 3, 0, "div", 58);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r104 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", !ctx_r104.errorInResponse);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r104.errorInResponse);
      }
    }

    function FileuploadComponent_ng_template_39_Template(rf, ctx) {}

    var _c0 = function _c0(a0) {
      return {
        "activeStep": a0
      };
    };

    var _c1 = function _c1() {
      return {
        backdropBorderRadius: "3px"
      };
    };

    function DialogOverviewExampleDialog_div_8_li_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "p");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r160 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        var i_r156 = ctx_r160.index;
        var headersVal_r155 = ctx_r160.$implicit;

        var ctx_r158 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", i_r156 + 1, ".");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("\xA0\xA0", headersVal_r155[ctx_r158.data.indx], "");
      }
    }

    function DialogOverviewExampleDialog_div_8_span_2_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "strong");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var i_r156 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]().index;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("+", i_r156 - 5, "");
      }
    }

    function DialogOverviewExampleDialog_div_8_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, DialogOverviewExampleDialog_div_8_li_1_Template, 5, 2, "li", 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, DialogOverviewExampleDialog_div_8_span_2_Template, 3, 1, "span", 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var i_r156 = ctx.index;
        var isLast_r157 = ctx.last;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", i_r156 < 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", isLast_r157 && i_r156 >= 6);
      }
    }

    function DialogOverviewExampleDialog_mat_form_field_10_mat_option_5_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-option", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var allavailableHeader_r165 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", allavailableHeader_r165.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", allavailableHeader_r165.show, " ");
      }
    }

    function DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_6_mat_option_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-option", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var member_r167 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", member_r167.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", member_r167.show, " ");
      }
    }

    function DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_6_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-optgroup", 15);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_6_mat_option_1_Template, 2, 2, "mat-option", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r163 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r163.memberShipHeader);
      }
    }

    function DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_7_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-optgroup", 16);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "mat-option", 17);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, "Create new field");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function DialogOverviewExampleDialog_mat_form_field_10_Template(rf, ctx) {
      if (rf & 1) {
        var _r169 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-form-field");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "mat-label");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, "Select an option");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "mat-select", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("valueChange", function DialogOverviewExampleDialog_mat_form_field_10_Template_mat_select_valueChange_3_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r169);

          var ctx_r168 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r168.selectColumn($event);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "mat-optgroup", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](5, DialogOverviewExampleDialog_mat_form_field_10_mat_option_5_Template, 2, 2, "mat-option", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](6, DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_6_Template, 2, 1, "mat-optgroup", 12);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](7, DialogOverviewExampleDialog_mat_form_field_10_mat_optgroup_7_Template, 3, 0, "mat-optgroup", 13);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r147 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", ctx_r147.data && ctx_r147.data.selectedColumn && ctx_r147.data.selectedColumn.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r147.data.availableHeader);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r147.memberShipHeader.length > 0);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r147.data.currImportType !== "archive");
      }
    }

    function DialogOverviewExampleDialog_div_13_mat_form_field_1_mat_error_4_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-error");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, " Column name is ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "strong");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "required");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function DialogOverviewExampleDialog_div_13_mat_form_field_1_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-form-field", 22);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "mat-label");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, "Field Name");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](3, "input", 23);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](4, DialogOverviewExampleDialog_div_13_mat_form_field_1_mat_error_4_Template, 4, 0, "mat-error", 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r170 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("errorStateMatcher", ctx_r170.matcher);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r170.addColumn.controls.col_name.hasError("required"));
      }
    }

    function DialogOverviewExampleDialog_div_13_mat_radio_button_5_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-radio-button", 24);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpipe"](2, "titlecase");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var season_r174 = ctx.$implicit;

        var ctx_r171 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("disabled", !ctx_r171.showInput)("value", season_r174.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpipeBind1"](2, 3, season_r174.name), " ");
      }
    }

    function DialogOverviewExampleDialog_div_13_mat_error_6_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-error");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1, " Datatype is ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "strong");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "required");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    function DialogOverviewExampleDialog_div_13_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](1, DialogOverviewExampleDialog_div_13_mat_form_field_1_Template, 5, 2, "mat-form-field", 18);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "label", 19);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3, "With the data Type of");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "mat-radio-group", 20);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](5, DialogOverviewExampleDialog_div_13_mat_radio_button_5_Template, 3, 5, "mat-radio-button", 21);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](6, DialogOverviewExampleDialog_div_13_mat_error_6_Template, 4, 0, "mat-error", 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r148 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r148.showInput);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r148.dataType);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r148.showInput && ctx_r148.addColumn.controls.datatype.hasError("required"));
      }
    }

    function DialogOverviewExampleDialog_div_14_div_5_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](0, "div", 28);
      }
    }

    function DialogOverviewExampleDialog_div_14_Template(rf, ctx) {
      if (rf & 1) {
        var _r177 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "button", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_14_Template_button_click_1_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r177);

          var ctx_r176 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r176.closeDialog();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](2, "Cancel");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "button", 26);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](5, DialogOverviewExampleDialog_div_14_div_5_Template, 1, 0, "div", 27);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r149 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("disabled", ctx_r149.loading);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r149.loading ? "Loading.." : "Confirm");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx_r149.loading);
      }
    }

    function DialogOverviewExampleDialog_div_16_div_2_mat_option_9_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-option", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var yesNo_r182 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", yesNo_r182.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", yesNo_r182.name, " ");
      }
    }

    function DialogOverviewExampleDialog_div_16_div_2_Template(rf, ctx) {
      if (rf & 1) {
        var _r184 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "mat-form-field");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "mat-label");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7, "Select an option");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "mat-select", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("valueChange", function DialogOverviewExampleDialog_div_16_div_2_Template_mat_select_valueChange_8_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r184);

          var data_r179 = ctx.$implicit;

          var ctx_r183 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r183.selectYesNo($event, data_r179, "getemail");
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](9, DialogOverviewExampleDialog_div_16_div_2_mat_option_9_Template, 2, 2, "mat-option", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var data_r179 = ctx.$implicit;
        var i_r180 = ctx.index;

        var ctx_r178 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", i_r180 + 1, ".");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("\xA0\xA0", data_r179, " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", ctx_r178.preSelectionGetEmail(data_r179));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r178.yesNo);
      }
    }

    function DialogOverviewExampleDialog_div_16_Template(rf, ctx) {
      if (rf & 1) {
        var _r186 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "ul", 3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, DialogOverviewExampleDialog_div_16_div_2_Template, 10, 4, "div", 4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "button", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_16_Template_button_click_4_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r186);

          var ctx_r185 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r185.closeDialog();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](5, "Cancel");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "button", 29);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_16_Template_button_click_6_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r186);

          var ctx_r187 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r187.submit();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r150 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r150.getMailUniqueArray);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](5);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r150.loading ? "Loading.." : "Confirm");
      }
    }

    function DialogOverviewExampleDialog_div_17_div_2_mat_option_9_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-option", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var yesNo_r192 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", yesNo_r192.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", yesNo_r192.name, " ");
      }
    }

    function DialogOverviewExampleDialog_div_17_div_2_Template(rf, ctx) {
      if (rf & 1) {
        var _r194 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "mat-form-field");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "mat-label");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7, "Select an option");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "mat-select", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("valueChange", function DialogOverviewExampleDialog_div_17_div_2_Template_mat_select_valueChange_8_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r194);

          var data_r189 = ctx.$implicit;

          var ctx_r193 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r193.selectYesNo($event, data_r189, "getsms");
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](9, DialogOverviewExampleDialog_div_17_div_2_mat_option_9_Template, 2, 2, "mat-option", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var data_r189 = ctx.$implicit;
        var i_r190 = ctx.index;

        var ctx_r188 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", i_r190 + 1, ".");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("\xA0\xA0", data_r189, " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", ctx_r188.preSelectionGetSMS(data_r189));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r188.yesNo);
      }
    }

    function DialogOverviewExampleDialog_div_17_Template(rf, ctx) {
      if (rf & 1) {
        var _r196 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "ul", 3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](2, DialogOverviewExampleDialog_div_17_div_2_Template, 10, 4, "div", 4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "button", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_17_Template_button_click_4_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r196);

          var ctx_r195 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r195.closeDialog();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](5, "Cancel");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "button", 29);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_17_Template_button_click_6_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r196);

          var ctx_r197 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r197.submit();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r151 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r151.getSmsUniqueArray);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](5);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r151.loading ? "Loading.." : "Confirm");
      }
    }

    function DialogOverviewExampleDialog_div_18_div_3_mat_option_9_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "mat-option", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var gender_r202 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", gender_r202.value);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"](" ", gender_r202.name, " ");
      }
    }

    function DialogOverviewExampleDialog_div_18_div_3_Template(rf, ctx) {
      if (rf & 1) {
        var _r204 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "span");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "mat-form-field");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "mat-label");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7, "Select an option");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "mat-select", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("valueChange", function DialogOverviewExampleDialog_div_18_div_3_Template_mat_select_valueChange_8_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r204);

          var data_r199 = ctx.$implicit;

          var ctx_r203 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

          return ctx_r203.selectGender($event, data_r199);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](9, DialogOverviewExampleDialog_div_18_div_3_mat_option_9_Template, 2, 2, "mat-option", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var data_r199 = ctx.$implicit;
        var i_r200 = ctx.index;

        var ctx_r198 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("", i_r200 + 1, ".");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate1"]("\xA0\xA0", data_r199, " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("value", ctx_r198.preSelectionGender(data_r199));

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r198.gender);
      }
    }

    function DialogOverviewExampleDialog_div_18_Template(rf, ctx) {
      if (rf & 1) {
        var _r206 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵgetCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "ul", 3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](3, DialogOverviewExampleDialog_div_18_div_3_Template, 10, 4, "div", 4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "div");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "button", 25);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_18_Template_button_click_5_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r206);

          var ctx_r205 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r205.closeDialog();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, "Cancel");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](7, "button", 29);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function DialogOverviewExampleDialog_div_18_Template_button_click_7_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵrestoreView"](_r206);

          var ctx_r207 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

          return ctx_r207.submit();
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](8);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }

      if (rf & 2) {
        var ctx_r152 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵnextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx_r152.genderUniqueArray);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](5);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](ctx_r152.loading ? "Loading.." : "Confirm");
      }
    }

    function DialogOverviewExampleDialog_ng_template_19_Template(rf, ctx) {}

    var FileuploadComponent =
    /*#__PURE__*/
    function () {
      function FileuploadComponent(papa, activatedRoute, dialog, apiService, updateData) {
        var _this = this;

        _classCallCheck(this, FileuploadComponent);

        this.papa = papa;
        this.activatedRoute = activatedRoute;
        this.dialog = dialog;
        this.apiService = apiService;
        this.updateData = updateData;
        this.csvValues = [];
        this.headers = [];
        this.csvData = [];
        this.rejectedData = [];
        this.insertedData = [];
        this.errorInResponse = false;
        this.availableHeader = [];
        this.sortedHeader = [];
        this.listedCsv = [];
        this.unlistedCsv = [];
        this.sectionAdd = false;
        this.columndata = [];
        this.step = 1;
        this.insertCsvFile = '';
        this.loading = false;
        this.currImportType = 'archive';
        this.showInsertBtn = false;
        this.defaultHeader = [{
          name: "first name",
          value: "FirstName"
        }, {
          name: "last name",
          value: "LastName"
        }, {
          name: "date of birth",
          value: "Dob"
        }, // ContactPhone
        {
          name: "phone number",
          value: "ContactPhone"
        }, {
          name: "mobile",
          value: "ContactPhone"
        }, {
          name: "phone",
          value: "ContactPhone"
        }, {
          name: "mobile phone",
          value: "ContactPhone"
        }, {
          name: "personal number ",
          value: "ContactPhone"
        }, {
          name: "נייד",
          value: "ContactPhone"
        }, {
          name: "מספר נייד",
          value: "ContactPhone"
        }, {
          name: "טלפון נייד",
          value: "ContactPhone"
        }, {
          name: "טלפון אישי",
          value: "ContactPhone"
        }, {
          name: "מספר טלפון נייד",
          value: "ContactPhone"
        }, {
          name: "מס׳ נייד",
          value: "ContactPhone"
        }, // End ContactPhone
        // Email
        {
          name: "Email",
          value: "Email"
        }, {
          name: "email",
          value: "Email"
        }, {
          name: "mail",
          value: "Email"
        }, {
          name: "מייל",
          value: "Email"
        }, {
          name: "דוא״ל",
          value: "Email"
        }, {
          name: "אי מייל",
          value: "Email"
        }, {
          name: "דואר אלקטרוני",
          value: "Email"
        }, {
          name: "כתובת מייל",
          value: "Email"
        }];
        this.activatedRoute.queryParams.subscribe(function (params) {
          _this.cNum = params['cNum'];
          console.log(_this.cNum);
          _this.uId = params['uid'];
          console.log(_this.uId);
        });
      }

      _createClass(FileuploadComponent, [{
        key: "ngOnInit",
        value: function ngOnInit() {}
      }, {
        key: "ngDoCheck",
        value: function ngDoCheck() {
          var _this2 = this;

          this.updateData.columnData.subscribe(function (data) {
            if (data) {
              _this2.columndata = data;
            }
          });
        }
      }, {
        key: "updateCustomMapping",
        value: function updateCustomMapping(apiName, cb) {
          var _this3 = this;

          this.updateData.genderData.subscribe(function (res) {
            if (res && res.changeGenderArray && res.changeGenderArray.length > 0) {
              var index = _this3.headersList.indexOf(res.header);

              _this3.csvData.map(function (ele) {
                for (var i = 0; i < res.changeGenderArray.length; i++) {
                  if (ele[index] === res.changeGenderArray[i].name) {
                    ele.splice(index, 1, res.changeGenderArray[i].value);
                  }
                }
              });
            }
          });
          this.updateData.getEmailData.subscribe(function (res) {
            if (res && res.changeEmailArray && res.changeEmailArray.length > 0) {
              var index = _this3.headersList.indexOf(res.header);

              _this3.csvData.map(function (ele) {
                for (var i = 0; i < res.changeEmailArray.length; i++) {
                  if (ele[index] === res.changeEmailArray[i].name) {
                    console.log(ele[index], "===", res.changeEmailArray[i].name);
                    ele.splice(index, 1, res.changeEmailArray[i].value);
                  }
                }
              });
            }
          });
          this.updateData.getSmsData.subscribe(function (res) {
            if (res && res.changeSmsArray && res.changeSmsArray.length > 0) {
              var index = _this3.headersList.indexOf(res.header);

              _this3.csvData.map(function (ele) {
                for (var i = 0; i < res.changeSmsArray.length; i++) {
                  if (ele[index] === res.changeSmsArray[i].name) {
                    console.log(ele[index], "===", res.changeSmsArray[i].name);
                    ele.splice(index, 1, res.changeSmsArray[i].value);
                  }
                }
              });
            }
          });
          setTimeout(function () {
            cb(true);
          }, 2000);
        }
      }, {
        key: "greater_than_zero",
        value: function greater_than_zero(totn_element) {
          return totn_element > 0;
        }
      }, {
        key: "checkMapingValue",
        value: function checkMapingValue(value) {
          var index = this.columndata.findIndex(function (ele) {
            return ele.name.toLowerCase() === value.toLowerCase();
          });
          return index === -1 ? false : true;
        }
      }, {
        key: "getUnique",
        value: function getUnique(valmatch) {
          if (this.sortedHeader.indexOf(valmatch)) {
            return 'not-classmatched';
          } else {
            return 'classmatched';
          }
        }
      }, {
        key: "ifHeaderSet",
        value: function ifHeaderSet(valmatch) {
          var index = this.columndata.findIndex(function (ele) {
            return ele.name.toLowerCase() === valmatch.toLowerCase();
          });

          if (index === -1) {
            return 'header-not-set clear_hide';
          } else {
            return 'header-set';
          }
        }
      }, {
        key: "clearColumn",
        value: function clearColumn(valmatch) {
          var index = this.columndata.findIndex(function (ele) {
            return ele.name.toLowerCase() === valmatch.toLowerCase();
          });
          var data = this.columndata[index];
          var coldata = [];
          this.columndata.map(function (ele, i) {
            if (data.value.toLowerCase() === 'membership') {
              if (ele.value.toLowerCase() === 'membership' || ele.value.toLowerCase() === 'startdate' || ele.value.toLowerCase() === 'vailddate' || ele.value.toLowerCase() === 'truebalancevalue') return;
              coldata.push(ele);
            } else {
              if (ele.value.toLowerCase() !== data.value.toLowerCase()) {
                coldata.push(ele);
              }
            }
          });
          this.updateData.updateSelectColumnData(coldata);
        }
      }, {
        key: "openDialog",
        value: function openDialog(headers, indx) {
          var _this4 = this;

          var selectedColumn;
          var avliableColValues = [];
          console.log("header", headers, indx);
          console.log("columndata", this.columndata);
          var colindex = this.columndata.findIndex(function (element) {
            return element.name.toLowerCase() === headers.toLowerCase();
          });

          if (colindex !== -1) {
            var index = this.availableHeader.findIndex(function (ele) {
              return ele.value.toLowerCase() === _this4.columndata[colindex].value.toLowerCase();
            });

            if (index !== -1) {
              selectedColumn = {
                name: headers,
                value: this.columndata[colindex].value,
                show: this.availableHeader[index].show
              };
              avliableColValues.push(selectedColumn);
            } else {
              selectedColumn = {
                name: headers,
                value: this.columndata[colindex].value,
                show: this.columndata[colindex].value
              };
              avliableColValues.push(selectedColumn);
            }
          }

          var colData = this.columndata;
          this.availableHeader.filter(function (obj) {
            var index = colData.findIndex(function (ele) {
              return ele.value.toLowerCase() === obj.value.toLowerCase();
            });

            if (index === -1) {
              avliableColValues.push(obj);
            }
          });
          avliableColValues.filter(function (obj) {
            var index = colData.findIndex(function (ele) {
              return ele.value.toLowerCase() === obj.value.toLowerCase();
            });

            if (index !== -1) {}
          });
          var data = {
            'csvData': this.csvData,
            'headersList': this.headersList,
            'currentHeader': headers,
            'availableHeader': avliableColValues,
            // 'avliableColValues': avliableColValues,
            'openModalHeader': headers.toLowerCase(),
            'indx': indx,
            'selectedColumn': selectedColumn,
            'columnData': this.columndata,
            'currImportType': this.currImportType
          };
          var dialogRef = this.dialog.open(DialogOverviewExampleDialog, {
            width: '50%',
            data: data
          });
          dialogRef.afterClosed().subscribe(function (result) {});
        }
      }, {
        key: "selectedValue",
        value: function selectedValue(currentHeader) {
          return 'FirstName';
        }
      }, {
        key: "compareCsvField",
        value: function compareCsvField(csvArray, databaseArray) {
          var finalarray = [];
          csvArray.forEach(function (e1) {
            return databaseArray.forEach(function (e2) {
              if (e1 === e2) {
                finalarray.push(e1);
              }
            });
          });
          this.sortedHeader = finalarray;
        }
      }, {
        key: "filterData",
        value: function filterData(type, arrayVal, indexval) {
          arrayVal = arrayVal.filter(function (item, index) {
            if (index > 0) {
              return item;
            }
          });
          var listedData = arrayVal;
          var oldArray = arrayVal;
          var unlistedCsv = [];
          var listedCsv = [];

          if (type != "") {
            this.headers.filter(function (item, index) {
              if (index > 0) {
                if (type === 'email') {
                  var emailField = item[4];
                  var EMAIL_REGEXP = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i;

                  if (emailField == '') {
                    unlistedCsv.push(item);
                  } else if (emailField != "" && (emailField.length <= 5 || !EMAIL_REGEXP.test(emailField))) {
                    unlistedCsv.push(item);
                  } else {
                    listedCsv.push(item);
                  }
                } else {
                  if (item[indexval] != '') {
                    listedCsv.push(item);
                  }
                }

                listedData = listedCsv;
              }
            });
          } else {
            listedData = oldArray;
          }

          this.csvData = listedData;
        }
      }, {
        key: "sortData",
        value: function sortData(type, arrayVal, indexval) {
          arrayVal = arrayVal.filter(function (item, index) {
            if (index > 0) {
              return item;
            }
          });
          var listedData = arrayVal;
          var oldArray = arrayVal;
          var unlistedCsv = [];
          var listedCsv = [];

          if (type != "") {
            this.headers.filter(function (item, index) {
              if (index > 0) {
                if (type === 'email') {
                  var emailField = item[4];
                  var EMAIL_REGEXP = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i;

                  if (emailField == '') {
                    unlistedCsv.push(item);
                  } else if (emailField != "" && (emailField.length <= 5 || !EMAIL_REGEXP.test(emailField))) {
                    unlistedCsv.push(item);
                  } else {
                    listedCsv.push(item);
                  }
                } else {
                  if (item[indexval] != '') {
                    listedCsv.push(item);
                  }
                }

                listedData = listedCsv;
              }
            });
          } else {
            listedData = oldArray;
          }

          this.csvData = listedData;
        }
      }, {
        key: "rejectedDownload",
        value: function rejectedDownload() {
          var rejectHeaders = [];
          var rejectedRecord = [];

          for (var i = 0; i < this.rejectedData.length; i++) {
            var obj = this.rejectedData[i];
            Object.values(this.rejectedData[i]).forEach(function (item) {
              if (typeof item === 'object') {
                delete obj.MembershipData ? obj.MembershipData : '';
                delete obj.additional_data ? obj.additional_data : '';
                delete obj.LeadsData ? obj.LeadsData : '';
                obj = Object.assign({}, item, obj);
              }
            });
            rejectedRecord.push(obj);
          }

          Object.keys(rejectedRecord[0]).forEach(function (item) {
            rejectHeaders.push(item);
          });
          var options = {
            fieldSeparator: ',',
            quoteStrings: '"',
            decimalSeparator: '.',
            showLabels: true,
            filename: 'Rejected_CSV_Data',
            showTitle: false,
            title: 'Rejected_Data_CSV',
            useTextFile: false,
            useBom: true,
            useKeysAsHeaders: false,
            headers: rejectHeaders
          };
          console.log("rejected", rejectedRecord);
          var csvExporter = new export_to_csv__WEBPACK_IMPORTED_MODULE_4__["ExportToCsv"](options);
          csvExporter.generateCsv(rejectedRecord);
        }
      }, {
        key: "backStep",
        value: function backStep(step) {
          var initialData = [{
            name: "first name",
            value: "FirstName"
          }, {
            name: "last name",
            value: "LastName"
          }, {
            name: "date of birth",
            value: "Dob"
          }, // ContactPhone
          {
            name: "phone number",
            value: "ContactPhone"
          }, {
            name: "mobile",
            value: "ContactPhone"
          }, {
            name: "phone",
            value: "ContactPhone"
          }, {
            name: "mobile phone",
            value: "ContactPhone"
          }, {
            name: "personal number ",
            value: "ContactPhone"
          }, {
            name: "נייד",
            value: "ContactPhone"
          }, {
            name: "מספר נייד",
            value: "ContactPhone"
          }, {
            name: "טלפון נייד",
            value: "ContactPhone"
          }, {
            name: "טלפון אישי",
            value: "ContactPhone"
          }, {
            name: "מספר טלפון נייד",
            value: "ContactPhone"
          }, {
            name: "מס׳ נייד",
            value: "ContactPhone"
          }, // End ContactPhone
          // Email
          {
            name: "Email",
            value: "Email"
          }, {
            name: "email",
            value: "Email"
          }, {
            name: "mail",
            value: "Email"
          }, {
            name: "מייל",
            value: "Email"
          }, {
            name: "דוא״ל",
            value: "Email"
          }, {
            name: "אי מייל",
            value: "Email"
          }, {
            name: "דואר אלקטרוני",
            value: "Email"
          }, {
            name: "כתובת מייל",
            value: "Email"
          }];
          if (step === 1) return;

          if (step === 2) {
            this.csvData = [];
            this.headersList = [];
            this.updateData.updateSelectColumnData(initialData);
          }

          this.step = this.step - 1;
        }
      }, {
        key: "selectFile",
        value: function selectFile(id) {
          document.getElementById(id).click();
          this.insertCsvFile = id;

          if (id === "lead") {
            this.currImportType = 'lead';
            this.availableHeader = [{
              name: "First Name",
              value: "FirstName",
              show: "First Name"
            }, {
              name: "Last Name",
              value: "LastName",
              show: "Last Name"
            }, {
              name: "Gender",
              value: "Gender",
              show: "Gender"
            }, {
              name: "Date of Birth",
              value: "Dob",
              show: "Date of Birth"
            }, {
              name: "Contact Phone",
              value: "ContactPhone",
              show: "Contact Phone"
            }, {
              name: "Email",
              value: "Email",
              show: "Email"
            }, {
              name: "Get Email",
              value: "GetEmail",
              show: "Get Email"
            }, {
              name: "Get SMS",
              value: "GetSMS",
              show: "Get SMS"
            }, {
              name: "Pipeline",
              value: "Pipeline",
              show: "Pipeline"
            }, {
              name: "Source",
              value: "Source",
              show: "Source"
            }, {
              name: "Status",
              value: "Status",
              show: "Status"
            }];
          } else if (id === "active") {
            this.currImportType = 'active';
            this.availableHeader = [{
              name: "First Name",
              value: "FirstName",
              show: "First Name"
            }, {
              name: "Last Name",
              value: "LastName",
              show: "Last Name"
            }, {
              name: "Gender",
              value: "Gender",
              show: "Gender"
            }, {
              name: "Date of Birth",
              value: "Dob",
              show: "Date of Birth"
            }, {
              name: "Contact Phone",
              value: "ContactPhone",
              show: "Contact Phone"
            }, {
              name: "Email",
              value: "Email",
              show: "Email"
            }, {
              name: "Get Email",
              value: "GetEmail",
              show: "Get Email"
            }, {
              name: "Get SMS",
              value: "GetSMS",
              show: "Get SMS"
            }, {
              name: "Membership",
              value: "MemberShip",
              show: "Membership"
            }];
          } else {
            this.currImportType = 'archive';
            this.availableHeader = [{
              name: "First Name",
              value: "FirstName",
              show: "First Name"
            }, {
              name: "Last Name",
              value: "LastName",
              show: "Last Name"
            }, {
              name: "Gender",
              value: "Gender",
              show: "Gender"
            }, {
              name: "Date of Birth",
              value: "Dob",
              show: "Date of Birth"
            }, {
              name: "Contact Phone",
              value: "ContactPhone",
              show: "Contact Phone"
            }, {
              name: "Email",
              value: "Email",
              show: "Email"
            }, {
              name: "Get Email",
              value: "GetEmail",
              show: "Get Email"
            }, {
              name: "Get SMS",
              value: "GetSMS",
              show: "Get SMS"
            }];
          }
        }
      }, {
        key: "changeListener",
        value: function changeListener(evt) {
          var _this5 = this;

          this.showInsertBtn = false;
          var newArray = [];
          var files = evt.target.files; // FileList object

          var file = files[0]; // if(file && file.type !== 'text/csv') return this.showToster('error', 'Please select .CSV file')

          var reader = new FileReader();
          reader.readAsText(file);

          reader.onload = function (event) {
            var csv = null;
            var csvData = null;
            _this5.csvData = [];
            _this5.headersList = [];
            _this5.headers = [];
            csv = event.target.result; // Content of CSV file

            _this5.papa.parse(csv, {
              skipEmptyLines: 'greedy',
              header: false,
              complete: function complete(results) {
                _this5.showInsertBtn = true;
                _this5.step = 2;

                for (var i = 0; i < results.data.length; i++) {
                  var headerDetails = results.data[i];

                  _this5.headers.push(headerDetails);
                }
              }
            });

            if (_this5.headers[0]) {
              _this5.compareCsvField(_this5.headers[0], _this5.availableHeader);

              var blankIndex = _this5.headers[0].findIndex(function (ele) {
                return ele === "";
              });

              if (blankIndex !== -1) {
                _this5.headers[0].splice(blankIndex, 1);
              }

              _this5.headersList = _this5.headers[0];
              var that = _this5;
              var defaultColData = [];

              _this5.defaultHeader.filter(function (obj) {
                var index = that.headersList.findIndex(function (ele) {
                  return ele.toLowerCase() === obj.name.toLowerCase();
                });

                if (index !== -1) {
                  defaultColData.push(obj);
                }
              });

              that.updateData.updateSelectColumnData(defaultColData);
              csvData = _this5.headers.filter(function (item, index) {
                if (index > 0) {
                  if (blankIndex !== -1) {
                    item.splice(blankIndex, 1);
                    return item;
                  } else {
                    return item;
                  }
                }
              });
              _this5.csvData = csvData;
            }
          };
        }
      }, {
        key: "showSelectedHeader",
        value: function showSelectedHeader(Value, i) {
          var index = this.columndata.findIndex(function (ele) {
            return ele.name.toLowerCase() === Value.toLowerCase();
          });
          return index === -1 ? "Column ".concat(i) : this.columndata[index].value;
        }
      }, {
        key: "submitInsertLeadApi",
        value: function submitInsertLeadApi() {
          var _this6 = this;

          if (this.loading) return;
          this.loading = true;
          var leadsApiData = [];
          this.updateCustomMapping('Active', function (status) {
            if (status) {
              _this6.csvData.forEach(function (element) {
                var tempArray = {};
                var LeadsData = {};
                var additional_field = {};

                _this6.columndata.forEach(function (colvalue, index) {
                  var array2 = _this6.headersList.map(function (x) {
                    return x.toLowerCase();
                  });

                  var index = array2.indexOf(colvalue.name.toLowerCase());
                  var currColumn = colvalue.value;

                  if (index >= 0) {
                    if (currColumn === 'Additional_Field') {
                      additional_field[colvalue.name] = element[index];
                    } else {
                      if (colvalue.value === "Pipeline" || colvalue.value === "Source" || colvalue.value === "Status") {
                        LeadsData[currColumn] = element[index];
                      } else {
                        tempArray[currColumn] = element[index];
                      }
                    }

                    if (additional_field && Object.keys(additional_field).length) {
                      tempArray["additional_data"] = additional_field;
                    }

                    tempArray["LeadsData"] = LeadsData;
                  }
                });

                leadsApiData.push(tempArray);
              });

              var body = {
                CompanyNum: _this6.cNum,
                fun: "leads",
                csvData: leadsApiData
              };
              console.log(body);

              _this6.apiService.insertLead(body).subscribe(function (data) {
                if (data['success'] === 1) {
                  _this6.rejectedData = [];

                  if (data['not_valid']) {
                    _this6.rejectedData = data['not_valid'];
                  }

                  if (data['insertedIds']) {
                    _this6.insertedData = data['insertedIds'];
                  }

                  _this6.errorInResponse = false;
                } else {
                  _this6.errorInResponse = true;
                }

                _this6.showToster('success', 'Data Inserted Successfully');

                _this6.updateData.updateSelectColumnData([]);

                _this6.loading = false;
                _this6.step = 3;
              }, function (err) {
                _this6.errorInResponse = true;

                _this6.showToster('error', 'Please provide valid column_names/data type');

                console.log(err);
                _this6.loading = false;
                _this6.step = 3;
              });
            }
          });
        }
      }, {
        key: "submitInsertActiveApi",
        value: function submitInsertActiveApi() {
          var _this7 = this;

          if (this.loading) return;
          this.loading = true;
          var insertApiData = [];
          this.updateCustomMapping('Active', function (status) {
            if (status) {
              _this7.csvData.forEach(function (element) {
                var additional_field = {};
                var tempArray = {};
                var MemberShip = {};

                _this7.columndata.forEach(function (colvalue, index) {
                  var array2 = _this7.headersList.map(function (x) {
                    return x.toLowerCase();
                  });

                  var index = array2.indexOf(colvalue.name.toLowerCase());
                  var currColumn = colvalue.value;

                  if (index >= 0) {
                    if (currColumn === 'Additional_Field') {
                      additional_field[colvalue.name] = element[index];
                    } else {
                      if (colvalue.value === "TrueBalanceValue" || colvalue.value === "MemberShip" || colvalue.value === "VaildDate" || colvalue.value === "StartDate") {
                        MemberShip[currColumn] = element[index];
                      } else {
                        tempArray[currColumn] = element[index];
                      }
                    }

                    tempArray["MembershipData"] = MemberShip;

                    if (additional_field && Object.keys(additional_field).length) {
                      tempArray["additional_data"] = additional_field;
                    }
                  }
                });

                insertApiData.push(tempArray);
              });

              var body = {
                CompanyNum: _this7.cNum,
                fun: "active",
                csvData: insertApiData
              };
              console.log(body);

              _this7.apiService.insertActive(body).subscribe(function (data) {
                if (data['success'] === 1) {
                  _this7.rejectedData = [];

                  if (data['not_valid']) {
                    _this7.rejectedData = data['not_valid'];
                  }

                  if (data['insertedIds']) {
                    _this7.insertedData = data['insertedIds'];
                  }
                } else {
                  _this7.errorInResponse = true;
                }

                console.log(data);

                _this7.showToster('success', 'Data Inserted Successfully');

                _this7.updateData.updateSelectColumnData([]);

                _this7.loading = false;
                _this7.step = 3;
              }, function (err) {
                _this7.errorInResponse = true;

                _this7.showToster('error', 'Please provide valid column_names/data type');

                _this7.loading = false;
                _this7.step = 3;
                console.log(err);
              });
            }
          });
        }
      }, {
        key: "submitInsertArchiveApi",
        value: function submitInsertArchiveApi() {
          var _this8 = this;

          if (this.loading) return;
          this.loading = true;
          var insertApiData = [];
          var additional_field = {};
          this.updateCustomMapping('Active', function (status) {
            if (status) {
              _this8.csvData.forEach(function (element) {
                var tempArray = {};

                _this8.columndata.forEach(function (colvalue, index) {
                  var array2 = _this8.headersList.map(function (x) {
                    return x.toLowerCase();
                  });

                  var index = array2.indexOf(colvalue.name.toLowerCase());
                  var currColumn = colvalue.value;

                  if (index >= 0) {
                    if (currColumn === 'Additional_Field') {
                      additional_field[colvalue.name] = element[index];
                    } else {
                      tempArray[currColumn] = element[index];
                    }

                    if (additional_field && Object.keys(additional_field).length) {
                      tempArray["additional_data"] = additional_field;
                    }
                  }
                });

                insertApiData.push(tempArray);
              });

              var body = {
                CompanyNum: _this8.cNum,
                fun: "archive",
                csvData: insertApiData
              };
              console.log(body);

              _this8.apiService.insertArchive(body).subscribe(function (data) {
                if (data['success'] === 1) {
                  _this8.rejectedData = [];

                  if (data['not_valid']) {
                    _this8.rejectedData = data['not_valid'];
                  }

                  if (data['insertedIds']) {
                    _this8.insertedData = data['insertedIds'];
                  }
                } else {
                  _this8.errorInResponse = true;
                }

                console.log(data);

                _this8.showToster('success', 'Data Inserted Successfully');

                _this8.updateData.updateSelectColumnData([]);

                _this8.loading = false;
                _this8.step = 3;
              }, function (err) {
                _this8.errorInResponse = true;

                _this8.showToster('error', 'Please provide valid column_names/data type');

                console.log(err);
                _this8.loading = false;
                _this8.step = 3;
              });
            }
          });
        }
      }, {
        key: "showToster",
        value: function showToster(icon, title) {
          var Toast = sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            onOpen: function onOpen(toast) {
              toast.addEventListener('mouseenter', sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.stopTimer);
              toast.addEventListener('mouseleave', sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.resumeTimer);
            }
          });
          Toast.fire({
            icon: icon,
            title: title
          });
        }
      }]);

      return FileuploadComponent;
    }();

    FileuploadComponent.ɵfac = function FileuploadComponent_Factory(t) {
      return new (t || FileuploadComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](ngx_papaparse__WEBPACK_IMPORTED_MODULE_1__["Papa"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_router__WEBPACK_IMPORTED_MODULE_2__["ActivatedRoute"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MatDialog"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_Service_api_service_service__WEBPACK_IMPORTED_MODULE_6__["ApiServiceService"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_Service_updatedata_service__WEBPACK_IMPORTED_MODULE_7__["UpdatedataService"]));
    };

    FileuploadComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: FileuploadComponent,
      selectors: [["app-fileupload"]],
      decls: 42,
      vars: 27,
      consts: [[1, "full-page"], [1, "sidebar"], [1, "content"], [1, "sidetext"], ["class", "sidebarText", 4, "ngIf"], ["class", "sidebarBtmBtn", 4, "ngIf"], [1, "main-content"], [1, "tp-bar"], [1, "btn", "backk", 3, "disabled", "click"], [1, "import-steps"], [3, "ngClass"], [1, ""], ["class", "fa fa-check-circle", 4, "ngIf", "ngIfElse"], ["first_else", ""], ["second_else", ""], ["third_else", ""], [1, "container"], ["class", "d-flex m-auto import-cards", 4, "ngIf"], [1, "container-fluid"], [1, "add_background"], ["class", "row top-margin", "style", "margin:0px;", 4, "ngIf"], ["class", "row", "style", "margin:0px;", 4, "ngIf"], ["customLoadingTemplate", ""], [3, "show", "config", "template"], [1, "sidebarText"], [1, "numberIcon"], [1, "sidebarBtmBtn"], ["class", "btn", 3, "click", 4, "ngIf"], [1, "btn", 3, "click"], [1, "fa", "fa-check-circle"], [1, "numberIconTb"], [1, "d-flex", "m-auto", "import-cards"], [1, "fa", "fa-user", "mat-icon"], [1, "p-2"], [1, "pb-3", 2, "text-align", "center"], ["type", "file", "accept", ".csv", "hidden", "true", "id", "active", 1, "form-control", "upload", 3, "change"], ["mat-raised-button", "", 1, "importBtn", 3, "click"], [1, "fa", "fa-users", "mat-icon"], ["type", "file", "accept", ".csv", "hidden", "true", "id", "lead", 1, "form-control", "upload", 3, "change"], [1, "fa", "fa-archive", "mat-icon"], ["type", "file", "accept", ".csv", "hidden", "true", "id", "archive", 1, "form-control", "upload", 3, "change"], [1, "row", "top-margin", 2, "margin", "0px"], ["fxFlex.gt-lg", "50%", "class", "outer_flx", 4, "ngFor", "ngForOf"], ["fxFlex.gt-lg", "50%", 1, "outer_flx"], [1, "colms_custom", "col-md-4"], ["fxFlex.gt-lg", "50%"], ["mat-raised-button", "", "color", "primary", 1, "select_col_btn", 3, "click"], ["class", "material-icons icon", 4, "ngIf"], [2, "padding", "0px 0px 0px 15px"], ["type", "button", 1, "btn", "btn-danger", "clear_col_btn", 3, "click"], [1, "vma-text"], [1, "material-icons"], [1, "title_and_filter"], [4, "ngFor", "ngForOf"], [1, "material-icons", "icon"], [4, "ngIf"], [1, "text-muted"], [1, "row", 2, "margin", "0px"], ["class", "container", 4, "ngIf"], ["class", "alert alert-success text-center", "role", "alert", 4, "ngIf"], ["class", "alert alert-warning text-center", "role", "alert", 4, "ngIf"], ["role", "alert", 1, "alert", "alert-success", "text-center"], ["role", "alert", 1, "alert", "alert-warning", "text-center"], ["mat-raised-button", "", "color", "primary", "class", "ml-2 DownloadRejected", 3, "click", 4, "ngIf"], ["mat-raised-button", "", "color", "primary", 1, "ml-2", "DownloadRejected", 3, "click"], [1, "fa", "fa-download"], [1, "text-center", "text-danger"]],
      template: function FileuploadComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "aside", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "div", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](4, FileuploadComponent_div_4_Template, 7, 0, "div", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](5, FileuploadComponent_div_5_Template, 7, 0, "div", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](6, FileuploadComponent_div_6_Template, 7, 0, "div", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](7, FileuploadComponent_div_7_Template, 4, 3, "div", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](9, "div", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](10, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](11, "button", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function FileuploadComponent_Template_button_click_11_listener($event) {
            return ctx.backStep(ctx.step);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](12, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](13, " Import CSV File ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](14, "ul", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](15, "li", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](16, "a", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](17, FileuploadComponent_i_17_Template, 1, 0, "i", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](18, FileuploadComponent_ng_template_18_Template, 2, 0, "ng-template", null, 13, _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplateRefExtractor"]);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](20, "Upload CSV ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](21, "li", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](22, "a", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](23, FileuploadComponent_i_23_Template, 1, 0, "i", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](24, FileuploadComponent_ng_template_24_Template, 2, 0, "ng-template", null, 14, _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplateRefExtractor"]);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](26, "Map Attributes ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](27, "li", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](28, "a", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](29, FileuploadComponent_i_29_Template, 1, 0, "i", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](30, FileuploadComponent_ng_template_30_Template, 2, 0, "ng-template", null, 15, _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplateRefExtractor"]);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](32, "Summary ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](33, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](34, FileuploadComponent_div_34_Template, 31, 0, "div", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](35, "div", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](36, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](37, FileuploadComponent_div_37_Template, 2, 1, "div", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](38, FileuploadComponent_div_38_Template, 3, 2, "div", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](39, FileuploadComponent_ng_template_39_Template, 0, 0, "ng-template", null, 22, _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplateRefExtractor"]);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](41, "ngx-loading", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
        }

        if (rf & 2) {
          var _r94 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵreference"](19);

          var _r97 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵreference"](25);

          var _r100 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵreference"](31);

          var _r105 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵreference"](40);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("disabled", ctx.step === 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngClass", _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpureFunction1"](20, _c0, ctx.step === 1));

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step >= 2)("ngIfElse", _r94);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngClass", _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpureFunction1"](22, _c0, ctx.step === 2));

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step >= 3)("ngIfElse", _r97);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngClass", _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpureFunction1"](24, _c0, ctx.step === 3));

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step >= 3)("ngIfElse", _r100);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.step === 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("show", ctx.loading)("config", _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpureFunction0"](26, _c1))("template", _r105);
        }
      },
      directives: [_angular_common__WEBPACK_IMPORTED_MODULE_9__["NgIf"], _angular_common__WEBPACK_IMPORTED_MODULE_9__["NgClass"], _angular_flex_layout_extended__WEBPACK_IMPORTED_MODULE_10__["DefaultClassDirective"], ngx_loading__WEBPACK_IMPORTED_MODULE_11__["NgxLoadingComponent"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatButton"], _angular_common__WEBPACK_IMPORTED_MODULE_9__["NgForOf"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_13__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatIcon"]],
      styles: ["button[_ngcontent-%COMP%]:focus{\r\n\toutline:none!important;\r\n\tbox-shadow:none!important;\r\n}\r\n\r\n.file_uploading[_ngcontent-%COMP%] {\r\n    padding: 20px 40px 0;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]:hover {\r\n    box-shadow: 0px 1px 11px 1px #0e0e0e3b;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%] {\r\n    width: 100%!important;\r\n    max-width: 100%!important;\r\n    background: #ffffff;\r\n    max-height: 350px;\r\n    overflow: auto;\r\n    border: 1px solid #d4d4d4;\r\n    border-radius: 4px;\r\n    padding: 10px 0 10px;\r\n    margin: 10px auto;\r\n}\r\n\r\n.header-set[_ngcontent-%COMP%]   .colms_custom[_ngcontent-%COMP%] {\r\n    border: 2px solid #28a745;\r\n}\r\n\r\n.outer_flx[_ngcontent-%COMP%] {\r\n    width: 100%!important;\r\n    max-width: 25%!important;\r\n    padding: 0 10px;\r\n\r\n}\r\n\r\n.add_background[_ngcontent-%COMP%] {\r\n    background: #fff;\r\n}\r\n\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%] {\r\n    background: transparent;\r\n    border-radius: 3px;\r\n    padding: 0;\r\n    margin: 0 15px;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   h3[_ngcontent-%COMP%] {\r\n\tcolor: #182433!important;\r\n    font-size: 20px;\r\n    padding: 10px 15px 10px;\r\n    font-weight: 600;\r\n    margin: 0;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   h4[_ngcontent-%COMP%] {\r\n    font-size: 15px;\r\n    text-transform: capitalize;\r\n    color: #182433;\r\n    font-weight: 500;\r\n    background: #f3f9ff;\r\n    width: 100%;\r\n    display: inline-block;\r\n    padding:10px 15px;\r\n    margin: 20px 0 20px 0;\r\n}\r\n\r\np.text-muted[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    padding: 0 20px 0 0px;\r\n}\r\n\r\nmat-list.mat-list.mat-list-base[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 100%!important;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base.mat-primary[_ngcontent-%COMP%] {\r\n    margin: 0 15px 10px;\r\n}\r\n\r\np.mat-line.text-muted[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    padding: 0 15px 0 0;\r\n}\r\n\r\n.text-muted[_ngcontent-%COMP%] {\r\n    color: #182433 !important;\r\n    font-size: 14px!important;\r\n    font-weight: 500!important;\r\n    padding: 0 0 0 20px;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 100%!important;\r\n    margin: 0px 0 0;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]{\r\n\tborder-top: 1px solid #e8e8e8;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]:last-child{\r\n\tborder-top: 1px solid #e8e8e8;\r\n}\r\n\r\nmat-list-item.mat-list-item.ng-star-inserted[_ngcontent-%COMP%] {\r\n    border-top: 1px solid #e8e8e8;\r\n    height:44px!important;\r\n}\r\n\r\nmat-list-item.mat-list-item.ng-star-inserted[_ngcontent-%COMP%]:last-child{\r\n\tborder-bottom: 1px solid #e8e8e8;\r\n}\r\n\r\n.read_more[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    text-align: center;\r\n    padding:10px 0 0px; \r\n}\r\n\r\n.read_more[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    font-size: 15px;\r\n    color: #182433;\r\n \ttext-decoration:none;\r\n    font-weight: 600;\r\n}\r\n\r\n.read_more[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{\r\n\tcolor: #3788e5;\r\n}\r\n\r\nol[_ngcontent-%COMP%], ul[_ngcontent-%COMP%], dl[_ngcontent-%COMP%]{\r\n\tmargin:0px;\r\n\tpadding:0px;\r\n}\r\n\r\nmat-card.mat-card[_ngcontent-%COMP%] {\r\n    background: transparent;\r\n    box-shadow: none !important;\r\n    max-width: 1170px;\r\n    margin: 20px auto;\r\n}\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%] {\r\n    padding: 20px !important;\r\n    margin: 15px;\r\n    background: #fff;\r\n    box-shadow: 0px 0px 11px 0px #0002 !important;\r\n    border: 1px solid transparent;\r\n}\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]   .mat-icon[_ngcontent-%COMP%] {\r\n    width: auto;\r\n    height: auto;\r\n    font-size: 60px;\r\n    color: #e4e4e4;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   h4[_ngcontent-%COMP%] {\r\n    padding: 0 15px;\r\n    font-weight: 500;\r\n    font-size: 16px;\r\n    margin: 0 0 10px;\r\n    display: inline;\r\n    background: transparent;\r\n}\r\n\r\n.import-cards[_ngcontent-%COMP%]{\r\n\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   p.p-2[_ngcontent-%COMP%] {\r\n    color: #989898;    \r\n    font-size: 15px;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   button.mat-raised-button.filter_Button[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 120px;\r\n    line-height: 40px;\r\n    box-shadow: none;\r\n    border: 1px solid #e4e4e4;\r\n    color: #e4e4e4;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   button.mat-raised-button.select_col_btn[_ngcontent-%COMP%]{\r\n    width: auto;\r\n    max-width: 100%;\r\n    line-height: 40px;\r\n    box-shadow: none;\r\n    border: 1px solid #e4e4e4;\r\n    color: #e4e4e4;\r\n    padding: 0px 8px;\r\n}\r\n\r\n.btn.clear_col_btn[_ngcontent-%COMP%]{\r\n    line-height: 1;\r\n    padding: 5px;\r\n    }\r\n\r\n.vma-text[_ngcontent-%COMP%]{\r\n    vertical-align: middle;\r\n    }\r\n\r\n.clear_hide[_ngcontent-%COMP%]   button.clear_col_btn[_ngcontent-%COMP%]\r\n    {\r\n    display: none;\r\n    }\r\n\r\n.clear_col_btn[_ngcontent-%COMP%]   .material-icons[_ngcontent-%COMP%]{\r\n    font-size: 16px;\r\n    vertical-align: middle;\r\n    margin-left: 5px;\r\n    }\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]:hover{\r\n    border: 1px solid #1e88e5;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover   *[_ngcontent-%COMP%], .m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]:hover   button.mat-primary[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    color: #fff;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover   span[_ngcontent-%COMP%] {\r\n    color: #fff;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover {\r\n    background: #1e88e5;\r\n    color: #fff !important;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar-thumb {\r\n    background: #acacac;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar-track {\r\n    background: #ddd;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar {\r\n    width: 3px;\r\n}\r\n\r\nspan.material-icons.icon.ng-star-inserted[_ngcontent-%COMP%] {\r\n    margin: 7px 10px;\r\n    position: absolute;\r\n    right: 0;\r\n}\r\n\r\n.page-content[_ngcontent-%COMP%] {\r\n    padding: 0 !important;\r\n}\r\n\r\n.full-page[_ngcontent-%COMP%] {\r\n    display: flex;\r\n    height: 100%;\r\n}\r\n\r\naside.sidebar[_ngcontent-%COMP%] {\r\n    width: 300px;\r\n    background: #fff;\r\n    padding: 15px;\r\n    border-right: 1px solid #e2e2e2;\r\n}\r\n\r\n.main-content[_ngcontent-%COMP%] {\r\n    width: calc(100% - 300px);\r\n    background: #fff;\r\n    align-items: center;\r\n    display: inline-flex;\r\n    flex-wrap: wrap;\r\n    padding-top: 60px;\r\n    height: 100vh;\r\n    overflow: auto;\r\n}\r\n\r\n.sidebarBtmBtn[_ngcontent-%COMP%]{\r\n    align-self: flex-end;\r\n    margin: 0 auto;\r\n}\r\n\r\n.sidebar[_ngcontent-%COMP%]   button.btn[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    align-self: flex-end;\r\n}\r\n\r\n.content[_ngcontent-%COMP%] {\r\n    display: inline-flex;\r\n    height: 100%;\r\n    flex-wrap: wrap;\r\n    align-items: stretch;\r\n}\r\n\r\nul.import-steps[_ngcontent-%COMP%]   li[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    margin-left: 20px;\r\n    position: relative;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%] {\r\n    padding: 10px 20px;\r\n    background: #f4f4f4;\r\n    display: flex;\r\n    align-items: baseline;\r\n    justify-content: space-between;\r\n    width: calc(100% - 300px);\r\n    position: fixed;\r\n    top: 0;\r\n    z-index:9;\r\n}\r\n\r\nbutton.btn.backk[_ngcontent-%COMP%] {\r\n    border: 1px solid #222;\r\n    color: #222;\r\n    border-radius: 3px;\r\n    margin-right: 15px;\r\n    line-height: 1em;\r\n    text-transform: capitalize;\r\n}\r\n\r\nbutton.btn.backk[_ngcontent-%COMP%]:hover {\r\n    border: 1px solid #3788e5;\r\n\r\n    color: #3788e5;\r\n}\r\n\r\nul.import-steps[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    font-size: 14px;\r\n    font-weight: 500;\r\n    position: relative;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    font-weight: 600;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   h5[_ngcontent-%COMP%] {\r\n    text-align: center;\r\n    color: #1e88e5;\r\n    position: relative;\r\n    width: auto;\r\n    display: inline-block;\r\n    margin-bottom: 20px;\r\n    font-size:20px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   p[_ngcontent-%COMP%] {\r\n    font-size:18px;\r\n}\r\n\r\n.numberIcon[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    width: 22px;\r\n    height: 22px;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    border-radius: 50%;\r\n    margin-right: 10px;\r\n    padding: 3px;\r\n    font-size: 16px;\r\n    text-align: center;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   .numberIconTb[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    width: 22px;\r\n    height: 22px;\r\n    background: #fff;\r\n    color: #000;\r\n    margin-right: 5px;\r\n    padding: 1px;\r\n    border-radius: 50%;\r\n    text-align: center;\r\n    border: 1px solid #2c2c2c;\r\n    line-height: 1.4em;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   .activeStep[_ngcontent-%COMP%]   .numberIconTb[_ngcontent-%COMP%] {\r\n    margin-right: 5px;\r\n    padding: 1px;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    line-height: 1.4em;\r\n    border: 1px solid #3788e5;\r\n}\r\n\r\n.activeStep[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{\r\n    color: #1e88e5;\r\n}\r\n\r\n.import-steps[_ngcontent-%COMP%]   .fa-check-circle[_ngcontent-%COMP%]{\r\n    color: #00b33c;\r\n    font-size: 18px;\r\n    margin-right: 5px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%] {\r\n    text-align: center;\r\n    margin-top: 60px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   p[_ngcontent-%COMP%] {\r\n    text-align: left;\r\n}\r\n\r\n.header-set[_ngcontent-%COMP%]   button.select_col_btn.mat-raised-button.mat-button-base.mat-primary[_ngcontent-%COMP%] {\r\n    background: #00b33c;\r\n}\r\n\r\n\r\n\r\n@media(max-width: 767px){\r\n.outer_flx[_ngcontent-%COMP%]{max-width:100%!important; width: 100%;}\r\n.add_background[_ngcontent-%COMP%]{padding:10px 0 0px!important;}\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%]{font-size: 12px;padding: 3px 11px;}\r\n}\r\n\r\n@media(min-width:768px) and (max-width: 1024px){\r\n.mat-card[_ngcontent-%COMP%]   .mat-card-title[_ngcontent-%COMP%]{font-size:15px;}\r\n.outer_flx[_ngcontent-%COMP%]{max-width:50%!important; width: 100%;}\r\n.colms_custom[_ngcontent-%COMP%]   h3[_ngcontent-%COMP%]{font-size:16px;}\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%]{font-size: 12px;padding: 3px 14px;}\r\n}\r\n\r\n.icon[_ngcontent-%COMP%]{\r\n    background-color: #00b33c;\r\n    border-radius: 15px;\r\n    color: whitesmoke;\r\n    float:right;\r\n    margin : 10px 10px;\r\n    padding: 3px;\r\n    font-size: 18px;\r\n}\r\n\r\n.example-form[_ngcontent-%COMP%] {\r\n    min-width: 150px;\r\n    max-width: 500px;\r\n    width: 100%;\r\n}\r\n\r\n.example-full-width[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n}\r\n\r\n.example-radio-group[_ngcontent-%COMP%] {\r\n    display: flex;\r\n    flex-direction: column;\r\n    margin: 15px 0;\r\n}\r\n\r\n.example-radio-button[_ngcontent-%COMP%] {\r\n    margin: 5px;\r\n}\r\n\r\n.mat-raised-butto.importBtn[_ngcontent-%COMP%]:active, .mat-raised-butto.importBtn[_ngcontent-%COMP%]:hover{\r\n    color: #fff;\r\n}\r\n\r\n.mat-raised-butto.insertBtn[_ngcontent-%COMP%]:active, .insertBtn[_ngcontent-%COMP%]:hover{\r\n    color: #fff;\r\n}\r\n\r\n.smallloader[_ngcontent-%COMP%] {\r\n    border: 4px solid #f3f3f3; \r\n    border-top: 4px solid #3498db; \r\n    border-radius: 50%;\r\n    width: 24px;\r\n    height: 24px;\r\n    display: inline-block;\r\n    -webkit-animation: loaderspin 2s linear infinite;\r\n            animation: loaderspin 2s linear infinite;\r\n  }\r\n\r\n@-webkit-keyframes loaderspin {\r\n    0% { -webkit-transform: rotate(0deg); }\r\n    100% { -webkit-transform: rotate(360deg); }\r\n  }\r\n\r\n@keyframes loaderspin {\r\n    0% { transform: rotate(0deg); }\r\n    100% { transform: rotate(360deg); }\r\n  }\r\n\r\n.insertBtnBtm[_ngcontent-%COMP%]{\r\n    display: block;\r\n    text-align: center;\r\n    margin: 30px auto!important;\r\n    width: 50%;\r\n  }\r\n\r\n.DownloadRejected[_ngcontent-%COMP%]{\r\n    margin: 0 auto!important;\r\n    display: block;\r\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvZmlsZXVwbG9hZC9maWxldXBsb2FkLmNvbXBvbmVudC5jc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Q0FDQyxzQkFBc0I7Q0FDdEIseUJBQXlCO0FBQzFCOztBQUVBO0lBQ0ksb0JBQW9CO0FBQ3hCOztBQUVBO0lBQ0ksc0NBQXNDO0FBQzFDOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLHlCQUF5QjtJQUN6QixtQkFBbUI7SUFDbkIsaUJBQWlCO0lBQ2pCLGNBQWM7SUFDZCx5QkFBeUI7SUFDekIsa0JBQWtCO0lBQ2xCLG9CQUFvQjtJQUNwQixpQkFBaUI7QUFDckI7O0FBRUE7SUFDSSx5QkFBeUI7QUFDN0I7O0FBQ0E7SUFDSSxxQkFBcUI7SUFDckIsd0JBQXdCO0lBQ3hCLGVBQWU7QUFDbkI7c0JBQ3NCO0FBQ3RCOztBQUVBO0lBQ0ksZ0JBQWdCO0FBQ3BCOztBQUVBO0lBQ0ksdUJBQXVCO0lBQ3ZCLGtCQUFrQjtJQUNsQixVQUFVO0lBQ1YsY0FBYztBQUNsQjs7QUFFQTtDQUNDLHdCQUF3QjtJQUNyQixlQUFlO0lBQ2YsdUJBQXVCO0lBQ3ZCLGdCQUFnQjtJQUNoQixTQUFTO0FBQ2I7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsMEJBQTBCO0lBQzFCLGNBQWM7SUFDZCxnQkFBZ0I7SUFDaEIsbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxxQkFBcUI7SUFDckIsaUJBQWlCO0lBQ2pCLHFCQUFxQjtBQUN6Qjs7QUFFQTtJQUNJLHFCQUFxQjtBQUN6Qjs7QUFFQTtJQUNJLFdBQVc7SUFDWCx5QkFBeUI7QUFDN0I7O0FBRUE7SUFDSSxtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSxtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSx5QkFBeUI7SUFDekIseUJBQXlCO0lBQ3pCLDBCQUEwQjtJQUMxQixtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSxXQUFXO0lBQ1gseUJBQXlCO0lBQ3pCLGVBQWU7QUFDbkI7O0FBRUE7Q0FDQyw2QkFBNkI7QUFDOUI7O0FBRUE7Q0FDQyw2QkFBNkI7QUFDOUI7O0FBRUE7SUFDSSw2QkFBNkI7SUFDN0IscUJBQXFCO0FBQ3pCOztBQUVBO0NBQ0MsZ0NBQWdDO0FBQ2pDOztBQUVBO0lBQ0ksV0FBVztJQUNYLGtCQUFrQjtJQUNsQixrQkFBa0I7QUFDdEI7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsY0FBYztFQUNoQixvQkFBb0I7SUFDbEIsZ0JBQWdCO0FBQ3BCOztBQUVBO0NBQ0MsY0FBYztBQUNmOztBQUVBO0NBQ0MsVUFBVTtDQUNWLFdBQVc7QUFDWjs7QUFFQTtJQUNJLHVCQUF1QjtJQUN2QiwyQkFBMkI7SUFDM0IsaUJBQWlCO0lBQ2pCLGlCQUFpQjtBQUNyQjs7QUFFQTtJQUNJLHdCQUF3QjtJQUN4QixZQUFZO0lBQ1osZ0JBQWdCO0lBQ2hCLDZDQUE2QztJQUM3Qyw2QkFBNkI7QUFDakM7O0FBRUE7SUFDSSxXQUFXO0lBQ1gsWUFBWTtJQUNaLGVBQWU7SUFDZixjQUFjO0FBQ2xCOztBQUVBO0lBQ0ksZUFBZTtJQUNmLGdCQUFnQjtJQUNoQixlQUFlO0lBQ2YsZ0JBQWdCO0lBQ2hCLGVBQWU7SUFDZix1QkFBdUI7QUFDM0I7O0FBRUE7O0FBRUE7O0FBQ0E7SUFDSSxjQUFjO0lBQ2QsZUFBZTtBQUNuQjs7QUFFQTtJQUNJLFdBQVc7SUFDWCxnQkFBZ0I7SUFDaEIsaUJBQWlCO0lBQ2pCLGdCQUFnQjtJQUNoQix5QkFBeUI7SUFDekIsY0FBYztBQUNsQjs7QUFFQTtJQUNJLFdBQVc7SUFDWCxlQUFlO0lBQ2YsaUJBQWlCO0lBQ2pCLGdCQUFnQjtJQUNoQix5QkFBeUI7SUFDekIsY0FBYztJQUNkLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLGNBQWM7SUFDZCxZQUFZO0lBQ1o7O0FBQ0E7SUFDQSxzQkFBc0I7SUFDdEI7O0FBQ0E7O0lBRUEsYUFBYTtJQUNiOztBQUNBO0lBQ0EsZUFBZTtJQUNmLHNCQUFzQjtJQUN0QixnQkFBZ0I7SUFDaEI7O0FBR0o7SUFDSSx5QkFBeUI7QUFDN0I7O0FBRUE7SUFDSSxXQUFXO0FBQ2Y7O0FBRUE7SUFDSSxXQUFXO0FBQ2Y7O0FBRUE7SUFDSSxtQkFBbUI7SUFDbkIsc0JBQXNCO0FBQzFCOztBQUVBO0lBQ0ksbUJBQW1CO0FBQ3ZCOztBQUVBO0lBQ0ksZ0JBQWdCO0FBQ3BCOztBQUNBO0lBQ0ksVUFBVTtBQUNkOztBQUVBO0lBQ0ksZ0JBQWdCO0lBQ2hCLGtCQUFrQjtJQUNsQixRQUFRO0FBQ1o7O0FBRUE7SUFDSSxxQkFBcUI7QUFDekI7O0FBRUE7SUFDSSxhQUFhO0lBQ2IsWUFBWTtBQUNoQjs7QUFFQTtJQUNJLFlBQVk7SUFDWixnQkFBZ0I7SUFDaEIsYUFBYTtJQUNiLCtCQUErQjtBQUNuQzs7QUFFQTtJQUNJLHlCQUF5QjtJQUN6QixnQkFBZ0I7SUFDaEIsbUJBQW1CO0lBQ25CLG9CQUFvQjtJQUNwQixlQUFlO0lBQ2YsaUJBQWlCO0lBQ2pCLGFBQWE7SUFDYixjQUFjO0FBQ2xCOztBQUVBO0lBQ0ksb0JBQW9CO0lBQ3BCLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxXQUFXO0lBQ1gsbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxvQkFBb0I7QUFDeEI7O0FBRUE7SUFDSSxvQkFBb0I7SUFDcEIsWUFBWTtJQUNaLGVBQWU7SUFDZixvQkFBb0I7QUFDeEI7O0FBRUE7SUFDSSxxQkFBcUI7SUFDckIsaUJBQWlCO0lBQ2pCLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLGtCQUFrQjtJQUNsQixtQkFBbUI7SUFDbkIsYUFBYTtJQUNiLHFCQUFxQjtJQUNyQiw4QkFBOEI7SUFDOUIseUJBQXlCO0lBQ3pCLGVBQWU7SUFDZixNQUFNO0lBQ04sU0FBUztBQUNiOztBQUVBO0lBQ0ksc0JBQXNCO0lBQ3RCLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsa0JBQWtCO0lBQ2xCLGdCQUFnQjtJQUNoQiwwQkFBMEI7QUFDOUI7O0FBRUE7SUFDSSx5QkFBeUI7O0lBRXpCLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsZ0JBQWdCO0lBQ2hCLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLGdCQUFnQjtBQUNwQjs7QUFHQTtJQUNJLGtCQUFrQjtJQUNsQixjQUFjO0lBQ2Qsa0JBQWtCO0lBQ2xCLFdBQVc7SUFDWCxxQkFBcUI7SUFDckIsbUJBQW1CO0lBQ25CLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxjQUFjO0FBQ2xCOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLFdBQVc7SUFDWCxZQUFZO0lBQ1osbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsa0JBQWtCO0lBQ2xCLFlBQVk7SUFDWixlQUFlO0lBQ2Ysa0JBQWtCO0FBQ3RCOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLFdBQVc7SUFDWCxZQUFZO0lBQ1osZ0JBQWdCO0lBQ2hCLFdBQVc7SUFDWCxpQkFBaUI7SUFDakIsWUFBWTtJQUNaLGtCQUFrQjtJQUNsQixrQkFBa0I7SUFDbEIseUJBQXlCO0lBQ3pCLGtCQUFrQjtBQUN0Qjs7QUFDQTtJQUNJLGlCQUFpQjtJQUNqQixZQUFZO0lBQ1osbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIseUJBQXlCO0FBQzdCOztBQUVBO0lBQ0ksY0FBYztBQUNsQjs7QUFFQTtJQUNJLGNBQWM7SUFDZCxlQUFlO0lBQ2YsaUJBQWlCO0FBQ3JCOztBQUVBO0lBQ0ksa0JBQWtCO0lBQ2xCLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLG1CQUFtQjtBQUN2Qjs7QUFHQSwwRUFBMEU7O0FBRTFFO0FBQ0EsV0FBVyx3QkFBd0IsRUFBRSxXQUFXLENBQUM7QUFDakQsZ0JBQWdCLDRCQUE0QixDQUFDO0FBQzdDLGtDQUFrQyxlQUFlLENBQUMsaUJBQWlCLENBQUM7QUFDcEU7O0FBRUE7QUFDQSwwQkFBMEIsY0FBYyxDQUFDO0FBQ3pDLFdBQVcsdUJBQXVCLEVBQUUsV0FBVyxDQUFDO0FBQ2hELGlCQUFpQixjQUFjLENBQUM7QUFDaEMsa0NBQWtDLGVBQWUsQ0FBQyxpQkFBaUIsQ0FBQztBQUNwRTs7QUFFQTtJQUNJLHlCQUF5QjtJQUN6QixtQkFBbUI7SUFDbkIsaUJBQWlCO0lBQ2pCLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsWUFBWTtJQUNaLGVBQWU7QUFDbkI7O0FBRUE7SUFDSSxnQkFBZ0I7SUFDaEIsZ0JBQWdCO0lBQ2hCLFdBQVc7QUFDZjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFFQTtJQUNJLGFBQWE7SUFDYixzQkFBc0I7SUFDdEIsY0FBYztBQUNsQjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFDQTtJQUNJLFdBQVc7QUFDZjs7QUFHQTtJQUNJLHlCQUF5QixFQUFFLGVBQWU7SUFDMUMsNkJBQTZCLEVBQUUsU0FBUztJQUN4QyxrQkFBa0I7SUFDbEIsV0FBVztJQUNYLFlBQVk7SUFDWixxQkFBcUI7SUFDckIsZ0RBQXdDO1lBQXhDLHdDQUF3QztFQUMxQzs7QUFFQTtJQUNFLEtBQUssK0JBQStCLEVBQUU7SUFDdEMsT0FBTyxpQ0FBaUMsRUFBRTtFQUM1Qzs7QUFFQTtJQUNFLEtBQUssdUJBQXVCLEVBQUU7SUFDOUIsT0FBTyx5QkFBeUIsRUFBRTtFQUNwQzs7QUFFQTtJQUNFLGNBQWM7SUFDZCxrQkFBa0I7SUFDbEIsMkJBQTJCO0lBQzNCLFVBQVU7RUFDWjs7QUFHRjtJQUNJLHdCQUF3QjtJQUN4QixjQUFjO0FBQ2xCIiwiZmlsZSI6InNyYy9hcHAvZmlsZXVwbG9hZC9maWxldXBsb2FkLmNvbXBvbmVudC5jc3MiLCJzb3VyY2VzQ29udGVudCI6WyJidXR0b246Zm9jdXN7XHJcblx0b3V0bGluZTpub25lIWltcG9ydGFudDtcclxuXHRib3gtc2hhZG93Om5vbmUhaW1wb3J0YW50O1xyXG59XHJcblxyXG4uZmlsZV91cGxvYWRpbmcge1xyXG4gICAgcGFkZGluZzogMjBweCA0MHB4IDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20uY29sLW1kLTQ6aG92ZXIge1xyXG4gICAgYm94LXNoYWRvdzogMHB4IDFweCAxMXB4IDFweCAjMGUwZTBlM2I7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20ge1xyXG4gICAgd2lkdGg6IDEwMCUhaW1wb3J0YW50O1xyXG4gICAgbWF4LXdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxuICAgIGJhY2tncm91bmQ6ICNmZmZmZmY7XHJcbiAgICBtYXgtaGVpZ2h0OiAzNTBweDtcclxuICAgIG92ZXJmbG93OiBhdXRvO1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgI2Q0ZDRkNDtcclxuICAgIGJvcmRlci1yYWRpdXM6IDRweDtcclxuICAgIHBhZGRpbmc6IDEwcHggMCAxMHB4O1xyXG4gICAgbWFyZ2luOiAxMHB4IGF1dG87XHJcbn1cclxuXHJcbi5oZWFkZXItc2V0IC5jb2xtc19jdXN0b20ge1xyXG4gICAgYm9yZGVyOiAycHggc29saWQgIzI4YTc0NTtcclxufVxyXG4ub3V0ZXJfZmx4IHtcclxuICAgIHdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxuICAgIG1heC13aWR0aDogMjUlIWltcG9ydGFudDtcclxuICAgIHBhZGRpbmc6IDAgMTBweDtcclxuLyogIGZsZXg6IGluaGVyaXQhaW1wb3J0YW50O1xyXG4gICAgZGlzcGxheTogaW5oZXJpdDsqL1xyXG59XHJcblxyXG4uYWRkX2JhY2tncm91bmQge1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1idXR0b24ubWF0LWJ1dHRvbi1iYXNlIHtcclxuICAgIGJhY2tncm91bmQ6IHRyYW5zcGFyZW50O1xyXG4gICAgYm9yZGVyLXJhZGl1czogM3B4O1xyXG4gICAgcGFkZGluZzogMDtcclxuICAgIG1hcmdpbjogMCAxNXB4O1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tIGgzIHtcclxuXHRjb2xvcjogIzE4MjQzMyFpbXBvcnRhbnQ7XHJcbiAgICBmb250LXNpemU6IDIwcHg7XHJcbiAgICBwYWRkaW5nOiAxMHB4IDE1cHggMTBweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA2MDA7XHJcbiAgICBtYXJnaW46IDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gaDQge1xyXG4gICAgZm9udC1zaXplOiAxNXB4O1xyXG4gICAgdGV4dC10cmFuc2Zvcm06IGNhcGl0YWxpemU7XHJcbiAgICBjb2xvcjogIzE4MjQzMztcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZjNmOWZmO1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBwYWRkaW5nOjEwcHggMTVweDtcclxuICAgIG1hcmdpbjogMjBweCAwIDIwcHggMDtcclxufVxyXG5cclxucC50ZXh0LW11dGVkIHNwYW4ge1xyXG4gICAgcGFkZGluZzogMCAyMHB4IDAgMHB4O1xyXG59XHJcblxyXG5tYXQtbGlzdC5tYXQtbGlzdC5tYXQtbGlzdC1iYXNlIHtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG4gICAgbWF4LXdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZS5tYXQtcHJpbWFyeSB7XHJcbiAgICBtYXJnaW46IDAgMTVweCAxMHB4O1xyXG59XHJcblxyXG5wLm1hdC1saW5lLnRleHQtbXV0ZWQgc3BhbiB7XHJcbiAgICBwYWRkaW5nOiAwIDE1cHggMCAwO1xyXG59XHJcblxyXG4udGV4dC1tdXRlZCB7XHJcbiAgICBjb2xvcjogIzE4MjQzMyAhaW1wb3J0YW50O1xyXG4gICAgZm9udC1zaXplOiAxNHB4IWltcG9ydGFudDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDAhaW1wb3J0YW50O1xyXG4gICAgcGFkZGluZzogMCAwIDAgMjBweDtcclxufVxyXG5cclxuLmNvbG1zX2N1c3RvbSB1bCB7XHJcbiAgICB3aWR0aDogMTAwJTtcclxuICAgIG1heC13aWR0aDogMTAwJSFpbXBvcnRhbnQ7XHJcbiAgICBtYXJnaW46IDBweCAwIDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gdWwgbGl7XHJcblx0Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gdWwgbGk6bGFzdC1jaGlsZHtcclxuXHRib3JkZXItdG9wOiAxcHggc29saWQgI2U4ZThlODtcclxufVxyXG5cclxubWF0LWxpc3QtaXRlbS5tYXQtbGlzdC1pdGVtLm5nLXN0YXItaW5zZXJ0ZWQge1xyXG4gICAgYm9yZGVyLXRvcDogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbiAgICBoZWlnaHQ6NDRweCFpbXBvcnRhbnQ7XHJcbn1cclxuXHJcbm1hdC1saXN0LWl0ZW0ubWF0LWxpc3QtaXRlbS5uZy1zdGFyLWluc2VydGVkOmxhc3QtY2hpbGR7XHJcblx0Ym9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbn1cclxuXHJcbi5yZWFkX21vcmUge1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBwYWRkaW5nOjEwcHggMCAwcHg7IFxyXG59XHJcblxyXG4ucmVhZF9tb3JlIGEge1xyXG4gICAgZm9udC1zaXplOiAxNXB4O1xyXG4gICAgY29sb3I6ICMxODI0MzM7XHJcbiBcdHRleHQtZGVjb3JhdGlvbjpub25lO1xyXG4gICAgZm9udC13ZWlnaHQ6IDYwMDtcclxufVxyXG5cclxuLnJlYWRfbW9yZSBhOmhvdmVye1xyXG5cdGNvbG9yOiAjMzc4OGU1O1xyXG59XHJcblxyXG5vbCwgdWwsIGRse1xyXG5cdG1hcmdpbjowcHg7XHJcblx0cGFkZGluZzowcHg7XHJcbn1cclxuXHJcbm1hdC1jYXJkLm1hdC1jYXJkIHtcclxuICAgIGJhY2tncm91bmQ6IHRyYW5zcGFyZW50O1xyXG4gICAgYm94LXNoYWRvdzogbm9uZSAhaW1wb3J0YW50O1xyXG4gICAgbWF4LXdpZHRoOiAxMTcwcHg7XHJcbiAgICBtYXJnaW46IDIwcHggYXV0bztcclxufVxyXG5cclxuLm0tYXV0byAubWF0LWNhcmQge1xyXG4gICAgcGFkZGluZzogMjBweCAhaW1wb3J0YW50O1xyXG4gICAgbWFyZ2luOiAxNXB4O1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxuICAgIGJveC1zaGFkb3c6IDBweCAwcHggMTFweCAwcHggIzAwMDIgIWltcG9ydGFudDtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkIHRyYW5zcGFyZW50O1xyXG59XHJcblxyXG4ubS1hdXRvIC5tYXQtY2FyZCAubWF0LWljb24ge1xyXG4gICAgd2lkdGg6IGF1dG87XHJcbiAgICBoZWlnaHQ6IGF1dG87XHJcbiAgICBmb250LXNpemU6IDYwcHg7XHJcbiAgICBjb2xvcjogI2U0ZTRlNDtcclxufVxyXG5cclxuLm1hdC1jYXJkIGg0IHtcclxuICAgIHBhZGRpbmc6IDAgMTVweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBmb250LXNpemU6IDE2cHg7XHJcbiAgICBtYXJnaW46IDAgMCAxMHB4O1xyXG4gICAgZGlzcGxheTogaW5saW5lO1xyXG4gICAgYmFja2dyb3VuZDogdHJhbnNwYXJlbnQ7XHJcbn1cclxuXHJcbi5pbXBvcnQtY2FyZHN7XHJcblxyXG59XHJcbi5tYXQtY2FyZCBwLnAtMiB7XHJcbiAgICBjb2xvcjogIzk4OTg5ODsgICAgXHJcbiAgICBmb250LXNpemU6IDE1cHg7XHJcbn1cclxuXHJcbi5tYXQtY2FyZCBidXR0b24ubWF0LXJhaXNlZC1idXR0b24uZmlsdGVyX0J1dHRvbiB7XHJcbiAgICB3aWR0aDogMTAwJTtcclxuICAgIG1heC13aWR0aDogMTIwcHg7XHJcbiAgICBsaW5lLWhlaWdodDogNDBweDtcclxuICAgIGJveC1zaGFkb3c6IG5vbmU7XHJcbiAgICBib3JkZXI6IDFweCBzb2xpZCAjZTRlNGU0O1xyXG4gICAgY29sb3I6ICNlNGU0ZTQ7XHJcbn1cclxuXHJcbi5tYXQtY2FyZCBidXR0b24ubWF0LXJhaXNlZC1idXR0b24uc2VsZWN0X2NvbF9idG57XHJcbiAgICB3aWR0aDogYXV0bztcclxuICAgIG1heC13aWR0aDogMTAwJTtcclxuICAgIGxpbmUtaGVpZ2h0OiA0MHB4O1xyXG4gICAgYm94LXNoYWRvdzogbm9uZTtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICNlNGU0ZTQ7XHJcbiAgICBjb2xvcjogI2U0ZTRlNDtcclxuICAgIHBhZGRpbmc6IDBweCA4cHg7XHJcbn1cclxuXHJcbi5idG4uY2xlYXJfY29sX2J0bntcclxuICAgIGxpbmUtaGVpZ2h0OiAxO1xyXG4gICAgcGFkZGluZzogNXB4O1xyXG4gICAgfVxyXG4gICAgLnZtYS10ZXh0e1xyXG4gICAgdmVydGljYWwtYWxpZ246IG1pZGRsZTtcclxuICAgIH1cclxuICAgIC5jbGVhcl9oaWRlIGJ1dHRvbi5jbGVhcl9jb2xfYnRuXHJcbiAgICB7XHJcbiAgICBkaXNwbGF5OiBub25lO1xyXG4gICAgfVxyXG4gICAgLmNsZWFyX2NvbF9idG4gLm1hdGVyaWFsLWljb25ze1xyXG4gICAgZm9udC1zaXplOiAxNnB4O1xyXG4gICAgdmVydGljYWwtYWxpZ246IG1pZGRsZTtcclxuICAgIG1hcmdpbi1sZWZ0OiA1cHg7XHJcbiAgICB9XHJcbiAgICBcclxuXHJcbi5tLWF1dG8gLm1hdC1jYXJkOmhvdmVye1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgIzFlODhlNTtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZTpob3ZlciAqLCAubS1hdXRvIC5tYXQtY2FyZDpob3ZlciBidXR0b24ubWF0LXByaW1hcnkgc3BhbiB7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZTpob3ZlciBzcGFuIHtcclxuICAgIGNvbG9yOiAjZmZmO1xyXG59XHJcblxyXG5idXR0b24ubWF0LXJhaXNlZC1idXR0b24ubWF0LWJ1dHRvbi1iYXNlOmhvdmVyIHtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZiAhaW1wb3J0YW50O1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tLmNvbC1tZC00Ojotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XHJcbiAgICBiYWNrZ3JvdW5kOiAjYWNhY2FjO1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tLmNvbC1tZC00Ojotd2Via2l0LXNjcm9sbGJhci10cmFjayB7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZGRkO1xyXG59XHJcbi5jb2xtc19jdXN0b20uY29sLW1kLTQ6Oi13ZWJraXQtc2Nyb2xsYmFyIHtcclxuICAgIHdpZHRoOiAzcHg7XHJcbn1cclxuXHJcbnNwYW4ubWF0ZXJpYWwtaWNvbnMuaWNvbi5uZy1zdGFyLWluc2VydGVkIHtcclxuICAgIG1hcmdpbjogN3B4IDEwcHg7XHJcbiAgICBwb3NpdGlvbjogYWJzb2x1dGU7XHJcbiAgICByaWdodDogMDtcclxufVxyXG5cclxuLnBhZ2UtY29udGVudCB7XHJcbiAgICBwYWRkaW5nOiAwICFpbXBvcnRhbnQ7XHJcbn1cclxuXHJcbi5mdWxsLXBhZ2Uge1xyXG4gICAgZGlzcGxheTogZmxleDtcclxuICAgIGhlaWdodDogMTAwJTtcclxufVxyXG5cclxuYXNpZGUuc2lkZWJhciB7XHJcbiAgICB3aWR0aDogMzAwcHg7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZmZmO1xyXG4gICAgcGFkZGluZzogMTVweDtcclxuICAgIGJvcmRlci1yaWdodDogMXB4IHNvbGlkICNlMmUyZTI7XHJcbn1cclxuXHJcbi5tYWluLWNvbnRlbnQge1xyXG4gICAgd2lkdGg6IGNhbGMoMTAwJSAtIDMwMHB4KTtcclxuICAgIGJhY2tncm91bmQ6ICNmZmY7XHJcbiAgICBhbGlnbi1pdGVtczogY2VudGVyO1xyXG4gICAgZGlzcGxheTogaW5saW5lLWZsZXg7XHJcbiAgICBmbGV4LXdyYXA6IHdyYXA7XHJcbiAgICBwYWRkaW5nLXRvcDogNjBweDtcclxuICAgIGhlaWdodDogMTAwdmg7XHJcbiAgICBvdmVyZmxvdzogYXV0bztcclxufVxyXG5cclxuLnNpZGViYXJCdG1CdG57XHJcbiAgICBhbGlnbi1zZWxmOiBmbGV4LWVuZDtcclxuICAgIG1hcmdpbjogMCBhdXRvO1xyXG59XHJcblxyXG4uc2lkZWJhciBidXR0b24uYnRuIHtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG4gICAgYmFja2dyb3VuZDogIzFlODhlNTtcclxuICAgIGNvbG9yOiAjZmZmO1xyXG4gICAgYWxpZ24tc2VsZjogZmxleC1lbmQ7XHJcbn1cclxuXHJcbi5jb250ZW50IHtcclxuICAgIGRpc3BsYXk6IGlubGluZS1mbGV4O1xyXG4gICAgaGVpZ2h0OiAxMDAlO1xyXG4gICAgZmxleC13cmFwOiB3cmFwO1xyXG4gICAgYWxpZ24taXRlbXM6IHN0cmV0Y2g7XHJcbn1cclxuXHJcbnVsLmltcG9ydC1zdGVwcyBsaSB7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBtYXJnaW4tbGVmdDogMjBweDtcclxuICAgIHBvc2l0aW9uOiByZWxhdGl2ZTtcclxufVxyXG5cclxuLnRwLWJhciB7XHJcbiAgICBwYWRkaW5nOiAxMHB4IDIwcHg7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZjRmNGY0O1xyXG4gICAgZGlzcGxheTogZmxleDtcclxuICAgIGFsaWduLWl0ZW1zOiBiYXNlbGluZTtcclxuICAgIGp1c3RpZnktY29udGVudDogc3BhY2UtYmV0d2VlbjtcclxuICAgIHdpZHRoOiBjYWxjKDEwMCUgLSAzMDBweCk7XHJcbiAgICBwb3NpdGlvbjogZml4ZWQ7XHJcbiAgICB0b3A6IDA7XHJcbiAgICB6LWluZGV4Ojk7XHJcbn1cclxuXHJcbmJ1dHRvbi5idG4uYmFja2sge1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgIzIyMjtcclxuICAgIGNvbG9yOiAjMjIyO1xyXG4gICAgYm9yZGVyLXJhZGl1czogM3B4O1xyXG4gICAgbWFyZ2luLXJpZ2h0OiAxNXB4O1xyXG4gICAgbGluZS1oZWlnaHQ6IDFlbTtcclxuICAgIHRleHQtdHJhbnNmb3JtOiBjYXBpdGFsaXplO1xyXG59XHJcblxyXG5idXR0b24uYnRuLmJhY2trOmhvdmVyIHtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICMzNzg4ZTU7XHJcblxyXG4gICAgY29sb3I6ICMzNzg4ZTU7XHJcbn1cclxuXHJcbnVsLmltcG9ydC1zdGVwcyBsaSBhIHtcclxuICAgIGZvbnQtc2l6ZTogMTRweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBwb3NpdGlvbjogcmVsYXRpdmU7XHJcbn1cclxuXHJcbi50cC1iYXIgc3BhbiB7XHJcbiAgICBmb250LXdlaWdodDogNjAwO1xyXG59XHJcblxyXG5cclxuLnNpZGV0ZXh0IGg1IHtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxuICAgIGNvbG9yOiAjMWU4OGU1O1xyXG4gICAgcG9zaXRpb246IHJlbGF0aXZlO1xyXG4gICAgd2lkdGg6IGF1dG87XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBtYXJnaW4tYm90dG9tOiAyMHB4O1xyXG4gICAgZm9udC1zaXplOjIwcHg7XHJcbn1cclxuXHJcbi5zaWRldGV4dCBwIHtcclxuICAgIGZvbnQtc2l6ZToxOHB4O1xyXG59XHJcblxyXG4ubnVtYmVySWNvbiB7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICB3aWR0aDogMjJweDtcclxuICAgIGhlaWdodDogMjJweDtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxuICAgIGJvcmRlci1yYWRpdXM6IDUwJTtcclxuICAgIG1hcmdpbi1yaWdodDogMTBweDtcclxuICAgIHBhZGRpbmc6IDNweDtcclxuICAgIGZvbnQtc2l6ZTogMTZweDtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxufVxyXG5cclxuLnRwLWJhciAubnVtYmVySWNvblRiIHtcclxuICAgIGRpc3BsYXk6IGlubGluZS1ibG9jaztcclxuICAgIHdpZHRoOiAyMnB4O1xyXG4gICAgaGVpZ2h0OiAyMnB4O1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxuICAgIGNvbG9yOiAjMDAwO1xyXG4gICAgbWFyZ2luLXJpZ2h0OiA1cHg7XHJcbiAgICBwYWRkaW5nOiAxcHg7XHJcbiAgICBib3JkZXItcmFkaXVzOiA1MCU7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBib3JkZXI6IDFweCBzb2xpZCAjMmMyYzJjO1xyXG4gICAgbGluZS1oZWlnaHQ6IDEuNGVtO1xyXG59XHJcbi50cC1iYXIgLmFjdGl2ZVN0ZXAgLm51bWJlckljb25UYiB7XHJcbiAgICBtYXJnaW4tcmlnaHQ6IDVweDtcclxuICAgIHBhZGRpbmc6IDFweDtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxuICAgIGxpbmUtaGVpZ2h0OiAxLjRlbTtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICMzNzg4ZTU7XHJcbn1cclxuXHJcbi5hY3RpdmVTdGVwIGF7XHJcbiAgICBjb2xvcjogIzFlODhlNTtcclxufVxyXG5cclxuLmltcG9ydC1zdGVwcyAuZmEtY2hlY2stY2lyY2xle1xyXG4gICAgY29sb3I6ICMwMGIzM2M7XHJcbiAgICBmb250LXNpemU6IDE4cHg7XHJcbiAgICBtYXJnaW4tcmlnaHQ6IDVweDtcclxufVxyXG5cclxuLnNpZGV0ZXh0IHtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxuICAgIG1hcmdpbi10b3A6IDYwcHg7XHJcbn1cclxuXHJcbi5zaWRldGV4dCBwIHtcclxuICAgIHRleHQtYWxpZ246IGxlZnQ7XHJcbn1cclxuXHJcbi5oZWFkZXItc2V0IGJ1dHRvbi5zZWxlY3RfY29sX2J0bi5tYXQtcmFpc2VkLWJ1dHRvbi5tYXQtYnV0dG9uLWJhc2UubWF0LXByaW1hcnkge1xyXG4gICAgYmFja2dyb3VuZDogIzAwYjMzYztcclxufVxyXG5cclxuXHJcbi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqTWVkaWEgUXVlcnkqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqL1xyXG5cclxuQG1lZGlhKG1heC13aWR0aDogNzY3cHgpe1xyXG4ub3V0ZXJfZmx4e21heC13aWR0aDoxMDAlIWltcG9ydGFudDsgd2lkdGg6IDEwMCU7fVxyXG4uYWRkX2JhY2tncm91bmR7cGFkZGluZzoxMHB4IDAgMHB4IWltcG9ydGFudDt9XHJcbmJ1dHRvbi5tYXQtYnV0dG9uLm1hdC1idXR0b24tYmFzZXtmb250LXNpemU6IDEycHg7cGFkZGluZzogM3B4IDExcHg7fVxyXG59XHJcblxyXG5AbWVkaWEobWluLXdpZHRoOjc2OHB4KSBhbmQgKG1heC13aWR0aDogMTAyNHB4KXtcclxuLm1hdC1jYXJkIC5tYXQtY2FyZC10aXRsZXtmb250LXNpemU6MTVweDt9XHJcbi5vdXRlcl9mbHh7bWF4LXdpZHRoOjUwJSFpbXBvcnRhbnQ7IHdpZHRoOiAxMDAlO31cclxuLmNvbG1zX2N1c3RvbSBoM3tmb250LXNpemU6MTZweDt9XHJcbmJ1dHRvbi5tYXQtYnV0dG9uLm1hdC1idXR0b24tYmFzZXtmb250LXNpemU6IDEycHg7cGFkZGluZzogM3B4IDE0cHg7fVxyXG59XHJcblxyXG4uaWNvbntcclxuICAgIGJhY2tncm91bmQtY29sb3I6ICMwMGIzM2M7XHJcbiAgICBib3JkZXItcmFkaXVzOiAxNXB4O1xyXG4gICAgY29sb3I6IHdoaXRlc21va2U7XHJcbiAgICBmbG9hdDpyaWdodDtcclxuICAgIG1hcmdpbiA6IDEwcHggMTBweDtcclxuICAgIHBhZGRpbmc6IDNweDtcclxuICAgIGZvbnQtc2l6ZTogMThweDtcclxufVxyXG5cclxuLmV4YW1wbGUtZm9ybSB7XHJcbiAgICBtaW4td2lkdGg6IDE1MHB4O1xyXG4gICAgbWF4LXdpZHRoOiA1MDBweDtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG59XHJcbiAgXHJcbi5leGFtcGxlLWZ1bGwtd2lkdGgge1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbn1cclxuXHJcbi5leGFtcGxlLXJhZGlvLWdyb3VwIHtcclxuICAgIGRpc3BsYXk6IGZsZXg7XHJcbiAgICBmbGV4LWRpcmVjdGlvbjogY29sdW1uO1xyXG4gICAgbWFyZ2luOiAxNXB4IDA7XHJcbn1cclxuXHJcbi5leGFtcGxlLXJhZGlvLWJ1dHRvbiB7XHJcbiAgICBtYXJnaW46IDVweDtcclxufVxyXG5cclxuLm1hdC1yYWlzZWQtYnV0dG8uaW1wb3J0QnRuOmFjdGl2ZSwgLm1hdC1yYWlzZWQtYnV0dG8uaW1wb3J0QnRuOmhvdmVye1xyXG4gICAgY29sb3I6ICNmZmY7XHJcbn1cclxuLm1hdC1yYWlzZWQtYnV0dG8uaW5zZXJ0QnRuOmFjdGl2ZSwgLmluc2VydEJ0bjpob3ZlcntcclxuICAgIGNvbG9yOiAjZmZmO1xyXG59XHJcblxyXG5cclxuLnNtYWxsbG9hZGVyIHtcclxuICAgIGJvcmRlcjogNHB4IHNvbGlkICNmM2YzZjM7IC8qIExpZ2h0IGdyZXkgKi9cclxuICAgIGJvcmRlci10b3A6IDRweCBzb2xpZCAjMzQ5OGRiOyAvKiBCbHVlICovXHJcbiAgICBib3JkZXItcmFkaXVzOiA1MCU7XHJcbiAgICB3aWR0aDogMjRweDtcclxuICAgIGhlaWdodDogMjRweDtcclxuICAgIGRpc3BsYXk6IGlubGluZS1ibG9jaztcclxuICAgIGFuaW1hdGlvbjogbG9hZGVyc3BpbiAycyBsaW5lYXIgaW5maW5pdGU7XHJcbiAgfVxyXG5cclxuICBALXdlYmtpdC1rZXlmcmFtZXMgbG9hZGVyc3BpbiB7XHJcbiAgICAwJSB7IC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7IH1cclxuICAgIDEwMCUgeyAtd2Via2l0LXRyYW5zZm9ybTogcm90YXRlKDM2MGRlZyk7IH1cclxuICB9XHJcbiAgXHJcbiAgQGtleWZyYW1lcyBsb2FkZXJzcGluIHtcclxuICAgIDAlIHsgdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7IH1cclxuICAgIDEwMCUgeyB0cmFuc2Zvcm06IHJvdGF0ZSgzNjBkZWcpOyB9XHJcbiAgfVxyXG5cclxuICAuaW5zZXJ0QnRuQnRte1xyXG4gICAgZGlzcGxheTogYmxvY2s7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBtYXJnaW46IDMwcHggYXV0byFpbXBvcnRhbnQ7XHJcbiAgICB3aWR0aDogNTAlO1xyXG4gIH1cclxuXHJcbiAgXHJcbi5Eb3dubG9hZFJlamVjdGVke1xyXG4gICAgbWFyZ2luOiAwIGF1dG8haW1wb3J0YW50O1xyXG4gICAgZGlzcGxheTogYmxvY2s7XHJcbn1cclxuXHJcbiJdfQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](FileuploadComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-fileupload',
          templateUrl: './fileupload.component.html',
          styleUrls: ['./fileupload.component.css']
        }]
      }], function () {
        return [{
          type: ngx_papaparse__WEBPACK_IMPORTED_MODULE_1__["Papa"]
        }, {
          type: _angular_router__WEBPACK_IMPORTED_MODULE_2__["ActivatedRoute"]
        }, {
          type: _angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MatDialog"]
        }, {
          type: _Service_api_service_service__WEBPACK_IMPORTED_MODULE_6__["ApiServiceService"]
        }, {
          type: _Service_updatedata_service__WEBPACK_IMPORTED_MODULE_7__["UpdatedataService"]
        }];
      }, null);
    })();

    var DialogOverviewExampleDialog =
    /*#__PURE__*/
    function () {
      function DialogOverviewExampleDialog(activatedRoute, formbuilder, apiService, updateData, dialogRef, data) {
        var _this9 = this;

        _classCallCheck(this, DialogOverviewExampleDialog);

        this.activatedRoute = activatedRoute;
        this.formbuilder = formbuilder;
        this.apiService = apiService;
        this.updateData = updateData;
        this.dialogRef = dialogRef;
        this.data = data;
        this.cNum = '';
        this.uId = '';
        this.showInput = false;
        this.loading = false;
        this.columndata = [];
        this.memberShipHeader = [];
        this.genderModal = false;
        this.genderUniqueArray = [];
        this.changeGenderArray = [];
        this.yesNoModal = false;
        this.getEmailModal = false;
        this.getMailUniqueArray = [];
        this.changeEmailArray = [];
        this.getSmsModal = false;
        this.getSmsUniqueArray = [];
        this.changeSmsArray = [];
        this.gender = [{
          name: 'Male',
          value: '0'
        }, {
          name: 'Female',
          value: '1'
        }, {
          name: 'Other',
          value: '2'
        }];
        this.yesNo = [{
          name: 'Yes',
          value: '1'
        }, {
          name: 'No',
          value: '0'
        }];
        this.dataType = [{
          name: 'string',
          value: 'string'
        }, {
          name: 'number',
          value: 'number'
        }, {
          name: 'list',
          value: 'list'
        }];
        this.activatedRoute.queryParams.subscribe(function (params) {
          _this9.cNum = params['cNum'];
          _this9.uId = params['uid'];
        });
        this.headersList = data.headersList;
        this.currentHeader = data.currentHeader;
        this.csvData = data.csvData;
        this.openModel = data.openModalHeader;
        this.currImportType = data.currImportType;
        this.addColumn = this.formbuilder.group({
          col_name: ['', [_angular_forms__WEBPACK_IMPORTED_MODULE_5__["Validators"].required, _angular_forms__WEBPACK_IMPORTED_MODULE_5__["Validators"].pattern('^[a-zA-Z ]*$')]],
          datatype: ['', [_angular_forms__WEBPACK_IMPORTED_MODULE_5__["Validators"].required]]
        });
        this.updateData.columnData.subscribe(function (data) {
          if (data) {
            _this9.columndata = data;
          }
        });
        var possibleYesNoHeader = ['get email', 'get mail', 'getemail', 'getmail', 'get sms', 'getsms', 'get message'];

        if (data && data.selectedColumn && data.selectedColumn.value.toLowerCase() === 'getsms') {
          this.getSmsModalData();
        }

        if (data && data.selectedColumn && data.selectedColumn.value.toLowerCase() === 'getemail') {
          this.getEmailModalData();
        }

        if (data && data.selectedColumn && data.selectedColumn.value.toLowerCase() === 'gender') {
          this.genderModalData();
        }

        var colIndex = this.columndata.findIndex(function (ele) {
          return ele.value === "MemberShip";
        });
        var activeMemberHeader = [{
          name: "Start Date",
          value: "StartDate",
          show: "Start Date"
        }, {
          name: "Valid Date",
          value: "VaildDate",
          show: "Valid Date"
        }, {
          name: "True Balance Value",
          value: "TrueBalanceValue",
          show: "True Balance Value"
        }];

        if (colIndex !== -1) {
          activeMemberHeader.map(function (ele, i) {
            var Index = _this9.columndata.findIndex(function (element) {
              return element.value.toLowerCase() === ele.value.toLowerCase();
            });

            if (Index === -1) {
              _this9.memberShipHeader.push(ele);
            }
          }); // this.memberShipHeader = this.memberShipHeader.concat(activeMemberHeader)
        }
      }

      _createClass(DialogOverviewExampleDialog, [{
        key: "submit",
        value: function submit() {
          var _this10 = this;

          if (this.showInput) {
            if (this.addColumn.invalid) return;
            if (this.loading) return;
            this.loading = true;
            console.log(this.addColumn);
            var data = this.addColumn.value;
            data.type = this.currImportType;
            data.uId = this.uId;
            data.CompanyNum = this.cNum;

            if (this.addColumn.value.datatype === 'list' || this.addColumn.value.datatype === 'radio') {
              var index = this.headersList.indexOf(this.currentHeader);
              var array = [];
              this.csvData.map(function (ele, i) {
                if (ele[index] === '') return;
                array.push(ele[index]);
              });

              var uniqueValue = _toConsumableArray(new Set(array));

              data.data = uniqueValue;
            }

            this.apiService.addNewColumn(this.addColumn.value).subscribe(function (data) {
              //
              var colIndex = _this10.columndata.findIndex(function (ele) {
                return ele.name.toLowerCase() === _this10.currentHeader.toLowerCase();
              });

              if (colIndex !== -1) {
                _this10.columndata.splice(colIndex, 1);
              }

              var index = _this10.headersList.indexOf(_this10.currentHeader);

              _this10.headersList[index] = _this10.addColumn.value.col_name;

              _this10.columndata.push({
                name: _this10.addColumn.value.col_name,
                value: 'Additional_Field'
              });

              _this10.updateData.updateHeadersList(_this10.headersList); //


              _this10.showToster('success', 'Column added successfuly');

              _this10.loading = false;

              _this10.closeDialog();
            }, function (error) {
              _this10.loading = false;

              _this10.showToster('error', 'Error in adding column');

              console.log("error add column", error);
            });
          } else {
            if (!this.selectedValue) return; // var index = this.columndata.findIndex(ele => ele.name === this.currentHeader)

            var index = this.columndata.findIndex(function (ele) {
              return ele.name.toLowerCase() === _this10.currentHeader.toLowerCase();
            });

            if (index === -1) {
              this.columndata.push({
                name: this.currentHeader,
                value: this.selectedValue
              });
            } else {
              this.columndata[index] = {
                name: this.currentHeader,
                value: this.selectedValue
              }; // this.columndata.splice(index,1,{ name: this.currentHeader, value: this.selectedValue })
              // this.columndata.splice(index, 1)
              // this.columndata.push()
            }

            this.updateData.updateSelectColumnData(this.columndata);
            if (this.selectedValue.toLowerCase() === 'gender') this.updateData.updateSelectGenderData({
              changeGenderArray: this.changeGenderArray,
              header: this.currentHeader
            });
            if (this.selectedValue.toLowerCase() === 'getemail') this.updateData.updateGetEmailList({
              changeEmailArray: this.changeEmailArray,
              header: this.currentHeader
            });
            if (this.selectedValue.toLowerCase() === 'getsms') this.updateData.updateGetSmsList({
              changeSmsArray: this.changeSmsArray,
              header: this.currentHeader
            });
            this.selectedValue = "";
            this.closeDialog();
          }
        }
      }, {
        key: "closeDialog",
        value: function closeDialog() {
          this.dialogRef.close();
        }
      }, {
        key: "genderModalData",
        value: function genderModalData() {
          this.genderModal = true;
          this.yesNoModal = false;
          this.getSmsModal = false;
          this.getEmailModal = false;
          var index = this.headersList.indexOf(this.currentHeader);
          var array = [];
          this.csvData.map(function (ele, i) {
            if (ele[index] === '') return;
            array.push(ele[index]);
          });
          this.genderUniqueArray = _toConsumableArray(new Set(array));

          for (var i = 0; i < this.genderUniqueArray.length; i++) {
            this.changeGenderArray.push({
              name: this.genderUniqueArray[i],
              value: '2'
            });
          }
        }
      }, {
        key: "getSmsModalData",
        value: function getSmsModalData() {
          this.genderModal = false;
          this.yesNoModal = false;
          this.getSmsModal = true;
          this.getEmailModal = false;
          var index = this.headersList.indexOf(this.currentHeader);
          var newData = [];
          this.csvData.map(function (ele) {
            if (ele[index] === '') return;
            newData.push(ele[index]);
          });
          this.getSmsUniqueArray = _toConsumableArray(new Set(newData));

          for (var i = 0; i < this.getSmsUniqueArray.length; i++) {
            this.changeSmsArray.push({
              name: this.getSmsUniqueArray[i],
              value: '1'
            });
          }
        }
      }, {
        key: "getEmailModalData",
        value: function getEmailModalData() {
          this.genderModal = false;
          this.yesNoModal = false;
          this.getSmsModal = false;
          this.getEmailModal = true;
          var index = this.headersList.indexOf(this.currentHeader);
          var newData = [];
          this.csvData.map(function (ele) {
            if (ele[index] === '') return;
            newData.push(ele[index]);
          });
          this.getMailUniqueArray = _toConsumableArray(new Set(newData));

          for (var i = 0; i < this.getMailUniqueArray.length; i++) {
            this.changeEmailArray.push({
              name: this.getMailUniqueArray[i],
              value: '1'
            });
          }
        }
      }, {
        key: "selectColumn",
        value: function selectColumn(value) {
          if (value === "new") {
            this.showInput = true;
            this.genderModal = false;
            this.getEmailModal = false;
            this.getSmsModal = false;
          } else {
            this.showInput = false;
            this.genderModal = false;
            this.getEmailModal = false;
            this.getSmsModal = false;
            this.selectedValue = value;

            if (value.toLowerCase() === 'gender') {
              this.genderModalData();
            }

            if (value.toLowerCase() === 'getemail') {
              this.getEmailModalData();
            }

            if (value.toLowerCase() === 'getsms') {
              this.getSmsModalData();
            }
          }
        }
      }, {
        key: "selectGender",
        value: function selectGender(value, gender) {
          var index = this.changeGenderArray.findIndex(function (ele) {
            return ele.name === gender;
          });

          if (index === -1) {
            this.changeGenderArray.push({
              name: gender,
              value: value
            });
          } else {
            this.changeGenderArray.splice(index, 1);
            this.changeGenderArray.push({
              name: gender,
              value: value
            });
          }

          this.selectedValue = 'Gender';
        }
      }, {
        key: "selectYesNo",
        value: function selectYesNo(value, data, gettype) {
          if (gettype == 'getemail') {
            var index = this.changeEmailArray.findIndex(function (ele) {
              return ele.name === data;
            });

            if (index === -1) {
              this.changeEmailArray.push({
                name: data,
                value: value
              });
            } else {
              this.changeEmailArray.splice(index, 1);
              this.changeEmailArray.push({
                name: data,
                value: value
              });
            }

            this.selectedValue = 'GetEmail';
            return;
          }

          if (gettype == 'getsms') {
            var index = this.changeSmsArray.findIndex(function (ele) {
              return ele.name === data;
            });

            if (index === -1) {
              this.changeSmsArray.push({
                name: data,
                value: value
              });
            } else {
              this.changeSmsArray.splice(index, 1);
              this.changeSmsArray.push({
                name: data,
                value: value
              });
            }

            this.selectedValue = 'GetSMS';
            return;
          }
        }
      }, {
        key: "showToster",
        value: function showToster(icon, title) {
          var Toast = sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            onOpen: function onOpen(toast) {
              toast.addEventListener('mouseenter', sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.stopTimer);
              toast.addEventListener('mouseleave', sweetalert2__WEBPACK_IMPORTED_MODULE_8___default.a.resumeTimer);
            }
          });
          Toast.fire({
            icon: icon,
            title: title
          });
        }
      }, {
        key: "preSelectionGender",
        value: function preSelectionGender(value) {
          var _this11 = this;

          var t;
          this.updateData.genderData.subscribe(function (res) {
            return __awaiter(_this11, void 0, void 0,
            /*#__PURE__*/
            regeneratorRuntime.mark(function _callee() {
              return regeneratorRuntime.wrap(function _callee$(_context) {
                while (1) {
                  switch (_context.prev = _context.next) {
                    case 0:
                      if (res && res.changeGenderArray && res.changeGenderArray.length > 0) {
                        t = res.changeGenderArray;
                      }

                    case 1:
                    case "end":
                      return _context.stop();
                  }
                }
              }, _callee);
            }));
          });

          if (t && t.length > 0) {
            var index = t.findIndex(function (ele) {
              return ele.name === value;
            });
            if (index === -1) return;
            return t[index].value;
          }
        }
      }, {
        key: "preSelectionGetEmail",
        value: function preSelectionGetEmail(value) {
          var _this12 = this;

          var t;
          this.updateData.getEmailData.subscribe(function (res) {
            return __awaiter(_this12, void 0, void 0,
            /*#__PURE__*/
            regeneratorRuntime.mark(function _callee2() {
              return regeneratorRuntime.wrap(function _callee2$(_context2) {
                while (1) {
                  switch (_context2.prev = _context2.next) {
                    case 0:
                      if (res && res.changeEmailArray && res.changeEmailArray.length > 0) {
                        t = res.changeEmailArray;
                      }

                    case 1:
                    case "end":
                      return _context2.stop();
                  }
                }
              }, _callee2);
            }));
          });

          if (t && t.length > 0) {
            var index = t.findIndex(function (ele) {
              return ele.name === value;
            });
            if (index === -1) return;
            return t[index].value;
          }
        }
      }, {
        key: "preSelectionGetSMS",
        value: function preSelectionGetSMS(value) {
          var _this13 = this;

          var t;
          this.updateData.getSmsData.subscribe(function (res) {
            return __awaiter(_this13, void 0, void 0,
            /*#__PURE__*/
            regeneratorRuntime.mark(function _callee3() {
              return regeneratorRuntime.wrap(function _callee3$(_context3) {
                while (1) {
                  switch (_context3.prev = _context3.next) {
                    case 0:
                      if (res && res.changeSmsArray && res.changeSmsArray.length > 0) {
                        t = res.changeSmsArray;
                      }

                    case 1:
                    case "end":
                      return _context3.stop();
                  }
                }
              }, _callee3);
            }));
          });

          if (t && t.length > 0) {
            var index = t.findIndex(function (ele) {
              return ele.name === value;
            });
            if (index === -1) return;
            return t[index].value;
          }
        }
      }]);

      return DialogOverviewExampleDialog;
    }();

    DialogOverviewExampleDialog.ɵfac = function DialogOverviewExampleDialog_Factory(t) {
      return new (t || DialogOverviewExampleDialog)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_router__WEBPACK_IMPORTED_MODULE_2__["ActivatedRoute"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_forms__WEBPACK_IMPORTED_MODULE_5__["FormBuilder"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_Service_api_service_service__WEBPACK_IMPORTED_MODULE_6__["ApiServiceService"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_Service_updatedata_service__WEBPACK_IMPORTED_MODULE_7__["UpdatedataService"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MatDialogRef"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MAT_DIALOG_DATA"]));
    };

    DialogOverviewExampleDialog.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: DialogOverviewExampleDialog,
      selectors: [["dialog-overview-example-dialog"]],
      decls: 21,
      vars: 11,
      consts: [["mat-dialog-content", ""], [1, "row"], [1, "col-4", "col-md-4", "col-sm-4"], ["type", "none", "fxFlex.gt-lg", "50%"], [4, "ngFor", "ngForOf"], [1, "col-8", "col-md-8", "col-sm-8"], [4, "ngIf"], [1, "example-form", 3, "formGroup", "ngSubmit"], ["customLoadingTemplate", ""], [3, "value", "valueChange"], ["label", "Predefined Columns"], [3, "value", 4, "ngFor", "ngForOf"], ["label", "Membership Field", 4, "ngIf"], ["label", "Custom Field", 4, "ngIf"], [3, "value"], ["label", "Membership Field"], ["label", "Custom Field"], ["value", "new"], ["class", "example-full-width", 4, "ngIf"], ["id", "example-radio-group-label"], ["aria-labelledby", "example-radio-group-label", "formControlName", "datatype", 1, "example-radio-group"], ["class", "example-radio-button", 3, "disabled", "value", 4, "ngFor", "ngForOf"], [1, "example-full-width"], ["matInput", "", "formControlName", "col_name", "placeholder", "Column Name", 3, "errorStateMatcher"], [1, "example-radio-button", 3, "disabled", "value"], ["type", "button", "mat-raised-button", "", 3, "click"], ["type", "submit", "mat-raised-button", "", "color", "primary", 3, "disabled"], ["class", "smallloader", 4, "ngIf"], [1, "smallloader"], ["mat-raised-button", "", "color", "primary", 3, "click"]],
      template: function DialogOverviewExampleDialog_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](3, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "ul", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpipe"](7, "uppercase");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](8, DialogOverviewExampleDialog_div_8_Template, 3, 2, "div", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](9, "div", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](10, DialogOverviewExampleDialog_mat_form_field_10_Template, 8, 4, "mat-form-field", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](11, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](12, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("ngSubmit", function DialogOverviewExampleDialog_Template_form_ngSubmit_12_listener($event) {
            return ctx.submit();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](13, DialogOverviewExampleDialog_div_13_Template, 7, 3, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](14, DialogOverviewExampleDialog_div_14_Template, 6, 3, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](15, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](16, DialogOverviewExampleDialog_div_16_Template, 8, 2, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](17, DialogOverviewExampleDialog_div_17_Template, 8, 2, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](18, DialogOverviewExampleDialog_div_18_Template, 9, 2, "div", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](19, DialogOverviewExampleDialog_ng_template_19_Template, 0, 0, "ng-template", null, 8, _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplateRefExtractor"]);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtextInterpolate"](_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵpipeBind1"](7, 9, ctx.data.headersList[ctx.data.indx]));

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngForOf", ctx.data.csvData);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", !ctx.showInput);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("formGroup", ctx.addColumn);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.showInput);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.getEmailModal || ctx.getSmsModal || ctx.genderModal ? false : true);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.getEmailModal);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.getSmsModal);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.genderModal);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_12__["MatDialogContent"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_13__["DefaultFlexDirective"], _angular_common__WEBPACK_IMPORTED_MODULE_9__["NgForOf"], _angular_common__WEBPACK_IMPORTED_MODULE_9__["NgIf"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["ɵangular_packages_forms_forms_y"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["NgControlStatusGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["FormGroupDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatLabel"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatSelect"], _angular_material_core__WEBPACK_IMPORTED_MODULE_14__["MatOptgroup"], _angular_material_core__WEBPACK_IMPORTED_MODULE_14__["MatOption"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatRadioGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["FormControlName"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatInput"], _angular_forms__WEBPACK_IMPORTED_MODULE_5__["DefaultValueAccessor"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatError"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatRadioButton"], _angular_material__WEBPACK_IMPORTED_MODULE_12__["MatButton"]],
      pipes: [_angular_common__WEBPACK_IMPORTED_MODULE_9__["UpperCasePipe"], _angular_common__WEBPACK_IMPORTED_MODULE_9__["TitleCasePipe"]],
      styles: ["button[_ngcontent-%COMP%]:focus{\r\n\toutline:none!important;\r\n\tbox-shadow:none!important;\r\n}\r\n\r\n.file_uploading[_ngcontent-%COMP%] {\r\n    padding: 20px 40px 0;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]:hover {\r\n    box-shadow: 0px 1px 11px 1px #0e0e0e3b;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%] {\r\n    width: 100%!important;\r\n    max-width: 100%!important;\r\n    background: #ffffff;\r\n    max-height: 350px;\r\n    overflow: auto;\r\n    border: 1px solid #d4d4d4;\r\n    border-radius: 4px;\r\n    padding: 10px 0 10px;\r\n    margin: 10px auto;\r\n}\r\n\r\n.header-set[_ngcontent-%COMP%]   .colms_custom[_ngcontent-%COMP%] {\r\n    border: 2px solid #28a745;\r\n}\r\n\r\n.outer_flx[_ngcontent-%COMP%] {\r\n    width: 100%!important;\r\n    max-width: 25%!important;\r\n    padding: 0 10px;\r\n\r\n}\r\n\r\n.add_background[_ngcontent-%COMP%] {\r\n    background: #fff;\r\n}\r\n\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%] {\r\n    background: transparent;\r\n    border-radius: 3px;\r\n    padding: 0;\r\n    margin: 0 15px;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   h3[_ngcontent-%COMP%] {\r\n\tcolor: #182433!important;\r\n    font-size: 20px;\r\n    padding: 10px 15px 10px;\r\n    font-weight: 600;\r\n    margin: 0;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   h4[_ngcontent-%COMP%] {\r\n    font-size: 15px;\r\n    text-transform: capitalize;\r\n    color: #182433;\r\n    font-weight: 500;\r\n    background: #f3f9ff;\r\n    width: 100%;\r\n    display: inline-block;\r\n    padding:10px 15px;\r\n    margin: 20px 0 20px 0;\r\n}\r\n\r\np.text-muted[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    padding: 0 20px 0 0px;\r\n}\r\n\r\nmat-list.mat-list.mat-list-base[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 100%!important;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base.mat-primary[_ngcontent-%COMP%] {\r\n    margin: 0 15px 10px;\r\n}\r\n\r\np.mat-line.text-muted[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    padding: 0 15px 0 0;\r\n}\r\n\r\n.text-muted[_ngcontent-%COMP%] {\r\n    color: #182433 !important;\r\n    font-size: 14px!important;\r\n    font-weight: 500!important;\r\n    padding: 0 0 0 20px;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 100%!important;\r\n    margin: 0px 0 0;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]{\r\n\tborder-top: 1px solid #e8e8e8;\r\n}\r\n\r\n.colms_custom[_ngcontent-%COMP%]   ul[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]:last-child{\r\n\tborder-top: 1px solid #e8e8e8;\r\n}\r\n\r\nmat-list-item.mat-list-item.ng-star-inserted[_ngcontent-%COMP%] {\r\n    border-top: 1px solid #e8e8e8;\r\n    height:44px!important;\r\n}\r\n\r\nmat-list-item.mat-list-item.ng-star-inserted[_ngcontent-%COMP%]:last-child{\r\n\tborder-bottom: 1px solid #e8e8e8;\r\n}\r\n\r\n.read_more[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    text-align: center;\r\n    padding:10px 0 0px; \r\n}\r\n\r\n.read_more[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    font-size: 15px;\r\n    color: #182433;\r\n \ttext-decoration:none;\r\n    font-weight: 600;\r\n}\r\n\r\n.read_more[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{\r\n\tcolor: #3788e5;\r\n}\r\n\r\nol[_ngcontent-%COMP%], ul[_ngcontent-%COMP%], dl[_ngcontent-%COMP%]{\r\n\tmargin:0px;\r\n\tpadding:0px;\r\n}\r\n\r\nmat-card.mat-card[_ngcontent-%COMP%] {\r\n    background: transparent;\r\n    box-shadow: none !important;\r\n    max-width: 1170px;\r\n    margin: 20px auto;\r\n}\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%] {\r\n    padding: 20px !important;\r\n    margin: 15px;\r\n    background: #fff;\r\n    box-shadow: 0px 0px 11px 0px #0002 !important;\r\n    border: 1px solid transparent;\r\n}\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]   .mat-icon[_ngcontent-%COMP%] {\r\n    width: auto;\r\n    height: auto;\r\n    font-size: 60px;\r\n    color: #e4e4e4;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   h4[_ngcontent-%COMP%] {\r\n    padding: 0 15px;\r\n    font-weight: 500;\r\n    font-size: 16px;\r\n    margin: 0 0 10px;\r\n    display: inline;\r\n    background: transparent;\r\n}\r\n\r\n.import-cards[_ngcontent-%COMP%]{\r\n\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   p.p-2[_ngcontent-%COMP%] {\r\n    color: #989898;    \r\n    font-size: 15px;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   button.mat-raised-button.filter_Button[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    max-width: 120px;\r\n    line-height: 40px;\r\n    box-shadow: none;\r\n    border: 1px solid #e4e4e4;\r\n    color: #e4e4e4;\r\n}\r\n\r\n.mat-card[_ngcontent-%COMP%]   button.mat-raised-button.select_col_btn[_ngcontent-%COMP%]{\r\n    width: auto;\r\n    max-width: 100%;\r\n    line-height: 40px;\r\n    box-shadow: none;\r\n    border: 1px solid #e4e4e4;\r\n    color: #e4e4e4;\r\n    padding: 0px 8px;\r\n}\r\n\r\n.btn.clear_col_btn[_ngcontent-%COMP%]{\r\n    line-height: 1;\r\n    padding: 5px;\r\n    }\r\n\r\n.vma-text[_ngcontent-%COMP%]{\r\n    vertical-align: middle;\r\n    }\r\n\r\n.clear_hide[_ngcontent-%COMP%]   button.clear_col_btn[_ngcontent-%COMP%]\r\n    {\r\n    display: none;\r\n    }\r\n\r\n.clear_col_btn[_ngcontent-%COMP%]   .material-icons[_ngcontent-%COMP%]{\r\n    font-size: 16px;\r\n    vertical-align: middle;\r\n    margin-left: 5px;\r\n    }\r\n\r\n.m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]:hover{\r\n    border: 1px solid #1e88e5;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover   *[_ngcontent-%COMP%], .m-auto[_ngcontent-%COMP%]   .mat-card[_ngcontent-%COMP%]:hover   button.mat-primary[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    color: #fff;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover   span[_ngcontent-%COMP%] {\r\n    color: #fff;\r\n}\r\n\r\nbutton.mat-raised-button.mat-button-base[_ngcontent-%COMP%]:hover {\r\n    background: #1e88e5;\r\n    color: #fff !important;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar-thumb {\r\n    background: #acacac;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar-track {\r\n    background: #ddd;\r\n}\r\n\r\n.colms_custom.col-md-4[_ngcontent-%COMP%]::-webkit-scrollbar {\r\n    width: 3px;\r\n}\r\n\r\nspan.material-icons.icon.ng-star-inserted[_ngcontent-%COMP%] {\r\n    margin: 7px 10px;\r\n    position: absolute;\r\n    right: 0;\r\n}\r\n\r\n.page-content[_ngcontent-%COMP%] {\r\n    padding: 0 !important;\r\n}\r\n\r\n.full-page[_ngcontent-%COMP%] {\r\n    display: flex;\r\n    height: 100%;\r\n}\r\n\r\naside.sidebar[_ngcontent-%COMP%] {\r\n    width: 300px;\r\n    background: #fff;\r\n    padding: 15px;\r\n    border-right: 1px solid #e2e2e2;\r\n}\r\n\r\n.main-content[_ngcontent-%COMP%] {\r\n    width: calc(100% - 300px);\r\n    background: #fff;\r\n    align-items: center;\r\n    display: inline-flex;\r\n    flex-wrap: wrap;\r\n    padding-top: 60px;\r\n    height: 100vh;\r\n    overflow: auto;\r\n}\r\n\r\n.sidebarBtmBtn[_ngcontent-%COMP%]{\r\n    align-self: flex-end;\r\n    margin: 0 auto;\r\n}\r\n\r\n.sidebar[_ngcontent-%COMP%]   button.btn[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    align-self: flex-end;\r\n}\r\n\r\n.content[_ngcontent-%COMP%] {\r\n    display: inline-flex;\r\n    height: 100%;\r\n    flex-wrap: wrap;\r\n    align-items: stretch;\r\n}\r\n\r\nul.import-steps[_ngcontent-%COMP%]   li[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    margin-left: 20px;\r\n    position: relative;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%] {\r\n    padding: 10px 20px;\r\n    background: #f4f4f4;\r\n    display: flex;\r\n    align-items: baseline;\r\n    justify-content: space-between;\r\n    width: calc(100% - 300px);\r\n    position: fixed;\r\n    top: 0;\r\n    z-index:9;\r\n}\r\n\r\nbutton.btn.backk[_ngcontent-%COMP%] {\r\n    border: 1px solid #222;\r\n    color: #222;\r\n    border-radius: 3px;\r\n    margin-right: 15px;\r\n    line-height: 1em;\r\n    text-transform: capitalize;\r\n}\r\n\r\nbutton.btn.backk[_ngcontent-%COMP%]:hover {\r\n    border: 1px solid #3788e5;\r\n\r\n    color: #3788e5;\r\n}\r\n\r\nul.import-steps[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    font-size: 14px;\r\n    font-weight: 500;\r\n    position: relative;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   span[_ngcontent-%COMP%] {\r\n    font-weight: 600;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   h5[_ngcontent-%COMP%] {\r\n    text-align: center;\r\n    color: #1e88e5;\r\n    position: relative;\r\n    width: auto;\r\n    display: inline-block;\r\n    margin-bottom: 20px;\r\n    font-size:20px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   p[_ngcontent-%COMP%] {\r\n    font-size:18px;\r\n}\r\n\r\n.numberIcon[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    width: 22px;\r\n    height: 22px;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    border-radius: 50%;\r\n    margin-right: 10px;\r\n    padding: 3px;\r\n    font-size: 16px;\r\n    text-align: center;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   .numberIconTb[_ngcontent-%COMP%] {\r\n    display: inline-block;\r\n    width: 22px;\r\n    height: 22px;\r\n    background: #fff;\r\n    color: #000;\r\n    margin-right: 5px;\r\n    padding: 1px;\r\n    border-radius: 50%;\r\n    text-align: center;\r\n    border: 1px solid #2c2c2c;\r\n    line-height: 1.4em;\r\n}\r\n\r\n.tp-bar[_ngcontent-%COMP%]   .activeStep[_ngcontent-%COMP%]   .numberIconTb[_ngcontent-%COMP%] {\r\n    margin-right: 5px;\r\n    padding: 1px;\r\n    background: #1e88e5;\r\n    color: #fff;\r\n    line-height: 1.4em;\r\n    border: 1px solid #3788e5;\r\n}\r\n\r\n.activeStep[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{\r\n    color: #1e88e5;\r\n}\r\n\r\n.import-steps[_ngcontent-%COMP%]   .fa-check-circle[_ngcontent-%COMP%]{\r\n    color: #00b33c;\r\n    font-size: 18px;\r\n    margin-right: 5px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%] {\r\n    text-align: center;\r\n    margin-top: 60px;\r\n}\r\n\r\n.sidetext[_ngcontent-%COMP%]   p[_ngcontent-%COMP%] {\r\n    text-align: left;\r\n}\r\n\r\n.header-set[_ngcontent-%COMP%]   button.select_col_btn.mat-raised-button.mat-button-base.mat-primary[_ngcontent-%COMP%] {\r\n    background: #00b33c;\r\n}\r\n\r\n\r\n\r\n@media(max-width: 767px){\r\n.outer_flx[_ngcontent-%COMP%]{max-width:100%!important; width: 100%;}\r\n.add_background[_ngcontent-%COMP%]{padding:10px 0 0px!important;}\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%]{font-size: 12px;padding: 3px 11px;}\r\n}\r\n\r\n@media(min-width:768px) and (max-width: 1024px){\r\n.mat-card[_ngcontent-%COMP%]   .mat-card-title[_ngcontent-%COMP%]{font-size:15px;}\r\n.outer_flx[_ngcontent-%COMP%]{max-width:50%!important; width: 100%;}\r\n.colms_custom[_ngcontent-%COMP%]   h3[_ngcontent-%COMP%]{font-size:16px;}\r\nbutton.mat-button.mat-button-base[_ngcontent-%COMP%]{font-size: 12px;padding: 3px 14px;}\r\n}\r\n\r\n.icon[_ngcontent-%COMP%]{\r\n    background-color: #00b33c;\r\n    border-radius: 15px;\r\n    color: whitesmoke;\r\n    float:right;\r\n    margin : 10px 10px;\r\n    padding: 3px;\r\n    font-size: 18px;\r\n}\r\n\r\n.example-form[_ngcontent-%COMP%] {\r\n    min-width: 150px;\r\n    max-width: 500px;\r\n    width: 100%;\r\n}\r\n\r\n.example-full-width[_ngcontent-%COMP%] {\r\n    width: 100%;\r\n}\r\n\r\n.example-radio-group[_ngcontent-%COMP%] {\r\n    display: flex;\r\n    flex-direction: column;\r\n    margin: 15px 0;\r\n}\r\n\r\n.example-radio-button[_ngcontent-%COMP%] {\r\n    margin: 5px;\r\n}\r\n\r\n.mat-raised-butto.importBtn[_ngcontent-%COMP%]:active, .mat-raised-butto.importBtn[_ngcontent-%COMP%]:hover{\r\n    color: #fff;\r\n}\r\n\r\n.mat-raised-butto.insertBtn[_ngcontent-%COMP%]:active, .insertBtn[_ngcontent-%COMP%]:hover{\r\n    color: #fff;\r\n}\r\n\r\n.smallloader[_ngcontent-%COMP%] {\r\n    border: 4px solid #f3f3f3; \r\n    border-top: 4px solid #3498db; \r\n    border-radius: 50%;\r\n    width: 24px;\r\n    height: 24px;\r\n    display: inline-block;\r\n    -webkit-animation: loaderspin 2s linear infinite;\r\n            animation: loaderspin 2s linear infinite;\r\n  }\r\n\r\n@-webkit-keyframes loaderspin {\r\n    0% { -webkit-transform: rotate(0deg); }\r\n    100% { -webkit-transform: rotate(360deg); }\r\n  }\r\n\r\n@keyframes loaderspin {\r\n    0% { transform: rotate(0deg); }\r\n    100% { transform: rotate(360deg); }\r\n  }\r\n\r\n.insertBtnBtm[_ngcontent-%COMP%]{\r\n    display: block;\r\n    text-align: center;\r\n    margin: 30px auto!important;\r\n    width: 50%;\r\n  }\r\n\r\n.DownloadRejected[_ngcontent-%COMP%]{\r\n    margin: 0 auto!important;\r\n    display: block;\r\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvZmlsZXVwbG9hZC9maWxldXBsb2FkLmNvbXBvbmVudC5jc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Q0FDQyxzQkFBc0I7Q0FDdEIseUJBQXlCO0FBQzFCOztBQUVBO0lBQ0ksb0JBQW9CO0FBQ3hCOztBQUVBO0lBQ0ksc0NBQXNDO0FBQzFDOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLHlCQUF5QjtJQUN6QixtQkFBbUI7SUFDbkIsaUJBQWlCO0lBQ2pCLGNBQWM7SUFDZCx5QkFBeUI7SUFDekIsa0JBQWtCO0lBQ2xCLG9CQUFvQjtJQUNwQixpQkFBaUI7QUFDckI7O0FBRUE7SUFDSSx5QkFBeUI7QUFDN0I7O0FBQ0E7SUFDSSxxQkFBcUI7SUFDckIsd0JBQXdCO0lBQ3hCLGVBQWU7QUFDbkI7c0JBQ3NCO0FBQ3RCOztBQUVBO0lBQ0ksZ0JBQWdCO0FBQ3BCOztBQUVBO0lBQ0ksdUJBQXVCO0lBQ3ZCLGtCQUFrQjtJQUNsQixVQUFVO0lBQ1YsY0FBYztBQUNsQjs7QUFFQTtDQUNDLHdCQUF3QjtJQUNyQixlQUFlO0lBQ2YsdUJBQXVCO0lBQ3ZCLGdCQUFnQjtJQUNoQixTQUFTO0FBQ2I7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsMEJBQTBCO0lBQzFCLGNBQWM7SUFDZCxnQkFBZ0I7SUFDaEIsbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxxQkFBcUI7SUFDckIsaUJBQWlCO0lBQ2pCLHFCQUFxQjtBQUN6Qjs7QUFFQTtJQUNJLHFCQUFxQjtBQUN6Qjs7QUFFQTtJQUNJLFdBQVc7SUFDWCx5QkFBeUI7QUFDN0I7O0FBRUE7SUFDSSxtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSxtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSx5QkFBeUI7SUFDekIseUJBQXlCO0lBQ3pCLDBCQUEwQjtJQUMxQixtQkFBbUI7QUFDdkI7O0FBRUE7SUFDSSxXQUFXO0lBQ1gseUJBQXlCO0lBQ3pCLGVBQWU7QUFDbkI7O0FBRUE7Q0FDQyw2QkFBNkI7QUFDOUI7O0FBRUE7Q0FDQyw2QkFBNkI7QUFDOUI7O0FBRUE7SUFDSSw2QkFBNkI7SUFDN0IscUJBQXFCO0FBQ3pCOztBQUVBO0NBQ0MsZ0NBQWdDO0FBQ2pDOztBQUVBO0lBQ0ksV0FBVztJQUNYLGtCQUFrQjtJQUNsQixrQkFBa0I7QUFDdEI7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsY0FBYztFQUNoQixvQkFBb0I7SUFDbEIsZ0JBQWdCO0FBQ3BCOztBQUVBO0NBQ0MsY0FBYztBQUNmOztBQUVBO0NBQ0MsVUFBVTtDQUNWLFdBQVc7QUFDWjs7QUFFQTtJQUNJLHVCQUF1QjtJQUN2QiwyQkFBMkI7SUFDM0IsaUJBQWlCO0lBQ2pCLGlCQUFpQjtBQUNyQjs7QUFFQTtJQUNJLHdCQUF3QjtJQUN4QixZQUFZO0lBQ1osZ0JBQWdCO0lBQ2hCLDZDQUE2QztJQUM3Qyw2QkFBNkI7QUFDakM7O0FBRUE7SUFDSSxXQUFXO0lBQ1gsWUFBWTtJQUNaLGVBQWU7SUFDZixjQUFjO0FBQ2xCOztBQUVBO0lBQ0ksZUFBZTtJQUNmLGdCQUFnQjtJQUNoQixlQUFlO0lBQ2YsZ0JBQWdCO0lBQ2hCLGVBQWU7SUFDZix1QkFBdUI7QUFDM0I7O0FBRUE7O0FBRUE7O0FBQ0E7SUFDSSxjQUFjO0lBQ2QsZUFBZTtBQUNuQjs7QUFFQTtJQUNJLFdBQVc7SUFDWCxnQkFBZ0I7SUFDaEIsaUJBQWlCO0lBQ2pCLGdCQUFnQjtJQUNoQix5QkFBeUI7SUFDekIsY0FBYztBQUNsQjs7QUFFQTtJQUNJLFdBQVc7SUFDWCxlQUFlO0lBQ2YsaUJBQWlCO0lBQ2pCLGdCQUFnQjtJQUNoQix5QkFBeUI7SUFDekIsY0FBYztJQUNkLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLGNBQWM7SUFDZCxZQUFZO0lBQ1o7O0FBQ0E7SUFDQSxzQkFBc0I7SUFDdEI7O0FBQ0E7O0lBRUEsYUFBYTtJQUNiOztBQUNBO0lBQ0EsZUFBZTtJQUNmLHNCQUFzQjtJQUN0QixnQkFBZ0I7SUFDaEI7O0FBR0o7SUFDSSx5QkFBeUI7QUFDN0I7O0FBRUE7SUFDSSxXQUFXO0FBQ2Y7O0FBRUE7SUFDSSxXQUFXO0FBQ2Y7O0FBRUE7SUFDSSxtQkFBbUI7SUFDbkIsc0JBQXNCO0FBQzFCOztBQUVBO0lBQ0ksbUJBQW1CO0FBQ3ZCOztBQUVBO0lBQ0ksZ0JBQWdCO0FBQ3BCOztBQUNBO0lBQ0ksVUFBVTtBQUNkOztBQUVBO0lBQ0ksZ0JBQWdCO0lBQ2hCLGtCQUFrQjtJQUNsQixRQUFRO0FBQ1o7O0FBRUE7SUFDSSxxQkFBcUI7QUFDekI7O0FBRUE7SUFDSSxhQUFhO0lBQ2IsWUFBWTtBQUNoQjs7QUFFQTtJQUNJLFlBQVk7SUFDWixnQkFBZ0I7SUFDaEIsYUFBYTtJQUNiLCtCQUErQjtBQUNuQzs7QUFFQTtJQUNJLHlCQUF5QjtJQUN6QixnQkFBZ0I7SUFDaEIsbUJBQW1CO0lBQ25CLG9CQUFvQjtJQUNwQixlQUFlO0lBQ2YsaUJBQWlCO0lBQ2pCLGFBQWE7SUFDYixjQUFjO0FBQ2xCOztBQUVBO0lBQ0ksb0JBQW9CO0lBQ3BCLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxXQUFXO0lBQ1gsbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxvQkFBb0I7QUFDeEI7O0FBRUE7SUFDSSxvQkFBb0I7SUFDcEIsWUFBWTtJQUNaLGVBQWU7SUFDZixvQkFBb0I7QUFDeEI7O0FBRUE7SUFDSSxxQkFBcUI7SUFDckIsaUJBQWlCO0lBQ2pCLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLGtCQUFrQjtJQUNsQixtQkFBbUI7SUFDbkIsYUFBYTtJQUNiLHFCQUFxQjtJQUNyQiw4QkFBOEI7SUFDOUIseUJBQXlCO0lBQ3pCLGVBQWU7SUFDZixNQUFNO0lBQ04sU0FBUztBQUNiOztBQUVBO0lBQ0ksc0JBQXNCO0lBQ3RCLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsa0JBQWtCO0lBQ2xCLGdCQUFnQjtJQUNoQiwwQkFBMEI7QUFDOUI7O0FBRUE7SUFDSSx5QkFBeUI7O0lBRXpCLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxlQUFlO0lBQ2YsZ0JBQWdCO0lBQ2hCLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLGdCQUFnQjtBQUNwQjs7QUFHQTtJQUNJLGtCQUFrQjtJQUNsQixjQUFjO0lBQ2Qsa0JBQWtCO0lBQ2xCLFdBQVc7SUFDWCxxQkFBcUI7SUFDckIsbUJBQW1CO0lBQ25CLGNBQWM7QUFDbEI7O0FBRUE7SUFDSSxjQUFjO0FBQ2xCOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLFdBQVc7SUFDWCxZQUFZO0lBQ1osbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsa0JBQWtCO0lBQ2xCLFlBQVk7SUFDWixlQUFlO0lBQ2Ysa0JBQWtCO0FBQ3RCOztBQUVBO0lBQ0kscUJBQXFCO0lBQ3JCLFdBQVc7SUFDWCxZQUFZO0lBQ1osZ0JBQWdCO0lBQ2hCLFdBQVc7SUFDWCxpQkFBaUI7SUFDakIsWUFBWTtJQUNaLGtCQUFrQjtJQUNsQixrQkFBa0I7SUFDbEIseUJBQXlCO0lBQ3pCLGtCQUFrQjtBQUN0Qjs7QUFDQTtJQUNJLGlCQUFpQjtJQUNqQixZQUFZO0lBQ1osbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIseUJBQXlCO0FBQzdCOztBQUVBO0lBQ0ksY0FBYztBQUNsQjs7QUFFQTtJQUNJLGNBQWM7SUFDZCxlQUFlO0lBQ2YsaUJBQWlCO0FBQ3JCOztBQUVBO0lBQ0ksa0JBQWtCO0lBQ2xCLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLGdCQUFnQjtBQUNwQjs7QUFFQTtJQUNJLG1CQUFtQjtBQUN2Qjs7QUFHQSwwRUFBMEU7O0FBRTFFO0FBQ0EsV0FBVyx3QkFBd0IsRUFBRSxXQUFXLENBQUM7QUFDakQsZ0JBQWdCLDRCQUE0QixDQUFDO0FBQzdDLGtDQUFrQyxlQUFlLENBQUMsaUJBQWlCLENBQUM7QUFDcEU7O0FBRUE7QUFDQSwwQkFBMEIsY0FBYyxDQUFDO0FBQ3pDLFdBQVcsdUJBQXVCLEVBQUUsV0FBVyxDQUFDO0FBQ2hELGlCQUFpQixjQUFjLENBQUM7QUFDaEMsa0NBQWtDLGVBQWUsQ0FBQyxpQkFBaUIsQ0FBQztBQUNwRTs7QUFFQTtJQUNJLHlCQUF5QjtJQUN6QixtQkFBbUI7SUFDbkIsaUJBQWlCO0lBQ2pCLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsWUFBWTtJQUNaLGVBQWU7QUFDbkI7O0FBRUE7SUFDSSxnQkFBZ0I7SUFDaEIsZ0JBQWdCO0lBQ2hCLFdBQVc7QUFDZjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFFQTtJQUNJLGFBQWE7SUFDYixzQkFBc0I7SUFDdEIsY0FBYztBQUNsQjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFFQTtJQUNJLFdBQVc7QUFDZjs7QUFDQTtJQUNJLFdBQVc7QUFDZjs7QUFHQTtJQUNJLHlCQUF5QixFQUFFLGVBQWU7SUFDMUMsNkJBQTZCLEVBQUUsU0FBUztJQUN4QyxrQkFBa0I7SUFDbEIsV0FBVztJQUNYLFlBQVk7SUFDWixxQkFBcUI7SUFDckIsZ0RBQXdDO1lBQXhDLHdDQUF3QztFQUMxQzs7QUFFQTtJQUNFLEtBQUssK0JBQStCLEVBQUU7SUFDdEMsT0FBTyxpQ0FBaUMsRUFBRTtFQUM1Qzs7QUFFQTtJQUNFLEtBQUssdUJBQXVCLEVBQUU7SUFDOUIsT0FBTyx5QkFBeUIsRUFBRTtFQUNwQzs7QUFFQTtJQUNFLGNBQWM7SUFDZCxrQkFBa0I7SUFDbEIsMkJBQTJCO0lBQzNCLFVBQVU7RUFDWjs7QUFHRjtJQUNJLHdCQUF3QjtJQUN4QixjQUFjO0FBQ2xCIiwiZmlsZSI6InNyYy9hcHAvZmlsZXVwbG9hZC9maWxldXBsb2FkLmNvbXBvbmVudC5jc3MiLCJzb3VyY2VzQ29udGVudCI6WyJidXR0b246Zm9jdXN7XHJcblx0b3V0bGluZTpub25lIWltcG9ydGFudDtcclxuXHRib3gtc2hhZG93Om5vbmUhaW1wb3J0YW50O1xyXG59XHJcblxyXG4uZmlsZV91cGxvYWRpbmcge1xyXG4gICAgcGFkZGluZzogMjBweCA0MHB4IDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20uY29sLW1kLTQ6aG92ZXIge1xyXG4gICAgYm94LXNoYWRvdzogMHB4IDFweCAxMXB4IDFweCAjMGUwZTBlM2I7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20ge1xyXG4gICAgd2lkdGg6IDEwMCUhaW1wb3J0YW50O1xyXG4gICAgbWF4LXdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxuICAgIGJhY2tncm91bmQ6ICNmZmZmZmY7XHJcbiAgICBtYXgtaGVpZ2h0OiAzNTBweDtcclxuICAgIG92ZXJmbG93OiBhdXRvO1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgI2Q0ZDRkNDtcclxuICAgIGJvcmRlci1yYWRpdXM6IDRweDtcclxuICAgIHBhZGRpbmc6IDEwcHggMCAxMHB4O1xyXG4gICAgbWFyZ2luOiAxMHB4IGF1dG87XHJcbn1cclxuXHJcbi5oZWFkZXItc2V0IC5jb2xtc19jdXN0b20ge1xyXG4gICAgYm9yZGVyOiAycHggc29saWQgIzI4YTc0NTtcclxufVxyXG4ub3V0ZXJfZmx4IHtcclxuICAgIHdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxuICAgIG1heC13aWR0aDogMjUlIWltcG9ydGFudDtcclxuICAgIHBhZGRpbmc6IDAgMTBweDtcclxuLyogIGZsZXg6IGluaGVyaXQhaW1wb3J0YW50O1xyXG4gICAgZGlzcGxheTogaW5oZXJpdDsqL1xyXG59XHJcblxyXG4uYWRkX2JhY2tncm91bmQge1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1idXR0b24ubWF0LWJ1dHRvbi1iYXNlIHtcclxuICAgIGJhY2tncm91bmQ6IHRyYW5zcGFyZW50O1xyXG4gICAgYm9yZGVyLXJhZGl1czogM3B4O1xyXG4gICAgcGFkZGluZzogMDtcclxuICAgIG1hcmdpbjogMCAxNXB4O1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tIGgzIHtcclxuXHRjb2xvcjogIzE4MjQzMyFpbXBvcnRhbnQ7XHJcbiAgICBmb250LXNpemU6IDIwcHg7XHJcbiAgICBwYWRkaW5nOiAxMHB4IDE1cHggMTBweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA2MDA7XHJcbiAgICBtYXJnaW46IDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gaDQge1xyXG4gICAgZm9udC1zaXplOiAxNXB4O1xyXG4gICAgdGV4dC10cmFuc2Zvcm06IGNhcGl0YWxpemU7XHJcbiAgICBjb2xvcjogIzE4MjQzMztcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZjNmOWZmO1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBwYWRkaW5nOjEwcHggMTVweDtcclxuICAgIG1hcmdpbjogMjBweCAwIDIwcHggMDtcclxufVxyXG5cclxucC50ZXh0LW11dGVkIHNwYW4ge1xyXG4gICAgcGFkZGluZzogMCAyMHB4IDAgMHB4O1xyXG59XHJcblxyXG5tYXQtbGlzdC5tYXQtbGlzdC5tYXQtbGlzdC1iYXNlIHtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG4gICAgbWF4LXdpZHRoOiAxMDAlIWltcG9ydGFudDtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZS5tYXQtcHJpbWFyeSB7XHJcbiAgICBtYXJnaW46IDAgMTVweCAxMHB4O1xyXG59XHJcblxyXG5wLm1hdC1saW5lLnRleHQtbXV0ZWQgc3BhbiB7XHJcbiAgICBwYWRkaW5nOiAwIDE1cHggMCAwO1xyXG59XHJcblxyXG4udGV4dC1tdXRlZCB7XHJcbiAgICBjb2xvcjogIzE4MjQzMyAhaW1wb3J0YW50O1xyXG4gICAgZm9udC1zaXplOiAxNHB4IWltcG9ydGFudDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDAhaW1wb3J0YW50O1xyXG4gICAgcGFkZGluZzogMCAwIDAgMjBweDtcclxufVxyXG5cclxuLmNvbG1zX2N1c3RvbSB1bCB7XHJcbiAgICB3aWR0aDogMTAwJTtcclxuICAgIG1heC13aWR0aDogMTAwJSFpbXBvcnRhbnQ7XHJcbiAgICBtYXJnaW46IDBweCAwIDA7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gdWwgbGl7XHJcblx0Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbn1cclxuXHJcbi5jb2xtc19jdXN0b20gdWwgbGk6bGFzdC1jaGlsZHtcclxuXHRib3JkZXItdG9wOiAxcHggc29saWQgI2U4ZThlODtcclxufVxyXG5cclxubWF0LWxpc3QtaXRlbS5tYXQtbGlzdC1pdGVtLm5nLXN0YXItaW5zZXJ0ZWQge1xyXG4gICAgYm9yZGVyLXRvcDogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbiAgICBoZWlnaHQ6NDRweCFpbXBvcnRhbnQ7XHJcbn1cclxuXHJcbm1hdC1saXN0LWl0ZW0ubWF0LWxpc3QtaXRlbS5uZy1zdGFyLWluc2VydGVkOmxhc3QtY2hpbGR7XHJcblx0Ym9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbn1cclxuXHJcbi5yZWFkX21vcmUge1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBwYWRkaW5nOjEwcHggMCAwcHg7IFxyXG59XHJcblxyXG4ucmVhZF9tb3JlIGEge1xyXG4gICAgZm9udC1zaXplOiAxNXB4O1xyXG4gICAgY29sb3I6ICMxODI0MzM7XHJcbiBcdHRleHQtZGVjb3JhdGlvbjpub25lO1xyXG4gICAgZm9udC13ZWlnaHQ6IDYwMDtcclxufVxyXG5cclxuLnJlYWRfbW9yZSBhOmhvdmVye1xyXG5cdGNvbG9yOiAjMzc4OGU1O1xyXG59XHJcblxyXG5vbCwgdWwsIGRse1xyXG5cdG1hcmdpbjowcHg7XHJcblx0cGFkZGluZzowcHg7XHJcbn1cclxuXHJcbm1hdC1jYXJkLm1hdC1jYXJkIHtcclxuICAgIGJhY2tncm91bmQ6IHRyYW5zcGFyZW50O1xyXG4gICAgYm94LXNoYWRvdzogbm9uZSAhaW1wb3J0YW50O1xyXG4gICAgbWF4LXdpZHRoOiAxMTcwcHg7XHJcbiAgICBtYXJnaW46IDIwcHggYXV0bztcclxufVxyXG5cclxuLm0tYXV0byAubWF0LWNhcmQge1xyXG4gICAgcGFkZGluZzogMjBweCAhaW1wb3J0YW50O1xyXG4gICAgbWFyZ2luOiAxNXB4O1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxuICAgIGJveC1zaGFkb3c6IDBweCAwcHggMTFweCAwcHggIzAwMDIgIWltcG9ydGFudDtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkIHRyYW5zcGFyZW50O1xyXG59XHJcblxyXG4ubS1hdXRvIC5tYXQtY2FyZCAubWF0LWljb24ge1xyXG4gICAgd2lkdGg6IGF1dG87XHJcbiAgICBoZWlnaHQ6IGF1dG87XHJcbiAgICBmb250LXNpemU6IDYwcHg7XHJcbiAgICBjb2xvcjogI2U0ZTRlNDtcclxufVxyXG5cclxuLm1hdC1jYXJkIGg0IHtcclxuICAgIHBhZGRpbmc6IDAgMTVweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBmb250LXNpemU6IDE2cHg7XHJcbiAgICBtYXJnaW46IDAgMCAxMHB4O1xyXG4gICAgZGlzcGxheTogaW5saW5lO1xyXG4gICAgYmFja2dyb3VuZDogdHJhbnNwYXJlbnQ7XHJcbn1cclxuXHJcbi5pbXBvcnQtY2FyZHN7XHJcblxyXG59XHJcbi5tYXQtY2FyZCBwLnAtMiB7XHJcbiAgICBjb2xvcjogIzk4OTg5ODsgICAgXHJcbiAgICBmb250LXNpemU6IDE1cHg7XHJcbn1cclxuXHJcbi5tYXQtY2FyZCBidXR0b24ubWF0LXJhaXNlZC1idXR0b24uZmlsdGVyX0J1dHRvbiB7XHJcbiAgICB3aWR0aDogMTAwJTtcclxuICAgIG1heC13aWR0aDogMTIwcHg7XHJcbiAgICBsaW5lLWhlaWdodDogNDBweDtcclxuICAgIGJveC1zaGFkb3c6IG5vbmU7XHJcbiAgICBib3JkZXI6IDFweCBzb2xpZCAjZTRlNGU0O1xyXG4gICAgY29sb3I6ICNlNGU0ZTQ7XHJcbn1cclxuXHJcbi5tYXQtY2FyZCBidXR0b24ubWF0LXJhaXNlZC1idXR0b24uc2VsZWN0X2NvbF9idG57XHJcbiAgICB3aWR0aDogYXV0bztcclxuICAgIG1heC13aWR0aDogMTAwJTtcclxuICAgIGxpbmUtaGVpZ2h0OiA0MHB4O1xyXG4gICAgYm94LXNoYWRvdzogbm9uZTtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICNlNGU0ZTQ7XHJcbiAgICBjb2xvcjogI2U0ZTRlNDtcclxuICAgIHBhZGRpbmc6IDBweCA4cHg7XHJcbn1cclxuXHJcbi5idG4uY2xlYXJfY29sX2J0bntcclxuICAgIGxpbmUtaGVpZ2h0OiAxO1xyXG4gICAgcGFkZGluZzogNXB4O1xyXG4gICAgfVxyXG4gICAgLnZtYS10ZXh0e1xyXG4gICAgdmVydGljYWwtYWxpZ246IG1pZGRsZTtcclxuICAgIH1cclxuICAgIC5jbGVhcl9oaWRlIGJ1dHRvbi5jbGVhcl9jb2xfYnRuXHJcbiAgICB7XHJcbiAgICBkaXNwbGF5OiBub25lO1xyXG4gICAgfVxyXG4gICAgLmNsZWFyX2NvbF9idG4gLm1hdGVyaWFsLWljb25ze1xyXG4gICAgZm9udC1zaXplOiAxNnB4O1xyXG4gICAgdmVydGljYWwtYWxpZ246IG1pZGRsZTtcclxuICAgIG1hcmdpbi1sZWZ0OiA1cHg7XHJcbiAgICB9XHJcbiAgICBcclxuXHJcbi5tLWF1dG8gLm1hdC1jYXJkOmhvdmVye1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgIzFlODhlNTtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZTpob3ZlciAqLCAubS1hdXRvIC5tYXQtY2FyZDpob3ZlciBidXR0b24ubWF0LXByaW1hcnkgc3BhbiB7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxufVxyXG5cclxuYnV0dG9uLm1hdC1yYWlzZWQtYnV0dG9uLm1hdC1idXR0b24tYmFzZTpob3ZlciBzcGFuIHtcclxuICAgIGNvbG9yOiAjZmZmO1xyXG59XHJcblxyXG5idXR0b24ubWF0LXJhaXNlZC1idXR0b24ubWF0LWJ1dHRvbi1iYXNlOmhvdmVyIHtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZiAhaW1wb3J0YW50O1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tLmNvbC1tZC00Ojotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XHJcbiAgICBiYWNrZ3JvdW5kOiAjYWNhY2FjO1xyXG59XHJcblxyXG4uY29sbXNfY3VzdG9tLmNvbC1tZC00Ojotd2Via2l0LXNjcm9sbGJhci10cmFjayB7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZGRkO1xyXG59XHJcbi5jb2xtc19jdXN0b20uY29sLW1kLTQ6Oi13ZWJraXQtc2Nyb2xsYmFyIHtcclxuICAgIHdpZHRoOiAzcHg7XHJcbn1cclxuXHJcbnNwYW4ubWF0ZXJpYWwtaWNvbnMuaWNvbi5uZy1zdGFyLWluc2VydGVkIHtcclxuICAgIG1hcmdpbjogN3B4IDEwcHg7XHJcbiAgICBwb3NpdGlvbjogYWJzb2x1dGU7XHJcbiAgICByaWdodDogMDtcclxufVxyXG5cclxuLnBhZ2UtY29udGVudCB7XHJcbiAgICBwYWRkaW5nOiAwICFpbXBvcnRhbnQ7XHJcbn1cclxuXHJcbi5mdWxsLXBhZ2Uge1xyXG4gICAgZGlzcGxheTogZmxleDtcclxuICAgIGhlaWdodDogMTAwJTtcclxufVxyXG5cclxuYXNpZGUuc2lkZWJhciB7XHJcbiAgICB3aWR0aDogMzAwcHg7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZmZmO1xyXG4gICAgcGFkZGluZzogMTVweDtcclxuICAgIGJvcmRlci1yaWdodDogMXB4IHNvbGlkICNlMmUyZTI7XHJcbn1cclxuXHJcbi5tYWluLWNvbnRlbnQge1xyXG4gICAgd2lkdGg6IGNhbGMoMTAwJSAtIDMwMHB4KTtcclxuICAgIGJhY2tncm91bmQ6ICNmZmY7XHJcbiAgICBhbGlnbi1pdGVtczogY2VudGVyO1xyXG4gICAgZGlzcGxheTogaW5saW5lLWZsZXg7XHJcbiAgICBmbGV4LXdyYXA6IHdyYXA7XHJcbiAgICBwYWRkaW5nLXRvcDogNjBweDtcclxuICAgIGhlaWdodDogMTAwdmg7XHJcbiAgICBvdmVyZmxvdzogYXV0bztcclxufVxyXG5cclxuLnNpZGViYXJCdG1CdG57XHJcbiAgICBhbGlnbi1zZWxmOiBmbGV4LWVuZDtcclxuICAgIG1hcmdpbjogMCBhdXRvO1xyXG59XHJcblxyXG4uc2lkZWJhciBidXR0b24uYnRuIHtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG4gICAgYmFja2dyb3VuZDogIzFlODhlNTtcclxuICAgIGNvbG9yOiAjZmZmO1xyXG4gICAgYWxpZ24tc2VsZjogZmxleC1lbmQ7XHJcbn1cclxuXHJcbi5jb250ZW50IHtcclxuICAgIGRpc3BsYXk6IGlubGluZS1mbGV4O1xyXG4gICAgaGVpZ2h0OiAxMDAlO1xyXG4gICAgZmxleC13cmFwOiB3cmFwO1xyXG4gICAgYWxpZ24taXRlbXM6IHN0cmV0Y2g7XHJcbn1cclxuXHJcbnVsLmltcG9ydC1zdGVwcyBsaSB7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBtYXJnaW4tbGVmdDogMjBweDtcclxuICAgIHBvc2l0aW9uOiByZWxhdGl2ZTtcclxufVxyXG5cclxuLnRwLWJhciB7XHJcbiAgICBwYWRkaW5nOiAxMHB4IDIwcHg7XHJcbiAgICBiYWNrZ3JvdW5kOiAjZjRmNGY0O1xyXG4gICAgZGlzcGxheTogZmxleDtcclxuICAgIGFsaWduLWl0ZW1zOiBiYXNlbGluZTtcclxuICAgIGp1c3RpZnktY29udGVudDogc3BhY2UtYmV0d2VlbjtcclxuICAgIHdpZHRoOiBjYWxjKDEwMCUgLSAzMDBweCk7XHJcbiAgICBwb3NpdGlvbjogZml4ZWQ7XHJcbiAgICB0b3A6IDA7XHJcbiAgICB6LWluZGV4Ojk7XHJcbn1cclxuXHJcbmJ1dHRvbi5idG4uYmFja2sge1xyXG4gICAgYm9yZGVyOiAxcHggc29saWQgIzIyMjtcclxuICAgIGNvbG9yOiAjMjIyO1xyXG4gICAgYm9yZGVyLXJhZGl1czogM3B4O1xyXG4gICAgbWFyZ2luLXJpZ2h0OiAxNXB4O1xyXG4gICAgbGluZS1oZWlnaHQ6IDFlbTtcclxuICAgIHRleHQtdHJhbnNmb3JtOiBjYXBpdGFsaXplO1xyXG59XHJcblxyXG5idXR0b24uYnRuLmJhY2trOmhvdmVyIHtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICMzNzg4ZTU7XHJcblxyXG4gICAgY29sb3I6ICMzNzg4ZTU7XHJcbn1cclxuXHJcbnVsLmltcG9ydC1zdGVwcyBsaSBhIHtcclxuICAgIGZvbnQtc2l6ZTogMTRweDtcclxuICAgIGZvbnQtd2VpZ2h0OiA1MDA7XHJcbiAgICBwb3NpdGlvbjogcmVsYXRpdmU7XHJcbn1cclxuXHJcbi50cC1iYXIgc3BhbiB7XHJcbiAgICBmb250LXdlaWdodDogNjAwO1xyXG59XHJcblxyXG5cclxuLnNpZGV0ZXh0IGg1IHtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxuICAgIGNvbG9yOiAjMWU4OGU1O1xyXG4gICAgcG9zaXRpb246IHJlbGF0aXZlO1xyXG4gICAgd2lkdGg6IGF1dG87XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICBtYXJnaW4tYm90dG9tOiAyMHB4O1xyXG4gICAgZm9udC1zaXplOjIwcHg7XHJcbn1cclxuXHJcbi5zaWRldGV4dCBwIHtcclxuICAgIGZvbnQtc2l6ZToxOHB4O1xyXG59XHJcblxyXG4ubnVtYmVySWNvbiB7XHJcbiAgICBkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XHJcbiAgICB3aWR0aDogMjJweDtcclxuICAgIGhlaWdodDogMjJweDtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxuICAgIGJvcmRlci1yYWRpdXM6IDUwJTtcclxuICAgIG1hcmdpbi1yaWdodDogMTBweDtcclxuICAgIHBhZGRpbmc6IDNweDtcclxuICAgIGZvbnQtc2l6ZTogMTZweDtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxufVxyXG5cclxuLnRwLWJhciAubnVtYmVySWNvblRiIHtcclxuICAgIGRpc3BsYXk6IGlubGluZS1ibG9jaztcclxuICAgIHdpZHRoOiAyMnB4O1xyXG4gICAgaGVpZ2h0OiAyMnB4O1xyXG4gICAgYmFja2dyb3VuZDogI2ZmZjtcclxuICAgIGNvbG9yOiAjMDAwO1xyXG4gICAgbWFyZ2luLXJpZ2h0OiA1cHg7XHJcbiAgICBwYWRkaW5nOiAxcHg7XHJcbiAgICBib3JkZXItcmFkaXVzOiA1MCU7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBib3JkZXI6IDFweCBzb2xpZCAjMmMyYzJjO1xyXG4gICAgbGluZS1oZWlnaHQ6IDEuNGVtO1xyXG59XHJcbi50cC1iYXIgLmFjdGl2ZVN0ZXAgLm51bWJlckljb25UYiB7XHJcbiAgICBtYXJnaW4tcmlnaHQ6IDVweDtcclxuICAgIHBhZGRpbmc6IDFweDtcclxuICAgIGJhY2tncm91bmQ6ICMxZTg4ZTU7XHJcbiAgICBjb2xvcjogI2ZmZjtcclxuICAgIGxpbmUtaGVpZ2h0OiAxLjRlbTtcclxuICAgIGJvcmRlcjogMXB4IHNvbGlkICMzNzg4ZTU7XHJcbn1cclxuXHJcbi5hY3RpdmVTdGVwIGF7XHJcbiAgICBjb2xvcjogIzFlODhlNTtcclxufVxyXG5cclxuLmltcG9ydC1zdGVwcyAuZmEtY2hlY2stY2lyY2xle1xyXG4gICAgY29sb3I6ICMwMGIzM2M7XHJcbiAgICBmb250LXNpemU6IDE4cHg7XHJcbiAgICBtYXJnaW4tcmlnaHQ6IDVweDtcclxufVxyXG5cclxuLnNpZGV0ZXh0IHtcclxuICAgIHRleHQtYWxpZ246IGNlbnRlcjtcclxuICAgIG1hcmdpbi10b3A6IDYwcHg7XHJcbn1cclxuXHJcbi5zaWRldGV4dCBwIHtcclxuICAgIHRleHQtYWxpZ246IGxlZnQ7XHJcbn1cclxuXHJcbi5oZWFkZXItc2V0IGJ1dHRvbi5zZWxlY3RfY29sX2J0bi5tYXQtcmFpc2VkLWJ1dHRvbi5tYXQtYnV0dG9uLWJhc2UubWF0LXByaW1hcnkge1xyXG4gICAgYmFja2dyb3VuZDogIzAwYjMzYztcclxufVxyXG5cclxuXHJcbi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqTWVkaWEgUXVlcnkqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqL1xyXG5cclxuQG1lZGlhKG1heC13aWR0aDogNzY3cHgpe1xyXG4ub3V0ZXJfZmx4e21heC13aWR0aDoxMDAlIWltcG9ydGFudDsgd2lkdGg6IDEwMCU7fVxyXG4uYWRkX2JhY2tncm91bmR7cGFkZGluZzoxMHB4IDAgMHB4IWltcG9ydGFudDt9XHJcbmJ1dHRvbi5tYXQtYnV0dG9uLm1hdC1idXR0b24tYmFzZXtmb250LXNpemU6IDEycHg7cGFkZGluZzogM3B4IDExcHg7fVxyXG59XHJcblxyXG5AbWVkaWEobWluLXdpZHRoOjc2OHB4KSBhbmQgKG1heC13aWR0aDogMTAyNHB4KXtcclxuLm1hdC1jYXJkIC5tYXQtY2FyZC10aXRsZXtmb250LXNpemU6MTVweDt9XHJcbi5vdXRlcl9mbHh7bWF4LXdpZHRoOjUwJSFpbXBvcnRhbnQ7IHdpZHRoOiAxMDAlO31cclxuLmNvbG1zX2N1c3RvbSBoM3tmb250LXNpemU6MTZweDt9XHJcbmJ1dHRvbi5tYXQtYnV0dG9uLm1hdC1idXR0b24tYmFzZXtmb250LXNpemU6IDEycHg7cGFkZGluZzogM3B4IDE0cHg7fVxyXG59XHJcblxyXG4uaWNvbntcclxuICAgIGJhY2tncm91bmQtY29sb3I6ICMwMGIzM2M7XHJcbiAgICBib3JkZXItcmFkaXVzOiAxNXB4O1xyXG4gICAgY29sb3I6IHdoaXRlc21va2U7XHJcbiAgICBmbG9hdDpyaWdodDtcclxuICAgIG1hcmdpbiA6IDEwcHggMTBweDtcclxuICAgIHBhZGRpbmc6IDNweDtcclxuICAgIGZvbnQtc2l6ZTogMThweDtcclxufVxyXG5cclxuLmV4YW1wbGUtZm9ybSB7XHJcbiAgICBtaW4td2lkdGg6IDE1MHB4O1xyXG4gICAgbWF4LXdpZHRoOiA1MDBweDtcclxuICAgIHdpZHRoOiAxMDAlO1xyXG59XHJcbiAgXHJcbi5leGFtcGxlLWZ1bGwtd2lkdGgge1xyXG4gICAgd2lkdGg6IDEwMCU7XHJcbn1cclxuXHJcbi5leGFtcGxlLXJhZGlvLWdyb3VwIHtcclxuICAgIGRpc3BsYXk6IGZsZXg7XHJcbiAgICBmbGV4LWRpcmVjdGlvbjogY29sdW1uO1xyXG4gICAgbWFyZ2luOiAxNXB4IDA7XHJcbn1cclxuXHJcbi5leGFtcGxlLXJhZGlvLWJ1dHRvbiB7XHJcbiAgICBtYXJnaW46IDVweDtcclxufVxyXG5cclxuLm1hdC1yYWlzZWQtYnV0dG8uaW1wb3J0QnRuOmFjdGl2ZSwgLm1hdC1yYWlzZWQtYnV0dG8uaW1wb3J0QnRuOmhvdmVye1xyXG4gICAgY29sb3I6ICNmZmY7XHJcbn1cclxuLm1hdC1yYWlzZWQtYnV0dG8uaW5zZXJ0QnRuOmFjdGl2ZSwgLmluc2VydEJ0bjpob3ZlcntcclxuICAgIGNvbG9yOiAjZmZmO1xyXG59XHJcblxyXG5cclxuLnNtYWxsbG9hZGVyIHtcclxuICAgIGJvcmRlcjogNHB4IHNvbGlkICNmM2YzZjM7IC8qIExpZ2h0IGdyZXkgKi9cclxuICAgIGJvcmRlci10b3A6IDRweCBzb2xpZCAjMzQ5OGRiOyAvKiBCbHVlICovXHJcbiAgICBib3JkZXItcmFkaXVzOiA1MCU7XHJcbiAgICB3aWR0aDogMjRweDtcclxuICAgIGhlaWdodDogMjRweDtcclxuICAgIGRpc3BsYXk6IGlubGluZS1ibG9jaztcclxuICAgIGFuaW1hdGlvbjogbG9hZGVyc3BpbiAycyBsaW5lYXIgaW5maW5pdGU7XHJcbiAgfVxyXG5cclxuICBALXdlYmtpdC1rZXlmcmFtZXMgbG9hZGVyc3BpbiB7XHJcbiAgICAwJSB7IC13ZWJraXQtdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7IH1cclxuICAgIDEwMCUgeyAtd2Via2l0LXRyYW5zZm9ybTogcm90YXRlKDM2MGRlZyk7IH1cclxuICB9XHJcbiAgXHJcbiAgQGtleWZyYW1lcyBsb2FkZXJzcGluIHtcclxuICAgIDAlIHsgdHJhbnNmb3JtOiByb3RhdGUoMGRlZyk7IH1cclxuICAgIDEwMCUgeyB0cmFuc2Zvcm06IHJvdGF0ZSgzNjBkZWcpOyB9XHJcbiAgfVxyXG5cclxuICAuaW5zZXJ0QnRuQnRte1xyXG4gICAgZGlzcGxheTogYmxvY2s7XHJcbiAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XHJcbiAgICBtYXJnaW46IDMwcHggYXV0byFpbXBvcnRhbnQ7XHJcbiAgICB3aWR0aDogNTAlO1xyXG4gIH1cclxuXHJcbiAgXHJcbi5Eb3dubG9hZFJlamVjdGVke1xyXG4gICAgbWFyZ2luOiAwIGF1dG8haW1wb3J0YW50O1xyXG4gICAgZGlzcGxheTogYmxvY2s7XHJcbn1cclxuXHJcbiJdfQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](DialogOverviewExampleDialog, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'dialog-overview-example-dialog',
          templateUrl: './dialog-overview-example-dialog.html',
          styleUrls: ['./fileupload.component.css']
        }]
      }], function () {
        return [{
          type: _angular_router__WEBPACK_IMPORTED_MODULE_2__["ActivatedRoute"]
        }, {
          type: _angular_forms__WEBPACK_IMPORTED_MODULE_5__["FormBuilder"]
        }, {
          type: _Service_api_service_service__WEBPACK_IMPORTED_MODULE_6__["ApiServiceService"]
        }, {
          type: _Service_updatedata_service__WEBPACK_IMPORTED_MODULE_7__["UpdatedataService"]
        }, {
          type: _angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MatDialogRef"]
        }, {
          type: undefined,
          decorators: [{
            type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Inject"],
            args: [_angular_material_dialog__WEBPACK_IMPORTED_MODULE_3__["MAT_DIALOG_DATA"]]
          }]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/layouts/full/full.component.ts":
  /*!************************************************!*\
    !*** ./src/app/layouts/full/full.component.ts ***!
    \************************************************/

  /*! exports provided: FullComponent */

  /***/
  function srcAppLayoutsFullFullComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "FullComponent", function () {
      return FullComponent;
    });
    /* harmony import */


    var _angular_cdk_layout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/cdk/layout */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/layout.js");
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ../../shared/menu-items/menu-items */
    "./src/app/shared/menu-items/menu-items.ts");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _shared_spinner_component__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! ../../shared/spinner.component */
    "./src/app/shared/spinner.component.ts");
    /** @title Responsive sidenav */


    var FullComponent =
    /*#__PURE__*/
    function () {
      function FullComponent(changeDetectorRef, media, menuItems) {
        _classCallCheck(this, FullComponent);

        this.menuItems = menuItems;
        this.mobileQuery = media.matchMedia('(min-width: 768px)');

        this._mobileQueryListener = function () {
          return changeDetectorRef.detectChanges();
        };

        this.mobileQuery.addListener(this._mobileQueryListener);
      }

      _createClass(FullComponent, [{
        key: "ngOnDestroy",
        value: function ngOnDestroy() {
          this.mobileQuery.removeListener(this._mobileQueryListener);
        }
      }, {
        key: "ngAfterViewInit",
        value: function ngAfterViewInit() {}
      }]);

      return FullComponent;
    }();

    FullComponent.ɵfac = function FullComponent_Factory(t) {
      return new (t || FullComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵdirectiveInject"](_angular_core__WEBPACK_IMPORTED_MODULE_1__["ChangeDetectorRef"]), _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵdirectiveInject"](_angular_cdk_layout__WEBPACK_IMPORTED_MODULE_0__["MediaMatcher"]), _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵdirectiveInject"](_shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__["MenuItems"]));
    };

    FullComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵdefineComponent"]({
      type: FullComponent,
      selectors: [["app-full-layout"]],
      decls: 6,
      vars: 2,
      consts: [[1, "main-container"], [1, "example-sidenav-container"], [1, "page-wrapper"], [1, "page-content"]],
      template: function FullComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementStart"](1, "mat-sidenav-container", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementStart"](2, "mat-sidenav-content", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementStart"](3, "div", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementStart"](4, "router-outlet");

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelement"](5, "app-spinner");

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵelementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵadvance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵɵstyleProp"]("margin-top", ctx.mobileQuery.matches ? 0 : 0, "px");
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_3__["MatSidenavContainer"], _angular_material__WEBPACK_IMPORTED_MODULE_3__["MatSidenavContent"], _angular_router__WEBPACK_IMPORTED_MODULE_4__["RouterOutlet"], _shared_spinner_component__WEBPACK_IMPORTED_MODULE_5__["SpinnerComponent"]],
      styles: ["mat-sidenav#snav[_ngcontent-%COMP%] {\r\n    background: #182434;\r\n    transform: none;\r\n    visibility: visible !important;\r\n    width: 52px;\r\n}\r\n\r\nmat-sidenav#snav.mat-drawer-opened[_ngcontent-%COMP%] {\r\n    width: 240px;\r\n}\r\n\r\n#snav[_ngcontent-%COMP%]   app-sidebar[_ngcontent-%COMP%] {\r\n    height: 100%;\r\n    display: flex;\r\n    flex-direction: column;\r\n    justify-content: space-between;\r\n    position: relative;\r\n}\r\n\r\n#snav[_ngcontent-%COMP%]   app-sidebar[_ngcontent-%COMP%]:before {\r\n    content: \"\";\r\n    width: 50px;\r\n    position: absolute;\r\n    height: 100%;\r\n    background: #15202e;\r\n}\r\n\r\nmat-sidenav#snav[_ngcontent-%COMP%]:hover {\r\n    width: 240px;\r\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbGF5b3V0cy9mdWxsL2Z1bGwuY29tcG9uZW50LmNzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUNJLG1CQUFtQjtJQUNuQixlQUFlO0lBQ2YsOEJBQThCO0lBQzlCLFdBQVc7QUFDZjs7QUFFQTtJQUNJLFlBQVk7QUFDaEI7O0FBRUE7SUFDSSxZQUFZO0lBQ1osYUFBYTtJQUNiLHNCQUFzQjtJQUN0Qiw4QkFBOEI7SUFDOUIsa0JBQWtCO0FBQ3RCOztBQUVBO0lBQ0ksV0FBVztJQUNYLFdBQVc7SUFDWCxrQkFBa0I7SUFDbEIsWUFBWTtJQUNaLG1CQUFtQjtBQUN2Qjs7QUFFQTtJQUNJLFlBQVk7QUFDaEIiLCJmaWxlIjoic3JjL2FwcC9sYXlvdXRzL2Z1bGwvZnVsbC5jb21wb25lbnQuY3NzIiwic291cmNlc0NvbnRlbnQiOlsibWF0LXNpZGVuYXYjc25hdiB7XHJcbiAgICBiYWNrZ3JvdW5kOiAjMTgyNDM0O1xyXG4gICAgdHJhbnNmb3JtOiBub25lO1xyXG4gICAgdmlzaWJpbGl0eTogdmlzaWJsZSAhaW1wb3J0YW50O1xyXG4gICAgd2lkdGg6IDUycHg7XHJcbn1cclxuXHJcbm1hdC1zaWRlbmF2I3NuYXYubWF0LWRyYXdlci1vcGVuZWQge1xyXG4gICAgd2lkdGg6IDI0MHB4O1xyXG59XHJcblxyXG4jc25hdiBhcHAtc2lkZWJhciB7XHJcbiAgICBoZWlnaHQ6IDEwMCU7XHJcbiAgICBkaXNwbGF5OiBmbGV4O1xyXG4gICAgZmxleC1kaXJlY3Rpb246IGNvbHVtbjtcclxuICAgIGp1c3RpZnktY29udGVudDogc3BhY2UtYmV0d2VlbjtcclxuICAgIHBvc2l0aW9uOiByZWxhdGl2ZTtcclxufVxyXG5cclxuI3NuYXYgYXBwLXNpZGViYXI6YmVmb3JlIHtcclxuICAgIGNvbnRlbnQ6IFwiXCI7XHJcbiAgICB3aWR0aDogNTBweDtcclxuICAgIHBvc2l0aW9uOiBhYnNvbHV0ZTtcclxuICAgIGhlaWdodDogMTAwJTtcclxuICAgIGJhY2tncm91bmQ6ICMxNTIwMmU7XHJcbn1cclxuXHJcbm1hdC1zaWRlbmF2I3NuYXY6aG92ZXIge1xyXG4gICAgd2lkdGg6IDI0MHB4O1xyXG59Il19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_1__["ɵsetClassMetadata"](FullComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_1__["Component"],
        args: [{
          selector: 'app-full-layout',
          templateUrl: 'full.component.html',
          styleUrls: ['./full.component.css']
        }]
      }], function () {
        return [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_1__["ChangeDetectorRef"]
        }, {
          type: _angular_cdk_layout__WEBPACK_IMPORTED_MODULE_0__["MediaMatcher"]
        }, {
          type: _shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__["MenuItems"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/layouts/full/header/header.component.ts":
  /*!*********************************************************!*\
    !*** ./src/app/layouts/full/header/header.component.ts ***!
    \*********************************************************/

  /*! exports provided: AppHeaderComponent */

  /***/
  function srcAppLayoutsFullHeaderHeaderComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AppHeaderComponent", function () {
      return AppHeaderComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");

    var AppHeaderComponent = function AppHeaderComponent() {
      _classCallCheck(this, AppHeaderComponent);
    };

    AppHeaderComponent.ɵfac = function AppHeaderComponent_Factory(t) {
      return new (t || AppHeaderComponent)();
    };

    AppHeaderComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: AppHeaderComponent,
      selectors: [["app-header"]],
      decls: 20,
      vars: 1,
      consts: [["mat-icon-button", "", 1, "m-r-5", 3, "matMenuTriggerFor"], ["src", "assets/images/users/1.jpg", "alt", "user", 1, "profile-pic"], [1, "mymegamenu"], ["profile", "matMenu"], ["mat-menu-item", ""]],
      template: function AppHeaderComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "button", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](1, "img", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "mat-menu", 2, 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](5, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](6, "settings");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](7, " Settings ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](8, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](9, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](10, "account_box");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](11, " Profile ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](12, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](13, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](14, "notifications_off");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](15, " Disable notifications ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](16, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](17, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](18, "exit_to_app");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](19, " Sign Out ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
        }

        if (rf & 2) {
          var _r0 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵreference"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("matMenuTriggerFor", _r0);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatMenuTrigger"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["_MatMenu"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatMenuItem"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIcon"]],
      encapsulation: 2
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AppHeaderComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-header',
          templateUrl: './header.component.html',
          styleUrls: []
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/layouts/full/sidebar/sidebar.component.ts":
  /*!***********************************************************!*\
    !*** ./src/app/layouts/full/sidebar/sidebar.component.ts ***!
    \***********************************************************/

  /*! exports provided: AppSidebarComponent */

  /***/
  function srcAppLayoutsFullSidebarSidebarComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AppSidebarComponent", function () {
      return AppSidebarComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_cdk_layout__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/cdk/layout */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/layout.js");
    /* harmony import */


    var _shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ../../../shared/menu-items/menu-items */
    "./src/app/shared/menu-items/menu-items.ts");

    var AppSidebarComponent =
    /*#__PURE__*/
    function () {
      function AppSidebarComponent(changeDetectorRef, media, menuItems) {
        _classCallCheck(this, AppSidebarComponent);

        this.menuItems = menuItems;
        this.mobileQuery = media.matchMedia('(min-width: 768px)');

        this._mobileQueryListener = function () {
          return changeDetectorRef.detectChanges();
        };

        this.mobileQuery.addListener(this._mobileQueryListener);
      }

      _createClass(AppSidebarComponent, [{
        key: "ngOnDestroy",
        value: function ngOnDestroy() {
          this.mobileQuery.removeListener(this._mobileQueryListener);
        }
      }]);

      return AppSidebarComponent;
    }();

    AppSidebarComponent.ɵfac = function AppSidebarComponent_Factory(t) {
      return new (t || AppSidebarComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_core__WEBPACK_IMPORTED_MODULE_0__["ChangeDetectorRef"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_cdk_layout__WEBPACK_IMPORTED_MODULE_1__["MediaMatcher"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__["MenuItems"]));
    };

    AppSidebarComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: AppSidebarComponent,
      selectors: [["app-sidebar"]],
      decls: 62,
      vars: 0,
      consts: [[1, "side-nav-menu"], ["href", ""], [1, "fa", "fa-bold", "mat-icon"], ["href", "/office/"], [1, "fa", "fa-life-ring", "mat-icon"], ["href", "/office/DeskPlan.php"], [1, "fa", "fa-calendar-day", "mat-icon"], ["href", "/office/ManageLeads.php?u=1"], [1, "fa", "fa-users", "mat-icon"], ["href", "/office/Client.php?Act=0"], [1, "fa", "fa-user", "mat-icon"], ["href", "/office/Cal.php"], [1, "fa", "fa-tasks", "mat-icon"], [1, "side-nav-menu", "snm2"], ["href", "/office/Items.php"], [1, "fa", "fa-store", "mat-icon"], ["href", "/office/ReportsDash.php"], [1, "fa", "fa-chart-line", "mat-icon"], ["href", "/office/landings/"], ["href", "/office/CartesetAll.php"], [1, "fa", "fa-list-alt", "mat-icon"], ["href", "/office/SettingsDashboard.php"], [1, "fa", "fa-cog", "mat-icon"], ["href", "/office/support/FAQ.php"]],
      template: function AppSidebarComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "ul", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](2, "a", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](3, "i", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](4, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](5, "\u05D7\u05D1\u05E8\u05D4 \u05DC\u05D3\u05D5\u05D2\u05DE\u05D0");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](6, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](7, "a", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](8, "i", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](9, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](10, "\u05E8\u05D0\u05E9\u05D9");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](11, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](12, "a", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](13, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](14, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](15, "\u05E9\u05D9\u05E2\u05D5\u05E8\u05D9\u05DD ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](16, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](17, "a", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](18, "i", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](19, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](20, "\u05DE\u05EA\u05E2\u05E0\u05D9\u05D9\u05E0\u05D9\u05DD ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](21, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](22, "a", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](23, "i", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](24, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](25, "\u05DC\u05E7\u05D5\u05D7\u05D5\u05EA ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](26, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](27, "a", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](28, "i", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](29, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](30, "\u05DE\u05E9\u05D9\u05DE\u05D5\u05EA");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](31, "ul", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](32, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](33, "a", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](34, "i", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](35, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](36, "\u05D7\u05E0\u05D5\u05EA");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](37, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](38, "a", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](39, "i", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](40, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](41, "\u05D3\u05D5\u05D7\u05D5\u05EA");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](42, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](43, "a", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](44, "i", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](45, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](46, "\u05D3\u05E4\u05D9 \u05E0\u05D7\u05D9\u05EA\u05D4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](47, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](48, "a", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](49, "i", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](50, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](51, "\u05D4\u05E0\u05D4\"\u05D7");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](52, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](53, "a", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](54, "i", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](55, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](56, "\u05D4\u05D2\u05D3\u05E8\u05D5\u05EA");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](57, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](58, "a", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](59, "i", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](60, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtext"](61, "\u05EA\u05DE\u05D9\u05DB\u05D4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
        }
      },
      styles: ["ul.side-nav-menu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%] {\r\n    list-style: none;\r\n    position: relative;\r\n}\r\n\r\nul.side-nav-menu[_ngcontent-%COMP%] {\r\n    padding: 0;\r\n    margin-top: 10px;\r\n}\r\n\r\n.side-nav-menu[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    display: flex;\r\n    align-items: center;\r\n    color: #fff;\r\n    font-size: 24px;\r\n}\r\n\r\n.side-nav-menu[_ngcontent-%COMP%]   .mat-icon[_ngcontent-%COMP%] {\r\n    width: 48px;\r\n    height: 38px;\r\n    line-height: 38px;\r\n    font-size: 24px;\r\n    text-align: center;\r\n    color: #fff;\r\n    margin-right: 12px;\r\n}\r\n\r\n.submenu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%] {\r\n    padding: 5px 10px;\r\n}\r\n\r\nul.side-nav-menu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]:hover   .submenu[_ngcontent-%COMP%] {\r\n    height: auto;\r\n    transition: all ease 0.5s;\r\n    min-height: auto;\r\n}\r\n\r\nul.submenu[_ngcontent-%COMP%] {\r\n    margin-left: 20px;\r\n    height: 0px;\r\n    overflow: hidden;\r\n    transition: all ease 0.5s;\r\n    min-height: 1px;\r\n    margin-right: 10px;\r\n}\r\n\r\nul.side-nav-menu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover   mat-icon[_ngcontent-%COMP%] {\r\n    color: #15202e !important;\r\n}\r\n\r\nul.side-nav-menu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover {\r\n    background: #fff;\r\n    border-radius: 5px;\r\n    color: #15202e !important;\r\n    font-weight: 500;\r\n}\r\n\r\n\r\n\r\n.mat-spinner-loader[_ngcontent-%COMP%]   svg[_ngcontent-%COMP%]{\r\n    width: 24px!important; \r\n    height: 24px!important; \r\n    margin: 5px 10px 0px 0px;    \r\n}\r\n\r\n.mat-progress-spinner[_ngcontent-%COMP%]   svg[_ngcontent-%COMP%]{\r\n    width: 24px!important; \r\n    height: 24px!important; \r\n    margin: 5px 10px 0px 0px;    \r\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbGF5b3V0cy9mdWxsL3NpZGViYXIvc2lkZWJhci5jb21wb25lbnQuY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQ0ksZ0JBQWdCO0lBQ2hCLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLFVBQVU7SUFDVixnQkFBZ0I7QUFDcEI7O0FBRUE7SUFDSSxhQUFhO0lBQ2IsbUJBQW1CO0lBQ25CLFdBQVc7SUFDWCxlQUFlO0FBQ25COztBQUVBO0lBQ0ksV0FBVztJQUNYLFlBQVk7SUFDWixpQkFBaUI7SUFDakIsZUFBZTtJQUNmLGtCQUFrQjtJQUNsQixXQUFXO0lBQ1gsa0JBQWtCO0FBQ3RCOztBQUVBO0lBQ0ksaUJBQWlCO0FBQ3JCOztBQUVBO0lBQ0ksWUFBWTtJQUNaLHlCQUF5QjtJQUN6QixnQkFBZ0I7QUFDcEI7O0FBRUE7SUFDSSxpQkFBaUI7SUFDakIsV0FBVztJQUNYLGdCQUFnQjtJQUNoQix5QkFBeUI7SUFDekIsZUFBZTtJQUNmLGtCQUFrQjtBQUN0Qjs7QUFFQTtJQUNJLHlCQUF5QjtBQUM3Qjs7QUFFQTtJQUNJLGdCQUFnQjtJQUNoQixrQkFBa0I7SUFDbEIseUJBQXlCO0lBQ3pCLGdCQUFnQjtBQUNwQjs7QUFDQTs7Ozs7O0NBTUM7O0FBQ0Q7SUFDSSxxQkFBcUI7SUFDckIsc0JBQXNCO0lBQ3RCLHdCQUF3QjtBQUM1Qjs7QUFDQTtJQUNJLHFCQUFxQjtJQUNyQixzQkFBc0I7SUFDdEIsd0JBQXdCO0FBQzVCIiwiZmlsZSI6InNyYy9hcHAvbGF5b3V0cy9mdWxsL3NpZGViYXIvc2lkZWJhci5jb21wb25lbnQuY3NzIiwic291cmNlc0NvbnRlbnQiOlsidWwuc2lkZS1uYXYtbWVudSBsaSB7XHJcbiAgICBsaXN0LXN0eWxlOiBub25lO1xyXG4gICAgcG9zaXRpb246IHJlbGF0aXZlO1xyXG59XHJcblxyXG51bC5zaWRlLW5hdi1tZW51IHtcclxuICAgIHBhZGRpbmc6IDA7XHJcbiAgICBtYXJnaW4tdG9wOiAxMHB4O1xyXG59XHJcblxyXG4uc2lkZS1uYXYtbWVudSBhIHtcclxuICAgIGRpc3BsYXk6IGZsZXg7XHJcbiAgICBhbGlnbi1pdGVtczogY2VudGVyO1xyXG4gICAgY29sb3I6ICNmZmY7XHJcbiAgICBmb250LXNpemU6IDI0cHg7XHJcbn1cclxuXHJcbi5zaWRlLW5hdi1tZW51IC5tYXQtaWNvbiB7XHJcbiAgICB3aWR0aDogNDhweDtcclxuICAgIGhlaWdodDogMzhweDtcclxuICAgIGxpbmUtaGVpZ2h0OiAzOHB4O1xyXG4gICAgZm9udC1zaXplOiAyNHB4O1xyXG4gICAgdGV4dC1hbGlnbjogY2VudGVyO1xyXG4gICAgY29sb3I6ICNmZmY7XHJcbiAgICBtYXJnaW4tcmlnaHQ6IDEycHg7XHJcbn1cclxuXHJcbi5zdWJtZW51IGxpIGEge1xyXG4gICAgcGFkZGluZzogNXB4IDEwcHg7XHJcbn1cclxuXHJcbnVsLnNpZGUtbmF2LW1lbnUgbGk6aG92ZXIgLnN1Ym1lbnUge1xyXG4gICAgaGVpZ2h0OiBhdXRvO1xyXG4gICAgdHJhbnNpdGlvbjogYWxsIGVhc2UgMC41cztcclxuICAgIG1pbi1oZWlnaHQ6IGF1dG87XHJcbn1cclxuXHJcbnVsLnN1Ym1lbnUge1xyXG4gICAgbWFyZ2luLWxlZnQ6IDIwcHg7XHJcbiAgICBoZWlnaHQ6IDBweDtcclxuICAgIG92ZXJmbG93OiBoaWRkZW47XHJcbiAgICB0cmFuc2l0aW9uOiBhbGwgZWFzZSAwLjVzO1xyXG4gICAgbWluLWhlaWdodDogMXB4O1xyXG4gICAgbWFyZ2luLXJpZ2h0OiAxMHB4O1xyXG59XHJcblxyXG51bC5zaWRlLW5hdi1tZW51IGxpIGE6aG92ZXIgbWF0LWljb24ge1xyXG4gICAgY29sb3I6ICMxNTIwMmUgIWltcG9ydGFudDtcclxufVxyXG5cclxudWwuc2lkZS1uYXYtbWVudSBsaSBhOmhvdmVyIHtcclxuICAgIGJhY2tncm91bmQ6ICNmZmY7XHJcbiAgICBib3JkZXItcmFkaXVzOiA1cHg7XHJcbiAgICBjb2xvcjogIzE1MjAyZSAhaW1wb3J0YW50O1xyXG4gICAgZm9udC13ZWlnaHQ6IDUwMDtcclxufVxyXG4vKiBtZC1zcGlubmVyIHtcclxuICAgIGZsb2F0OmxlZnQ7IFxyXG4gICAgd2lkdGg6IDI0cHg7IFxyXG4gICAgaGVpZ2h0OiAyNHB4OyBcclxuICAgIG1hcmdpbjogNXB4IDEwcHggMHB4IDBweDtcclxuICB9XHJcbiovXHJcbi5tYXQtc3Bpbm5lci1sb2FkZXIgc3Zne1xyXG4gICAgd2lkdGg6IDI0cHghaW1wb3J0YW50OyBcclxuICAgIGhlaWdodDogMjRweCFpbXBvcnRhbnQ7IFxyXG4gICAgbWFyZ2luOiA1cHggMTBweCAwcHggMHB4OyAgICBcclxufSBcclxuLm1hdC1wcm9ncmVzcy1zcGlubmVyIHN2Z3tcclxuICAgIHdpZHRoOiAyNHB4IWltcG9ydGFudDsgXHJcbiAgICBoZWlnaHQ6IDI0cHghaW1wb3J0YW50OyBcclxuICAgIG1hcmdpbjogNXB4IDEwcHggMHB4IDBweDsgICAgXHJcbn0gIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AppSidebarComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-sidebar',
          templateUrl: './sidebar.component.html',
          styleUrls: ['./sidebar.component.css']
        }]
      }], function () {
        return [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["ChangeDetectorRef"]
        }, {
          type: _angular_cdk_layout__WEBPACK_IMPORTED_MODULE_1__["MediaMatcher"]
        }, {
          type: _shared_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_2__["MenuItems"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/shared/accordion/accordion.directive.ts":
  /*!*********************************************************!*\
    !*** ./src/app/shared/accordion/accordion.directive.ts ***!
    \*********************************************************/

  /*! exports provided: AccordionDirective */

  /***/
  function srcAppSharedAccordionAccordionDirectiveTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AccordionDirective", function () {
      return AccordionDirective;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var rxjs_operators__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! rxjs/operators */
    "./node_modules/rxjs/_esm2015/operators/index.js");

    var AccordionDirective =
    /*#__PURE__*/
    function () {
      function AccordionDirective(router) {
        var _this14 = this;

        _classCallCheck(this, AccordionDirective);

        this.router = router;
        this.navlinks = [];
        setTimeout(function () {
          return _this14.checkOpenLinks();
        });
      }

      _createClass(AccordionDirective, [{
        key: "closeOtherLinks",
        value: function closeOtherLinks(selectedLink) {
          this.navlinks.forEach(function (link) {
            if (link !== selectedLink) {
              link.selected = false;
            }
          });
        }
      }, {
        key: "addLink",
        value: function addLink(link) {
          this.navlinks.push(link);
        }
      }, {
        key: "removeGroup",
        value: function removeGroup(link) {
          var index = this.navlinks.indexOf(link);

          if (index !== -1) {
            this.navlinks.splice(index, 1);
          }
        }
      }, {
        key: "checkOpenLinks",
        value: function checkOpenLinks() {
          var _this15 = this;

          this.navlinks.forEach(function (link) {
            if (link.group) {
              var routeUrl = _this15.router.url;
              var currentUrl = routeUrl.split('/');

              if (currentUrl.indexOf(link.group) > 0) {
                link.selected = true;

                _this15.closeOtherLinks(link);
              }
            }
          });
        }
      }, {
        key: "ngAfterContentChecked",
        value: function ngAfterContentChecked() {
          var _this16 = this;

          this.router.events.pipe(Object(rxjs_operators__WEBPACK_IMPORTED_MODULE_2__["filter"])(function (event) {
            return event instanceof _angular_router__WEBPACK_IMPORTED_MODULE_1__["NavigationEnd"];
          })).subscribe(function (e) {
            return _this16.checkOpenLinks();
          });
        }
      }]);

      return AccordionDirective;
    }();

    AccordionDirective.ɵfac = function AccordionDirective_Factory(t) {
      return new (t || AccordionDirective)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_router__WEBPACK_IMPORTED_MODULE_1__["Router"]));
    };

    AccordionDirective.ɵdir = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineDirective"]({
      type: AccordionDirective,
      selectors: [["", "appAccordion", ""]]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AccordionDirective, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Directive"],
        args: [{
          selector: '[appAccordion]'
        }]
      }], function () {
        return [{
          type: _angular_router__WEBPACK_IMPORTED_MODULE_1__["Router"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/shared/accordion/accordionanchor.directive.ts":
  /*!***************************************************************!*\
    !*** ./src/app/shared/accordion/accordionanchor.directive.ts ***!
    \***************************************************************/

  /*! exports provided: AccordionAnchorDirective */

  /***/
  function srcAppSharedAccordionAccordionanchorDirectiveTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AccordionAnchorDirective", function () {
      return AccordionAnchorDirective;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ./accordionlink.directive */
    "./src/app/shared/accordion/accordionlink.directive.ts");

    var AccordionAnchorDirective =
    /*#__PURE__*/
    function () {
      function AccordionAnchorDirective(navlink) {
        _classCallCheck(this, AccordionAnchorDirective);

        this.navlink = navlink;
      }

      _createClass(AccordionAnchorDirective, [{
        key: "onClick",
        value: function onClick(e) {
          this.navlink.toggle();
        }
      }]);

      return AccordionAnchorDirective;
    }();

    AccordionAnchorDirective.ɵfac = function AccordionAnchorDirective_Factory(t) {
      return new (t || AccordionAnchorDirective)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionLinkDirective"]));
    };

    AccordionAnchorDirective.ɵdir = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineDirective"]({
      type: AccordionAnchorDirective,
      selectors: [["", "appAccordionToggle", ""]],
      hostBindings: function AccordionAnchorDirective_HostBindings(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵlistener"]("click", function AccordionAnchorDirective_click_HostBindingHandler($event) {
            return ctx.onClick($event);
          });
        }
      }
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AccordionAnchorDirective, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Directive"],
        args: [{
          selector: '[appAccordionToggle]'
        }]
      }], function () {
        return [{
          type: _accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionLinkDirective"],
          decorators: [{
            type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Inject"],
            args: [_accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionLinkDirective"]]
          }]
        }];
      }, {
        onClick: [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["HostListener"],
          args: ['click', ['$event']]
        }]
      });
    })();
    /***/

  },

  /***/
  "./src/app/shared/accordion/accordionlink.directive.ts":
  /*!*************************************************************!*\
    !*** ./src/app/shared/accordion/accordionlink.directive.ts ***!
    \*************************************************************/

  /*! exports provided: AccordionLinkDirective */

  /***/
  function srcAppSharedAccordionAccordionlinkDirectiveTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "AccordionLinkDirective", function () {
      return AccordionLinkDirective;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _accordion_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ./accordion.directive */
    "./src/app/shared/accordion/accordion.directive.ts");

    var AccordionLinkDirective =
    /*#__PURE__*/
    function () {
      function AccordionLinkDirective(nav) {
        _classCallCheck(this, AccordionLinkDirective);

        this.nav = nav;
      }

      _createClass(AccordionLinkDirective, [{
        key: "ngOnInit",
        value: function ngOnInit() {
          this.nav.addLink(this);
        }
      }, {
        key: "ngOnDestroy",
        value: function ngOnDestroy() {
          this.nav.removeGroup(this);
        }
      }, {
        key: "toggle",
        value: function toggle() {
          this.selected = !this.selected;
        }
      }, {
        key: "selected",
        get: function get() {
          return this._selected;
        },
        set: function set(value) {
          this._selected = value;

          if (value) {
            this.nav.closeOtherLinks(this);
          }
        }
      }]);

      return AccordionLinkDirective;
    }();

    AccordionLinkDirective.ɵfac = function AccordionLinkDirective_Factory(t) {
      return new (t || AccordionLinkDirective)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_accordion_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionDirective"]));
    };

    AccordionLinkDirective.ɵdir = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineDirective"]({
      type: AccordionLinkDirective,
      selectors: [["", "appAccordionLink", ""]],
      hostVars: 2,
      hostBindings: function AccordionLinkDirective_HostBindings(rf, ctx) {
        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵclassProp"]("selected", ctx.selected);
        }
      },
      inputs: {
        group: "group",
        selected: "selected"
      }
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](AccordionLinkDirective, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Directive"],
        args: [{
          selector: '[appAccordionLink]'
        }]
      }], function () {
        return [{
          type: _accordion_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionDirective"],
          decorators: [{
            type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Inject"],
            args: [_accordion_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionDirective"]]
          }]
        }];
      }, {
        group: [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Input"]
        }],
        selected: [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["HostBinding"],
          args: ['class.selected']
        }, {
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Input"]
        }]
      });
    })();
    /***/

  },

  /***/
  "./src/app/shared/accordion/index.ts":
  /*!*******************************************!*\
    !*** ./src/app/shared/accordion/index.ts ***!
    \*******************************************/

  /*! exports provided: AccordionAnchorDirective, AccordionLinkDirective, AccordionDirective */

  /***/
  function srcAppSharedAccordionIndexTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony import */


    var _accordionanchor_directive__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! ./accordionanchor.directive */
    "./src/app/shared/accordion/accordionanchor.directive.ts");
    /* harmony reexport (safe) */


    __webpack_require__.d(__webpack_exports__, "AccordionAnchorDirective", function () {
      return _accordionanchor_directive__WEBPACK_IMPORTED_MODULE_0__["AccordionAnchorDirective"];
    });
    /* harmony import */


    var _accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ./accordionlink.directive */
    "./src/app/shared/accordion/accordionlink.directive.ts");
    /* harmony reexport (safe) */


    __webpack_require__.d(__webpack_exports__, "AccordionLinkDirective", function () {
      return _accordionlink_directive__WEBPACK_IMPORTED_MODULE_1__["AccordionLinkDirective"];
    });
    /* harmony import */


    var _accordion_directive__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ./accordion.directive */
    "./src/app/shared/accordion/accordion.directive.ts");
    /* harmony reexport (safe) */


    __webpack_require__.d(__webpack_exports__, "AccordionDirective", function () {
      return _accordion_directive__WEBPACK_IMPORTED_MODULE_2__["AccordionDirective"];
    });
    /***/

  },

  /***/
  "./src/app/shared/menu-items/menu-items.ts":
  /*!*************************************************!*\
    !*** ./src/app/shared/menu-items/menu-items.ts ***!
    \*************************************************/

  /*! exports provided: MenuItems */

  /***/
  function srcAppSharedMenuItemsMenuItemsTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "MenuItems", function () {
      return MenuItems;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");

    var MENUITEMS = [//{ state: 'dashboard', name: 'Dashboard', type: 'link', icon: 'av_timer' },
    {
      state: 'fileupload',
      name: 'File Upload',
      type: 'link',
      icon: 'av_timer'
    }];

    var MenuItems =
    /*#__PURE__*/
    function () {
      function MenuItems() {
        _classCallCheck(this, MenuItems);
      }

      _createClass(MenuItems, [{
        key: "getMenuitem",
        value: function getMenuitem() {
          return MENUITEMS;
        }
      }]);

      return MenuItems;
    }();

    MenuItems.ɵfac = function MenuItems_Factory(t) {
      return new (t || MenuItems)();
    };

    MenuItems.ɵprov = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineInjectable"]({
      token: MenuItems,
      factory: MenuItems.ɵfac
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](MenuItems, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Injectable"]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/shared/shared.module.ts":
  /*!*****************************************!*\
    !*** ./src/app/shared/shared.module.ts ***!
    \*****************************************/

  /*! exports provided: SharedModule */

  /***/
  function srcAppSharedSharedModuleTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "SharedModule", function () {
      return SharedModule;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _menu_items_menu_items__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ./menu-items/menu-items */
    "./src/app/shared/menu-items/menu-items.ts");
    /* harmony import */


    var _accordion__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ./accordion */
    "./src/app/shared/accordion/index.ts");

    var SharedModule = function SharedModule() {
      _classCallCheck(this, SharedModule);
    };

    SharedModule.ɵmod = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineNgModule"]({
      type: SharedModule
    });
    SharedModule.ɵinj = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineInjector"]({
      factory: function SharedModule_Factory(t) {
        return new (t || SharedModule)();
      },
      providers: [_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_1__["MenuItems"]]
    });

    (function () {
      (typeof ngJitMode === "undefined" || ngJitMode) && _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵsetNgModuleScope"](SharedModule, {
        declarations: [_accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionAnchorDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionLinkDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionDirective"]],
        exports: [_accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionAnchorDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionLinkDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionDirective"]]
      });
    })();
    /*@__PURE__*/


    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](SharedModule, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["NgModule"],
        args: [{
          declarations: [_accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionAnchorDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionLinkDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionDirective"]],
          exports: [_accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionAnchorDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionLinkDirective"], _accordion__WEBPACK_IMPORTED_MODULE_2__["AccordionDirective"]],
          providers: [_menu_items_menu_items__WEBPACK_IMPORTED_MODULE_1__["MenuItems"]]
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/shared/spinner.component.ts":
  /*!*********************************************!*\
    !*** ./src/app/shared/spinner.component.ts ***!
    \*********************************************/

  /*! exports provided: SpinnerComponent */

  /***/
  function srcAppSharedSpinnerComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "SpinnerComponent", function () {
      return SpinnerComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function SpinnerComponent_div_0_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](0, "div", 1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementStart"](1, "div", 2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](2, "div", 3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelement"](3, "div", 4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵelementEnd"]();
      }
    }

    var SpinnerComponent =
    /*#__PURE__*/
    function () {
      function SpinnerComponent(router, document) {
        var _this17 = this;

        _classCallCheck(this, SpinnerComponent);

        this.router = router;
        this.document = document;
        this.isSpinnerVisible = true;
        this.backgroundColor = 'rgba(0, 115, 170, 0.69)';
        this.router.events.subscribe(function (event) {
          if (event instanceof _angular_router__WEBPACK_IMPORTED_MODULE_1__["NavigationStart"]) {
            _this17.isSpinnerVisible = true;
          } else if (event instanceof _angular_router__WEBPACK_IMPORTED_MODULE_1__["NavigationEnd"] || event instanceof _angular_router__WEBPACK_IMPORTED_MODULE_1__["NavigationCancel"] || event instanceof _angular_router__WEBPACK_IMPORTED_MODULE_1__["NavigationError"]) {
            _this17.isSpinnerVisible = false;
          }
        }, function () {
          _this17.isSpinnerVisible = false;
        });
      }

      _createClass(SpinnerComponent, [{
        key: "ngOnDestroy",
        value: function ngOnDestroy() {
          this.isSpinnerVisible = false;
        }
      }]);

      return SpinnerComponent;
    }();

    SpinnerComponent.ɵfac = function SpinnerComponent_Factory(t) {
      return new (t || SpinnerComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_router__WEBPACK_IMPORTED_MODULE_1__["Router"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdirectiveInject"](_angular_common__WEBPACK_IMPORTED_MODULE_2__["DOCUMENT"]));
    };

    SpinnerComponent.ɵcmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵdefineComponent"]({
      type: SpinnerComponent,
      selectors: [["app-spinner"]],
      inputs: {
        backgroundColor: "backgroundColor"
      },
      decls: 1,
      vars: 1,
      consts: [["class", "preloader", 4, "ngIf"], [1, "preloader"], [1, "spinner"], [1, "double-bounce1"], [1, "double-bounce2"]],
      template: function SpinnerComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵtemplate"](0, SpinnerComponent_div_0_Template, 4, 0, "div", 0);
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵɵproperty"]("ngIf", ctx.isSpinnerVisible);
        }
      },
      directives: [_angular_common__WEBPACK_IMPORTED_MODULE_2__["NgIf"]],
      encapsulation: 2
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["ɵsetClassMetadata"](SpinnerComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-spinner',
          template: "<div class=\"preloader\" *ngIf=\"isSpinnerVisible\">\n        <div class=\"spinner\">\n          <div class=\"double-bounce1\"></div>\n          <div class=\"double-bounce2\"></div>\n        </div>\n    </div>",
          encapsulation: _angular_core__WEBPACK_IMPORTED_MODULE_0__["ViewEncapsulation"].None
        }]
      }], function () {
        return [{
          type: _angular_router__WEBPACK_IMPORTED_MODULE_1__["Router"]
        }, {
          type: Document,
          decorators: [{
            type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Inject"],
            args: [_angular_common__WEBPACK_IMPORTED_MODULE_2__["DOCUMENT"]]
          }]
        }];
      }, {
        backgroundColor: [{
          type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Input"]
        }]
      });
    })();
    /***/

  },

  /***/
  "./src/environments/environment.ts":
  /*!*****************************************!*\
    !*** ./src/environments/environment.ts ***!
    \*****************************************/

  /*! exports provided: environment */

  /***/
  function srcEnvironmentsEnvironmentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "environment", function () {
      return environment;
    }); // The file contents for the current environment will overwrite these during build.
    // The build system defaults to the dev environment which uses `environment.ts`, but if you do
    // `ng build --env=prod` then `environment.prod.ts` will be used instead.
    // The list of which env maps to which file can be found in `.angular-cli.json`.


    var environment = {
      production: false,
      BASE_URL: '/'
    };
    /***/
  },

  /***/
  "./src/main.ts":
  /*!*********************!*\
    !*** ./src/main.ts ***!
    \*********************/

  /*! no exports provided */

  /***/
  function srcMainTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var hammerjs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! hammerjs */
    "./node_modules/hammerjs/hammer.js");
    /* harmony import */


    var hammerjs__WEBPACK_IMPORTED_MODULE_1___default =
    /*#__PURE__*/
    __webpack_require__.n(hammerjs__WEBPACK_IMPORTED_MODULE_1__);
    /* harmony import */


    var _environments_environment__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ./environments/environment */
    "./src/environments/environment.ts");
    /* harmony import */


    var _app_app_module__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! ./app/app.module */
    "./src/app/app.module.ts");
    /* harmony import */


    var _angular_platform_browser__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/platform-browser */
    "./node_modules/@angular/platform-browser/__ivy_ngcc__/fesm2015/platform-browser.js");

    if (_environments_environment__WEBPACK_IMPORTED_MODULE_2__["environment"].production) {
      Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["enableProdMode"])();
    }

    _angular_platform_browser__WEBPACK_IMPORTED_MODULE_4__["platformBrowser"]().bootstrapModule(_app_app_module__WEBPACK_IMPORTED_MODULE_3__["AppModule"])["catch"](function (err) {
      return console.log(err);
    });
    /***/

  },

  /***/
  0:
  /*!***************************!*\
    !*** multi ./src/main.ts ***!
    \***************************/

  /*! no static exports found */

  /***/
  function _(module, exports, __webpack_require__) {
    module.exports = __webpack_require__(
    /*! C:\boostapp\csv\src\main.ts */
    "./src/main.ts");
    /***/
  }
}, [[0, "runtime", "vendor"]]]);
//# sourceMappingURL=main-es5.js.map