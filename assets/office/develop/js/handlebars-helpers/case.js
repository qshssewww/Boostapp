module.exports = function(value, options) {
	if (value == this.switch_value) {
		this.switch_break = true;
		return options.fn(this);
	}
};