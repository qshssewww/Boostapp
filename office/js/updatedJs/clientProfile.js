$(document).ready(function () {
    $("body").on("change","#regSelectOption",function() {
        let type = $(this).val();
        if(type == 1){
            $("#regStartDate").show();
            $("#regActive").hide();
            $("#regCancel").hide();
            $("#regDisplay").hide();
        }
        else if(type == 2){
            $("#regStartDate").hide();
            $("#regActive").hide();
            $("#regDisplay").hide();
            $("#regCancel").show();
        }
        else if(type == 3){
            $("#regStartDate").hide();
            $("#regActive").show();
            $("#regCancel").hide();
            $("#regDisplay").hide();
        }
        else if(type == 9){
            $("#regStartDate").hide();
            $("#regActive").hide();
            $("#regCancel").hide();
            $("#regDisplay").show();
        }
    });
    $('body').on("submit",".clientRegForm",function(e){
        e.preventDefault();
        var values = {};
        var data = new FormData();
        $.each($(this).serializeArray(), function(i, field) {
            values[field.name] = field.value;
            data.append(field.name,field.value);
        });
        if(values["regType"] == 1){
            if(values["regDate"] == ""){
                Swal.fire({
                    title: 'לא נבחר תאריך חדש',
                    confirmButtonText: `סגור`,
                });
                return;
            }
        }
        else if(values["regType"] == 2){
            data.append("status","0");
        }
        else if(values["regType"] == 3){
            data.append("status","1");
        }
        $.ajax({
            url: "/office/ajax/clientProfileAjax.php",
            type: "post",
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                console.log(response)
                var modal = $('#OptionsActivityPopup');
                modal.modal("hide");
                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        })
    });
});
