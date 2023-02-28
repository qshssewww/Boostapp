$(document).ready(function () {
    $("#js-success-update-payment").modal('show');

    $("#js-success-update-payment").on("hidden.bs.modal", function () {

        const url = location.href.split("?")[0];
        window.history.pushState('object', document.title, url);
        return;
    });
})