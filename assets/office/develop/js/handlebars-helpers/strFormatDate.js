import {setDate} from "@modules/Helpers";
export default function strFormatDate(key, toString, divider) {
	// {{strFormatDate date false '/'}}
	if (isNaN(new Date(key))) {
		return '';
	}
	return setDate(key, toString, divider);
};