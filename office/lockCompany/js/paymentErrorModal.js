$(document).ready(function () {
    $("#js-error-update-res").modal('show');

    $("#js-error-update-res").on("hidden.bs.modal", function () {

        const url = location.href.split("?")[0];
        window.history.pushState('object', document.title, url);
        $("#js-lock-modal").modal('show');
        return;
    });
})