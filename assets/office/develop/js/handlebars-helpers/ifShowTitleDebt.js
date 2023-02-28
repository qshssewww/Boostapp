module.exports = function(options) {
	const root = options.data && options.data.root;
	const debtItem = root && root.items.find(el => el.type === 'debt');
	if (debtItem && debtItem.id == this.id) {
		return options.fn(this);
	}
};