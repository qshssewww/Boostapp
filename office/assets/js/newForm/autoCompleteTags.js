function tagifyCheck() {

    var input = document.querySelector('#interestedIn');
    if (!input)
        return

    var tagify = new Tagify(input, { whitelist: [], enforceWhitelist: true });
    var controller; // for aborting the call

    // listen to any keystrokes which modify tagify's input
    tagify.on('input', onInput)

    function onInput(e) {
        var value = e.detail.value;
        tagify.settings.whitelist.length = 0; // reset the whitelist

        controller && controller.abort();
        controller = new AbortController();

        // show loading animation and hide the suggestions dropdown
        tagify.loading(true).dropdown.hide.call(tagify)
        getClasses(value, function (whitelist) {
            if (Object.keys(whitelist).length === 0 && whitelist.constructor === Object) { //Check for empty object
                whitelist = [];
            }
            tagify.settings.whitelist.splice(0, whitelist.length, ...whitelist)
            tagify.loading(false).dropdown.show.call(tagify, value); // render the suggestions dropdown
        })
    }
}

function getClasses(className, callback) {
    $.ajax({
        url: "/office/GetClasses.php",
        type: "post",
        dataType: "json",
        contentType: "application/json",
        success: function (res) {
            if (res && res.length > 0) {
                res.forEach(function (_class) {
                    _class.value = _class.value.trim();
                });
            }
            console.log(res);
            callback(res);
        },
        error: function (err) {
            console.log(err);

        },
        data: JSON.stringify({ className: className })
    });
}