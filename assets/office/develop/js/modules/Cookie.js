const jsCookie = {
	set: function(name, value, expires, path, domain, secure) {
		let cookieText = `${encodeURIComponent(name)}=${encodeURIComponent(value)}`;
		if (expires instanceof Date) {
			cookieText += `; expires=${expires.toGMTString()}`;
		}
		if (path) cookieText += `; path=${path}`;
		if (domain) cookieText += `; domain=${domain}`;
		if (secure) cookieText += `; secure`;
		document.cookie = cookieText;
	},
	get: function(e) {
		let t = e + "=";
		let i = document.cookie.split(";");
		for (let n = 0; n < i.length; n++) {
			let a = i[n];
			while (" " == a.charAt(0))
				a = a.substring(1, a.length);
			if (0 == a.indexOf(t))
				return decodeURIComponent(a.substring(t.length, a.length))
		}
		return null;
	},
	erase: function(e) {
		jsCookie.set(e, "", -1)
	}
}

export default jsCookie;