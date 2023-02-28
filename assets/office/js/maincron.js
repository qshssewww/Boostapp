	function get_boostapplogin_domain(){
		var queryString = 'devlogin.boostapp.co.il';
		var url = window.location.href;
		if(url.indexOf(queryString) != -1){
			return 'https://devlogin.boostapp.co.il';
		}
		return 'https://login.boostapp.co.il'
	}

	BeePOS.options = {
			ajaxUrl: get_boostapplogin_domain + '/ajax.php',
			lang: {"error":"\u05d0\u05d5\u05e4\u05e1! \u05d4\u05ea\u05d2\u05dc\u05ea\u05d4 \u05e9\u05d2\u05d9\u05d0\u05d4.","connecting":"\u05d4\u05ea\u05d7\u05d1\u05e8 \u05dc ","changes_saved":"\u05d4\u05d4\u05d2\u05d3\u05e8\u05d5\u05ea \u05e9\u05d5\u05e0\u05d5 \u05d1\u05d4\u05e6\u05dc\u05d7\u05d4.","db_saved":"\u05d4\u05e0\u05ea\u05d5\u05e0\u05d9\u05dd \u05e0\u05e9\u05de\u05e8\u05d5 \u05d1\u05d4\u05e6\u05dc\u05d7\u05d4","pass_changed":"\u05e1\u05d9\u05e1\u05de\u05ea\u05da \u05e9\u05d5\u05e0\u05ea\u05d4!","no_messages":"\u05d0\u05d9\u05df \u05dc\u05da \u05d4\u05d5\u05d3\u05e2\u05d5\u05ea.","loading":"\u05d8\u05d5\u05e2\u05df...","message_sent":"\u05d4\u05d4\u05d5\u05d3\u05e2\u05d4 \u05e9\u05dc\u05da \u05e0\u05e9\u05dc\u05d7\u05d4 \u05d1\u05d4\u05e6\u05dc\u05d7\u05d4 \u05d0\u05dc \u05e6\u05d5\u05d5\u05ea \u05d4\u05ea\u05de\u05d9\u05db\u05d4 \u05e9\u05dc\u05e0\u05d5."},
			debug: 1,
			
		};
