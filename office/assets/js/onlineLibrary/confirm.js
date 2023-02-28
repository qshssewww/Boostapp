window.customConfirm = function(data, callback) { 
    Swal.fire({
        title: '',
        text: data,
        type: "warning",
        icon:'question',
        showCancelButton: true,
        confirmButtonColor: "blue",
        confirmButtonText: "כן",
        cancelButtonText: "לא",
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function(isConfirm){
        if(isConfirm.dismiss !== 'cancel'){
            callback()
        }
    });
};
window.warningConfirm = function(data, callback) { 
    Swal.fire({
        title: '',
        text: data,
        type: "warning",
        icon:'warning',
        showCancelButton: true,
        confirmButtonColor: "blue",
        confirmButtonText: "כן",
        cancelButtonText: "לא",
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function(isConfirm){
        if(isConfirm.dismiss !== 'cancel'){
            callback()
        }
    });
};
window.customAlert = function(data, callback) {
    Swal.fire({
        title: '',
        text: data,
        type: "warning",
        icon:'warning',
        showCancelButton: false,
        confirmButtonColor: "blue",
        confirmButtonText: "סגור",
        cancelButtonText: "לא",
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function(isConfirm){
        if(isConfirm.dismiss !== 'cancel'){
            callback()
        }
    });
};