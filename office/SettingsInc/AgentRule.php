<?php

	// התחלה הוספה / עריכה של משתמש
	if (Auth::userCan('ManageRules')) {
		$AgentRules = array(
		"בעלים"=>"1",
		"מנהל כללי"=>"5",
		"מאמן"=>"4",
        "פקידה"=>"3",    
		"נציג מכירות"=>"2",
		);
	}
	else {
		$AgentRules = array(
		"מנהל צוותי מכירות"=>"5",
		"מאמן"=>"4",
        "פקידה"=>"3",            
		"נציג מכירות"=>"2",
		);
	}
	// סיום הוספה / עריכה של משתמש


	// טבלת משתמשים וכרטיס נציג
		$AgentRulesTable = array(
		"בעלים"=>"1",
		"מנהל כללי"=>"5",
		"מאמן"=>"4",
        "פקידה"=>"3",            
		"נציג מכירות"=>"2",    
		);
	// טבלת משתמשים וכרטיס נציג
?>