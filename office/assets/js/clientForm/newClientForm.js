function Ajaxcall(){

    var dataString = 'fromPage=form';
    $.ajax({
        type: 'POST',
        data: dataString,
        url: '/office/ajax/ajaxNewClientForm.php',
        success: function (data) {
            alert(data);
        }

    });
}


$("#CitiesSelect").on("select2:unselect", function(e) {
    $("#StreetSelect").select2("val", "");
    $('.AddressCols').removeClass('col-md-4');
    $('.AddressCols').addClass('col-md-6');
    $('.NoAddress').hide();
    $("#StreetH").val("");
});
$("#StreetSelect").on("select2:unselect", function(e) {
    $('.AddressCols').removeClass('col-md-4');
    $('.AddressCols').addClass('col-md-6');
    $('.NoAddress').hide();
    $("#StreetH").val("");
});


$('#CitiesSelect').select2({
    theme:"bootstrap",
    placeholder: "בחר עיר",
    language: "he",
    allowClear: true,
    width: '25%',
    ajax: {
        url: 'action/CitiesSelect.php',
        dataType: 'json'
    },
    minimumInputLength: 3,
});

$('#StreetSelect').select2({
    theme:"bootstrap",
    placeholder: "בחר רחוב",
    language: "he",
    allowClear: true,
    width: '25%',
    ajax: {
        url: 'action/StreetsSelect.php',
        dataType: 'json',
        data: function (params) {
            var CityId = $("#CitiesSelect").val();
            var query = {
                q: params.term,
                CityId: CityId
            }
            return query;
        }
    },
    minimumInputLength: 3,
});

$(document).ready(function() {
    $("#add_file").click(function() {
        $("#separation_line").show();
    });
    $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();
        if (/^\s*$/.test(filename)) {
            $(".file-select").removeClass('active');
            $("#noFile").text("בחר קובץ");
        }
        else {
            $(".file-select").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
        }
    });

    $('#form_type').on('change', function() {
        var type = $('#form_type').val();
        if(type == 'lead'){
            $("#form-lead").show();
            $("#form-new-cus").hide();


        }else if(type == 'new_customer'){
            $("#form-lead").hide();
            $("#form-new-cus").show();

        }
    });


    $('#new_customer').on('click', function() {
        $("#form-lead").hide();
        $("#form-new-cus").show();
        $config_lead_color = '';
        $config_new_customer_color = 'color'
    });

    $('#lead').on('click', function() {
        $("#form-lead").show();
        $("#form-new-cus").hide();
    });



    $("#new_client_form, #lead_form").on("submit", function (e) {
       var a=0;
    });
    var new_cont = 1;
    $("#add_contact, #add_contact2").click(function(){

        var new_contact =
        "<div class='field contacts'>"+
            "<div class='line-one'>"+
                "<div>"+
                    "<label>שם מלא</label><br>"+
                    "<div class='details'>"+
                        "<input type='text' value='' class='contacts remove-border' name='contacts["+new_cont+"][full_name]'>"+
                    "</div>"+
                "</div>"+
                "<div>"+
                    "<label>קרבה</label><br>"+
                    "<div class='details'>"+
                        "<input type='text' value='' class='contacts remove-border' name='contacts["+new_cont+"][relationship]'>"+
                    "</div>"+
                "</div>"+
            "</div>"+
            "<div class='line-one'>"+
                "<div>"+
                    "<label>טלפון</label><br>"+
                    "<div class='hold-2-details'>"+
                        "<div class='details details1'>"+
                            "<input type='number' class='contacts remove-border' value='' name='contacts["+new_cont+"][phone][number]'>"+
                            "<i class='fas fa-phone'></i>"+
                        "</div>"+
                        "<div class='details details2'>"+
                            "<select class='remove-border' name='contacts["+new_cont+"][phone][area code]'>"+
                                "<option value='+972'>972+</option>" +
                                "<option value='050'>050</option>" +
                                "<option value='054'>054</option>" +
                                "<option value='077'>077</option>" +
                            "</select>" +
                        "</div>"+
                    "</div>"+
                "</div>"+
                "<div>"+
                    "<label>מייל</label><br>"+
                    "<div class='details'>"+
                        "<input type='email' value='' class='contacts field remove-border' name='contacts["+new_cont+"][mail]'><br>"+
                    "</div>"+
                "</div>"+
            "</div><br></div>";
 
        // var new_contact ="<br><input type='text' value='' class='contacts' placeholder='שם מלא' name='contacts["+new_cont+"][full_name]'>" +
        //     "<input type='text' value='' class='contacts' placeholder='קרבה' name='contacts["+new_cont+"][relationship]'>" +
        //     "<input type='number' class='contacts' value='' placeholder='טלפון' name='contacts["+new_cont+"][phone][number]'>  -" +
        //     "<select name='contacts["+new_cont+"][phone][area code]'>" +
        //     "<option value='+972'>+972</option>" +
        //     "<option value='050'>050</option>" +
        //     "<option value='054'>054</option>" +
        //     "<option value='077'>077</option>" +
        //     "</select>" +
        //     "<input type='email' value='' class='contacts' placeholder='כתובת מייל' name='contacts["+new_cont+"][mail]'><br>";
        
       // let newcont = $(this).prev().find(".contacts");
        $('.contacts').append(new_contact);

        new_cont++;
    });

    var new_f = 1;
    $("#add_file").click(function(){
        var new_file =
        "<div class='line-one'>"+
            "<div class='delete'><div></div></div>"+
            "<div class='file-select'>"+
                "<div class='file-select-name' id='noFile'>בחר קובץ</div>"+ 
                "<input type='file' name='add-files["+new_f+"]' id='chooseFile'>"+
           "</div></div><div class='line-one'>"+
            "<div>"+
                "<label>שם טופס</label><br>"+
                "<div class='details'><input type='text' value='' class='add-files remove-border' name='add-files["+new_f+"]'></div>"+
            "</div>"+
            "<div class='select-new'>"+
                "<div class='btn-on-off'>"+
                    "<label class='switch'><input type='checkbox' checked><span class='slider round'></span></label>"+
                "</div>"+
                "<div class='validity-months'>"+
                    "<span>תוקף</span><div class='details'><input type='number' value='' class='add-files remove-border' name='add-files["+new_f+"]'></div><span>חודשים</span>"+
                "</div>"+
            "</div>"+
        "</div>"+
        "<button id='add_file' class='add-new'><i class='fas fa-plus'></i><span>קובץ</span></button>";
 
        $('.add-files').append(new_file);

        new_f++;
    });


});












