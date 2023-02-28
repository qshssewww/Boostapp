module.exports = function(price, withoutZero = true, minusInParentheses=false) {
	// {{strFormatPrice originalPrice}} or {{strFormatPrice originalPrice false}}
	if (price === undefined || price === null) {
		return '';
	}
	if(minusInParentheses) {
		price = Math.abs(price);
	}

	// return '₪' + (+price).toFixed(2).replace(/\.0+$/,'');
	let priceStr = parseFloat(price).toLocaleString('en-US', {
		style: 'currency',
		currency: 'ILS',
	})

	if(minusInParentheses) {
		priceStr = '(' + priceStr + ')';
	}


	return withoutZero ? priceStr.replace(/\.0+$/,'') : priceStr;
};

