function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["material-component-material-module"], {
  /***/
  "./src/app/material-component/buttons/buttons.component.ts":
  /*!*****************************************************************!*\
    !*** ./src/app/material-component/buttons/buttons.component.ts ***!
    \*****************************************************************/

  /*! exports provided: ButtonsComponent */

  /***/
  function srcAppMaterialComponentButtonsButtonsComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ButtonsComponent", function () {
      return ButtonsComponent;
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


    var _angular_router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");

    var ButtonsComponent = function ButtonsComponent() {
      _classCallCheck(this, ButtonsComponent);
    };

    ButtonsComponent.??fac = function ButtonsComponent_Factory(t) {
      return new (t || ButtonsComponent)();
    };

    ButtonsComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ButtonsComponent,
      selectors: [["app-buttons"]],
      decls: 168,
      vars: 1,
      consts: [["href", "https://material.angular.io/components/button/overview", "target", "_blank"], [1, "bg-light"], [1, "button-row"], ["mat-button", ""], ["mat-button", "", "color", "primary"], ["mat-button", "", "color", "accent"], ["mat-button", "", "color", "warn"], ["mat-button", "", "disabled", ""], ["mat-button", "", "routerLink", "."], ["mat-raised-button", ""], ["mat-raised-button", "", "color", "primary"], ["mat-raised-button", "", "color", "accent"], ["mat-raised-button", "", "color", "warn"], ["mat-raised-button", "", "disabled", ""], ["mat-raised-button", "", "routerLink", "."], ["mat-icon-button", ""], ["aria-label", "Example icon-button with a heart icon"], ["mat-icon-button", "", "color", "primary"], ["mat-icon-button", "", "color", "accent"], ["mat-icon-button", "", "color", "warn"], ["mat-icon-button", "", "disabled", ""], ["mat-fab", ""], ["mat-fab", "", "color", "primary"], ["mat-fab", "", "color", "accent"], ["mat-fab", "", "color", "warn"], ["mat-fab", "", "disabled", ""], ["mat-fab", "", "routerLink", "."], ["mat-mini-fab", ""], ["mat-mini-fab", "", "color", "primary"], ["mat-mini-fab", "", "color", "accent"], ["mat-mini-fab", "", "color", "warn"], ["mat-mini-fab", "", "disabled", ""], ["mat-mini-fab", "", "routerLink", "."], [1, "bg-success", "text-white", "rounded", "font-12", "pl-5", "pr-5"], ["name", "fontStyle", "aria-label", "Font Style"], ["value", "bold"], ["value", "italic"], ["value", "underline"], ["group", "matButtonToggleGroup"], ["value", "left"], ["value", "center"], ["value", "right"], ["value", "justify", "disabled", ""], [1, "m-t-20"], ["name", "fontStyle", "aria-label", "Font Style", 1, "m-l-20"], ["appearance", "legacy", "name", "fontStyle", "aria-label", "Font Style", 1, "m-l-20"]],
      template: function ButtonsComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Buttons");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Angular Material buttons are native ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, " button or a ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, " elements enhanced with Material Design styling and ink ripples. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "a", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](11, "Official Doc");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](13, "Basic Buttons ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](14, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](16, "<button mat-button color=\"primary\">Primary</button>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "button", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "Basic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](21, "Primary");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](23, "Accent");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "button", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25, "Warn");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "button", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, "Disabled");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "a", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](29, "Link");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, "Raised Buttons ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](32, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, "<button mat-raised-button color=\"primary\">Primary</button>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Basic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](38, "button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](39, "Primary");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "button", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, "Accent");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "button", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](43, "Warn");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "button", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](45, "Disabled");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "a", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](47, "Link");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "Icon Buttons ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](50, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](52, "<button mat-icon-button color=\"primary\"> ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](53, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](54, "\xA0\xA0\xA0\xA0<mat-icon aria-label=\"Example icon-button with a heart icon\">favorite</mat-icon>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](55, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](56, "</button>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](57, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "button", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](59, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](60, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](62, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](63, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](64, "button", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](65, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](66, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "button", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](68, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](69, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](70, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](72, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](73, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](74, "Fab Buttons ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](75, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](76, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](77, "<button mat-fab color=\"primary\">Primary</button>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](78, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](79, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](80, "Basic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](81, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](82, "Primary");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](83, "button", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](84, "Accent");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](85, "button", 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](86, "Warn");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](87, "button", 25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](88, "Disabled");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](89, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](90, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](91, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](92, "a", 26);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](93, "Link");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](94, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](95, "Mini Fab Buttons ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](96, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](97, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](98, "<button mat-mini-fab color=\"primary\">Primary</button>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](99, "div", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](100, "button", 27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](101, "Base");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](102, "button", 28);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](103, "Pri");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](104, "button", 29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](105, "Acc");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](106, "button", 30);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](107, "Warn");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](108, "button", 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](109, "Dis");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](110, "button", 27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](111, "mat-icon", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](112, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](113, "a", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](114, "Link");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](115, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](116, "Basic button-toggles ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](117, "span", 33);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](118, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](119, "mat-button-toggle-group", 34);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](120, "mat-button-toggle", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](121, "Bold");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](122, "mat-button-toggle", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](123, "Italic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](124, "mat-button-toggle", 37);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](125, "Underline");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](126, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](127, "Button toggle ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](128, "mat-button-toggle-group", null, 38);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](130, "mat-button-toggle", 39);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](131, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](132, "format_align_left");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](133, "mat-button-toggle", 40);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](134, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](135, "format_align_center");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](136, "mat-button-toggle", 41);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](137, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](138, "format_align_right");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](139, "mat-button-toggle", 42);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](140, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](141, "format_align_justify");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](142, "div", 43);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](143);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](144, "h4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](145, "Button toggle appearance ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](146, "span", 33);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](147, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](148, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](149, "Default appearance:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](150, "mat-button-toggle-group", 44);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](151, "mat-button-toggle", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](152, "Bold");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](153, "mat-button-toggle", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](154, "Italic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](155, "mat-button-toggle", 37);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](156, "Underline");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](157, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](158, "div", 43);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](159, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](160, "Legacy appearance:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](161, "mat-button-toggle-group", 45);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](162, "mat-button-toggle", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](163, "Bold");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](164, "mat-button-toggle", 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](165, "Italic");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](166, "mat-button-toggle", 37);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](167, "Underline");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r1 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](129);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](143);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"]("Selected value: ", _r1.value, "");
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatAnchor"], _angular_router__WEBPACK_IMPORTED_MODULE_2__["RouterLinkWithHref"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonToggleGroup"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButtonToggle"]],
      styles: [".example-button-row[_ngcontent-%COMP%] {\n  display: flex;\n  align-items: center;\n  justify-content: space-around;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2J1dHRvbnMvQzpcXGJvb3N0YXBwXFxjc3Yvc3JjXFxhcHBcXG1hdGVyaWFsLWNvbXBvbmVudFxcYnV0dG9uc1xcYnV0dG9ucy5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2J1dHRvbnMvYnV0dG9ucy5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNFLGFBQWE7RUFDYixtQkFBbUI7RUFDbkIsNkJBQTZCO0FDQy9CIiwiZmlsZSI6InNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2J1dHRvbnMvYnV0dG9ucy5jb21wb25lbnQuc2NzcyIsInNvdXJjZXNDb250ZW50IjpbIi5leGFtcGxlLWJ1dHRvbi1yb3cge1xyXG4gIGRpc3BsYXk6IGZsZXg7XHJcbiAgYWxpZ24taXRlbXM6IGNlbnRlcjtcclxuICBqdXN0aWZ5LWNvbnRlbnQ6IHNwYWNlLWFyb3VuZDtcclxufVxyXG5cclxuIiwiLmV4YW1wbGUtYnV0dG9uLXJvdyB7XG4gIGRpc3BsYXk6IGZsZXg7XG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XG4gIGp1c3RpZnktY29udGVudDogc3BhY2UtYXJvdW5kO1xufVxuIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ButtonsComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-buttons',
          templateUrl: './buttons.component.html',
          styleUrls: ['./buttons.component.scss']
        }]
      }], function () {
        return [];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/chips/chips.component.ts":
  /*!*************************************************************!*\
    !*** ./src/app/material-component/chips/chips.component.ts ***!
    \*************************************************************/

  /*! exports provided: ChipsComponent */

  /***/
  function srcAppMaterialComponentChipsChipsComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ChipsComponent", function () {
      return ChipsComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_cdk_keycodes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/cdk/keycodes */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/keycodes.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function ChipsComponent_mat_chip_29_mat_icon_2_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-icon", 12);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1, "cancel");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }
    }

    function ChipsComponent_mat_chip_29_Template(rf, ctx) {
      if (rf & 1) {
        var _r68 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-chip", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("removed", function ChipsComponent_mat_chip_29_Template_mat_chip_removed_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r68);

          var fruit_r65 = ctx.$implicit;

          var ctx_r67 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

          return ctx_r67.remove(fruit_r65);
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](2, ChipsComponent_mat_chip_29_mat_icon_2_Template, 2, 0, "mat-icon", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var fruit_r65 = ctx.$implicit;

        var ctx_r63 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("selectable", ctx_r63.selectable)("removable", ctx_r63.removable);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", fruit_r65.name, " ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngIf", ctx_r63.removable);
      }
    }

    function ChipsComponent_mat_chip_41_Template(rf, ctx) {
      if (rf & 1) {
        var _r71 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-chip", 13);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("focus", function ChipsComponent_mat_chip_41_Template_mat_chip_focus_0_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r71);

          var aColor_r69 = ctx.$implicit;

          var ctx_r70 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

          return ctx_r70.color = aColor_r69.color;
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var aColor_r69 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????propertyInterpolate"]("color", aColor_r69.color);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", aColor_r69.name, " ");
      }
    }

    var ChipsComponent =
    /*#__PURE__*/
    function () {
      function ChipsComponent() {
        _classCallCheck(this, ChipsComponent);

        this.visible = true;
        this.selectable = true;
        this.removable = true;
        this.addOnBlur = true;
        this.availableColors = [{
          name: 'none',
          color: 'gray'
        }, {
          name: 'Primary',
          color: 'primary'
        }, {
          name: 'Accent',
          color: 'accent'
        }, {
          name: 'Warn',
          color: 'warn'
        }]; // Enter, comma

        this.separatorKeysCodes = [_angular_cdk_keycodes__WEBPACK_IMPORTED_MODULE_1__["ENTER"], _angular_cdk_keycodes__WEBPACK_IMPORTED_MODULE_1__["COMMA"]];
        this.fruits = [{
          name: 'Lemon'
        }, {
          name: 'Lime'
        }, {
          name: 'Apple'
        }];
      }

      _createClass(ChipsComponent, [{
        key: "add",
        value: function add(event) {
          var input = event.input;
          var value = event.value; // Add our fruit

          if ((value || '').trim()) {
            this.fruits.push({
              name: value.trim()
            });
          } // Reset the input value


          if (input) {
            input.value = '';
          }
        }
      }, {
        key: "remove",
        value: function remove(fruit) {
          var index = this.fruits.indexOf(fruit);

          if (index >= 0) {
            this.fruits.splice(index, 1);
          }
        }
      }]);

      return ChipsComponent;
    }();

    ChipsComponent.??fac = function ChipsComponent_Factory(t) {
      return new (t || ChipsComponent)();
    };

    ChipsComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ChipsComponent,
      selectors: [["app-chips"]],
      decls: 42,
      vars: 5,
      consts: [[1, ""], ["href", "https://material.angular.io/components/chips/overview"], ["color", "primary", "selected", "true"], ["color", "accent", "selected", "true"], [1, "demo-chip-list"], ["chipList", ""], [3, "selectable", "removable", "removed", 4, "ngFor", "ngForOf"], ["placeholder", "New fruit...", 3, "matChipInputFor", "matChipInputSeparatorKeyCodes", "matChipInputAddOnBlur", "matChipInputTokenEnd"], [1, "mat-chip-list-stacked"], ["selected", "true", 3, "color", "focus", 4, "ngFor", "ngForOf"], [3, "selectable", "removable", "removed"], ["matChipRemove", "", 4, "ngIf"], ["matChipRemove", ""], ["selected", "true", 3, "color", "focus"]],
      template: function ChipsComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Basic Chips");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](6, "<mat-chip>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "displays a list of values as individual, keyboard accessible, chips. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "code", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "a", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "mat-chip-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-chip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](13, "One fish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-chip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, "Two fish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-chip", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Primary fish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "mat-chip", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "Accent fish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](23, "Chip input");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25, "The MatChipInput directive can be used together with a chip-list to streamline the interaction between the two components. This directive adds chip-specific behaviors to the input element within for adding and removing chips. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "mat-form-field", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "mat-chip-list", null, 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](29, ChipsComponent_mat_chip_29_Template, 3, 4, "mat-chip", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "input", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("matChipInputTokenEnd", function ChipsComponent_Template_input_matChipInputTokenEnd_30_listener($event) {
            return ctx.add($event);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, "Stacked Chips");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](36, "You can also stack the chips if you want them on top of each other and/or use the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, "(focus)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](39, " event to run custom code.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "mat-chip-list", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](41, ChipsComponent_mat_chip_41_Template, 2, 2, "mat-chip", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r62 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](28);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.fruits);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matChipInputFor", _r62)("matChipInputSeparatorKeyCodes", ctx.separatorKeysCodes)("matChipInputAddOnBlur", ctx.addOnBlur);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.availableColors);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatChipList"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatChip"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatFormField"], _angular_common__WEBPACK_IMPORTED_MODULE_3__["NgForOf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatChipInput"], _angular_common__WEBPACK_IMPORTED_MODULE_3__["NgIf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatChipRemove"]],
      styles: [".demo-chip-list[_ngcontent-%COMP%] {\n  width: 100%;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2NoaXBzL0M6XFxib29zdGFwcFxcY3N2L3NyY1xcYXBwXFxtYXRlcmlhbC1jb21wb25lbnRcXGNoaXBzXFxjaGlwcy5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2NoaXBzL2NoaXBzLmNvbXBvbmVudC5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQ0UsV0FBVztBQ0NiIiwiZmlsZSI6InNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2NoaXBzL2NoaXBzLmNvbXBvbmVudC5zY3NzIiwic291cmNlc0NvbnRlbnQiOlsiLmRlbW8tY2hpcC1saXN0IHtcclxuICB3aWR0aDogMTAwJTtcclxufSIsIi5kZW1vLWNoaXAtbGlzdCB7XG4gIHdpZHRoOiAxMDAlO1xufVxuIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ChipsComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-chips',
          templateUrl: './chips.component.html',
          styleUrls: ['./chips.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/dialog/dialog.component.ts":
  /*!***************************************************************!*\
    !*** ./src/app/material-component/dialog/dialog.component.ts ***!
    \***************************************************************/

  /*! exports provided: DialogOverviewExampleDialogComponent, DialogComponent */

  /***/
  function srcAppMaterialComponentDialogDialogComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DialogOverviewExampleDialogComponent", function () {
      return DialogOverviewExampleDialogComponent;
    });
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "DialogComponent", function () {
      return DialogComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/flex-layout/flex */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function DialogComponent_li_18_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "li");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1, " You chose: ");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "i");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var ctx_r81 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](ctx_r81.animal);
      }
    }

    var DialogOverviewExampleDialogComponent =
    /*#__PURE__*/
    function () {
      function DialogOverviewExampleDialogComponent(dialogRef, data) {
        _classCallCheck(this, DialogOverviewExampleDialogComponent);

        this.dialogRef = dialogRef;
        this.data = data;
      }

      _createClass(DialogOverviewExampleDialogComponent, [{
        key: "onNoClick",
        value: function onNoClick() {
          this.dialogRef.close();
        }
      }]);

      return DialogOverviewExampleDialogComponent;
    }();

    DialogOverviewExampleDialogComponent.??fac = function DialogOverviewExampleDialogComponent_Factory(t) {
      return new (t || DialogOverviewExampleDialogComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["????directiveInject"](_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogRef"]), _angular_core__WEBPACK_IMPORTED_MODULE_0__["????directiveInject"](_angular_material__WEBPACK_IMPORTED_MODULE_1__["MAT_DIALOG_DATA"]));
    };

    DialogOverviewExampleDialogComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: DialogOverviewExampleDialogComponent,
      selectors: [["app-dialog-overview-example-dialog"]],
      decls: 12,
      vars: 3,
      consts: [["mat-dialog-title", ""], ["mat-dialog-content", ""], ["matInput", "", "tabindex", "1", 3, "ngModel", "ngModelChange"], ["mat-dialog-actions", ""], ["mat-button", "", "tabindex", "2", 3, "mat-dialog-close"], ["mat-button", "", "tabindex", "-1", 3, "click"]],
      template: function DialogOverviewExampleDialogComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "h1", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](4, "What's your favorite animal?");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "input", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function DialogOverviewExampleDialogComponent_Template_input_ngModelChange_6_listener($event) {
            return ctx.data.animal = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "div", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, "Ok");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function DialogOverviewExampleDialogComponent_Template_button_click_10_listener($event) {
            return ctx.onNoClick();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](11, "No Thanks");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"]("Hi ", ctx.data.name, "");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.data.animal);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("mat-dialog-close", ctx.data.animal);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInput"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["DefaultValueAccessor"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgModel"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogActions"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogClose"]],
      encapsulation: 2
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](DialogOverviewExampleDialogComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-dialog-overview-example-dialog',
          template: "<h1 mat-dialog-title>Hi {{data.name}}</h1>\n<div mat-dialog-content>\n  <p>What's your favorite animal?</p>\n  <mat-form-field>\n    <input matInput tabindex=\"1\" [(ngModel)]=\"data.animal\">\n  </mat-form-field>\n</div>\n<div mat-dialog-actions>\n  <button mat-button [mat-dialog-close]=\"data.animal\" tabindex=\"2\">Ok</button>\n  <button mat-button (click)=\"onNoClick()\" tabindex=\"-1\">No Thanks</button>\n</div>"
        }]
      }], function () {
        return [{
          type: _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialogRef"]
        }, {
          type: undefined,
          decorators: [{
            type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Inject"],
            args: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MAT_DIALOG_DATA"]]
          }]
        }];
      }, null);
    })();

    var DialogComponent =
    /*#__PURE__*/
    function () {
      function DialogComponent(dialog) {
        _classCallCheck(this, DialogComponent);

        this.dialog = dialog;
      }

      _createClass(DialogComponent, [{
        key: "openDialog",
        value: function openDialog() {
          var _this = this;

          var dialogRef = this.dialog.open(DialogOverviewExampleDialogComponent, {
            width: '250px',
            data: {
              name: this.name,
              animal: this.animal
            }
          });
          dialogRef.afterClosed().subscribe(function (result) {
            console.log('The dialog was closed');
            _this.animal = result;
          });
        }
      }]);

      return DialogComponent;
    }();

    DialogComponent.??fac = function DialogComponent_Factory(t) {
      return new (t || DialogComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["????directiveInject"](_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialog"]));
    };

    DialogComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: DialogComponent,
      selectors: [["app-dialog"]],
      decls: 19,
      vars: 2,
      consts: [["fxLayout", "row wrap"], ["fxFlex.gt-sm", "100%"], ["matInput", "", "placeholder", "What's your name?", 3, "ngModel", "ngModelChange"], ["mat-raised-button", "", 3, "click"], [4, "ngIf"]],
      template: function DialogComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Dialog Overview");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "The ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, "<MatDialog>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, " service can be used to open modal dialogs with Material Design styling and animations.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "ol");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "input", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function DialogComponent_Template_input_ngModelChange_14_listener($event) {
            return ctx.name = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "li");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "button", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function DialogComponent_Template_button_click_16_listener($event) {
            return ctx.openDialog();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Pick one");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](18, DialogComponent_li_18_Template, 4, 1, "li", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.name);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngIf", ctx.animal);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInput"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["DefaultValueAccessor"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgModel"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_common__WEBPACK_IMPORTED_MODULE_4__["NgIf"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9kaWFsb2cvZGlhbG9nLmNvbXBvbmVudC5zY3NzIn0= */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](DialogComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-dialog',
          templateUrl: './dialog.component.html',
          styleUrls: ['./dialog.component.scss']
        }]
      }], function () {
        return [{
          type: _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDialog"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/expansion/expansion.component.ts":
  /*!*********************************************************************!*\
    !*** ./src/app/material-component/expansion/expansion.component.ts ***!
    \*********************************************************************/

  /*! exports provided: ExpansionComponent */

  /***/
  function srcAppMaterialComponentExpansionExpansionComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ExpansionComponent", function () {
      return ExpansionComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");

    var ExpansionComponent =
    /*#__PURE__*/
    function () {
      function ExpansionComponent() {
        _classCallCheck(this, ExpansionComponent);

        this.panelOpenState = false;
        this.step = 0;
      }

      _createClass(ExpansionComponent, [{
        key: "setStep",
        value: function setStep(index) {
          this.step = index;
        }
      }, {
        key: "nextStep",
        value: function nextStep() {
          this.step++;
        }
      }, {
        key: "prevStep",
        value: function prevStep() {
          this.step--;
        }
      }]);

      return ExpansionComponent;
    }();

    ExpansionComponent.??fac = function ExpansionComponent_Factory(t) {
      return new (t || ExpansionComponent)();
    };

    ExpansionComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ExpansionComponent,
      selectors: [["app-expansion"]],
      decls: 82,
      vars: 5,
      consts: [[1, ""], ["href", "https://material.angular.io/components/expansion/overview"], ["matInput", "", "placeholder", "First name"], ["matInput", "", "placeholder", "Age"], [3, "opened", "closed"], [1, "example-headers-align"], ["hideToggle", "true", 3, "expanded", "opened"], ["matInput", "", "type", "number", "min", "1", "placeholder", "Age"], ["mat-button", "", "color", "primary", 3, "click"], ["matInput", "", "placeholder", "Country"], ["mat-button", "", "color", "warn", 3, "click"], ["matInput", "", "placeholder", "Date", "readonly", "", 3, "matDatepicker", "focus"], ["picker", ""]],
      template: function ExpansionComponent_Template(rf, ctx) {
        if (rf & 1) {
          var _r61 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Basic Expansion");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Expansion panel ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "code", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "a", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "mat-accordion");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "mat-expansion-panel");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "mat-expansion-panel-header");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-panel-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](13, " Personal data ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-panel-description");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, " Type your name and age ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](17, "input", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](19, "input", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-expansion-panel", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("opened", function ExpansionComponent_Template_mat_expansion_panel_opened_20_listener($event) {
            return ctx.panelOpenState = true;
          })("closed", function ExpansionComponent_Template_mat_expansion_panel_closed_20_listener($event) {
            return ctx.panelOpenState = false;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "mat-expansion-panel-header");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-panel-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](23, " Self aware panel ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-panel-description");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, "I'm visible because I am open");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, "Accordion");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](33, "Expansion panel");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "mat-accordion", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-expansion-panel", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("opened", function ExpansionComponent_Template_mat_expansion_panel_opened_35_listener($event) {
            return ctx.setStep(0);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-expansion-panel-header");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "mat-panel-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, " Personal data ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "mat-panel-description");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](40, " Type your name and age ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](41, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](42, "account_circle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](43, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](44, "input", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](46, "input", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](47, "mat-action-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "button", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function ExpansionComponent_Template_button_click_48_listener($event) {
            return ctx.nextStep();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-expansion-panel", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("opened", function ExpansionComponent_Template_mat_expansion_panel_opened_50_listener($event) {
            return ctx.setStep(1);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "mat-expansion-panel-header");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "mat-panel-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, " Destination ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "mat-panel-description");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](55, " Type the country name ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](56, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](57, "map");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](59, "input", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](60, "mat-action-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function ExpansionComponent_Template_button_click_61_listener($event) {
            return ctx.prevStep();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](62, "Previous");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](63, "button", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function ExpansionComponent_Template_button_click_63_listener($event) {
            return ctx.nextStep();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](64, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](65, "mat-expansion-panel", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("opened", function ExpansionComponent_Template_mat_expansion_panel_opened_65_listener($event) {
            return ctx.setStep(2);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "mat-expansion-panel-header");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "mat-panel-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](68, " Day of the trip ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "mat-panel-description");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](70, " Inform the date you wish to travel ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](72, "date_range");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](73, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](74, "input", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("focus", function ExpansionComponent_Template_input_focus_74_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r61);

            var _r60 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](76);

            return _r60.open();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](75, "mat-datepicker", null, 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](77, "mat-action-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](78, "button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function ExpansionComponent_Template_button_click_78_listener($event) {
            return ctx.prevStep();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](79, "Previous");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](80, "button", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function ExpansionComponent_Template_button_click_80_listener($event) {
            return ctx.nextStep();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](81, "End");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r60 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](76);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" Currently I am ", ctx.panelOpenState ? "open" : "closed", " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("expanded", ctx.step === 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("expanded", ctx.step === 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("expanded", ctx.step === 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matDatepicker", _r60);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatAccordion"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionPanel"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionPanelHeader"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionPanelTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionPanelDescription"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInput"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatExpansionPanelActionRow"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDatepickerInput"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatDatepicker"]],
      styles: [".example-headers-align[_ngcontent-%COMP%]   .mat-expansion-panel-header-title[_ngcontent-%COMP%], .example-headers-align[_ngcontent-%COMP%]   .mat-expansion-panel-header-description[_ngcontent-%COMP%] {\n  flex-basis: 0;\n}\n\n.example-headers-align[_ngcontent-%COMP%]   .mat-expansion-panel-header-description[_ngcontent-%COMP%] {\n  justify-content: space-between;\n  align-items: center;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2V4cGFuc2lvbi9DOlxcYm9vc3RhcHBcXGNzdi9zcmNcXGFwcFxcbWF0ZXJpYWwtY29tcG9uZW50XFxleHBhbnNpb25cXGV4cGFuc2lvbi5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2V4cGFuc2lvbi9leHBhbnNpb24uY29tcG9uZW50LnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7O0VBRUUsYUFBYTtBQ0NmOztBREVBO0VBQ0UsOEJBQThCO0VBQzlCLG1CQUFtQjtBQ0NyQiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9leHBhbnNpb24vZXhwYW5zaW9uLmNvbXBvbmVudC5zY3NzIiwic291cmNlc0NvbnRlbnQiOlsiLmV4YW1wbGUtaGVhZGVycy1hbGlnbiAubWF0LWV4cGFuc2lvbi1wYW5lbC1oZWFkZXItdGl0bGUsIFxyXG4uZXhhbXBsZS1oZWFkZXJzLWFsaWduIC5tYXQtZXhwYW5zaW9uLXBhbmVsLWhlYWRlci1kZXNjcmlwdGlvbiB7XHJcbiAgZmxleC1iYXNpczogMDtcclxufVxyXG5cclxuLmV4YW1wbGUtaGVhZGVycy1hbGlnbiAubWF0LWV4cGFuc2lvbi1wYW5lbC1oZWFkZXItZGVzY3JpcHRpb24ge1xyXG4gIGp1c3RpZnktY29udGVudDogc3BhY2UtYmV0d2VlbjtcclxuICBhbGlnbi1pdGVtczogY2VudGVyO1xyXG59IiwiLmV4YW1wbGUtaGVhZGVycy1hbGlnbiAubWF0LWV4cGFuc2lvbi1wYW5lbC1oZWFkZXItdGl0bGUsXG4uZXhhbXBsZS1oZWFkZXJzLWFsaWduIC5tYXQtZXhwYW5zaW9uLXBhbmVsLWhlYWRlci1kZXNjcmlwdGlvbiB7XG4gIGZsZXgtYmFzaXM6IDA7XG59XG5cbi5leGFtcGxlLWhlYWRlcnMtYWxpZ24gLm1hdC1leHBhbnNpb24tcGFuZWwtaGVhZGVyLWRlc2NyaXB0aW9uIHtcbiAganVzdGlmeS1jb250ZW50OiBzcGFjZS1iZXR3ZWVuO1xuICBhbGlnbi1pdGVtczogY2VudGVyO1xufVxuIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ExpansionComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-expansion',
          templateUrl: './expansion.component.html',
          styleUrls: ['./expansion.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/grid/grid.component.ts":
  /*!***********************************************************!*\
    !*** ./src/app/material-component/grid/grid.component.ts ***!
    \***********************************************************/

  /*! exports provided: GridComponent */

  /***/
  function srcAppMaterialComponentGridGridComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "GridComponent", function () {
      return GridComponent;
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


    var _angular_common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function GridComponent_mat_grid_tile_14_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-grid-tile", 6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var tile_r3 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????styleProp"]("background", tile_r3.color, _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defaultStyleSanitizer"]);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("colspan", tile_r3.cols)("rowspan", tile_r3.rows);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", tile_r3.text, " ");
      }
    }

    var GridComponent = function GridComponent() {
      _classCallCheck(this, GridComponent);

      this.tiles = [{
        text: 'One',
        cols: 3,
        rows: 1,
        color: 'lightblue'
      }, {
        text: 'Two',
        cols: 1,
        rows: 2,
        color: 'lightgreen'
      }, {
        text: 'Three',
        cols: 1,
        rows: 1,
        color: 'lightpink'
      }, {
        text: 'Four',
        cols: 2,
        rows: 1,
        color: '#DDBDF1'
      }];
    };

    GridComponent.??fac = function GridComponent_Factory(t) {
      return new (t || GridComponent)();
    };

    GridComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: GridComponent,
      selectors: [["app-grid"]],
      decls: 34,
      vars: 1,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], ["href", "https://material.io/guidelines/components/grid-lists.html"], ["cols", "4", "rowHeight", "100px"], [3, "colspan", "rowspan", "background", 4, "ngFor", "ngForOf"], ["cols", "2", "rowHeight", "2:1"], [3, "colspan", "rowspan"]],
      template: function GridComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Fixed height grid-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-grid-list>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " is a two-dimensional list view that arranges cells into grid-based layout. See Material Design spec. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "a", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](12, "Official Doc here");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-grid-list", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](14, GridComponent_mat_grid_tile_14_Template, 2, 5, "mat-grid-tile", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](18, "Basic grid-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](21, "<mat-grid-list>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](22, " is a two-dimensional list view that arranges cells into grid-based layout. See Material Design spec. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "a", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](24, "here");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "mat-grid-list", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "mat-grid-tile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, "1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-grid-tile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](29, "2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "mat-grid-tile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, "3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-grid-tile");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](33, "4");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.tiles);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatGridList"], _angular_common__WEBPACK_IMPORTED_MODULE_3__["NgForOf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatGridTile"]],
      styles: ["mat-grid-tile[_ngcontent-%COMP%] {\n  background: lightblue;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2dyaWQvQzpcXGJvb3N0YXBwXFxjc3Yvc3JjXFxhcHBcXG1hdGVyaWFsLWNvbXBvbmVudFxcZ3JpZFxcZ3JpZC5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L2dyaWQvZ3JpZC5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNFLHFCQUFxQjtBQ0N2QiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9ncmlkL2dyaWQuY29tcG9uZW50LnNjc3MiLCJzb3VyY2VzQ29udGVudCI6WyJtYXQtZ3JpZC10aWxlIHtcclxuICBiYWNrZ3JvdW5kOiBsaWdodGJsdWU7XHJcbn0iLCJtYXQtZ3JpZC10aWxlIHtcbiAgYmFja2dyb3VuZDogbGlnaHRibHVlO1xufVxuIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](GridComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-grid',
          templateUrl: './grid.component.html',
          styleUrls: ['./grid.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/lists/lists.component.ts":
  /*!*************************************************************!*\
    !*** ./src/app/material-component/lists/lists.component.ts ***!
    \*************************************************************/

  /*! exports provided: ListsComponent */

  /***/
  function srcAppMaterialComponentListsListsComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ListsComponent", function () {
      return ListsComponent;
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


    var _angular_common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");
    /* harmony import */


    var _angular_material_divider__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/material/divider */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/divider.js");
    /* harmony import */


    var _angular_material_core__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! @angular/material/core */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/core.js");

    function ListsComponent_mat_list_option_30_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-list-option");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var shoe_r10 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", shoe_r10, " ");
      }
    }

    function ListsComponent_mat_list_item_41_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-list-item");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "h3", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "p", 12);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "p", 12);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var message_r11 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](message_r11.from);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](message_r11.subject);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](message_r11.content);
      }
    }

    function ListsComponent_mat_list_item_51_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-list-item");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](1, "img", 13);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "h3", 11);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "p", 12);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var message_r12 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????propertyInterpolate1"]("alt", "Image of ", message_r12.from, "");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("src", message_r12.image, _angular_core__WEBPACK_IMPORTED_MODULE_0__["????sanitizeUrl"]);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](message_r12.from);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](message_r12.content);
      }
    }

    function ListsComponent_mat_list_item_64_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-list-item");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-icon", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2, "folder");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "h4", 15);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "p", 15);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????pipe"](7, "date");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var folder_r13 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](folder_r13.name);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _angular_core__WEBPACK_IMPORTED_MODULE_0__["????pipeBind1"](7, 2, folder_r13.updated), " ");
      }
    }

    function ListsComponent_mat_list_item_69_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-list-item");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-icon", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2, "note");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "h4", 15);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](5, "p", 15);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](6);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????pipe"](7, "date");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var note_r14 = ctx.$implicit;

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate"](note_r14.name);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _angular_core__WEBPACK_IMPORTED_MODULE_0__["????pipeBind1"](7, 2, note_r14.updated), " ");
      }
    }

    var ListsComponent = function ListsComponent() {
      _classCallCheck(this, ListsComponent);

      this.typesOfShoes = ['Boots', 'Clogs', 'Loafers', 'Moccasins', 'Sneakers'];
      this.messages = [{
        from: 'Nirav joshi (nbj@gmail.com)',
        image: 'assets/images/users/1.jpg',
        subject: 'Material angular',
        content: 'This is the material angular template'
      }, {
        from: 'Sunil joshi (sbj@gmail.com)',
        image: 'assets/images/users/2.jpg',
        subject: 'Wrappixel',
        content: 'We have wrappixel launched'
      }, {
        from: 'Vishal Bhatt (bht@gmail.com)',
        image: 'assets/images/users/3.jpg',
        subject: 'Task list',
        content: 'This is the latest task hasbeen done'
      }];
      this.folders = [{
        name: 'Photos',
        updated: new Date('1/1/16')
      }, {
        name: 'Recipes',
        updated: new Date('1/17/16')
      }, {
        name: 'Work',
        updated: new Date('1/28/16')
      }];
      this.notes = [{
        name: 'Vacation Itinerary',
        updated: new Date('2/20/16')
      }, {
        name: 'Kitchen Remodel',
        updated: new Date('1/18/16')
      }];
    };

    ListsComponent.??fac = function ListsComponent_Factory(t) {
      return new (t || ListsComponent)();
    };

    ListsComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ListsComponent,
      selectors: [["app-lists"]],
      decls: 70,
      vars: 6,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], ["href", "https://material.angular.io/components/list/overview"], ["role", "list"], ["role", "listitem"], ["fxFlex.gt-sm", "50%"], ["shoes", ""], [4, "ngFor", "ngForOf"], [1, "p-b-0", "m-b-0"], [1, "p-t-0"], ["mat-subheader", ""], ["matLine", ""], ["matLine", "", 1, "text-muted"], ["mat-list-avatar", "", 3, "src", "alt"], ["mat-list-icon", ""], ["mat-line", ""]],
      template: function ListsComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-list>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " is a container component that wraps and formats a series of line items. As the base list component, it provides Material Design styling, but no behavior of its own.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "a", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](12, "Official Doc here");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-list", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-list-item", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-list-item", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "mat-list-item", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "Item 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "div", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25, "List with selection");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, "A selection list provides an interface for selecting values, where each list item is an option.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-selection-list", null, 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](30, ListsComponent_mat_list_option_30_Template, 2, 1, "mat-list-option", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "div", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Multiline lists");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](38, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](39, "A selection list provides an interface for selecting values, where each list item is an option.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "mat-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](41, ListsComponent_mat_list_item_41_Template, 7, 3, "mat-list-item", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](43, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](47, "Multiline lists");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "A selection list provides an interface for selecting values, where each list item is an option.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](51, ListsComponent_mat_list_item_51_Template, 6, 4, "mat-list-item", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](53, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](55, "mat-card-content", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](56, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](57, "List with sections");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](59, "A selection list provides an interface for selecting values, where each list item is an option.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](60, "mat-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "mat-card-content", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](62, "h3", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](63, "Folders");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](64, ListsComponent_mat_list_item_64_Template, 8, 4, "mat-list-item", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](65, "mat-divider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "h3", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](68, "Notes");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](69, ListsComponent_mat_list_item_69_Template, 8, 4, "mat-list-item", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r4 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](30);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.typesOfShoes);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" Options selected: ", _r4.selectedOptions.selected.length, " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.messages);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.messages);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.folders);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngForOf", ctx.notes);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatList"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatListItem"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSelectionList"], _angular_common__WEBPACK_IMPORTED_MODULE_3__["NgForOf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatListSubheaderCssMatStyler"], _angular_material_divider__WEBPACK_IMPORTED_MODULE_4__["MatDivider"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatListOption"], _angular_material_core__WEBPACK_IMPORTED_MODULE_5__["MatLine"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatListAvatarCssMatStyler"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatListIconCssMatStyler"]],
      pipes: [_angular_common__WEBPACK_IMPORTED_MODULE_3__["DatePipe"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9saXN0cy9saXN0cy5jb21wb25lbnQuc2NzcyJ9 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ListsComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-lists',
          templateUrl: './lists.component.html',
          styleUrls: ['./lists.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/material.module.ts":
  /*!*******************************************************!*\
    !*** ./src/app/material-component/material.module.ts ***!
    \*******************************************************/

  /*! exports provided: MaterialComponentsModule */

  /***/
  function srcAppMaterialComponentMaterialModuleTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "MaterialComponentsModule", function () {
      return MaterialComponentsModule;
    });
    /* harmony import */


    var hammerjs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! hammerjs */
    "./node_modules/hammerjs/hammer.js");
    /* harmony import */


    var hammerjs__WEBPACK_IMPORTED_MODULE_0___default =
    /*#__PURE__*/
    __webpack_require__.n(hammerjs__WEBPACK_IMPORTED_MODULE_0__);
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/router */
    "./node_modules/@angular/router/__ivy_ngcc__/fesm2015/router.js");
    /* harmony import */


    var _angular_common_http__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/common/http */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/http.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");
    /* harmony import */


    var _demo_material_module__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! ../demo-material-module */
    "./src/app/demo-material-module.ts");
    /* harmony import */


    var _angular_cdk_table__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! @angular/cdk/table */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/table.js");
    /* harmony import */


    var _angular_forms__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(
    /*! @angular/flex-layout */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex-layout.js");
    /* harmony import */


    var _material_routing__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(
    /*! ./material.routing */
    "./src/app/material-component/material.routing.ts");
    /* harmony import */


    var _buttons_buttons_component__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(
    /*! ./buttons/buttons.component */
    "./src/app/material-component/buttons/buttons.component.ts");
    /* harmony import */


    var _grid_grid_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(
    /*! ./grid/grid.component */
    "./src/app/material-component/grid/grid.component.ts");
    /* harmony import */


    var _lists_lists_component__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(
    /*! ./lists/lists.component */
    "./src/app/material-component/lists/lists.component.ts");
    /* harmony import */


    var _menu_menu_component__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(
    /*! ./menu/menu.component */
    "./src/app/material-component/menu/menu.component.ts");
    /* harmony import */


    var _tabs_tabs_component__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(
    /*! ./tabs/tabs.component */
    "./src/app/material-component/tabs/tabs.component.ts");
    /* harmony import */


    var _stepper_stepper_component__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(
    /*! ./stepper/stepper.component */
    "./src/app/material-component/stepper/stepper.component.ts");
    /* harmony import */


    var _expansion_expansion_component__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(
    /*! ./expansion/expansion.component */
    "./src/app/material-component/expansion/expansion.component.ts");
    /* harmony import */


    var _chips_chips_component__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(
    /*! ./chips/chips.component */
    "./src/app/material-component/chips/chips.component.ts");
    /* harmony import */


    var _toolbar_toolbar_component__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(
    /*! ./toolbar/toolbar.component */
    "./src/app/material-component/toolbar/toolbar.component.ts");
    /* harmony import */


    var _progress_snipper_progress_snipper_component__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(
    /*! ./progress-snipper/progress-snipper.component */
    "./src/app/material-component/progress-snipper/progress-snipper.component.ts");
    /* harmony import */


    var _progress_progress_component__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(
    /*! ./progress/progress.component */
    "./src/app/material-component/progress/progress.component.ts");
    /* harmony import */


    var _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(
    /*! ./dialog/dialog.component */
    "./src/app/material-component/dialog/dialog.component.ts");
    /* harmony import */


    var _tooltip_tooltip_component__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(
    /*! ./tooltip/tooltip.component */
    "./src/app/material-component/tooltip/tooltip.component.ts");
    /* harmony import */


    var _snackbar_snackbar_component__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(
    /*! ./snackbar/snackbar.component */
    "./src/app/material-component/snackbar/snackbar.component.ts");
    /* harmony import */


    var _slider_slider_component__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(
    /*! ./slider/slider.component */
    "./src/app/material-component/slider/slider.component.ts");
    /* harmony import */


    var _slide_toggle_slide_toggle_component__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(
    /*! ./slide-toggle/slide-toggle.component */
    "./src/app/material-component/slide-toggle/slide-toggle.component.ts");

    var MaterialComponentsModule = function MaterialComponentsModule() {
      _classCallCheck(this, MaterialComponentsModule);
    };

    MaterialComponentsModule.??mod = _angular_core__WEBPACK_IMPORTED_MODULE_1__["????defineNgModule"]({
      type: MaterialComponentsModule
    });
    MaterialComponentsModule.??inj = _angular_core__WEBPACK_IMPORTED_MODULE_1__["????defineInjector"]({
      factory: function MaterialComponentsModule_Factory(t) {
        return new (t || MaterialComponentsModule)();
      },
      providers: [],
      imports: [[_angular_common__WEBPACK_IMPORTED_MODULE_4__["CommonModule"], _angular_router__WEBPACK_IMPORTED_MODULE_2__["RouterModule"].forChild(_material_routing__WEBPACK_IMPORTED_MODULE_9__["MaterialRoutes"]), _demo_material_module__WEBPACK_IMPORTED_MODULE_5__["DemoMaterialModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_3__["HttpClientModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["FormsModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["ReactiveFormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_6__["CdkTableModule"]]]
    });

    (function () {
      (typeof ngJitMode === "undefined" || ngJitMode) && _angular_core__WEBPACK_IMPORTED_MODULE_1__["????setNgModuleScope"](MaterialComponentsModule, {
        declarations: [_buttons_buttons_component__WEBPACK_IMPORTED_MODULE_10__["ButtonsComponent"], _grid_grid_component__WEBPACK_IMPORTED_MODULE_11__["GridComponent"], _lists_lists_component__WEBPACK_IMPORTED_MODULE_12__["ListsComponent"], _menu_menu_component__WEBPACK_IMPORTED_MODULE_13__["MenuComponent"], _tabs_tabs_component__WEBPACK_IMPORTED_MODULE_14__["TabsComponent"], _stepper_stepper_component__WEBPACK_IMPORTED_MODULE_15__["StepperComponent"], _expansion_expansion_component__WEBPACK_IMPORTED_MODULE_16__["ExpansionComponent"], _chips_chips_component__WEBPACK_IMPORTED_MODULE_17__["ChipsComponent"], _toolbar_toolbar_component__WEBPACK_IMPORTED_MODULE_18__["ToolbarComponent"], _progress_snipper_progress_snipper_component__WEBPACK_IMPORTED_MODULE_19__["ProgressSnipperComponent"], _progress_progress_component__WEBPACK_IMPORTED_MODULE_20__["ProgressComponent"], _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__["DialogComponent"], _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__["DialogOverviewExampleDialogComponent"], _tooltip_tooltip_component__WEBPACK_IMPORTED_MODULE_22__["TooltipComponent"], _snackbar_snackbar_component__WEBPACK_IMPORTED_MODULE_23__["SnackbarComponent"], _slider_slider_component__WEBPACK_IMPORTED_MODULE_24__["SliderComponent"], _slide_toggle_slide_toggle_component__WEBPACK_IMPORTED_MODULE_25__["SlideToggleComponent"]],
        imports: [_angular_common__WEBPACK_IMPORTED_MODULE_4__["CommonModule"], _angular_router__WEBPACK_IMPORTED_MODULE_2__["RouterModule"], _demo_material_module__WEBPACK_IMPORTED_MODULE_5__["DemoMaterialModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_3__["HttpClientModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["FormsModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["ReactiveFormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_6__["CdkTableModule"]]
      });
    })();
    /*@__PURE__*/


    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_1__["??setClassMetadata"](MaterialComponentsModule, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_1__["NgModule"],
        args: [{
          imports: [_angular_common__WEBPACK_IMPORTED_MODULE_4__["CommonModule"], _angular_router__WEBPACK_IMPORTED_MODULE_2__["RouterModule"].forChild(_material_routing__WEBPACK_IMPORTED_MODULE_9__["MaterialRoutes"]), _demo_material_module__WEBPACK_IMPORTED_MODULE_5__["DemoMaterialModule"], _angular_common_http__WEBPACK_IMPORTED_MODULE_3__["HttpClientModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["FormsModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_7__["ReactiveFormsModule"], _angular_flex_layout__WEBPACK_IMPORTED_MODULE_8__["FlexLayoutModule"], _angular_cdk_table__WEBPACK_IMPORTED_MODULE_6__["CdkTableModule"]],
          providers: [],
          entryComponents: [_dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__["DialogOverviewExampleDialogComponent"]],
          declarations: [_buttons_buttons_component__WEBPACK_IMPORTED_MODULE_10__["ButtonsComponent"], _grid_grid_component__WEBPACK_IMPORTED_MODULE_11__["GridComponent"], _lists_lists_component__WEBPACK_IMPORTED_MODULE_12__["ListsComponent"], _menu_menu_component__WEBPACK_IMPORTED_MODULE_13__["MenuComponent"], _tabs_tabs_component__WEBPACK_IMPORTED_MODULE_14__["TabsComponent"], _stepper_stepper_component__WEBPACK_IMPORTED_MODULE_15__["StepperComponent"], _expansion_expansion_component__WEBPACK_IMPORTED_MODULE_16__["ExpansionComponent"], _chips_chips_component__WEBPACK_IMPORTED_MODULE_17__["ChipsComponent"], _toolbar_toolbar_component__WEBPACK_IMPORTED_MODULE_18__["ToolbarComponent"], _progress_snipper_progress_snipper_component__WEBPACK_IMPORTED_MODULE_19__["ProgressSnipperComponent"], _progress_progress_component__WEBPACK_IMPORTED_MODULE_20__["ProgressComponent"], _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__["DialogComponent"], _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_21__["DialogOverviewExampleDialogComponent"], _tooltip_tooltip_component__WEBPACK_IMPORTED_MODULE_22__["TooltipComponent"], _snackbar_snackbar_component__WEBPACK_IMPORTED_MODULE_23__["SnackbarComponent"], _slider_slider_component__WEBPACK_IMPORTED_MODULE_24__["SliderComponent"], _slide_toggle_slide_toggle_component__WEBPACK_IMPORTED_MODULE_25__["SlideToggleComponent"]]
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/material.routing.ts":
  /*!********************************************************!*\
    !*** ./src/app/material-component/material.routing.ts ***!
    \********************************************************/

  /*! exports provided: MaterialRoutes */

  /***/
  function srcAppMaterialComponentMaterialRoutingTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "MaterialRoutes", function () {
      return MaterialRoutes;
    });
    /* harmony import */


    var _buttons_buttons_component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! ./buttons/buttons.component */
    "./src/app/material-component/buttons/buttons.component.ts");
    /* harmony import */


    var _grid_grid_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! ./grid/grid.component */
    "./src/app/material-component/grid/grid.component.ts");
    /* harmony import */


    var _lists_lists_component__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! ./lists/lists.component */
    "./src/app/material-component/lists/lists.component.ts");
    /* harmony import */


    var _menu_menu_component__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! ./menu/menu.component */
    "./src/app/material-component/menu/menu.component.ts");
    /* harmony import */


    var _tabs_tabs_component__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! ./tabs/tabs.component */
    "./src/app/material-component/tabs/tabs.component.ts");
    /* harmony import */


    var _stepper_stepper_component__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(
    /*! ./stepper/stepper.component */
    "./src/app/material-component/stepper/stepper.component.ts");
    /* harmony import */


    var _expansion_expansion_component__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(
    /*! ./expansion/expansion.component */
    "./src/app/material-component/expansion/expansion.component.ts");
    /* harmony import */


    var _chips_chips_component__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(
    /*! ./chips/chips.component */
    "./src/app/material-component/chips/chips.component.ts");
    /* harmony import */


    var _toolbar_toolbar_component__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(
    /*! ./toolbar/toolbar.component */
    "./src/app/material-component/toolbar/toolbar.component.ts");
    /* harmony import */


    var _progress_snipper_progress_snipper_component__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(
    /*! ./progress-snipper/progress-snipper.component */
    "./src/app/material-component/progress-snipper/progress-snipper.component.ts");
    /* harmony import */


    var _progress_progress_component__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(
    /*! ./progress/progress.component */
    "./src/app/material-component/progress/progress.component.ts");
    /* harmony import */


    var _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(
    /*! ./dialog/dialog.component */
    "./src/app/material-component/dialog/dialog.component.ts");
    /* harmony import */


    var _tooltip_tooltip_component__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(
    /*! ./tooltip/tooltip.component */
    "./src/app/material-component/tooltip/tooltip.component.ts");
    /* harmony import */


    var _snackbar_snackbar_component__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(
    /*! ./snackbar/snackbar.component */
    "./src/app/material-component/snackbar/snackbar.component.ts");
    /* harmony import */


    var _slider_slider_component__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(
    /*! ./slider/slider.component */
    "./src/app/material-component/slider/slider.component.ts");
    /* harmony import */


    var _slide_toggle_slide_toggle_component__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(
    /*! ./slide-toggle/slide-toggle.component */
    "./src/app/material-component/slide-toggle/slide-toggle.component.ts");

    var MaterialRoutes = [{
      path: 'button',
      component: _buttons_buttons_component__WEBPACK_IMPORTED_MODULE_0__["ButtonsComponent"]
    }, {
      path: 'grid',
      component: _grid_grid_component__WEBPACK_IMPORTED_MODULE_1__["GridComponent"]
    }, {
      path: 'lists',
      component: _lists_lists_component__WEBPACK_IMPORTED_MODULE_2__["ListsComponent"]
    }, {
      path: 'menu',
      component: _menu_menu_component__WEBPACK_IMPORTED_MODULE_3__["MenuComponent"]
    }, {
      path: 'tabs',
      component: _tabs_tabs_component__WEBPACK_IMPORTED_MODULE_4__["TabsComponent"]
    }, {
      path: 'stepper',
      component: _stepper_stepper_component__WEBPACK_IMPORTED_MODULE_5__["StepperComponent"]
    }, {
      path: 'expansion',
      component: _expansion_expansion_component__WEBPACK_IMPORTED_MODULE_6__["ExpansionComponent"]
    }, {
      path: 'chips',
      component: _chips_chips_component__WEBPACK_IMPORTED_MODULE_7__["ChipsComponent"]
    }, {
      path: 'toolbar',
      component: _toolbar_toolbar_component__WEBPACK_IMPORTED_MODULE_8__["ToolbarComponent"]
    }, {
      path: 'progress-snipper',
      component: _progress_snipper_progress_snipper_component__WEBPACK_IMPORTED_MODULE_9__["ProgressSnipperComponent"]
    }, {
      path: 'progress',
      component: _progress_progress_component__WEBPACK_IMPORTED_MODULE_10__["ProgressComponent"]
    }, {
      path: 'dialog',
      component: _dialog_dialog_component__WEBPACK_IMPORTED_MODULE_11__["DialogComponent"]
    }, {
      path: 'tooltip',
      component: _tooltip_tooltip_component__WEBPACK_IMPORTED_MODULE_12__["TooltipComponent"]
    }, {
      path: 'snackbar',
      component: _snackbar_snackbar_component__WEBPACK_IMPORTED_MODULE_13__["SnackbarComponent"]
    }, {
      path: 'slider',
      component: _slider_slider_component__WEBPACK_IMPORTED_MODULE_14__["SliderComponent"]
    }, {
      path: 'slide-toggle',
      component: _slide_toggle_slide_toggle_component__WEBPACK_IMPORTED_MODULE_15__["SlideToggleComponent"]
    }];
    /***/
  },

  /***/
  "./src/app/material-component/menu/menu.component.ts":
  /*!***********************************************************!*\
    !*** ./src/app/material-component/menu/menu.component.ts ***!
    \***********************************************************/

  /*! exports provided: MenuComponent */

  /***/
  function srcAppMaterialComponentMenuMenuComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "MenuComponent", function () {
      return MenuComponent;
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

    var MenuComponent = function MenuComponent() {
      _classCallCheck(this, MenuComponent);
    };

    MenuComponent.??fac = function MenuComponent_Factory(t) {
      return new (t || MenuComponent)();
    };

    MenuComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: MenuComponent,
      selectors: [["app-menu"]],
      decls: 205,
      vars: 14,
      consts: [["fxLayout", "row", "fxLayoutWrap", "wrap"], ["fxFlex.gt-sm", "100%", "fxFlex", "100"], ["mat-button", "", 3, "matMenuTriggerFor"], ["menu", "matMenu"], ["mat-menu-item", ""], ["mat-icon-button", "", 3, "matMenuTriggerFor"], ["menu2", "matMenu"], ["mat-raised-button", "", "color", "accent", 3, "matMenuTriggerFor"], ["animals", "matMenu"], ["mat-menu-item", "", 3, "matMenuTriggerFor"], ["vertebrates", "matMenu"], ["invertebrates", "matMenu"], ["fish", "matMenu"], ["amphibians", "matMenu"], ["reptiles", "matMenu"], ["mat-menu-item", "", "disabled", ""], ["menu4", "matMenu"], [1, "bg-success", "text-white", "rounded", "font-12", "pl-5", "pr-5"], ["yPosition", "above"], ["appMenu", "matMenu"], ["aboveMenu", "matMenu"], ["yPosition", "below"], ["belowMenu", "matMenu"], ["xPosition", "before"], ["beforeMenu", "matMenu"], ["xPosition", "after"], ["afterMenu", "matMenu"]],
      template: function MenuComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-menu>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " is a floating panel containing list of options.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "button", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](11, "Menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-menu", null, 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](21, "On icon menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](24, "<mat-menu>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25, " is a floating panel containing list of options.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](28, "menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "mat-menu", null, 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](32, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, "Nested menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, "<mat-menu>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](42, " is a floating panel containing list of options.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](43, "button", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](44, "Animal index");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "mat-menu", null, 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](47, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](48, "Vertebrates");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](49, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](50, "Invertebrates");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "mat-menu", null, 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](53, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](54, "Fishes");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](55, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](56, "Amphibians");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](57, "button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](58, "Reptiles");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](59, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](60, "Birds");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](62, "Mammals");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](63, "mat-menu", null, 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](65, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](66, "Insects");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](68, "Molluscs");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](70, "Crustaceans");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](72, "Corals");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](73, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](74, "Arachnids");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](75, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](76, "Velvet worms");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](77, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](78, "Horseshoe crabs");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](79, "mat-menu", null, 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](81, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](82, "Baikal oilfish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](83, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](84, "Bala shark");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](85, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](86, "Ballan wrasse");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](87, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](88, "Bamboo shark");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](89, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](90, "Banded killifish");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](91, "mat-menu", null, 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](93, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](94, "Sonoran desert toad");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](95, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](96, "Western toad");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](97, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](98, "Arroyo toad");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](99, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](100, "Yosemite toad");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](101, "mat-menu", null, 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](103, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](104, "Banded Day Gecko");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](105, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](106, "Banded Gila Monster");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](107, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](108, "Black Tree Monitor");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](109, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](110, "Blue Spiny Lizard");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](111, "button", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](112, "Velociraptor");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](113, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](114, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](115, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](116, "With icon menu");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](117, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](118, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](119, "<mat-menu>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](120, " is a floating panel containing list of options.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](121, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](122, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](123, "more_vert");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](124, "mat-menu", null, 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](126, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](127, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](128, "dialpad");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](129, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](130, "Redial");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](131, "button", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](132, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](133, "voicemail");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](134, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](135, "Check voicemail");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](136, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](137, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](138, "notifications_off");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](139, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](140, "Disable alerts");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](141, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](142, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](143, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](144, "Customizing menu position ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](145, "span", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](146, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](147, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](148, "By default, the menu will display below (y-axis), after (x-axis), without overlapping its trigger. The position can be changed using the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](149, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](150, "xPosition (before | after)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](151, " and ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](152, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](153, "yPosition (above | below)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](154, " attributes. The menu can be forced to overlap the trigger using the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](155, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](156, "overlapTrigger");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](157, " attribute.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](158, "mat-menu", 18, 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](160, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](161, "Settings");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](162, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](163, "Help");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](164, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](165, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](166, "more_vert");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](167, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](168, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](169, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](170, "Menu positioning ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](171, "span", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](172, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](173, "button", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](174, "Above");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](175, "mat-menu", 18, 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](177, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](178, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](179, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](180, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](181, "button", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](182, "Below");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](183, "mat-menu", 21, 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](185, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](186, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](187, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](188, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](189, "button", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](190, "Before");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](191, "mat-menu", 23, 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](193, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](194, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](195, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](196, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](197, "button", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](198, "After");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](199, "mat-menu", 25, 26);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](201, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](202, "Item 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](203, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](204, "Item 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r15 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](13);

          var _r16 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](30);

          var _r17 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](46);

          var _r18 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](52);

          var _r19 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](64);

          var _r20 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](80);

          var _r21 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](92);

          var _r22 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](102);

          var _r23 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](125);

          var _r24 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](159);

          var _r25 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](176);

          var _r26 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](184);

          var _r27 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](192);

          var _r28 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](200);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](64);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](43);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r26);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matMenuTriggerFor", _r28);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatMenuTrigger"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["_MatMenu"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatMenuItem"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatIcon"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9tZW51L21lbnUuY29tcG9uZW50LnNjc3MifQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](MenuComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-menu',
          templateUrl: './menu.component.html',
          styleUrls: ['./menu.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/progress-snipper/progress-snipper.component.ts":
  /*!***********************************************************************************!*\
    !*** ./src/app/material-component/progress-snipper/progress-snipper.component.ts ***!
    \***********************************************************************************/

  /*! exports provided: ProgressSnipperComponent */

  /***/
  function srcAppMaterialComponentProgressSnipperProgressSnipperComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ProgressSnipperComponent", function () {
      return ProgressSnipperComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function ProgressSnipperComponent_section_35_Template(rf, ctx) {
      if (rf & 1) {
        var _r74 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "section", 8);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "label", 3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2, "Progress:");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-slider", 14);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressSnipperComponent_section_35_Template_mat_slider_ngModelChange_3_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r74);

          var ctx_r73 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

          return ctx_r73.value = $event;
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var ctx_r72 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx_r72.value);
      }
    }

    var ProgressSnipperComponent = function ProgressSnipperComponent() {
      _classCallCheck(this, ProgressSnipperComponent);

      this.color = 'warn';
      this.mode = 'determinate';
      this.value = 50;
    };

    ProgressSnipperComponent.??fac = function ProgressSnipperComponent_Factory(t) {
      return new (t || ProgressSnipperComponent)();
    };

    ProgressSnipperComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ProgressSnipperComponent,
      selectors: [["app-snipper"]],
      decls: 39,
      vars: 6,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], [1, "example-section", "m-t-20"], [1, "example-margin"], [1, "m-l-20", 3, "ngModel", "ngModelChange"], ["value", "primary", 1, "m-r-10"], ["value", "accent", 1, "m-r-10"], ["value", "warn", 1, "example-margin"], [1, "example-section"], ["value", "determinate", 1, "m-r-10"], ["value", "indeterminate", 1, "example-margin"], ["class", "example-section", 4, "ngIf"], [1, "example-h2"], [1, "example-margin", 3, "color", "mode", "value"], [1, "example-margin", 3, "ngModel", "ngModelChange"]],
      template: function ProgressSnipperComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic Progress spinner");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-progress-spinner>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " are a circular indicators of progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](10, "mat-spinner");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](14, "Configurable progress spinner");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "section", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "label", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Color:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "mat-radio-group", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressSnipperComponent_Template_mat_radio_group_ngModelChange_18_listener($event) {
            return ctx.color = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "mat-radio-button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](20, " Primary ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "mat-radio-button", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](22, " Accent ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "mat-radio-button", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](24, " Warn ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](25, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "section", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "label", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](28, "Mode:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "mat-radio-group", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressSnipperComponent_Template_mat_radio_group_ngModelChange_29_listener($event) {
            return ctx.mode = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "mat-radio-button", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, " Determinate ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-radio-button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](33, " Indeterminate ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](34, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](35, ProgressSnipperComponent_section_35_Template, 4, 1, "section", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "h4", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Result");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](38, "mat-progress-spinner", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.color);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.mode);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngIf", ctx.mode == "determinate");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("color", ctx.color)("mode", ctx.mode)("value", ctx.value);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSpinner"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgModel"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioButton"], _angular_common__WEBPACK_IMPORTED_MODULE_4__["NgIf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatProgressSpinner"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSlider"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9wcm9ncmVzcy1zbmlwcGVyL3Byb2dyZXNzLXNuaXBwZXIuY29tcG9uZW50LnNjc3MifQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ProgressSnipperComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-snipper',
          templateUrl: './progress-snipper.component.html',
          styleUrls: ['./progress-snipper.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/progress/progress.component.ts":
  /*!*******************************************************************!*\
    !*** ./src/app/material-component/progress/progress.component.ts ***!
    \*******************************************************************/

  /*! exports provided: ProgressComponent */

  /***/
  function srcAppMaterialComponentProgressProgressComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ProgressComponent", function () {
      return ProgressComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_common__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/common */
    "./node_modules/@angular/common/__ivy_ngcc__/fesm2015/common.js");

    function ProgressComponent_section_92_Template(rf, ctx) {
      if (rf & 1) {
        var _r78 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "section", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "label", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2, "Progress:");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-slider", 22);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressComponent_section_92_Template_mat_slider_ngModelChange_3_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r78);

          var ctx_r77 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

          return ctx_r77.value = $event;
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var ctx_r75 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx_r75.value);
      }
    }

    function ProgressComponent_section_93_Template(rf, ctx) {
      if (rf & 1) {
        var _r80 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "section", 9);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "label", 10);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](2, "Buffer:");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-slider", 22);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressComponent_section_93_Template_mat_slider_ngModelChange_3_listener($event) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r80);

          var ctx_r79 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

          return ctx_r79.bufferValue = $event;
        });

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }

      if (rf & 2) {
        var ctx_r76 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????nextContext"]();

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx_r76.bufferValue);
      }
    }

    var ProgressComponent = function ProgressComponent() {
      _classCallCheck(this, ProgressComponent);

      this.color = 'primary';
      this.mode = 'determinate';
      this.value = 50;
      this.bufferValue = 75;
    };

    ProgressComponent.??fac = function ProgressComponent_Factory(t) {
      return new (t || ProgressComponent)();
    };

    ProgressComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ProgressComponent,
      selectors: [["app-progress"]],
      decls: 98,
      vars: 8,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], ["mode", "determinate", "value", "40"], ["mode", "indeterminate", "value", "40"], ["mode", "buffer"], ["mode", "query"], ["mode", "determinate", "value", "40", "color", "primary"], ["mode", "determinate", "value", "80", "color", "accent"], ["mode", "determinate", "value", "20", "color", "warn"], [1, "example-section"], [1, "example-margin"], [3, "ngModel", "ngModelChange"], ["value", "primary", 1, "example-margin"], ["value", "accent", 1, "example-margin"], ["value", "warn", 1, "example-margin"], ["value", "determinate", 1, "example-margin"], ["value", "indeterminate", 1, "example-margin"], ["value", "buffer", 1, "example-margin"], ["value", "query", 1, "example-margin"], ["class", "example-section", 4, "ngIf"], [1, "example-h2"], [1, "example-margin", 3, "color", "mode", "value", "bufferValue"], [1, "example-margin", 3, "ngModel", "ngModelChange"]],
      template: function ProgressComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Determinate progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-progress-bar mode=\"determinate\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](10, "mat-progress-bar", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](16, "Indeterminate progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "<mat-progress-bar mode=\"indeterminate\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](20, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](21, "mat-progress-bar", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, "Buffer progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](30, "<mat-progress-bar mode=\"buffer\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](32, "mat-progress-bar", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, "Query progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, "<mat-progress-bar mode=\"query\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](42, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](43, "mat-progress-bar", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](47, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "Colored progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](52, "<mat-progress-bar mode=\"determinate\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](54, "mat-progress-bar", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](55, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](56, "mat-progress-bar", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](57, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](58, "mat-progress-bar", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](59, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](60, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](62, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](63, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](64, "Configurable progress-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](65, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](67, "<mat-progress-bar mode=\"query\">");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](68, " is a horizontal progress-bar for indicating progress and activity.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "section", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](70, "label", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](71, "Color:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](72, "mat-radio-group", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressComponent_Template_mat_radio_group_ngModelChange_72_listener($event) {
            return ctx.color = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](73, "mat-radio-button", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](74, " Primary ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](75, "mat-radio-button", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](76, " Accent ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](77, "mat-radio-button", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](78, " Warn ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](79, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](80, "section", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](81, "label", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](82, "Mode:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](83, "mat-radio-group", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function ProgressComponent_Template_mat_radio_group_ngModelChange_83_listener($event) {
            return ctx.mode = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](84, "mat-radio-button", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](85, " Determinate ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](86, "mat-radio-button", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](87, " Indeterminate ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](88, "mat-radio-button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](89, " Buffer ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](90, "mat-radio-button", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](91, " Query ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](92, ProgressComponent_section_92_Template, 4, 1, "section", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](93, ProgressComponent_section_93_Template, 4, 1, "section", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](94, "h2", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](95, "Result");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](96, "section", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](97, "mat-progress-bar", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](72);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.color);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.mode);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngIf", ctx.mode == "determinate" || ctx.mode == "buffer");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngIf", ctx.mode == "buffer");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("color", ctx.color)("mode", ctx.mode)("value", ctx.value)("bufferValue", ctx.bufferValue);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatProgressBar"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgModel"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioButton"], _angular_common__WEBPACK_IMPORTED_MODULE_4__["NgIf"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSlider"]],
      styles: [".example-h2[_ngcontent-%COMP%] {\n  margin: 10px;\n}\n\n.example-section[_ngcontent-%COMP%] {\n  display: flex;\n  align-content: center;\n  align-items: center;\n  height: 60px;\n}\n\n.example-margin[_ngcontent-%COMP%] {\n  margin: 0 10px;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Byb2dyZXNzL0M6XFxib29zdGFwcFxcY3N2L3NyY1xcYXBwXFxtYXRlcmlhbC1jb21wb25lbnRcXHByb2dyZXNzXFxwcm9ncmVzcy5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Byb2dyZXNzL3Byb2dyZXNzLmNvbXBvbmVudC5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQ0UsWUFBWTtBQ0NkOztBREVBO0VBQ0UsYUFBYTtFQUNiLHFCQUFxQjtFQUNyQixtQkFBbUI7RUFDbkIsWUFBWTtBQ0NkOztBREVBO0VBQ0UsY0FBYztBQ0NoQiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9wcm9ncmVzcy9wcm9ncmVzcy5jb21wb25lbnQuc2NzcyIsInNvdXJjZXNDb250ZW50IjpbIi5leGFtcGxlLWgyIHtcclxuICBtYXJnaW46IDEwcHg7XHJcbn1cclxuXHJcbi5leGFtcGxlLXNlY3Rpb24ge1xyXG4gIGRpc3BsYXk6IGZsZXg7XHJcbiAgYWxpZ24tY29udGVudDogY2VudGVyO1xyXG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XHJcbiAgaGVpZ2h0OiA2MHB4O1xyXG59XHJcblxyXG4uZXhhbXBsZS1tYXJnaW4ge1xyXG4gIG1hcmdpbjogMCAxMHB4O1xyXG59IiwiLmV4YW1wbGUtaDIge1xuICBtYXJnaW46IDEwcHg7XG59XG5cbi5leGFtcGxlLXNlY3Rpb24ge1xuICBkaXNwbGF5OiBmbGV4O1xuICBhbGlnbi1jb250ZW50OiBjZW50ZXI7XG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XG4gIGhlaWdodDogNjBweDtcbn1cblxuLmV4YW1wbGUtbWFyZ2luIHtcbiAgbWFyZ2luOiAwIDEwcHg7XG59XG4iXX0= */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ProgressComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-progress',
          templateUrl: './progress.component.html',
          styleUrls: ['./progress.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/slide-toggle/slide-toggle.component.ts":
  /*!***************************************************************************!*\
    !*** ./src/app/material-component/slide-toggle/slide-toggle.component.ts ***!
    \***************************************************************************/

  /*! exports provided: SlideToggleComponent */

  /***/
  function srcAppMaterialComponentSlideToggleSlideToggleComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "SlideToggleComponent", function () {
      return SlideToggleComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");

    var SlideToggleComponent = function SlideToggleComponent() {
      _classCallCheck(this, SlideToggleComponent);

      this.color = 'accent';
      this.checked = false;
      this.disabled = false;
    };

    SlideToggleComponent.??fac = function SlideToggleComponent_Factory(t) {
      return new (t || SlideToggleComponent)();
    };

    SlideToggleComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: SlideToggleComponent,
      selectors: [["app-slide-toggle"]],
      decls: 41,
      vars: 6,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], [1, "example-section"], [1, "example-margin"], [3, "ngModel", "ngModelChange"], ["color", "primary", "value", "primary", 1, "example-margin"], ["color", "accent", "value", "accent", 1, "example-margin"], ["color", "warn", "value", "warn", 1, "example-margin"], [1, "example-margin", 3, "ngModel", "ngModelChange"], [1, "example-h2"], [1, "example-margin", 3, "color", "checked", "disabled"]],
      template: function SlideToggleComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic slide-toggles");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "<mat-slide-toggle>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, " is an on/off control that can be toggled via clicking or dragging.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](10, "mat-slide-toggle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](11, "Slide me!");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](15, "Basic grid-list");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](18, "<mat-slide-toggle>");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, " is an on/off control that can be toggled via clicking or dragging.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "section", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "label", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](22, "Color:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "mat-radio-group", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SlideToggleComponent_Template_mat_radio_group_ngModelChange_23_listener($event) {
            return ctx.color = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-radio-button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](25, " Primary ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "mat-radio-button", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, " Accent ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-radio-button", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](29, " Warn ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "section", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "mat-checkbox", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SlideToggleComponent_Template_mat_checkbox_ngModelChange_31_listener($event) {
            return ctx.checked = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](32, "Checked");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "section", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "mat-checkbox", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SlideToggleComponent_Template_mat_checkbox_ngModelChange_34_listener($event) {
            return ctx.disabled = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](35, "Disabled");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "h2", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Result");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](38, "section", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "mat-slide-toggle", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](40, " Slide me! ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.color);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.checked);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.disabled);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("color", ctx.color)("checked", ctx.checked)("disabled", ctx.disabled);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSlideToggle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgModel"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatRadioButton"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCheckbox"]],
      styles: [".example-h2[_ngcontent-%COMP%] {\n  margin: 10px;\n}\n\n.example-section[_ngcontent-%COMP%] {\n  display: flex;\n  align-content: center;\n  align-items: center;\n  height: 60px;\n}\n\n.example-margin[_ngcontent-%COMP%] {\n  margin: 10px;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3NsaWRlLXRvZ2dsZS9DOlxcYm9vc3RhcHBcXGNzdi9zcmNcXGFwcFxcbWF0ZXJpYWwtY29tcG9uZW50XFxzbGlkZS10b2dnbGVcXHNsaWRlLXRvZ2dsZS5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3NsaWRlLXRvZ2dsZS9zbGlkZS10b2dnbGUuY29tcG9uZW50LnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7RUFDRSxZQUFZO0FDQ2Q7O0FERUE7RUFDRSxhQUFhO0VBQ2IscUJBQXFCO0VBQ3JCLG1CQUFtQjtFQUNuQixZQUFZO0FDQ2Q7O0FERUE7RUFDRSxZQUFZO0FDQ2QiLCJmaWxlIjoic3JjL2FwcC9tYXRlcmlhbC1jb21wb25lbnQvc2xpZGUtdG9nZ2xlL3NsaWRlLXRvZ2dsZS5jb21wb25lbnQuc2NzcyIsInNvdXJjZXNDb250ZW50IjpbIi5leGFtcGxlLWgyIHtcclxuICBtYXJnaW46IDEwcHg7XHJcbn1cclxuXHJcbi5leGFtcGxlLXNlY3Rpb24ge1xyXG4gIGRpc3BsYXk6IGZsZXg7XHJcbiAgYWxpZ24tY29udGVudDogY2VudGVyO1xyXG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XHJcbiAgaGVpZ2h0OiA2MHB4O1xyXG59XHJcblxyXG4uZXhhbXBsZS1tYXJnaW4ge1xyXG4gIG1hcmdpbjogMTBweDtcclxufSIsIi5leGFtcGxlLWgyIHtcbiAgbWFyZ2luOiAxMHB4O1xufVxuXG4uZXhhbXBsZS1zZWN0aW9uIHtcbiAgZGlzcGxheTogZmxleDtcbiAgYWxpZ24tY29udGVudDogY2VudGVyO1xuICBhbGlnbi1pdGVtczogY2VudGVyO1xuICBoZWlnaHQ6IDYwcHg7XG59XG5cbi5leGFtcGxlLW1hcmdpbiB7XG4gIG1hcmdpbjogMTBweDtcbn1cbiJdfQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](SlideToggleComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-slide-toggle',
          templateUrl: './slide-toggle.component.html',
          styleUrls: ['./slide-toggle.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/slider/slider.component.ts":
  /*!***************************************************************!*\
    !*** ./src/app/material-component/slider/slider.component.ts ***!
    \***************************************************************/

  /*! exports provided: SliderComponent */

  /***/
  function srcAppMaterialComponentSliderSliderComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "SliderComponent", function () {
      return SliderComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");

    var SliderComponent = function SliderComponent() {
      _classCallCheck(this, SliderComponent);

      this.val = 50;
      this.min = 0;
      this.max = 100;
    };

    SliderComponent.??fac = function SliderComponent_Factory(t) {
      return new (t || SliderComponent)();
    };

    SliderComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: SliderComponent,
      selectors: [["app-slider"]],
      decls: 64,
      vars: 12,
      consts: [[1, ""], ["href", "https://material.angular.io/components/slider/overview"], [1, "m-b-0"], ["color", "warn", "value", "40"], ["color", "primary", "value", "40"], ["slidey", ""], ["matInput", "", 3, "ngModel", "ngModelChange"], ["tickInterval", "5", "color", "warn", 3, "min", "max"], ["slider2", ""], ["disabled", ""], ["slider3", ""], ["vertical", "", "value", "50"], ["min", "1", "max", "100", "step", "20"], ["slider5", ""], ["tickInterval", "auto"], ["tickInterval", "9"], ["thumbLabel", ""], ["step", "40", 3, "value"], ["step", "40", 3, "ngModel", "ngModelChange"], ["invert", "", "value", "50", "tickInterval", "5"], ["vertical", "", "invert", "", "thumbLabel", "", "tickInterval", "auto", "value", "50"]],
      template: function SliderComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "mat-slider allows for the selection of a value from a range via mouse, touch, or keyboard, similar to ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "code", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "a", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, "Basic Slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](11, "mat-slider", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](13, "value Slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](14, " Label ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](15, "mat-slider", 4, 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](19, "With Min and Max");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "input", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SliderComponent_Template_input_ngModelChange_21_listener($event) {
            return ctx.min = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "input", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SliderComponent_Template_input_ngModelChange_23_listener($event) {
            return ctx.max = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](24, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](25, "mat-slider", 7, 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](29, "Disabled Slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](30, "mat-slider", 9, 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, "Vertical slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](35, "mat-slider", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Selecting a value");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](38, "mat-slider", 12, 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](40);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](41, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](42, "Slider with set tick interval");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](43, "mat-slider", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](44, "mat-slider", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](46, "Slider with Thumb Label");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](47, "mat-slider", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "Slider with one-way binding");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](50, "mat-slider", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "input", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SliderComponent_Template_input_ngModelChange_52_listener($event) {
            return ctx.val = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](53, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](54, "Slider with two-way binding");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](55, "mat-slider", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SliderComponent_Template_mat_slider_ngModelChange_55_listener($event) {
            return ctx.demo = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](56, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](57, "input", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function SliderComponent_Template_input_ngModelChange_57_listener($event) {
            return ctx.demo = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](59, "Inverted slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](60, "mat-slider", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "h4", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](62, "Inverted vertical slider");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](63, "mat-slider", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          var _r85 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](16);

          var _r86 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](26);

          var _r87 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](31);

          var _r88 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](39);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _r85.value, " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.min);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.max);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("min", ctx.min)("max", ctx.max);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _r86.value, " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _r87.value, " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", _r88.value, " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("value", ctx.val);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.val);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.demo);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.demo);
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSlider"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInput"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["DefaultValueAccessor"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_2__["NgModel"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9zbGlkZXIvc2xpZGVyLmNvbXBvbmVudC5zY3NzIn0= */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](SliderComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-slider',
          templateUrl: './slider.component.html',
          styleUrls: ['./slider.component.scss']
        }]
      }], function () {
        return [];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/snackbar/snackbar.component.ts":
  /*!*******************************************************************!*\
    !*** ./src/app/material-component/snackbar/snackbar.component.ts ***!
    \*******************************************************************/

  /*! exports provided: SnackbarComponent */

  /***/
  function srcAppMaterialComponentSnackbarSnackbarComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "SnackbarComponent", function () {
      return SnackbarComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");

    var SnackbarComponent =
    /*#__PURE__*/
    function () {
      function SnackbarComponent(snackBar) {
        _classCallCheck(this, SnackbarComponent);

        this.snackBar = snackBar;
      }

      _createClass(SnackbarComponent, [{
        key: "openSnackBar",
        value: function openSnackBar(message, action) {
          this.snackBar.open(message, action, {
            duration: 2000
          });
        }
      }]);

      return SnackbarComponent;
    }();

    SnackbarComponent.??fac = function SnackbarComponent_Factory(t) {
      return new (t || SnackbarComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["????directiveInject"](_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSnackBar"]));
    };

    SnackbarComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: SnackbarComponent,
      selectors: [["app-snackbar"]],
      decls: 17,
      vars: 0,
      consts: [[1, ""], ["href", "https://material.angular.io/components/snack-bar/overview"], ["matInput", "", "value", "Disco party!", "placeholder", "Message"], ["message", ""], ["matInput", "", "value", "Dance", "placeholder", "Action"], ["action", ""], ["mat-raised-button", "", "color", "warn", 3, "click"]],
      template: function SnackbarComponent_Template(rf, ctx) {
        if (rf & 1) {
          var _r84 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Basic snack-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "matSnackBar is a service for displaying snack-bar notifications. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "code", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "a", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](10, "input", 2, 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](13, "input", 4, 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "button", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function SnackbarComponent_Template_button_click_15_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r84);

            var _r82 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](11);

            var _r83 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](14);

            return ctx.openSnackBar(_r82.value, _r83.value);
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](16, "Show snack-bar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatInput"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9zbmFja2Jhci9zbmFja2Jhci5jb21wb25lbnQuc2NzcyJ9 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](SnackbarComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-snackbar',
          templateUrl: './snackbar.component.html',
          styleUrls: ['./snackbar.component.scss']
        }]
      }], function () {
        return [{
          type: _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatSnackBar"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/stepper/stepper.component.ts":
  /*!*****************************************************************!*\
    !*** ./src/app/material-component/stepper/stepper.component.ts ***!
    \*****************************************************************/

  /*! exports provided: StepperComponent */

  /***/
  function srcAppMaterialComponentStepperStepperComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "StepperComponent", function () {
      return StepperComponent;
    });
    /* harmony import */


    var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(
    /*! @angular/core */
    "./node_modules/@angular/core/__ivy_ngcc__/fesm2015/core.js");
    /* harmony import */


    var _angular_forms__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_cdk_stepper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/cdk/stepper */
    "./node_modules/@angular/cdk/__ivy_ngcc__/esm2015/stepper.js");
    /* harmony import */


    var _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/flex-layout/flex */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex.js");
    /* harmony import */


    var _angular_material__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/material */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/material.js");

    function StepperComponent_ng_template_16_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_24_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_33_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_60_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_68_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_77_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_101_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_109_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_118_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_147_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_155_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_164_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_193_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_201_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_210_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_229_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_237_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_246_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    function StepperComponent_ng_template_271_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-icon");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1, "call_end");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }
    }

    function StepperComponent_ng_template_272_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-icon");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](1, "forum");

        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
      }
    }

    function StepperComponent_ng_template_293_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your name");
      }
    }

    function StepperComponent_ng_template_301_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Fill out your address");
      }
    }

    function StepperComponent_ng_template_310_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, "Done");
      }
    }

    var StepperComponent =
    /*#__PURE__*/
    function () {
      function StepperComponent(_formBuilder) {
        _classCallCheck(this, StepperComponent);

        this._formBuilder = _formBuilder;
        this.isLinear = false;
        this.isLinearvarient = false;
        this.isLinearposition = false;
        this.isOptional = false;
        this.isEditable = false;
      }

      _createClass(StepperComponent, [{
        key: "ngOnInit",
        value: function ngOnInit() {
          this.firstFormGroup = this._formBuilder.group({
            firstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.secondFormGroup = this._formBuilder.group({
            secondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // varient

          this.varientfirstFormGroup = this._formBuilder.group({
            varientfirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.varientsecondFormGroup = this._formBuilder.group({
            varientsecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // position

          this.positionfirstFormGroup = this._formBuilder.group({
            positionfirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.positionsecondFormGroup = this._formBuilder.group({
            positionsecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // optional

          this.optionalfirstFormGroup = this._formBuilder.group({
            optionalfirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.optionalsecondFormGroup = this._formBuilder.group({
            optionalsecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // editable

          this.editablefirstFormGroup = this._formBuilder.group({
            editablefirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.editablesecondFormGroup = this._formBuilder.group({
            editablesecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // customize

          this.customizefirstFormGroup = this._formBuilder.group({
            customizefirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.customizesecondFormGroup = this._formBuilder.group({
            customizesecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          }); // error

          this.errorfirstFormGroup = this._formBuilder.group({
            errorfirstCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
          this.errorsecondFormGroup = this._formBuilder.group({
            errorsecondCtrl: ['', _angular_forms__WEBPACK_IMPORTED_MODULE_1__["Validators"].required]
          });
        }
      }]);

      return StepperComponent;
    }();

    StepperComponent.??fac = function StepperComponent_Factory(t) {
      return new (t || StepperComponent)(_angular_core__WEBPACK_IMPORTED_MODULE_0__["????directiveInject"](_angular_forms__WEBPACK_IMPORTED_MODULE_1__["FormBuilder"]));
    };

    StepperComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: StepperComponent,
      selectors: [["app-stepper"]],
      features: [_angular_core__WEBPACK_IMPORTED_MODULE_0__["????ProvidersFeature"]([{
        provide: _angular_cdk_stepper__WEBPACK_IMPORTED_MODULE_2__["STEPPER_GLOBAL_OPTIONS"],
        useValue: {
          displayDefaultIndicatorType: false
        }
      }])],
      decls: 317,
      vars: 35,
      consts: [["fxLayout", "row", "fxLayoutWrap", "wrap"], ["fxFlex.gt-sm", "100%", "fxFlex", "100"], [1, ""], ["href", "https://material.angular.io/components/stepper/overview"], ["mat-raised-button", "", "id", "toggle-linear", 3, "click"], [3, "linear"], [3, "stepControl"], [3, "formGroup"], ["matStepLabel", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "firstCtrl", "required", ""], ["mat-raised-button", "", "color", "warn", "matStepperNext", ""], ["matInput", "", "placeholder", "Address", "formControlName", "secondCtrl", "required", ""], ["mat-raised-button", "", "color", "accent", "matStepperPrevious", ""], [1, "bg-success", "text-white", "rounded", "font-12", "pl-5", "pr-5"], [1, "m-t-20", 3, "linear"], ["steppervarient", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "varientfirstCtrl", "required", ""], ["mat-raised-button", "", "matStepperNext", "", "color", "accent"], ["matInput", "", "placeholder", "Address", "formControlName", "varientsecondCtrl", "required", ""], [1, "button-row"], ["mat-raised-button", "", "color", "primary", "matStepperPrevious", ""], ["mat-raised-button", "", "color", "accent", "matStepperNext", ""], ["mat-raised-button", "", "color", "warn", 3, "click"], ["labelPosition", "bottom"], ["stepperposition", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "positionfirstCtrl", "required", ""], ["optional", "", 3, "stepControl"], ["matInput", "", "placeholder", "Address", "formControlName", "positionsecondCtrl", "required", ""], [1, "button-row", "m-t-10"], ["mat-raised-button", "", 3, "click"], ["linear", ""], ["stepperoptional", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "optionalfirstCtrl", "required", ""], [3, "stepControl", "optional"], ["matInput", "", "placeholder", "Address", "formControlName", "optionalsecondCtrl", "required", ""], [1, "button-row", "m-t-20"], ["steppereditable", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "editablefirstCtrl", "required", ""], [3, "stepControl", "editable"], ["matInput", "", "placeholder", "Address", "formControlName", "editablesecondCtrl", "required", ""], ["steppercustomize", ""], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "customizefirstCtrl", "required", ""], ["matInput", "", "placeholder", "Address", "formControlName", "customizesecondCtrl", "required", ""], [1, "m-t-20"], ["label", "Step 1", "state", "phone"], ["label", "Step 2", "state", "chat"], ["label", "Step 3"], ["matStepperIcon", "phone"], ["matStepperIcon", "chat"], ["steppererror", ""], ["errorMessage", "Name is required.", 3, "stepControl"], ["matInput", "", "placeholder", "Last name, First name", "formControlName", "errorfirstCtrl", "required", ""], ["errorMessage", "Address is required.", 3, "stepControl"], ["matInput", "", "placeholder", "Address", "formControlName", "errorsecondCtrl", "required", ""]],
      template: function StepperComponent_Template(rf, ctx) {
        if (rf & 1) {
          var _r59 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????getCurrentView"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Stepper");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "Check the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "code", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "a", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_11_listener($event) {
            return ctx.isLinear = true;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](12, "Enable linear mode");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-horizontal-stepper", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](16, StepperComponent_ng_template_16_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](18, "input", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](21, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](24, StepperComponent_ng_template_24_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](26, "input", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "button", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](29, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "button", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](33, StepperComponent_ng_template_33_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "button", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](37, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](38, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](41, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](43, "Stepper variants ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](45, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](47, "There are two stepper components: ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "mat-horizontal-stepper");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](50, " and ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](52, "mat-vertical-stepper");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, ". They can be used the same way. The only difference is the orientation of stepper.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_54_listener($event) {
            return ctx.isLinearvarient = !ctx.isLinearvarient;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](55);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](56, "mat-vertical-stepper", 14, 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](59, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](60, StepperComponent_ng_template_60_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](62, "input", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](63, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](64, "button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](65, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](68, StepperComponent_ng_template_68_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](70, "input", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](72, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](73, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](74, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](75, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](76, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](77, StepperComponent_ng_template_77_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](78, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](79, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](80, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](81, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](82, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_82_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r33 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](57);

            return _r33.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](83, "Reset");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](84, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](85, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](86, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](87, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](88, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](89, "Stepper Label ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](90, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](91, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](92, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](93, "If a step's label is only text, then the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](94, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](95, "label");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](96, " attribute can be used.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](97, "mat-horizontal-stepper", 23, 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](99, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](100, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](101, StepperComponent_ng_template_101_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](102, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](103, "input", 25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](104, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](105, "button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](106, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](107, "mat-step", 26);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](108, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](109, StepperComponent_ng_template_109_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](110, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](111, "input", 27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](112, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](113, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](114, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](115, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](116, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](117, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](118, StepperComponent_ng_template_118_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](119, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](120, "div", 28);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](121, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](122, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](123, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_123_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r37 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](98);

            return _r37.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](124, "Reset");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](125, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](126, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](127, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](128, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](129, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](130, "Stepper with optional steps ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](131, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](132, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](133, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](134, "If completion of a step in linear stepper is not required, then the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](135, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](136, "optional");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](137, " attribute can be set on ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](138, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](139, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](140, ".");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](141, "button", 29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_141_listener($event) {
            return ctx.isOptional = !ctx.isOptional;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](142);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](143, "mat-horizontal-stepper", 30, 31);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](145, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](146, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](147, StepperComponent_ng_template_147_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](148, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](149, "input", 32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](150, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](151, "button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](152, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](153, "mat-step", 33);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](154, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](155, StepperComponent_ng_template_155_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](156, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](157, "input", 34);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](158, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](159, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](160, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](161, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](162, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](163, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](164, StepperComponent_ng_template_164_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](165, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](166, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](167, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](168, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](169, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_169_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r41 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](144);

            return _r41.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](170, "Reset");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](171, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](172, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](173, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](174, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](175, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](176, "Stepper with editable steps ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](177, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](178, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](179, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](180, "By default, steps are editable, which means users can return to previously completed steps and edit their responses. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](181, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](182, "editable=\"false\"");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](183, " can be set on ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](184, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](185, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](186, " to change the default.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](187, "button", 29);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_187_listener($event) {
            return ctx.isEditable = !ctx.isEditable;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](188);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](189, "mat-horizontal-stepper", 30, 36);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](191, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](192, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](193, StepperComponent_ng_template_193_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](194, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](195, "input", 37);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](196, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](197, "button", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](198, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](199, "mat-step", 38);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](200, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](201, StepperComponent_ng_template_201_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](202, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](203, "input", 39);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](204, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](205, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](206, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](207, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](208, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](209, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](210, StepperComponent_ng_template_210_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](211, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](212, "div", 35);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](213, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](214, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](215, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_215_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r45 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](190);

            return _r45.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](216, "Reset");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](217, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](218, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](219, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](220, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](221, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](222, "Stepper with customized states ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](223, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](224, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](225, "mat-horizontal-stepper", null, 40);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](227, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](228, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](229, StepperComponent_ng_template_229_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](230, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](231, "input", 41);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](232, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](233, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](234, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](235, "mat-step", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](236, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](237, StepperComponent_ng_template_237_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](238, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](239, "input", 42);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](240, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](241, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](242, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](243, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](244, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](245, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](246, StepperComponent_ng_template_246_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](247, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](248, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](249, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](250, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](251, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_251_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r49 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](226);

            return _r49.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](252, "Reset");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](253, "mat-horizontal-stepper", 43);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](254, "mat-step", 44);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](255, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](256, "Put down your phones.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](257, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](258, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](259, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](260, "mat-step", 45);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](261, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](262, "Socialize with each other.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](263, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](264, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](265, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](266, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](267, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](268, "mat-step", 46);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](269, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](270, "You're welcome.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](271, StepperComponent_ng_template_271_Template, 2, 0, "ng-template", 47);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](272, StepperComponent_ng_template_272_Template, 2, 0, "ng-template", 48);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](273, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](274, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](275, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](276, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](277, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](278, "Error State ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](279, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](280, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](281, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](282, "The stepper can now show error states by simply providing the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](283, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](284, "showError");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](285, " option to the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](286, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](287, "STEPPER_GLOBAL_OPTIONS");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](288, " in your application's root module as mentioned above.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](289, "mat-horizontal-stepper", 30, 49);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](291, "mat-step", 50);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](292, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](293, StepperComponent_ng_template_293_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](294, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](295, "input", 51);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](296, "div");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](297, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](298, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](299, "mat-step", 52);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](300, "form", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](301, StepperComponent_ng_template_301_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](302, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](303, "input", 53);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](304, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](305, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](306, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](307, "button", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](308, "Next");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](309, "mat-step");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](310, StepperComponent_ng_template_310_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](311, " You are now done. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](312, "div", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](313, "button", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](314, "Back");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](315, "button", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("click", function StepperComponent_Template_button_click_315_listener($event) {
            _angular_core__WEBPACK_IMPORTED_MODULE_0__["????restoreView"](_r59);

            var _r55 = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????reference"](290);

            return _r55.reset();
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](316, "Reset");

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
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("linear", ctx.isLinear);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.firstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.firstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.secondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.secondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", !ctx.isLinearvarient ? "Enable linear mode" : "Disable linear mode", " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("linear", ctx.isLinearvarient);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.varientfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.varientfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.varientsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.varientsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](32);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.positionfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.positionfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.positionsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.positionsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](34);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", !ctx.isOptional ? "Enable optional steps" : "Disable optional steps", " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.optionalfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.optionalfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.optionalsecondFormGroup)("optional", ctx.isOptional);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.optionalsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](34);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????textInterpolate1"](" ", !ctx.isEditable ? "Enable edit mode" : "Disable edit mode", " ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.editablefirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.editablefirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.editablesecondFormGroup)("editable", ctx.isEditable);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.editablesecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](27);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.customizefirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.customizefirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.customizesecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.customizesecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](55);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.errorfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.errorfirstFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("stepControl", ctx.errorsecondFormGroup);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("formGroup", ctx.errorsecondFormGroup);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_3__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatHorizontalStepper"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatStep"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["??angular_packages_forms_forms_y"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["NgControlStatusGroup"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["FormGroupDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatStepLabel"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatInput"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["DefaultValueAccessor"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["FormControlName"], _angular_forms__WEBPACK_IMPORTED_MODULE_1__["RequiredValidator"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatStepperNext"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatStepperPrevious"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatVerticalStepper"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatStepperIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_4__["MatIcon"]],
      styles: ["\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC9zdGVwcGVyL3N0ZXBwZXIuY29tcG9uZW50LnNjc3MifQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](StepperComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-stepper',
          templateUrl: './stepper.component.html',
          styleUrls: ['./stepper.component.scss'],
          providers: [{
            provide: _angular_cdk_stepper__WEBPACK_IMPORTED_MODULE_2__["STEPPER_GLOBAL_OPTIONS"],
            useValue: {
              displayDefaultIndicatorType: false
            }
          }]
        }]
      }], function () {
        return [{
          type: _angular_forms__WEBPACK_IMPORTED_MODULE_1__["FormBuilder"]
        }];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/tabs/tabs.component.ts":
  /*!***********************************************************!*\
    !*** ./src/app/material-component/tabs/tabs.component.ts ***!
    \***********************************************************/

  /*! exports provided: TabsComponent */

  /***/
  function srcAppMaterialComponentTabsTabsComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "TabsComponent", function () {
      return TabsComponent;
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

    function TabsComponent_ng_template_29_Template(rf, ctx) {
      if (rf & 1) {
        _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](0, " \u2B50 ");
      }
    }

    var TabsComponent = function TabsComponent() {
      _classCallCheck(this, TabsComponent);
    };

    TabsComponent.??fac = function TabsComponent_Factory(t) {
      return new (t || TabsComponent)();
    };

    TabsComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: TabsComponent,
      selectors: [["app-tabs"]],
      decls: 119,
      vars: 0,
      consts: [["fxLayout", "row", "fxLayoutWrap", "wrap"], ["fxFlex.gt-sm", "100%", "fxFlex", "100"], [1, ""], ["href", "https://material.angular.io/components/tabs/overview"], ["label", "Tab 1"], ["label", "Tab 2"], [1, "demo-tab-group"], [1, "demo-tab-content"], ["mat-tab-label", ""], ["label", "Tab 3", "disabled", ""], ["label", "Tab 4"], ["label", "Tab 5"], ["label", "Tab 6"], [1, "bg-success", "text-white", "rounded", "font-12", "pl-5", "pr-5"], ["mat-align-tabs", "start"], ["label", "First"], [1, "p-20"], ["label", "Second"], ["label", "Third"], ["mat-align-tabs", "center"], ["mat-align-tabs", "end"], [1, "m-b-0"], [1, "p-l-20", "p-r-20", "p-b-20"], [1, "m-0"], ["animationDuration", "0ms"], ["animationDuration", "2000ms"]],
      template: function TabsComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic Tab ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "Check the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "code", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "a", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "mat-tab-group");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](12, "mat-tab", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](14, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "mat-tab", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](18, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](23, "Complex Tab Example (Responsive tab)");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "mat-tab-group", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "mat-tab", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](26, "div", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](27, " Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla venenatis ante augue. Phasellus volutpat neque ac dui mattis vulputate. Etiam consequat aliquam cursus. In sodales pretium ultrices. Maecenas lectus est, sollicitudin consectetur felis nec, feugiat ultricies mi. Aliquam erat volutpat. Nam placerat, tortor in ultrices porttitor, orci enim rutrum enim, vel tempor sapien arcu a tellus. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-tab", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????template"](29, TabsComponent_ng_template_29_Template, 1, 0, "ng-template", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](30, "div", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](31, " Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla venenatis ante augue. Phasellus volutpat neque ac dui mattis vulputate. Etiam consequat aliquam cursus. In sodales pretium ultrices. Maecenas lectus est, sollicitudin consectetur felis nec, feugiat ultricies mi. Aliquam erat volutpat. Nam placerat, tortor in ultrices porttitor, orci enim rutrum enim, vel tempor sapien arcu a tellus. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "mat-tab", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](33, " No content ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "mat-tab", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "div", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](36, " Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla venenatis ante augue. Phasellus volutpat neque ac dui mattis vulputate. Etiam consequat aliquam cursus. In sodales pretium ultrices. Maecenas lectus est, sollicitudin consectetur felis nec, feugiat ultricies mi. Aliquam erat volutpat. Nam placerat, tortor in ultrices porttitor, orci enim rutrum enim, vel tempor sapien arcu a tellus. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](37, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](38, "br");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](39, " Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla venenatis ante augue. Phasellus volutpat neque ac dui mattis vulputate. Etiam consequat aliquam cursus. In sodales pretium ultrices. Maecenas lectus est, sollicitudin consectetur felis nec, feugiat ultricies mi. Aliquam erat volutpat. Nam placerat, tortor in ultrices porttitor, orci enim rutrum enim, vel tempor sapien arcu a tellus. ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "mat-tab", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, " No content ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "mat-tab", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](43, " No content ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](47, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "Label alignment ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](51, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, "If you want to align the tab labels in the center or towards the end of the container, you can do so using the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](55, "[mat-align-tabs]");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](56, " attribute.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](57, "mat-tab-group", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "mat-tab", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](59, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](60, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "mat-tab", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](62, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](63, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](64, "mat-tab", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](65, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](66, "Content 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](67, "mat-tab-group", 19);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](68, "mat-tab", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](69, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](70, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](71, "mat-tab", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](72, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](73, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](74, "mat-tab", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](75, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](76, "Content 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](77, "mat-tab-group", 20);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](78, "mat-tab", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](79, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](80, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](81, "mat-tab", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](82, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](83, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](84, "mat-tab", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](85, "div", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](86, "Content 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](87, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](88, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](89, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](90, "mat-card-content", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](91, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](92, "Tab group animations ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](93, "span", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](94, "New");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](95, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](96, "If you want to align the tab labels in the center or towards the end of the container, you can do so using the ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](97, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](98, "[mat-align-tabs]");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](99, " attribute.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](100, "div", 22);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](101, "h5", 23);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](102, "No animation");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](103, "mat-tab-group", 24);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](104, "mat-tab", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](105, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](106, "mat-tab", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](107, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](108, "mat-tab", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](109, "Content 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](110, "h5", 21);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](111, "Very slow animation");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](112, "mat-tab-group", 25);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](113, "mat-tab", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](114, "Content 1");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](115, "mat-tab", 17);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](116, "Content 2");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](117, "mat-tab", 18);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](118, "Content 3");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTabGroup"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTab"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTabLabel"]],
      styles: [".demo-tab-group[_ngcontent-%COMP%] {\n  border: 1px solid #e8e8e8;\n}\n\n.demo-tab-content[_ngcontent-%COMP%] {\n  padding: 24px;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3RhYnMvQzpcXGJvb3N0YXBwXFxjc3Yvc3JjXFxhcHBcXG1hdGVyaWFsLWNvbXBvbmVudFxcdGFic1xcdGFicy5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3RhYnMvdGFicy5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNFLHlCQUF5QjtBQ0MzQjs7QURFQTtFQUNFLGFBQWE7QUNDZiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC90YWJzL3RhYnMuY29tcG9uZW50LnNjc3MiLCJzb3VyY2VzQ29udGVudCI6WyIuZGVtby10YWItZ3JvdXAge1xyXG4gIGJvcmRlcjogMXB4IHNvbGlkICNlOGU4ZTg7XHJcbn1cclxuXHJcbi5kZW1vLXRhYi1jb250ZW50IHtcclxuICBwYWRkaW5nOiAyNHB4O1xyXG59IiwiLmRlbW8tdGFiLWdyb3VwIHtcbiAgYm9yZGVyOiAxcHggc29saWQgI2U4ZThlODtcbn1cblxuLmRlbW8tdGFiLWNvbnRlbnQge1xuICBwYWRkaW5nOiAyNHB4O1xufVxuIl19 */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](TabsComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-tabs',
          templateUrl: './tabs.component.html',
          styleUrls: ['./tabs.component.scss']
        }]
      }], null, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/toolbar/toolbar.component.ts":
  /*!*****************************************************************!*\
    !*** ./src/app/material-component/toolbar/toolbar.component.ts ***!
    \*****************************************************************/

  /*! exports provided: ToolbarComponent */

  /***/
  function srcAppMaterialComponentToolbarToolbarComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "ToolbarComponent", function () {
      return ToolbarComponent;
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


    var _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(
    /*! @angular/flex-layout/flex */
    "./node_modules/@angular/flex-layout/__ivy_ngcc__/esm2015/flex.js");

    var ToolbarComponent =
    /*#__PURE__*/
    function () {
      function ToolbarComponent() {
        _classCallCheck(this, ToolbarComponent);
      }

      _createClass(ToolbarComponent, [{
        key: "ngOnInit",
        value: function ngOnInit() {}
      }]);

      return ToolbarComponent;
    }();

    ToolbarComponent.??fac = function ToolbarComponent_Factory(t) {
      return new (t || ToolbarComponent)();
    };

    ToolbarComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: ToolbarComponent,
      selectors: [["app-toolbar"]],
      decls: 68,
      vars: 0,
      consts: [[1, "no-shadow"], [1, ""], ["href", "https://material.angular.io/components/toolbar/overview"], ["color", "primary"], ["fxFlex", ""], ["mat-button", "", "href", "#", "mat-icon-button", ""], [1, "example-fill-remaining-space"], ["color", "accent"], ["color", "warn", 1, "bg-success"], [1, "example-spacer"], [1, "example-icon"]],
      template: function ToolbarComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "mat-card", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](3, "Toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "matToolbar is a container for headers, titles, or actions.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "code", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](7, "a", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](8, "Official Component");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](9, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, "Basic toolbar:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "mat-toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](12, "My App");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](14, "The primary color toolbar:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "mat-toolbar", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](17, "Primary Toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](18, "span", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](20, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](21, "search");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](22, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](23, "mat-icon");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](24, "more_vert");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](26, "Multiple row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "mat-toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](28, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](30, "First Row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](32, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](33, "Second Row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](34, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](35, "Positining toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-toolbar", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, "Application Title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](39, "span", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](40, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](41, "Right Aligned Text");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](43, "An accent toolbar using the second toolbar row:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "mat-toolbar", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](47, "Second Line Toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "p");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "A primary toolbar using the third toolbar row:");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-toolbar", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](51, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, "Custom Toolbar");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](55, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](56, "Second Line");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](57, "span", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](58, "mat-icon", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](59, "verified_user");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](60, "mat-toolbar-row");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](61, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](62, "Third Line");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????element"](63, "span", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](64, "mat-icon", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](65, "favorite");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](66, "mat-icon", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](67, "delete");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }
      },
      directives: [_angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatToolbar"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_2__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatIcon"], _angular_material__WEBPACK_IMPORTED_MODULE_1__["MatToolbarRow"]],
      styles: [".no-shadow[_ngcontent-%COMP%]   mat-toolbar[_ngcontent-%COMP%] {\n  box-shadow: none;\n}\n\n.example-fill-remaining-space[_ngcontent-%COMP%] {\n  flex: 1 1 auto;\n}\n\n.example-icon[_ngcontent-%COMP%] {\n  padding: 0 14px;\n}\n\n.example-spacer[_ngcontent-%COMP%] {\n  flex: 1 1 auto;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Rvb2xiYXIvQzpcXGJvb3N0YXBwXFxjc3Yvc3JjXFxhcHBcXG1hdGVyaWFsLWNvbXBvbmVudFxcdG9vbGJhclxcdG9vbGJhci5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Rvb2xiYXIvdG9vbGJhci5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNJLGdCQUFnQjtBQ0NwQjs7QURDQTtFQUdFLGNBQWM7QUNBaEI7O0FERUE7RUFDRSxlQUFlO0FDQ2pCOztBREVBO0VBQ0UsY0FBYztBQ0NoQiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC90b29sYmFyL3Rvb2xiYXIuY29tcG9uZW50LnNjc3MiLCJzb3VyY2VzQ29udGVudCI6WyIubm8tc2hhZG93IG1hdC10b29sYmFye1xyXG4gICAgYm94LXNoYWRvdzogbm9uZTtcclxufVxyXG4uZXhhbXBsZS1maWxsLXJlbWFpbmluZy1zcGFjZSB7XHJcbiAgLy8gVGhpcyBmaWxscyB0aGUgcmVtYWluaW5nIHNwYWNlLCBieSB1c2luZyBmbGV4Ym94LiBcclxuICAvLyBFdmVyeSB0b29sYmFyIHJvdyB1c2VzIGEgZmxleGJveCByb3cgbGF5b3V0LlxyXG4gIGZsZXg6IDEgMSBhdXRvO1xyXG59XHJcbi5leGFtcGxlLWljb24ge1xyXG4gIHBhZGRpbmc6IDAgMTRweDtcclxufVxyXG5cclxuLmV4YW1wbGUtc3BhY2VyIHtcclxuICBmbGV4OiAxIDEgYXV0bztcclxufSIsIi5uby1zaGFkb3cgbWF0LXRvb2xiYXIge1xuICBib3gtc2hhZG93OiBub25lO1xufVxuXG4uZXhhbXBsZS1maWxsLXJlbWFpbmluZy1zcGFjZSB7XG4gIGZsZXg6IDEgMSBhdXRvO1xufVxuXG4uZXhhbXBsZS1pY29uIHtcbiAgcGFkZGluZzogMCAxNHB4O1xufVxuXG4uZXhhbXBsZS1zcGFjZXIge1xuICBmbGV4OiAxIDEgYXV0bztcbn1cbiJdfQ== */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](ToolbarComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-toolbar',
          templateUrl: './toolbar.component.html',
          styleUrls: ['./toolbar.component.scss']
        }]
      }], function () {
        return [];
      }, null);
    })();
    /***/

  },

  /***/
  "./src/app/material-component/tooltip/tooltip.component.ts":
  /*!*****************************************************************!*\
    !*** ./src/app/material-component/tooltip/tooltip.component.ts ***!
    \*****************************************************************/

  /*! exports provided: TooltipComponent */

  /***/
  function srcAppMaterialComponentTooltipTooltipComponentTs(module, __webpack_exports__, __webpack_require__) {
    "use strict";

    __webpack_require__.r(__webpack_exports__);
    /* harmony export (binding) */


    __webpack_require__.d(__webpack_exports__, "TooltipComponent", function () {
      return TooltipComponent;
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


    var _angular_forms__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(
    /*! @angular/forms */
    "./node_modules/@angular/forms/__ivy_ngcc__/fesm2015/forms.js");
    /* harmony import */


    var _angular_material_core__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(
    /*! @angular/material/core */
    "./node_modules/@angular/material/__ivy_ngcc__/esm2015/core.js");

    var TooltipComponent = function TooltipComponent() {
      _classCallCheck(this, TooltipComponent);

      this.position = 'before';
    };

    TooltipComponent.??fac = function TooltipComponent_Factory(t) {
      return new (t || TooltipComponent)();
    };

    TooltipComponent.??cmp = _angular_core__WEBPACK_IMPORTED_MODULE_0__["????defineComponent"]({
      type: TooltipComponent,
      selectors: [["app-tooltip"]],
      decls: 58,
      vars: 2,
      consts: [["fxLayout", "row"], ["fxFlex.gt-sm", "100%"], ["matTooltip", "Tooltip!"], [1, "button-row"], ["mat-raised-button", "", "color", "accent", "matTooltip", "Tooltip!", "matTooltipPosition", "above"], ["mat-raised-button", "", "color", "warn", "matTooltip", "Tooltip!", "matTooltipPosition", "below"], ["mat-raised-button", "", "color", "primary", "matTooltip", "Tooltip!", "matTooltipPosition", "left"], ["mat-raised-button", "", "color", "warn", "matTooltip", "Tooltip!", "matTooltipPosition", "right"], ["mat-raised-button", "", "color", "accent", "matTooltip", "Tooltip!", "matTooltipPosition", "before"], ["matTooltip", "Tooltip!", 1, "example-tooltip-host", 3, "matTooltipPosition"], [1, "example-select", 3, "ngModel", "ngModelChange"], ["value", "before"], ["value", "after"], ["value", "above"], ["value", "below"], ["value", "left"], ["value", "right"]],
      template: function TooltipComponent_Template(rf, ctx) {
        if (rf & 1) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](0, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](1, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](2, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](3, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](4, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](5, "Basic Tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](6, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](7, "The Angular Material tooltip provides a text label that is displayed when the user hovers over or longpresses an element. add ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](8, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](9, "matTooltip=\"yourtext\"");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](10, " to any element ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](11, "span", 2);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](12, "I have a tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](13, "div", 0);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](14, "div", 1);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](15, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](16, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](17, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](18, "Positioning Tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](19, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](20, "Add ");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](21, "code");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](22, "matTooltipPosition=\"below, above, left, right, before, after\"");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](23, " to any element");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](24, "div", 3);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](25, "button", 4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](26, "Above tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](27, "button", 5);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](28, "below tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](29, "button", 6);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](30, "left tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](31, "button", 7);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](32, "right tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](33, "button", 8);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](34, "Before tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](35, "mat-card");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](36, "mat-card-content");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](37, "mat-card-title");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](38, "Tooltip with custom position");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](39, "mat-card-subtitle");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](40, "The Angular Material tooltip provides a text label that is displayed when the user hovers over or longpresses an element.");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](41, "div", 9);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](42, "span");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](43, "Show tooltip");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](44, "mat-form-field");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](45, "mat-select", 10);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????listener"]("ngModelChange", function TooltipComponent_Template_mat_select_ngModelChange_45_listener($event) {
            return ctx.position = $event;
          });

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](46, "mat-option", 11);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](47, "Before");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](48, "mat-option", 12);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](49, "After");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](50, "mat-option", 13);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](51, "Above");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](52, "mat-option", 14);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](53, "Below");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](54, "mat-option", 15);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](55, "Left");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementStart"](56, "mat-option", 16);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????text"](57, "Right");

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????elementEnd"]();
        }

        if (rf & 2) {
          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](41);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("matTooltipPosition", ctx.position);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????advance"](4);

          _angular_core__WEBPACK_IMPORTED_MODULE_0__["????property"]("ngModel", ctx.position);
        }
      },
      directives: [_angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultLayoutDirective"], _angular_flex_layout_flex__WEBPACK_IMPORTED_MODULE_1__["DefaultFlexDirective"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCard"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardContent"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardTitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatCardSubtitle"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatTooltip"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatButton"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatFormField"], _angular_material__WEBPACK_IMPORTED_MODULE_2__["MatSelect"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgControlStatus"], _angular_forms__WEBPACK_IMPORTED_MODULE_3__["NgModel"], _angular_material_core__WEBPACK_IMPORTED_MODULE_4__["MatOption"]],
      styles: [".example-tooltip-host[_ngcontent-%COMP%] {\n  display: inline-flex;\n  align-items: center;\n  margin: 50px;\n}\n\n.example-select[_ngcontent-%COMP%] {\n  margin: 0 10px;\n}\n/*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Rvb2x0aXAvQzpcXGJvb3N0YXBwXFxjc3Yvc3JjXFxhcHBcXG1hdGVyaWFsLWNvbXBvbmVudFxcdG9vbHRpcFxcdG9vbHRpcC5jb21wb25lbnQuc2NzcyIsInNyYy9hcHAvbWF0ZXJpYWwtY29tcG9uZW50L3Rvb2x0aXAvdG9vbHRpcC5jb21wb25lbnQuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtFQUNFLG9CQUFvQjtFQUNwQixtQkFBbUI7RUFDbkIsWUFBWTtBQ0NkOztBREVBO0VBQ0UsY0FBYztBQ0NoQiIsImZpbGUiOiJzcmMvYXBwL21hdGVyaWFsLWNvbXBvbmVudC90b29sdGlwL3Rvb2x0aXAuY29tcG9uZW50LnNjc3MiLCJzb3VyY2VzQ29udGVudCI6WyIuZXhhbXBsZS10b29sdGlwLWhvc3Qge1xyXG4gIGRpc3BsYXk6IGlubGluZS1mbGV4O1xyXG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XHJcbiAgbWFyZ2luOiA1MHB4O1xyXG59XHJcblxyXG4uZXhhbXBsZS1zZWxlY3Qge1xyXG4gIG1hcmdpbjogMCAxMHB4O1xyXG59IiwiLmV4YW1wbGUtdG9vbHRpcC1ob3N0IHtcbiAgZGlzcGxheTogaW5saW5lLWZsZXg7XG4gIGFsaWduLWl0ZW1zOiBjZW50ZXI7XG4gIG1hcmdpbjogNTBweDtcbn1cblxuLmV4YW1wbGUtc2VsZWN0IHtcbiAgbWFyZ2luOiAwIDEwcHg7XG59XG4iXX0= */"]
    });
    /*@__PURE__*/

    (function () {
      _angular_core__WEBPACK_IMPORTED_MODULE_0__["??setClassMetadata"](TooltipComponent, [{
        type: _angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"],
        args: [{
          selector: 'app-tooltip',
          templateUrl: './tooltip.component.html',
          styleUrls: ['./tooltip.component.scss']
        }]
      }], null, null);
    })();
    /***/

  }
}]);
//# sourceMappingURL=material-component-material-module-es5.js.map