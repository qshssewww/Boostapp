function updateSelectedMenu() {
	var loc = document.location.href; 
	var scripts = document.getElementsByTagName('script');
	var parentNode = scripts[scripts.length - 1].parentNode;
	var parentTag = parentNode.tagName.toLowerCase();
	if (parentTag == "li") {
		parentNode = parentNode.parentNode;
		parentTag = parentNode.tagName.toLowerCase();
	}
	if (parentTag == "ul") {
		var ul = parentNode;
		var href;
		for (var i=0;i<ul.childNodes.length;i++) {
			if (ul.childNodes[i].tagName == "LI") {
				href = ul.childNodes[i].childNodes[0].href; 
				if(ul.childNodes[i].childNodes[0].className == 'clicked') {
					ul.childNodes[i].childNodes[0].className = '';					
				}
				if (loc == href) {
					ul.childNodes[i].childNodes[0].className = 'clicked';
					break;
				}
			}
		}
	}
};
updateSelectedMenu();