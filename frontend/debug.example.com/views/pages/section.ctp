<?php
	if(file_exists(VIEWS . 'sections/' . $section['nickname'] . '.ctp')) {
		include(VIEWS . 'sections/' . $section['nickname'] . '.ctp');
	} else {
		include(VIEWS . 'pages/generic_section.ctp');
	}
?>