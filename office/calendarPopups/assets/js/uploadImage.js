$('document').ready(function () {


    // var inputElement = document.getElementById("uploadImage");
    // inputElement.addEventListener("change", validateAndUpload, false);
    //
    // function validateAndUpload() {
    //     debugger
    //     input = this;
    //     var URL = window.URL || window.webkitURL;
    //     var file = input.files[0];
    //     if (file) {
    //         var image = new Image();
    //         image.onload = function () {
    //             debugger
    //             if (this.width <= 350 && this.height <= 200) {
    //                 // var rawImageData = file.replace(/^data\:image\/\w+\;base64\,/, '');
    //                 // file = new Blob([this.base64DecToArr(rawImageData)], { type: 'image/jpeg' });
    //                 uploadImage(file, function () {
    //
    //                 });
    //             } else {
    //                 alert('not goot...')
    //             }
    //         };
    //         image.src = URL.createObjectURL(file);
    //     }
    // };
    //
    // function uploadImage(file, callback) {
    //     debugger
    //     let fd = new FormData();
    //     fd.append('action', 'upload');
    //     fd.append('file', file);
    //     $.ajax({
    //         type: "POST",
    //         enctype: 'multipart/form-data',
    //         url: "/office/Server/upload_classes.php",
    //         // url:'/office/fileuploadcheck/upload.php',
    //         data: fd,
    //         processData: false,
    //         contentType: false,
    //         cache: false,
    //         // timeout: 600000,
    //         success: function (data) {
    //             debugger
    //             console.log("SUCCESS : ", data);
    //             callback(data);
    //         },
    //         error: function (e) {
    //             debugger
    //             console.log("ERROR : ", e);
    //         }
    //     });
    // }
});