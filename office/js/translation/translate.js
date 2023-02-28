var json;
var cookieLanguage = $.cookie("boostapp_lang");

//set languages
var lag = cookieLanguage && (cookieLanguage === 'eng' || cookieLanguage === 'en') ? 'eng' : 'he';

var url = '/storage/lang/translations-' + lag + '.json';

(async function () {

    async function getJson() {
        var result = null;
        await $.ajax({
            url: url,
            dataType: "json",
            success: function (data) {
                result = data.translation_keys;
            },
            error: function () {
                console.log('error while loading translation json');
            }
        });

        return result ? result : {};
    }

    // creat now sessionStorage if not exist
    if (!sessionStorage["translation_" + lag]) {
        await getJson().then((res => {
            json = res;
            sessionStorage.setItem("translation_" + lag, JSON.stringify(res));
        }));
    }
})();


function lang(key) {
    // return translation answer or ""
    try {
        let translationAnswer = JSON.parse(sessionStorage.getItem('translation_'+ lag))[key];
        return translationAnswer;
    } catch (e) {
        return "";
    }
}
