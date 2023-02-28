module.exports = function(value) {
	// {{strFormatZero quantity}}
	if (value === undefined || value === null) {
		return '';
	}
	const num = Number(value);
	return num.toFixed(2);
};