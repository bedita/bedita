<?php
	if(file_exists(VIEWS . 'pages/' . $section['nickname'] . '.ctp')) {
		include(VIEWS . 'pages/' . $section['nickname'] . '.ctp');
	} else {
		include(VIEWS . 'pages/generic_section.ctp');
	}
?>