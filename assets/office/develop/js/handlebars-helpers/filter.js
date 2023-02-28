module.exports = function(array, value = null, key = 'id', returnKey = 'id') {
	// {{filter items selectedOptionId 'id' 'text'}}
	if (value === null) {
		return '';
	}
	// filter on a string value
	const a = array.find(v => v[key].toString() === value.toString());
	return a ? a[returnKey] : '';
};