function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["dashboard-dashboard-module"], {
  /***/
  "./src/app/dashboard/dashboard.component.ts":
  /*!**************************************************!*\
    !*** ./src/app/dashboard/dashboard.component.ts ***!
    \**************************************************/

  /*! exports provided: DashboardComponent */

  /***/
  function srcAppDashboardDashboardComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DashboardComponent", function () {
      return DashboardComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/flex-layout/flex */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");
    /* harmony import */


    var ng_chartist__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! ng-chartist */
    "./node_modules/ng-chartist/__ivy_ngcc__/fesm2015/ng-chartist.js");
    /* harmony import */


    var _angular_material_core__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/material/core */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/core.js");

    var data = __webpack_require__(
    /*! ./data.json */
    "./src/app/dashboard/data.json");

    var DashboardComponent =
    /*#__PURE__*/
    function () {
      function DashboardComponent() {
        _classCallCheck(this, DashboardComponent);

        // Barchart
        this.barChart1 = {
          type: 'Bar',
          data: data['Bar'],
          options: {
            seriesBarDistance: 15,
            high: 12,
            axisX: {
              showGrid: false,
              offset: 20
            },
            axisY: {
              showGrid: true,
              offset: 40
            },
            height: 360
          },
          responsiveOptions: [['screen and (min-width: 640px)', {
            axisX: {
              labelInterpolationFnc: function labelInterpolationFnc(value, index) {
                return index % 1 === 0 ? "".concat(value) : null;
              }
            }
          }]]
        }; // This is for the donute chart

        this.donuteChart1 = {
          type: 'Pie',
          data: data['Pie'],
          options: {
            donut: true,
            height: 260,
            showLabel: false,
            donutWidth: 20
          }
        };
      }

      _createClass(DashboardComponent, [{
        key: "ngAfterViewInit",
        value: function ngAfterViewInit() {}
      }]);

      return DashboardComponent;
    }();

    DashboardComponent.??fac = function DashboardComponent_Factory(t) {
      return new (t || DashboardComponent)();
    };

    DashboardComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: DashboardComponent,
      selectors: [["app-dashboard"]],
      decls: 226,
      vars: 10,
      consts: [["fxLayout", "row wrap"], ["fxFlex.gt-lg", "66", "fxFlex.gt-md", "66", "fxFlex.gt-xs", "100", "fxFlex", "100"], [1, "d-flex", "flex-wrap"], [1, "ml-auto"], [1, "list-inline"], [1, "text-success", "m-0"], [1, "mdi", "mdi-checkbox-blank-circle", "font-10", "m-r-10"], [1, "text-info", "m-0"], [1, "barchrt", 2, "height", "360px"], [1, "", 3, "data", "type", "options", "responsiveOptions", "events"], ["fxFlex.gt-lg", "33", "fxFlex.gt-md", "33", "fxFlex.gt-xs", "100", "fxFlex", "100"], [1, "piechart"], [1, "list-inline", "text-center"], [1, "text-purple", "m-0"], ["fxFlex.gt-lg", "25", "fxFlex.gt-md", "40", "fxFlex.gt-xs", "100", "fxFlex", "100"], [1, "oh", "text-center", "little-profile"], ["mat-card-image", "", "src", "assets/images/background/profile-bg.jpg", "alt", "Photo of a Shiba Inu"], [1, "pro-img"], ["src", "assets/images/users/4.jpg", "width", "100", "alt", "user", 1, "img-circle"], [1, "m-b-0"], [1, "m-t-0"], ["mat-raised-button", "", "color", "warn"], ["fxLayout", "row", "fxLayoutWrap", "wrap", 1, "m-t-30"], ["fxFlex.gt-sm", "33.33%", "fxFlex.gt-xs", "33.33%", "fxFlex", "100"], [1, "m-0", "font-light"], [1, "p-20", "bg-info", "position-relative"], [1, "card-title", "text-white", "m-0"], [1, "card-subtitle", "text-white", "m-0", "op-5"], ["mat-mini-fab", "", "color", "accent", 1, "add-contact"], [1, "message-box", "contact-box", "p-20"], [1, "message-widget", "contact-widget"], ["href", "#"], [1, "user-img"], ["src", "../assets/images/users/1.jpg", "alt", "user", 1, "img-circle"], [1, "profile-status", "online", "pull-right"], [1, "mail-contnet"], [1, "mail-desc"], ["src", "../assets/images/users/2.jpg", "alt", "user", 1, "img-circle"], [1, "profile-status", "busy", "pull-right"], [1, "round"], [1, "profile-status", "away", "pull-right"], ["src", "../assets/images/users/4.jpg", "alt", "user", 1, "img-circle"], [1, "profile-status", "offline", "pull-right"], ["src", "../assets/images/users/5.jpg", "alt", "user", 1, "img-circle"], ["src", "../assets/images/users/6.jpg", "alt", "user", 1, "img-circle"], ["fxFlex.gt-lg", "75", "fxFlex.gt-md", "60", "fxFlex.gt-xs", "100", "fxFlex", "100"], ["label", "Activity"], [1, "d-flex", "no-blcok"], [1, "m-r-20"], ["width", "50", "src", "assets/images/users/1.jpg", "alt", "Image", 1, "img-circle"], [1, "p-b-20", "b-b", "m-b-30"], [1, "m-0"], [1, "text-muted"], ["fxFlex.gt-sm", "20", "fxFlex", "100"], ["src", "assets/images/big/img2.jpg", "alt", "Image", 1, "img-responsive", "rounded"], ["width", "50", "src", "assets/images/users/2.jpg", "alt", "Image", 1, "img-circle"], ["mat-raised-button", "", "color", "primary"], ["width", "50", "src", "assets/images/users/3.jpg", "alt", "Image", 1, "img-circle"], ["src", "assets/images/big/img1.jpg", "alt", "Image", 1, "img-responsive", "rounded"], ["width", "50", "src", "assets/images/users/4.jpg", "alt", "Image", 1, "img-circle"], ["label", "Profile"], [1, "basic-form"], ["fxFlex.gt-sm", "100", "fxFlex", "100"], ["matInput", "", "placeholder", "Some text value"], ["matInput", "", "placeholder", "EmailId", "type", "email"], ["matInput", "", "placeholder", "Password", "type", "password"], ["placeholder", "Select"], ["value", "option"], ["matInput", "", "placeholder", "Textarea"]],
      template: function DashboardComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "Sales Overview");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, "Ample Admin Vs Material Admin");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "div", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "ul", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "h6", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](14, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, "Ample");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "h6", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](18, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "Pixel");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "div", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](21, "x-chartist", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "div", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](26, "Our Visitors");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](28, "Different Devices Used to Visit");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "div", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](30, "x-chartist", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](31, "hr");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "ul", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "h6", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](36, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Mobile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](38, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "h6", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](40, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, "Desktop");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](43, "h6", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](44, "i", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](45, "Tablet");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](47, "div", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "mat-card", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](49, "img", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "div", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](52, "img", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](53, "h3", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](54, "Angela Dominic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](55, "h6", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](56, "Web Designer & Developer");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](57, "mat-card-actions");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](59, "Follow");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](60, "div", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "div", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](62, "h3", 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](63, "1099");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](64, "small");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](65, "Articles");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "div", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "h3", 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](68, "23,469");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "small");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](70, "Followers");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "div", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](72, "h3", 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](73, "6035");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](74, "small");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](75, "Likes");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](76, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](77, "div", 25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](78, "h4", 26);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](79, "My Contact");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](80, "h6", 27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](81, "Checkout my contacts here");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](82, "button", 28);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](83, "+");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](84, "div", 29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](85, "div", 30);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](86, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](87, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](88, "img", 33);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](89, "span", 34);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](90, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](91, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](92, "Pavan kumar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](93, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](94, "info@wrappixel.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](95, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](96, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](97, "img", 37);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](98, "span", 38);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](99, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](100, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](101, "Sonu Nigam");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](102, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](103, "pamela1987@gmail.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](104, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](105, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](106, "span", 39);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](107, "A");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](108, "span", 40);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](109, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](110, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](111, "Arijit Sinh");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](112, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](113, "cruise1298.fiplip@gmail.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](114, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](115, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](116, "img", 41);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](117, "span", 42);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](118, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](119, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](120, "Pavan kumar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](121, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](122, "kat@gmail.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](123, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](124, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](125, "img", 43);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](126, "span", 42);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](127, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](128, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](129, "Andrew");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](130, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](131, "and@gmail.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](132, "a", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](133, "div", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](134, "img", 44);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](135, "span", 42);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](136, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](137, "h5");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](138, "Jonathan Jones");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](139, "span", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](140, "jj@gmail.com");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](141, "div", 45);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](142, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](143, "mat-tab-group");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](144, "mat-tab", 46);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](145, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](146, "div", 47);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](147, "div", 48);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](148, "img", 49);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](149, "div", 50);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](150, "h4", 51);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](151, "Nirav joshi ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](152, "small", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](153, "(5 minute ago)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](154, "p", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](155, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](156, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](157, "div", 53);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](158, "img", 54);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](159, "div", 47);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](160, "div", 48);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](161, "img", 55);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](162, "div", 50);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](163, "h4", 51);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](164, "Sunil joshi ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](165, "small", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](166, "(3 minute ago)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](167, "p", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](168, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](169, "button", 56);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](170, "Check Now");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](171, "div", 47);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](172, "div", 48);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](173, "img", 57);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](174, "div", 50);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](175, "h4", 51);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](176, "Vishal Bhatt ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](177, "small", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](178, "(1 minute ago)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](179, "p", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](180, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](181, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](182, "div", 53);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](183, "img", 58);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](184, "div", 47);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](185, "div", 48);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](186, "img", 59);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](187, "div", 50);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](188, "h4", 51);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](189, "Dhiren Adesara ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](190, "small", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](191, "(1 minute ago)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](192, "p", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](193, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](194, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](195, "Check Now");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](196, "mat-tab", 60);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](197, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](198, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](199, "Form Basic Layouts");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](200, "form", 61);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](201, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](202, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](203, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](204, "input", 63);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](205, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](206, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](207, "input", 64);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](208, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](209, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](210, "input", 65);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](211, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](212, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](213, "mat-select", 66);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](214, "mat-option", 67);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](215, "Option");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](216, "mat-option", 67);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](217, "Option2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](218, "mat-option", 67);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](219, "Option3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](220, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](221, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](222, "textarea", 68);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](223, "div", 62);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](224, "button", 56);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](225, "Update Profile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("data", ctx.barChart1.data)("type", ctx.barChart1.type)("options", ctx.barChart1.options)("responsiveOptions", ctx.barChart1.responsiveOptions)("events", ctx.barChart1.events);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("data", ctx.donuteChart1.data)("type", ctx.donuteChart1.type)("options", ctx.donuteChart1.options)("responsiveOptions", ctx.donuteChart1.responsiveOptions)("events", ctx.donuteChart1.events);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], ng_chartist__WEBPACK_IMPORTED_MODULE_3__["ChartistComponent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardImage"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardActions"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTabGroup"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTab"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatInput"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSelect"], _angular_material_core__WEBPACK_IMPORTED_MODULE_4__["MatOption"]],
      styles: [".position-relative[_ngcontent-%COMP%] {\n  position: relative;\n}\n\n.add-contact[_ngcontent-%COMP%] {\n  position: absolute;\n  right: 17px;\n  top: 57px;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvZGFzaGJvYXJkL0M6XFxib29zdGFwcFxcY3N2L3NyY1xcYXBwXFxkYXNoYm9hcmRcXGRhc2hib2FyZC5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvZGFzaGJvYXJkL2Rhc2hib2FyZC5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNDLGtCQUFrQjtBQ0NuQjs7QURFQTtFQUNDLGtCQUFrQjtFQUNmLFdBQVc7RUFDWCxTQUFTO0FDQ2IiLCJmaWxlIjoic3JjL2FwcC9kYXNoYm9hcmQvZGFzaGJvYXJkLmNvbXBvbmVudC5zY3NzIiwic291cmNlc0NvbnRlbnQiOlsiLnBvc2l0aW9uLXJlbGF0aXZlIHtcclxuXHRwb3NpdGlvbjogcmVsYXRpdmU7XHJcbn1cclxuXHJcbi5hZGQtY29udGFjdCB7XHJcblx0cG9zaXRpb246IGFic29sdXRlO1xyXG4gICAgcmlnaHQ6IDE3cHg7XHJcbiAgICB0b3A6IDU3cHg7XHJcbn0iLCIucG9zaXRpb24tcmVsYXRpdmUge1xuICBwb3NpdGlvbjogcmVsYXRpdmU7XG59XG5cbi5hZGQtY29udGFjdCB7XG4gIHBvc2l0aW9uOiBhYnNvbHV0ZTtcbiAgcmlnaHQ6IDE3cHg7XG4gIHRvcDogNTdweDtcbn1cbiJdfQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](DashboardComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-dashboard',
          templateUrl: './dashboard.component.html',
          styleUrls: ['./dashboard.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/dashboard/dashboard.module.ts":
  /*!***********************************************!*\
    !*** ./src/app/dashboard/dashboard.module.ts ***!
    \***********************************************/

  /*! exports provided: DashboardModule */

  /***/
  function srcAppDashboardDashboardModuleTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DashboardModule", function () {
      return DashboardModule;
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
    /* harmony import */


    var _demo_material_module__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! ../demo-material-module */
    "./src/app/demo-material-module.ts");
    /* harmony import */


    var _angular_flex_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/flex-layout */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex-layout.js");
    /* harmony import */


    var _dashboard_component__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! ./dashboard.component */
    "./src/app/dashboard/dashboard.component.ts");
    /* harmony import */


    var _dashboard_routing__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! ./dashboard.routing */
    "./src/app/dashboard/dashboard.routing.ts");
    /* harmony import */


    var ng_chartist__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! ng-chartist */
    "./node_modules/ng-chartist/__ivy_ngcc__/fesm2015/ng-chartist.js");

    var DashboardModule = function DashboardModule() {
      _classCallCheck(this, DashboardModule);
    };

    DashboardModule.??mod = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineNgModule"]({
      type: DashboardModule
    });
    DashboardModule.??inj = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineInjector"]({
      factory: function DashboardModule_Factory(t) {
        return new (t || DashboardModule)();
      },
      imports: [[_angular_common__WEBPACK_IMPORTED_MODULE_2__["CommonModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_3__["DemoMaterialModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_4__["FlexLayoutModule"], ng_chartist__WEBPACK_IMPORTED_MODULE_7__["ChartistModule"], _angular_router__WEBPACK_IMPORTED_MODULE_1__["RouterModule"].forChild(_dashboard_routing__WEBPACK_IMPORTED_MODULE_6__["DashboardRoutes"])]]
    });

    (function () {
      (typeof ngJitMode === "undefined" || ngJitMode) && _angular_core__WEBPACK_IMPORTED_MODULE_0__["????setNgModuleScope"](DashboardModule, {
        declarations: [_dashboard_component__WEBPACK_IMPORTED_MODULE_5__["DashboardComponent"]],
        imports: [_angular_common__WEBPACK_IMPORTED_MODULE_2__["CommonModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_3__["DemoMaterialModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_4__["FlexLayoutModule"], ng_chartist__WEBPACK_IMPORTED_MODULE_7__["ChartistModule"], _angular_router__WEBPACK_IMPORTED_MODULE_1__["RouterModule"]]
      });
    })();
    /*@__PURE__*/


    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](DashboardModule, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["NgModule"],
        args: [{
          imports: [_angular_common__WEBPACK_IMPORTED_MODULE_2__["CommonModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_3__["DemoMaterialModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_4__["FlexLayoutModule"], ng_chartist__WEBPACK_IMPORTED_MODULE_7__["ChartistModule"], _angular_router__WEBPACK_IMPORTED_MODULE_1__["RouterModule"].forChild(_dashboard_routing__WEBPACK_IMPORTED_MODULE_6__["DashboardRoutes"])],
          declarations: [_dashboard_component__WEBPACK_IMPORTED_MODULE_5__["DashboardComponent"]]
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/dashboard/dashboard.routing.ts":
  /*!************************************************!*\
    !*** ./src/app/dashboard/dashboard.routing.ts ***!
    \************************************************/

  /*! exports provided: DashboardRoutes */

  /***/
  function srcAppDashboardDashboardRoutingTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DashboardRoutes", function () {
      return DashboardRoutes;
    });
    /* harmony import */


    var _dashboard_component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! ./dashboard.component */
    "./src/app/dashboard/dashboard.component.ts");

    var DashboardRoutes = [{
      path: '',
      component: _dashboard_component__WEBPACK_IMPORTED_MODULE_0__["DashboardComponent"]
    }];
    /***/
  },

  /***/
  "./src/app/dashboard/data.json":
  /*!*************************************!*\
    !*** ./src/app/dashboard/data.json ***!
    \*************************************/

  /*! exports provided: Bar, Pie, default */

  /***/
  function srcAppDashboardDataJson(module) {
    module.exports = JSON.parse("{\"Bar\":{\"labels\":[\"Jan\",\"Feb\",\"Mar\",\"Apr\",\"May\",\"Jun\"],\"series\":[[9,4,11,7,10,12],[3,2,9,5,8,10]]},\"Pie\":{\"series\":[20,10,30,40]}}");
    /***/
  }
}]);
//# sourceMappingURL=dashboard-dashboard-module-es5.js.map