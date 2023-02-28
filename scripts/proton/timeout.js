$(document).ready(function() {
    !verboseBuild || console.log('-- starting proton.timeout build');
    
    proton.timeout.build();
});

proton.timeout = {
	build: function () {
		$.sessionTimeout({
			message: 'המערכת לא זיהתה ביצוע פעולה, הנך מועבר לטופס ההרשמה תוך 5 שניות',
			keepAlive: false,
			logoutUrl: 'index.php',
			redirUrl: 'index.php',
			warnAfter: 3000,
			redirAfter: 8000
		});
	}
}