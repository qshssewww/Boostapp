function CustomCheckbox(original_checkbox_id, custom_checkbox_wrapper , isChecked) {

    this.original_checkbox_id = original_checkbox_id;
    this.custom_checkbox_wrapper = custom_checkbox_wrapper;
    this.isChecked = isChecked;

}

CustomCheckbox.prototype.init = function () {
    
    var _this = this;

    //Custom checkbox
    var customCheckboxWrapper = document.querySelector('#' + _this.custom_checkbox_wrapper);
    customCheckboxWrapper.style.display = 'inline-block';

    var box = document.createElement('div')
    box.className = 'custom-box';

    var v = document.createElement('div');
    v.className = 'custom-v';
    
    var vicon = document.createElement('i');
    vicon.className = 'fas fa-check';

    v.appendChild(vicon);
    box.appendChild(v);
    customCheckboxWrapper.appendChild(box);

    setV();

    //Original checkbox
    var originalCheckbox = document.querySelector('#' + _this.original_checkbox_id);
    originalCheckbox.style.display = 'none';
    originalCheckbox.setAttribute('checked', _this.isChecked);

    box.addEventListener('click', function () {
        _this.isChecked = !_this.isChecked;
        originalCheckbox.setAttribute('checked', _this.isChecked);
        setV();
    });

    function setV() {
        if (_this.isChecked) {
            v.style.display = 'flex';
        } else {
            v.style.display = 'none';
        }
    }


}