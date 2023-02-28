$(document).ready(function () {
    const permanentNotice = $("#js-lock-modal .js-try-later").attr("disabled") == "disabled";
    if(permanentNotice || !checkIfSeen()) {
        $("#js-lock-modal").modal("show");
    }
    $("#js-update-card").attr("src", '');

    $('body').on("click", ".js-credit-btn", function () {
        let e = 1;
        let lockCredit = $("#js-lock-credit");
        let lockModal = $("#js-lock-modal");
        var updateCard = $("#js-update-card");
        lockCredit.modal("show").find(".js-fa-spin").show();
        lockModal.modal("hide");
        updateCard.attr("src", '');

        $.ajax({
            url: '/office/lockCompany/ajax.php',
            type: 'POST',
            success: function (response) {
                let res = JSON.parse(response);
                console.log(response);
                if(res.code == 200){
                    updateCard.attr("src", res.res);
                    lockCredit.find(".js-fa-spin").hide();
                }

                // $("#DarkBG").show();
                // $("body").css("overflow","hidden");
            },
            error: function (err) {
                console.log(err);
            }
        });
    });
    $('body').on("click","#js-show_lock",function () {
        $("#js-lock-modal").modal("show");
    });

    function checkIfSeen() {
        if (localStorage)
        {
            //  get the localStorage variable.
            let timerValue = localStorage.getItem('seen_update_payment');
            //  if it's not set yet, set it.
            if (!timerValue)
            {
                timerValue = new Date().toString();
                localStorage.setItem('seen_update_payment', timerValue);
                return false;
            }
            //  parse string date and get difference between now - old time in milliseconds.
            let diff = new Date().getTime() - new Date(timerValue).getTime();
            //  compare difference and check if it matches 1 hour (1000ms * 60s * 60m)
            if (diff >= (1000 * 60 * 60 * 6))   // 6 hours
            {
                //  reset timer immediately.
                localStorage.setItem('seen_update_payment', new Date().toString());
                return false;
            }
        }
        return true;
    }
});
