const {getDayOfWeekName} = require("../modules/Helpers");
module.exports = function(item) {
	debugger;

	console.log(item);
	switch (item.type) {
		case 'service':
			const date = new Date(item.date);
			const dayNumber =  getDayOfWeekName(date.getDay()) ?? '';
			const shortDate = date.toLocaleDateString('en-GB', { month:"numeric", day:"numeric"})
			return item.name + ': ' + dayNumber + ' ' + shortDate;
		default:
			return item.name ?? '';
	}
};

