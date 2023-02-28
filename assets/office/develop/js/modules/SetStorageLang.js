import jsCookie from '@modules/Cookie';

async function setStorageLang() {
	const cookieLanguage = jsCookie.get('boostapp_lang');
	const lag = cookieLanguage && (cookieLanguage === 'eng' || cookieLanguage === 'en') ? 'eng' : 'he';
	const storageName = "translation_" + lag;

	if (!sessionStorage.getItem(storageName)) {
		const rawResponse = await fetch('/storage/lang/translations-' + lag + '.json', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			}
		});

		const content = await rawResponse.json();
		const result = content.translation_keys || {};
		sessionStorage.setItem(storageName, JSON.stringify(result));
	}
}

export { setStorageLang };