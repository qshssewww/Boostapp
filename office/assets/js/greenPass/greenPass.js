$(document).ready(function() {
    $(document).on("click",".greenPassStatus",function () {
        let clientId = $(this).attr("data-id");
        var data = {
            client_id: clientId,
            fun : "modal"
        }
        $.ajax({
            url: "/office/ajax/covidGreenPass.php",
            type: "post",
            data: data,
            success: function (response) {
                $("#greenPassModalReport").append(response);
                $("#green_pass_modal").modal('show');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus);
                console.log(textStatus, errorThrown);
            },
        });
    })
    // $(document).on("click","#greenPassModalReport .ip-close",function (){
    //     $("#green_pass_modal").remove();
    // })
});


