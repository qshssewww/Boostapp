import jsCookie from '@modules/Cookie';

export function Lang(key) {
	const cookieLanguage = jsCookie.get('boostapp_lang');
	const lag = cookieLanguage && (cookieLanguage === 'eng' || cookieLanguage === 'en') ? 'eng' : 'he';
	const storageName = "translation_" + lag;

	// return translation answer or ""
	try {
		return JSON.parse(sessionStorage.getItem(storageName))[key];
	} catch (e) {
		return "";
	}
}